<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站最新上映电影列表页
 * added by xiongjiewu at 2013-3-4
 */
class Latestmovie extends CI_Controller {

    private $_cacheP = "last_total_dy_info_";//缓存前缀
    function __construct() {
        parent::__construct();
        $this->load->model('Backgroundadmin');
        $this->load->driver('cache');
    }
    public function index()
    {
        //今天
        $today = date("Ymd");
        //昨天
        $yesToday = date("Ymd",strtotime("-1 day"));
        //新的缓存key
        $cacheKey = $this->_cacheP . $today;
        //老的缓存key
        $oldCacheKey = $this->_cacheP . $yesToday;
        $lastTotalInfo = $this->cache->file->get($cacheKey);
        if ($lastTotalInfo === false) {
            $sortStr = $this->_movieSortType[5]['sort'];
            $nTime = time();
            $time = strtotime(date("Y-m-01",$nTime));
            $movieList = array();
            $monthArr = array();
            $monthCount = APF::get_instance()->get_config_value("last_movie_month");
            for($i = 1;$i <= $monthCount;$i++) {
                $sTime = strtotime(date("Y-m-01 00:00:00",$time));//当前月开始时间
                $nMonth = strtotime("+1 month",$time);//下个月
                $nMonthFirstDayTime = strtotime(date("Y-m-01",$nMonth));//下个月第一天
                $eTime = strtotime(date("Y-m-d 23:59:59",$nMonthFirstDayTime - 86400));//当前月最后时间
                if ($eTime > $nTime) {
                    $eTime = $nTime;
                }
                $infoList  = $this->Backgroundadmin->getDetailInfoListByTime($sTime,$eTime,0,$sortStr);
                if (!empty($infoList)) {
                    $movieList[date("Y.m",$time)] = $infoList;
                    $monthArr[date("Y.m",$time)] = date("Ym",$time);
                }
                $time = strtotime(date("Y-m-01",$time));
                $time = strtotime("-1 month",$time);
            }

            $ids = $totalMovieInfo = array();
            foreach($movieList as $movieListKey => $movieListVal) {
                if (!empty($movieListVal)) {
                    foreach($movieListVal as $mKey => $mVal) {
                        $ids[] = $mVal['id'];
                        $totalMovieInfo[] = $mVal;
                        $movieListVal[$mKey]['jieshao'] = $this->splitStr($mVal['jieshao'],50);
                    }
                    $movieList[$movieListKey] = $movieListVal;
                }
            }
            $lastTotalInfo["totalMovieInfo"] = $totalMovieInfo;

            //观看链接
            $watchLinkInfo = $this->Backgroundadmin->getWatchLinkInfoByInfoId($ids);
            $watchLinkInfo = $this->_initArr($watchLinkInfo);

            //下载链接
            $downLoadLinkInfo = $this->Backgroundadmin->getDownLoadLinkInfoByInfoId($ids);
            $downLoadLinkInfo = $this->_initArr($downLoadLinkInfo);

            $lastTotalInfo["monthArr"] = $monthArr;
            $lastTotalInfo["movieList"] = $movieList;
            $lastTotalInfo["watchLinkInfo"] = $watchLinkInfo;
            $lastTotalInfo["downLoadLinkInfo"] = $downLoadLinkInfo;

            //新缓存起来，1天
            $this->cache->file->save($cacheKey, json_encode($lastTotalInfo), 86400);

            //删除老的缓存
            if ($this->cache->file->get($oldCacheKey) !== false) {
                $this->cache->file->delete($oldCacheKey);
            }
        } else {
            $lastTotalInfo = json_decode($lastTotalInfo,true);
            $movieList = $lastTotalInfo['movieList'];
            $monthArr = $lastTotalInfo['monthArr'];
            $watchLinkInfo = $lastTotalInfo['watchLinkInfo'];
            $downLoadLinkInfo = $lastTotalInfo['downLoadLinkInfo'];
            $ids = array();
            foreach($movieList as $movieListKey => $movieListVal) {
                if (!empty($movieListVal)) {
                    foreach($movieListVal as $mKey => $mVal) {
                        $ids[] = $mVal['id'];
                        $movieListVal[$mKey]['jieshao'] = $this->splitStr($mVal['jieshao'],50);
                    }
                    $movieList[$movieListKey] = $movieListVal;
                }
            }
            $totalMovieInfo = $lastTotalInfo["totalMovieInfo"];
        }

        $this->set_attr("movieList",$movieList);
        $this->set_attr("monthArr",$monthArr);
        $this->set_attr("watchLinkInfo",$watchLinkInfo);
        $this->set_attr("downLoadLinkInfo",$downLoadLinkInfo);
        if (!empty($this->userId)) {
            $this->set_attr("userId",$this->userId);
            $this->load->model("Shoucang");
            $shouCangInfo = $this->Shoucang->getUserShoucangInfoByInfoIds($this->userId,$ids);
            $shouCangInfo = $this->initArrById($shouCangInfo,"infoId");
            $this->set_attr("shouCangInfo",$shouCangInfo);
        }
        shuffle($totalMovieInfo);
        $totalMovieInfo = array_slice($totalMovieInfo,0,18);
        $this->set_attr("totalMovieInfo",$totalMovieInfo);

        $this->set_attr("moviePlace",$this->_moviePlace);
        $this->set_attr("movieType",$this->_movieType);
        $this->load->set_head_img(false);
        
        $this->load->set_title("最新上映列表 - " . APF::get_instance()->get_config_value("base_title") . " - " . APF::get_instance()->get_config_value("base_name"));
        $this->load->set_css(array("css/dianying/newlatestmovie2.css"));
        $this->load->set_js(array("js/dianying/newlatestmovie2.js"));
        $this->load->set_top_index(-1);
        $this->set_attr("tabIndex",1);
        $this->set_view('dianying/newlatestmovie2','base3');
    }

    private function _initArr($nfo)
    {
        if (empty($nfo)) {
            return $nfo;
        }
        $result = array();
        foreach($nfo as $infoVal) {
            $result[$infoVal['infoId']][] = $infoVal;
        }
        return $result;
    }
}