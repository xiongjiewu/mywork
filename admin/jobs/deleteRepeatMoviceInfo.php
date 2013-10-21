<?php
/**
 * 删除名字重复的电影信息
 * added by xiongjiewu at 2013-05-06
 */
$do = new DeleteRepeatMoviceInfo();
$do->run();
class DeleteRepeatMoviceInfo extends myPdo
{
    private $_id;
    private $_pdo;

    public function __construct()
    {
        $this->_pdo = $this->getPdo();
        $this->_id = 0;
    }

    public function run() {
        global $argv;
        if (!empty($argv[2])) {
            $this->_id = $argv[2];
            $moviceInfo = $this->_getMoviceInfo();
            if (!empty($moviceInfo)) {
                $r = $this->_updateMoviceInfoByIdAndName($moviceInfo['id'],$moviceInfo['name'],$moviceInfo['webType']);
                var_dump($r);
            }
        } else {
            while(true) {
                $moviceInfo = $this->_getMoviceInfo();
                if (!empty($moviceInfo)) {
                    $this->_id = $moviceInfo['id'];
                    $r = $this->_updateMoviceInfoByIdAndName($moviceInfo['id'],$moviceInfo['name'],$moviceInfo['webType']);
                    var_dump($r);
                } else {
                    exit;
                }
            }
        }
    }

    private function _getMoviceInfo() {
        $sql = "select id,name,webType from `tbl_detailInfo` where del = 0 and id > " . $this->_id ." order by id asc limit 1";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetch();
    }

    /** 根据名称和上映时间获取电影信息
     * @param $name
     * @param $time1
     * @return bool|mixed
     */
    private function _updateMoviceInfoByIdAndName($id,$name,$webType) {
        $id = intval($id);
        $webType = intval($webType);
        if (!isset($name) || empty($id) || empty($webType)) {
            return false;
        }
        $sql = "update `tbl_detailInfo` set del = 1 where name = '{$name}' and id > {$id} and webType = {$webType};";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount();
    }
}
