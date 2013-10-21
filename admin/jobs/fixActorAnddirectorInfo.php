<?php
/**
 * 修复电影演员和导演信息，使得电影演员和导演于相应索引表的信息保持一致
 * added by xiongjiewu 2013-06-07
 */
include("jobBase.php");
class fixActorAnddirectorInfo extends  jobBase {
    private $_limit = 500;
    private $_id;

    public function __construct()
    {
        parent::__construct();
        $this->_id = 0;
    }
    public function run() {
        while(true) {
            //处理的电影信息
            $movieInfos = $this->_getMoviceInfo($this->_id,$this->_limit,"all");
            if (empty($movieInfos)) {
                exit;
            }
            foreach($movieInfos as $movieVal) {
                $this->_id = $movieVal['id'];
                $upDataArr = array();
                //主演信息为空，删除主演索引表信息
                if (empty($movieVal['zhuyan']) || ($movieVal['zhuyan'] == "暂无")) {
                    $this->_updateUserInfoById($movieVal['id'],"tbl_actInfo");
                } else {
                    $movieVal['zhuyan'] = trim($movieVal['zhuyan']);
                    $movieVal['zhuyan'] = str_replace("　　","",trim($movieVal['zhuyan']));
                    $movieVal['zhuyan'] = str_replace("/","、",$movieVal['zhuyan']);
                    $movieVal['zhuyan'] = str_replace("，","、",$movieVal['zhuyan']);
                    $movieVal['zhuyan'] = str_replace(",","、",$movieVal['zhuyan']);
                    $upDataArr['zhuyan'] = $movieVal['zhuyan'];
                    //演员信息
                    $acTorInfo = $this->_getMovieActorInfo($movieVal['id']);
                    //电影演员数组
                    $yanYuanArr = explode("、",$movieVal['zhuyan']);
                    if (empty($acTorInfo)) {//索引表无信息
                        foreach($yanYuanArr as $yanyuan) {//插入索引表
                            $yanyuan = trim($yanyuan);
                            $insertInfo = array("infoId" => $movieVal['id'],"name" => $yanyuan);
                            $firstLetter = $this->getFirstLetter($yanyuan);
                            if ($firstLetter != "*") {
                                $insertInfo['firstLetter'] = $firstLetter;
                            }
                            //拼音
                            $pinyin = $this->getPinyin($yanyuan,2);
                            if (!empty($pinyin)) {
                                $insertInfo['pinyin'] = $pinyin;
                            }
                            $this->_insertActOrDirectorInfo($insertInfo);
                        }
                    } else {
                        //演员索引表演员数组
                        $acTorArr = $this->_getActorArr($acTorInfo);
                        //获取需要删除的索引表id数组和需要新增的索引信息数组
                        list($delIdArr,$addInfoArr) = $this->_comUserInfo($yanYuanArr,$acTorArr);
                        //删除
                        if (!empty($delIdArr)) {
                            $this->_delUserInfoByIdArr($delIdArr);
                        }
                        //新增
                        if (!empty($addInfoArr)) {
                            foreach($addInfoArr as $val) {
                                $val = trim($val);
                                $insertInfo = array("infoId" => $movieVal['id'],"name" => $val);
                                $firstLetter = $this->getFirstLetter($val);
                                if ($firstLetter != "*") {
                                    $insertInfo['firstLetter'] = $firstLetter;
                                }
                                //拼音
                                $pinyin = $this->getPinyin($val,2);
                                if (!empty($pinyin)) {
                                    $insertInfo['pinyin'] = $pinyin;
                                }
                                $this->_insertActOrDirectorInfo($insertInfo);
                            }
                        }
                    }
                }
                //导演信息为空，删除导演索引表信息
                if (empty($movieVal['daoyan']) || ($movieVal['daoyan'] == "暂无")) {
                    $this->_updateUserInfoById($movieVal['id'],"tbl_directorInfo");
                } else {
                    $movieVal['daoyan'] = trim($movieVal['daoyan']);
                    $movieVal['daoyan'] = str_replace("　　","",trim($movieVal['daoyan']));
                    $movieVal['daoyan'] = str_replace("/","、",$movieVal['daoyan']);
                    $movieVal['daoyan'] = str_replace("，","、",$movieVal['daoyan']);
                    $movieVal['daoyan'] = str_replace(",","、",$movieVal['daoyan']);
                    $upDataArr['daoyan'] = $movieVal['daoyan'];
                    //导演信息
                    $directorInfo = $this->_getDirectorInfo($movieVal['id']);
                    //电影导演数组
                    $daoYanArr = explode("、",$movieVal['daoyan']);
                    if (empty($directorInfo)) {//索引表无信息
                        foreach($daoYanArr as $daoyuan) {//插入索引表
                            $daoyuan = trim($daoyuan);
                            $insertInfo = array("infoId" => $movieVal['id'],"name" => $daoyuan);
                            $firstLetter = $this->getFirstLetter($daoyuan);
                            if ($firstLetter != "*") {
                                $insertInfo['firstLetter'] = $firstLetter;
                            }
                            //拼音
                            $pinyin = $this->getPinyin($daoyuan,2);
                            if (!empty($pinyin)) {
                                $insertInfo['pinyin'] = $pinyin;
                            }
                            $this->_insertActOrDirectorInfo($insertInfo,"tbl_directorInfo");
                        }
                    } else {
                        //演员索引表演员数组
                        $directorInfoArr = $this->_getActorArr($directorInfo);
                        //获取需要删除的索引表id数组和需要新增的索引信息数组
                        list($delIdArr,$addInfoArr) = $this->_comUserInfo($daoYanArr,$directorInfoArr);
                        //删除
                        if (!empty($delIdArr)) {
                            $this->_delUserInfoByIdArr($delIdArr,"tbl_directorInfo");
                        }
                        //新增
                        if (!empty($addInfoArr)) {
                            foreach($addInfoArr as $val) {
                                $val = trim($val);
                                $insertInfo = array("infoId" => $movieVal['id'],"name" => $val);
                                $firstLetter = $this->getFirstLetter($val);
                                if ($firstLetter != "*") {
                                    $insertInfo['firstLetter'] = $firstLetter;
                                }
                                //拼音
                                $pinyin = $this->getPinyin($val,2);
                                if (!empty($pinyin)) {
                                    $insertInfo['pinyin'] = $pinyin;
                                }
                                $this->_insertActOrDirectorInfo($insertInfo,"tbl_directorInfo");
                            }
                        }
                    }
                }
                if (!empty($upDataArr)) {//更新主演和导演，过滤首尾空格
                    $upDataArr['jieshao'] = str_replace("　　","",trim($movieVal['jieshao']));
                    $upDataArr['jieshao'] = trim($upDataArr['jieshao']);
                    $this->_updateDetailInfo($movieVal['id'],$upDataArr);
                }
                var_dump("电影[{$movieVal['id']}]处理成功!\n");
            }
        }
    }

    /** 根据id数组删除索引表信息
     * @param $idArr
     * @param string $tableName
     * @return bool
     */
    private function _delUserInfoByIdArr($idArr,$tableName = "tbl_actInfo") {
        if (empty($idArr)) {
            return false;
        }
        foreach($idArr as $id) {
            $this->_delUserInfoById($id,$tableName);
        }
        return true;
    }

    /**
     * 比较2个数组信息，返回差异
     * @param $arrInfo1
     * @param $arrInfo2
     * @return array
     */
    private function _comUserInfo($arrInfo1,$arrInfo2) {
        $delIdArr = $addInfoArr = array();//需要删除的id数组+需要新增的信息数组+需要更新数组
        foreach($arrInfo2 as $arrKey2 => $arrVa2) {
            if (!in_array($arrVa2,$arrInfo1)) {
                $delIdArr[] = $arrKey2;
            }
        }
        foreach($arrInfo1 as $arrVal1) {
            if (!in_array($arrVal1,$arrInfo2)) {
                $addInfoArr[] = $arrVal1;
            }
        }
        return array($delIdArr,$addInfoArr);
    }

}
$do = new fixActorAnddirectorInfo();
$do->run();
