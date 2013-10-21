<?php
/**
 * 将之前被合并的电影信息插入到被合并表tbl_delMovieInfo
 * 此表记录被合并电影被合并到的电影信息，被合并时间
 * added by xiongjiewu 2013-06-12
 */
include("jobBase.php");
class insertDelMovieInfo extends  jobBase {
    private $_limit = 500;
    private $_id;
    private $_xiangtongC = 1;

    public function __construct()
    {
        parent::__construct();
        $this->_id = 0;
    }

    public function run() {
        while(true) {
            //处理的电影信息
            $movieInfos = $this->_getMoviceInfo($this->_id,$this->_limit,"all",1);
            if (empty($movieInfos)) {
                exit;
            }
            foreach($movieInfos as $movieVal) {
                $this->_id = $movieVal['id'];
                //主演信息为空，删除主演索引表信息
                if (!empty($movieVal['zhuyan']) && ($movieVal['zhuyan'] != "暂无")) {
                    //查询名字相同没有被删除的电影信息
                    $releationMovieInfo = $this->_getCurrentMovieInfoByName($movieVal['name']);
                    if (empty($releationMovieInfo) || empty($releationMovieInfo['zhuyan']) || ($releationMovieInfo['zhuyan'] == "暂无")) {
                        continue;
                    }
                    //被处理电影的演员信息
                    $movieVal['zhuyan'] = trim($movieVal['zhuyan']);
                    $movieVal['zhuyan'] = str_replace("　　","",trim($movieVal['zhuyan']));
                    $movieVal['zhuyan'] = str_replace("/","、",$movieVal['zhuyan']);
                    $movieVal['zhuyan'] = str_replace("，","、",$movieVal['zhuyan']);
                    $movieVal['zhuyan'] = str_replace(",","、",$movieVal['zhuyan']);
                    $movieActorInfo = explode("、",$movieVal['zhuyan']);

                    //名字相同的电影演员信息
                    $releationMovieInfo['zhuyan'] = trim($releationMovieInfo['zhuyan']);
                    $releationMovieInfo['zhuyan'] = str_replace("　　","",trim($releationMovieInfo['zhuyan']));
                    $releationMovieInfo['zhuyan'] = str_replace("/","、",$releationMovieInfo['zhuyan']);
                    $releationMovieInfo['zhuyan'] = str_replace("，","、",$releationMovieInfo['zhuyan']);
                    $releationMovieInfo['zhuyan'] = str_replace(",","、",$releationMovieInfo['zhuyan']);
                    $releationActorInfo = explode("、",$releationMovieInfo['zhuyan']);

                    //比较演员相同个数
                    if ($this->_judgeActorInfo($movieActorInfo,$releationActorInfo) >= $this->_xiangtongC) {
                        $insertInfo = array();
                        $insertInfo['infoId'] = $movieVal['id'];
                        $insertInfo['webId'] = $movieVal['webId'];
                        $insertInfo['webType'] = $movieVal['webType'];
                        $insertInfo['currentInfoId'] = $releationMovieInfo['id'];
                        $insertInfo['delTime'] = time();
                        $lastInsertId = $this->_insertInfo($insertInfo,"tbl_delMovieInfo");
                        if (!empty($lastInsertId)) {
                            var_dump("电影[{$movieVal['id']}]处理成功!\n");
                        } else {
                            var_dump("电影[{$movieVal['id']}]处理成功!\n");
                        }
                    }
                }
            }
        }
    }

    /**
     * 根据电影和id获取其他名称一致的电影信息
     * @param $name
     * @param $id
     * @return array|bool
     */
    private function _getCurrentMovieInfoByName($name) {
        $name = trim($name);
        if (empty($name)) {
            return false;
        }
        $sql = "select * from `tbl_detailInfo` where del = 0 and name = '" . $name . "' limit 1;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetch();
    }
}
$doo = new insertDelMovieInfo();
$doo->run();