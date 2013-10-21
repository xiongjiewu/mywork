<?php
/**
 * 去除重复电影评分信息
 * added by xiongjiewu 2013-06-06
 * Class conformMovieInfo
 */
include("jobBase.php");
class removeRepeatTopInfo extends jobBase {
    private $_id;

    public function __construct()
    {
        parent::__construct();
        $this->_id = 0;
    }
    public function run() {
        global $argv;
        //删除动作类型
        $delType = empty($argv[2]) ? "infoId" : $argv[2];
        //删除类型
        $type = empty($argv[3]) ? 2 : $argv[3];
        $infos = $this->_getRepeatTopInfo($type,$delType);
        if (!empty($infos)) {
           foreach($infos as $infoVal) {
               if ($infoVal['cn'] >= 2) {
                    $deRes = $this->_updateMovieScoreInfo($infoVal[$delType],$infoVal['type'],array("del"=>1),$infoVal['cn'] - 1,$delType);
                   if (!empty($deRes)) {
                       var_dump("电影[{$infoVal[$delType]}]删除成功!\n");
                   } else {
                       var_dump("电影[{$infoVal[$delType]}]删除失败!\n");
                   }
               }
           }
        }
    }

    private function _getRepeatTopInfo($type = 2,$delType = "infoId") {
        $sql = "select count(1) as cn,{$delType},type from tbl_movieScore where type = " . $type . " and del = 0 group by {$delType} order by cn desc;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** 更新电影信息
     * @param array $dataArr 新数据数组
     * @return bool|int
     */
    protected function _updateMovieScoreInfo($filed,$type,$dataArr = array(),$limit =  1,$delType = "infoId")
    {
        $filed = trim($filed);
        $type = intval($type);
        if (empty($filed) || empty($dataArr) || empty($type)) {
            return false;
        }
        $dataArr = array_filter($dataArr);
        $setArr = $valArr = array();
        foreach($dataArr as $dataKey => $dataVal) {
            $setArr[] = "{$dataKey} = ?";
            $valArr[] = $dataVal;
        }
        $valArr[] = $filed;
        $setStr = implode(",",$setArr);
        $sql = "update `tbl_movieScore` set {$setStr} where {$delType} = ? and type = " . $type . " limit " . $limit;
        $stmt = $this->_pdo->prepare($sql);
        $stmt->execute($valArr);
        return $stmt->rowCount();
    }
}

$do = new removeRepeatTopInfo();
$do->run();