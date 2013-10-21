<?php
/**
 * 校正观看链接中的播放器类型信息
 * added by xiongjiewu at 2013-4-22
 */
$do = new ReviseWatchType();
$do->run();
class ReviseWatchType extends myPdo
{
    private $_id;
    private $_pdo;
    private $_limit = 500;

    public function __construct()
    {
        $this->_pdo = $this->getPdo();
        $this->_id = 0;
    }
    public function run() {
        while(true) {
            $watchInfos = $this->_getWatchLinkInfo();
            if (!empty($watchInfos)) {
                foreach($watchInfos as $infoVal) {
                    $this->_id = $infoVal['id'];
                    if (strpos($infoVal['link'],"pps.") !== false) {
                        $this->_updateWatchPlayerTypeById($infoVal['id'],11);
                    } elseif (strpos($infoVal['link'],"sohu.") !== false) {//搜狐
                        $this->_updateWatchPlayerTypeById($infoVal['id'],7);
                    } elseif (strpos($infoVal['link'],"pptv.") !== false) {
                        $this->_updateWatchPlayerTypeById($infoVal['id'],10);
                    } elseif (strpos($infoVal['link'],"funshion.") !== false) {//风行
                        $this->_updateWatchPlayerTypeById($infoVal['id'],9);
                    } elseif (strpos($infoVal['link'],"sina.") !== false) {//新浪
                        $this->_updateWatchPlayerTypeById($infoVal['id'],8);
                    } elseif (strpos($infoVal['link'],"letv.") !== false) {//乐视
                        $this->_updateWatchPlayerTypeById($infoVal['id'],13);
                    } elseif (strpos($infoVal['link'],"m1905.") !== false) {//电影网
                        $this->_updateWatchPlayerTypeById($infoVal['id'],14);
                    }
                    var_dump($infoVal['id'] . "---do--\n");
                }
            } else {
                exit;
            }
        }
    }

    private function _getWatchLinkInfo() {
        $sql = "select id,link,player from `tbl_watchLink` where id >" . $this->_id;
        $sql .= " order by id asc limit " . $this->_limit;
        $stmt = $this->_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function _updateWatchPlayerTypeById($id,$playerType) {
        $id = intval($id);
        $playerType = intval($playerType);
        if (empty($id) || empty($playerType)) {
            return false;
        }
        $sql = "update `tbl_watchLink` set player = {$playerType} where id = ? limit 1;;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->execute(array($id));
        return $stmt->rowCount();
    }
}