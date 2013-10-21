<?php
/**
 * 把抓取的电影信息更新进入电影列表
 * added by xiongjiewu at 2013-04-29
 */
class UpdateGrabMoviceToList extends myPdo
{
    private $_pdo;
    private $_limit = 1000;
    public function __construct() {
        $this->_pdo = $this->getPdo();
    }
    public function run() {
        while(true) {
            $moviceInfos = $this->_getGrabNeedDoInfo();
            if (!empty($moviceInfos)) {
                foreach($moviceInfos as $moviceVal) {
                    //检查信息是否合法
                    $checkRes = $this->_checkDetail($moviceVal);
                    if (!$checkRes['code']) {
                        printf("ID为：%s 的电影{$checkRes['error']}---%s---\n",$moviceVal['id'],date("Y-m-d H:i:s"));
                    } else {
                        //检查电影列表是否存在信息
                        $info = $this->_getMoviceInfoByNameAndNianfen($moviceVal['name'],$moviceVal['nianfen']);
                        if (!empty($info)) {
                            printf("ID为：%s 的电影信息已存在于电影列表中---%s---\n",$moviceVal['id'],date("Y-m-d H:i:s"));
                            $this->_updateGrabMoviceInfoById($moviceVal['id']);
                        } else {
                            //插入信息
                            $insertRes = $this->_insertMoviceInfo($moviceVal);
                            if ($insertRes == 1) {
                                printf("ID为：%s 的电影信息处理成功---%s---\n",$moviceVal['id'],date("Y-m-d H:i:s"));
                                $this->_updateGrabMoviceInfoById($moviceVal['id']);
                            } else {
                                printf("ID为：%s 的电影信息处理失败---%s---\n",$moviceVal['id'],date("Y-m-d H:i:s"));
                            }
                        }
                    }
                }
            }
        }
    }

    private $_detailFildInfo = array(//detailInfo表各字段信息
        "name" => array("null" => false,'title' => "名称"),
        "type" => array("null" => false,'title' => "类型"),
        "jieshao" => array("null" => false,'title' => "简介"),
        "zhuyan" => array("null" => false,'title' => "主演"),
        "time0" => array("null" => true,'title' => "本周提供观看链接时间"),
        "time1" => array("null" => true,'title' => "中国上映时间"),
        "time2" => array("null" => true,'title' => "欧美上映时间"),
        "time3" => array("null" => true,'title' => "港台上映时间"),
        "diqu" => array("null" => false,'title' => "地区"),
        "nianfen" => array("null" => true,'title' => "年份"),
        "daoyan" => array("null" => false,'title' => "导演"),
        "shichang" => array("null" => false,'title' => "时长"),
        "image" => array("null",'title' => "图片")
    );

    /** 检查信息是否合法
     * @param array $info
     * @return array
     */
    private function _checkDetail($info = array())
    {
        $result = array(
            "code" => false,
            "error" => "参数错误！",
        );
        if (empty($info)) {
            return $result;
        }
        foreach($this->_detailFildInfo as $infoKey => $infoVal) {
            if (empty($infoVal['null']) && !$info[$infoKey]) {
                $result['code'] = false;
                $result['error'] = $infoVal['title'] . "参数错误";
                break;
            } else {
                $result['code'] = true;
            }
        }
        return $result;
    }

    /** 获取需要处理的电影信息
     * @return array
     */
    private function _getGrabNeedDoInfo() {
        $sql = "select id,name,type,jieshao,zhuyan,time0,time1,time2,time3,diqu,nianfen,daoyan,shichang,image,topType";
        $sql .= " from `tbl_grabMovice` where del = 0 and inType = 1 order by id asc limit " . $this->_limit;
        $stmt = $this->_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** 根据名称和上映时间获取电影信息
     * @param $name
     * @param $time1
     * @return bool|mixed
     */
    private function _getMoviceInfoByNameAndNianfen($name,$nianfen) {
        $nianfen = intval($nianfen);
        if (!isset($name) || empty($nianfen)) {
            return false;
        }
        $sql = "select * from `tbl_detailInfo` where name like '{$name}%' and nianfen = {$nianfen} limit 1;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetch();
    }

    /** 把数据插入电影列表中
     * @param array $dataArr 数据数组
     * @return bool|int
     */
    private function _insertMoviceInfo($dataArr = array()) {
        if (empty($dataArr)) {
            return false;
        }
        unset($dataArr['id']);
        $keyArr = array_keys($dataArr);
        $keyStr = implode(",",$keyArr);
        $valueArr = array_fill(0, count($dataArr), '?');
        $valueStr = implode(",",$valueArr);
        $sql = "insert into `tbl_detailInfo` ({$keyStr}) values ({$valueStr});";
        $stmt = $this->_pdo->prepare($sql);
        $dataArr = explode("\t\t\t",implode("\t\t\t",$dataArr));
        $stmt->execute($dataArr);
        return $stmt->rowCount();
    }

    /** 更新抓取的电影信息
     * @param $id
     * @return bool|int
     */
    private function _updateGrabMoviceInfoById($id) {
        $id = intval($id);
        if (empty($id)) {
            return false;
        }
        $sql = "update `tbl_grabMovice` set inType = 2 where id = ? limit 1;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->execute(array($id));
        return $stmt->rowCount();
    }
}
$run = new UpdateGrabMoviceToList();
$run->run();