<?php
/**
 * 抓取电影下载链接
 * added by xiongjiewu at 2013-06-28
 */
include("jobBase.php");
class getdownlink extends jobBase {
    private $_idFile;
    private $_webConfigInfo;

    private $_filePath;
    private $_G;
    function __construct() {
        parent::__construct();
        $this->_idFile = $this->get_config_value("zhuaqu_movice_id_file_path","downlink"); //当前配置文件，读取控制抓取网站
        $this->_webConfigInfo = $this->get_config_value("zhuaqu_web_info","downlink");
        $this->_filePath = $this->get_config_value("zhuaqu_movie_error_log","downlink");
        $this->_G = date("G");
    }
    public function run() {
        //读取抓取网站和抓取链接信息
        $webname = json_decode(trim(file_get_contents($this->_idFile)), true);

        //判断信息是否有效
        if (!empty($webname['name']) && (strtoupper($webname['name']) != "END")) {
            $webConfigInfo = $this->_webConfigInfo;
            if (!empty($webConfigInfo[$webname['name']])) {
                if (!empty($webname['urlType']) && is_array($webConfigInfo[$webname['name']][$webname['urlType']])) {
                    foreach ($webConfigInfo[$webname['name']][$webname['urlType']] as $url) {
                        $functionName = "_" . $webname['name'];//调用函数，规定函数名由"_"与网站名称组成
                        if (!is_array($url)) {//如果不是数组
                            $this->$functionName($url, $webname['urlType']);
                        } else {
                            if (!empty($url['fenye'])) {//url是分页的
                                if (!empty($webname['fenye']) && (intval($webname['fenye']) > $url['start'])) {
                                    $url['start'] = $webname['fenye'];
                                }
                                for($i = $url['start'];$i <= $url['end'];$i++) {
                                    file_put_contents($this->_idFile, json_encode(array("name" => $webname['name'], "urlType" => $webname['urlType'],"fenye" => $i))); //作执行结束标志
                                    $realUrl = str_replace("{A}",$i,$url['base_url']);
                                    $this->$functionName($realUrl, $webname['urlType']);
                                }
                            } else {

                            }
                        }
                    }
                }

                $currentVal = $webConfigInfo[$webname['name']][$webname['urlType']];
                $currentWebName = $webname['name'];
                $key = array_keys($webConfigInfo[$webname['name']]);
                $val = array_values($webConfigInfo[$webname['name']]);
                $nextIndex = array_search($currentVal,$val) + 1;
                if (!empty($key[$nextIndex])) {//下一个元素存在
                    file_put_contents($this->_idFile, json_encode(array("name" => $currentWebName, "urlType" => $key[$nextIndex],"fenye"=>1)));
                } else {
                    $currentVal = $webConfigInfo[$webname['name']];
                    $key = array_keys($webConfigInfo);
                    $val = array_values($webConfigInfo);
                    $nextIndex = array_search($currentVal,$val) + 1;
                    if (!empty($key[$nextIndex])) {//下一个网站
                        $nextWebName = $key[$nextIndex];
                        file_put_contents($this->_idFile, json_encode(array("name" => $nextWebName, "urlType" => "name","fenye"=>1)));
                    } else  {
                        file_put_contents($this->_idFile, json_encode(array("name" => "END", "urlType" => "END","fenye"=>1))); //作执行结束标志
                        system("php /home/www/jobs/runJob.php conformDownLinkToMovieInfo.php");//job跑完以后整合下载信息
                    }
                }
            }
        } elseif ($this->_G == 18) {//每天晚上8点开始跑
            file_put_contents($this->_idFile, json_encode(array("name" => "tiantang", "urlType" => "new","fenye"=>1)));
        } else {
            //do nothing
        }
        exit;
    }

    /**
     * 抓取电影天堂下载链接主函数
     * @param $url
     * @param $urlType
     * @return bool
     */
    private function _tiantang($url, $urlType) {
        if (empty($url) || empty($urlType)) {
            return false;
        }
        $moviceInfoHtml = $this->_getCurlInfo($url);
        //下载链接
        $titleInfo = $this->_getPregMatchAll("/\/html\/gndy\/dyzz\/[0-9]+\/[0-9]+\.html/i", $moviceInfoHtml, PREG_PATTERN_ORDER);
        if (!empty($titleInfo[0])) {
            $totalUrlInfo = array_unique($titleInfo[0]);
            $baseUrl = "http://www.dytt8.net";
            foreach($totalUrlInfo as $urlVal) {
                $urlVal = $baseUrl . $urlVal;
                //$urlVal = "http://www.dytt8.net//html/gndy/dyzz/20130525/42243.html";//测试，待删除
                $resultArr = array();
                $idArr = $this->_getPregMatchAll("/[0-9]+/i", $urlVal);
                $resultArr['webType'] = $this->_webConfigInfo['tiantang']['type'];
                //来源网站链接id，为了以后不重复抓取
                $resultArr['webId'] = $idArr[0][2];
                //来源链接
                $resultArr['sourceLink'] = $urlVal;

                $moviceHtml = mb_convert_encoding($this->_getCurlInfo($urlVal),"UTF-8","GBK");

                //名称
                $nameArr = $this->_getPregMatch("/<title>(.*?)《(.*?)》(.*?)<\/title>/si", $moviceHtml);
                if (empty($nameArr[2])) {
                    var_dump("下载名称出错--" . $urlVal . "\n");
                    continue;
                }
                $nameArr = explode("/",trim($nameArr[2]));
                $resultArr['name'] = $nameArr[0];

                //主演
                $zhuyanArr = $this->_getPregMatch("/(◎主(.*?)演(.*?)◎)|(◎演(.*?)员(.*?)◎)|(◎声(.*?)优(.*?)◎)/si", $moviceHtml);
                if (!empty($zhuyanArr[count($zhuyanArr) - 1])) {
                    $zhuyanStr = trim($zhuyanArr[count($zhuyanArr) - 1]);
                    $zhuyanArr = explode("<br />",$zhuyanStr);
                    $zhuyanArr = array_filter($zhuyanArr);
                    $totalZhuYanArr = array();
                    foreach($zhuyanArr as $zyVal) {
                        $zyVal = str_replace("&nbsp;","",trim($zyVal));
                        if (empty($zyVal) || ($zyVal == "&nbsp;")) {
                            continue;
                        }
                        $zyArr = array_filter(explode(" ",$zyVal));
                        foreach($zyArr as $zy) {
                            $zy = preg_replace("/ /","",$zy);
                            $zy = preg_replace("/&nbsp;/","",$zy);
                            $zy = preg_replace("/　/","",$zy);
                            $zy = preg_replace("/\r\n/","",$zy);
                            $zy = str_replace(chr(13),"",$zy);
                            $zy = str_replace(chr(10),"",$zy);
                            $zy = str_replace(chr(9),"",$zy);
                            $zy = preg_replace("/[ ]+/", "", $zy);
                            if (!empty($zy)) {
                                $totalZhuYanArr[] = str_replace("&middot;","·",$zy);
                                break;
                            }
                        }
                    }
                    $resultArr['zhuyan'] = implode("、",$totalZhuYanArr);
                } else {
                    var_dump("下载主演出错--" . $urlVal . "\n");
                    continue;
                }

                //导演
                $daoyanArr = $this->_getPregMatch("/◎导(.*?)演(.*?)◎/i", $moviceHtml);
                if (!empty($daoyanArr[count($daoyanArr) - 1])) {
                    $daoyanStr = htmlspecialchars_decode(trim($daoyanArr[count($daoyanArr) - 1],"　"));
                    $daoyanArr = explode("<br />",$daoyanStr);
                    $daoyanArr = array_filter($daoyanArr);
                    $totalDaoYanArr = array();
                    foreach($daoyanArr as $dyVal) {
                        $dyVal = str_replace("&nbsp;","",trim($dyVal));
                        if (empty($dyVal) || ($dyVal == "&nbsp;")) {
                            continue;
                        }
                        $dyArr = array_filter(explode(" ",$dyVal));
                        foreach($dyArr as $dy) {
                            $dy = preg_replace("/ /","",$dy);
                            $dy = preg_replace("/&nbsp;/","",$dy);
                            $dy = preg_replace("/　/","",$dy);
                            $dy = preg_replace("/\r\n/","",$dy);
                            $dy = str_replace(chr(13),"",$dy);
                            $dy = str_replace(chr(10),"",$dy);
                            $dy = str_replace(chr(9),"",$dy);
                            $dy = preg_replace("/[ ]+/", "", $dy);
                            if (!empty($dy)) {
                                $totalDaoYanArr[] = str_replace("&middot;","·",$dy);
                                break;
                            }
                        }
                    }
                    $resultArr['daoyan'] = implode("、",$totalDaoYanArr);
                } else {
                    $resultArr['daoyan'] = "暂无";
                }

                //下载链接
                $linkArr = $this->_getPregMatch("/【下载地址】(.*?)<\/tbody>/si", $moviceHtml);
                if (!empty($linkArr[1])) {
                    $lArr = $this->_getPregMatch("/href=\"(.*?)\"/si", $linkArr[1]);
                    if (empty($lArr[1])) {
                        var_dump("下载链接出错--" . $urlVal . "\n");
                        continue;
                    }
                    $resultArr['downLink'] = trim($lArr[1]);
                }

                //查询链接是否存在
                $info = $this->_getInfo(array("webId" => $resultArr['webId'],"webType" => $resultArr['webType']),"one","tbl_grabMoviceDownInfo");
                if (!empty($info) && ($info['del'] == 0)) {//存在且没被删除，则更新
                    $upRes = $this->_updateInfo(array("id" => $info['id']),$resultArr,"tbl_grabMoviceDownInfo");
                    var_dump("更新---{$resultArr['webId']}---{$upRes}--{$urlVal}\n");
                } else {//插入
                    $resultArr['createtime'] = time();
                    $lastId = $this->_insertInfo($resultArr,"tbl_grabMoviceDownInfo");
                    if (!empty($lastId)) {
                        var_dump("{$resultArr['webId']}下载链接抓取成功!\n");
                    } else {
                        var_dump("{$resultArr['webId']}下载链接抓取失败--{$urlVal}!\n");
                    }
                }
            }
        }
    }

    /**
     * 抓取飘花网下载链接
     */
    private function _piaohua($url, $urlType) {
        if (empty($url) || empty($urlType)) {
            return false;
        }
        $moviceInfoHtml = $this->_getCurlInfo($url);
        //下载链接
        $titleInfo = $this->_getPregMatchAll("/\/movie\/[0-9]+\.htm/i", $moviceInfoHtml, PREG_PATTERN_ORDER);
        if (!empty($titleInfo[0])) {
            $totalUrlInfo = array_unique($titleInfo[0]);
            $baseUrl = "http://www.piaohua.com";
            foreach($totalUrlInfo as $urlVal) {
                $urlVal = $baseUrl . $urlVal;
                //$urlVal = "http://www.piaohua.com/movie/17339.htm";//测试，待删除
                $resultArr = array();
                $idArr = $this->_getPregMatchAll("/[0-9]+/i", $urlVal);
                $resultArr['webType'] = $this->_webConfigInfo['piaohua']['type'];
                //来源网站链接id，为了以后不重复抓取
                $resultArr['webId'] = $idArr[0][0];
                //来源链接
                $resultArr['sourceLink'] = $urlVal;

                $moviceHtml = $this->_getCurlInfo($urlVal);

                //名称
                $nameArr = $this->_getPregMatch("/<title>(.*?)<\/title>/si", $moviceHtml);
                if (empty($nameArr[0])) {
                    var_dump("下载名称出错--" . $urlVal . "\n");
                    continue;
                }
                $nameArr = explode("_",trim($nameArr[0]));
                if (empty($nameArr[1])) {
                    var_dump("下载名称出错--" . $urlVal . "\n");
                    continue;
                }
                $nameArr[1] = str_replace("在线观看","",$nameArr[1]);
                $nameArr = explode("/",trim($nameArr[1]));
                $resultArr['name'] = $nameArr[0];

                //主演
                $zhuyanArr = $this->_getPregMatch("/演员：(.*?)<\/div>/si", $moviceHtml);
                if (!empty($zhuyanArr[count($zhuyanArr) - 1])) {
                    $zhuyanArr = explode("</a>",$zhuyanArr[count($zhuyanArr) - 1]);
                    $zhuyanArr = array_filter($zhuyanArr);
                    $totalZhuYanArr = array();
                    foreach($zhuyanArr as $zyVal) {
                        $zyVal = str_replace("&nbsp;","",trim($zyVal));
                        if (empty($zyVal) || ($zyVal == "&nbsp;")) {
                            continue;
                        }
                        $zyVal = str_replace("&middot;","·",$zyVal);
                        $zyVal = strip_tags($zyVal);
                        if (empty($zyVal)) {
                            continue;
                        }
                        $totalZhuYanArr[] = strip_tags($zyVal);
                    }
                    $resultArr['zhuyan'] = implode("、",$totalZhuYanArr);
                } else {
                    var_dump("下载主演出错--" . $urlVal . "\n");
                    continue;
                }

                //导演
                $daoyanArr = $this->_getPregMatch("/导演：(.*?)<\/li>/si", $moviceHtml);
                if (!empty($zhuyanArr[count($daoyanArr) - 1])) {
                    $daoyanArr = explode("</a>",$daoyanArr[count($daoyanArr) - 1]);
                    $daoyanArr = array_filter($daoyanArr);
                    $totalDaoYanArr = array();
                    foreach($daoyanArr as $dyVal) {
                        $dyVal = str_replace("&nbsp;","",trim($dyVal));
                        if (empty($dyVal) || ($dyVal == "&nbsp;")) {
                            continue;
                        }
                        $dyVal = str_replace("&middot;","·",$dyVal);
                        $dyVal = strip_tags($dyVal);
                        if (empty($dyVal)) {
                            continue;
                        }
                        $totalDaoYanArr[] = strip_tags($dyVal);
                    }
                    $resultArr['daoyan'] = implode("、",$totalDaoYanArr);
                } else {
                    $resultArr['daoyan'] = "暂无";
                }

                //下载链接
                $linkArr = $this->_getPregMatch("/xzurl=(.*?)\'/si", $moviceHtml);
                if (!empty($linkArr[1])) {
                    $resultArr['downLink'] = trim($linkArr[1]);
                } else {
                    var_dump("下载链接出错--" . $urlVal . "\n");
                    continue;
                }

                //查询链接是否存在
                $info = $this->_getInfo(array("webId" => $resultArr['webId'],"webType" => $resultArr['webType']),"one","tbl_grabMoviceDownInfo");
                if (!empty($info) && ($info['del'] == 0)) {//存在且没被删除，则更新
                    $upRes = $this->_updateInfo(array("id" => $info['id']),$resultArr,"tbl_grabMoviceDownInfo");
                    var_dump("更新---{$resultArr['webId']}---{$upRes}--{$urlVal}\n");
                } else {//插入
                    $resultArr['createtime'] = time();
                    $lastId = $this->_insertInfo($resultArr,"tbl_grabMoviceDownInfo");
                    if (!empty($lastId)) {
                        var_dump("{$resultArr['webId']}下载链接抓取成功!\n");
                    } else {
                        var_dump("{$resultArr['webId']}下载链接抓取失败--{$urlVal}!\n");
                    }
                }
            }
        }
    }

    /**
     * 抓取飘花网2下载链接
     */
    private function _piaohua2($url, $urlType) {
        if (empty($url) || empty($urlType)) {
            return false;
        }
        $moviceInfoHtml = $this->_getCurlInfo($url);
        //下载链接
        $titleInfo = $this->_getPregMatchAll("/\/html\/{$urlType}\/[0-9]+\/[0-9]+\/[0-9]+\.html/i", $moviceInfoHtml, PREG_PATTERN_ORDER);
        if (!empty($titleInfo[0])) {
            $totalUrlInfo = array_unique($titleInfo[0]);
            $baseUrl = "http://www.piaohua.com";
            foreach($totalUrlInfo as $urlVal) {
                $urlVal = $baseUrl . $urlVal;
                //$urlVal = "http://www.piaohua.com/html/zainan/2008/1013/13673.html";//测试，待删除
                $resultArr = array();
                $idArr = $this->_getPregMatchAll("/[0-9]+/i", $urlVal);
                $resultArr['webType'] = $this->_webConfigInfo['piaohua2']['type'];
                //来源网站链接id，为了以后不重复抓取
                $resultArr['webId'] = $idArr[0][2];
                //来源链接
                $resultArr['sourceLink'] = $urlVal;

                $moviceHtml = $this->_getCurlInfo($urlVal);

                //名称
                $nameArr = $this->_getPregMatch("/<title>(.*?)<\/title>/si", $moviceHtml);
                if (empty($nameArr[1])) {
                    var_dump("下载名称出错--" . $urlVal . "\n");
                    continue;
                }
                $nameArr = explode("_",trim($nameArr[1]));
                if (empty($nameArr[0])) {
                    var_dump("下载名称出错--" . $urlVal . "\n");
                    continue;
                }
                $nameArr[0] = str_replace("下载","",$nameArr[0]);
                $nameArr = explode("/",trim($nameArr[0]));
                $nameArr[0] = trim($nameArr[0],"》");
                $nameArr = explode("《",$nameArr[0]);
                $nameArr = explode("BD",$nameArr[0]);
                $nameArr = explode("CD",$nameArr[0]);
                $resultArr['name'] = $nameArr[0];

                //主演
                $zhuyanArr = $this->_getPregMatch("/主(.*?)演：(.*?)：/si", $moviceHtml);
                if (empty($zhuyanArr[count($zhuyanArr) - 1])  || mb_strlen($zhuyanArr[count($zhuyanArr) - 1],"UTF-8") > 150) {var_dump(0);
                    $zhuyanArr = $this->_getPregMatch("/主(.*?)演(.*?)◎/si", $moviceHtml);
                }

                if (empty($zhuyanArr[count($zhuyanArr) - 1]) || (strpos($zhuyanArr[count($zhuyanArr) - 1],"，") !== false && strpos($zhuyanArr[count($zhuyanArr) - 1],"。") !== false)) {var_dump(2);
                    $zhuyanArr = $this->_getPregMatch("/演(.*?)员(.*?)<\/p>/si", $moviceHtml);
                }

                if (empty($zhuyanArr[count($zhuyanArr) - 1]) || (strpos($zhuyanArr[count($zhuyanArr) - 1],"，") !== false && strpos($zhuyanArr[count($zhuyanArr) - 1],"。") !== false)) {var_dump(1);
                    $zhuyanArr = $this->_getPregMatch("/主(.*?)演(.*?)【/si", $moviceHtml);
                }

                if (empty($zhuyanArr[count($zhuyanArr) - 1]) || (strpos($zhuyanArr[count($zhuyanArr) - 1],"，") !== false && strpos($zhuyanArr[count($zhuyanArr) - 1],"。") !== false)) {var_dump(3);
                    $zhuyanArr = $this->_getPregMatch("/主(.*?)演(.*?)<\/p>/si", $moviceHtml);
                }

                if (empty($zhuyanArr[count($zhuyanArr) - 1]) || (strpos($zhuyanArr[count($zhuyanArr) - 1],"，") !== false && strpos($zhuyanArr[count($zhuyanArr) - 1],"。") !== false)) {var_dump(4);
                    $zhuyanArr = $this->_getPregMatch("/演员 (.*?)(.*?)制作人/si", $moviceHtml);
                }

                if (empty($zhuyanArr[count($zhuyanArr) - 1]) || (strpos($zhuyanArr[count($zhuyanArr) - 1],"，") !== false && strpos($zhuyanArr[count($zhuyanArr) - 1],"。") !== false)) {var_dump(5);
                    $zhuyanArr = $this->_getPregMatch("/演(.*?)员(.*?)◎/si", $moviceHtml);
                }

                if (empty($zhuyanArr[count($zhuyanArr) - 1]) || (strpos($zhuyanArr[count($zhuyanArr) - 1],"，") !== false && strpos($zhuyanArr[count($zhuyanArr) - 1],"。") !== false)) {var_dump(6);
                    $zhuyanArr = $this->_getPregMatch("/演(.*?)员(.*?)影片介绍/si", $moviceHtml);
                }

                if (empty($zhuyanArr[count($zhuyanArr) - 1]) || (strpos($zhuyanArr[count($zhuyanArr) - 1],"，") !== false && strpos($zhuyanArr[count($zhuyanArr) - 1],"。") !== false)) {var_dump(7);
                    $zhuyanArr = $this->_getPregMatch("/演(.*?)员(.*?)【/si", $moviceHtml);
                }

                if (!empty($zhuyanArr[count($zhuyanArr) - 1])) {
                    $zhuyanStr = trim($zhuyanArr[count($zhuyanArr) - 1],"】");
                    $zhuyanStr = trim($zhuyanStr,":");
                    $zhuyanStr = trim($zhuyanStr,"：");
                    $zhuyanStr = trim($zhuyanStr,"】");
                    $zhuyanStr = explode("</p>",$zhuyanStr);
                    $zhuyanStr = $zhuyanStr[0];
                    $zhuyanStr = explode("<br />【",$zhuyanStr);
                    $zhuyanStr = $zhuyanStr[0];
                    $zhuyanStr = str_replace("</font>","",$zhuyanStr);
                    $zhuyanStr = str_replace("</span>","",$zhuyanStr);
                    $zhuyanStr = str_replace("<BR>","<br />",$zhuyanStr);
                    $zhuyanStr = explode("<br />",$zhuyanStr);
                    if (strpos($zhuyanStr[0],"/") !== false) {
                        $zhuyanStr = $zhuyanStr[0];
                        $zhuyanArr = explode("/",$zhuyanStr);
                    } else {
                        $zhuyanArr = $zhuyanStr;
                        $zhuyanArr = array_filter($zhuyanArr);
                    }
                    $totalZhuYanArr = array();
                    foreach($zhuyanArr as $zyVal) {
                        $zyVal = str_replace("&nbsp;","",trim($zyVal));
                        if (empty($zyVal) || ($zyVal == "&nbsp;")) {
                            continue;
                        }
                        $zyArr = array_filter(explode(" ",$zyVal));
                        foreach($zyArr as $zy) {
                            $zy = preg_replace("/ /","",$zy);
                            $zy = preg_replace("/&nbsp;/","",$zy);
                            $zy = preg_replace("/　/","",$zy);
                            $zy = preg_replace("/\r\n/","",$zy);
                            $zy = str_replace(chr(13),"",$zy);
                            $zy = str_replace(chr(10),"",$zy);
                            $zy = str_replace(chr(9),"",$zy);
                            $zy = preg_replace("/[ ]+/", "", $zy);
                            if (!empty($zy)) {
                                $zy = strip_tags($zy);
                                $zy = str_replace("&bull;","·",$zy);
                                $zy = str_replace("&middot;","·",$zy);
                                $totalZhuYanArr[] = $zy;
                                break;
                            }
                        }
                    }
                    $resultArr['zhuyan'] = implode("、",$totalZhuYanArr);
                } else {
                    $zhuyanArr = $this->_getPregMatch("/(主(.*?)演:(.*?)<br \/>)|(主(.*?)演：(.*?)<br \/>)|(演(.*?)员:(.*?)<br \/>)|(演(.*?)员：(.*?)<br \/>)/si", $moviceHtml);
                    if (!empty($zhuyanArr[count($zhuyanArr) - 1])) {
                        $zhuyanArr[1] = str_replace(" ","",$zhuyanArr[count($zhuyanArr) - 1]);
                        $zhuyanArr[1] = str_replace(" / ","、",$zhuyanArr[1]);
                        $zhuyanArr[1] = str_replace("&middot;","·",$zhuyanArr[1]);
                        $zhuyanArr[1] = strip_tags($zhuyanArr[1]);
                        if (empty($zhuyanArr[1])) {
                            continue;
                        }
                        $resultArr['zhuyan'] = str_replace("/","、",$zhuyanArr[1]);
                    } else {
                        if (!in_array($resultArr['webId'],array(26692,26757,24601,24405,24266))) {
                            var_dump("下载主演出错--" . $urlVal . "\n");
                        }
                        continue;
                    }
                }

                //导演
                $daoyanArr = $this->_getPregMatch("/(◎导(.*?)演(.*?)◎)|(【导(.*?)演】(.*?)【)/si", $moviceHtml);
                if (!empty($daoyanArr[count($daoyanArr) - 1])) {
                    $daoyanStr = htmlspecialchars_decode(trim($daoyanArr[count($daoyanArr) - 1],"　"));
                    $daoyanArr = explode("<br />",$daoyanStr);
                    $daoyanArr = array_filter($daoyanArr);
                    $totalDaoYanArr = array();
                    foreach($daoyanArr as $dyVal) {
                        $dyVal = str_replace("&nbsp;","",trim($dyVal));
                        if (empty($dyVal) || ($dyVal == "&nbsp;")) {
                            continue;
                        }
                        $dyArr = array_filter(explode(" ",$dyVal));
                        foreach($dyArr as $dy) {
                            $dy = preg_replace("/ /","",$dy);
                            $dy = preg_replace("/&nbsp;/","",$dy);
                            $dy = preg_replace("/　/","",$dy);
                            $dy = preg_replace("/\r\n/","",$dy);
                            $dy = str_replace(chr(13),"",$dy);
                            $dy = str_replace(chr(10),"",$dy);
                            $dy = str_replace(chr(9),"",$dy);
                            $dy = preg_replace("/[ ]+/", "", $dy);
                            if (!empty($dy)) {
                                $dy = strip_tags($dy);
                                $totalDaoYanArr[] = str_replace("&middot;","·",$dy);
                                break;
                            }
                        }
                    }
                    $resultArr['daoyan'] = implode("、",$totalDaoYanArr);
                } else {
                    $resultArr['daoyan'] = "暂无";
                }

                //下载链接
                $linkArr = $this->_getPregMatch("/ftp:\/\/(.*?)\"/si", $moviceHtml);
                if (!empty($linkArr[0])) {
                    $resultArr['downLink'] = trim($linkArr[0],'"');
                } else {
                    var_dump("下载链接出错--" . $urlVal . "\n");
                    continue;
                }

                //查询链接是否存在
                $info = $this->_getInfo(array("webId" => $resultArr['webId'],"webType" => $resultArr['webType']),"one","tbl_grabMoviceDownInfo");
                if (!empty($info) && ($info['del'] == 0)) {//存在且没被删除，则更新
                    $upRes = $this->_updateInfo(array("id" => $info['id']),$resultArr,"tbl_grabMoviceDownInfo");
                    var_dump("更新---{$resultArr['webId']}---{$upRes}--{$urlVal}\n");
                } else {//插入
                    $resultArr['createtime'] = time();
                    $lastId = $this->_insertInfo($resultArr,"tbl_grabMoviceDownInfo");
                    if (!empty($lastId)) {
                        var_dump("{$resultArr['webId']}下载链接抓取成功!\n");
                    } else {
                        var_dump("{$resultArr['webId']}下载链接抓取失败--{$urlVal}!\n");
                    }
                }
            }
        }
    }

    /**
     * 抓取迅雷仓库下载链接
     * @param $url
     * @param $urlType
     * @return bool
     */
    private function _xunleicang($url,$urlType) {
        if (empty($url) || empty($urlType)) {
            return false;
        }
        $moviceInfoHtml = $this->_getCurlInfo($url);
        //下载链接
        $titleInfo = $this->_getPregMatchAll("/\/vod-read-id-[0-9]+\.html/i", $moviceInfoHtml, PREG_PATTERN_ORDER);
        if (!empty($titleInfo[0])) {
            $totalUrlInfo = array_unique($titleInfo[0]);
            $baseUrl = "http://www.xunleicang.com";
            foreach($totalUrlInfo as $urlVal) {
                $urlVal = $baseUrl . $urlVal;
                //$urlVal = "http://www.piaohua.com/html/dongzuo/2013/0606/26994.html";//测试，待删除
                $resultArr = array();
                $idArr = $this->_getPregMatchAll("/[0-9]+/i", $urlVal);
                $resultArr['webType'] = $this->_webConfigInfo['xunleicang']['type'];
                //来源网站链接id，为了以后不重复抓取
                $resultArr['webId'] = $idArr[0][0];
                //来源链接
                $resultArr['sourceLink'] = $urlVal;

                $moviceHtml = $this->_getCurlInfo($urlVal);

                //名称
                $nameArr = $this->_getPregMatch("/<title>(.*?)<\/title>/si", $moviceHtml);
                if (empty($nameArr[1])) {
                    var_dump("下载名称出错--" . $urlVal . "\n");
                    continue;
                }
                $nameArr = explode("_",trim($nameArr[1]));
                if (empty($nameArr[0])) {
                    var_dump("下载名称出错--" . $urlVal . "\n");
                    continue;
                }
                $nameArr = explode("/",trim($nameArr[0]));
                $resultArr['name'] = $nameArr[0];

                //主演
                $zhuyanArr = $this->_getPregMatch("/主演：<\/STRONG>(.*?)<\/SPAN>/si", $moviceHtml);
                if (!empty($zhuyanArr[1])) {
                    $zyArr = explode("</a>",$zhuyanArr[1]);
                    $totalZhuYanArr = array();
                    foreach($zyArr as $zy) {
                        $zy = trim(strip_tags($zy));
                        if (empty($zy)) {
                            continue;
                        }
                        $totalZhuYanArr[] = $zy;
                    }
                    $resultArr['zhuyan'] = implode("、",$totalZhuYanArr);
                } else {
                    var_dump("下载主演出错--" . $urlVal . "\n");
                    continue;
                }

                //导演
                $resultArr['daoyan'] = "暂无";

                //下载链接
                $linkArr = $this->_getPregMatch("/var GvodUrls=\"(.*?)\"/si", $moviceHtml);
                if (!empty($linkArr[1])) {
                    $lArr = explode("#",$linkArr[1]);
                    $resultArr['downLink'] = "thunder://" . base64_encode("AA{$lArr[0]}ZZ");
                } else {
                    var_dump("下载链接出错--" . $urlVal . "\n");
                    continue;
                }

                //查询链接是否存在
                $info = $this->_getInfo(array("webId" => $resultArr['webId'],"webType" => $resultArr['webType']),"one","tbl_grabMoviceDownInfo");
                if (!empty($info) && ($info['del'] == 0)) {//存在且没被删除，则更新
                    $upRes = $this->_updateInfo(array("id" => $info['id']),$resultArr,"tbl_grabMoviceDownInfo");
                    var_dump("更新---{$resultArr['webId']}---{$upRes}--{$urlVal}\n");
                } else {//插入
                    $resultArr['createtime'] = time();
                    $lastId = $this->_insertInfo($resultArr,"tbl_grabMoviceDownInfo");
                    if (!empty($lastId)) {
                        var_dump("{$resultArr['webId']}下载链接抓取成功!\n");
                    } else {
                        var_dump("{$resultArr['webId']}下载链接抓取失败--{$urlVal}!\n");
                    }
                }
            }
        }
    }

    /**
     * 抓取迅播下载链接
     * @param $url
     * @param $urlType
     * @return bool
     */
    private function _2tu($url,$urlType) {
        if (empty($url) || empty($urlType)) {
            return false;
        }
        $moviceInfoHtml = $this->_getCurlInfo($url);
        //下载链接
        $titleInfo = $this->_getPregMatchAll("/\/Html\/GP[0-9]+\.html/i", $moviceInfoHtml, PREG_PATTERN_ORDER);
        if (!empty($titleInfo[0])) {
            $totalUrlInfo = array_unique($titleInfo[0]);
            $baseUrl = "http://www.2tu.cc";
            foreach($totalUrlInfo as $urlVal) {
                $urlVal = $baseUrl . $urlVal;
                //$urlVal = "http://www.piaohua.com/html/dongzuo/2013/0606/26994.html";//测试，待删除
                $resultArr = array();
                $idArr = $this->_getPregMatchAll("/[0-9]+/i", $urlVal);
                $resultArr['webType'] = $this->_webConfigInfo['2tu']['type'];
                //来源网站链接id，为了以后不重复抓取
                $resultArr['webId'] = $idArr[0][1];
                //来源链接
                $resultArr['sourceLink'] = $urlVal;

                $moviceHtml = mb_convert_encoding($this->_getCurlInfo($urlVal),"UTF-8","GBK");

                //名称
                $nameArr = $this->_getPregMatch("/<title>(.*?)<\/title>/si", $moviceHtml);
                if (empty($nameArr[1])) {
                    var_dump("下载名称出错--" . $urlVal . "\n");
                    continue;
                }
                $nameArr = explode("_",trim($nameArr[1]));
                if (empty($nameArr[0])) {
                    var_dump("下载名称出错--" . $urlVal . "\n");
                    continue;
                }
                $nameArr = explode("/",trim($nameArr[0]));
                $resultArr['name'] = $nameArr[0];

                //电影主演以及年份等信息
                $moviceZhuyanInfo = $this->_getPregMatch("/<div class=\"text c0071bc\">(.*?)<\/div>/si",$moviceHtml);

                if (!empty($moviceZhuyanInfo[1])) {
                    $moviceZhuyanInfo = strip_tags(trim($moviceZhuyanInfo[1]));
                    $moviceZhuyanInfo  = array_filter(explode("&nbsp;",$moviceZhuyanInfo));
                    $moviceZhuyanInfo = explode("\t\t",implode("\t\t",$moviceZhuyanInfo));
                    $i = 1;
                    $count = count($moviceZhuyanInfo);
                    $zhuYanArr = array();
                    foreach($moviceZhuyanInfo as $infoVal) {
                        if ($i != $count) {
                            $zhuYanArr[] = str_replace("主演：","",$infoVal);
                        }
                        $i++;
                    }
                    $resultArr['zhuyan'] = implode("、",$zhuYanArr);
                } else {
                    var_dump("下载主演出错--" . $urlVal . "\n");
                    continue;
                }

                //导演
                $resultArr['daoyan'] = "暂无";

                //下载链接
                $linkArr = $this->_getPregMatch("/var GvodUrls = \"(.*?)\"/si", $moviceHtml);
                if (!empty($linkArr[1])) {
                    $lArr = explode("#",$linkArr[1]);
                    $resultArr['downLink'] = "thunder://" . base64_encode("AA{$lArr[0]}ZZ");
                } else {
                    var_dump("下载链接出错--" . $urlVal . "\n");
                    continue;
                }

                //查询链接是否存在
                $info = $this->_getInfo(array("webId" => $resultArr['webId'],"webType" => $resultArr['webType']),"one","tbl_grabMoviceDownInfo");
                if (!empty($info) && ($info['del'] == 0)) {//存在且没被删除，则更新
                    $upRes = $this->_updateInfo(array("id" => $info['id']),$resultArr,"tbl_grabMoviceDownInfo");
                    var_dump("更新---{$resultArr['webId']}---{$upRes}--{$urlVal}\n");
                } else {//插入
                    $resultArr['createtime'] = time();
                    $lastId = $this->_insertInfo($resultArr,"tbl_grabMoviceDownInfo");
                    if (!empty($lastId)) {
                        var_dump("{$resultArr['webId']}下载链接抓取成功!\n");
                    } else {
                        var_dump("{$resultArr['webId']}下载链接抓取失败--{$urlVal}!\n");
                    }
                }
            }
        }
    }

    /**
     * 抓取比特鱼载链接
     * @param $url
     * @param $urlType
     * @return bool
     */
    private function _bitfish8($url,$urlType) {
        if (empty($url) || empty($urlType)) {
            return false;
        }
        $moviceInfoHtml = $this->_getCurlInfo($url);
        //下载链接
        $titleInfo = $this->_getPregMatchAll("/\/btmovieseed\/{$urlType}\/[0-9]+\.html/i", $moviceInfoHtml, PREG_PATTERN_ORDER);
        if (!empty($titleInfo[0])) {
            $totalUrlInfo = array_unique($titleInfo[0]);
            $baseUrl = "http://www.bitfish8.com";
            foreach($totalUrlInfo as $urlVal) {
                $urlVal = $baseUrl . $urlVal;
                //$urlVal = "http://www.bitfish8.com/btmovieseed/dongzuo/201212152863.html";//测试，待删除
                $resultArr = array();
                $idArr = $this->_getPregMatchAll("/[0-9]+/i", $urlVal);
                $resultArr['webType'] = $this->_webConfigInfo['bitfish8']['type'];
                //来源网站链接id，为了以后不重复抓取
                $resultArr['webId'] = substr($idArr[0][1],8);
                //来源链接
                $resultArr['sourceLink'] = $urlVal;

                $moviceHtml = mb_convert_encoding($this->_getCurlInfo($urlVal),"UTF-8","GBK");

                //名称
                $nameArr = $this->_getPregMatch("/<title>(.*?)<\/title>/si", $moviceHtml);
                if (empty($nameArr[1])) {
                    var_dump("下载名称出错--" . $urlVal . "\n");
                    continue;
                }
                $nameArr = explode("_",trim($nameArr[1]));
                if (empty($nameArr[0])) {
                    var_dump("下载名称出错--" . $urlVal . "\n");
                    continue;
                }
                $nameArr = explode("/",trim($nameArr[0]));
                $resultArr['name'] = $nameArr[0];

                //电影主演以及年份等信息
                $moviceZhuyanInfo = $this->_getPregMatchAll("/content=\"(.*?)\"/si",$moviceHtml,PREG_PATTERN_ORDER);
                if (!empty($moviceZhuyanInfo[1][2])) {
                    $resultArr['zhuyan'] = $moviceZhuyanInfo[1][2];
                } else {
                    var_dump("下载主演出错--" . $urlVal . "\n");
                    continue;
                }

                //导演
                $resultArr['daoyan'] = "暂无";

                //下载链接
                $linkArr = $this->_getPregMatch("/href=\"\/plus\/download\.php\?open=[0-9]+&id=[0-9]+&uhash=[0-9a-zA-Z]+\" target=\"_blank\"> BT下载地址(.*?)<\/a>/si", $moviceHtml);
                if (!empty($linkArr[0])) {
                    $lArr = $this->_getPregMatch("/href=\"(.*?)\"/si", $linkArr[0]);
                    $resultArr['downLink'] = $baseUrl . $lArr[1];
                } else {
                    var_dump("下载链接出错--" . $urlVal . "\n");
                    continue;
                }

                //查询链接是否存在
                $info = $this->_getInfo(array("webId" => $resultArr['webId'],"webType" => $resultArr['webType']),"one","tbl_grabMoviceDownInfo");
                if (!empty($info) && ($info['del'] == 0)) {//存在且没被删除，则更新
                    $upRes = $this->_updateInfo(array("id" => $info['id']),$resultArr,"tbl_grabMoviceDownInfo");
                    var_dump("更新---{$resultArr['webId']}---{$upRes}--{$urlVal}\n");
                } else {//插入
                    $resultArr['createtime'] = time();
                    $lastId = $this->_insertInfo($resultArr,"tbl_grabMoviceDownInfo");
                    if (!empty($lastId)) {
                        var_dump("{$resultArr['webId']}下载链接抓取成功!\n");
                    } else {
                        var_dump("{$resultArr['webId']}下载链接抓取失败--{$urlVal}!\n");
                    }
                }
            }
        }
    }
}
$doo = new getdownlink();
$doo->run();
