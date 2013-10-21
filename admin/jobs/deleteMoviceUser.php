<?php
/**
 * 将已经删除掉的电影的演员和导演信息删除
 * added by xiongjiewu at 2013-06-04
 */
$do = new DeleteMoviceUser();
$do->run();
class DeleteMoviceUser extends myPdo
{
    private $_limit = 500;//每次处理个数
    private $_id;//当前跑到的id
    private $_pdo;

    public function __construct()
    {
        $this->_pdo = $this->getPdo();
        $this->_id = 0;
    }

    public function run() {
        while(true) {
            //获取需要删除的电影信息
            $moviceInfo = $this->_getDeleteMoviceInfo();
            if (!empty($moviceInfo)) {
                foreach($moviceInfo as $mVal) {
                    $this->_id = $mVal['id'];
                    //删除演员信息
                    $rz = $this->_deleteUserInfoByInfoId($mVal['id']);
                    //删除导演信息
                    $rd = $this->_deleteUserInfoByInfoId($mVal['id'],"tbl_directorInfo");
                    var_dump($rz."---" . $rd . "--\n");
                }
            } else {
                exit;
            }
        }
    }

    /**
     * 获取已经删除的电影信息
     * @return mixed
     */
    private function _getDeleteMoviceInfo() {
        $sql = "select * from `tbl_detailInfo` where del = 1 and id > " . $this->_id ." order by id asc limit " . $this->_limit;
        $stmt = $this->_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** 删除已经删除的电影的演员和导演信息
     * @param $name
     * @param $time1
     * @return bool|mixed
     */
    private function _deleteUserInfoByInfoId($id,$tableName = "tbl_actInfo") {
        $id = intval($id);
        if (!isset($id)) {
            return false;
        }
        $sql = "update `" . $tableName . "` set del = 1 where infoId = {$id};";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount();
    }
}