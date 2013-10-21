<?php
/**
 * 处理用户电影通知job
 * added by xiongjiewu at 2013-4-5
 */
$send = new sendNotice();
$send->run();
class sendNotice extends myPdo {
    private $_idLogPath = "/home/www/logs/dianying/dianyingNotice.id";
    private $_outputLogPath = "/home/www/logs/dianying/dianyingNotice.log";

    private $_logId;
    private $_currentYmd;

    private $_pdo;


    public function __construct() {
        $this->_pdo = $this->getPdo();
    }

    public function run() {
        $this->_logId = 0;
        $this->_currentYmd = date("Ymd");
        if (file_exists($this->_idLogPath)) {
            $fileJsonInfo = file_get_contents($this->_idLogPath);
            $fileArrInfo = json_decode($fileJsonInfo,true);
            if (isset($fileArrInfo['currentId']) && !empty($fileArrInfo['currentYmd']) && ($fileArrInfo['currentYmd'] == $this->_currentYmd)) {
                $this->_logId = $fileArrInfo['currentId'];
            }
        }
        $noticeInfos = $this->_getNoticeInfos();//获取需要处理的通知
        if (!empty($noticeInfos)) {
            foreach($noticeInfos as $noticeVal) {
                $this->_logId = $noticeVal['id'];
                $dyInfo = $this->_getDyInfoByInfoId($noticeVal['infoId']);
                if (empty($dyInfo) || ($dyInfo['del'] == 1)) {//电影不存在，或者已被删除
                    $upRes = $this->_updateNotcieInfoById($noticeVal['id'],true);
                    if ($upRes == 1) {
                        $this->_replyMessage($dyInfo['name'],$noticeVal['userId']);
                    }
                } else {
                    $watchInfo = $this->_getWatchInfoByInfoId($noticeVal['infoId']);
                    if (!empty($watchInfo)) {//有观看链接
                        $sendRes = $this->_sendMail($noticeVal['id'],$noticeVal['infoId'],$noticeVal['email'],$noticeVal['userId'],$dyInfo['name']);
                        if ($sendRes == 1) {
                            printf("--%s邮件发送成功---\n",date("Y-m-d H:i:s"));
                            $this->_updateNotcieInfoById($noticeVal['id']);
                        }
                    } else {
                        //do nothing
                    }
                }
            }
            file_put_contents($this->_idLogPath,json_encode(array("currentYmd"=>$this->_currentYmd,"currentId"=>$this->_logId)));
        }
    }

    private function _replyMessage($dyName,$userId) {
        $userId = intval($userId);
        if (!isset($dyName) || empty($userId)) {
            return false;
        }
        $content = "尊敬的用户,您好。由于电影《{$dyName}》内容违法或者个别原因已被管理员删除,因此您订阅的有关于电影《{$dyName}》";
        $content .= "的通知都被从您的电影订阅通知列表里面删除，特此告知您。有问题联系我们，谢谢！";
        $insertData = array($userId,$content,time());
        $sql = "insert into `tbl_message` (userId,content,time) values (?,?,?);";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->execute($insertData);
        return $stmt->rowCount();
    }

    private $_limit = 500;
    private function _getNoticeInfos() {
        $sql = "select id,userId,infoId,time,type,reply,del,email,mesId";
        $sql .= " from `tbl_notice` where id > " . $this->_logId;
        $sql .= " and reply = 0 and del = 0  order by id asc limit " . $this->_limit . ";";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function _getWatchInfoByInfoId($infoId) {
        $infoId = intval($infoId);
        if (empty($infoId)) {
            return false;
        }
        $sql = "select link from `tbl_watchLink` where infoId = " . $infoId . " limit 1;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetch();
    }

    private function _sendMail($id,$infoId,$email,$userId,$dyName) {
        $id = intval($id);
        $infoId = intval($infoId);
        $userId = intval($userId);
        if (empty($id) || empty($infoId) || empty($email) || empty($userId) || empty($dyName)) {
            return false;
        }
        $idStr = $this->encodeId($infoId);
        $url = "http://www.dianying8.tv/detail/index/{$idStr}/";
        $userName = $this->_getUserNameByUserId($userId);
        $sendRes = $this->_insertEmail($email,$userName,$dyName,$url);
        return $sendRes;
    }

    /** 更新电影信息
     * @param $id
     * @param bool $del
     * @return bool|int
     */
    private function _updateNotcieInfoById($id,$del = false){
        $id  = intval($id);
        if (empty($id)) {
            return false;
        }
        $sql = "update `tbl_notice` set reply = 1";
        if ($del) {
            $sql .= ",del = 1";
        }
        $sql .= " where id = " . $id . " limit 1;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /** 获取电影信息
     * @param $infoId
     * @return bool|mixed
     */
    private function _getDyInfoByInfoId($infoId) {
        $infoId = intval($infoId);
        if (empty($infoId)) {
            return false;
        }
        $sql = "select name,del from `tbl_detailInfo` where id = " . $infoId . " limit 1;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetch();
    }

    /** 获取用户名称
     * @param $userId
     * @return bool|string
     */
    private function _getUserNameByUserId($userId) {
        $userId = intval($userId);
        if (empty($userId)) {
            return false;
        }
        $sql = "select userName from `tbl_user` where id = " . $userId . " limit 1;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /** 插入邮件队列表
     * @param $email 邮箱
     * @param $userName 用户名称
     * @param $dyName 电影名称
     * @param $url 打开链接
     * @return bool|int
     */
    private function _insertEmail($email,$userName,$dyName,$url) {
        if (empty($email) || !isset($userName) || empty($dyName) || empty($url)) {
            return false;
        }
        $title = "您订阅的电影已有观看地址";
        $content = "尊敬的{$userName}用户,您好。您订阅的电影《[url={$url}]{$dyName}[/url]》已有观看地址，详情请查看[url={$url}]{$url}[/url],有问题联系我们，谢谢！";
        $insertData = array($title,$content,$email,time(),$userName);
        $sql = "insert into `tbl_emailQueue` (title,content,email,time,userName) values (?,?,?,?,?);";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->execute($insertData);
        return $stmt->rowCount();
    }

    static private $key = "abcdefghijklmnopqrstuvwxyz0123456789"; //可以多位 保证每位的字符在URL里面正常显示即可
    static private $_md5Len  = 10;//附加md5值长度
    /** 将id转换成字符串
     * @param $value
     * @return string
     */
    public function encodeId($value) {
        $base = strlen(self::$key );
        $arr = array();
        while( $value != 0 ) {
            $arr[] = $value % $base;
            $value = floor( $value / $base );
        }
        $result = "";
        while( isset($arr[0]) ) $result .= substr(self::$key, array_pop($arr), 1 );
        $md5Time = md5(microtime());
        $result = substr($md5Time,0,self::$_md5Len) . $result . substr($md5Time,self::$_md5Len,self::$_md5Len);
        return $result;
    }
    /** 将字符串转换成id
     * @param $value
     * @return string
     */
    public function decodeId($value) {
        $valueLen = strlen($value);
        $value = substr($value,self::$_md5Len,$valueLen - self::$_md5Len * 2);
        $base = strlen(self::$key);
        $num = 0;
        $key = array_flip( str_split(self::$key) );
        $arr = str_split($value);
        for($len = count($arr) - 1, $i = 0; $i <= $len; $i++) {
            $num += pow($base, $i) * $key[$arr[$len-$i]];
        }
        return $num;
    }
}