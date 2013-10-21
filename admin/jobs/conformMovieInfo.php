<?php
/**
 * 整合电影job，电影名相同，演员有1个以上相同的默认为同一部电影，
 * 此时合并，并将信息整合起来，最终结果肯定是全部中最完整的。
 * added by xiongjiewu 2013-06-06
 * Class conformMovieInfo
 */
include("jobBase.php");
class conformMovieInfo extends jobBase {
    private $_id;
    private $_xiangtongC = 1;

    private $_replaceStrArr = array("：",":"," ","-");

    public function __construct()
    {
        parent::__construct();
        $this->_id = 0;
    }
    public function run() {
        while(true) {
            //需要处理的电影信息
            $movieInfo = $this->_getMoviceInfo($this->_id);
            if (empty($movieInfo)) {
                exit;
            }
            $this->_id = $movieInfo['id'];
            //名称替换字符串
            $releationNameInfo = $this->_getReplaceName($movieInfo['name']);
            //过滤掉特殊字符的名称
            $replaceName = $releationNameInfo['replaceName'];
            //相关电影信息
            $releationMovieInfo = $this->_getReleationMovieInfoByName($releationNameInfo["rName"],$movieInfo['id']);
            if (!empty($releationMovieInfo) && !empty($movieInfo['zhuyan'])) {
                //主演
                $movieInfo['zhuyan'] = str_replace("/","、",$movieInfo['zhuyan']);
                $movieActorInfo = explode("、",$movieInfo['zhuyan']);
                //导演
                $movieInfo['daoyan'] = str_replace("/","、",$movieInfo['daoyan']);
                $movieDoYanInfo = explode("、",$movieInfo['daoyan']);

                if (!empty($movieActorInfo) && count($movieActorInfo) >= $this->_xiangtongC ) {
                    $idsArr = $delInfoArr = array();
                    foreach($releationMovieInfo as $releationVal) {
                        $reNameInfo = $this->_getReplaceName($releationVal['name']);
                        if ($reNameInfo['replaceName'] != $replaceName) {//如果名字不相近，跳过处理下一个
                            continue;
                        }
                        //主演
                        $releationVal['zhuyan'] = str_replace("/","、",$releationVal['zhuyan']);
                        $releationActorInfo = explode("、",$releationVal['zhuyan']);
                        //导演
                        $releationVal['daoyan'] = str_replace("/","、",$releationVal['daoyan']);
                        $releationDaoYanInfo = explode("、",$releationVal['daoyan']);

                        $delInfoArr[$releationVal['id']] = $releationVal;
                        if (!empty($releationActorInfo) && count($releationActorInfo) >= $this->_xiangtongC) {
                            //如果演员为“暂无”而且图片没有
                            $aC = ($releationVal['zhuyan'] == "暂无" && $releationVal['image'] == $this->get_config_value("dy_common_img")) ? true : false;
                            //比较演员或者导演个数，符合则合并
                            if ($aC || ($this->_judgeActorInfo($movieActorInfo,$releationActorInfo) >= $this->_xiangtongC) || ($this->_judgeActorInfo($movieDoYanInfo,$releationDaoYanInfo) >= $this->_xiangtongC)) {
                                $idsArr[] = $releationVal['id'];//记录id数组
                                //合并后的信息，最完整的
                                $movieInfo = $this->_comMovieInfo($movieInfo,$releationVal);
                            }
                        }
                    }

                    //表示有需要合并的信息
                    if (!empty($idsArr)) {
                        //更新电影信息
                        $this->_updateDetailInfo($movieInfo['id'],$movieInfo);
                        foreach($idsArr as $id) {
                            //删除电影废弃的电影
                            $upRes = $this->_updateDetailInfo($id,array("del"=>1));

                            if (!empty($upRes)) {//合并观看链接+下载链接+排行榜+删除废弃电影演员+删除废弃电影导演
                                //更新观看链接
                                $this->_updateWatchOrDownLinkInfo($id,array("infoId" => $movieInfo['id']),"tbl_watchLink");
                                //更新下载链接
                                $this->_updateWatchOrDownLinkInfo($id,array("infoId" => $movieInfo['id']),"tbl_downLoad");
                                //更新排行榜id
                                $this->_updateWatchOrDownLinkInfo($id,array("infoId" => $movieInfo['id']),"tbl_movieScore");
                                //删除搜索榜
                                $this->_updateWatchOrDownLinkInfo($id,array("del" => 1),"tbl_movieSearch");
                                //更新评论id
                                $this->_updateWatchOrDownLinkInfo($id,array("infoId" => $movieInfo['id']),"tbl_yingping");
                                //更新订阅通知id
                                $this->_updateWatchOrDownLinkInfo($id,array("infoId" => $movieInfo['id']),"tbl_notice");
                                //更新收藏id
                                $this->_updateWatchOrDownLinkInfo($id,array("infoId" => $movieInfo['id']),"tbl_shoucang");
                                //更新用户提供链接id
                                $this->_updateWatchOrDownLinkInfo($id,array("infoId" => $movieInfo['id']),"tbl_userGive");
                                //更新影评链接id
                                $this->_updateWatchOrDownLinkInfo($id,array("infoId" => $movieInfo['id']),"tbl_yingpingLink");
                                //删除演员
                                $this->_updateUserInfoById($id,"tbl_actInfo");
                                //更新用户观看记录id
                                $this->_updateWatchOrDownLinkInfo($id,array("infoId" => $movieInfo['id']),"tbl_userViewingRecords");
                                //更新票房记录id
                                $this->_updateWatchOrDownLinkInfo($id,array("infoId" => $movieInfo['id']),"tbl_boxOfficeInfo");
                                //更新奖项id
                                $this->_updateWatchOrDownLinkInfo($id,array("infoId" => $movieInfo['id']),"tbl_moviePrizeInfo");
                                //更新专题/系列电影inId
                                $this->_updateWatchOrDownLinkInfo($id,array("infoId" => $movieInfo['id']),"tbl_movieTopicMovie");
                                //删除导演
                                $this->_updateUserInfoById($id,"tbl_directorInfo");
                                //更新被合并信息表
                                $this->_updateDelMovieInfo($id,array("currentInfoId" => $movieInfo['id']));
                                //插入被合并信息
                                $insertInfo = array();
                                $insertInfo['infoId'] = $id;
                                $insertInfo['webId'] = $delInfoArr[$id]['webId'];
                                $insertInfo['webType'] = $delInfoArr[$id]['webType'];
                                $insertInfo['currentInfoId'] = $movieInfo['id'];
                                $insertInfo['delTime'] = time();
                                $this->_insertInfo($insertInfo,"tbl_delMovieInfo");
                                var_dump("电影[{$id}]合并到电影[{$movieInfo['id']}]成功!\n");
                            } else {
                                var_dump("电影[{$id}]合并到电影[{$movieInfo['id']}]失败!\n");
                            }
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
    private function _updateWatchOrDownLinkInfo($InfoId,$dataArr = array(),$tableName = "tbl_watchLink")
    {
        $InfoId = intval($InfoId);
        if (empty($InfoId) || empty($dataArr)) {
            return false;
        }
        $dataArr = array_filter($dataArr);
        $setArr = $valArr = array();
        foreach($dataArr as $dataKey => $dataVal) {
            $setArr[] = "{$dataKey} = ?";
            $valArr[] = $dataVal;
        }
        $setStr = implode(",",$setArr);
        $sql = "update `" . $tableName . "` set {$setStr} where infoId = {$InfoId};";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->execute($valArr);
        return $stmt->rowCount();
    }

    /**
     * 比较2个电影信息，最后返回2个电影的整合信息
     * @param $movieInfo1
     * @param $movieInfo2
     */
    private function _comMovieInfo($movieInfo1,$movieInfo2) {
        $scoreCount1 = $movieInfo1['totalStartNum'];
        $scoreCount2 = $movieInfo2['totalStartNum'];
        foreach($movieInfo1 as $infoKey => $infoVal) {
            if (($infoKey  == "daoyan" || $infoKey  == "zhuyan" || $infoKey == "jieshao")) {
                if (!empty($movieInfo2[$infoKey]) && (trim($movieInfo2[$infoKey]) != "暂无")) {
                    if (trim($infoVal) == "暂无" || (mb_strlen($movieInfo2[$infoKey],"UTF-8") > mb_strlen($movieInfo1[$infoKey],"UTF-8"))) {
                        $movieInfo1[$infoKey] = $movieInfo2[$infoKey];
                    }
                }
            } elseif ($infoKey  == "playNum" || $infoKey  == "downNum" || $infoKey  == "searchNum" || $infoKey  == "yaoyaoNum" || $infoKey  == "start1Num" || $infoKey  == "start2Num" || $infoKey  == "start3Num" || $infoKey  == "start4Num" || $infoKey  == "start5Num" || $infoKey  == "totalStartNum") {
                $movieInfo1[$infoKey] += $movieInfo2[$infoKey];
            } elseif ($infoKey  == "score") {
                if (($scoreCount1 + $scoreCount2) <= 0) {
                    $newScore = 0;
                } else {
                    $newScore = ($scoreCount1 * $movieInfo1['score'] + $scoreCount2 * $movieInfo2['score']) / ($scoreCount1 + $scoreCount2);
                }
                $movieInfo1['score'] = $newScore;
            } elseif ($infoKey  == "image") {
                if ((!file_exists(rtrim(IMGPATH,"/") . $movieInfo1['image']) || (strpos($movieInfo1['image'],"dy_common.jpg") !== false)) && file_exists(rtrim(IMGPATH,"/") . $movieInfo2['image'])) {
                    $movieInfo1["image"] = $movieInfo2["image"];
                }
            } else  {
                if (empty($infoVal) && !empty($movieInfo2[$infoKey])) {
                    $movieInfo1[$infoKey] = $movieInfo2[$infoKey];
                }
            }
        }
        return $movieInfo1;
    }

    /** 更新电影信息
     * @param array $dataArr 新数据数组
     * @return bool|int
     */
    private function _updateDelMovieInfo($InfoId,$dataArr = array())
    {
        $InfoId = intval($InfoId);
        if (empty($InfoId) || empty($dataArr)) {
            return false;
        }
        $dataArr = array_filter($dataArr);
        $setArr = $valArr = array();
        foreach($dataArr as $dataKey => $dataVal) {
            $setArr[] = "{$dataKey} = ?";
            $valArr[] = $dataVal;
        }
        $setStr = implode(",",$setArr);
        $sql = "update `tbl_delMovieInfo` set {$setStr} where currentInfoId = {$InfoId};";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->execute($valArr);
        return $stmt->rowCount();
    }

    /**
     * 根据电影和id获取其他名称一致的电影信息
     * @param $name
     * @param $id
     * @return array|bool
     */
    protected function _getReleationMovieInfoByName($name, $id)
    {
        $name = trim ($name);
        $id = intval ($id);
        if (empty($name) || empty($id)) {
            return false;
        }
        $sql = "select * from `tbl_detailInfo` where del = 0 and id > " . $id . " and name like '" . $name . "%';";
        $stmt = $this->_pdo->prepare ($sql);
        $stmt->setFetchMode (PDO::FETCH_ASSOC);
        $stmt->execute ();
        return $stmt->fetchAll ();
    }

    /**
     * 获取处理的电影名称
     */
    private function _getReplaceName($name) {
        foreach($this->_replaceStrArr as $str) {
            //名称替换字符串
            $name = preg_replace("/([0-9]+{$str})|({$str})/",",",$name);
        }

        $releationNameArr = explode(",",$name);
        //过滤掉特殊字符的名称
        $replaceName = str_replace(",","",$name);
        if (empty($releationNameArr[0])) {
            return array("replaceName" => $replaceName,"rName" => $releationNameArr[1]);
        } else {
            return array("replaceName" => $replaceName,"rName" => $releationNameArr[0]);
        }
    }
}

$do = new conformMovieInfo();
$do->run();
