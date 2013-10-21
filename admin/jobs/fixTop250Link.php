<?php
/**
 * 给top250经典电影添加链接电影信息
 * added by xiongjiewu at 2013-05-06
 */
class fixTop250Link extends myPdo
{
    private $_id;
    private $_pdo;
    private $_limit = 100;

    public function __construct()
    {
        $this->_pdo = $this->getPdo();
        $this->_id = 0;
    }
    public function run() {
        global $argv;
        if (!empty($argv[2])) {
            $this->_id = $argv[2];
            $moviceInfo = $this->_getTop250NotLinkInfos();
            if (!empty($moviceInfo)) {

            }
        } else {
            while(true) {
                $moviceInfo = $this->_getTop250NotLinkInfos();

                if (!empty($moviceInfo)) {
                    foreach($moviceInfo as $infoVal) {
                        $this->_id = $infoVal['id'];

                        //读取相同名字而且有观看连接的电影信息
                        $hasWatchMoviceInfo = $this->_getMoviceInfoByName($infoVal['name']);

                        if (!empty($hasWatchMoviceInfo)) {
                            foreach($hasWatchMoviceInfo as $watchMovice) {
                                $watchLinkInfo = $this->_getWatchLinkInfoByInfoId($watchMovice['id']);

                                if (!empty($watchLinkInfo)) {
                                    $i = 0;
                                    foreach($watchLinkInfo as $watch) {
                                        unset($watch['id']);
                                        $watch['infoId'] = $infoVal['id'];
                                        $resId = $this->_insertWatchLinkInfo($watch);
                                        if (!empty($resId)) {
                                            var_dump("[{$infoVal['id']}]--do--\n");
                                            if ($i == 0) {
                                                $this->_updateMoviceInfoById($infoVal['id']);
                                            }
                                        } else {
                                            var_dump("[{$infoVal['id']}]--fail--\n");
                                        }
                                        $i++;
                                    }
                                }
                            }
                        }
                    }
                } else {
                    exit;
                }
            }
        }
    }

    /**
     * 获取没有链接的top250电影信息
     */
    private function _getTop250NotLinkInfos() {
        $sql = "select id,name from `tbl_detailInfo` where del = 0 and id > " . $this->_id ." and topType = 1 and exist_watch = 0 order by id asc limit " . $this->_limit;
        $stmt = $this->_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function _getMoviceInfoByName($name) {
        $name = trim($name);
        if (empty($name)) {
            return false;
        }
        $sql = "select id from `tbl_detailInfo` where del = 0 and exist_watch = 1 and name = '{$name}';";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function _getWatchLinkInfoByInfoId($infoId) {
        $infoId = intval($infoId);
        if (empty($infoId)) {
            return false;
        }
        $sql = "select * from `tbl_watchLink` where infoId = {$infoId};";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** 插入观看链接信息
     * @param array $dataArr 数据数组
     * @return bool|int
     */
    private function _insertWatchLinkInfo($dataArr = array())
    {
        if (empty($dataArr)) {
            return false;
        }
        $keyArr = array_keys($dataArr);
        $keyStr = implode(",", $keyArr);
        $valueArr = array_fill(0, count($dataArr), '?');
        $valueStr = implode(",", $valueArr);
        $sql = "insert into `tbl_watchLink` ({$keyStr}) values ({$valueStr});";
        $stmt = $this->_pdo->prepare($sql);
        $dataArr = explode("[AAAAA]", implode("[AAAAA]", $dataArr));
        $stmt->execute($dataArr);
        return $this->_pdo->lastInsertId();
    }

    private function _updateMoviceInfoById($id)
    {
        $id = intval($id);
        if (empty($id)) {
            return false;
        }
        $sql = "update `tbl_detailInfo` set exist_watch = 1 where id = ? limit 1;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->execute(array($id));
        return $stmt->rowCount();
    }
}
$do = new fixTop250Link();
$do->run();