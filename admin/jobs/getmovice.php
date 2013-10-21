<?php
/**
 * 电影信息抓去
 * added by xiongjiewu at 2013-4-22
 */
include("jobBase.php");
class Getmovice extends jobBase
{
    private $_idFile;
    private $_webConfigInfo;

    private static $_base_hex = 5;
    private static $_charlist = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    private $_filePath;

    private $_waterString;//水印文字
    private $_waterImg;//水印图片
    private $_G;

    public function __construct()
    {
        parent::__construct();
        $this->_idFile = $this->get_config_value("zhuaqu_movice_id_file_path","watchlink"); //当前配置文件，读取控制抓取网站
        $this->_webConfigInfo = $this->get_config_value("zhuaqu_web_info","watchlink");
        $this->_filePath = $this->get_config_value("zhuaqu_movie_error_log","watchlink");
        $this->_waterString = $this->get_config_value("dy_water_text");
        $this->_waterImg = IMGPATH . $this->get_config_value("dy_water_img");
        $this->_G = date("G");
    }

    public function run()
    {
        //手工输入，抓取$argv[2]=网站，$argv[3] = 抓取url，$argv[4] = 类型（new或者hot等等）
        global $argv;
        if (!empty($argv[2]) && !empty($argv[3]) && !empty($argv[4])) {
            $functionName = "_" . $argv[2];
            $this->$functionName($argv[3],$argv[4]);
            var_dump("---do----");
            exit;
        }

        //读取抓取网站和抓取链接信息
        $webname = json_decode(trim(file_get_contents($this->_idFile)), true);

        //判断信息是否有效
        if (!empty($webname['name']) && (strtoupper($webname['name']) != "END")) {
            $webConfigInfo = $this->_webConfigInfo;
            if (!empty($webConfigInfo[$webname['name']])) {
                if (($webConfigInfo[$webname['name']]['open'] == "yes") && !empty($webname['urlType']) && is_array($webConfigInfo[$webname['name']][$webname['urlType']])) {
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
                        system("php /home/www/jobs/runJob.php fixActorAnddirectorInfo.php");//job跑完以后修正导演和演员信息
                        system("php /home/www/jobs/runJob.php recombinationMovieName.php");//删除电影别名
                        system("php /home/www/jobs/runJob.php conformMovieInfo.php");//job跑完以后电影整合信息
                        system("php /home/www/jobs/runJob.php initializationModule.php");//job跑完以后更新首页各个模块信息
			            $dayDate = date("Ymd");
			            system("rm /home/webapp/www/dianying8/application/cache/home_total_dy_info_" . $dayDate);//删除缓存
                    }
                }
            }
        } elseif ($this->_G == 18) {//每天晚上8点开始跑
            file_put_contents($this->_idFile, json_encode(array("name" => "douban", "urlType" => "comming","fenye"=>1)));
        } else {
            //do nothing
        }
        exit;
    }

    /** 给图片添加水印
     * @param $img
     * @return bool
     */
    private function _addWaterToImg($img) {
        if (true || empty($img) || strpos($img,"dy_common") !== false) {//暂时关闭，不加水印
            return false;
        }
        $imgPath = rtrim(IMGPATH, "/") . $img;
        return $this->addWaterDo(1,$imgPath);
    }

    /** 匹配抓取
     * @param $match 正则
     * @param $subject 目标字符串
     * @param $pather 模式
     * @return mixed
     */
    protected function _getPregMatchAll($match, $subject, $pather = null)
    {
        preg_match_all($match, $subject, $resInfo, $pather);
        return $resInfo;
    }

    /** 匹配抓取
     * @param $match 正则
     * @param $subject 目标字符串
     * @return mixed
     */
    protected function _getPregMatch($match, $subject)
    {
        preg_match($match, $subject, $resInfo);
        return $resInfo;
    }

    /** 正则替换
     * @param $match 正则
     * @param $replace 替换成的字符
     * @param $subject 目标字符
     * @return mixed
     */
    private function _pregReplace($match, $replace, $subject)
    {
        return preg_replace($match, $replace, $subject);
    }

    /**
     * 时光网抓取主函数
     */
    private function _mtime($url, $urlType)
    {
        if (empty($url) || empty($urlType)) {
            return false;
        }
        $moviceInfoHtml = $this->_getCurlInfo($url);
        if (!empty($moviceInfoHtml)) {
            $titleInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceInfoHtml, PREG_PATTERN_ORDER);
            $pingfengTotalInfo = $piaofangInfo = array();
            if ($urlType == "top") {//如果是经典电影，则获取评分
                $pingfenInfo = $this->_getPregMatchAll("/<span class=total>(.*?)<\/b>/i", $moviceInfoHtml, PREG_PATTERN_ORDER);
                foreach($pingfenInfo[0] as $pingfenVal) {
                    $pingfengTotalInfo[] = strip_tags($pingfenVal);
                }
            }

            $piaofangType = $piaofangDiqu = 0;//票房类型+票房地区
            if ($urlType == "benzhoupiaofang") {
                $piaofangType = 1;
                $piaofangInfo = $this->_getPregMatchAll("/<span class=\"weekly\">(.*?)<\/span>/si", $moviceInfoHtml, PREG_PATTERN_ORDER);
                if (strpos($url,"#US") !== false) {
                    $piaofangDiqu = 2;
                    $titleInfo = array_slice($titleInfo[0],0,10);
                    $piaofangInfo = array_slice($piaofangInfo[1],1,10);
                } elseif (strpos($url,"#CN") !== false) {
                    $piaofangDiqu = 1;
                    $titleInfo = array_slice($titleInfo[0],10,10);
                    $piaofangInfo = array_slice($piaofangInfo[1],12,10);
                } elseif (strpos($url,"#JP") !== false) {
                    $piaofangDiqu = 4;
                    $titleInfo = array_slice($titleInfo[0],40,10);
                    $piaofangInfo = array_slice($piaofangInfo[1],45,10);
                } elseif (strpos($url,"#KR") !== false) {
                    $piaofangDiqu = 3;
                    $titleInfo = array_slice($titleInfo[0],50,10);
                    $piaofangInfo = array_slice($piaofangInfo[1],56,10);
                }
                $piaofangInfo = array_values($piaofangInfo);
            } else {
                $titleInfo = array_unique($titleInfo[0]);
            }

            $titleInfo = array_values($titleInfo);
            if (!empty($titleInfo)) {
                foreach ($titleInfo as $infoKey => $infoVal) {
                    $infoVal = "http://" . $infoVal;
                    $resultArr = array();
                    $moviceHtml = $this->_getCurlInfo($infoVal);
                    $titleArr = $this->_getPregMatch("/<title>(.*?)<\/title>/si", $moviceHtml);
                    $titleRealArr = explode(" ", $titleArr[1]);
                    $resultArr['name'] = trim($titleRealArr[0]);
                    $firstLetter = $this->getFirstLetter($resultArr['name']);
                    if ($firstLetter != "*") {
                        $resultArr['firstLetter'] = $firstLetter;
                    }
                    //拼音
                    $pinyin = $this->getPinyin($resultArr['name'],2);
                    if (!empty($pinyin)) {
                        $resultArr['pinyin'] = $pinyin;
                    }

                    $idArr = $this->_getPregMatchAll("/[0-9]+/", $infoVal);
                    $moviceId = $idArr[0][0]; //电影id
                    $resultArr['webType'] = $this->_webConfigInfo['mtime']['type'];
                    $resultArr['webId'] = $moviceId;

                    //电影介绍
                    $jeishaoInfo = $this->_getPregMatch("/<span property=\"v:summary\">(.*?)<\/span>/i", $moviceHtml);
                    if (!empty($jeishaoInfo[1])) {
                        $resultArr['jieshao'] = str_replace("　　","",trim($jeishaoInfo[1]));
                    } else {
                        $resultArr['jieshao'] = "暂无";
                    }

                    //图片
                    $imageUrl = "movie.mtime.com/{$moviceId}/posters_and_images/";
                    $imageHtml = $this->_getCurlInfo("http://" . $imageUrl);
                    $imgArr = $this->_getPregMatch("/<a href=\"http\:\/\/movie\.mtime\.com\/{$moviceId}\/posters_and_images(.*?)\" src=\"(.*?)\"\/><\/a>/i", $imageHtml);
                    if (!empty($imgArr)) {
                        $imgUrl = $imgArr[2];
                        $imgArr = explode(".", $imgUrl);
                        $imagesName = md5($resultArr['name'] . "_" . $resultArr['webType'] . "_" . $resultArr['webId']) . "." . $imgArr[count($imgArr) - 1];
                        $resultArr['image'] = $this->_downLoadImg($imagesName, $imgUrl); //下载图片并保存,并返回图片地址
                    } else { //默认图片地址 todo
                        $resultArr['image'] = $this->get_config_value("dy_common_img");
                    }

                    //导演
                    $daoyanInfo = $this->_getPregMatch("/导演：<\/strong>(.*?)<\/li>/", $moviceHtml);
                    if (!empty($daoyanInfo[1])) {
                        $daoyanArr = explode("</a>",$daoyanInfo[1]);
                        $totalDaoYan = array();
                        foreach($daoyanArr as $daoyan) {
                            $daoyan = trim(strip_tags($daoyan));
                            if (empty($daoyan) || strpos($daoyan,"更多") !== false) {
                                continue;
                            }
                            $totalDaoYan[] = $daoyan;
                        }
                        $resultArr['daoyan'] = implode("、",$totalDaoYan);
                    } else {
                        $resultArr['daoyan'] = "暂无";
                    }

                    //主演
                    $zhuyanInfo = $this->_getPregMatch("/主演：<\/strong>(.*?)<\/li>/", $moviceHtml);
                    if (!empty($zhuyanInfo[1])) {
                        $zhuyanArr = explode("</a>",$zhuyanInfo[1]);
                        $totalZhuYan = array();
                        foreach($zhuyanArr as $zhuyan) {
                            $zhuyan = trim(strip_tags($zhuyan));
                            if (empty($zhuyan) || strpos($zhuyan,"更多") !== false) {
                                continue;
                            }
                            $totalZhuYan[] = $zhuyan;
                        }
                        $resultArr['zhuyan'] = implode("、",$totalZhuYan);
                    } else {
                        $resultArr['zhuyan'] = "暂无";
                    }

                    //上映时间
                    $timeInfo = $this->_getPregMatch("/上映日期：<\/strong>(.*?)<\/span>/", $moviceHtml);
                    if (!empty($timeInfo[1])) {
                        $time = trim(strip_tags($timeInfo[1]));
                        $time = str_replace("年","-",$time);
                        $time = str_replace("月","-",$time);
                        $time = str_replace("日","",$time);
                        $timeArr = explode("-",$time);
                        if (empty($timeArr[2])) {
                            $time .= "01";
                        }
                        $resultArr['time1'] = strtotime($time);
                        $resultArr['nianfen'] = date("Y",$resultArr['time1']);
                    }

                    //片长
                    $pianchangInfo = $this->_getPregMatch("/片长：<\/strong>(.*?)<\/span>/", $moviceHtml);
                    if (!empty($pianchangInfo[1])) {
                        $pianchang = trim(strip_tags($pianchangInfo[1]));
                        $pianchangArr = explode(" ",$pianchang);
                        $shichangArr = $this->_getPregMatch("/[0-9]+/i",$pianchangArr[0]);
                        $resultArr['shichang'] = $shichangArr[0];
                    }

                    //地区
                    $diquInfo = $this->_getPregMatch("/国家\/地区：<\/strong>(.*?)<\/li>/", $moviceHtml);
                    if (!empty($diquInfo[1])) {
                        $resultArr['diqu'] = $this->_getDiQuType(trim(strip_tags($diquInfo[1])));
                    } else {
                        $resultArr['diqu'] = $this->_getDiQuType("其他");
                    }

                    //类型
                    $typeInfo = $this->_getPregMatch("/类型：<\/strong>(.*?)<\/li>/", $moviceHtml);
                    if (!empty($typeInfo[1])) {
                        $resultArr['type'] = $this->_getMoviceType(trim(strip_tags($typeInfo[1])));
                    } else {
                        $resultArr['type'] = $this->_getMoviceType("其他");
                    }

                    //top电影，作标志
                    if ($urlType == "top") {
                        $resultArr['topType'] = 3;
                        //评分
                        $movieScoreInfo['score'] = $pingfengTotalInfo[$infoKey];
                        $movieScoreInfo['link'] = $infoVal;
                        $movieScoreInfo['type'] = 3;
                    }

                    $moviePiaoFangInfo = array();
                    if ($urlType == "benzhoupiaofang") {
                        $moviePiaoFangInfo['webId'] = $resultArr['webId'];
                        $moviePiaoFangInfo['webType'] = $resultArr['webType'];
                        $moviePiaoFangInfo['type'] = $piaofangType;
                        $moviePiaoFangInfo['diqu'] = $piaofangDiqu;
                        $piaoFangArr = $this->_getPregMatch('/[0-9.]+/i',$piaofangInfo[$infoKey]);
                        if (empty($piaoFangArr[0])) {
                            $piaofang = "0";
                        } else {
                            $piaofang = $piaoFangArr[0];
                            if (strpos($piaofangInfo[$infoKey],"亿") !== false) {
                                $piaofang *= 10000;
                            }
                        }
                        $moviePiaoFangInfo['piaofang'] = $piaofang;
                    }

                    //读取信息是否存在
                    $info = $this->_getMoviceInfoByIdAndType($resultArr['webId'], $resultArr['webType']);

                    if (!empty($info)) { //信息已存在
                        if ($info['del'] == 1) {
                            //获取电影被合并信息
                            $delInfo = $this->_getDelMoviceInfoById($info['id']);
                            if (!empty($delInfo)) {
                                if ($urlType == "top") {
                                    //获取电影评分信息
                                    $info['id'] = $delInfo['currentInfoId'];
                                    $movieLastScoreInfo = $this->_getMoviceScoreInfoByInfoId($info['id'],3);
                                    if (empty($movieLastScoreInfo)) {//为空，则插入
                                        $movieScoreInfo['infoId'] = $info['id'];
                                        $movieScoreInfo['createTime'] = time();
                                        $this->_insertMovieScoreInfo($movieScoreInfo);
                                    } else {
                                        $movieScoreInfo['upTime'] = time();
                                        $this->_updateMovieScoreInfo($info['id'],$movieScoreInfo);
                                    }
                                } elseif ($urlType == "benzhoupiaofang") {
                                    //获取电影票房信息
                                    $infoId = $delInfo['currentInfoId'];
                                    $infoArr = array("infoId" => $infoId,"diqu" => $piaofangDiqu,"type" => $piaofangType,"del" => 0);
                                    $pfInfo = $this->_getInfo($infoArr,"one","tbl_boxOfficeInfo");
                                    if (!empty($pfInfo)) {//存在则更新
                                        $moviePiaoFangInfo['updateTime'] = time();
                                        $this->_updateInfo(array("id" => $pfInfo['id']),$moviePiaoFangInfo,"tbl_boxOfficeInfo");
                                    } else {//否则插入
                                        $moviePiaoFangInfo['infoId'] = $infoId;
                                        $moviePiaoFangInfo['createTime'] = $moviePiaoFangInfo['updateTime'] = time();
                                        $this->_insertInfo($moviePiaoFangInfo,"tbl_boxOfficeInfo");
                                    }
                                }
                            }
                        } else {
                            //获取差异信息
                            $deInfo = $this->_getComDetailInfo($info,$resultArr);
                            //更新电影信息
                            $this->_updateDetailInfo($info['id'],$deInfo);
                            if ($urlType == "top") {
                                //获取电影评分信息
                                $movieLastScoreInfo = $this->_getMoviceScoreInfoByInfoId($info['id'],3);
                                if (empty($movieLastScoreInfo)) {//为空，则插入
                                    $movieScoreInfo['infoId'] = $info['id'];
                                    $movieScoreInfo['createTime'] = time();
                                    $this->_insertMovieScoreInfo($movieScoreInfo);
                                } else {
                                    $movieScoreInfo['upTime'] = time();
                                    $this->_updateMovieScoreInfo($info['id'],$movieScoreInfo);
                                }
                            } elseif ($urlType == "benzhoupiaofang") {
                                //获取电影票房信息
                                $infoId = $info['id'];
                                $infoArr = array("infoId" => $infoId,"diqu" => $piaofangDiqu,"type" => $piaofangType,"del" => 0);
                                $pfInfo = $this->_getInfo($infoArr,"one","tbl_boxOfficeInfo");
                                if (!empty($pfInfo)) {//存在则更新
                                    $moviePiaoFangInfo['updateTime'] = time();
                                    $this->_updateInfo(array("id" => $pfInfo['id']),$moviePiaoFangInfo,"tbl_boxOfficeInfo");
                                } else {//否则插入
                                    $moviePiaoFangInfo['infoId'] = $infoId;
                                    //初始创建时间和更新时间一致
                                    $moviePiaoFangInfo['createTime'] = $moviePiaoFangInfo['updateTime'] = time();
                                    $this->_insertInfo($moviePiaoFangInfo,"tbl_boxOfficeInfo");
                                }
                            }
                        }
                    } else {
                        //写入数据库 todo
                        $insertRes = $this->_insertMoviceDetailInfo($resultArr);
                        if (!empty($insertRes) && !is_array($insertRes)) {
                            $this->_addWaterToImg($resultArr['image']);
                        }
                        if (!empty($insertRes) && !is_array($insertRes)) {
                            if ($urlType == "top") {
                                $movieScoreInfo['infoId'] = $insertRes;
                                $movieScoreInfo['createTime'] = time();
                                $this->_insertMovieScoreInfo($movieScoreInfo);
                            } elseif ($urlType == "benzhoupiaofang") {
                                //获取电影票房信息
                                $infoId = $insertRes;
                                $moviePiaoFangInfo['infoId'] = $infoId;
                                //初始创建时间和更新时间一致
                                $moviePiaoFangInfo['createTime'] = $moviePiaoFangInfo['updateTime'] = time();
                                $this->_insertInfo($moviePiaoFangInfo,"tbl_boxOfficeInfo");
                            }
                            var_dump("插入成功！" . date('Y-m-d H:i:s') . "\n");
                        } else {
                            var_dump("插入失败 {$resultArr['webId']} --- {$infoVal}！" . date('Y-m-d H:i:s') . "\n");
                            var_dump($insertRes);
                        }
                    }
                }
            }
        }
    }

    /**
     * 豆瓣网抓取主函数
     */
    private function _douban($url, $urlType)
    {
        if (empty($url) || empty($urlType)) {
            return false;
        }
        if ($urlType == "piaofang") {//暂停历史票房榜抓取
            return false;
        }

        $totalInfoHtml = $this->_getCurlInfo($url);

        $titleInfo = $this->_getPregMatchAll("/http:\/\/movie\.douban\.com\/subject\/[0-9]+\//i", $totalInfoHtml, PREG_PATTERN_ORDER);

        if (!empty($titleInfo[0]) && is_array($titleInfo[0])) {
            $titleTotalInfo = array_unique($titleInfo[0]);
            $titleTotalInfo = array_values($titleTotalInfo);
            $piaofangInfo = array();
            if ($urlType == "piaofang") {//票房榜
                $piaofangInfo = $this->_getPregMatchAll("/<span class=\"pl\">评语<\/span>(.*?)<\/p>/si", $totalInfoHtml, PREG_PATTERN_ORDER);
                $piaofangInfo = $piaofangInfo[1];
            }

            //如果是top（经典电影），则获取豆瓣评分信息
            $pingFenInfo = array();
            if ($urlType == "top") {
                $pingFenInfo = $this->_getPregMatchAll("/<span class=\"rating(.*?)\"><em>(.*?)<\/em><\/span>/i", $totalInfoHtml, PREG_PATTERN_ORDER);
                $pingFenInfo = $pingFenInfo[2];
            }
            foreach ($titleTotalInfo as $titleKey => $titleVal) {
                //$titleVal = "http://movie.douban.com/subject/1437342/";
                $resultArr = array();
                $resultArr['webType'] = $this->_webConfigInfo['douban']['type'];
                $moviceIdArr = $this->_getPregMatch("/[0-9]+/i", $titleVal);
                $resultArr['webId'] = $moviceIdArr[0];

                $moviceHtml = $this->_getCurlInfo($titleVal);
                if (!empty($moviceHtml)) {
                    //标题
                    $titleArr = $this->_getPregMatch("/<span property=\"v:itemreviewed\">(.*?)<\/span>/", $moviceHtml);
                    $titleRealArr = explode(" ", $titleArr[1]);
                    $resultArr['name'] = trim($titleRealArr[0]);
                    $firstLetter = $this->getFirstLetter($resultArr['name']);
                    if ($firstLetter != "*") {
                        $resultArr['firstLetter'] = $firstLetter;
                    }
                    //拼音
                    $pinyin = $this->getPinyin($resultArr['name'],2);
                    if (!empty($pinyin)) {
                        $resultArr['pinyin'] = $pinyin;
                    }

                    //图片地址
                    $imgArr = $this->_getPregMatch("/<img src=\"(.*?)\" title=\"(点击看更多海报|点击看大图)\" alt=\"(.*?)\" rel=\"v:image\" \/>/", $moviceHtml);
                    if (!empty($imgArr)) {
                        $imgArr  = explode('"',$imgArr[1]);
                        $imgArr[1] = $imgArr[0];
                        $imgInfo = explode(".", $imgArr[1]);
                        $imagesName = md5($resultArr['name'] . "_" . $resultArr['webType'] . "_" . $resultArr['webId']) . "." . $imgInfo[count($imgInfo) - 1];
                        $resultArr["image"] = $this->_downLoadImg($imagesName, $imgArr[1]); //下载图片并保存,并返回图片地址
                    } else {
                        $resultArr["image"] = $this->get_config_value("dy_common_img"); //默认图片
                    };

                    //导演
                    $daoyanArr = $this->_getPregMatch("/(<span class=\"pl\">导演<\/span>:(.*?)<br\/>)|(<span class='pl'>导演<\/span>:(.*?)<br\/>)/", $moviceHtml);
                    if (!empty($daoyanArr[0])) {
                        $daoyanArr[0] = strip_tags($daoyanArr[0]);
                        $daoyanArr[0]  = str_replace("导演:","",$daoyanArr[0]);
                        $resultArr["daoyan"] = trim(str_replace(" / ", "、", $daoyanArr[0]));
                    } else {
                        $resultArr["daoyan"] = "暂无";
                    }

                    //主演
                    $zhuyanArr = $this->_getPregMatch("/(<span class=\"pl\">主演(.*?)<br\/>)|(<span class='pl'>主演(.*?)<br\/>)/si", $moviceHtml);
                    if (!empty($zhuyanArr[0])) {
                        $zhuyanArr[0] = strip_tags($zhuyanArr[0]);
                        $zhuyanArr[0]  = str_replace("主演:","",$zhuyanArr[0]);
                        $resultArr["zhuyan"] = trim(str_replace(" / ", "、", $zhuyanArr[0]));
                    } else {
                        $resultArr["zhuyan"] = "暂无";
                    }

                    //类型
                    $leixingArr = $this->_getPregMatch("/<span class=\"pl\">类型(.*?)<br\/>/si", $moviceHtml);
                    if (!empty($leixingArr[1])) {
                        $resultArr["type"] = $this->_getMoviceType(strip_tags(trim($leixingArr[1])));
                    } else {
                        $resultArr["type"] = $this->_getMoviceType("其他");
                    }


                    //地区
                    $diquArr = $this->_getPregMatch("/<span class=\"pl\">制片国家(.*?)<br\/>/", $moviceHtml);
                    if (!empty($diquArr[1])) {
                        $resultArr["diqu"] = $this->_getDiQuType(strip_tags(trim($diquArr[1])));
                    } else {
                        $resultArr["diqu"] = $this->_getDiQuType("其他");
                    }


                    //片长
                    $shichangArr = $this->_getPregMatch("/<span class=\"pl\">片长:<\/span> <span property=\"v:runtime\" content=\"(.*?)\">(.*?)分钟(.*?)<\/span>/", $moviceHtml);
                    if (!empty($shichangArr)) {
                        $resultArr["shichang"] = intval($shichangArr[2]);
                    } else {
                        $resultArr["shichang"] = 0;
                    }

                    //上映时间
                    $moviceTimeArr = array_filter($this->_getPregMatchAll("/([0-9]+)-([0-9]+)-([0-9]+)\((.*?)\)/i", $moviceHtml));
                    if (empty($moviceTimeArr)) {
                        $moviceTimeArr = $this->_getPregMatchAll("/([0-9]+)-([0-9]+)\((.*?)\)/i", $moviceHtml);
                    }
                    if (!empty($moviceTimeArr[0])) {
                        foreach ($moviceTimeArr[0] as $timeVal) {
                            $timeArr = array_filter($this->_getPregMatch("/([0-9]+)-([0-9]+)-([0-9]+)\((.*?)\)/", $timeVal));
                            if (empty($timeArr)) {
                                $timeArr = array_filter($this->_getPregMatch("/([0-9]+)-([0-9]+)\((.*?)\)/", $timeVal));
                            }
                            if (empty($timeArr)) {
                                continue;
                            }
                            if (count($timeArr) < 5) {
                                $timeArr[4] = $timeArr[3];
                                $timeArr[3] = "01";
                            }
                            if (strpos($timeArr[4], "台湾") !== false || strpos($timeArr[4], "香港") !== false) {
                                $timeFiled = "time2";
                            } elseif (strpos($timeArr[4], "中国") !== false) {
                                $timeFiled = "time1";
                            } else {
                                $timeFiled = "time3";
                            }
                            $resultArr[$timeFiled] = strtotime("{$timeArr[1]}-{$timeArr[2]}-{$timeArr[3]}");
                        }
                    }

                    //本站提供链接时间+年份
                    if (!empty($resultArr["time1"])) {
                        if ($resultArr["time1"] > time()) { //未上映
                            $resultArr["time0"] = strtotime("+2 day", $resultArr["time1"]);
                        }
                        $resultArr["nianfen"] = date("Y", $resultArr["time1"]); //年份
                    }

                    //介绍
                    $jieshaoArr = $this->_getPregMatch("/(<span property=\"v:summary\">(.*?)<\/span>)|(<span property=\"v:summary\" class=\"\">(.*?)<\/span>)/si", $moviceHtml);
                    if (!empty($jieshaoArr[0])) {
                        $resultArr["jieshao"] = $this->_pregReplace("/\[[0-9]+\]/", "", trim(strip_tags($jieshaoArr[0]))); //替换掉"[数字]"的字符的介绍
                    }

                    //top电影，作标志
                    $movieScoreInfo = array();
                    if ($urlType == "top") {
                        $resultArr['topType'] = 1;
                        $movieScoreInfo['score'] = $pingFenInfo[$titleKey];
                        $movieScoreInfo['link'] = $titleVal;
                        $movieScoreInfo['type'] = 1;
                    }

                    $moviePiaoFangInfo = array();
                    if ($urlType == "piaofang") {
                        $moviePiaoFangInfo['webId'] = $resultArr['webId'];
                        $moviePiaoFangInfo['webType'] = $resultArr['webType'];
                        $moviePiaoFangInfo['type'] = 0;
                        $moviePiaoFangInfo['diqu'] = 0;
                        $piaoFangArr = $this->_getPregMatch('/[0-9.]+/i',$piaofangInfo[$titleKey]);
                        if (empty($piaoFangArr[0])) {
                            $piaofang = "0";
                        } else {
                            $piaofang = $piaoFangArr[0];
                            if (strpos($piaofangInfo[$titleKey],"亿") !== false) {
                                $piaofang *= 10000;
                            }
                        }
                        $moviePiaoFangInfo['piaofang'] = $piaofang;
                    }

                    //读取信息是否存在
                    $info = $this->_getMoviceInfoByIdAndType($resultArr['webId'], $resultArr['webType']);
                    if (!empty($info)) { //信息已存在
                        if ($info['del'] == 1) {
                            //获取电影被合并信息
                            $delInfo = $this->_getDelMoviceInfoById($info['id']);
                            if (!empty($delInfo)) {
                                if ($urlType == "top") {
                                    //获取电影评分信息
                                    $info['id'] = $delInfo['currentInfoId'];
                                    $movieLastScoreInfo = $this->_getMoviceScoreInfoByInfoId($info['id'],1);
                                    if (empty($movieLastScoreInfo)) {//为空，则插入
                                        $movieScoreInfo['infoId'] = $info['id'];
                                        $movieScoreInfo['createTime'] = time();
                                        $this->_insertMovieScoreInfo($movieScoreInfo);
                                    } else {
                                        $movieScoreInfo['upTime'] = time();
                                        $this->_updateMovieScoreInfo($info['id'],$movieScoreInfo);
                                    }
                                } elseif ($urlType == "piaofang") {
                                    //获取电影票房信息
                                    $infoId = $delInfo['currentInfoId'];
                                    $infoArr = array("infoId" => $infoId,"diqu" => 0,"type" => 0,"del" => 0);
                                    $pfInfo = $this->_getInfo($infoArr,"one","tbl_boxOfficeInfo");
                                    if (!empty($pfInfo)) {//存在则更新
                                        $moviePiaoFangInfo['updateTime'] = time();
                                        $this->_updateInfo(array("id" => $pfInfo['id']),$moviePiaoFangInfo,"tbl_boxOfficeInfo");
                                    } else {//否则插入
                                        $moviePiaoFangInfo['infoId'] = $infoId;
                                        $moviePiaoFangInfo['createTime'] = $moviePiaoFangInfo['updateTime'] = time();
                                        $this->_insertInfo($moviePiaoFangInfo,"tbl_boxOfficeInfo");
                                    }
                                }
                            }
                        } else {
                            //获取差异信息
                            $deInfo = $this->_getComDetailInfo($info,$resultArr);
                            //更新电影信息
                            $this->_updateDetailInfo($info['id'],$deInfo);
                            if ($urlType == "top") {
                                //获取电影评分信息
                                $movieLastScoreInfo = $this->_getMoviceScoreInfoByInfoId($info['id'],1);
                                if (empty($movieLastScoreInfo)) {//为空，则插入
                                    $movieScoreInfo['infoId'] = $info['id'];
                                    $movieScoreInfo['createTime'] = time();
                                    $this->_insertMovieScoreInfo($movieScoreInfo);
                                } else {
                                    $movieScoreInfo['upTime'] = time();
                                    $this->_updateMovieScoreInfo($info['id'],$movieScoreInfo);
                                }
                            } elseif ($urlType == "piaofang") {
                                //获取电影票房信息
                                $infoId = $info['id'];
                                $moviePiaoFangInfo['infoId'] = $infoId;
                                $moviePiaoFangInfo['createTime'] = $moviePiaoFangInfo['updateTime'] = time();
                                $this->_insertInfo($moviePiaoFangInfo,"tbl_boxOfficeInfo");
                            }
                        }
                    } else {
                        //写入数据库 todo
                        $insertRes = $this->_insertMoviceDetailInfo($resultArr);
                        if (!empty($insertRes) && !is_array($insertRes)) {
                            $this->_addWaterToImg($resultArr['image']);
                        }
                        if (!empty($insertRes) && !is_array($insertRes)) {
                            if ($urlType == "top") {
                                $movieScoreInfo['infoId'] = $insertRes;
                                $movieScoreInfo['createTime'] = time();
                                $this->_insertMovieScoreInfo($movieScoreInfo);
                            } elseif ($urlType == "piaofang") {
                                //获取电影票房信息
                                $infoId = $insertRes;
                                $moviePiaoFangInfo['infoId'] = $infoId;
                                $moviePiaoFangInfo['createTime'] = $moviePiaoFangInfo['updateTime'] = time();
                                $this->_insertInfo($moviePiaoFangInfo,"tbl_boxOfficeInfo");
                            }
                            var_dump("插入成功！" . date('Y-m-d H:i:s') . "\n");
                        } else {
                            var_dump("插入失败 {$moviceIdArr[0]} --- {$titleVal}！" . date('Y-m-d H:i:s') . "\n");
                            var_dump($insertRes);
                            var_dump($resultArr['webId']);
                        }
                    }
                }
            }
        }
    }

    /** 抓取搜狐视频
     * @param $url
     * @param $urlType
     */
    private function _sohu($url, $urlType)
    {
        if (empty($url) || empty($urlType)) {
            return false;
        }
        $totalInfoHtml = $this->_getCurlInfo($url);

        //观看链接
        $titleInfo = $this->_getPregMatchAll("/(http:\/\/tv\.sohu\.com\/[0-9]+\/n[0-9]+\.shtml)|(http:\/\/store\.tv\.sohu\.com\/view_html\/[0-9]+_[0-9]+\.html)/i", $totalInfoHtml, PREG_PATTERN_ORDER);
        if (!empty($titleInfo[0]) && is_array($titleInfo[0])) {
            //视频链接链接去重
            $titleRealInfo = array_values(array_unique($titleInfo[0]));

            //图片链接
            $imgInfo = $this->_getPregMatchAll("/http:\/\/photocdn\.sohu\.com\/[0-9]+\/vrsb[0-9]+\.jpg/i", $totalInfoHtml, PREG_PATTERN_ORDER);
            $imgRealInfo = array_values(array_unique($imgInfo[0]));

            foreach ($titleRealInfo as $key => $titleVal) {
                if (empty($titleVal)) {
                    continue;
                }
                //$titleVal = "http://tv.sohu.com/20130428/n374389684.shtml";
                $resultArr = array();
                $resultArr['webType'] = $this->_webConfigInfo['sohu']['type'];
                $moviceIdArr = $this->_getPregMatchAll("/[0-9]+/i", $titleVal);
                $resultArr['webId'] = (empty($moviceIdArr[0][1])) ? $moviceIdArr[0][0] : $moviceIdArr[0][1];

                $moviceHtml = mb_convert_encoding($this->_getCurlInfo($titleVal),"UTF-8","GBK");
                if (strpos(substr($moviceHtml,0,200),"The URL has moved") !== false) {
                    continue;
                }

                //电影类型信息截取
                $moviceTypeInfo = $this->_getPregMatch("/类型：(.*?)<\/li>/si",$moviceHtml);
                if (!empty($moviceTypeInfo[1])) {
                    $moviceTypeInfo = strip_tags($moviceTypeInfo[1]);
                    $resultArr['type'] = $this->_getMoviceType($moviceTypeInfo);
                } else {
                    $resultArr['type'] = $this->_getMoviceType("其他");
                }

                //电影名称
                $moviceNameInfo = $this->_getPregMatch("/<title>《(.*?)》(.*?)<\/title>/i",$moviceHtml);
                if (!empty($moviceNameInfo[1])) {
                    $resultArr['name'] = $moviceNameInfo[1];
                    $firstLetter = $this->getFirstLetter($resultArr['name']);
                    if ($firstLetter != "*") {
                        $resultArr['firstLetter'] = $firstLetter;
                    }
                } else {
                    continue;
                }

                //拼音
                $pinyin = $this->getPinyin($resultArr['name'],2);
                if (!empty($pinyin)) {
                    $resultArr['pinyin'] = $pinyin;
                }

                //电影图片
                $imgInfo = explode(".", $imgRealInfo[$key]);
                $imagesName = md5($resultArr['name'] . "_" . $resultArr['webType'] . "_" . $resultArr['webId']) . "." . $imgInfo[count($imgInfo) - 1];
                $resultArr['image'] = $this->_downLoadImg($imagesName,$imgRealInfo[$key]);
                if (empty($resultArr['image'])) {
                    $resultArr["image"] = $this->get_config_value("dy_common_img"); //默认图片
                }

                //导演
                $daoyanInfo = $this->_getPregMatch("/导演：(.*?)<\/a>/si",$moviceHtml);
                if (!empty($daoyanInfo[1])) {
                    $daoyanArr = explode("</a>",$daoyanInfo[1]);
                    $daoyanTotalArr = array();
                    foreach($daoyanArr as $daoyaVal) {
                        $daoyaVal = trim(strip_tags($daoyaVal));
                        if (empty($daoyaVal)) {
                            continue;
                        }
                        $daoyanTotalArr[] = $daoyaVal;
                    }
                    $resultArr["daoyan"] = implode("、",$daoyanTotalArr);
                } else {
                    $resultArr["daoyan"] = "暂无";
                }

                //主演
                $zhuyanInfo = $this->_getPregMatch("/主演：(.*?)<\/a>/si",$moviceHtml);
                if (!empty($zhuyanInfo[1])) {
                    $zhuyanArr = explode("</a>",$zhuyanInfo[1]);
                    $zhuyanTotalArr = array();
                    foreach($zhuyanArr as $zhuyanVal) {
                        $zhuyanVal = trim(strip_tags($zhuyanVal));
                        if (empty($zhuyanVal)) {
                            continue;
                        }
                        $zhuyanTotalArr[] = $zhuyanVal;
                    }
                    $resultArr["zhuyan"] = implode("、",$zhuyanTotalArr);
                } else {
                    $resultArr["zhuyan"] = "暂无";
                }

                //介绍
                $jieshaoInfo = $this->_getPregMatch("/<p class=\"intro\">简介：(.*?)<\/p>/si",$moviceHtml);
                $resultArr['jieshao'] = $jieshaoInfo[1];

                //年份
                $nianfenInfo = $this->_getPregMatch("/年份：(.*?)<\/a>/si",$moviceHtml);
                if (!empty($nianfenInfo[1])) {
                    $resultArr['nianfen'] = trim(strip_tags($nianfenInfo[1]));
                }

                //地区
                $diquInfo = $this->_getPregMatch("/产地：(.*?)<\/a>/si",$moviceHtml);
                if (!empty($diquInfo[1])) {
                    $resultArr['diqu'] = $this->_getDiQuType(trim(strip_tags($diquInfo[1])));
                } else {
                    $resultArr['diqu'] = $this->_getDiQuType("其他");
                }

                //年份
                $shichangInfo = $this->_getPregMatch("/时长：(.*?)<\/li>/si",$moviceHtml);
                if (!empty($shichangInfo[1])) {
                    $shichang = str_replace("时",",h,",$shichangInfo[1]);
                    $shichang = str_replace("分",",m,",$shichang);
                    $shichang = str_replace("秒",",s,",$shichang);
                    $shichangArr = explode(",",$shichang);
                    $realShiChang = 0;
                    foreach($shichangArr as $shichangKey => $shichangVal) {
                        if (empty($shichangVal)) {
                            continue;
                        }
                        if ($shichangVal == "h") {
                            $realShiChang += $shichangArr[$shichangKey - 1] * 60;
                        } elseif ($shichangVal == "m") {
                            $realShiChang += $shichangArr[$shichangKey - 1];
                        }
                    }
                    $resultArr['shichang'] = $realShiChang;
                    if (!empty($resultArr['shichang']) && ($resultArr['shichang'] > 45)) {
                        $resultArr['exist_watch'] = 1;
                    } elseif (empty($resultArr['shichang'])) {
                        $resultArr['exist_watch'] = 0;
                    } else {
                        continue;
                    }
                    $watchArr = array();
                    $watchArr['link'] = $titleVal;
                    $watchArr['player'] = 7;
                    $watchArr['qingxi'] = 3;
                    $watchArr['shoufei'] = 1;

                    //读取信息是否存在
                    $info = $this->_getMoviceInfoByIdAndType($resultArr['webId'], $resultArr['webType']);
                    if (!empty($info)) { //信息已存在
                        if ($info['del'] == 1) {
                            //获取电影被合并信息
                            $delInfo = $this->_getDelMoviceInfoById($info['id']);
                            if (!empty($delInfo)) {
                                //更新观看链接
                                $this->_updateWatchLinkInfo($info['id'],$watchArr);
                            }
                        } else {
                            //获取差异信息
                            $deInfo = $this->_getComDetailInfo($info,$resultArr);
                            //更新电影信息
                            $this->_updateDetailInfo($info['id'],$deInfo);
                            //更新观看链接
                            $this->_updateWatchLinkInfo($info['id'],$watchArr);
                        }
                        var_dump("更新成功！" . date('Y-m-d H:i:s') . "{$info['id']}\n");
                    } else {
                        //插入记录
                        $lastId = $this->_insertMoviceDetailInfo($resultArr);
                        if (!empty($lastId) && !is_array($lastId)) {
                            $this->_addWaterToImg($resultArr['image']);
                        }
                        if (!empty($lastId) && !is_array($lastId) && ($resultArr['exist_watch'] == 1)) {
                            $watchArr['infoId'] = $lastId;
                            $watchId = $this->_insertWatchLinkInfo($watchArr);
                            var_dump("插入成功！" . date('Y-m-d H:i:s') . "{$lastId}\n");
                        } else {
                            var_dump("插入失败！" . date('Y-m-d H:i:s') . "{$titleVal}\n");
                        }
                    }
                }
            }
        }
    }

    /** 迅播抓取函数
     * @param $url
     * @param $urlType
     * @return bool
     */
    private function _2tu($url, $urlType)
    {
        if (empty($url) || empty($urlType)) {
            return false;
        }
        $baseUrl = "http://www.2tu.cc";
        $totalInfoHtml = $this->_getCurlInfo($url);

        $titleInfo = $this->_getPregMatchAll("/\/Html\/GP[0-9]+\.html/i", $totalInfoHtml, PREG_PATTERN_ORDER);

        if (!empty($titleInfo[0]) && is_array($titleInfo[0])) {
            $titleInfo[0] = array_unique($titleInfo[0]);
            //视频链接链接去重
            $titleRealInfo = array_values(array_unique($titleInfo[0]));
            foreach ($titleRealInfo as $titleVal) {
                $titleVal = $baseUrl . $titleVal;
                //$titleVal = "http://www.2tu.cc/Html/GP13822.html";//测试。待删除
                $resultArr = $watchLinkInfo = $downLoadLinkInfo = array();
                $resultArr['webType'] = $this->_webConfigInfo['2tu']['type'];
                $moviceIdArr = $this->_getPregMatchAll("/[0-9]+/i", $titleVal,PREG_PATTERN_ORDER);
                $resultArr['webId'] = $moviceIdArr[0][1];

                //读取信息是否存在
                $info = $this->_getMoviceInfoByIdAndType($resultArr['webId'], $resultArr['webType']);
                if (!empty($info)) { //信息已存在
                    continue;
                }

                $moviceHtml = mb_convert_encoding($this->_getCurlInfo($titleVal),"UTF-8","GBK");

                if (!empty($moviceHtml)) {
                    //电影名称+电影类型
                    $moviceNameInfo = $this->_getPregMatch("/<div class=\"tit c5b5c5c\">当前位置:(.*?)<\/div>/si",$moviceHtml);
                    $moviceNameInfo = strip_tags(trim($moviceNameInfo[1]));
                    $moviceNameInfo  = array_filter(explode("&nbsp;",$moviceNameInfo));
                    $moviceNameInfo = explode("\t\t",implode("\t\t",$moviceNameInfo));
                    $resultArr['name'] = $moviceNameInfo[count($moviceNameInfo) - 1];
                    $firstLetter = $this->getFirstLetter($resultArr['name']);
                    if ($firstLetter != "*") {
                        $resultArr['firstLetter'] = $firstLetter;
                    }
                    //拼音
                    $pinyin = $this->getPinyin($resultArr['name'],2);
                    if (!empty($pinyin)) {
                        $resultArr['pinyin'] = $pinyin;
                    }

                    $resultArr['type'] = $this->_getMoviceType($moviceNameInfo[count($moviceNameInfo) - 3]);

                    //电影主演以及年份等信息
                    $moviceZhuyanInfo = $this->_getPregMatch("/<div class=\"text c0071bc\">(.*?)<\/div>/si",$moviceHtml);

                    $moviceZhuyanInfo = strip_tags(trim($moviceZhuyanInfo[1]));
                    $moviceZhuyanInfo  = array_filter(explode("&nbsp;",$moviceZhuyanInfo));
                    $moviceZhuyanInfo = array_values($moviceZhuyanInfo);
                    $i = 1;
                    $count = count($moviceZhuyanInfo);
                    $zhuYanArr = array();
                    foreach($moviceZhuyanInfo as $infoVal) {
                        if ($i != $count) {
                            $zhuYanArr[] = str_replace("主演：","",$infoVal);
                        } else {
                            $infoValArr = explode("：",$infoVal);
                            $resultArr['diqu'] = $this->_getDiQuType($infoValArr[3]);
                            $resultArr['nianfen'] = trim($infoValArr[count($infoValArr) - 1]);
                        }
                        $i++;
                    }
                    $resultArr['zhuyan'] = implode("、",$zhuYanArr);

                    //介绍信息
                    $moviceJieShaoInfo = $this->_getPregMatch("/<div class=\"about_t\">(.*?)<\/div>/si",$moviceHtml);
                    $resultArr['jieshao'] = strip_tags(trim($moviceJieShaoInfo[1]));

                    //观看链接
                    $watchLink1 = $this->_getPregMatchAll("/<ul class=\"pdown\">(.*?)<\/ul>/si",$moviceHtml,PREG_PATTERN_ORDER);
                    if (!empty($watchLink1[1][0])) {
                        $watchLink1 = $this->_getPregMatchAll("/<li><a title=\'(.*?)\' href=\'(.*?)\' target=\"_blank\">(.*?)<\/a><\/li>/si",$watchLink1[1][0],PREG_PATTERN_ORDER);
                        if (!empty($watchLink1[2])) {
                            $wI = 0;
                            foreach($watchLink1[2] as $link) {
                                $watchLinkInfo[$wI]['link'] = $baseUrl . $link;//观看链接1
                                $watchLinkInfo[$wI]['player'] = 2;//百度影音播放
                                $watchLinkInfo[$wI]['qingxi'] = 3;
                                $watchLinkInfo[$wI]['shoufei'] = 1;
                                $wI++;
                            }
                        }
                    }

                    //下载链接
                    $downLoadHtml = $this->_getPregMatch("/<div class=\"dwon_tx\">(.*?)<\/div>/si",$moviceHtml);
                    if (!empty($downLoadHtml[1])) {
                        $downLoadHtml = $this->_getPregMatchAll("/<a href=\"(.*?)\'/si",$downLoadHtml[1],PREG_PATTERN_ORDER);
                        if (!empty($downLoadHtml[1])) {
                            $downLoadArr = array_filter($downLoadHtml[1]);
                            $dI = 0;
                            foreach($downLoadArr as $downLink) {
                                $downLoadLinkInfo[$dI]["link"] = $downLink;
                                $downLoadLinkInfo[$dI]["type"] = 1;
                                $downLoadLinkInfo[$dI]["size"] = 0;
                                $dI++;
                            }
                        }
                    }
                    //图片地址
                    $imgInfo = $this->_getPregMatch("/<div class=\"img\">(.*?)<\/div>/si",$moviceHtml);
                    $imgInfo = $this->_getPregMatch("/(http:(.*?)\.jpg)/si",$imgInfo[1]);
                    if (!empty($imgInfo[0])) {
                        $url = trim($imgInfo[0]);
                        $urlArr = explode(".", $url);
                        $imagesName = md5($resultArr['name'] . "_" . $resultArr['webType'] . "_" . $resultArr['webId']) . "." . $urlArr[count($urlArr) - 1];
                        $resultArr['image'] = $this->_downLoadImg($imagesName,$imgInfo[0]);
                    } else {
                        $resultArr['image'] = $this->get_config_value("dy_common_img"); //默认图片
                    }
                }
                if (!empty($watchLinkInfo)) {
                    $resultArr['exist_watch'] = 1;
                }

                if (!empty($resultArr)) {
                    $lastId = $this->_insertMoviceDetailInfo($resultArr);
                    if (!empty($lastId) && !is_array($lastId)) {
                        $this->_addWaterToImg($resultArr['image']);
                    }
                    if (!empty($lastId) && !is_array($lastId)) {
                        if (!empty($watchLinkInfo)) {//插入观看链接
                            foreach($watchLinkInfo as $val) {
                                $val['infoId']  = $lastId;
                                $this->_insertWatchLinkInfo($val);
                            }
                        }
                    }
                }
            }
        }
    }

    /** 土豆抓取函数
     * @param $url
     * @param $urlType
     * @return bool
     */
    private function _tudou($url, $urlType)
    {
        if (empty($url) || empty($urlType)) {
            return false;
        }
        $totalInfoHtml = $this->_getCurlInfo($url);

        $titleInfo = $this->_getPregMatchAll("/http:\/\/www\.tudou\.com\/albumcover\/[0-9a-zA-Z]+\.html/i", $totalInfoHtml, PREG_PATTERN_ORDER);

        if (!empty($titleInfo[0]) && is_array($titleInfo[0])) {
            //视频链接链接去重
            $titleRealInfo = array_values(array_unique($titleInfo[0]));
            foreach ($titleRealInfo as $titleVal) {
                //$titleVal = "http://www.tudou.com/albumcover/DDPKzYeTq9c.html";//测试。待删除
                $resultArr = $watchLinkInfo = $downLoadLinkInfo = $resZhuYanArr = $resDaoYanArr = array();
                $resultArr['webType'] = $this->_webConfigInfo['tudou']['type'];
                $moviceIdArr = explode("/",$titleVal);
                $moviceIdArr = explode(".",$moviceIdArr[count($moviceIdArr) - 1]);
                $resultArr['webId'] = $this->_get_id($moviceIdArr[0]);

                $moviceHtml = mb_convert_encoding($this->_getCurlInfo($titleVal),"UTF-8","GBK");
                if (!empty($moviceHtml)) {
                    //电影名称
                    $moviceNameInfo = $this->_getPregMatch("/<h1>(.*?)<\/h1>/si",$moviceHtml);
                    $resultArr['name'] = $moviceNameInfo[1];
                    $firstLetter = $this->getFirstLetter($resultArr['name']);
                    if ($firstLetter != "*") {
                        $resultArr['firstLetter'] = $firstLetter;
                    }
                    //拼音
                    $pinyin = $this->getPinyin($resultArr['name'],2);
                    if (!empty($pinyin)) {
                        $resultArr['pinyin'] = $pinyin;
                    }

                    //电影图片
                    $moviceImgInfo = $this->_getPregMatch("/<img  class=\"quic\"  width=\"160\" height=\"240\"  src=\"(.*?)\"  >/si",$moviceHtml);
                    $imgUrl = trim($moviceImgInfo[1]);
                    if (!empty($imgUrl)) {
                        $imagesName = md5($resultArr['name'] . "_" . $resultArr['webType'] . "_" . $resultArr['webId']) . ".jpeg";
                        $resultArr['image'] = $this->_downLoadImg($imagesName,$imgUrl);
                    } else {
                        $resultArr['image'] = $this->get_config_value("dy_common_img"); //默认图片
                    }

                    //电影主演
                    $moviceZhuyanInfo = $this->_getPregMatch("/<ul class=\"album-txt-list\">(.*?)<\/ul>/si",$moviceHtml);
                    $zhuyanInfo = $this->_getPregMatchAll("/<b>演员： <\/b>(.*?)title=\"(.*?)\"(.*?)<\/li>/si",$moviceZhuyanInfo[1],PREG_PATTERN_ORDER);
                    if (empty($zhuyanInfo[2][0])) {
                        $resultArr['zhuyan'] = "暂无";
                    } else {
                        $resZhuYanArr[] = $zhuyanInfo[2][0];
                        if (!empty($zhuyanInfo[3][0])) {
                            $otherZy = $this->_getPregMatchAll("/title=\"(.*?)\"/i",$zhuyanInfo[3][0],PREG_PATTERN_ORDER);
                            $resZhuYanArr  =array_merge($resZhuYanArr,$otherZy[1]);
                            $resZhuYanArr = array_slice($resZhuYanArr,0,10);
                        }
                        $resultArr['zhuyan'] = implode("、",$resZhuYanArr);
                    }

                    //电影导演
                    $daoYanInfo = $this->_getPregMatchAll("/<b>导演： <\/b>(.*?)title=\"(.*?)\"(.*?)<\/li>/si",$moviceZhuyanInfo[1],PREG_PATTERN_ORDER);
                    if (empty($daoYanInfo[2][0])) {
                        $resultArr['daoyan'] = "暂无";
                    }
                    if (!empty($daoYanInfo[2][0])) {
                        $resDaoYanArr[] = $daoYanInfo[2][0];
                        if (!empty($daoYanInfo[3][0])) {
                            $otherDy = $this->_getPregMatchAll("/title=\"(.*?)\"/i",$daoYanInfo[3][0],PREG_PATTERN_ORDER);
                            $resDaoYanArr  =array_merge($resDaoYanArr,$otherDy[1]);
                        }
                        $resultArr['daoyan'] = implode("、",$resDaoYanArr);
                    }

                    //电影地区
                    $diQuInfo = $this->_getPregMatch("/<b>地区： <\/b>(.*?)<\/li>/si",$moviceZhuyanInfo[1]);
                    $resultArr['diqu'] = $this->_getDiQuType(strip_tags(trim($diQuInfo[1])));

                    //电影年代
                    $nianDaiInfo = $this->_getPregMatch("/<b>年代：<\/b>(.*?)<\/li>/si",$moviceZhuyanInfo[1]);

                    $resultArr['nianfen'] = trim(strip_tags(trim($nianDaiInfo[1])));
                    //电影类型
                    $typeInfo = $this->_getPregMatch("/类型：(.*?)<\/li>/si",$moviceZhuyanInfo[1]);
                    $resTypeArr = strip_tags(trim($typeInfo[1]));
                    $resultArr['type'] = $this->_getMoviceType(trim($resTypeArr));

                    //介绍信息
                    $moviceJieShaoInfo = $this->_getPregMatch("/<p id=\"albumIntro\">(.*?)<\/p>/si",$moviceZhuyanInfo[1]);
                    $jieJian = strip_tags(trim($moviceJieShaoInfo[1]));
                    if (empty($jieJian)) {
                        $resultArr['jieshao'] = "暂无";
                    } else {
                        $resultArr['jieshao'] = strip_tags(trim($moviceJieShaoInfo[1]));
                    }

                    //观看链接
                    $watchLink1 = $this->_getPregMatch("/<div class=\"album-btn\">(.*?)<\/div>/si",$moviceHtml);
                    $watchLink1 = $this->_getPregMatch("/href=\"(.*?)\"/i",$watchLink1[1]);
                    if (!empty($watchLink1[1])) {
                        $watchLinkInfo['link'] = $watchLink1[1];//观看链接1
                        $watchLinkInfo['player'] = 6;//土豆播放
                        $watchLinkInfo['qingxi'] = 3;
                        $watchLinkInfo['shoufei'] = 1;
                        $wHtml = mb_convert_encoding($this->_getCurlInfo($watchLink1[1]),"UTF-8","GBK");
                        $wInfo = $this->_getPregMatch("/<h4 class=\"vcate_title\" id=\"vcate_title\">(.*?)<\/h4>/si",$wHtml);
                        $wText = strip_tags($wInfo[1]);
                        if (strpos($wText,"预告") !== false) {
                            $watchLinkInfo['beizhu'] = "预告";
                            $watchLinkInfo['shoufei'] = 3;
                        } elseif (strpos($wText,"特辑") !== false) {
                            $watchLinkInfo['beizhu'] = "特辑";
                            $watchLinkInfo['shoufei'] = 4;
                        } elseif (strpos($wText,"纪录片") !== false) {
                            $watchLinkInfo['beizhu'] = "纪录片";
                            $watchLinkInfo['shoufei'] = 5;
                        } else {
                            $watchLinkInfo['beizhu'] = "高清免费";
                        }
                    }
                }

                if (!empty($watchLinkInfo)) {
                    $resultArr['exist_watch'] = 1;
                } else {
                    $resultArr['exist_watch'] = 0;
                }

                //读取信息是否存在
                $info = $this->_getMoviceInfoByIdAndType($resultArr['webId'], $resultArr['webType']);
                if (!empty($info)) { //信息已存在
                    if ($info['del'] == 1) {
                        //获取电影被合并信息
                        $delInfo = $this->_getDelMoviceInfoById($info['id']);
                        if (!empty($delInfo)) {
                            //更新观看链接
                            $this->_updateWatchLinkInfo($delInfo['currentInfoId'],$watchLinkInfo);
                        }
                    } else {
                        //获取差异信息
                        $deInfo = $this->_getComDetailInfo($info,$resultArr);
                        //更新电影信息
                        $this->_updateDetailInfo($info['id'],$deInfo);
                        //更新观看链接
                        $this->_updateWatchLinkInfo($info['id'],$watchLinkInfo);
                    }
                } else {
                    $lastId = $this->_insertMoviceDetailInfo($resultArr);
                    if (!empty($lastId) && !is_array($lastId)) {
                        $this->_addWaterToImg($resultArr['image']);
                    }
                    if (!empty($lastId) && !is_array($lastId)) {
                        $watchLinkInfo['infoId']  = $lastId;
                        $this->_insertWatchLinkInfo($watchLinkInfo);
                    }
                }
            }
        }
    }

    /** 抓取乐视视频
     * @param $url
     * @param $urlType
     */
    private function _letv($url, $urlType)
    {
        if (empty($url) || empty($urlType)) {
            return false;
        }
        $totalInfoHtml = $this->_getCurlInfo($url);

        //观看链接
        $titleInfo = $this->_getPregMatchAll("/(http:\/\/www\.letv\.com\/ptv\/pplay\/[0-9]+\/[0-9]+\.html)|(http:\/\/yuanxian\.letv\.com\/detail\/[0-9]+\.html)/i", $totalInfoHtml, PREG_PATTERN_ORDER);
        if (!empty($titleInfo[0]) && is_array($titleInfo[0])) {
            //视频链接链接去重
            $titleRealInfo = array_values(array_unique($titleInfo[0]));

            foreach ($titleRealInfo as $titleVal) {
                if (empty($titleVal)) {
                    continue;
                }
                if (strpos($titleVal,"yuanxian") !== false) {//收费视频
                    $this->_letvShouFei($titleVal);
                } else {//免费视频
                    $this->_letvMianFei($titleVal);
                }
            }
        }
    }

    /** 将只含有字符的字符串转换成数字
     * @param $char
     * @return string
     */
    private function _getCharNumStr($char) {
        $array = array(
            'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
            'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
        );
        $len=strlen($char);
        $sum = "";
        for($i=0;$i<$len;$i++) {
            if (is_numeric($char[$i])) {
                $index = $char[$i];
            } else {
                $index = array_search($char[$i],$array) + 1;
            }
            $sum .= $index;
        }
        return $sum;
    }

    public static function _get_id($str,$base = '') {
        $base = empty($base) ? self::$_base_hex : $base;
        $out = 0;
        $len = strlen($str) - 1;
        for ($t = 0; $t <= $len; $t++) {
            $out = $out + strpos(self::$_charlist, substr($str, $t, 1)) * pow($base, $len - $t);
        }
        $out = abs(intval($out));
        return $out;
    }


    private $_detailFildInfo = array(//detailInfo表各字段信息
        "name" => array("null" => false,'title' => "名称"),
        "type" => array("null" => false,'title' => "类型"),
        "jieshao" => array("null" => false,'title' => "简介"),
        "zhuyan" => array("null" => false,'title' => "主演"),
        "time0" => array("null" => true,'title' => "本周提供观看链接时间"),
        "time1" => array("null" => true,'title' => "中国上映时间"),
        "time2" => array("null" => true,'title' => "欧美上映时间"),
        "time3" => array("null" => true,'title' => "港台上映时间"),
        "diqu" => array("null" => false,'title' => "地区"),
        "nianfen" => array("null" => true,'title' => "年份"),
        "daoyan" => array("null" => true,'title' => "导演"),
        "shichang" => array("null" => true,'title' => "时长"),
        "image" => array("null",'title' => "图片")
    );

    /** 检查信息是否合法
     * @param array $info
     * @return array
     */
    private function _checkDetail($info = array())
    {
        $result = array(
            "code" => false,
            "error" => "参数错误！",
        );
        if (empty($info)) {
            return $result;
        }
        foreach($this->_detailFildInfo as $infoKey => $infoVal) {
            if (empty($infoVal['null']) && !$info[$infoKey]) {
                $result['code'] = false;
                $result['error'] = $infoVal['title'] . "参数错误";
                break;
            } else {
                $result['code'] = true;
            }
        }
        return $result;
    }

    /** 根据名称和上映时间获取电影信息
     * @param $name
     * @param $time1
     * @return bool|mixed
     */
    private function _getMoviceInfoByNameAndNianfen($name,$nianfen) {
        $nianfen = intval($nianfen);
        if (!isset($name) || empty($nianfen)) {
            return false;
        }
        $sql = "select * from `tbl_detailInfo` where name like '{$name}%' and nianfen = {$nianfen} limit 1;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetch();
    }

    /** 插入抓取数据
     * @param array $dataArr 数据数组
     * @return bool|int
     */
    private function _insertMoviceInfo($dataArr = array())
    {
        if (empty($dataArr)) {
            return false;
        }
        $checkRes = $this->_checkDetail($dataArr);
        if (empty($checkRes['code'])) {
            return $checkRes;
        }
        $keyArr = array_keys($dataArr);
        $keyStr = implode(",", $keyArr);
        $valueArr = array_fill(0, count($dataArr), '?');
        $valueStr = implode(",", $valueArr);
        $sql = "insert into `tbl_grabMovice` ({$keyStr}) values ({$valueStr});";
        $stmt = $this->_pdo->prepare($sql);
        $dataArr = array_values($dataArr);
        $stmt->execute($dataArr);
        return $stmt->rowCount();
    }

    private function _updateMoviceDetailInfo($dataArr = array()) {
        if (empty($dataArr)) {
            return false;
        }
        $sql = "update `tbl_detailInfo` set image = '{$dataArr['image']}' where id = {$dataArr['id']} limit 1;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->execute();
        $cou = $stmt->rowCount();
        return $cou;
    }

    /**
     * 对主演或者导演信息进行并插入
     * @param array $dataArr
     * @param string $tableName
     * @return bool|string
     */
    protected function _insertActOrDirectorInfo($dataArr = array(),$tableName = "tbl_actInfo")
    {
        if (empty($dataArr) || empty($dataArr['infoId']) || empty($dataArr['name'])) {
            return false;
        }
        $dataArr['createTime'] = time();//创建时间
        $keyArr = array_keys($dataArr);
        $keyStr = implode(",", $keyArr);
        $valueArr = array_fill(0, count($dataArr), '?');
        $valueStr = implode(",", $valueArr);
        $sql = "insert into `" . $tableName ."` ({$keyStr}) values ({$valueStr});";
        $stmt = $this->_pdo->prepare($sql);
        $dataArr = array_values($dataArr);
        try {
            $stmt->execute($dataArr);
            return $this->_pdo->lastInsertId();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 对主演或者导演信息进行处理并插入
     * @param $infoStr
     * @param $infoId
     * @param string $tableName
     * @return bool
     */
    private function _insertActOrDirectorInfoAction($infoStr,$infoId,$tableName = "tbl_actInfo") {
        $infoStr = trim($infoStr);
        $infoId = intval($infoId);
        if (empty($infoStr) || empty($infoId)) {
            return false;
        }
        $infoStr = str_replace("/","、",$infoStr);
        $infoStr = str_replace(",","、",$infoStr);
        $infoStr = str_replace("，","、",$infoStr);
        $infoArr = explode("、",$infoStr);
        foreach($infoArr as $infoVal) {
            $infoVal = trim($infoVal);
            if ($infoVal == "暂无") {
                continue;
            }
            $data = array();
            $data['infoId'] = $infoId;
            $data['name'] = $infoVal;
            $firstLetter = $this->getFirstLetter($infoVal);
            if ($firstLetter != "*") {
                $data['firstLetter'] = $firstLetter;
            }
            //拼音
            $pinyin = $this->getPinyin($infoVal,2);
            if (!empty($pinyin)) {
                $data['pinyin'] = $pinyin;
            }
            $this->_insertActOrDirectorInfo($data,$tableName);
        }
    }

    /**
     * 插入抓取数据
     * @param array $dataArr 数据数组
     * @return bool|int
     */
    private function _insertMoviceDetailInfo($dataArr = array())
    {
        if (empty($dataArr) || strpos($dataArr['name'],'特辑') !== false || strpos($dataArr['name'],'纪录片') !== false || strpos($dataArr['name'],'首映礼') !== false || strpos($dataArr['name'],'预告') !== false || strpos($dataArr['name'],'微电影') !== false) {
            return false;
        }
        $dataArr = array_filter($dataArr);
        unset($dataArr['inType']);
        $checkRes = $this->_checkDetail($dataArr);
        if (empty($checkRes['code'])) {
            return $checkRes;
        }
        $dataArr['createtime'] = time();//创建时间
        $keyArr = array_keys($dataArr);
        $keyStr = implode(",", $keyArr);
        $valueArr = array_fill(0, count($dataArr), '?');
        $valueStr = implode(",", $valueArr);
        $sql = "insert into `tbl_detailInfo` ({$keyStr}) values ({$valueStr});";
        $stmt = $this->_pdo->prepare($sql);
        $fisrtDataArr = $dataArr;
        $dataArr = array_values($dataArr);
        try{
            $stmt->execute($dataArr);
            $lastId = $this->_pdo->lastInsertId();
            if (!empty($lastId)) {
                //插入主演信息
                $this->_insertActOrDirectorInfoAction($fisrtDataArr['zhuyan'],$lastId,"tbl_actInfo");
                //插入导演信息
                $this->_insertActOrDirectorInfoAction($fisrtDataArr['daoyan'],$lastId,"tbl_directorInfo");
            }
            return $lastId;
        } catch (Exception $e) {
            return false;
        }
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
        $dataArr = array_values($dataArr);
        $stmt->execute($dataArr);
        return $this->_pdo->lastInsertId();
    }

    /** 插入下载链接信息
     * @param array $dataArr 数据数组
     * @return bool|int
     */
    private function _insertDownLoadLinkInfo($dataArr = array())
    {
        if (empty($dataArr)) {
            return false;
        }
        $keyArr = array_keys($dataArr);
        $keyStr = implode(",", $keyArr);
        $valueArr = array_fill(0, count($dataArr), '?');
        $valueStr = implode(",", $valueArr);
        $sql = "insert into `tbl_downLoad` ({$keyStr}) values ({$valueStr});";
        $stmt = $this->_pdo->prepare($sql);
        $dataArr = array_values($dataArr);
        $stmt->execute($dataArr);
        return $this->_pdo->lastInsertId();
    }

    private function _updateGrabMoviceInfoByWebIdAndType($id, $type)
    {
        $id = intval($id);
        $type = intval($type);
        if (empty($id) || empty($type)) {
            return false;
        }
        $sql = "update `tbl_grabMovice` set topType = 1 where webId = ? and webType = ? limit 1;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->execute(array($id, $type));
        return $stmt->rowCount();
    }
    /** curl 获取信息
     * @param $url
     * @param bool $json
     * @return mixed
     */
    protected function _getCurlInfo($url, $json = false)
    {
        $ch = curl_init(); //初始化curl
        $user_agent = "Mozilla/5.0";
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //设置是否返回信息
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: text/xml")); //设置HTTP头
        curl_setopt($ch, CURLOPT_URL, $url); //设置链接
        $response = curl_exec($ch); //接收返回信息
        curl_close($ch); //关闭curl链接
        return $json ? json_decode($response, true) : $response;
    }

    /** 乐视收费电影处理
     * @param $htmlUrl
     * @param $imgUrl
     */
    private function _letvShouFei($htmlUrl) {
        if (empty($htmlUrl)) {
            return false;
        }

        //$htmlUrl = "http://www.letv.com/ptv/pplay/39101/1.html";//测试，待删除
        $resultArr = array();
        $resultArr['webType'] = $this->_webConfigInfo['letv']['type'];
        $moviceIdArr = $this->_getPregMatchAll("/[0-9]+/i", $htmlUrl);
        $resultArr['webId'] = (empty($moviceIdArr[0][0])) ? $moviceIdArr[0][1] : $moviceIdArr[0][0];

        $moviceHtml = $this->_getCurlInfo($htmlUrl);

        //电影名称
        $moviceNameInfo = $this->_getPregMatch("/<title>(.*?)<\/title>/si",$moviceHtml);
        if (empty($moviceNameInfo[1])) {
            var_dump($htmlUrl);
            return false;
        }
        $moviceNameInfo = str_replace("网络院线—电影—","",strip_tags($moviceNameInfo[1]));
        $moviceNameInfoArr = explode("-",$moviceNameInfo);
        $resultArr['name'] = trim($moviceNameInfoArr[count($moviceNameInfoArr) - 1]);
        $firstLetter = $this->getFirstLetter($resultArr['name']);
        if ($firstLetter != "*") {
            $resultArr['firstLetter'] = $firstLetter;
        }
        //拼音
        $pinyin = $this->getPinyin($resultArr['name'],2);
        if (!empty($pinyin)) {
            $resultArr['pinyin'] = $pinyin;
        }

        //电影图片
        $imgInfo = $this->_getPregMatch("/<a class=\" w150\">(.*?)<\/a>/si",$moviceHtml);
        $imgUrlInfo = $this->_getPregMatch("/<img src=\"(.*?)\"(.*?)>/si",$imgInfo[1]);
        $imgUrl = $imgUrlInfo[1];
        $imgInfo = explode(".", $imgUrl);
        $imagesName = md5($resultArr['name'] . "_" . $resultArr['webType'] . "_" . $resultArr['webId']) . "." . $imgInfo[count($imgInfo) - 1];
        $resultArr['image'] = $this->_downLoadImg($imagesName,$imgUrl);
        if (empty($resultArr['image'])) {
            $resultArr["image"] = $this->get_config_value("dy_common_img"); //默认图片
        }

        //导演
        $daoyanInfo = $this->_getPregMatch("/<dd>导演：&nbsp;(.*?)<\/dd>/si",$moviceHtml);
        $daoyanInfo = strip_tags($daoyanInfo[1]);
        $resultArr["daoyan"] = trim($daoyanInfo);

        //主演
        $zhuyanInfo = $this->_getPregMatch("/<dd>主演：(.*?)<\/dd>/si",$moviceHtml);
        $zhuyanInfo = trim(strip_tags($zhuyanInfo[1]));
        $zhuyanInfoArr = array_filter(explode("\n",$zhuyanInfo));
        $zhuyanArr = array();
        foreach($zhuyanInfoArr as $zhuyanVal) {
            $zhuyanVal = trim($zhuyanVal);
            if (empty($zhuyanVal)) {
                continue;
            }
            $zhuyanArr[ ] = $zhuyanVal;
        }
        $resultArr["zhuyan"] = implode("、",$zhuyanArr);

        //地区
        $diquInfo = $this->_getPregMatch("/<dd>地区：(.*?)<\/dd>/si",$moviceHtml);
        $diquInfo = strip_tags($diquInfo[1]);
        $resultArr["diqu"] = $this->_getDiQuType(trim($diquInfo));

        //类型
        $typeInfo = $this->_getPregMatch("/<dd>类型：(.*?)<\/dd>/si",$moviceHtml);
        $typeInfo = strip_tags($typeInfo[1]);
        $resultArr["type"] = $this->_getMoviceType(trim($typeInfo));

        //年份
        $nianfenInfo = $this->_getPregMatch("/<dd>年份：&nbsp;(.*?)<\/dd>/si",$moviceHtml);
        $nianfenInfo = strip_tags($nianfenInfo[1]);
        $nianfenInfo = str_replace("年","",$nianfenInfo);
        $resultArr["nianfen"] = trim($nianfenInfo);

        //时长
        $shichangInfo = $this->_getPregMatch("/<dd>时长：&nbsp;(.*?)<\/dd>/si",$moviceHtml);
        $shichangInfo = strip_tags($shichangInfo[1]);
        $shichangInfo = str_replace("分钟","",$shichangInfo);
        $resultArr["shichang"] = trim($shichangInfo);

        //介绍
        $jieshaoInfo = $this->_getPregMatch("/<div class=\"tabDiv\">(.*?)<\/div>/si",$moviceHtml);
        $jieshaoInfo = strip_tags($jieshaoInfo[1]);
        $resultArr["jieshao"] = trim($jieshaoInfo);

        if (empty($resultArr['shichang']) || (!empty($resultArr['shichang']) && ($resultArr['shichang'] > 45))) {
            $resultArr['exist_watch'] = 1;
        } else {
            $resultArr['exist_watch'] = 0;
        }

        $watchArr = array();
        $watchArr['link'] = $htmlUrl;
        $watchArr['player'] = 13;
        $watchArr['qingxi'] = 3;
        $watchArr['shoufei'] = 2;

        //读取信息是否存在
        $info = $this->_getMoviceInfoByIdAndType($resultArr['webId'], $resultArr['webType']);
        if (!empty($info)) { //信息已存在
            if ($info['del'] == 1) {
                //获取电影被合并信息
                $delInfo = $this->_getDelMoviceInfoById($info['id']);
                if (!empty($delInfo)) {
                    //更新观看链接
                    $this->_updateWatchLinkInfo($delInfo['currentInfoId'],$watchArr);
                }
            } else {
                //获取差异信息
                $deInfo = $this->_getComDetailInfo($info,$resultArr);
                //更新电影信息
                $this->_updateDetailInfo($info['id'],$deInfo);
                //更新观看链接
                $this->_updateWatchLinkInfo($info['id'],$watchArr);
            }
        } else {
            $lastId = $this->_insertMoviceDetailInfo($resultArr);
            if (!empty($lastId) && !is_array($lastId)) {
                $this->_addWaterToImg($resultArr['image']);
            }
            if (!empty($lastId) && !is_array($lastId)) {
                $watchArr['infoId'] = $lastId;
                $watchId = $this->_insertWatchLinkInfo($watchArr);
            }
        }
    }
    /** 乐视免费电影处理
     * @param $htmlUrl
     * @param $imgUrl
     */
    private function _letvMianFei($htmlUrl) {
        if (empty($htmlUrl)) {
            return false;
        }
        //$htmlUrl = "http://www.letv.com/ptv/pplay/39101/1.html";//测试，待删除
        $resultArr = array();
        $resultArr['webType'] = $this->_webConfigInfo['letv']['type'];
        $moviceIdArr = $this->_getPregMatchAll("/[0-9]+/i", $htmlUrl);
        $resultArr['webId'] = (empty($moviceIdArr[0][0])) ? $moviceIdArr[0][1] : $moviceIdArr[0][0];

        //读取信息是否存在
        $info = $this->_getMoviceInfoByIdAndType($resultArr['webId'], $resultArr['webType']);
        if (!empty($info)) { //信息已存在
            return false;
        }

        //影片详情真正地址，这里的地址信息比较全
        $realUrl = str_replace("{B}",$resultArr['webId'],"http://so.letv.com/film/{B}.html");
        $moviceHtml = $this->_getCurlInfo($realUrl);

        //电影名称
        $moviceNameInfo = $this->_getPregMatch("/<title>(.*?)<\/title>/si",$moviceHtml);
        $moviceNameInfo = str_replace("在线观看-乐视网","",strip_tags($moviceNameInfo[1]));
        $moviceNameInfoArr = explode("-",$moviceNameInfo);
        $resultArr['name'] = trim($moviceNameInfoArr[0]);
        $firstLetter = $this->getFirstLetter($resultArr['name']);
        if ($firstLetter != "*") {
            $resultArr['firstLetter'] = $firstLetter;
        }
        //拼音
        $pinyin = $this->getPinyin($resultArr['name'],2);
        if (!empty($pinyin)) {
            $resultArr['pinyin'] = $pinyin;
        }

        //电影图片
        $imgUrlInfo = $this->_getPregMatch("/<dt data-itemhldr=\"a\" data-statectn=\"n_w150_dt\">(.*?)<\/dt>/si",$moviceHtml);
        $imgUrlInfo = $this->_getPregMatch("/<img src=\"(.*?)\" >/si",$imgUrlInfo[1]);
        $imgUrl = $imgUrlInfo[1];
        $imgInfo = explode(".", $imgUrl);
        $imagesName = md5($resultArr['name'] . "_" . $resultArr['webType'] . "_" . $resultArr['webId']) . "." . $imgInfo[count($imgInfo) - 1];
        $resultArr['image'] = $this->_downLoadImg($imagesName,$imgUrl);
        if (empty($resultArr['image'])) {
            $resultArr["image"] = $this->get_config_value("dy_common_img"); //默认图片
        }

        //电影导演
        $moviceDaoYanInfo = $this->_getPregMatch("/<span class=\"s1\">(.*?)<\/span>/si",$moviceHtml);
        $moviceDaoYanInfo = strip_tags($moviceDaoYanInfo[1]);
        $moviceDaoYanInfoArr = explode("导演：",$moviceDaoYanInfo);
        $resultArr['daoyan'] = trim($moviceDaoYanInfoArr[1]);

        //主演信息
        $moviceZhuYanInfo = $this->_getPregMatch("/<p class=\"p3\">主演：(.*?)<\/p>/si",$moviceHtml);
        $moviceZhuYanInfo = strip_tags($moviceZhuYanInfo[1]);
        $moviceZhuYanInfoArr = array_filter(explode("\n",$moviceZhuYanInfo));
        $realZhuYanArr = array();
        foreach($moviceZhuYanInfoArr as $val) {
            $val = trim($val);
            if (empty($val)) {
                continue;
            }
            $realZhuYanArr[] = $val;
        }
        $resultArr['zhuyan'] = implode("、",$realZhuYanArr);

        //时长
        $moviceShiChangInfo = $this->_getPregMatch("/<span class=\"s3\">(.*?)<\/span>/si",$moviceHtml);
        $moviceShiChangInfo = strip_tags($moviceShiChangInfo[1]);
        $moviceShiChangInfo = str_replace("时长：","",$moviceShiChangInfo);
        $moviceShiChangInfo = str_replace("分钟","",$moviceShiChangInfo);
        $resultArr['shichang'] = trim($moviceShiChangInfo);

        //地区
        $moviceDiQuInfo = $this->_getPregMatch("/<span class=\"s4\">(.*?)<\/span>/si",$moviceHtml);
        $moviceDiQuInfo = strip_tags($moviceDiQuInfo[1]);
        $moviceDiQuInfo = str_replace("地区：","",$moviceDiQuInfo);
        $resultArr['diqu'] = $this->_getDiQuType(trim($moviceDiQuInfo));

        //类型
        $moviceTypeInfo = $this->_getPregMatch("/<span class=\"s5\">(.*?)<\/span>/si",$moviceHtml);
        $moviceTypeInfo = strip_tags($moviceTypeInfo[1]);
        $moviceTypeInfo = str_replace("类型： ","",$moviceTypeInfo);
        $moviceTypeInfo = array_filter(explode("\t",$moviceTypeInfo));
        $typeArr = array();
        foreach($moviceTypeInfo as $typeVal) {
            $typeVal = trim($typeVal);
            if (empty($typeVal)) {
                continue;
            }
            $typeArr[] = $typeVal;
        }
        $resultArr['type'] = $this->_getMoviceType($typeArr[0]);

        //年代
        $moviceNianFenInfo = $this->_getPregMatch("/<span class=\"s5\">年代：(.*?)<\/span>/si",$moviceHtml);
        $moviceNianFenInfo = strip_tags($moviceNianFenInfo[1]);
        $moviceNianFenInfo = str_replace("年代：","",$moviceNianFenInfo);
        $resultArr['nianfen'] = trim($moviceNianFenInfo);

        //简介
        $moviceJieShaoInfo = $this->_getPregMatch("/<p class=\"p6\">(.*?)<\/p>/si",$moviceHtml);
        $moviceJieShaoInfo = strip_tags($moviceJieShaoInfo[1]);
        $resultArr['jieshao'] = trim($moviceJieShaoInfo);

        if (empty($resultArr['shichang']) || (!empty($resultArr['shichang']) && ($resultArr['shichang'] > 45))) {
            $resultArr['exist_watch'] = 1;
        } else {
            $resultArr['exist_watch'] = 0;
        }

        $watchArr = array();
        $watchArr['link'] = $htmlUrl;
        $watchArr['player'] = 13;
        $watchArr['qingxi'] = 3;
        $watchArr['shoufei'] = 1;

        //读取信息是否存在
        $info = $this->_getMoviceInfoByIdAndType($resultArr['webId'], $resultArr['webType']);
        if (!empty($info)) { //信息已存在
            if ($info['del'] == 1) {
                //获取电影被合并信息
                $delInfo = $this->_getDelMoviceInfoById($info['id']);
                if (!empty($delInfo)) {
                    //更新观看链接
                    $this->_updateWatchLinkInfo($delInfo['currentInfoId'],$watchArr);
                }
            } else {
                //获取差异信息
                $deInfo = $this->_getComDetailInfo($info,$resultArr);
                //更新电影信息
                $this->_updateDetailInfo($info['id'],$deInfo);
                //更新观看链接
                $this->_updateWatchLinkInfo($info['id'],$watchArr);
            }
        } else {
            $lastId = $this->_insertMoviceDetailInfo($resultArr);
            if (!empty($lastId) && !is_array($lastId)) {
                $this->_addWaterToImg($resultArr['image']);
            }
            if (!empty($lastId) && !is_array($lastId)) {
                $watchArr['infoId'] = $lastId;
                $watchId = $this->_insertWatchLinkInfo($watchArr);
            }
        }
    }

    /** 抓取风行网电影信息
     * @param $htmlUrl
     * @param $imgUrl
     */
    private function _funshion($url, $urlType)
    {
        if (empty($url) || empty($urlType)) {
            return false;
        }
        $totalInfoHtml = $this->_getCurlInfo($url);

        //观看链接
        $titleInfo = $this->_getPregMatchAll("/\/subject\/[0-9]+\//i", $totalInfoHtml, PREG_PATTERN_ORDER);
        if (!empty($titleInfo[0]) && is_array($titleInfo[0])) {
            //视频链接链接去重
            $titleRealInfo = array_unique($titleInfo[0]);

            $baseUrl = "http://www.funshion.com";
            foreach ($titleRealInfo as $titleVal) {
                if (empty($titleVal)) {
                    continue;
                }
                $titleVal = $baseUrl . $titleVal;
                //$titleVal = "http://tv.sohu.com/20130428/n374389684.shtml";
                $resultArr = array();
                $resultArr['webType'] = $this->_webConfigInfo['funshion']['type'];
                $moviceIdArr = $this->_getPregMatchAll("/[0-9]+/i", $titleVal);
                $resultArr['webId'] = (empty($moviceIdArr[0][1])) ? $moviceIdArr[0][0] : $moviceIdArr[0][1];

                $moviceHtml = $this->_getCurlInfo($titleVal);

                //电影名称
                $moviceNameInfo = $this->_getPregMatch("/<title>(.*?)<\/title>/i",$moviceHtml);
                $moviceNameInfo = explode("-",$moviceNameInfo[1]);
                $resultArr['name'] = trim($moviceNameInfo[0]);
                $firstLetter = $this->getFirstLetter($resultArr['name']);
                if ($firstLetter != "*") {
                    $resultArr['firstLetter'] = $firstLetter;
                }
                //拼音
                $pinyin = $this->getPinyin($resultArr['name'],2);
                if (!empty($pinyin)) {
                    $resultArr['pinyin'] = $pinyin;
                }

                //电影导演
                $moviceDaoyanInfo = $this->_getPregMatch("/<h4 class=\"inline\">导演：<\/h4><a(.*?)>(.*?)<\/a>/si",$moviceHtml);
                if (!empty($moviceDaoyanInfo[2])) {
                    $resultArr['daoyan'] = trim($moviceDaoyanInfo[2]);
                } else {
                    $resultArr['daoyan'] = "暂无";
                }

                //电影主演
                $moviceZhuyanInfo = $this->_getPregMatch("/<h4 class=\"inline\">演员：<\/h4>(.*?)<\/div>/si",$moviceHtml);
                $moviceZhuyanInfo = strip_tags($moviceZhuyanInfo[1]);
                $moviceZhuyanInfo = str_replace("更多&gt;","",$moviceZhuyanInfo);
                $moviceZhuyanInfo = str_replace("\t","",$moviceZhuyanInfo);
                $moviceZhuyanInfo = str_replace("\n","",$moviceZhuyanInfo);
                $moviceZhuyanInfoArr = explode(" ",$moviceZhuyanInfo);
                $zhuYanArr = array();
                foreach($moviceZhuyanInfoArr as $zhuyanVal) {
                    $zhuyanVal = trim($zhuyanVal);
                    if (empty($zhuyanVal)) {
                        continue;
                    }
                    $zhuYanArr[] = $zhuyanVal;
                }
                if (!empty($zhuYanArr)) {
                    $resultArr['zhuyan'] = implode("、",$zhuYanArr);
                } else {
                    $resultArr['zhuyan'] = "暂无";
                }

                //电影类型
                $moviceTypeInfo = $this->_getPregMatch("/<h4 class=\"inline\">类型：<\/h4><a(.*?)>(.*?)<\/a>/si",$moviceHtml);
                $resultArr['type'] = $this->_getMoviceType(trim($moviceTypeInfo[2]));

                //电影年份+地区
                $moviceNianFenInfo = $this->_getPregMatch("/<h4 class=\"inline\">首映：<\/h4><span(.*?)>(.*?)<\/span>/si",$moviceHtml);
                if (!empty($moviceNianFenInfo[2])) {
                    $nianFenArr = explode(" ",$moviceNianFenInfo[2]);
                    $resultArr['nianfen'] = substr($nianFenArr[0],0,4);
                    $time1 = str_replace("年","-",$nianFenArr[0]);//上映时间
                    $time1 = str_replace("月","-",$time1);//上映时间
                    $time1 = str_replace("日","",$time1);//上映时间
                    $resultArr['time1'] = strtotime($time1);//上映时间
                    $resultArr['diqu'] = $this->_getDiQuType($nianFenArr[1]);
                }

                //电影介绍
                $moviceJieShaoInfo = $this->_getPregMatch("/<div id=\"behind_content\" class=\"all\">(.*?)<\/div>/si",$moviceHtml);
                $moviceJieShaoInfo[1] = str_replace("\n","",trim($moviceJieShaoInfo[1]));
                $resultArr['jieshao'] = str_replace("<br />","",$moviceJieShaoInfo[1]);

                //电影图片
                $moviceImgInfo = $this->_getPregMatch("/<div class=\"img-primary\">(.*?)<\/div>/si",$moviceHtml);
                $moviceImgInfo = $this->_getPregMatchAll("/http:(.*?)\.jpg/i",$moviceImgInfo[1],PREG_PATTERN_ORDER);
                if (!empty($moviceImgInfo[0][0])) {
                    $imgUrl = $moviceImgInfo[0][0];
                    $imgArr = explode(".", $imgUrl);
                    $imagesName = md5($resultArr['name'] . "_" . $resultArr['webType'] . "_" . $resultArr['webId']) . "." . $imgArr[count($imgArr) - 1];
                    $resultArr['image'] = $this->_downLoadImg($imagesName, $imgUrl); //下载图片并保存,并返回图片地址
                } else { //默认图片地址 todo
                    $resultArr['image'] = $this->get_config_value("dy_common_img");
                }
                $resultArr['jieshao'] = str_replace("<br />","",$moviceJieShaoInfo[1]);

                //查询是否有观看链接的地址
                $apiUrl = "http://api.funshion.com/ajax/get_media_data/media/{$resultArr['webId']}";
                $apiInfo = $this->_getCurlInfo($apiUrl,true);
                if (!empty($apiInfo['data']['webplay'])) {//有观看链接
                    $resultArr['exist_watch'] = 1;
                } else {
                    $resultArr['exist_watch'] = 0;
                }

                $watchLink = "http://www.funshion.com/subject/play/{$resultArr['webId']}/1";
                $watchArr = array();
                $watchArr['link'] = $watchLink;
                $watchArr['player'] = 9;
                $watchArr['qingxi'] = 3;
                $watchArr['shoufei'] = 1;

                //读取信息是否存在
                $info = $this->_getMoviceInfoByIdAndType($resultArr['webId'], $resultArr['webType']);
                if (!empty($info)) { //信息已存在
                    if ($info['del'] == 1) {
                        //获取电影被合并信息
                        $delInfo = $this->_getDelMoviceInfoById($info['id']);
                        if (!empty($delInfo)) {
                            //更新观看链接
                            $this->_updateWatchLinkInfo($delInfo['currentInfoId'],$watchArr);
                        }
                    } else {
                        //获取差异信息
                        $deInfo = $this->_getComDetailInfo($info,$resultArr);
                        //更新电影信息
                        $this->_updateDetailInfo($info['id'],$deInfo);
                        //更新观看链接
                        $this->_updateWatchLinkInfo($info['id'],$watchArr);
                    }
                } else {
                    $lastId = $this->_insertMoviceDetailInfo($resultArr);
                    if (!empty($lastId) && !is_array($lastId)) {
                        $this->_addWaterToImg($resultArr['image']);
                    }
                    if (!empty($lastId) && !is_array($lastId) && !empty($resultArr['exist_watch'])) {
                        $watchArr['infoId'] = $lastId;
                        $watchId = $this->_insertWatchLinkInfo($watchArr);
                    }
                }
            }
        }
    }

    /** 抓取优酷网电影信息
     * @param $htmlUrl
     * @param $imgUrl
     */
    private function _youku($url, $urlType)
    {
        if (empty($url) || empty($urlType)) {
            return false;
        }
        $totalInfoHtml = $this->_getCurlInfo($url);
        //观看链接
        $titleInfo = $this->_getPregMatchAll("/http:\/\/v\.youku\.com\/v_show\/id_[0-9a-zA-Z]+\.html/i", $totalInfoHtml, PREG_PATTERN_ORDER);

        if (!empty($titleInfo[0]) && is_array($titleInfo[0])) {
            //视频链接链接去重
            $titleRealInfo = array_unique($titleInfo[0]);

            foreach ($titleRealInfo as $titleVal) {
                if (empty($titleVal)) {
                    continue;
                }
                //$titleVal = "http://v.youku.com/v_show/id_XNTU4MjIyMjk2.html";测试，待删除
                $resultArr = array();
                $resultArr['webType'] = $this->_webConfigInfo['youku']['type'];
                $moviceIdArr = explode("/",$titleVal);
                $moviceIdArr = explode(".",$moviceIdArr[count($moviceIdArr) - 1]);
                $moviceIdArr[0] = str_replace("id_","",$moviceIdArr[0]);
                $resultArr['webId'] = $this->_get_id($moviceIdArr[0]);

                $moviceHtml = $this->_getCurlInfo($titleVal);

                //获取影片详情链接
                $moviceInfo = $this->_getPregMatch("/<li class=\"show_intro\">(.*?)<\/li>/si",$moviceHtml);
                if (empty($moviceInfo[1])) {
                    continue;
                }
                $detailUrlInfo = $this->_getPregMatch("/href=\"(.*?)\"/i", $moviceInfo[1]);

                //影片详细信息
                $moviceDetailHtml = $this->_getCurlInfo($detailUrlInfo[1]);

                //电影名称
                $moviceNameInfo = $this->_getPregMatch("/<span class=\"name\">(.*?)<\/span>/si",$moviceDetailHtml);
                $resultArr['name']  = $moviceNameInfo[1];
                $firstLetter = $this->getFirstLetter($resultArr['name']);
                if ($firstLetter != "*") {
                    $resultArr['firstLetter'] = $firstLetter;
                }
                //拼音
                $pinyin = $this->getPinyin($resultArr['name'],2);
                if (!empty($pinyin)) {
                    $resultArr['pinyin'] = $pinyin;
                }

                //电影导演
                $moviceDaoyanInfo = $this->_getPregMatch("/<label>导演:<\/label>(.*?)<\/a>/si",$moviceDetailHtml);
                if (!empty($moviceDaoyanInfo[1])) {
                    $resultArr['daoyan'] = trim(strip_tags($moviceDaoyanInfo[1]));
                } else {
                    $resultArr['daoyan'] = "暂无";
                }

                //电影时长
                $moviceShiChangInfo = $this->_getPregMatch("/<label>时长:<\/label>(.*?)<\/span>/si",$moviceDetailHtml);
                if (!empty($moviceShiChangInfo[1])) {
                    $resultArr['shichang'] = trim(strip_tags($moviceShiChangInfo[1]));
                    $resultArr['shichang'] = str_replace("分钟","",$resultArr['shichang']);
                }

                //电影主演
                $moviceZhuyanInfo = $this->_getPregMatch("/<label>主演:<\/label>(.*?)<\/span>/si",$moviceDetailHtml);
                $moviceZhuyanInfo = strip_tags($moviceZhuyanInfo[1]);
                $moviceZhuyanInfo = str_replace("更多&gt;","",$moviceZhuyanInfo);
                $moviceZhuyanInfo = str_replace("\t","",$moviceZhuyanInfo);
                $moviceZhuyanInfo = str_replace("\n","",$moviceZhuyanInfo);
                $resultArr['zhuyan'] = str_replace(" / ","、",$moviceZhuyanInfo);

                //电影类型
                $moviceTypeInfo = $this->_getPregMatch("/<label>类型:<\/label>(.*?)<\/a>/si",$moviceDetailHtml);
                $resultArr['type'] = $this->_getMoviceType(trim(strip_tags($moviceTypeInfo[1])));

                //电影地区
                $moviceDiQuInfo = $this->_getPregMatch("/<label>地区:<\/label>(.*?)<\/a>/si",$moviceDetailHtml);
                $resultArr['diqu'] = $this->_getDiQuType(trim(strip_tags($moviceDiQuInfo[1])));

                //电影上映时间
                $moviceTime1Info = $this->_getPregMatch("/<label>上映:<\/label>(.*?)<\/span>/si",$moviceDetailHtml);
                if  (!empty($moviceTime1Info[1])) {
                    $resultArr['time1'] = strtotime(trim(strip_tags($moviceTime1Info[1])));
                    //电影年份
                    $resultArr['nianfen'] = date("Y",$resultArr['time1']);
                }

                //电影介绍
                $moviceJieShaoInfo = $this->_getPregMatch("/<span class=\"short\" style=\"display:block;\">(.*?)<\/span>/si",$moviceDetailHtml);
                $resultArr['jieshao'] = trim(strip_tags($moviceJieShaoInfo[1]));

                //电影图片
                $moviceImgInfo = $this->_getPregMatch("/<li class=\"thumb\">(.*?)<\/li>/si",$moviceDetailHtml);
                if (!empty($moviceImgInfo[1])) {
                    $imgUrlInfo = $this->_getPregMatch("/src='(.*?)'/i", $moviceImgInfo[1]);
                    $imgUrl = $imgUrlInfo[1];
                    $imagesName = md5($resultArr['name'] . "_" . $resultArr['webType'] . "_" . $resultArr['webId']) . ".jpeg";
                    $resultArr['image'] = $this->_downLoadImg($imagesName, $imgUrl); //下载图片并保存,并返回图片地址
                } else { //默认图片地址 todo
                    $resultArr['image'] = $this->get_config_value("dy_common_img");
                }

                //有观看链接的地址
                $resultArr['exist_watch'] = 1;

                //观看链接信息
                if (strpos($moviceHtml,'<div id="feeInfo">') !== false) {//收费
                    $shoufeiType = 2;
                } else {//免费
                    $shoufeiType = 1;
                }
                $watchLink = $titleVal;
                $watchArr = array();
                $watchArr['link'] = $watchLink;
                $watchArr['player'] = 5;
                $watchArr['qingxi'] = 3;
                $watchArr['shoufei'] = $shoufeiType;
                if (strpos($resultArr['name'],"预告") !== false) {
                    $watchArr['beizhu'] = "预告";
                    $watchArr['shoufei'] = 3;
                } elseif (strpos($resultArr['name'],"特辑") !== false) {
                    $watchArr['beizhu'] = "特辑";
                    $watchArr['shoufei'] = 4;
                } elseif (strpos($resultArr['name'],"记录片") !== false) {
                    $watchArr['beizhu'] = "记录片";
                    $watchArr['shoufei'] = 5;
                } elseif (strpos($resultArr['name'],"删减版") !== false) {
                    $watchArr['beizhu'] = "删减版";
                } else {
                    if ($shoufeiType == 1) {
                        $watchArr['beizhu'] = "高清免费";
                    } else {
                        $watchArr['beizhu'] = "高清收费";
                    }
                }

                //读取信息是否存在
                $info = $this->_getMoviceInfoByIdAndType($resultArr['webId'], $resultArr['webType']);
                if (!empty($info)) { //信息已存在
                    if ($info['del'] == 1) {
                        //获取电影被合并信息
                        $delInfo = $this->_getDelMoviceInfoById($info['id']);
                        if (!empty($delInfo)) {
                            //更新观看链接
                            $this->_updateWatchLinkInfo($delInfo['currentInfoId'],$watchArr);
                        }
                    } else {
                        //获取差异信息
                        $deInfo = $this->_getComDetailInfo($info,$resultArr);
                        //更新电影信息
                        $this->_updateDetailInfo($info['id'],$deInfo);
                        //更新观看链接
                        $this->_updateWatchLinkInfo($info['id'],$watchArr);
                    }
                } else {
                    $lastId = $this->_insertMoviceDetailInfo($resultArr);
                    if (!empty($lastId) && !is_array($lastId)) {
                        $this->_addWaterToImg($resultArr['image']);
                    }
                    if (!empty($lastId) && !is_array($lastId) && !empty($resultArr['exist_watch'])) {
                        $watchArr['infoId'] = $lastId;
                        $watchId = $this->_insertWatchLinkInfo($watchArr);
                    }
                }
            }
        }
    }

    /** 抓取优酷网电影信息
     * @param $htmlUrl
     * @param $imgUrl
     */
    private function _kankan($url, $urlType)
    {
        if (empty($url) || empty($urlType)) {
            return false;
        }
        //$url = "http://movie.kankan.com/type,status/movie,zp/page2/";
        $totalInfoHtml = $this->_getCurlInfo($url);
        //观看链接
        $titleInfo = $this->_getPregMatchAll("/(http:\/\/vod\.kankan\.com\/v\/[0-9]+\/[0-9]+\.shtml)|(http:\/\/vod\.kankan\.com\/vdetail\/[0-9]+\.shtml)/i", $totalInfoHtml, PREG_PATTERN_ORDER);

        if (!empty($titleInfo[0]) && is_array($titleInfo[0])) {
            //视频链接链接去重
            $titleRealInfo = array_unique($titleInfo[0]);
            $titleRealInfo = array_values($titleRealInfo);
            //img
            $imgInfo = $this->_getPregMatchAll("/http:\/\/images\.movie\.xunlei\.com\/[0-9]+x[0-9]+\/[0-9]+\/[0-9a-zA-Z]+\.jpg/i", $totalInfoHtml, PREG_PATTERN_ORDER);
            $imgInfo = array_unique($imgInfo[0]);
            $imgInfo = array_values($imgInfo);
            if (count($titleRealInfo) != count($imgInfo)) {
                file_put_contents($this->_filePath,"kankan:{$url} " . date("Y-m-d H:i:s") . "\n");
                var_dump($url);
                return false;
            }
            foreach ($titleRealInfo as $key => $titleVal) {
                if (empty($titleVal)) {
                    continue;
                }
                //$titleVal = "http://vod.kankan.com/v/70/70517.shtml";//test
                $resultArr = array();
                $resultArr['webType'] = $this->_webConfigInfo['kankan']['type'];
                $moviceIdArr = explode("/",$titleVal);
                $moviceIdArr = explode(".",$moviceIdArr[count($moviceIdArr) - 1]);
                $resultArr['webId'] = $moviceIdArr[0];

                if (!empty($_SERVER['XIONGJIEWU_TEST'])) {//测试，待删除
                    $titleVal = "http://vod.kankan.com/v/44/44275.shtml";
                    $imgInfo[$key] = "http://images.movie.xunlei.com/120x168/512/a29746d6a8f645accc97e5bdc3d7c784.jpg";
                }

                $moviceHtml = mb_convert_encoding($this->_getCurlInfo($titleVal),"UTF-8","GBK");
                $posStr = '<a href="http://vip.kankan.com/" title="付费频道" target="_blank">付费频道</a>';
                if (strpos($moviceHtml,$posStr) !== false) {//收费
                    $this->_kankanShouFei($moviceHtml,$imgInfo[$key],$resultArr,$titleVal);
                } else {//免费
                    $this->_kankanMianFei($moviceHtml,$imgInfo[$key],$resultArr,$titleVal);
                }

            }
        }
    }

    /** 迅雷看看免费
     * @param $moviceHtml
     * @param $imgUrl
     * @param $resultArr
     * @param $titleVal
     * @return bool
     */
    private function _kankanMianFei($moviceHtml,$imgUrl,$resultArr,$titleVal) {
        if (empty($moviceHtml) || empty($imgUrl)) {
            return false;
        }
        //电影名称
        $moviceNameInfo = $this->_getPregMatch("/<title>(.*?)<\/title>/si",$moviceHtml);
        $nameArr = explode("-",$moviceNameInfo[1]);
        $resultArr['name'] = trim($nameArr[0]);
        $firstLetter = $this->getFirstLetter($resultArr['name']);
        if ($firstLetter != "*") {
            $resultArr['firstLetter'] = $firstLetter;
        }
        //拼音
        $pinyin = $this->getPinyin($resultArr['name'],2);
        if (!empty($pinyin)) {
            $resultArr['pinyin'] = $pinyin;
        }

        //电影图片
        $imagesName = md5($resultArr['name'] . "_" . $resultArr['webType'] . "_" . $resultArr['webId']) . ".jpeg";
        $resultArr['image'] = $this->_downLoadImg($imagesName, $imgUrl); //下载图片并保存,并返回图片地址

        //电影导演
        $moviceDaoyanInfo = $this->_getPregMatch("/导演:(.*?)<\/a>/si",$moviceHtml);

        if (!empty($moviceDaoyanInfo[1])) {
            $resultArr['daoyan'] = trim(strip_tags($moviceDaoyanInfo[1]));
        } else {
            $resultArr['daoyan'] = "暂无";
        }

        //电影主演
        $moviceZhuyanInfo = $this->_getPregMatch("/主演:(.*?)<\/li>/si",$moviceHtml);
        $moviceZhuyanInfo = explode("</a>",$moviceZhuyanInfo[1]);
        $zhuYanArr = array();
        foreach($moviceZhuyanInfo as $zhuyan) {
            $zhuYanArr[] = strip_tags($zhuyan);
        }
        $resultArr['zhuyan'] = rtrim(implode("、",$zhuYanArr),"、");

        //电影类型
        $moviceTypeInfo = $this->_getPregMatch("/类型:(.*?)<\/li>/si",$moviceHtml);
        $resultArr['type'] = $this->_getMoviceType(trim(strip_tags($moviceTypeInfo[1])));

        //电影地区
        $moviceDiQuInfo = $this->_getPregMatch("/地区:(.*?)<\/a>/si",$moviceHtml);
        $resultArr['diqu'] = $this->_getDiQuType(trim(strip_tags($moviceDiQuInfo[1])));

        //电影shichang
        $moviceShiChangInfo = $this->_getPregMatch("/片长:(.*?)<\/a>/si",$moviceHtml);
        if (!empty($moviceShiChangInfo[1])) {
            $moviceShiChangInfo[1] = str_replace("分钟","",$moviceShiChangInfo[1]);
            $resultArr['shichang'] = trim($moviceShiChangInfo[1]);
        }

        //电影nianfen
        $moviceNianFenInfo = $this->_getPregMatch("/上映:(.*?)<\/a>/si",$moviceHtml);
        if (!empty($moviceNianFenInfo[1])) {
            $resultArr['nianfen'] = trim(strip_tags($moviceNianFenInfo[1]));
        }

        //电影jieshao
        $moviceJieShaoInfo = $this->_getPregMatch("/<p class=\"movieintro\" id=\"movie_info_intro_l\" style=\"display:none\">(.*?)<\/p>/si",$moviceHtml);
        $resultArr['jieshao'] = str_replace("...收起详细介绍","",trim(strip_tags($moviceJieShaoInfo[1])));

        //有观看链接的地址
        $resultArr['exist_watch'] = 1;

        //观看链接信息
        $watchLink = $titleVal;
        $watchArr = array();
        $watchArr['link'] = $watchLink;
        $watchArr['player'] = 3;
        $watchArr['qingxi'] = 3;
        $watchArr['shoufei'] = 1;
        if (strpos($resultArr['name'],"预告") !== false) {
            $watchArr['beizhu'] = "预告";
            $watchArr['shoufei'] = 3;
        } elseif (strpos($resultArr['name'],"特辑") !== false) {
            $watchArr['beizhu'] = "特辑";
            $watchArr['shoufei'] = 4;
        } elseif (strpos($resultArr['name'],"记录片") !== false) {
            $watchArr['beizhu'] = "记录片";
            $watchArr['shoufei'] = 5;
        } elseif (strpos($resultArr['name'],"删减版") !== false) {
            $watchArr['beizhu'] = "删减版";
        } else {
            $watchLinkInfo['beizhu'] = "高清免费";
        }

        //读取信息是否存在
        $info = $this->_getMoviceInfoByIdAndType($resultArr['webId'], $resultArr['webType']);
        if (!empty($info)) { //信息已存在
            //获取差异信息
            $deInfo = $this->_getComDetailInfo($info,$resultArr);
            //更新电影信息
            $this->_updateDetailInfo($info['id'],$deInfo);
            //更新观看链接
            $this->_updateWatchLinkInfo($info['id'],$watchArr);
        } else {
            $lastId = $this->_insertMoviceDetailInfo($resultArr);
            if (!empty($lastId) && !is_array($lastId)) {
                $this->_addWaterToImg($resultArr['image']);
            }
            if (!empty($lastId) && !is_array($lastId) && !empty($resultArr['exist_watch'])) {
                $watchArr['infoId'] = $lastId;
                $watchId = $this->_insertWatchLinkInfo($watchArr);
            }
        }
    }

    /** 迅雷看看收费
     * @param $moviceHtml
     * @param $imgUrl
     * @param $resultArr
     * @param $titleVal
     * @return bool
     */
    private function _kankanShouFei($moviceHtml,$imgUrl,$resultArr,$titleVal) {
        if (empty($moviceHtml) || empty($imgUrl)) {
            return false;
        }

        //电影名称
        $moviceNameInfo = $this->_getPregMatch("/<title>(.*?)<\/title>/si",$moviceHtml);
        $nameArr = explode("-",$moviceNameInfo[1]);
        $resultArr['name'] = trim($nameArr[0]);
        $firstLetter = $this->getFirstLetter($resultArr['name']);
        if ($firstLetter != "*") {
            $resultArr['firstLetter'] = $firstLetter;
        }
        //拼音
        $pinyin = $this->getPinyin($resultArr['name'],2);
        if (!empty($pinyin)) {
            $resultArr['pinyin'] = $pinyin;
        }

        //电影图片
        $imagesName = md5($resultArr['name'] . "_" . $resultArr['webType'] . "_" . $resultArr['webId']) . ".jpeg";
        $resultArr['image'] = $this->_downLoadImg($imagesName, $imgUrl); //下载图片并保存,并返回图片地址

        //电影导演
        $moviceDaoyanInfo = $this->_getPregMatch("/<p>导 演：(.*?)<\/p>/si",$moviceHtml);

        if (!empty($moviceDaoyanInfo[1])) {
            $resultArr['daoyan'] = trim(strip_tags($moviceDaoyanInfo[1]));
        } else {
            $resultArr['daoyan'] = "暂无";
        }

        //电影主演
        $moviceZhuyanInfo = $this->_getPregMatch("/<p>主 演：(.*?)<\/p>/si",$moviceHtml);
        $moviceZhuyanInfo = explode("</a>",$moviceZhuyanInfo[1]);
        $zhuYanArr = array();
        foreach($moviceZhuyanInfo as $zhuyan) {
            $zhuYanArr[] = strip_tags($zhuyan);
        }
        $resultArr['zhuyan'] = rtrim(implode("、",$zhuYanArr),"、");

        //电影类型
        $moviceTypeInfo = $this->_getPregMatch("/<p>类 型：(.*?)<\/p>/si",$moviceHtml);
        $resultArr['type'] = $this->_getMoviceType(trim(strip_tags($moviceTypeInfo[1])));

        //电影地区
        $moviceDiQuInfo = $this->_getPregMatch("/<p>地 区：(.*?)<\/p>/si",$moviceHtml);
        $resultArr['diqu'] = $this->_getDiQuType(trim(strip_tags($moviceDiQuInfo[1])));

        //电影shichang
        $moviceShiChangInfo = $this->_getPregMatch("/<p>片 长：(.*?)<\/p>/si",$moviceHtml);
        if (!empty($moviceShiChangInfo[1])) {
            $moviceShiChangInfo[1] = str_replace("分钟","",$moviceShiChangInfo[1]);
            $resultArr['shichang'] = trim($moviceShiChangInfo[1]);
        }

        //电影nianfen
        $moviceNianFenInfo = $this->_getPregMatch("/<p>年 份：(.*?)<\/p>/si",$moviceHtml);
        if (!empty($moviceNianFenInfo[1])) {
            $resultArr['nianfen'] = trim(strip_tags($moviceNianFenInfo[1]));
        }

        //电影jieshao
        $moviceJieShaoInfo = $this->_getPregMatch("/<span id=\"intro_long\" style=\"display:none;\">(.*?)<\/span>/si",$moviceHtml);
        if (empty($moviceJieShaoInfo[1])) {
            $moviceJieShaoInfo = $this->_getPregMatch("/<span id=\"intro_short\">(.*?)<\/span>/si",$moviceHtml);
        }
        $resultArr['jieshao'] = trim(strip_tags($moviceJieShaoInfo[1]));

        //有观看链接的地址
        $resultArr['exist_watch'] = 1;

        //观看链接信息
        $watchLink = $titleVal;
        $watchArr = array();
        $watchArr['link'] = $watchLink;
        $watchArr['player'] = 3;
        $watchArr['qingxi'] = 3;
        $watchArr['shoufei'] = 2;
        if (strpos($resultArr['name'],"预告") !== false) {
            $watchArr['beizhu'] = "预告";
            $watchArr['shoufei'] = 3;
        } elseif (strpos($resultArr['name'],"特辑") !== false) {
            $watchArr['beizhu'] = "特辑";
            $watchArr['shoufei'] = 4;
        } elseif (strpos($resultArr['name'],"记录片") !== false) {
            $watchArr['beizhu'] = "记录片";
            $watchArr['shoufei'] = 5;
        } elseif (strpos($resultArr['name'],"删减版") !== false) {
            $watchArr['beizhu'] = "删减版";
        } else {
            $watchLinkInfo['beizhu'] = "高清收费";
        }

        //读取信息是否存在
        $info = $this->_getMoviceInfoByIdAndType($resultArr['webId'], $resultArr['webType']);
        if (!empty($info)) { //信息已存在
            if ($info['del'] == 1) {
                //获取电影被合并信息
                $delInfo = $this->_getDelMoviceInfoById($info['id']);
                if (!empty($delInfo)) {
                    //更新观看链接
                    $this->_updateWatchLinkInfo($delInfo['currentInfoId'],$watchArr);
                }
            } else {
                //获取差异信息
                $deInfo = $this->_getComDetailInfo($info,$resultArr);
                //更新电影信息
                $this->_updateDetailInfo($info['id'],$deInfo);
                //更新观看链接
                $this->_updateWatchLinkInfo($info['id'],$watchArr);
            }
        } else {
            $lastId = $this->_insertMoviceDetailInfo($resultArr);
            if (!empty($lastId) && !is_array($lastId)) {
                $this->_addWaterToImg($resultArr['image']);
            }
            if (!empty($lastId) && !is_array($lastId) && !empty($resultArr['exist_watch'])) {
                $watchArr['infoId'] = $lastId;
                $watchId = $this->_insertWatchLinkInfo($watchArr);
            }
        }
    }
    /** 抓取爱奇艺网电影信息
     * @param $htmlUrl
     * @param $imgUrl
     */
    private function _iqiyi($url,$urlType)
    {
        if (empty($url) || empty($urlType)) {
            return false;
        }

        $totalInfoHtml = $this->_getCurlInfo($url);
        //观看链接
        $titleInfo = $this->_getPregMatchAll("/(http:\/\/www\.iqiyi\.com\/dianying\/[0-9]+\/[0-9a-zA-Z]+\.html)|(http:\/\/www\.iqiyi\.com\/dianying\/[0-9]+\/n[0-9]+\.html)/i", $totalInfoHtml, PREG_PATTERN_ORDER);

        if (!empty($titleInfo[0]) && is_array($titleInfo[0])) {
            //视频链接链接去重
            $titleRealInfo = array_unique($titleInfo[0]);

            foreach ($titleRealInfo as $titleVal) {
                if (empty($titleVal)) {
                    continue;
                }
//                $titleVal = "http://www.iqiyi.com/dianying/20130516/bf806ab505f3e6e8.html";
                //读取详细信息
                $moviceHtml = $this->_getCurlInfo($titleVal);

                //获取相信信息url
                $detailUrlInfo = $this->_getPregMatch("/<div class=\"clearfix\" data-seq=\"1\" data-elem=\"tabbody\">(.*?)<\/a>/si",$moviceHtml);
                if (empty($detailUrlInfo[1])) {
                    continue;
                }
                $detailUrlArr = $this->_getPregMatch("/href=\"(.*?)\"/si",$detailUrlInfo[1],PREG_PATTERN_ORDER);
                if (empty($detailUrlArr[1])) {
                    continue;
                }
                $detailUrl = $detailUrlArr[1];
                $resultArr = array();
                $resultArr['webType'] = $this->_webConfigInfo['iqiyi']['type'];
                $moviceIdArr = explode("/",$detailUrl);
                $resultArr['webId'] = $moviceIdArr[count($moviceIdArr) - 1];

                //电影真正的详细页
                $moviceDetailHtml = $this->_getCurlInfo($detailUrl);

                //电影名称
                $moviceNameInfo = $this->_getPregMatch("/<h1 itemprop=\"name\">(.*?)<\/h1>/si",$moviceDetailHtml);
                $resultArr['name'] = trim($moviceNameInfo[1]);
                $firstLetter = $this->getFirstLetter($resultArr['name']);
                if ($firstLetter != "*") {
                    $resultArr['firstLetter'] = $firstLetter;
                }
                //拼音
                $pinyin = $this->getPinyin($resultArr['name'],2);
                if (!empty($pinyin)) {
                    $resultArr['pinyin'] = $pinyin;
                }

                //电影导演
                $moviceDaoYanInfo = $this->_getPregMatch("/导演：(.*?)<\/a>/si",$moviceDetailHtml);
                if (!empty($moviceDaoYanInfo[1])) {
                    $resultArr['daoyan'] = trim(strip_tags($moviceDaoYanInfo[1]));
                } else {
                    $moviceDaoYanInfo = $this->_getPregMatch("/<span class=\"p_three\">导演：(.*?)<\/span>/si",$moviceHtml);
                    $resultArr['daoyan'] = trim(strip_tags($moviceDaoYanInfo[1]));
                }

                //电影主演
                $moviceZhuYanInfo = $this->_getPregMatchAll("/<a itemprop=\"name\"(.*?)>(.*?)<\/a>/i", $moviceDetailHtml, PREG_PATTERN_ORDER);
                if (!empty($moviceZhuYanInfo[2])) {
                    $resultArr['zhuyan'] = implode("、",$moviceZhuYanInfo[2]);
                } else {
                    $moviceZhuYanInfo = $this->_getPregMatch("/<p>主演：(.*?)<\/p>/si", $moviceHtml);
                    $moviceZhuYanStr = trim(strip_tags($moviceZhuYanInfo[1]));
                    $moviceZhuYanArr = explode("|",$moviceZhuYanStr);
                    $zhuYanRes = array();
                    foreach($moviceZhuYanArr as $zhuyan) {
                        if (empty($zhuyan)) {
                            continue;
                        }
                        $zhuYanRes[] = trim($zhuyan);
                    }
                    $resultArr['zhuyan'] = implode("、",$zhuYanRes);
                }

                //电影地区
                $moviceDiQuInfo = $this->_getPregMatch("/国家和地区：(.*?)<\/a>/si",$moviceDetailHtml);
                if (!empty($moviceDiQuInfo[1])) {
                    $resultArr['diqu'] = $this->_getDiQuType(trim(strip_tags($moviceDiQuInfo[1])));
                }

                //电影类型
                $moviceTypeInfo = $this->_getPregMatch("/<span>类型：(.*?)<\/p>/si",$moviceHtml);
                $resultArr['type'] = $this->_getMoviceType(trim(strip_tags($moviceTypeInfo[1])));

                //地区
                if (empty($resultArr['diqu'])) {
                    $resultArr['diqu'] = $this->_getDiQuType(trim(strip_tags($moviceTypeInfo[1])));
                }

                //电影年份
                $moviceNianFenInfo = $this->_getPregMatch("/上映时间：<span>(.*?)<\/span>/si",$moviceDetailHtml);
                if (!empty($moviceNianFenInfo[1])) {
                    $moviceNianFenInfo[1] = trim(strip_tags($moviceNianFenInfo[1]));
                    $moviceNianFenInfo[1] = str_replace("年","-",$moviceNianFenInfo[1]);
                    $moviceNianFenInfo[1] = str_replace("月","-",$moviceNianFenInfo[1]);
                    $moviceNianFenInfo[1] = str_replace("日","",$moviceNianFenInfo[1]);
                    $resultArr['nianfen'] = substr($moviceNianFenInfo[1],0,4);
                    $resultArr['time1'] = strtotime($moviceNianFenInfo[1]);
                } else {
                    $moviceNianFenInfo = $this->_getPregMatch("/<span class=\"p_three\">上映：(.*?)<\/span>/si",$moviceHtml);
                    if (!empty($moviceNianFenInfo[1])) {
                        $resultArr['nianfen'] = str_replace("年","",trim(strip_tags($moviceNianFenInfo[1])));
                    }
                }

                //电影介绍
                $moviceJieShaoInfo = $this->_getPregMatch("/<div id=\"j-less-detail\">(.*?)<\/div>/si",$moviceDetailHtml);
                if (!empty($moviceJieShaoInfo[1])) {
                    $resultArr['jieshao'] = str_replace("详细>>","",trim(strip_tags($moviceJieShaoInfo[1])));
                } else {
                    $moviceJieShaoInfo = $this->_getPregMatch("/<div class=\"clearfix\" data-seq=\"1\" data-elem=\"tabbody\">(.*?)<\/p>/si",$moviceHtml);
                    $resultArr['jieshao'] = trim(strip_tags($moviceJieShaoInfo[1]));
                }

                //电影图片
                $moviceImgInfo = $this->_getPregMatch("/<img itemprop=\"image\" src=\"(.*?)\"(.*?)>/si",$moviceDetailHtml);
                if (!empty($moviceImgInfo[1])) {
                    $imgUrl = $moviceImgInfo[1];
                    $imgArr = explode(".", $imgUrl);
                    $imagesName = md5($resultArr['name'] . "_" . $resultArr['webType'] . "_" . $resultArr['webId']) . "." . $imgArr[count($imgArr) - 1];
                    $resultArr['image'] = $this->_downLoadImg($imagesName, $imgUrl); //下载图片并保存,并返回图片地址
                } else { //默认图片地址 todo
                    $resultArr['image'] = $this->get_config_value("dy_common_img");
                }

                if (strpos($moviceHtml,'<div class="play_topright ">') !== false) {//收费
                    $shoufeiType = 2;
                } else {
                    $shoufeiType = 1;
                }

                $resultArr['exist_watch'] = 1;

                $watchLink = $titleVal;
                $watchArr = array();
                $watchArr['link'] = $watchLink;
                $watchArr['player'] = 4;
                $watchArr['qingxi'] = 3;
                $watchArr['shoufei'] = $shoufeiType;
                $wText = $this->_getPregMatch("/<title>(.*?)<\/title>/si",$moviceHtml);
                $wText = $wText[1];
                $wTextArr = explode("-",$wText);
                if (strpos($wTextArr[0],"特辑") !== false) {
                    $watchArr['beizhu'] = "特辑";
                    $watchArr['shoufei'] = 4;
                } elseif (strpos($wTextArr[0],"预告") !== false) {
                    $watchArr['beizhu'] = "预告";
                    $watchArr['shoufei'] = 3;
                } else {
                    if ($shoufeiType == 1) {
                        $watchArr['beizhu'] = "高清免费";
                    } else {
                        $watchArr['beizhu'] = "高清收费";
                    }
                }

                //读取信息是否存在
                $info = $this->_getMoviceInfoByIdAndType($resultArr['webId'], $resultArr['webType']);
                if (!empty($info)) { //信息已存在
                    if ($info['del'] == 1) {
                        //获取电影被合并信息
                        $delInfo = $this->_getDelMoviceInfoById($info['id']);
                        if (!empty($delInfo)) {
                            //更新观看链接
                            $this->_updateWatchLinkInfo($delInfo['currentInfoId'],$watchArr);
                        }
                    } else {
                        //获取差异信息
                        $deInfo = $this->_getComDetailInfo($info,$resultArr);
                        //更新电影信息
                        $this->_updateDetailInfo($info['id'],$deInfo);
                        //更新观看链接
                        $this->_updateWatchLinkInfo($info['id'],$watchArr);
                    }
                } else {
                    $lastId = $this->_insertMoviceDetailInfo($resultArr);
                    if (!empty($lastId) && !is_array($lastId)) {
                        $this->_addWaterToImg($resultArr['image']);
                    }
                    if (!empty($lastId) && !is_array($lastId)) {
                        $watchArr['infoId'] = $lastId;
                        $watchId = $this->_insertWatchLinkInfo($watchArr);
                    }
                }
            }
        }
    }

    /** 抓取电影网网电影信息
     * @param $htmlUrl
     * @param $imgUrl
     */
    private function _m1905($url, $urlType)
    {
        if (empty($url) || empty($urlType)) {
            return false;
        }

        if (!empty($_SERVER['XIONGJIEWU_TEST'])) {
            $url = "http://www.m1905.com/vod/list/a_1/o5u1l0p18.html";//测试，待删除
        }
        $totalInfoHtml = $this->_getCurlInfo($url);
        //观看链接
        $titleInfo = $this->_getPregMatchAll("/http:\/\/www\.m1905\.com\/vod\/info\/[0-9]+\.shtml/i", $totalInfoHtml, PREG_PATTERN_ORDER);

        if (!empty($titleInfo[0]) && is_array($titleInfo[0])) {
            //视频链接链接去重
            $titleRealInfo = array_unique($titleInfo[0]);
            $titleRealInfo = array_values($titleRealInfo);

            //图片地址
            $imgTotalInfo = $this->_getPregMatchAll("/http:\/\/image[0-9]+\.m1905\.cn\/uploadfile\/[0-9]+\/[0-9]+\/[0-9a-zA-Z_]+\.jpg/i", $totalInfoHtml, PREG_PATTERN_ORDER);
            //http://image11.m1905.cn/uploadfile/2013/0128/thumb_1_152_212_20130128033410812.jpg
            $imgTotalInfo = array_unique($imgTotalInfo[0]);
            $imgTotalInfo = array_values($imgTotalInfo);

            foreach ($titleRealInfo as $key => $titleVal) {
                if (empty($titleVal)) {
                    continue;
                }
                //$titleVal = "http://www.m1905.com/vod/info/645393.shtml";
                //读取详细信息
                $moviceHtml = $this->_getCurlInfo($titleVal);

                $resultArr = array();
                $resultArr['webType'] = $this->_webConfigInfo['m1905']['type'];
                $moviceIdArr = explode("/",$titleVal);
                $idArr = explode(".",$moviceIdArr[count($moviceIdArr) - 1]);
                $resultArr['webId'] = $idArr[0];

                //电影名称+介绍信息
                $moviceNameAndJieShaoInfo = $this->_getPregMatch("/<dl class=\"pic_left\">(.*?)<\/dl>/si",$moviceHtml);
                if (empty($moviceNameAndJieShaoInfo[1])) {//没有电影名称信息，不是电影，直接跳过
                    //电影名称
                    $moviceNameInfo = $this->_getPregMatch("/<h1 class=\"blcolor pr05 f26 fl\">(.*?)<\/h1>/si",$moviceHtml);
                    if (empty($moviceNameInfo[1])) {
                        continue;
                    }
                    $resultArr['name'] = trim(strip_tags($moviceNameInfo[1]));
                    $firstLetter = $this->getFirstLetter($resultArr['name']);
                    if ($firstLetter != "*") {
                        $resultArr['firstLetter'] = $firstLetter;
                    }
                    //拼音
                    $pinyin = $this->getPinyin($resultArr['name'],2);
                    if (!empty($pinyin)) {
                        $resultArr['pinyin'] = $pinyin;
                    }

                    //电影导演
                    $moviceDaoYanInfo = $this->_getPregMatch("/<p><span class=\"int01\">导演：(.*?)<\/p>/si",$moviceHtml);
                    if (empty($moviceDaoYanInfo[1])) {
                        continue;
                    }
                    $resultArr['daoyan'] = trim(strip_tags($moviceDaoYanInfo[1]));

                    //电影主演
                    $moviceZhuYanInfo = $this->_getPregMatch("/<p><span class=\"int01\">主演：(.*?)<\/p>/si",$moviceHtml);
                    if (empty($moviceZhuYanInfo[1])) {
                        continue;
                    }
                    $resultArr['zhuyan'] = str_replace(" / ","、",trim(strip_tags($moviceZhuYanInfo[1])));
                    if (empty($resultArr['zhuyan'])) {
                        continue;
                    }

                    //电影类型
                    $moviceTypeInfo = $this->_getPregMatch("/<p><span class=\"int01\">类型：(.*?)<\/p>/si",$moviceHtml);
                    if (empty($moviceTypeInfo[1])) {
                        continue;
                    }
                    $moviceTypeInfo[1] = trim(strip_tags($moviceTypeInfo[1]));
                    if (strpos($moviceTypeInfo[1],"综艺") !== false) {
                        continue;
                    }
                    $resultArr['type'] = $this->_getMoviceType($moviceTypeInfo[1]);

                    //电影介绍
                    $moviceJieShaoInfo = $this->_getPregMatch("/<p><span class=\"int01\">剧情：(.*?)<\/p>/si",$moviceHtml);
                    if (empty($moviceJieShaoInfo[1])) {
                        continue;
                    }
                    $resultArr['jieshao'] = mb_substr(trim(strip_tags($moviceJieShaoInfo[1])),0,300);
                    $resultArr['jieshao'] = str_replace("查看全部","",$resultArr['jieshao']);

                    //电影图片
                    $imgInfo = $this->_getPregMatch("/<div class=\"jc2_left fl\">(.*?)<\/div>/si",$moviceHtml);
                    $imgInfo = $this->_getPregMatch("/src=\"(.*?)\"/si",$imgInfo[1]);
                    if (!empty($imgInfo[1])) {
                        $imgUrl = $imgInfo[1];
                        $imgArr = explode(".", $imgUrl);
                        $imagesName = md5($resultArr['name'] . "_" . $resultArr['webType'] . "_" . $resultArr['webId']) . "." . $imgArr[count($imgArr) - 1];
                        $resultArr['image'] = $this->_downLoadImg($imagesName, $imgUrl); //下载图片并保存,并返回图片地址
                    } else {
                        $resultArr['image'] = $this->get_config_value("dy_common_img");
                    }

                    //观看地址
                    $watchLinkInfo = $this->_getPregMatch("/<span class=\"jc2_playbot\">(.*?)<\/span>/si",$moviceHtml);
                    $watchInfo  = array();
                    if (!empty($watchLinkInfo[1])) {
                        $watchInfo = $this->_getPregMatch("/href=\"(.*?)\"/si",$watchLinkInfo[1]);
                        $resultArr['exist_watch'] = 1;
                    } else {
                        $resultArr['exist_watch'] = 0;
                    }

                } else {
                    $moviceNameAndJieShaoInfoHtml = $moviceNameAndJieShaoInfo[1];

                    //电影名称
                    $moviceNameInfo = $this->_getPregMatch("/<h1>(.*?)<\/h1>/si",$moviceNameAndJieShaoInfoHtml);
                    $resultArr['name'] = trim(strip_tags($moviceNameInfo[1]));

                    //电影介绍
                    $moviceJieShaoInfo = $this->_getPregMatch("/<p>(.*?)<\/p>/si",$moviceNameAndJieShaoInfoHtml);
                    if (empty($moviceJieShaoInfo[1])) {
                        continue;
                    }
                    $resultArr['jieshao'] = trim(strip_tags($moviceJieShaoInfo[1]));

                    //电影导演
                    $moviceDaoYanInfo = $this->_getPregMatch("/<p>导演：(.*?)<\/p>/si",$moviceHtml);
                    if (empty($moviceDaoYanInfo[1])) {
                        continue;
                    }
                    $resultArr['daoyan'] = trim(strip_tags($moviceDaoYanInfo[1]));

                    //电影主演
                    $moviceZhuYanInfo = $this->_getPregMatch("/<p>主演：(.*?)<\/p>/si",$moviceHtml);
                    if (empty($moviceZhuYanInfo[1])) {
                        continue;
                    }
                    $resultArr['zhuyan'] = str_replace(" / ","、",trim(strip_tags($moviceZhuYanInfo[1])));
                    if (empty($resultArr['zhuyan'])) {
                        continue;
                    }
                    //电影类型
                    $moviceTypeInfo = $this->_getPregMatch("/<p>标签：(.*?)<\/p>/si",$moviceHtml);
                    if (empty($moviceTypeInfo[1])) {
                        continue;
                    }
                    $moviceTypeInfo[1] = trim(strip_tags($moviceTypeInfo[1]));
                    if (strpos($moviceTypeInfo[1],"综艺") !== false) {
                        continue;
                    }
                    $resultArr['type'] = $this->_getMoviceType($moviceTypeInfo[1]);

                    //电影图片
                    if (!empty($imgTotalInfo[$key])) {
                        $imgUrl = $imgTotalInfo[$key];
                        $imgArr = explode(".", $imgUrl);
                        $imagesName = md5($resultArr['name'] . "_" . $resultArr['webType'] . "_" . $resultArr['webId']) . "." . $imgArr[count($imgArr) - 1];
                        $resultArr['image'] = $this->_downLoadImg($imagesName, $imgUrl); //下载图片并保存,并返回图片地址
                    } else {
                        $resultArr['image'] = $this->get_config_value("dy_common_img");
                    }

                    //观看地址
                    $watchLinkInfo = $this->_getPregMatch("/<dl class=\"pic_right\">(.*?)<\/dl>/si",$moviceHtml);
                    $watchInfo  = array();
                    if (!empty($watchLinkInfo[1])) {
                        $watchInfo = $this->_getPregMatch("/href=\"(.*?)\"/si",$watchLinkInfo[1]);
                        $resultArr['exist_watch'] = 1;
                    } else {
                        $resultArr['exist_watch'] = 0;
                    }
                }

                if (strpos($resultArr['name'],"爱上电影网") !== false || strpos($resultArr['name'],"首映礼") !== false) {
                    continue;
                }

                if (strpos($resultArr['name'],"守株人") !== false) {
                    file_put_contents("/home/www/logs/dianying/zhuaqu_error.log",$titleVal . "\n");
                }
                //电影地区
                $resultArr['diqu'] = $this->_webConfigInfo['m1905'][$urlType][0]['diqu'];

                $watchLink = $watchInfo[1];
                $watchArr = array();
                $watchArr['link'] = $watchLink;
                $watchArr['player'] = 14;
                $watchArr['qingxi'] = 3;
                $watchArr['shoufei'] = 1;

                //读取信息是否存在
                $info = $this->_getMoviceInfoByIdAndType($resultArr['webId'], $resultArr['webType']);
                if (!empty($info)) { //信息已存在
                    if ($info['del'] == 1) {
                        //获取电影被合并信息
                        $delInfo = $this->_getDelMoviceInfoById($info['id']);
                        if (!empty($delInfo)) {
                            //更新观看链接
                            $this->_updateWatchLinkInfo($delInfo['currentInfoId'],$watchArr);
                        }
                    } else {
                        //获取差异信息
                        $deInfo = $this->_getComDetailInfo($info,$resultArr);
                        //更新电影信息
                        $this->_updateDetailInfo($info['id'],$deInfo);
                        //更新观看链接
                        $this->_updateWatchLinkInfo($info['id'],$watchArr);
                    }
                } else {
                    $lastId = $this->_insertMoviceDetailInfo($resultArr);
                    if (!empty($lastId) && !is_array($lastId)) {
                        $this->_addWaterToImg($resultArr['image']);
                    }
                    if (!empty($lastId) && !is_array($lastId) && ($resultArr['exist_watch'] == 1)) {
                        $watchArr['infoId'] = $lastId;
                        $watchId = $this->_insertWatchLinkInfo($watchArr);
                    }
                }
            }
        }
    }

    /** 抓取pps电影信息
     * @param $htmlUrl
     * @param $imgUrl
     */
    private function _pps($url,$urlType)
    {
        if (empty($url) || empty($urlType)) {
            return false;
        }
        //$url = "http://v.pps.tv/v_list/c_movie_o_3_p_50.html";//测试待删除
        $totalInfoHtml = $this->_getCurlInfo($url);
        $totalInfoHtmlArr = $this->_getPregMatch("/<div class=\"imgshow-bx\">.+<\/div>/si",$totalInfoHtml);
        $totalInfoHtml = $totalInfoHtmlArr[0];
        //观看链接
        $titleInfo = $this->_getPregMatchAll("/http:\/\/v\.pps\.tv\/splay_[0-9]+\.html/i", $totalInfoHtml, PREG_PATTERN_ORDER);

        if (!empty($titleInfo[0]) && is_array($titleInfo[0])) {
            //视频链接链接去重
            $titleRealInfo = array_unique($titleInfo[0]);
            $titleRealInfo = array_values($titleRealInfo);

            //图片信息
            $imgInfo = $this->_getPregMatchAll("/(http:\/\/image[0-9]+\.webscache\.com\/baike\/haibao\/small\/(.*?)\.jpg)|(src=\"\")/i", $totalInfoHtml, PREG_PATTERN_ORDER);
            //$imgInfo = array_unique($imgInfo[0]);
            $imgInfo = array_values($imgInfo[0]);
            if (count($titleRealInfo) != count($imgInfo)) {//如果链接个数与图片个数不一致，则退出
                var_dump($url);
                var_dump(date("Y-m-d H:i:s"));
                var_dump("---pps---count--\n");
                return false;
            }

            foreach ($titleRealInfo as $titleKey => $titleVal) {
                if (empty($titleVal)) {
                    continue;
                }
                //$titleVal = "http://www.iqiyi.com/dianying/20110411/1c822647f31334ec.html";
                //读取详细信息
                $moviceHtml = mb_convert_encoding($this->_getCurlInfo($titleVal),"UTF-8","GBK");

                $webArr = $this->_getPregMatch("/[0-9]+/i",$titleVal);
                $resultArr = array();
                $resultArr['webType'] = $this->_webConfigInfo['pps']['type'];
                $resultArr['webId'] = $webArr[0];

                //电影名称
                $moviceNameInfo = $this->_getPregMatch("/<div class=\"hd\"><h1(.*?)>(.*?)<\/h1>/si",$moviceHtml);
                $resultArr['name'] = trim($moviceNameInfo[2]);
                if (empty($resultArr['name'])) {
                    continue;
                }
                $firstLetter = $this->getFirstLetter($resultArr['name']);
                if ($firstLetter != "*") {
                    $resultArr['firstLetter'] = $firstLetter;
                }
                //拼音
                $pinyin = $this->getPinyin($resultArr['name'],2);
                if (!empty($pinyin)) {
                    $resultArr['pinyin'] = $pinyin;
                }

                //电影类型
                $moviceNameInfo = $this->_getPregMatch("/<span class=\"h\">类型：<\/span>(.*?)<\/li>/si",$moviceHtml);
                $resultArr['type'] = $this->_getMoviceType(trim(strip_tags($moviceNameInfo[1])));

                //电影年份
                $moviceNianFenInfo = $this->_getPregMatch("/<span class=\"h\">发行日期：<\/span>(.*?)<\/li>/si",$moviceHtml);
                $resultArr['nianfen'] = trim(strip_tags($moviceNianFenInfo[1]));

                //电影导演
                $moviceDaoYanInfo = $this->_getPregMatch("/<span class=\"h\">导演：<\/span>(.*?)<\/li>/si",$moviceHtml);
                $moviceDaoYanInfo = trim(strip_tags($moviceDaoYanInfo[1]));
                $moviceDaoYanInfo = explode(" ",$moviceDaoYanInfo);
                $moviceDaoYanInfo = array_filter($moviceDaoYanInfo);
                foreach($moviceDaoYanInfo as $moviceKey => $moviceVal) {
                    $moviceVal = trim($moviceVal);
                    if (empty($moviceVal)) {
                        unset($moviceDaoYanInfo[$moviceKey]);
                    }
                }
                $resultArr['daoyan'] = implode("、",$moviceDaoYanInfo);

                //电影地区
                $moviceDiQuInfo = $this->_getPregMatch("/<span class=\"h\">国家\/地区：<\/span>(.*?)<\/li>/si",$moviceHtml);
                $resultArr['diqu'] = $this->_getDiQuType(trim(strip_tags($moviceDiQuInfo[1])));

                //电影主演
                $moviceZhuYanInfo = $this->_getPregMatch("/<span class=\"h\">主演：<\/span>(.*?)<\/li>/si",$moviceHtml);
                $moviceZhuYanInfo = trim(strip_tags($moviceZhuYanInfo[1]));
                $moviceZhuYanInfo = explode(" ",$moviceZhuYanInfo);
                $moviceZhuYanInfo = array_filter($moviceZhuYanInfo);
                foreach($moviceZhuYanInfo as $moviceKey => $moviceVal) {
                    $moviceVal = trim($moviceVal);
                    if (empty($moviceVal)) {
                        unset($moviceZhuYanInfo[$moviceKey]);
                    }
                }
                $resultArr['zhuyan'] = implode("、",$moviceZhuYanInfo);

                //电影介绍
                $moviceJieShaoInfo = $this->_getPregMatch("/<span txt=\"(.*?)\">/si",$moviceHtml);
                $resultArr['jieshao'] = trim(strip_tags($moviceJieShaoInfo[1]));

                //电影图片
                $imgUrl = $imgInfo[$titleKey];
                $imgArr = explode(".", $imgUrl);
                $imagesName = md5($resultArr['name'] . "_" . $resultArr['webType'] . "_" . $resultArr['webId']) . "." . $imgArr[count($imgArr) - 1];
                $resultArr['image'] = $this->_downLoadImg($imagesName, $imgUrl); //下载图片并保存,并返回图片地址

                //观看链接
                $moviceWatchLinkInfo = $this->_getPregMatch("/<ul class=\"p-list128-80 p-list2\">(.*?)<\/a>/si",$moviceHtml);
                if (!empty($moviceWatchLinkInfo[1])) {
                    $moviceWatchLinkInfo = $this->_getPregMatch("/href=\"(.*?)\"/si",$moviceWatchLinkInfo[1]);
                    if (!empty($moviceWatchLinkInfo[1])) {
                        $resultArr['exist_watch'] = 1;
                    }
                } else {
                    $resultArr['exist_watch'] = 0;
                }

                $watchArr = array();
                $watchArr['link'] = $moviceWatchLinkInfo[1];
                $watchArr['player'] = 11;
                $watchArr['qingxi'] = 3;
                $watchArr['shoufei'] = 1;

                //读取信息是否存在
                $info = $this->_getMoviceInfoByIdAndType($resultArr['webId'], $resultArr['webType']);
                if (!empty($info)) { //信息已存在
                    if ($info['del'] == 1) {
                        //获取电影被合并信息
                        $delInfo = $this->_getDelMoviceInfoById($info['id']);
                        if (!empty($delInfo)) {
                            //更新观看链接
                            $this->_updateWatchLinkInfo($delInfo['currentInfoId'],$watchArr);
                        }
                    } else {
                        //获取差异信息
                        $deInfo = $this->_getComDetailInfo($info,$resultArr);
                        //更新电影信息
                        $this->_updateDetailInfo($info['id'],$deInfo);
                        //更新观看链接
                        $this->_updateWatchLinkInfo($info['id'],$watchArr);
                    }
                } else {
                    $lastId = $this->_insertMoviceDetailInfo($resultArr);
                    if (!empty($lastId) && !is_array($lastId)) {
                        $this->_addWaterToImg($resultArr['image']);
                    }
                    if (!empty($lastId) && !is_array($lastId) && !empty($resultArr['exist_watch'])) {
                        $watchArr['infoId'] = $lastId;
                        $watchId = $this->_insertWatchLinkInfo($watchArr);
                    } else {
                        var_dump($titleVal);
                        var_dump($lastId);
                        var_dump("--pps---error--");
                        var_dump(date("Y-m-d H:i:s") , "\n");
                    }
                }
            }
        }
    }

    /** 抓取pptv电影信息--这部分还没做，代码是copy过来的
     * @param $htmlUrl
     * @param $imgUrl
     */
    private function _pptv($url,$urlType)
    {
        if (empty($url) || empty($urlType)) {
            return false;
        }
        //$url = "http://v.pps.tv/v_list/c_movie_p_34.html";//测试待删除
        $totalInfoHtml = $this->_getCurlInfo($url);
        $totalInfoHtmlArr = $this->_getPregMatch("/<div class=\"imgshow-bx\">.+<\/div>/si",$totalInfoHtml);
        $totalInfoHtml = $totalInfoHtmlArr[0];
        //观看链接
        $titleInfo = $this->_getPregMatchAll("/http:\/\/v\.pps\.tv\/splay_[0-9]+\.html/i", $totalInfoHtml, PREG_PATTERN_ORDER);

        if (!empty($titleInfo[0]) && is_array($titleInfo[0])) {
            //视频链接链接去重
            $titleRealInfo = array_unique($titleInfo[0]);
            $titleRealInfo = array_values($titleRealInfo);

            //图片信息
            $imgInfo = $this->_getPregMatchAll("/(http:\/\/image[0-9]+\.webscache\.com\/baike\/haibao\/small\/(.*?)\.jpg)|(src=\"\")/i", $totalInfoHtml, PREG_PATTERN_ORDER);
            $imgInfo = array_unique($imgInfo[0]);
            $imgInfo = array_values($imgInfo);

            if (count($titleRealInfo) != count($imgInfo)) {//如果链接个数与图片个数不一致，则退出
                var_dump($url);
                var_dump(date("Y-m-d H:i:s"));
                var_dump("---pps---count--\n");
                return false;
            }

            foreach ($titleRealInfo as $titleKey => $titleVal) {
                if (empty($titleVal)) {
                    continue;
                }
                //$titleVal = "http://www.iqiyi.com/dianying/20110411/1c822647f31334ec.html";
                //读取详细信息
                $moviceHtml = mb_convert_encoding($this->_getCurlInfo($titleVal),"UTF-8","GBK");

                $webArr = $this->_getPregMatch("/[0-9]+/i",$titleVal);
                $resultArr = array();
                $resultArr['webType'] = $this->_webConfigInfo['pps']['type'];
                $resultArr['webId'] = $webArr[0];

                //电影名称
                $moviceNameInfo = $this->_getPregMatch("/<div class=\"hd\"><h1(.*?)>(.*?)<\/h1>/si",$moviceHtml);
                $resultArr['name'] = trim($moviceNameInfo[2]);
                if (empty($resultArr['name'])) {
                    continue;
                }
                $firstLetter = $this->getFirstLetter($resultArr['name']);
                if ($firstLetter != "*") {
                    $resultArr['firstLetter'] = $firstLetter;
                }
                //拼音
                $pinyin = $this->getPinyin($resultArr['name'],2);
                if (!empty($pinyin)) {
                    $resultArr['pinyin'] = $pinyin;
                }

                //电影类型
                $moviceNameInfo = $this->_getPregMatch("/<span class=\"h\">类型：<\/span>(.*?)<\/li>/si",$moviceHtml);
                $resultArr['type'] = $this->_getMoviceType(trim(strip_tags($moviceNameInfo[1])));

                //电影年份
                $moviceNianFenInfo = $this->_getPregMatch("/<span class=\"h\">发行日期：<\/span>(.*?)<\/li>/si",$moviceHtml);
                $resultArr['nianfen'] = trim(strip_tags($moviceNianFenInfo[1]));

                //电影导演
                $moviceDaoYanInfo = $this->_getPregMatch("/<span class=\"h\">导演：<\/span>(.*?)<\/li>/si",$moviceHtml);
                $moviceDaoYanInfo = trim(strip_tags($moviceDaoYanInfo[1]));
                $moviceDaoYanInfo = explode(" ",$moviceDaoYanInfo);
                $moviceDaoYanInfo = array_filter($moviceDaoYanInfo);
                foreach($moviceDaoYanInfo as $moviceKey => $moviceVal) {
                    $moviceVal = trim($moviceVal);
                    if (empty($moviceVal)) {
                        unset($moviceDaoYanInfo[$moviceKey]);
                    }
                }
                $resultArr['daoyan'] = implode("、",$moviceDaoYanInfo);

                //电影地区
                $moviceDiQuInfo = $this->_getPregMatch("/<span class=\"h\">国家\/地区：<\/span>(.*?)<\/li>/si",$moviceHtml);
                $resultArr['diqu'] = $this->_getDiQuType(trim(strip_tags($moviceDiQuInfo[1])));

                //电影主演
                $moviceZhuYanInfo = $this->_getPregMatch("/<span class=\"h\">主演：<\/span>(.*?)<\/li>/si",$moviceHtml);
                $moviceZhuYanInfo = trim(strip_tags($moviceZhuYanInfo[1]));
                $moviceZhuYanInfo = explode(" ",$moviceZhuYanInfo);
                $moviceZhuYanInfo = array_filter($moviceZhuYanInfo);
                foreach($moviceZhuYanInfo as $moviceKey => $moviceVal) {
                    $moviceVal = trim($moviceVal);
                    if (empty($moviceVal)) {
                        unset($moviceZhuYanInfo[$moviceKey]);
                    }
                }
                $resultArr['zhuyan'] = implode("、",$moviceZhuYanInfo);

                //电影介绍
                $moviceJieShaoInfo = $this->_getPregMatch("/<span txt=\"(.*?)\">/si",$moviceHtml);
                $resultArr['jieshao'] = trim(strip_tags($moviceJieShaoInfo[1]));

                //电影图片
                $imgUrl = $imgInfo[$titleKey];
                $imgArr = explode(".", $imgUrl);
                $imagesName = md5($resultArr['name'] . "_" . $resultArr['webType'] . "_" . $resultArr['webId']) . "." . $imgArr[count($imgArr) - 1];
                $resultArr['image'] = $this->_downLoadImg($imagesName, $imgUrl); //下载图片并保存,并返回图片地址

                //观看链接
                $moviceWatchLinkInfo = $this->_getPregMatch("/<ul class=\"p-list128-80 p-list2\">(.*?)<\/a>/si",$moviceHtml);
                if (!empty($moviceWatchLinkInfo[1])) {
                    $moviceWatchLinkInfo = $this->_getPregMatch("/href=\"(.*?)\"/si",$moviceWatchLinkInfo[1]);
                    if (!empty($moviceWatchLinkInfo[1])) {
                        $resultArr['exist_watch'] = 1;
                    }
                } else {
                    $resultArr['exist_watch'] = 1;
                }

                $watchArr = array();
                $watchArr['link'] = $moviceWatchLinkInfo[1];
                $watchArr['player'] = 11;
                $watchArr['qingxi'] = 3;
                $watchArr['shoufei'] = 1;

                //读取信息是否存在
                $info = $this->_getMoviceInfoByIdAndType($resultArr['webId'], $resultArr['webType']);
                if (!empty($info)) { //信息已存在
                    if ($info['del'] == 1) {
                        //获取电影被合并信息
                        $delInfo = $this->_getDelMoviceInfoById($info['id']);
                        if (!empty($delInfo)) {
                            //更新观看链接
                            $this->_updateWatchLinkInfo($delInfo['currentInfoId'],$watchArr);
                        }
                    } else {
                        //获取差异信息
                        $deInfo = $this->_getComDetailInfo($info,$resultArr);
                        //更新电影信息
                        $this->_updateDetailInfo($info['id'],$deInfo);
                        //更新观看链接
                        $this->_updateWatchLinkInfo($info['id'],$watchArr);
                    }
                } else {
                    $lastId = $this->_insertMoviceDetailInfo($resultArr);
                    if (!empty($lastId) && !is_array($lastId)) {
                        $this->_addWaterToImg($resultArr['image']);
                    }
                    if (!empty($lastId) && !is_array($lastId) && !empty($resultArr['exist_watch'])) {
                        $watchArr['infoId'] = $lastId;
                        $watchId = $this->_insertWatchLinkInfo($watchArr);
                    } else {
                        var_dump($titleVal);
                        var_dump($lastId);
                        var_dump("--pps---error--");
                        var_dump(date("Y-m-d H:i:s") , "\n");
                    }
                }
            }
        }
    }

    /** 获取电影信息与抓取过来的信息是否一致，返回差异的值
     * @param $oldDetailInfo
     * @param $newDetailInfo
     * @return array|bool
     */
    private function _getComDetailInfo($oldDetailInfo,$newDetailInfo) {
        if (empty($oldDetailInfo) || empty($newDetailInfo)) {
            return false;
        }
        $resultArr = array();
        foreach($oldDetailInfo as $oldDetailKey => $oldDetailVal) {
            if ($oldDetailKey == "image" && !empty($newDetailInfo["image"]) && strpos($newDetailInfo["image"],"dy_common") === false) {
                $resultArr["image"] = $newDetailInfo["image"];
            } elseif ((empty($oldDetailVal) || ($oldDetailKey == "daoyan") || ($oldDetailKey == "zhuyan") || ($oldDetailKey == "jieshao")) && !empty($newDetailInfo[$oldDetailKey])) {
                $resultArr[$oldDetailKey] = $newDetailInfo[$oldDetailKey];
            }
        }
        return $resultArr;
    }

    /** 更新电影信息
     * @param array $dataArr 新数据数组
     * @return bool|int
     */
    protected function _updateDetailInfo($id,$dataArr = array())
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
        $sql = "update `tbl_detailInfo` set {$setStr} where id = {$id} limit 1;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->execute($valArr);
        return $stmt->rowCount();
    }

    /** 更新观看链接信息
     * @param array $dataArr 新数据数组
     * @return bool|int
     */
    private function _updateWatchLinkInfo($infoId,$dataArr = array())
    {
        $infoId = intval($infoId);
        if (empty($infoId) || empty($dataArr)) {
            return false;
        }
        $dataArr = array_filter($dataArr);
        $setArr = $valArr = array();
        foreach($dataArr as $dataKey => $dataVal) {
            $setArr[] = "{$dataKey} = ?";
            $valArr[] = $dataVal;
        }
        $setStr = implode(",",$setArr);
        $sql = "update `tbl_watchLink` set {$setStr} where infoId = {$infoId} and player = " . $dataArr['player'] . " limit 1;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->execute($valArr);
        return $stmt->rowCount();
    }

    /** 根据电影id获取电影评分信息
     * @param $webMoviceId
     * @param $webType
     * @return bool|mixed
     */
    private function _getMoviceScoreInfoByInfoId($infoId,$type = 1,$tableName = "tbl_movieScore")
    {
        $infoId = intval($infoId);
        $type = intval($type);
        if (empty($infoId) || empty($type)) {
            return true;
        }
        $sql = "select * from `" . $tableName . "` where infoId = {$infoId} and type = {$type} and del = 0 limit 1;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetch();
    }

    /** 插入电影分数信息
     * @param array $dataArr 数据数组
     * @return bool|int
     */
    private function _insertMovieScoreInfo($dataArr = array(),$tableName = "tbl_movieScore")
    {
        if (empty($dataArr)) {
            return false;
        }
        $keyArr = array_keys($dataArr);
        $keyStr = implode(",", $keyArr);
        $valueArr = array_fill(0, count($dataArr), '?');
        $valueStr = implode(",", $valueArr);
        $sql = "insert into `" . $tableName . "` ({$keyStr}) values ({$valueStr});";
        $stmt = $this->_pdo->prepare($sql);
        $dataArr = array_values($dataArr);
        $stmt->execute($dataArr);
        return $this->_pdo->lastInsertId();
    }

    /** 更新电影分数信息
     * @param array $dataArr 新数据数组
     * @return bool|int
     */
    private function _updateMovieScoreInfo($infoId,$dataArr = array(),$tableName = "tbl_movieScore")
    {
        $infoId = intval($infoId);
        if (empty($infoId) || empty($dataArr)) {
            return false;
        }
        $dataArr = array_filter($dataArr);
        $setArr = $valArr = array();
        foreach($dataArr as $dataKey => $dataVal) {
            $setArr[] = "{$dataKey} = ?";
            $valArr[] = $dataVal;
        }
        $setStr = implode(",",$setArr);
        $sql = "update `" . $tableName . "` set {$setStr} where infoId = {$infoId} limit 1;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->execute($valArr);
        return $stmt->rowCount();
    }

    /** 给图片添加水印
     * @param $watertype 水印类型，1=文字，2=图片
     * @param $img
     */
    public function addWaterDo($watertype,$img)
    {
        $image_size = getimagesize($img);
        $iinfo = getimagesize($img);
        $nimage = imagecreatetruecolor($image_size[0], $image_size[1]);
        $white = imagecolorallocate($nimage, 255, 255, 255);
        $black = imagecolorallocate($nimage, 0, 0, 0);
        $red = imagecolorallocate($nimage, 255, 0, 0);
        imagefill($nimage, 0, 0, $white);
        switch ($iinfo[2]) {
            case 1:
                $simage = imagecreatefromgif($img);
                break;
            case 2:
                $simage = imagecreatefromjpeg($img);
                break;
            case 3:
                $simage = imagecreatefrompng($img);
                break;
            case 6:
                $simage = imagecreatefromwbmp($img);
                break;
            default:
                return false;
        }

        imagecopy($nimage, $simage, 0, 0, 0, 0, $image_size[0], $image_size[1]);
        //水印加背景图片
        //imagefilledrectangle($nimage, 1, $image_size[1] - 15, 80, $image_size[1], $white);

        switch ($watertype) {
            case 1: //加水印字符串
                $rN = rand(1,100);
                if ($rN <= 50) {
                    imagestring($nimage, 3, 2, 0, $this->_waterString, $black);
                } else {
                    imagestring($nimage, 3, 2, $image_size[1] - 15, $this->_waterString, $black);
                }
                break;
            case 2: //加水印图片
                $simage1 = $this->_createWaterImg($this->_waterImg);
                imagecopy($nimage, $simage1, 0, 0, 0, 0, 200, 15);
                imagedestroy($simage1);
                break;
        }

        switch ($iinfo[2]) {
            case 1:
                imagejpeg($nimage, $img);
                break;
            case 2:
                imagejpeg($nimage, $img);
                break;
            case 3:
                imagepng($nimage, $img);
                break;
            case 6:
                imagewbmp($nimage, $img);
                break;
        }

        //覆盖原上传文件
        imagedestroy($nimage);
        imagedestroy($simage);
    }

    private function _createWaterImg() {
        $imgArr = explode(".",$this->_waterImg);
        switch(strtolower($imgArr[count($imgArr) - 1])) {
            case "gif" :
                $simage1 = imagecreatefromgif($this->_waterImg);
                break;
            case "png" :
                $simage1 = imagecreatefrompng($this->_waterImg);
                break;
            case "jpeg" :
                $simage1 = imagecreatefromjpeg($this->_waterImg);
                break;
            default :
                die("水印图片类型不符合！");
        }
        return $simage1;
    }

    /** 抓取奇热网电影信息
     * @param $htmlUrl
     * @param $imgUrl
     */
    private function _qire($url, $urlType)
    {
        if (empty($url) || empty($urlType)) {
            return false;
        }
        //$url = "http://ajax.qire123.com/vod-showlist-id-8-order-time-c-2873-p-1.html";//测试，待删除
        $totalInfoHtml = $this->_getCurlInfo($url);
        $totalInfoHtml = json_decode($totalInfoHtml,true);
        //观看链接
        $titleInfo = $this->_getPregMatchAll("/http:\/\/www\.qire123\.com\/{$urlType}\/[0-9a-zA-Z]+\//i", $totalInfoHtml['ajaxtxt'], PREG_PATTERN_ORDER);

        if (!empty($titleInfo[0]) && is_array($titleInfo[0])) {
            //视频链接链接去重
            $titleRealInfo = array_unique($titleInfo[0]);

            $baseUrl = "http://www.qire123.com";
            foreach ($titleRealInfo as $titleVal) {
                if (empty($titleVal)) {
                    continue;
                }
                //$titleVal = "http://www.qire123.com/war/shengnvzhendejingdianbu100/";//测试，待删除
                $resultArr = array();
                $resultArr['webType'] = $this->_webConfigInfo['qire']['type'];
                $moviceIdArr = explode("/",$titleVal);
                $resultArr['webId'] = $this->_get_id($moviceIdArr[count($moviceIdArr) - 2]);

                $moviceHtml = $this->_getCurlInfo($titleVal);

                //电影名称
                $moviceNameInfo = $this->_getPregMatch("/<div class=\"detail-title fn-clear\"><h2>(.*?)<\/h2>/si",$moviceHtml);
                if (empty($moviceNameInfo[1])) {
                    continue;
                }
                $resultArr['name']  = trim($moviceNameInfo[1]);
                $nameArr = explode("[",$resultArr['name']);
                $resultArr['name'] = $nameArr[0];
                $firstLetter = $this->getFirstLetter($resultArr['name']);
                if ($firstLetter != "*") {
                    $resultArr['firstLetter'] = $firstLetter;
                }
                //拼音
                $pinyin = $this->getPinyin($resultArr['name'],2);
                if (!empty($pinyin)) {
                    $resultArr['pinyin'] = $pinyin;
                }

                //电影主演
                $moviceZhuYanInfo = $this->_getPregMatch("/<dt>主演：<\/dt><dd>(.*?)<\/dd>/si",$moviceHtml);
                if (!empty($moviceZhuYanInfo[1])) {
                    $moviceZhuYanInfo[1] = implode("、",explode("</a>",$moviceZhuYanInfo[1]));
                    $moviceZhuYanInfo[1] = trim(strip_tags($moviceZhuYanInfo[1]),"、");
                    $resultArr['zhuyan']  = trim($moviceZhuYanInfo[1]);
                } else {
                    $resultArr['zhuyan']  = "暂无";
                }


                //电影类型
                $moviceTypeInfo = $this->_getPregMatch("/<dl class=\"fn-left\"><dt>类型：<\/dt>(.*?)<\/dd>/si",$moviceHtml);
                $moviceTypeInfo[1] = trim(strip_tags($moviceTypeInfo[1]));
                $resultArr['type']  = $this->_getMoviceType($moviceTypeInfo[1]);

                //电影地区
                $moviceDiQuInfo = $this->_getPregMatch("/<dl class=\"fn-right\"><dt>地区：<\/dt><dd>(.*?)<\/dd>/si",$moviceHtml);
                $moviceDiQuInfo[1] = trim(strip_tags($moviceDiQuInfo[1]));
                $resultArr['diqu']  = $this->_getDiQuType($moviceDiQuInfo[1]);

                //电影导演
                $moviceDaoYanInfo = $this->_getPregMatch("/<dl class=\"fn-right\"><dt>导演：<\/dt><dd>(.*?)<\/dd>/si",$moviceHtml);
                if (!empty($moviceDaoYanInfo[1])) {
                    $moviceDaoYanInfo[1] = implode("、",explode("</a>",$moviceDaoYanInfo[1]));
                    $moviceDaoYanInfo[1] = trim(strip_tags($moviceDaoYanInfo[1]),"、");
                    $resultArr['daoyan']  = trim($moviceDaoYanInfo[1]);
                } else {
                    $resultArr['daoyan']  = "暂无";
                }

                //电影年份
                $moviceNianFenInfo = $this->_getPregMatch("/\([0-9]{4}\)/si",$resultArr['name']);
                if (!empty($moviceNianFenInfo[0])) {
                    $moviceNianFenInfo[0] = str_replace("(","",$moviceNianFenInfo[0]);
                    $resultArr['nianfen'] = str_replace(")","",$moviceNianFenInfo[0]);
                } else {
                    $moviceNianFenInfo = $this->_getPregMatch("/<dl class=\"fn-right\"><dt>年份：<\/dt><dd>(.*?)<\/dd>/si",$moviceHtml);
                    if (!empty($moviceNianFenInfo[1])) {
                        $moviceNianFenInfo[1] = trim(strip_tags($moviceNianFenInfo[1]));
                        $resultArr['nianfen']  = substr($moviceNianFenInfo[1],0,4);
                    }
                }

                //电影介绍
                $moviceJieShaoInfo = $this->_getPregMatch("/<dl class=\"juqing\"><dt>剧情：<\/dt><dd>(.*?)<\/dd>/si",$moviceHtml);
                if (!empty($moviceJieShaoInfo[1])) {
                    $moviceJieShaoInfo[1] = trim(strip_tags($moviceJieShaoInfo[1]));
                    $resultArr['jieshao']  = str_replace("详细剧情","",$moviceJieShaoInfo[1]);
                } else {
                    $resultArr['jieshao']  = "暂无";
                }

                //电影图片
                $moviceImgInfo = $this->_getPregMatch("/<div class=\"detail-pic fn-left\"><img(.*?)src=\"(.*?)\"(.*?)><\/div>/si",$moviceHtml);
                if (!empty($moviceImgInfo[2])) {
                    $imgUrl = $moviceImgInfo[2];
                    $imgArr = explode(".", $imgUrl);
                    $imagesName = md5($resultArr['name'] . "_" . $resultArr['webType'] . "_" . $resultArr['webId']) . "." . $imgArr[count($imgArr) - 1];
                    $resultArr['image'] = $this->_downLoadImg($imagesName, $imgUrl); //下载图片并保存,并返回图片地址
                } else {
                    $resultArr['image'] = $this->get_config_value("dy_common_img"); //默认图片
                }

                //电影观看链接
                $moviceWatchInfo = $this->_getPregMatch("/<p class=\"play-list\">(.*?)<\/p>/si",$moviceHtml);
                $watchLinkInfoArr = array();
                if (!empty($moviceWatchInfo[1])) {
                    $watchLinkInfo = $this->_getPregMatchAll("/href=\"(.*?)\"/i",$moviceWatchInfo[1],PREG_PATTERN_ORDER);
                    $moviceWatchInfo[1] = array_filter(explode("</a>",$moviceWatchInfo[1]));
                    foreach($moviceWatchInfo[1] as $moviceWatchInfoKey => $moviceWatchInfoVal) {
                        $moviceWatchInfoVal = trim(strip_tags($moviceWatchInfoVal));
                        $moviceWatchInfoVal = str_replace("new","",$moviceWatchInfoVal);
                        if ($moviceWatchInfoVal != "预告片") {
                            $watchLinkInfoArr[$moviceWatchInfoKey]["link"] = $baseUrl . $watchLinkInfo[1][$moviceWatchInfoKey];
                            $watchLinkInfoArr[$moviceWatchInfoKey]["player"] = 2;
                            $watchLinkInfoArr[$moviceWatchInfoKey]["shoufei"] = 1;
                            $watchLinkInfoArr[$moviceWatchInfoKey]["qingxi"] = 3;
                            $watchLinkInfoArr[$moviceWatchInfoKey]["beizhu"] = $moviceWatchInfoVal;
                        }
                    }
                }

                //有观看链接的地址
                if (!empty($watchLinkInfoArr)) {
                    $resultArr['exist_watch'] = 1;
                }

                //读取信息是否存在
                $info = $this->_getMoviceInfoByIdAndType($resultArr['webId'], $resultArr['webType']);
                if (!empty($info)) {//信息已存在
                    if ($info['del'] == 1) {
                        //获取电影被合并信息
                        $delInfo = $this->_getDelMoviceInfoById($info['id']);
                        if (!empty($delInfo) && !empty($watchLinkInfoArr)) {
                            //插入观看链接
                            $info['id'] = $delInfo['currentInfoId'];
                            foreach($watchLinkInfoArr as $watchVal) {
                                //查询观看链接是否存在
                                $watchInfo = $this->_getWatchInfoByInfoIdAndBeiZhu($info['id'],$watchVal['beizhu']);
                                //不存在则插入
                                if (empty($watchInfo)) {
                                    $watchVal['infoId'] = $info['id'];
                                    $this->_insertWatchLinkInfo($watchVal);
                                }

                            }
                        }
                    } else {
                        //获取差异信息
                        $deInfo = $this->_getComDetailInfo($info,$resultArr);
                        //更新电影信息
                        $this->_updateDetailInfo($info['id'],$deInfo);
                        //更新观看链接
                        if (!empty($watchLinkInfoArr)) {
                            foreach($watchLinkInfoArr as $watchVal) {
                                //查询观看链接是否存在
                                $watchInfo = $this->_getWatchInfoByInfoIdAndBeiZhu($info['id'],$watchVal['beizhu']);
                                //不存在则插入
                                if (empty($watchInfo)) {
                                    $watchVal['infoId'] = $info['id'];
                                    $this->_insertWatchLinkInfo($watchVal);
                                }

                            }
                        }
                    }
                } else {
                    $lastId = $this->_insertMoviceDetailInfo($resultArr);
                    if (!empty($lastId) && !is_array($lastId)) {
                        $this->_addWaterToImg($resultArr['image']);
                    }
                    if (!empty($lastId) && !is_array($lastId) && !empty($resultArr['exist_watch'])) {
                        if (!empty($watchLinkInfoArr)) {
                            foreach($watchLinkInfoArr as $watchVal) {
                                $watchVal['infoId'] = $lastId;
                                $this->_insertWatchLinkInfo($watchVal);
                            }
                        }
                    }
                }
            }
        }
    }

    /** 根据infoid和备注获取观看链接信息
     * @param $infoId
     * @param $beizhu
     * @return bool|mixed
     */
    private function _getWatchInfoByInfoIdAndBeiZhu($infoId, $beiZhu)
    {
        $infoId = intval($infoId);
        if (empty($infoId) || empty($beiZhu)) {
            return true;
        }
        $sql = "select * from `tbl_watchLink` where infoId = {$infoId} and beizhu = '{$beiZhu}' limit 1;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetch();
    }


    /**
     * imdb网抓取主函数
     */
    private function _imdb($url, $urlType)
    {
        if (empty($url) || empty($urlType)) {
            return false;
        }

        $totalInfoHtml = $this->_getCurlInfo($url);
        $titleInfo = $this->_getPregMatchAll("/http:\/\/www\.imdb\.cn\/title\/tt[0-9]+/i", $totalInfoHtml, PREG_PATTERN_ORDER);
        if (!empty($titleInfo[0]) && is_array($titleInfo[0])) {
            $titleTotalInfo = array_unique($titleInfo[0]);
            $titleTotalInfo = array_values($titleTotalInfo);

            $baseUrl = "http://www.imdb.cn";
            foreach ($titleTotalInfo as $titleVal) {
                //$titleVal = "http://www.imdb.cn/title/tt0056217";//测试，待删除
                $resultArr = array();
                $resultArr['webType'] = $this->_webConfigInfo['imdb']['type'];
                $moviceIdArr = $this->_getPregMatch("/[0-9]+/i", $titleVal);
                $resultArr['webId'] = $this->_get_id($moviceIdArr[0]);

                $moviceHtml = mb_convert_encoding($this->_getCurlInfo($titleVal),"UTF-8","GBK");
                if (!empty($moviceHtml)) {
                    //标题
                    $titleArr = $this->_getPregMatch("/<title>(.*?)<\/title>/", $moviceHtml);
                    $titleRealArr = explode(" ", $titleArr[1]);
                    $resultArr['name'] = trim($titleRealArr[0]);
                    $firstLetter = $this->getFirstLetter($resultArr['name']);
                    if ($firstLetter != "*") {
                        $resultArr['firstLetter'] = $firstLetter;
                    }
                    //拼音
                    $pinyin = $this->getPinyin($resultArr['name'],2);
                    if (!empty($pinyin)) {
                        $resultArr['pinyin'] = $pinyin;
                    }

                    //图片地址
                    $imgArr = $this->_getPregMatch("/<td rowspan=\"30\" width=\"16\%\" valign=\"top\">(.*?)<br>/", $moviceHtml);
                    $imgArr = $this->_getPregMatch("/src=(.*?) /", $imgArr[1]);
                    if (!empty($imgArr[1]) && strpos($imgArr[1],".") !== false) {
                        $imgArr[1] = $baseUrl . $imgArr[1];
                        $imgInfo = explode(".", $imgArr[1]);
                        $imagesName = md5($resultArr['name'] . "_" . $resultArr['webType'] . "_" . $resultArr['webId']) . "." . $imgInfo[count($imgInfo) - 1];
                        $resultArr["image"] = $this->_downLoadImg($imagesName, $imgArr[1]); //下载图片并保存,并返回图片地址
                    } else {
                        $resultArr["image"] = $this->get_config_value("dy_common_img"); //默认图片
                    }

                    //导演
                    $daoyanArr = $this->_getPregMatch("/<span class=mn>导 演：<\/span><\/div>(.*?)<\/tr>/si", $moviceHtml);
                    if (!empty($daoyanArr[1])) {
                        $daoyanInfo = strip_tags($daoyanArr[1]);
                        $daoyanInfo = str_replace("&nbsp;","",$daoyanInfo);
                        $daoyanInfo = trim($daoyanInfo);
                        if (!empty($daoyanInfo)) {
                            $daoyanInfo = str_replace("(","",$daoyanInfo);
                            $daoyanInfo = str_replace(")","",$daoyanInfo);
                            $resultArr["daoyan"] = trim(str_replace(" / ", "、",$daoyanInfo));
                        } else {
                            $resultArr["daoyan"] = "暂无";
                        }
                    } else {
                        $resultArr["daoyan"] = "暂无";
                    }

                    //主演
                    $zhuyanArr = $this->_getPregMatch("/<span class=mn>主 演：<\/span><\/div>(.*?)<\/tr>/si", $moviceHtml);
                    if (!empty($zhuyanArr[1])) {
                        $zhuyanInfo = strip_tags($zhuyanArr[1]);
                        $zhuyanInfo = str_replace("&nbsp;","",$zhuyanInfo);
                        $zhuyanInfo = trim($zhuyanInfo);
                        if (!empty($zhuyanInfo)) {
                            $zhuyanInfo = str_replace("(","",$zhuyanInfo);
                            $zhuyanInfo = explode(")",$zhuyanInfo);
                            $zhuyanA = array();
                            foreach($zhuyanInfo as $zy) {
                                if (empty($zy)) {
                                    continue;
                                }
                                $zy = trim($zy);
                                $val = explode(" ",$zy);
                                $zhuyanA[] = $val[0];
                            }
                            $resultArr["zhuyan"] = implode("、",$zhuyanA);
                        } else {
                            $resultArr["zhuyan"] = "暂无";
                        }
                    } else {
                        $resultArr["zhuyan"] = "暂无";
                    }

                    //类型
                    $leixingArr = $this->_getPregMatch("/<span class=mn>类 型：<\/span><\/div>(.*?)<\/tr>/si", $moviceHtml);
                    if (!empty($leixingArr[1])) {
                        $leixingInfo = strip_tags($leixingArr[1]);
                        $resultArr["type"] = $this->_getMoviceType(trim($leixingInfo));
                    } else {
                        $resultArr["type"] = 10;
                    }

                    //地区
                    $diquArr = $this->_getPregMatch("/<span class=mn>地 区：<\/span><\/div>(.*?)<\/tr>/si", $moviceHtml);
                    if (!empty($diquArr[1])) {
                        $diquInfo = trim(strip_tags($diquArr[1]));
                        $resultArr["diqu"] = $this->_getDiQuType(trim($diquInfo));
                    } else {
                        $resultArr["diqu"] = 10;
                    }

                    //片长
                    $shichangArr = $this->_getPregMatch("/<span class=mn>时 长：<\/span><\/div>(.*?)<\/tr>/si", $moviceHtml);
                    if (!empty($shichangArr[1])) {
                        $shichangInfo = trim(strip_tags($shichangArr[1]));
                        $resultArr["shichang"] = intval($shichangInfo);
                    }

                    //介绍
                    $jieshaoArr = $this->_getPregMatch("/<div id=imdbjqbody><h2>·剧情介绍<\/h2>(.*?)<\/div>/si", $moviceHtml);
                    if (!empty($jieshaoArr[0])) {
                        $jieshaoInfo = trim(strip_tags($jieshaoArr[0]));
                        $jieshaoInfo = mb_substr($jieshaoInfo,0,600,"UTF-8");
                        $jieshaoInfo  =str_replace("&nbsp;"," ",$jieshaoInfo);
                        $jieshaoInfo = str_replace("【剧情简介】：","",$jieshaoInfo);
                        $jieshaoInfo = str_replace("·剧情介绍","",$jieshaoInfo);
                        $resultArr["jieshao"] = $jieshaoInfo;
                    }

                    //上映时间
                    $timeArr = $this->_getPregMatch("/<span class=mn>上 映：<\/span><\/div>(.*?)<\/tr>/si", $moviceHtml);
                    if (!empty($timeArr[1])) {
                        $timeInfo = trim(strip_tags($timeArr[1]));
                        $timeInfo = str_replace(" &nbsp;&nbsp;","",$timeInfo);
                        $timeInfo = explode("日",$timeInfo);
                        $timeInfo[0] = str_replace("年","-",$timeInfo[0]);
                        $timeInfo[0] = str_replace("月","-",$timeInfo[0]);
                        if (strlen($timeInfo[0]) > 5) {
                            $resultArr["time1"] = strtotime($timeInfo[0]);
                        }
                        $resultArr["nianfen"] = substr($timeInfo[0],0,4);
                    }
                    if (empty($resultArr["time1"])) {
                        $resultArr["time1"] = 0;
                    }

                    //top电影，作标志
                    $movieScoreInfo = array();
                    if ($urlType == "top") {
                        $resultArr['topType'] = 2;
                        //评分
                        $pingFenInfo = $this->_getPregMatch("/IMDb评分<img(.*?)><b>(.*?)<\/b>/si", $moviceHtml);
                        if (!empty($pingFenInfo[2])) {
                            $pingFenInfo = trim($pingFenInfo[2]);
                            $pingFenInfo = explode("/",$pingFenInfo);
                        }
                        $movieScoreInfo['score'] = $pingFenInfo[0];
                        $movieScoreInfo['link'] = $titleVal;
                        $movieScoreInfo['type'] = 2;
                    }
                    //读取信息是否存在
                    $info = $this->_getMoviceInfoByIdAndType($resultArr['webId'], $resultArr['webType']);
                    if (!empty($info)) { //信息已存在
                        if ($info['del'] == 1) {
                            //获取电影被合并信息
                            $delInfo = $this->_getDelMoviceInfoById($info['id']);
                            if (!empty($delInfo) && $urlType == "top") {
                                $info['id'] = $delInfo['currentInfoId'];
                                //获取电影评分信息
                                $movieLastScoreInfo = $this->_getMoviceScoreInfoByInfoId($info['id'],2);
                                if (empty($movieLastScoreInfo)) {//为空，则插入
                                    $movieScoreInfo['infoId'] = $info['id'];
                                    $movieScoreInfo['createTime'] = time();
                                    $this->_insertMovieScoreInfo($movieScoreInfo);
                                } else {
                                    $movieScoreInfo['upTime'] = time();
                                    $this->_updateMovieScoreInfo($info['id'],$movieScoreInfo);
                                }
                            }
                        } else {
                            //获取差异信息
                            $deInfo = $this->_getComDetailInfo($info,$resultArr);
                            //更新电影信息
                            $this->_updateDetailInfo($info['id'],$deInfo);
                            if ($urlType == "top") {
                                //获取电影评分信息
                                $movieLastScoreInfo = $this->_getMoviceScoreInfoByInfoId($info['id'],2);
                                if (empty($movieLastScoreInfo)) {//为空，则插入
                                    $movieScoreInfo['infoId'] = $info['id'];
                                    $movieScoreInfo['createTime'] = time();
                                    $this->_insertMovieScoreInfo($movieScoreInfo);
                                } else {
                                    $movieScoreInfo['upTime'] = time();
                                    $this->_updateMovieScoreInfo($info['id'],$movieScoreInfo);
                                }
                            }
                        }
                    } else {
                        //写入数据库 todo
                        $insertRes = $this->_insertMoviceDetailInfo($resultArr);
                        if (!empty($insertRes) && !is_array($insertRes)) {
                            $this->_addWaterToImg($resultArr['image']);
                        }
                        if (!empty($insertRes) && !is_array($insertRes)) {
                            if ($urlType == "top") {
                                $movieScoreInfo['infoId'] = $insertRes;
                                $movieScoreInfo['createTime'] = time();
                                $this->_insertMovieScoreInfo($movieScoreInfo);
                            }
                            var_dump("插入成功！" . date('Y-m-d H:i:s') . "\n");
                        } else {
                            var_dump("插入失败 {$moviceIdArr[0]} --- {$titleVal}！" . date('Y-m-d H:i:s') . "\n");
                            var_dump($insertRes);
                        }
                    }
                    sleep(1);
                }
            }
        }
    }

    /**
     * 百度网抓取主函数
     */
    private function _baidu($url, $urlType)
    {
        if (empty($url) || empty($urlType)) {
            return false;
        }

        $totalInfoHtml = $this->_getCurlInfo($url);
        $titleInfo = $this->_getPregMatchAll("/\.\/detail\?b=26&w=(.*?)\"/si", $totalInfoHtml, PREG_PATTERN_ORDER);

        if (!empty($titleInfo[0]) && is_array($titleInfo[0])) {
            $titleTotalInfo = array_unique($titleInfo[0]);
            $titleTotalInfo = array_values($titleTotalInfo);
            //点击量信息
            $clickInfo = $this->_getPregMatchAll("/(<span class=\"icon-rise\">[0-9]+<\/span>)|(<span class=\"icon-fall\">[0-9]+<\/span>)|(<span class=\"icon-fair\">[0-9]+<\/span>)/si", $totalInfoHtml, PREG_PATTERN_ORDER);
            $clickTotalInfo = array();
            foreach($clickInfo[0] as $clickVal) {
                $clickTotalInfo[]  = strip_tags($clickVal);
            }

            $baseUrl = "http://top.baidu.com";
            foreach ($titleTotalInfo as $titleKey => $titleVal) {
                $titleVal = rtrim($baseUrl . $titleVal,'"');
                //$titleVal = "http://top.baidu.com/detail?b=26&w=%CB%D9%B6%C8%D3%EB%BC%A4%C7%E96";//测试，待删除

                $moviceHtml = mb_convert_encoding($this->_getCurlInfo($titleVal),"UTF-8","GBK");
                if (!empty($moviceHtml)) {
                    $resultArr = array();
                    //标题
                    $titleArr = $this->_getPregMatch("/<h2>(.*?)<\/h2>/", $moviceHtml);
                    $titleRealArr = explode(" ", $titleArr[1]);
                    $resultArr['name'] = trim($titleRealArr[0]);
                    $firstLetter = $this->getFirstLetter($resultArr['name']);
                    if ($firstLetter != "*") {
                        $resultArr['firstLetter'] = $firstLetter;
                    }
                    //拼音
                    $pinyin = $this->getPinyin($resultArr['name'],2);
                    if (!empty($pinyin)) {
                        $resultArr['pinyin'] = $pinyin;
                    }

                    $resultArr['webType'] = $this->_webConfigInfo['baidu']['type'];
                    $moviceIdArr = $this->_getPregMatch("/[0-9]+/i", $titleVal);
                    $resultArr['webId'] = $this->_get_id(substr(md5($resultArr['name']),0,8));

                    //导演
                    $daoyanArr = $this->_getPregMatch("/<strong>导演：<\/strong>(.*?)<\/a>/si", $moviceHtml);
                    if (!empty($daoyanArr[1])) {
                        $daoyanInfo = strip_tags($daoyanArr[1]);
                        $daoyanInfo = str_replace("&nbsp;","",$daoyanInfo);
                        $daoyanInfo = trim($daoyanInfo);
                        $resultArr["daoyan"] = $daoyanInfo;
                    } else {
                        $resultArr["daoyan"] = "暂无";
                    }

                    //地区
                    $diquArr = $this->_getPregMatch("/<strong>地区：<\/strong>(.*?)<\/a>/si", $moviceHtml);
                    if (!empty($diquArr[1])) {
                        $diquInfo = strip_tags($diquArr[1]);
                        $diquInfo = str_replace("&nbsp;","",$diquInfo);
                        $diquInfo = trim($diquInfo);
                        $resultArr["diqu"] = $this->_getDiQuType($diquInfo);
                    } else {
                        $resultArr["diqu"] = $this->_getDiQuType("其他");
                    }

                    //年份
                    $nianfenArr = $this->_getPregMatch("/<strong>上映时间：<\/strong>[0-9]+/si", $moviceHtml);
                    if (!empty($nianfenArr[0])) {
                        $nianfenArr = $this->_getPregMatch("/[0-9]+/i", $nianfenArr[0]);
                        $nianfenInfo = trim($nianfenArr[0]);
                        $resultArr["nianfen"] = $nianfenInfo;
                    } else {
                        var_dump($titleVal . "\n");
                    }

                    //主演
                    $zhuyanArr = $this->_getPregMatch("/<strong>主演：<\/strong>(.*?)<strong>/si", $moviceHtml);
                    if (!empty($zhuyanArr[1])) {
                        $zhuyanInfo = str_replace("&nbsp;","",$zhuyanArr[1]);
                        $zhuyanInfoArr = explode("</a>",$zhuyanInfo);
                        $zhuyanInfoA = array();
                      foreach($zhuyanInfoArr as $zy) {
                          $zy = trim(strip_tags($zy));
                          if (empty($zy)) {
                              continue;
                          }
                          $zhuyanInfoA[] = $zy;
                      }
                        $resultArr["zhuyan"] = implode("、",$zhuyanInfoA);
                    } else {
                        $resultArr["zhuyan"] = "暂无";
                    }

                    //介绍
                    $jieshaoArr = $this->_getPregMatch("/<strong>简介：<\/strong>(.*?)<\/span>/si", $moviceHtml);
                    if (!empty($jieshaoArr[1])) {
                        $jieshaoInfo = strip_tags($jieshaoArr[1]);
                        $jieshaoInfo = str_replace("&nbsp;","",$jieshaoInfo);
                        $jieshaoInfo = trim($jieshaoInfo);
                        $resultArr["jieshao"] = $jieshaoInfo;
                    } else {
                        $resultArr["jieshao"] = "暂无";
                    }

                    //类型
                    $typeArr = $this->_getPregMatch("/<strong>类型：<\/strong>(.*?)<\/a>/si", $moviceHtml);
                    if (!empty($typeArr[1])) {
                        $typeInfo = strip_tags($typeArr[1]);
                        $typeInfo = str_replace("&nbsp;","",$typeInfo);
                        $typeInfo = trim($typeInfo);
                        $resultArr["type"] = $this->_getMoviceType($typeInfo);
                    } else {
                        $resultArr["type"] = $this->_getMoviceType("其他");
                    }

                    //图片地址
                    $resultArr["image"] = $this->get_config_value("dy_common_img"); //默认图片

                    //搜索排行棒电影，作标志
                    $movieScoreInfo = array();
                    if ($urlType == "click") {
                        $resultArr['searchType'] = 4;
                        $movieScoreInfo['search'] = $clickTotalInfo[$titleKey];
                        $movieScoreInfo['link'] = $titleVal;
                        $movieScoreInfo['type'] = 4;
                    }
                    //读取信息是否存在
                    $info = $this->_getMoviceInfoByIdAndType($resultArr['webId'], $resultArr['webType']);
                    if (!empty($info)) { //信息已存在
                        if ($info['del'] == 1) {
                            //获取电影被合并信息
                            $delInfo = $this->_getDelMoviceInfoById($info['id']);
                            if (!empty($delInfo) && $urlType == "click") {
                                $info['id'] = $delInfo['currentInfoId'];
                                //获取电影评分信息
                                $movieLastScoreInfo = $this->_getMoviceScoreInfoByInfoId($info['id'],4,"tbl_movieSearch");
                                if (empty($movieLastScoreInfo)) {//为空，则插入
                                    $movieScoreInfo['infoId'] = $info['id'];
                                    $movieScoreInfo['createTime'] = time();
                                    $searchId = $this->_insertMovieScoreInfo($movieScoreInfo,"tbl_movieSearch");
                                    if (empty($searchId)) {
                                        var_dump("搜索表插入失败--已存在{$titleVal}\n");
                                        var_dump($movieScoreInfo);
                                    }
                                } else {
                                    $movieScoreInfo['upTime'] = time();
                                    $this->_updateMovieScoreInfo($info['id'],$movieScoreInfo,"tbl_movieSearch");
                                }
                            }
                        } else {
                            //获取差异信息
                            $deInfo = $this->_getComDetailInfo($info,$resultArr);
                            //更新电影信息
                            $this->_updateDetailInfo($info['id'],$deInfo);
                            if ($urlType == "click") {
                                //获取电影评分信息
                                $movieLastScoreInfo = $this->_getMoviceScoreInfoByInfoId($info['id'],4,"tbl_movieSearch");
                                if (empty($movieLastScoreInfo)) {//为空，则插入
                                    $movieScoreInfo['infoId'] = $info['id'];
                                    $movieScoreInfo['createTime'] = time();
                                    $searchId = $this->_insertMovieScoreInfo($movieScoreInfo,"tbl_movieSearch");
                                    if (empty($searchId)) {
                                        var_dump("搜索表插入失败--已存在{$titleVal}\n");
                                        var_dump($movieScoreInfo);
                                    }
                                } else {
                                    $movieScoreInfo['upTime'] = time();
                                    $this->_updateMovieScoreInfo($info['id'],$movieScoreInfo,"tbl_movieSearch");
                                }
                            }
                        }
                    } else {
                        //写入数据库 todo
                        $insertRes = $this->_insertMoviceDetailInfo($resultArr);
                        if (!empty($insertRes) && !is_array($insertRes)) {
                            $this->_addWaterToImg($resultArr['image']);
                        }
                        if (!empty($insertRes) && !is_array($insertRes)) {
                            if ($urlType == "click") {
                                $movieScoreInfo['infoId'] = $insertRes;
                                $movieScoreInfo['createTime'] = time();
                                $searchId = $this->_insertMovieScoreInfo($movieScoreInfo,"tbl_movieSearch");
                                if (empty($searchId)) {
                                    var_dump("搜索表插入失败--没存在{$titleVal}\n");
                                    var_dump($movieScoreInfo);
                                }
                            }
                            var_dump("插入成功！" . date('Y-m-d H:i:s') . "\n");
                        } else {
                            var_dump("插入失败 {$moviceIdArr[0]} --- {$titleVal}！" . date('Y-m-d H:i:s') . "\n");
                            var_dump($insertRes);
                        }
                    }
                    sleep(1);
                }
            }
        }
    }
}

$c = new Getmovice();
$c->run();
