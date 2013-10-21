<?php
/**
 *  奖项信息抓取
 * added by xiongjiewu at 2013-06-23
 */
include("jobBase.php");
class getPrizeInfo extends jobBase {
    private $_idFile;
    private $_webConfigInfo;

    private $_filePath;
    private $_G;

    //百度搜索链接
    private $_baiduSearchLink = "http://www.baidu.com/s?wd={A}&rsv_spt=1&issp=1&rsv_bp=0&ie=utf-8&tn=baiduhome_pg&rsv_n=2";
    //mtime剧照链接
    private $_mtimePhotoLink = "http://people.mtime.com/{A}/photo_gallery/member_score/";
    //mtime图片地址
    private $_mtimeImg = "http://img31.mtime.cn/ph/1100/{A}/{A}_96X128.jpg";

    function __construct() {
        parent::__construct();
        $this->_idFile = $this->get_config_value("zhuaqu_prize_id_file_path","prizelink"); //当前配置文件，读取控制抓取网站
        $this->_webConfigInfo = $this->get_config_value("zhuaqu_web_info","prizelink");
        $this->_filePath = $this->get_config_value("zhuaqu_prize_error_log","prizelink");
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
                        if (!empty($url['fenye'])) {//url是分页的
                            if (!empty($webname['fenye']) && (intval($webname['fenye']) > $url['start'])) {
                                $url['start'] = $webname['fenye'];
                            }
                            for($i = $url['start'];$i <= $url['end'];$i++) {
                                file_put_contents($this->_idFile, json_encode(array("name" => $webname['name'], "urlType" => $webname['urlType'],"fenye" => $i))); //作执行结束标志
                                $realUrl = str_replace("{A}",$i,$url['base_url']);
                                $this->$functionName($realUrl, $webname['urlType'],$i);
                            }
                        } else {

                        }
                    }
                }

                $currentVal = $webConfigInfo[$webname['name']][$webname['urlType']];
                $currentWebName = $webname['name'];
                $key = array_keys($webConfigInfo[$webname['name']]);
                $val = array_values($webConfigInfo[$webname['name']]);
                $nextIndex = array_search($currentVal,$val) + 1;
                if (!empty($key[$nextIndex])) {//下一个元素存在
                    $start = $key[$nextIndex]['url'][0]['start'];
                    file_put_contents($this->_idFile, json_encode(array("name" => $currentWebName, "urlType" => $key[$nextIndex],"fenye" => $start)));
                } else {
                    $currentVal = $webConfigInfo[$webname['name']];
                    $key = array_keys($webConfigInfo);
                    $val = array_values($webConfigInfo);
                    $nextIndex = array_search($currentVal,$val) + 1;
                    if (!empty($key[$nextIndex])) {//下一个网站
                        $nextWebName = $key[$nextIndex];
                        $start = $key[$nextIndex]['url'][0]['start'];
                        file_put_contents($this->_idFile, json_encode(array("name" => $nextWebName, "urlType" => "name","fenye" => $start)));
                    } else  {
                        file_put_contents($this->_idFile, json_encode(array("name" => "END", "urlType" => "END","fenye" => 1929)));//作执行结束标志
                    }
                }
            }
        } elseif ($this->_G == 18) {//每天晚上8点开始跑
            file_put_contents($this->_idFile, json_encode(array("name" => "academyAward", "urlType" => "url","fenye" => 1929)));
        } else {
            //do nothing
        }
        exit;
    }

    /**
     * 抓取奥斯卡信息主函数
     * @param $url
     * @param $urlType
     * @return bool
     */
    private function _academyAward($url, $urlType,$nianfen) {
        if (empty($url) || empty($urlType) || empty($nianfen)) {
            return false;
        }

        $moviceInfoHtml = $this->_getCurlInfo($url);

        //最佳影片
        $moviceNameInfo = $this->_getPregMatch("/最佳影片&nbsp;Best Picture(.*?)<\/dd>/si",$moviceInfoHtml);
        if (empty($moviceNameInfo[1])) {
            return false;
        }
        $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
        if (empty($urlInfo[0])) {
            return false;
        }
        //最佳影片链接数组+插入信息
        $urlArr = array_unique($urlInfo[0]);
        $bestAwardInfo['nianFen'] = $nianfen;
        $bestAwardInfo['prizeType'] = 1;//奥斯卡
        $bestAwardInfo['movieType'] = 1;//最佳影片
        $bestAwardInfo['prizeMovieType'] = 1;//获奖得主
        $bestAwardInfo['commentText'] = "奥斯卡最佳影片";//获奖得主
        $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
        $this->_inserAwardMovieInfo($urlArr[0],$bestAwardInfo);

        //最佳影片被提名影片信息
        $moviceNameInfo = $this->_getPregMatch("/最佳影片&nbsp;Best Picture(.*?)Best/si",$moviceInfoHtml);
        if (!empty($moviceNameInfo[1])) {
            $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
            $timingBestUrlArr = array_unique($urlInfo[0]);
            foreach($timingBestUrlArr as $url) {
                if ($url == $urlArr[0]) {
                    continue;
                }
                $bestAwardInfo['nianFen'] = $nianfen;
                $bestAwardInfo['prizeType'] = 1;//奥斯卡
                $bestAwardInfo['movieType'] = 1;//最佳影片
                $bestAwardInfo['prizeMovieType'] = 2;//被提名
                $bestAwardInfo['commentText'] = "奥斯卡最佳影片被提名影片";
                $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
                $this->_inserAwardMovieInfo($url,$bestAwardInfo);
            }
        }

        //最佳导演
        if ($nianfen == 1929) {//1929年特殊处理
            //奥斯卡最佳喜剧类导演
            $bestDaoYanArr['nianFen'] = 1929;
            $bestDaoYanArr['prizeType'] = 1;
            $bestDaoYanArr['personType'] = 3;//奥斯卡最佳导演
            $bestDaoYanArr['prizePersonType'] = 1;//奥斯卡最佳导演
            $bestDaoYanArr['commentText'] = "奥斯卡最佳喜剧类导演";
            $bestDaoYanArr['volume'] = 1;
            $bestDaoYanArr['createTime'] = time();
            $this->_inserAwardPeopleInfo("people.mtime.com/894046/",$bestDaoYanArr,array("movie.mtime.com/121602/"));

            //奥斯卡最佳喜剧类导演被提名
            $bestDaoYanArr1['nianFen'] = 1929;
            $bestDaoYanArr1['prizeType'] = 1;
            $bestDaoYanArr1['personType'] = 3;//奥斯卡最佳导演
            $bestDaoYanArr1['prizePersonType'] = 2;//奥斯卡最佳导演被提名
            $bestDaoYanArr1['commentText'] = "奥斯卡最佳喜剧类被提名导演";
            $bestDaoYanArr1['volume'] = 1;
            $bestDaoYanArr1['createTime'] = time();
            $this->_inserAwardPeopleInfo("people.mtime.com/893916/",$bestDaoYanArr1,array("movie.mtime.com/111625/"));

            //奥斯卡最佳剧情类导演被提名
            $bestDaoYanArr2['nianFen'] = 1929;
            $bestDaoYanArr2['prizeType'] = 1;
            $bestDaoYanArr2['personType'] = 3;//奥斯卡最佳导演
            $bestDaoYanArr2['prizePersonType'] = 1;//奥斯卡最佳导演
            $bestDaoYanArr2['commentText'] = "奥斯卡最佳剧情类导演";
            $bestDaoYanArr2['volume'] = 1;
            $bestDaoYanArr2['createTime'] = time();
            $this->_inserAwardPeopleInfo("people.mtime.com/894016/",$bestDaoYanArr2,array("movie.mtime.com/14721/"));

            //奥斯卡最佳剧情类导演
            $bestDaoYanArr3['nianFen'] = 1929;
            $bestDaoYanArr3['prizeType'] = 1;
            $bestDaoYanArr3['personType'] = 3;//奥斯卡最佳导演
            $bestDaoYanArr3['prizePersonType'] = 2;//奥斯卡最佳导演
            $bestDaoYanArr3['commentText'] = "奥斯卡最佳剧情类被提名导演";
            $bestDaoYanArr3['volume'] = 1;
            $bestDaoYanArr3['createTime'] = time();
            $this->_inserAwardPeopleInfo("people.mtime.com/893917/",$bestDaoYanArr3,array("movie.mtime.com/157662/"));

            //奥斯卡最佳剧情类导演
            $bestDaoYanArr4['nianFen'] = 1929;
            $bestDaoYanArr4['prizeType'] = 1;
            $bestDaoYanArr4['personType'] = 3;//奥斯卡最佳导演
            $bestDaoYanArr4['prizePersonType'] = 2;//奥斯卡最佳导演
            $bestDaoYanArr4['commentText'] = "奥斯卡最佳剧情类被提名导演";
            $bestDaoYanArr4['volume'] = 1;
            $bestDaoYanArr4['createTime'] = time();
            $this->_inserAwardPeopleInfo("people.mtime.com/893954/",$bestDaoYanArr4,array("movie.mtime.com/18769/"));
        } else {
            $bestDaoYanInfo = $this->_getPregMatch("/最佳导演&nbsp;Best Directing(.*?)<\/dd>/si",$moviceInfoHtml);
            if (!empty($bestDaoYanInfo[1])) {
                $daoyanMovieUrlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $bestDaoYanInfo[1], PREG_PATTERN_ORDER);
                $daoyanMovieUrlInfo = array_unique($daoyanMovieUrlInfo[0]);
                $daoyanPeopleUrlInfo = $this->_getPregMatchAll("|people\.mtime\.com\/[0-9]+\/|", $bestDaoYanInfo[1], PREG_PATTERN_ORDER);
                $daoyanPeopleUrlInfo = array_unique($daoyanPeopleUrlInfo[0]);
                //奥斯卡最佳导演
                $bestDaoYanArr['nianFen'] = $nianfen;
                $bestDaoYanArr['prizeType'] = 1;
                $bestDaoYanArr['personType'] = 3;//奥斯卡最佳导演
                $bestDaoYanArr['prizePersonType'] = 1;//奥斯卡最佳导演
                $bestDaoYanArr['commentText'] = "奥斯卡最佳导演";
                $bestDaoYanArr['volume'] = $nianfen - 1928;
                $bestDaoYanArr['createTime'] = time();
                $this->_inserAwardPeopleInfo($daoyanPeopleUrlInfo[0],$bestDaoYanArr,$daoyanMovieUrlInfo);

                //奥斯卡最佳被提名导演信息
                $daoYanNameInfo = $this->_getPregMatch("/最佳导演&nbsp;Best Directing(.*?)Best/si",$moviceInfoHtml);
                if (!empty($daoYanNameInfo[1])) {
                    $urlInfo = $this->_getPregMatchAll("|people\.mtime\.com\/[0-9]+\/|", $daoYanNameInfo[1], PREG_PATTERN_ORDER);
                    $movieInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $daoYanNameInfo[1], PREG_PATTERN_ORDER);
                    $timingBestUrlArr = array_values(array_unique($urlInfo[0]));
                    $timingMovieUrlArr = array_values(array_unique($movieInfo[0]));
                    foreach($timingBestUrlArr as $urlKey => $url) {
                        $bestDaoYanArr = array();
                        if ($url == $daoyanPeopleUrlInfo[0]) {
                            continue;
                        }
                        //奥斯卡最佳被提名导演
                        $bestDaoYanArr['nianFen'] = $nianfen;
                        $bestDaoYanArr['prizeType'] = 1;
                        $bestDaoYanArr['personType'] = 3;//奥斯卡最佳导演
                        $bestDaoYanArr['prizePersonType'] = 2;//奥斯卡最佳导演
                        $bestDaoYanArr['commentText'] = "奥斯卡最佳被提名导演";
                        $bestDaoYanArr['volume'] = $nianfen - 1928;
                        $bestDaoYanArr['createTime'] = time();
                        $this->_inserAwardPeopleInfo($url,$bestDaoYanArr,array($timingMovieUrlArr[$urlKey]));
                    }
                }
            }
        }

        //最佳男主角
        $bestNanZhuYanInfo = $this->_getPregMatch("/最佳男主角&nbsp;Best Actor(.*?)<\/dd>/si",$moviceInfoHtml);
        if (!empty($bestNanZhuYanInfo[1])) {
            $zhuyanMovieUrlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $bestNanZhuYanInfo[1], PREG_PATTERN_ORDER);
            $zhuyanMovieUrlInfo = array_unique($zhuyanMovieUrlInfo[0]);
            $zhuyanPeopleUrlInfo = $this->_getPregMatchAll("|people\.mtime\.com\/[0-9]+\/|", $bestNanZhuYanInfo[1], PREG_PATTERN_ORDER);
            $zhuyanPeopleUrlInfo = array_unique($zhuyanPeopleUrlInfo[0]);
            //奥斯卡最佳男主角
            $bestZhuYanArr['nianFen'] = $nianfen;
            $bestZhuYanArr['prizeType'] = 1;
            $bestZhuYanArr['personType'] = 1;//奥斯卡最佳男主角
            $bestZhuYanArr['prizePersonType'] = 1;//奥斯卡最佳男主角
            $bestZhuYanArr['commentText'] = "奥斯卡最佳男主角";
            $bestZhuYanArr['volume'] = $nianfen - 1928;
            $bestZhuYanArr['createTime'] = time();
            $this->_inserAwardPeopleInfo($zhuyanPeopleUrlInfo[0],$bestZhuYanArr,$zhuyanMovieUrlInfo);

            //奥斯卡最佳被提名男主角信息
            $zhuYanNameInfo = $this->_getPregMatch("/最佳男主角&nbsp;Best Actor(.*?)Best/si",$moviceInfoHtml);
            if (!empty($zhuYanNameInfo[1])) {
                $urlInfo = $this->_getPregMatchAll("|people\.mtime\.com\/[0-9]+\/|", $zhuYanNameInfo[1], PREG_PATTERN_ORDER);
                $movieInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $zhuYanNameInfo[1], PREG_PATTERN_ORDER);
                $timingBestUrlArr = array_values(array_unique($urlInfo[0]));
                $timingMovieUrlArr = array_values(array_unique($movieInfo[0]));
                foreach($timingBestUrlArr as $urlKey => $url) {
                    $bestDaoYanArr = array();
                    if ($url == $zhuyanPeopleUrlInfo[0]) {
                        continue;
                    }
                    //奥斯卡最佳被提名男主角
                    $bestDaoYanArr['nianFen'] = $nianfen;
                    $bestDaoYanArr['prizeType'] = 1;
                    $bestDaoYanArr['personType'] = 1;//奥斯卡最佳男主角
                    $bestDaoYanArr['prizePersonType'] = 2;//奥斯卡最佳男主角
                    $bestDaoYanArr['commentText'] = "奥斯卡最佳被提名男主角";
                    $bestDaoYanArr['volume'] = $nianfen - 1928;
                    $bestDaoYanArr['createTime'] = time();
                    $this->_inserAwardPeopleInfo($url,$bestDaoYanArr,array($timingMovieUrlArr[$urlKey]));
                }
            }
        }

        //最佳女主角
        $bestNvZhuYanInfo = $this->_getPregMatch("/最佳女主角&nbsp;Best Actress(.*?)<\/dd>/si",$moviceInfoHtml);
        if (!empty($bestNvZhuYanInfo[1])) {
            $zhuyanMovieUrlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $bestNvZhuYanInfo[1], PREG_PATTERN_ORDER);
            $zhuyanMovieUrlInfo = array_unique($zhuyanMovieUrlInfo[0]);
            $zhuyanPeopleUrlInfo = $this->_getPregMatchAll("|people\.mtime\.com\/[0-9]+\/|", $bestNvZhuYanInfo[1], PREG_PATTERN_ORDER);
            $zhuyanPeopleUrlInfo = array_unique($zhuyanPeopleUrlInfo[0]);
            //奥斯卡最佳女主角
            $bestZhuYanArr['nianFen'] = $nianfen;
            $bestZhuYanArr['prizeType'] = 1;
            $bestZhuYanArr['personType'] = 2;//奥斯卡最佳女主角
            $bestZhuYanArr['prizePersonType'] = 1;//奥斯卡最佳女主角
            $bestZhuYanArr['commentText'] = "奥斯卡最佳女主角";
            $bestZhuYanArr['volume'] = $nianfen - 1928;
            $bestZhuYanArr['createTime'] = time();
            $this->_inserAwardPeopleInfo($zhuyanPeopleUrlInfo[0],$bestZhuYanArr,$zhuyanMovieUrlInfo);

            //奥斯卡最佳被提名女主角信息
            $zhuYanNameInfo = $this->_getPregMatch("/最佳女主角&nbsp;Best Actress(.*?)Best/si",$moviceInfoHtml);
            if (!empty($zhuYanNameInfo[1])) {
                $urlInfo = $this->_getPregMatchAll("|people\.mtime\.com\/[0-9]+\/|", $zhuYanNameInfo[1], PREG_PATTERN_ORDER);
                $movieInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $zhuYanNameInfo[1], PREG_PATTERN_ORDER);
                $timingBestUrlArr = array_values(array_unique($urlInfo[0]));
                $timingMovieUrlArr = array_values(array_unique($movieInfo[0]));
                foreach($timingBestUrlArr as $urlKey => $url) {
                    $bestDaoYanArr = array();
                    if ($url == $zhuyanPeopleUrlInfo[0]) {
                        continue;
                    }
                    //奥斯卡最佳被提名男主角
                    $bestDaoYanArr['nianFen'] = $nianfen;
                    $bestDaoYanArr['prizeType'] = 1;
                    $bestDaoYanArr['personType'] = 2;//奥斯卡最佳女主角
                    $bestDaoYanArr['prizePersonType'] = 2;//奥斯卡最佳女主角
                    $bestDaoYanArr['commentText'] = "奥斯卡最佳被提名女主角";
                    $bestDaoYanArr['volume'] = $nianfen - 1928;
                    $bestDaoYanArr['createTime'] = time();
                    $this->_inserAwardPeopleInfo($url,$bestDaoYanArr,array($timingMovieUrlArr[$urlKey]));
                }
            }
        }

        //最佳男配角
        $bestNanPeiJueInfo = $this->_getPregMatch("/最佳男配角&nbsp;Best Actor(.*?)<\/dd>/si",$moviceInfoHtml);
        if (!empty($bestNanPeiJueInfo[1])) {
            $zhuyanMovieUrlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $bestNanPeiJueInfo[1], PREG_PATTERN_ORDER);
            $zhuyanMovieUrlInfo = array_unique($zhuyanMovieUrlInfo[0]);
            $zhuyanPeopleUrlInfo = $this->_getPregMatchAll("|people\.mtime\.com\/[0-9]+\/|", $bestNanPeiJueInfo[1], PREG_PATTERN_ORDER);
            $zhuyanPeopleUrlInfo = array_unique($zhuyanPeopleUrlInfo[0]);
            //奥斯卡最佳男配角
            $bestZhuYanArr['nianFen'] = $nianfen;
            $bestZhuYanArr['prizeType'] = 1;
            $bestZhuYanArr['personType'] = 4;//奥斯卡最佳男配角
            $bestZhuYanArr['prizePersonType'] = 1;//奥斯卡最佳男配角
            $bestZhuYanArr['commentText'] = "奥斯卡最佳男配角";
            $bestZhuYanArr['volume'] = $nianfen - 1928;
            $bestZhuYanArr['createTime'] = time();
            $this->_inserAwardPeopleInfo($zhuyanPeopleUrlInfo[0],$bestZhuYanArr,$zhuyanMovieUrlInfo);

            //奥斯卡最佳被提名男配角信息
            $zhuYanNameInfo = $this->_getPregMatch("/最佳男配角&nbsp;Best Actor(.*?)Best/si",$moviceInfoHtml);
            if (!empty($zhuYanNameInfo[1])) {
                $urlInfo = $this->_getPregMatchAll("|people\.mtime\.com\/[0-9]+\/|", $zhuYanNameInfo[1], PREG_PATTERN_ORDER);
                $movieInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $zhuYanNameInfo[1], PREG_PATTERN_ORDER);
                $timingBestUrlArr = array_values(array_unique($urlInfo[0]));
                $timingMovieUrlArr = array_values(array_unique($movieInfo[0]));
                foreach($timingBestUrlArr as $urlKey => $url) {
                    $bestDaoYanArr = array();
                    if ($url == $zhuyanPeopleUrlInfo[0]) {
                        continue;
                    }
                    //奥斯卡最佳被提名男主角
                    $bestDaoYanArr['nianFen'] = $nianfen;
                    $bestDaoYanArr['prizeType'] = 4;
                    $bestDaoYanArr['personType'] = 1;//奥斯卡最佳男主角
                    $bestDaoYanArr['prizePersonType'] = 2;//奥斯卡最佳男主角
                    $bestDaoYanArr['commentText'] = "奥斯卡最佳被提名男主角";
                    $bestDaoYanArr['volume'] = $nianfen - 1928;
                    $bestDaoYanArr['createTime'] = time();
                    $this->_inserAwardPeopleInfo($url,$bestDaoYanArr,array($timingMovieUrlArr[$urlKey]));
                }
            }
        }

        //最佳女配角
        $bestNvPeiJueInfo = $this->_getPregMatch("/最佳女配角&nbsp;Best Actress(.*?)<\/dd>/si",$moviceInfoHtml);
        if (!empty($bestNvPeiJueInfo[1])) {
            $zhuyanMovieUrlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $bestNvPeiJueInfo[1], PREG_PATTERN_ORDER);
            $zhuyanMovieUrlInfo = array_unique($zhuyanMovieUrlInfo[0]);
            $zhuyanPeopleUrlInfo = $this->_getPregMatchAll("|people\.mtime\.com\/[0-9]+\/|", $bestNvPeiJueInfo[1], PREG_PATTERN_ORDER);
            $zhuyanPeopleUrlInfo = array_unique($zhuyanPeopleUrlInfo[0]);
            //奥斯卡最佳女主角
            $bestZhuYanArr['nianFen'] = $nianfen;
            $bestZhuYanArr['prizeType'] = 1;
            $bestZhuYanArr['personType'] = 5;//奥斯卡最佳女配角
            $bestZhuYanArr['prizePersonType'] = 1;//奥斯卡最佳女配角
            $bestZhuYanArr['commentText'] = "奥斯卡最佳女配角";
            $bestZhuYanArr['volume'] = $nianfen - 1928;
            $bestZhuYanArr['createTime'] = time();
            $this->_inserAwardPeopleInfo($zhuyanPeopleUrlInfo[0],$bestZhuYanArr,$zhuyanMovieUrlInfo);

            //奥斯卡最佳被提名女配角信息
            $zhuYanNameInfo = $this->_getPregMatch("/最佳女配角&nbsp;Best Actress(.*?)Best/si",$moviceInfoHtml);
            if (!empty($zhuYanNameInfo[1])) {
                $urlInfo = $this->_getPregMatchAll("|people\.mtime\.com\/[0-9]+\/|", $zhuYanNameInfo[1], PREG_PATTERN_ORDER);
                $movieInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $zhuYanNameInfo[1], PREG_PATTERN_ORDER);
                $timingBestUrlArr = array_values(array_unique($urlInfo[0]));
                $timingMovieUrlArr = array_values(array_unique($movieInfo[0]));
                foreach($timingBestUrlArr as $urlKey => $url) {
                    $bestDaoYanArr = array();
                    if ($url == $zhuyanPeopleUrlInfo[0]) {
                        continue;
                    }
                    //奥斯卡最佳被提名男主角
                    $bestDaoYanArr['nianFen'] = $nianfen;
                    $bestDaoYanArr['prizeType'] = 1;
                    $bestDaoYanArr['personType'] = 5;//奥斯卡最佳女配角
                    $bestDaoYanArr['prizePersonType'] = 2;//奥斯卡最佳女配角
                    $bestDaoYanArr['commentText'] = "奥斯卡最佳被提名女配角";
                    $bestDaoYanArr['volume'] = $nianfen - 1928;
                    $bestDaoYanArr['createTime'] = time();
                    $this->_inserAwardPeopleInfo($url,$bestDaoYanArr,array($timingMovieUrlArr[$urlKey]));
                }
            }
        }


        //最佳原创剧本
        $moviceNameInfo = $this->_getPregMatch("/最佳原创剧本&nbsp;Best Writing(.*?)<\/dd>/si",$moviceInfoHtml);
        if (!empty($moviceNameInfo[1])) {
            $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
            //最佳原创剧本链接数组+插入信息
            $urlArr = array_unique($urlInfo[0]);
            $bestAwardInfo['nianFen'] = $nianfen;
            $bestAwardInfo['prizeType'] = 1;//奥斯卡
            $bestAwardInfo['movieType'] = 2;//原创剧本
            $bestAwardInfo['prizeMovieType'] = 1;//获奖得主
            $bestAwardInfo['commentText'] = "奥斯卡最佳原创剧本";//获奖得主
            $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
            $this->_inserAwardMovieInfo($urlArr[0],$bestAwardInfo);

            //最佳原创剧本被提名影片信息
            $moviceNameInfo = $this->_getPregMatch("/最佳原创剧本&nbsp;Best Writing(.*?)Best/si",$moviceInfoHtml);
            if (!empty($moviceNameInfo[1])) {
                $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
                $timingBestUrlArr = array_unique($urlInfo[0]);
                foreach($timingBestUrlArr as $url) {
                    if ($url == $urlArr[0]) {
                        continue;
                    }
                    $bestAwardInfo['nianFen'] = $nianfen;
                    $bestAwardInfo['prizeType'] = 1;//奥斯卡
                    $bestAwardInfo['movieType'] = 2;//原创剧本
                    $bestAwardInfo['prizeMovieType'] = 2;//被提名
                    $bestAwardInfo['commentText'] = "奥斯卡最佳原创剧本被提名影片";
                    $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
                    $this->_inserAwardMovieInfo($url,$bestAwardInfo);
                }
            }
        }

        //最佳改编剧本
        $moviceNameInfo = $this->_getPregMatch("/最佳改编剧本&nbsp;Best Writing(.*?)<\/dd>/si",$moviceInfoHtml);
        if (!empty($moviceNameInfo[1])) {
            $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
            //最佳改编剧本链接数组+插入信息
            $urlArr = array_unique($urlInfo[0]);
            $bestAwardInfo['nianFen'] = $nianfen;
            $bestAwardInfo['prizeType'] = 1;//奥斯卡
            $bestAwardInfo['movieType'] = 3;//最佳改编剧本
            $bestAwardInfo['prizeMovieType'] = 1;//获奖得主
            $bestAwardInfo['commentText'] = "奥斯卡最佳改编剧本";//获奖得主
            $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
            $this->_inserAwardMovieInfo($urlArr[0],$bestAwardInfo);

            //最佳改编剧本被提名影片信息
            $moviceNameInfo = $this->_getPregMatch("/最佳改编剧本&nbsp;Best Writing(.*?)Best/si",$moviceInfoHtml);
            if (!empty($moviceNameInfo[1])) {
                $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
                $timingBestUrlArr = array_unique($urlInfo[0]);
                foreach($timingBestUrlArr as $url) {
                    if ($url == $urlArr[0]) {
                        continue;
                    }
                    $bestAwardInfo['nianFen'] = $nianfen;
                    $bestAwardInfo['prizeType'] = 1;//奥斯卡
                    $bestAwardInfo['movieType'] = 3;//最佳改编剧本
                    $bestAwardInfo['prizeMovieType'] = 2;//被提名
                    $bestAwardInfo['commentText'] = "奥斯卡最佳改编剧本被提名影片";
                    $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
                    $this->_inserAwardMovieInfo($url,$bestAwardInfo);
                }
            }
        }

        //最佳摄影
        $moviceNameInfo = $this->_getPregMatch("/最佳摄影&nbsp;Best Cinematography(.*?)<\/dd>/si",$moviceInfoHtml);
        if (!empty($moviceNameInfo[1])) {
            $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
            //最佳摄影链接数组+插入信息
            $urlArr = array_unique($urlInfo[0]);
            $bestAwardInfo['nianFen'] = $nianfen;
            $bestAwardInfo['prizeType'] = 1;//奥斯卡
            $bestAwardInfo['movieType'] = 4;//最佳摄影
            $bestAwardInfo['prizeMovieType'] = 1;//获奖得主
            $bestAwardInfo['commentText'] = "奥斯卡最佳摄影";//获奖得主
            $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
            $this->_inserAwardMovieInfo($urlArr[0],$bestAwardInfo);

            //最佳摄影被提名影片信息
            $moviceNameInfo = $this->_getPregMatch("/最佳摄影&nbsp;Best Cinematography(.*?)Best/si",$moviceInfoHtml);
            if (!empty($moviceNameInfo[1])) {
                $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
                $timingBestUrlArr = array_unique($urlInfo[0]);
                foreach($timingBestUrlArr as $url) {
                    if ($url == $urlArr[0]) {
                        continue;
                    }
                    $bestAwardInfo['nianFen'] = $nianfen;
                    $bestAwardInfo['prizeType'] = 1;//奥斯卡
                    $bestAwardInfo['movieType'] = 4;//最佳摄影
                    $bestAwardInfo['prizeMovieType'] = 2;//被提名
                    $bestAwardInfo['commentText'] = "奥斯卡最佳摄影被提名影片";
                    $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
                    $this->_inserAwardMovieInfo($url,$bestAwardInfo);
                }
            }
        }

        //最佳电影剪辑
        $moviceNameInfo = $this->_getPregMatch("/最佳电影剪辑&nbsp;Best Achievement(.*?)<\/dd>/si",$moviceInfoHtml);
        if (!empty($moviceNameInfo[1])) {
            $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
            //最佳电影剪辑链接数组+插入信息
            $urlArr = array_unique($urlInfo[0]);
            $bestAwardInfo['nianFen'] = $nianfen;
            $bestAwardInfo['prizeType'] = 1;//奥斯卡
            $bestAwardInfo['movieType'] = 5;//最佳电影剪辑
            $bestAwardInfo['prizeMovieType'] = 1;//获奖得主
            $bestAwardInfo['commentText'] = "奥斯卡最佳电影剪辑";//获奖得主
            $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
            $this->_inserAwardMovieInfo($urlArr[0],$bestAwardInfo);

            //最佳电影剪辑被提名影片信息
            $moviceNameInfo = $this->_getPregMatch("/最佳电影剪辑&nbsp;Best Achievement(.*?)Best/si",$moviceInfoHtml);
            if (!empty($moviceNameInfo[1])) {
                $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
                $timingBestUrlArr = array_unique($urlInfo[0]);
                foreach($timingBestUrlArr as $url) {
                    if ($url == $urlArr[0]) {
                        continue;
                    }
                    $bestAwardInfo['nianFen'] = $nianfen;
                    $bestAwardInfo['prizeType'] = 1;//奥斯卡
                    $bestAwardInfo['movieType'] = 5;//最佳电影剪辑
                    $bestAwardInfo['prizeMovieType'] = 2;//被提名
                    $bestAwardInfo['commentText'] = "奥斯卡最佳电影剪辑被提名影片";
                    $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
                    $this->_inserAwardMovieInfo($url,$bestAwardInfo);
                }
            }
        }

        //最佳艺术指导
        $moviceNameInfo = $this->_getPregMatch("/最佳艺术指导&nbsp;Best Achievement(.*?)<\/dd>/si",$moviceInfoHtml);
        if (!empty($moviceNameInfo[1])) {
            $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
            //最佳艺术指导链接数组+插入信息
            $urlArr = array_unique($urlInfo[0]);
            $bestAwardInfo['nianFen'] = $nianfen;
            $bestAwardInfo['prizeType'] = 1;//奥斯卡
            $bestAwardInfo['movieType'] = 6;//最佳艺术指导
            $bestAwardInfo['prizeMovieType'] = 1;//获奖得主
            $bestAwardInfo['commentText'] = "奥斯卡最佳艺术指导";//获奖得主
            $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
            $this->_inserAwardMovieInfo($urlArr[0],$bestAwardInfo);

            //最佳艺术指导被提名影片信息
            $moviceNameInfo = $this->_getPregMatch("/最佳艺术指导&nbsp;Best Achievement(.*?)Best/si",$moviceInfoHtml);
            if (!empty($moviceNameInfo[1])) {
                $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
                $timingBestUrlArr = array_unique($urlInfo[0]);
                foreach($timingBestUrlArr as $url) {
                    if ($url == $urlArr[0]) {
                        continue;
                    }
                    $bestAwardInfo['nianFen'] = $nianfen;
                    $bestAwardInfo['prizeType'] = 1;//奥斯卡
                    $bestAwardInfo['movieType'] = 6;//最佳艺术指导
                    $bestAwardInfo['prizeMovieType'] = 2;//被提名
                    $bestAwardInfo['commentText'] = "奥斯卡最佳艺术指导被提名影片";
                    $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
                    $this->_inserAwardMovieInfo($url,$bestAwardInfo);
                }
            }
        }

        //最佳视觉效果
        $moviceNameInfo = $this->_getPregMatch("/最佳视觉效果&nbsp;Best Visual(.*?)<\/dd>/si",$moviceInfoHtml);
        if (!empty($moviceNameInfo[1])) {
            $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
            //最佳视觉效果链接数组+插入信息
            $urlArr = array_unique($urlInfo[0]);
            $bestAwardInfo['nianFen'] = $nianfen;
            $bestAwardInfo['prizeType'] = 1;//奥斯卡
            $bestAwardInfo['movieType'] = 7;//最佳视觉效果
            $bestAwardInfo['prizeMovieType'] = 1;//获奖得主
            $bestAwardInfo['commentText'] = "奥斯卡最佳视觉效果";//获奖得主
            $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
            $this->_inserAwardMovieInfo($urlArr[0],$bestAwardInfo);

            //最佳视觉效果被提名影片信息
            $moviceNameInfo = $this->_getPregMatch("/最佳视觉效果&nbsp;Best Visual(.*?)Best/si",$moviceInfoHtml);
            if (!empty($moviceNameInfo[1])) {
                $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
                $timingBestUrlArr = array_unique($urlInfo[0]);
                foreach($timingBestUrlArr as $url) {
                    if ($url == $urlArr[0]) {
                        continue;
                    }
                    $bestAwardInfo['nianFen'] = $nianfen;
                    $bestAwardInfo['prizeType'] = 1;//奥斯卡
                    $bestAwardInfo['movieType'] = 7;//最佳视觉效果
                    $bestAwardInfo['prizeMovieType'] = 2;//被提名
                    $bestAwardInfo['commentText'] = "奥斯卡最佳视觉效果被提名影片";
                    $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
                    $this->_inserAwardMovieInfo($url,$bestAwardInfo);
                }
            }
        }

        //最佳服装设计
        $moviceNameInfo = $this->_getPregMatch("/最佳服装设计&nbsp;Best Achievement(.*?)<\/dd>/si",$moviceInfoHtml);
        if (!empty($moviceNameInfo[1])) {
            $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
            //最佳服装设计链接数组+插入信息
            $urlArr = array_unique($urlInfo[0]);
            $bestAwardInfo['nianFen'] = $nianfen;
            $bestAwardInfo['prizeType'] = 1;//奥斯卡
            $bestAwardInfo['movieType'] = 8;//最佳服装设计
            $bestAwardInfo['prizeMovieType'] = 1;//获奖得主
            $bestAwardInfo['commentText'] = "奥斯卡最佳服装设计";//获奖得主
            $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
            $this->_inserAwardMovieInfo($urlArr[0],$bestAwardInfo);

            //最佳服装设计被提名影片信息
            $moviceNameInfo = $this->_getPregMatch("/最佳服装设计&nbsp;Best Achievement(.*?)Best/si",$moviceInfoHtml);
            if (!empty($moviceNameInfo[1])) {
                $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
                $timingBestUrlArr = array_unique($urlInfo[0]);
                foreach($timingBestUrlArr as $url) {
                    if ($url == $urlArr[0]) {
                        continue;
                    }
                    $bestAwardInfo['nianFen'] = $nianfen;
                    $bestAwardInfo['prizeType'] = 1;//奥斯卡
                    $bestAwardInfo['movieType'] = 8;//最佳服装设计
                    $bestAwardInfo['prizeMovieType'] = 2;//被提名
                    $bestAwardInfo['commentText'] = "奥斯卡最佳服装设计被提名影片";
                    $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
                    $this->_inserAwardMovieInfo($url,$bestAwardInfo);
                }
            }
        }

        //最佳音响效果
        $moviceNameInfo = $this->_getPregMatch("/最佳音响效果&nbsp;Best Achievement(.*?)<\/dd>/si",$moviceInfoHtml);
        if (!empty($moviceNameInfo[1])) {
            $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
            //最佳音响效果链接数组+插入信息
            $urlArr = array_unique($urlInfo[0]);
            $bestAwardInfo['nianFen'] = $nianfen;
            $bestAwardInfo['prizeType'] = 1;//奥斯卡
            $bestAwardInfo['movieType'] = 9;//最佳音响效果
            $bestAwardInfo['prizeMovieType'] = 1;//获奖得主
            $bestAwardInfo['commentText'] = "奥斯卡最佳音响效果";//获奖得主
            $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
            $this->_inserAwardMovieInfo($urlArr[0],$bestAwardInfo);

            //最佳音响效果被提名影片信息
            $moviceNameInfo = $this->_getPregMatch("/最佳音响效果&nbsp;Best Achievement(.*?)Best/si",$moviceInfoHtml);
            if (!empty($moviceNameInfo[1])) {
                $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
                $timingBestUrlArr = array_unique($urlInfo[0]);
                foreach($timingBestUrlArr as $url) {
                    if ($url == $urlArr[0]) {
                        continue;
                    }
                    $bestAwardInfo['nianFen'] = $nianfen;
                    $bestAwardInfo['prizeType'] = 1;//奥斯卡
                    $bestAwardInfo['movieType'] = 9;//最佳音响效果
                    $bestAwardInfo['prizeMovieType'] = 2;//被提名
                    $bestAwardInfo['commentText'] = "奥斯卡最佳音响效果被提名影片";
                    $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
                    $this->_inserAwardMovieInfo($url,$bestAwardInfo);
                }
            }
        }

        //最佳音效剪辑
        $moviceNameInfo = $this->_getPregMatch("/最佳音效剪辑&nbsp;Best Sound(.*?)<\/dd>/si",$moviceInfoHtml);
        if (!empty($moviceNameInfo[1])) {
            $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
            //最佳音效剪辑链接数组+插入信息
            $urlArr = array_unique($urlInfo[0]);
            $bestAwardInfo['nianFen'] = $nianfen;
            $bestAwardInfo['prizeType'] = 1;//奥斯卡
            $bestAwardInfo['movieType'] = 10;//最佳音效剪辑
            $bestAwardInfo['prizeMovieType'] = 1;//获奖得主
            $bestAwardInfo['commentText'] = "奥斯卡最佳音效剪辑";//获奖得主
            $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
            $this->_inserAwardMovieInfo($urlArr[0],$bestAwardInfo);

            //最佳音效剪辑被提名影片信息
            $moviceNameInfo = $this->_getPregMatch("/最佳音效剪辑&nbsp;Best Sound(.*?)Best/si",$moviceInfoHtml);
            if (!empty($moviceNameInfo[1])) {
                $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
                $timingBestUrlArr = array_unique($urlInfo[0]);
                foreach($timingBestUrlArr as $url) {
                    if ($url == $urlArr[0]) {
                        continue;
                    }
                    $bestAwardInfo['nianFen'] = $nianfen;
                    $bestAwardInfo['prizeType'] = 1;//奥斯卡
                    $bestAwardInfo['movieType'] = 10;//最佳音效剪辑
                    $bestAwardInfo['prizeMovieType'] = 2;//被提名
                    $bestAwardInfo['commentText'] = "奥斯卡最佳音效剪辑被提名影片";
                    $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
                    $this->_inserAwardMovieInfo($url,$bestAwardInfo);
                }
            }
        }

        //最佳化妆与发型
        $moviceNameInfo = $this->_getPregMatch("/最佳化妆与发型&nbsp;Best Makeup(.*?)<\/dd>/si",$moviceInfoHtml);
        if (!empty($moviceNameInfo[1])) {
            $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
            //最佳化妆与发型链接数组+插入信息
            $urlArr = array_unique($urlInfo[0]);
            $bestAwardInfo['nianFen'] = $nianfen;
            $bestAwardInfo['prizeType'] = 1;//奥斯卡
            $bestAwardInfo['movieType'] = 11;//最佳化妆与发型
            $bestAwardInfo['prizeMovieType'] = 1;//获奖得主
            $bestAwardInfo['commentText'] = "奥斯卡最佳化妆与发型";//获奖得主
            $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
            $this->_inserAwardMovieInfo($urlArr[0],$bestAwardInfo);

            //最佳化妆与发型被提名影片信息
            $moviceNameInfo = $this->_getPregMatch("/最佳化妆与发型&nbsp;Best Makeup(.*?)Best/si",$moviceInfoHtml);
            if (!empty($moviceNameInfo[1])) {
                $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
                $timingBestUrlArr = array_unique($urlInfo[0]);
                foreach($timingBestUrlArr as $url) {
                    if ($url == $urlArr[0]) {
                        continue;
                    }
                    $bestAwardInfo['nianFen'] = $nianfen;
                    $bestAwardInfo['prizeType'] = 1;//奥斯卡
                    $bestAwardInfo['movieType'] = 11;//最佳音效剪辑
                    $bestAwardInfo['prizeMovieType'] = 2;//被提名
                    $bestAwardInfo['commentText'] = "奥斯卡最佳化妆与发型被提名影片";
                    $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
                    $this->_inserAwardMovieInfo($url,$bestAwardInfo);
                }
            }
        }

        //最佳原创配乐
        $moviceNameInfo = $this->_getPregMatch("/最佳原创配乐&nbsp;Best Original(.*?)<\/dd>/si",$moviceInfoHtml);
        if (!empty($moviceNameInfo[1])) {
            $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
            //最佳原创配乐链接数组+插入信息
            $urlArr = array_unique($urlInfo[0]);
            $bestAwardInfo['nianFen'] = $nianfen;
            $bestAwardInfo['prizeType'] = 1;//奥斯卡
            $bestAwardInfo['movieType'] = 12;//最佳原创配乐
            $bestAwardInfo['prizeMovieType'] = 1;//获奖得主
            $bestAwardInfo['commentText'] = "奥斯卡最佳原创配乐";//获奖得主
            $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
            $this->_inserAwardMovieInfo($urlArr[0],$bestAwardInfo);

            //最佳原创配乐被提名影片信息
            $moviceNameInfo = $this->_getPregMatch("/最佳原创配乐&nbsp;Best Original(.*?)Best/si",$moviceInfoHtml);
            if (!empty($moviceNameInfo[1])) {
                $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
                $timingBestUrlArr = array_unique($urlInfo[0]);
                foreach($timingBestUrlArr as $url) {
                    if ($url == $urlArr[0]) {
                        continue;
                    }
                    $bestAwardInfo['nianFen'] = $nianfen;
                    $bestAwardInfo['prizeType'] = 1;//奥斯卡
                    $bestAwardInfo['movieType'] = 12;//最佳原创配乐
                    $bestAwardInfo['prizeMovieType'] = 2;//被提名
                    $bestAwardInfo['commentText'] = "奥斯卡最佳原创配乐被提名影片";
                    $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
                    $this->_inserAwardMovieInfo($url,$bestAwardInfo);
                }
            }
        }

        //最佳原创歌曲
        $moviceNameInfo = $this->_getPregMatch("/最佳原创歌曲&nbsp;Best Original(.*?)<\/dd>/si",$moviceInfoHtml);
        if (!empty($moviceNameInfo[1])) {
            $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
            //最佳原创歌曲链接数组+插入信息
            $urlArr = array_unique($urlInfo[0]);
            $bestAwardInfo['nianFen'] = $nianfen;
            $bestAwardInfo['prizeType'] = 1;//奥斯卡
            $bestAwardInfo['movieType'] = 13;//最佳原创歌曲
            $bestAwardInfo['prizeMovieType'] = 1;//获奖得主
            $bestAwardInfo['commentText'] = "奥斯卡最佳原创歌曲";//获奖得主
            $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
            $this->_inserAwardMovieInfo($urlArr[0],$bestAwardInfo);

            //最佳原创歌曲被提名影片信息
            $moviceNameInfo = $this->_getPregMatch("/最佳原创歌曲&nbsp;Best Original(.*?)Best/si",$moviceInfoHtml);
            if (!empty($moviceNameInfo[1])) {
                $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
                $timingBestUrlArr = array_unique($urlInfo[0]);
                foreach($timingBestUrlArr as $url) {
                    if ($url == $urlArr[0]) {
                        continue;
                    }
                    $bestAwardInfo['nianFen'] = $nianfen;
                    $bestAwardInfo['prizeType'] = 1;//奥斯卡
                    $bestAwardInfo['movieType'] = 13;//最佳原创歌曲
                    $bestAwardInfo['prizeMovieType'] = 2;//被提名
                    $bestAwardInfo['commentText'] = "奥斯卡最佳原创歌曲被提名影片";
                    $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
                    $this->_inserAwardMovieInfo($url,$bestAwardInfo);
                }
            }
        }

        //最佳纪录长片
        $moviceNameInfo = $this->_getPregMatch("/最佳纪录长片&nbsp;Best Documentary(.*?)<\/dd>/si",$moviceInfoHtml);
        if (!empty($moviceNameInfo[1])) {
            $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
            //最佳纪录长片链接数组+插入信息
            $urlArr = array_unique($urlInfo[0]);
            $bestAwardInfo['nianFen'] = $nianfen;
            $bestAwardInfo['prizeType'] = 1;//奥斯卡
            $bestAwardInfo['movieType'] = 14;//最佳纪录长片
            $bestAwardInfo['prizeMovieType'] = 1;//获奖得主
            $bestAwardInfo['commentText'] = "奥斯卡最佳纪录长片";//获奖得主
            $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
            $this->_inserAwardMovieInfo($urlArr[0],$bestAwardInfo);

            //最佳纪录长片被提名影片信息
            $moviceNameInfo = $this->_getPregMatch("/最佳纪录长片&nbsp;Best Documentary(.*?)Best/si",$moviceInfoHtml);
            if (!empty($moviceNameInfo[1])) {
                $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
                $timingBestUrlArr = array_unique($urlInfo[0]);
                foreach($timingBestUrlArr as $url) {
                    if ($url == $urlArr[0]) {
                        continue;
                    }
                    $bestAwardInfo['nianFen'] = $nianfen;
                    $bestAwardInfo['prizeType'] = 1;//奥斯卡
                    $bestAwardInfo['movieType'] = 14;//最佳原创歌曲
                    $bestAwardInfo['prizeMovieType'] = 2;//被提名
                    $bestAwardInfo['commentText'] = "奥斯卡最佳纪录长片被提名影片";
                    $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
                    $this->_inserAwardMovieInfo($url,$bestAwardInfo);
                }
            }
        }

        //最佳外语片
        $moviceNameInfo = $this->_getPregMatch("/最佳外语片&nbsp;Best Foreign(.*?)<\/dd>/si",$moviceInfoHtml);
        if (!empty($moviceNameInfo[1])) {
            $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
            //最佳外语片链接数组+插入信息
            $urlArr = array_unique($urlInfo[0]);
            $bestAwardInfo['nianFen'] = $nianfen;
            $bestAwardInfo['prizeType'] = 1;//奥斯卡
            $bestAwardInfo['movieType'] = 15;//最佳外语片
            $bestAwardInfo['prizeMovieType'] = 1;//获奖得主
            $bestAwardInfo['commentText'] = "奥斯卡最佳外语片";//获奖得主
            $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
            $this->_inserAwardMovieInfo($urlArr[0],$bestAwardInfo);

            //最佳外语片被提名影片信息
            $moviceNameInfo = $this->_getPregMatch("/最佳外语片&nbsp;Best Foreign(.*?)Best/si",$moviceInfoHtml);
            if (!empty($moviceNameInfo[1])) {
                $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
                $timingBestUrlArr = array_unique($urlInfo[0]);
                foreach($timingBestUrlArr as $url) {
                    if ($url == $urlArr[0]) {
                        continue;
                    }
                    $bestAwardInfo['nianFen'] = $nianfen;
                    $bestAwardInfo['prizeType'] = 1;//奥斯卡
                    $bestAwardInfo['movieType'] = 15;//最佳外语片
                    $bestAwardInfo['prizeMovieType'] = 2;//被提名
                    $bestAwardInfo['commentText'] = "奥斯卡最佳外语片被提名影片";
                    $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
                    $this->_inserAwardMovieInfo($url,$bestAwardInfo);
                }
            }
        }

        //最佳动画长片
        $moviceNameInfo = $this->_getPregMatch("/最佳动画长片&nbsp;Best Animated(.*?)<\/dd>/si",$moviceInfoHtml);
        if (!empty($moviceNameInfo[1])) {
            $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
            //最佳动画长片链接数组+插入信息
            $urlArr = array_unique($urlInfo[0]);
            $bestAwardInfo['nianFen'] = $nianfen;
            $bestAwardInfo['prizeType'] = 1;//奥斯卡
            $bestAwardInfo['movieType'] = 16;//最佳动画长片
            $bestAwardInfo['prizeMovieType'] = 1;//获奖得主
            $bestAwardInfo['commentText'] = "奥斯卡最佳动画长片";//获奖得主
            $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
            $this->_inserAwardMovieInfo($urlArr[0],$bestAwardInfo);

            //最佳动画长片被提名影片信息
            $moviceNameInfo = $this->_getPregMatch("/最佳动画长片&nbsp;Best Animated(.*?)Best/si",$moviceInfoHtml);
            if (!empty($moviceNameInfo[1])) {
                $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
                $timingBestUrlArr = array_unique($urlInfo[0]);
                foreach($timingBestUrlArr as $url) {
                    if ($url == $urlArr[0]) {
                        continue;
                    }
                    $bestAwardInfo['nianFen'] = $nianfen;
                    $bestAwardInfo['prizeType'] = 1;//奥斯卡
                    $bestAwardInfo['movieType'] = 16;//最佳动画长片
                    $bestAwardInfo['prizeMovieType'] = 2;//被提名
                    $bestAwardInfo['commentText'] = "奥斯卡最佳动画长片被提名影片";
                    $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
                    $this->_inserAwardMovieInfo($url,$bestAwardInfo);
                }
            }
        }

        //最佳真人短片
        $moviceNameInfo = $this->_getPregMatch("/最佳真人短片&nbsp;Best Short(.*?)<\/dd>/si",$moviceInfoHtml);
        if (!empty($moviceNameInfo[1])) {
            $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
            //最佳真人短片链接数组+插入信息
            $urlArr = array_unique($urlInfo[0]);
            $bestAwardInfo['nianFen'] = $nianfen;
            $bestAwardInfo['prizeType'] = 1;//奥斯卡
            $bestAwardInfo['movieType'] = 17;//最佳真人短片
            $bestAwardInfo['prizeMovieType'] = 1;//获奖得主
            $bestAwardInfo['commentText'] = "奥斯卡最佳真人短片";//获奖得主
            $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
            $this->_inserAwardMovieInfo($urlArr[0],$bestAwardInfo);

            //最佳真人短片被提名影片信息
            $moviceNameInfo = $this->_getPregMatch("/最佳真人短片&nbsp;Best Short(.*?)Best/si",$moviceInfoHtml);
            if (!empty($moviceNameInfo[1])) {
                $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
                $timingBestUrlArr = array_unique($urlInfo[0]);
                foreach($timingBestUrlArr as $url) {
                    if ($url == $urlArr[0]) {
                        continue;
                    }
                    $bestAwardInfo['nianFen'] = $nianfen;
                    $bestAwardInfo['prizeType'] = 1;//奥斯卡
                    $bestAwardInfo['movieType'] = 17;//最佳真人短片
                    $bestAwardInfo['prizeMovieType'] = 2;//被提名
                    $bestAwardInfo['commentText'] = "奥斯卡最佳真人短片被提名影片";
                    $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
                    $this->_inserAwardMovieInfo($url,$bestAwardInfo);
                }
            }
        }

        //最佳动画短片
        $moviceNameInfo = $this->_getPregMatch("/最佳动画短片&nbsp;Best Short(.*?)<\/dd>/si",$moviceInfoHtml);
        if (!empty($moviceNameInfo[1])) {
            $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
            //最佳动画短片链接数组+插入信息
            $urlArr = array_unique($urlInfo[0]);
            $bestAwardInfo['nianFen'] = $nianfen;
            $bestAwardInfo['prizeType'] = 1;//奥斯卡
            $bestAwardInfo['movieType'] = 18;//最佳动画短片
            $bestAwardInfo['prizeMovieType'] = 1;//获奖得主
            $bestAwardInfo['commentText'] = "奥斯卡最佳动画短片";//获奖得主
            $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
            $this->_inserAwardMovieInfo($urlArr[0],$bestAwardInfo);

            //最佳动画短片被提名影片信息
            $moviceNameInfo = $this->_getPregMatch("/最佳真人短片&nbsp;Best Short(.*?)Best/si",$moviceInfoHtml);
            if (!empty($moviceNameInfo[1])) {
                $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
                $timingBestUrlArr = array_unique($urlInfo[0]);
                foreach($timingBestUrlArr as $url) {
                    if ($url == $urlArr[0]) {
                        continue;
                    }
                    $bestAwardInfo['nianFen'] = $nianfen;
                    $bestAwardInfo['prizeType'] = 1;//奥斯卡
                    $bestAwardInfo['movieType'] = 18;//最佳动画短片
                    $bestAwardInfo['prizeMovieType'] = 2;//被提名
                    $bestAwardInfo['commentText'] = "奥斯卡最佳动画短片被提名影片";
                    $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
                    $this->_inserAwardMovieInfo($url,$bestAwardInfo);
                }
            }
        }

        //最佳纪录短片
        $moviceNameInfo = $this->_getPregMatch("/最佳纪录短片&nbsp;Best Documentary(.*?)<\/dd>/si",$moviceInfoHtml);
        if (!empty($moviceNameInfo[1])) {
            $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
            //最佳纪录短片链接数组+插入信息
            $urlArr = array_unique($urlInfo[0]);
            $bestAwardInfo['nianFen'] = $nianfen;
            $bestAwardInfo['prizeType'] = 1;//奥斯卡
            $bestAwardInfo['movieType'] = 19;//最佳纪录短片
            $bestAwardInfo['prizeMovieType'] = 1;//获奖得主
            $bestAwardInfo['commentText'] = "奥斯卡最佳纪录短片";//获奖得主
            $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
            $this->_inserAwardMovieInfo($urlArr[0],$bestAwardInfo);

            //最佳纪录短片被提名影片信息
            $moviceNameInfo = $this->_getPregMatch("/最佳纪录短片&nbsp;Best Documentary(.*?)Best/si",$moviceInfoHtml);
            if (!empty($moviceNameInfo[1])) {
                $urlInfo = $this->_getPregMatchAll("|movie\.mtime\.com\/[0-9]+\/|", $moviceNameInfo[1], PREG_PATTERN_ORDER);
                $timingBestUrlArr = array_unique($urlInfo[0]);
                foreach($timingBestUrlArr as $url) {
                    if ($url == $urlArr[0]) {
                        continue;
                    }
                    $bestAwardInfo['nianFen'] = $nianfen;
                    $bestAwardInfo['prizeType'] = 1;//奥斯卡
                    $bestAwardInfo['movieType'] = 19;//最佳纪录短片
                    $bestAwardInfo['prizeMovieType'] = 2;//被提名
                    $bestAwardInfo['commentText'] = "奥斯卡最佳纪录短片被提名影片";
                    $bestAwardInfo['volume'] = $nianfen - 1928;//第几届
                    $this->_inserAwardMovieInfo($url,$bestAwardInfo);
                }
            }
        }
    }

    /**
     * 影片插入控制函数
     * @param $url
     * @param $awardInfo
     */
    private function _inserAwardMovieInfo($url,$awardInfo) {
        $infoId = $this->_getMtimeMovieInfo($url,$awardInfo);
        if (!empty($infoId)) {
            $awardInfo['infoId'] = $infoId;
            //查询获奖信息是否存在
            $conArr = array(
                "infoId" => $awardInfo['infoId'],
                "nianFen" => $awardInfo['nianFen'],
                "prizeType" => $awardInfo['prizeType'],
                "movieType" => $awardInfo['movieType'],
                "prizeMovieType" => $awardInfo['prizeMovieType'],
            );
            $awInfo = $this->_getInfo($conArr,"one","tbl_moviePrizeInfo");
            if (empty($awInfo)) {//为空则插入
                $this->_insertInfo($awardInfo,"tbl_moviePrizeInfo");
            }
        }
    }

    /**
     * 插入获奖人物信息
     * @param $purl
     * @param $awardInfo
     * @param array $murl
     */
    private function _inserAwardPeopleInfo($purl,$awardInfo,$murl = array()) {
        $movieId = array();
        foreach($murl as $m) {
            $movieId[] = $this->_getMtimeMovieInfo($m);
        }
        $movieIdStr = implode(",",$movieId);
        $characterId = $this->_getPeopleInfo($purl,$awardInfo);
        if (!empty($characterId)) {
            $awardInfo['infoIdStr'] = $movieIdStr;
            $awardInfo['characterId'] = $characterId;
            //查询获奖信息是否存在
            $conArr = array(
                "webId" => $awardInfo['webId'],
                "webType" => $awardInfo['webType'],
                "nianFen" => $awardInfo['nianFen'],
                "prizeType" => $awardInfo['prizeType'],
                "personType" => $awardInfo['personType'],
                "prizePersonType" => $awardInfo['prizePersonType'],
            );
            $awInfo = $this->_getInfo($conArr,"one","tbl_prizePersonInfo");
            if (empty($awInfo)) {//为空则插入
                $this->_insertInfo($awardInfo,"tbl_prizePersonInfo");
            }
        }
    }

    /**
     * 获取电影信息
     * @param $infoVal
     * @param $awardInfo
     */
    private function _getMtimeMovieInfo($infoVal,&$awardInfo = array()) {
        $awardInfo['createTime'] = time();

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
        $resultArr['webType'] = $awardInfo['webType'] = $this->_webConfigInfo['academyAward']['prizeType'];
        $resultArr['webId'] = $awardInfo['webId'] = $moviceId;

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

        //读取信息是否存在
        $info = $this->_getMoviceInfoByIdAndType($resultArr['webId'], $resultArr['webType']);

        if (!empty($info)) { //信息已存在
            return $info['id'];
        } else {
            //写入数据库 todo
            $insertRes = $this->_insertMoviceDetailInfo($resultArr);
            if (!empty($insertRes) && !is_array($insertRes)) {
                var_dump("插入成功！" . date('Y-m-d H:i:s') . "\n");
            } else {
                var_dump("插入失败 {$resultArr['webId']} --- {$infoVal}！" . date('Y-m-d H:i:s') . "\n");
                var_dump($insertRes);
            }
            return $insertRes;
        }
    }

    /**
     * 获取人物信息
     * @param $url
     * @return bool
     */
    private function _getPeopleInfo($url,&$awarPeopleInfo = array()) {
        if (empty($url)) {
            return false;
        }
        $url = "http://" . $url;
        $moviceInfoHtml = $this->_getCurlInfo($url);
        if (strpos($moviceInfoHtml,"很抱歉，你要访问的页面不存在") !== false || strlen($moviceInfoHtml) < 3000) {
            file_put_contents("/home/www/logs/dianying/zhuaqu_people_id.id",$url);
            return false;
        }

        //处理信息开始
        $resultArr = array();

        //来源信息
        $idArr = $this->_getPregMatch("/[0-9]+/", $url);
        $resultArr['webId'] = $awarPeopleInfo['webId'] = $idArr[0];
        $resultArr['webType'] = $awarPeopleInfo['webType'] = $this->_webConfigInfo['academyAward']['prizeType'];

        //人名
        $titleArr = $this->_getPregMatch("/<title>(.*?)<\/title>/si", $moviceInfoHtml);
        if (!empty($titleArr[1])) {
            $nameArr = explode(" ",$titleArr[1]);
            $resultArr['name'] = $awarPeopleInfo['userName'] = trim($nameArr[0]);
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
        if (!empty($info)) {//信息已存在且没有被删除
            return $info['id'];
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
            return $lastId;
        }
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
$doo = new getPrizeInfo();
$doo->run();
