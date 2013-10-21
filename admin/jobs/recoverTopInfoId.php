<?php
/**
 * 恢复经典电影信息
 * Class recoverTopInfoId
 */
class recoverTopInfoId extends  myPdo {
    private $_id;
    private $_pdo;
    private $_limit = 1000;

    public function __construct()
    {
        $this->_pdo = $this->getPdo();
        $this->_id = 0;
    }
    public function run() {
        while(true) {
            //需要处理的电影评分信息
            $movieScoreInfo = $this->_getMoviceScoreInfo();
            if (empty($movieScoreInfo)) {
                exit;
            }
            foreach($movieScoreInfo as $infoVal) {
                $this->_id = $id = $infoVal['id'];
                //获取原所属电影信息
                $movieInfo = $this->_getMovieInfoById($infoVal['infoId']);
                if (!empty($movieInfo)) {
                    //获取新电影信息
                    $topInfo = $this->_getMovieInfoByName($movieInfo['name'],$movieInfo['topType']);
                    if (!empty($topInfo)) {
                        $upRes = $this->_updateScoreInfo($infoVal['id'],array("infoId"=>$topInfo['id']));
                        if (!empty($upRes)) {
                            var_dump("电影[{$id}]更新到[{$topInfo['id']}]成功!\n");
                        } else {
//                            var_dump("电影[{$id}]更新到[{$topInfo['id']}]失败!\n");
                        }
                    }
                }
            }
        }
    }

    /** 更新电影信息
     * @param array $dataArr 新数据数组
     * @return bool|int
     */
    private function _updateScoreInfo($id,$dataArr = array())
    {
        $id = intval($id);
        if (empty($id) || empty($dataArr)) {
            return false;
        }
        $dataArr = array_filter($dataArr);
        $setArr = $valArr = array();
        foreach($dataArr as $dataKey => $dataVal) {
            $setArr[] = "{$dataKey} = ?";
            $valArr[] = $dataVal;
        }
        $setStr = implode(",",$setArr);
        $sql = "update `tbl_movieScore` set {$setStr} where id = {$id} limit 1;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->execute($valArr);
        return $stmt->rowCount();
    }

    /**
     * 获取当前处理的电影信息
     * @return mixed
     */
    private function _getMoviceScoreInfo() {
        $sql = "select * from `tbl_movieScore` where del = 0 and id > " . $this->_id ." order by id asc limit " . $this->_limit;
        $stmt = $this->_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetchAll();
    }


    /**
     * 根据电影和id获取其他名称一致的电影信息
     * @param $name
     * @param $id
     * @return array|bool
     */
    private function _getMovieInfoById($id) {
        $id = trim($id);
        if (empty($id)) {
            return false;
        }
        $sql = "select * from `tbl_detailInfo` where id = " . $id ." limit 1;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * 根据电影和id获取其他名称一致的电影信息
     * @param $name
     * @param $id
     * @return array|bool
     */
    private function _getMovieInfoByName($name,$topType) {
        $name = trim($name);
        $topType = intval($topType);
        if (empty($name) || empty($topType)) {
            return false;
        }
        $sql = "select * from `tbl_detailInfo` where del = 0 and topType = " . $topType ." and name = '" . $name . "' limit 1;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetch();
    }
}
$do = new recoverTopInfoId();
$do->run();