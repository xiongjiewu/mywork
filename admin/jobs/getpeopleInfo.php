<?php
/**
 *  人物信息抓取
 * added by xiongjiewu at 2013-06-23
 */
include("jobBase.php");
class getpeopleInfo extends jobBase {
    private $_idFile;
    private $_webConfigInfo;

    private $_filePath;
    private $_G;
    private $_j;

    //百度搜索链接
    private $_baiduSearchLink = "http://www.baidu.com/s?wd={A}&rsv_spt=1&issp=1&rsv_bp=0&ie=utf-8&tn=baiduhome_pg&rsv_n=2";
    //mtime剧照链接
    private $_mtimePhotoLink = "http://people.mtime.com/{A}/photo_gallery/member_score/";
    //mtime图片地址
    private $_mtimeImg = "http://img31.mtime.cn/ph/1100/{A}/{A}_300X500.jpg";

    function __construct() {
        parent::__construct();
        $this->_idFile = $this->get_config_value("zhuaqu_people_id_file_path","peoplelink"); //当前配置文件，读取控制抓取网站
        $this->_webConfigInfo = $this->get_config_value("zhuaqu_web_info","peoplelink");
        $this->_filePath = $this->get_config_value("zhuaqu_people_error_log","peoplelink");
        $this->_G = date("G");
        $this->_j = date("j");
    }
    public function run() {
        global $argv;
        //手动输入控制，webname+urlType+start+end
        if (!empty($argv[2]) && !empty($argv[3]) && !empty($argv[4]) && !empty($argv[5])) {;
            $urlType = $argv[3];
            $start = intval($argv[4]);
            $end = intval($argv[5]);
            $url = $this->_webConfigInfo[$argv[2]][$urlType][0]["base_url"];
            $functionName = "_" . $argv[2];
            if (!empty($url)) {
                for($i = $start;$i <= $end;$i++) {
                    $realUrl = str_replace("{A}",$i,$url);
                    $this->$functionName($realUrl, $urlType);
                }
            }
        } else {
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
                            $start = $key[$nextIndex]['url_1']['start'];
                            file_put_contents($this->_idFile, json_encode(array("name" => $nextWebName, "urlType" => "url_1","fenye" => $start)));
                        } else  {
                            file_put_contents($this->_idFile, json_encode(array("name" => "END", "urlType" => "END","fenye" => 892742))); //作执行结束标志
                        }
                    }
                }
            } elseif ($this->_j == 27 && $this->_G == 1) {//每月27号凌晨1点开始跑
                file_put_contents($this->_idFile, json_encode(array("name" => "mtime", "urlType" => "url_1","fenye" => 892742)));
            } else {
                //do nothing
            }
        }
        exit;
    }

    /**
     * 抓取时光网人物信息
     * @param $url
     * @param $urlType
     * @return bool
     */
    private function _mtime($url, $urlType) {
        if (empty($url) || empty($urlType)) {
            return false;
        }
        //$url = "http://people.mtime.com/914869/";//测试待删除 todo
        $moviceInfoHtml = $this->_getCurlInfo($url);
        if (strpos($moviceInfoHtml,"很抱歉，你要访问的页面不存在") !== false || strlen($moviceInfoHtml) < 3000) {
            file_put_contents("/home/www/logs/dianying/zhuaqu_people_id.id",$url);
            return false;
        }

        //处理信息开始
        $resultArr = array();

        //来源信息
        $idArr = $this->_getPregMatch("/[0-9]+/", $url);
        $resultArr['webId'] = $idArr[0];
        $resultArr['webType'] = $this->_webConfigInfo['mtime']['type'];

        //人名
        $titleArr = $this->_getPregMatch("/<title>(.*?)<\/title>/si", $moviceInfoHtml);
        if (!empty($titleArr[1])) {
            $nameArr = explode(" ",$titleArr[1]);
            $resultArr['name'] = trim($nameArr[0]);
            if (!empty($nameArr[1])) {
                unset($nameArr[0]);
                $resultArr['EnglishName'] = implode(" ",$nameArr);
            }
        }

        //性别、生日、出生地、星座信息
        $peopleBaseInfo = $this->_getPregMatch("/<p class=\"lh18\">(.*?)<\/p>/si",$moviceInfoHtml);
        if (!empty($peopleBaseInfo[1])) {
            $baseInfo = str_replace("<br />","，",$peopleBaseInfo[1]);
            $baseInfo = strip_tags($baseInfo);
            $baseInfoArr = explode("，",$baseInfo);
            foreach($baseInfoArr as $baseVal) {
                $baseVal = trim($baseVal);
                if (empty($baseVal)) {
                    continue;
                }

                //性别
                if ($baseVal == "女") {
                    $resultArr['sex'] = 2;
                } elseif ($baseVal == "男") {
                    $resultArr['sex'] = 1;
                } elseif (strpos($baseVal,"生于") !== false) {//生日
                    $birthdayArr = $this->_getPregMatchAll("/[0-9]+/", $baseVal);
                    if (!empty($birthdayArr[0])) {
                        $resultArr['birthday'] = implode("",$birthdayArr[0]);
                    }
                } elseif (strpos($baseVal,"星座") !== false) {//星座
                    $resultArr['constellatory'] = $this->_getXingZuoType($baseVal);
                } elseif(empty($resultArr['birthplace'])) {//出生地
                    $resultArr['birthplace'] = $baseVal;
                }
            }
        }

        //身高
        $shengaoInfo = $this->_getPregMatch("/<strong>身高：<\/strong>(.*?)<\/li>/si", $moviceInfoHtml);
        if (!empty($shengaoInfo[1])) {
            $shengao = strip_tags($shengaoInfo[1]);
            $shengaoArr = $this->_getPregMatch("/[0-9]+/", $shengao);
            $resultArr['height'] = trim($shengaoArr[0]);
        }

        //获奖记录
        $huojiangJilu = $this->_getPregMatch("/<h2>(.*?)获奖记录(.*?)<\/ul>/si", $moviceInfoHtml);
        if (!empty($huojiangJilu[2])) {
            $huojiangArr = explode("</li>",$huojiangJilu[2]);
            $jiangArr = array();
            foreach($huojiangArr as $huojiangVal) {
                //做链接标志，到时候替换成我们自己的链接
                $huojiangVal = preg_replace("/<a(.*?)>(.*?)<\/a>/si",'[DY]$2[DY]',$huojiangVal);
                $huojiangVal = str_replace("&nbsp;","",$huojiangVal);
                $huojiangVal = str_replace("&#12288;","",$huojiangVal);
                $huojiangVal = trim(strip_tags($huojiangVal));
                if (empty($huojiangVal)) {
                    continue;
                }
                $jiangArr[] = $huojiangVal;
            }
            if (!empty($jiangArr)) {
                $resultArr['awardRecording'] = json_encode($jiangArr);
            }
        }

        if (strpos($moviceInfoHtml,"更多生平") !== false) {
            //生平链接
            $shengpingLink = "http://people.mtime.com/{$resultArr['webId']}/details.html";
            $detailHtml = $this->_getCurlInfo($shengpingLink);
            //获奖记录
            $shengpingInfo = $this->_getPregMatch("/<p class=\"mt20\">(.*?)<\/p>/si", $detailHtml);
            if (!empty($shengpingInfo[1])) {
                $resultArr['jieshao'] = trim(strip_tags($shengpingInfo[1]));
                $resultArr['jieshao'] = str_replace("　　","",$resultArr['jieshao']);
            }
        }

        //首字母
        $firstLetter = $this->getFirstLetter($resultArr['name']);
        if (!empty($firstLetter)) {
            $resultArr['firstLetter'] = $firstLetter;
        }

        //百科链接
        $baiduSearchLink = $this->_baiduSearchLink;
        $baiduSearchLink = str_replace("{A}",$resultArr['name'],$baiduSearchLink);//拼接百度搜索链接
        $baiduHmtl = $this->_getCurlInfo($baiduSearchLink);
        $shengpingInfo = $this->_getPregMatch("/<div class=\"op_zx_renwu_baike_content\">(.*?)<h2>(.*?)<\/h2>/si", $baiduHmtl);
        if (empty($shengpingInfo[2])) {
            $shengpingInfo = $this->_getPregMatch("/{$resultArr['name']}_百度百科(.*?)}/si", $baiduHmtl);
            if (!empty($shengpingInfo[1])) {
                $shengpingInfo[2] = $shengpingInfo[1];
                $shengpingInfo[2] = str_replace("link : ","href=",$shengpingInfo[2]);
                $shengpingInfo[2] = str_replace("'",'"',$shengpingInfo[2]) . "_百度百科";
            }
        }
        if (!empty($shengpingInfo[2]) && strpos($shengpingInfo[2],"_百度百科") !== false) {
            $baikeLink = $this->_getPregMatch("/href=\"(.*?)\"/si",$shengpingInfo[2]);
            if (!empty($baikeLink[1])) {
                //获取百科详细url
                $baiduHmtl = $this->_getCurlInfo($baikeLink[1]);
                if (strpos($baiduHmtl,"The document has moved") !== false) {
                    $baikeLink = $this->_getPregMatch("/href=\"(.*?)\"/si",$baiduHmtl);
                    if (!empty($baikeLink[1])) {
                        $resultArr['baikeLink'] = $baikeLink[1];
                    }
                }
            }
        }

        //剧照
        $juzhaoLink = str_replace("{A}",$resultArr['webId'],$this->_mtimePhotoLink);
        $juzhaoHtml = $this->_getCurlInfo($juzhaoLink);
        $juzhaoTotalArr = array();
        if (!empty($juzhaoHtml)) {
            $juzhaoLinkInfo = $this->_getPregMatchAll("/http:\/\/img[0-9]+\.mtime\.cn\/pi\/[0-9]+\/[0-9]+\/[0-9]+\/[0-9]+\.[0-9]+_[0-9]+X[0-9]+\.jpg/i", $juzhaoHtml, PREG_PATTERN_ORDER);
            if (!empty($juzhaoLinkInfo[0])) {
                $jzLinkArr = $juzhaoLinkInfo[0];
                foreach($jzLinkArr as $linkKey => $linkVal) {
                    //小图片
                    $imgUrl = $linkVal;
                    $imgArr = explode(".", $imgUrl);
                    $imagesName = md5($resultArr['name'] . $linkVal . "_people_juzhao_" . $resultArr['webType'] . "_" . $resultArr['webId']) . "_100X140." . $imgArr[count($imgArr) - 1];
                    $smallPhoto = $this->_downLoadImg($imagesName, $imgUrl); //下载图片并保存,并返回图片地址
                    $juzhaoTotalArr[$linkKey]["type"] = 1;
                    $juzhaoTotalArr[$linkKey]["photo"] = $smallPhoto;
                }
            }
        }
        //图片
        $imgUrl = str_replace("{A}",$resultArr['webId'],$this->_mtimeImg);
        $imgArr = explode(".", $imgUrl);
        $imagesName = md5($resultArr['name'] . $imgUrl . "_people_" . $resultArr['webType'] . "_" . $resultArr['webId']) . "." .$imgArr[count($imgArr) - 1];
        $resultArr['photo'] = $this->_downLoadImg($imagesName, $imgUrl); //下载图片并保存,并返回图片地址

        $info = $this->_getInfo(array("webId" => $resultArr['webId'],"webType" => $resultArr['webType']),"one","tbl_character");
        if (!empty($info) && $info['del'] == 0) {//信息已存在且没有被删除
            //更新
            $this->_updateInfo(array("id" => $info['id']),$resultArr,"tbl_character");
            //查询剧照是否存在
            $juzhaoinfo = $this->_getInfo(array("webId" => $resultArr['webId'],"webType" => $resultArr['webType']),"one","tbl_characterImg");
            if (empty($juzhaoinfo) && !empty($juzhaoTotalArr)) {//不存在剧照则插入
                foreach($juzhaoTotalArr as $juzhaoVal) {
                    $juzhaoVal['characterId'] = $info['id'];
                    $juzhaoVal['name'] = $resultArr['name'];
                    $juzhaoVal['EnglishName'] = $resultArr['EnglishName'];
                    $juzhaoVal['webId'] = $resultArr['webId'];
                    $juzhaoVal['webType'] = $resultArr['webType'];
                    $this->_insertInfo($juzhaoVal,"tbl_characterImg");
                }
            }
        } else {//插入
            $lastId = $this->_insertInfo($resultArr,"tbl_character");
            if (empty($lastId)) {
                var_dump("插入失败" . $resultArr['webId'] . "\n");
            } else {
                var_dump("插入成功" . $lastId . "\n");
                //插入剧照
                if(!empty($juzhaoTotalArr)) {
                    foreach($juzhaoTotalArr as $juzhaoVal) {
                        $juzhaoVal['characterId'] = $lastId;
                        $juzhaoVal['name'] = $resultArr['name'];
                        $juzhaoVal['EnglishName'] = $resultArr['EnglishName'];
                        $juzhaoVal['webId'] = $resultArr['webId'];
                        $juzhaoVal['webType'] = $resultArr['webType'];
                        $this->_insertInfo($juzhaoVal,"tbl_characterImg");
                    }
                }
            }
        }
        return true;
    }

    /**
     * 获取星座对应的key
     * @param $xingzuo
     * @return int|string
     */
    private function _getXingZuoType($xingzuo) {
        $xingzuo = trim($xingzuo);
        if (empty($xingzuo)) {
            return 0;
        }
        $xingzuoInfo = $this->get_config_value("constellatoryInfo");
        foreach($xingzuoInfo as $xingzuoKey => $xingzuoVal) {
            if (strpos($xingzuo,"宝瓶座") !== false) {
                return 11;
            } elseif (strpos($xingzuo,"人马座") !== false) {
                return 9;
            } elseif (strpos($xingzuo,"室女座") !== false) {
                return 6;
            } elseif (strpos($xingzuo,$xingzuoVal) !== false) {
                return $xingzuoKey;
            }
        }
        return 0;
    }
}
$doo = new getpeopleInfo();
$doo->run();
