<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站首页
 * added by xiongjiewu at 2013-3-3
 */
class Welcome extends CI_Controller {

    private $_limit = 18;//最新上映和即将上映电影个数
    private $_topMovieLimit = 34;//电影墙电影个数
    private $_cacheP = "home_total_dy_info_";//缓存前缀
    private $_jieshaoLen = 45;
    private $_todayLimit = 20;//今日推荐
    private $_topLimit = 15;

    public function __construct() {
        parent::__construct();
        $this->load->model('Backgroundadmin');
        $this->load->model('Moviesearch');
        $this->load->model('Moviescore');
        $this->load->driver("cache");
    }

	public function index()
	{
        //电影总数
        $dyCount = $this->Backgroundadmin->getdyCount();
        $this->set_attr("dyCount",$dyCount);

        //今天
        $today = date("Ymd");
        //昨天
        $yesToday = date("Ymd",strtotime("-1 day"));
        //新的缓存key
        $cacheKey = $this->_cacheP . $today;
        //老的缓存key
        $oldCacheKey = $this->_cacheP . $yesToday;
        $homeTotalDyInfo = $this->cache->file->get($cacheKey);
        $newestDyInfo = $willDyInfo = array();
        $classDyInfo = $doubanTopMovice = $doubanDetailInfo = array();
        if ($homeTotalDyInfo === false) {
            //电影墙信息
            $conStr = "exist_watch = 1";
            $topMovieInfos = $this->Backgroundadmin->getDetailInfoByCondition($conStr,0,100);
            $homeTotalDyInfo['topMovieInfos'] = $topMovieInfos;

            //最新上映
            $theNewestInfo = $this->Backgroundadmin->getNewestInfo(1);
            if (!empty($theNewestInfo[0])) {
                $idStr = trim($theNewestInfo[0]['infoIdStr'],";");
                $ids = explode(";",$idStr);
                if (!empty($ids)) {
                    $newestDyInfo = $this->Backgroundadmin->getDetailInfo($ids,null,true);
                    $homeTotalDyInfo['newestDyInfo'] = $newestDyInfo;
                }
            }
            $willInfo = $this->Backgroundadmin->getNewestInfo(2);
            if (!empty($willInfo[0])) {
                $idStr = trim($willInfo[0]['infoIdStr'],";");
                $ids = explode(";",$idStr);
                if (!empty($ids)) {
                    $willDyInfo = $this->Backgroundadmin->getDetailInfo($ids,null,true);
                    $homeTotalDyInfo['willDyInfo'] = $willDyInfo;
                }
            }
            $classInfo = $this->Backgroundadmin->getNewestInfo(3);
            if (!empty($classInfo[0])) {
                $idStr = trim($classInfo[0]['infoIdStr'],";");
                $ids = explode(";",$idStr);
                if (!empty($ids)) {
                    $classDyInfo = $this->Backgroundadmin->getDetailInfo($ids,null,true);
                    $homeTotalDyInfo['classDyInfo'] = $classDyInfo;
                }
            }

            //百度搜索排行榜
            $moviceIds = $this->Moviesearch->getSearchMoviceInfoByType(4,0,$this->_topLimit);
            $moviceIds = $this->initArrById($moviceIds,"infoId",$baiduIdsArr);
            //电影详细信息
            $baiduDetailInfo = $this->Backgroundadmin->getDetailInfo($baiduIdsArr,0,true);
            $serchArr = array();
            foreach($baiduDetailInfo as $topKey => $topVal) {
                $serchArr[] = $moviceIds[$topVal['id']]['search'];
                $baiduDetailInfo[$topKey]['search'] = $moviceIds[$topVal['id']]['search'];
            }
            array_multisort($serchArr,SORT_DESC,$baiduDetailInfo);
            $homeTotalDyInfo['baiduDetailInfo'] = $baiduDetailInfo;

            //豆瓣top电影
            $doubanTopMovice = $this->Moviescore->getTopMoviceInfoByType(1,0,$this->_topLimit);
            $doubanTopMovice = $this->initArrById($doubanTopMovice,"infoId",$doubanIdArr);
            //电影详细信息
            $doubanDetailInfo = $this->Backgroundadmin->getDetailInfo($doubanIdArr,0,true);
            $scoreArr = array();
            foreach($doubanDetailInfo as $doubanKey => $doubanVal) {
                $scoreArr[] = $doubanTopMovice[$doubanVal['id']]['score'];
                $doubanDetailInfo[$doubanKey]['score'] = $doubanTopMovice[$doubanVal['id']]['score'];
            }
            array_multisort($scoreArr,SORT_DESC,$doubanDetailInfo);
            $homeTotalDyInfo['doubanDetailInfo'] = $doubanDetailInfo;

            //IMDB top电影
            $imdbTopMovice = $this->Moviescore->getTopMoviceInfoByType(2,0,$this->_topLimit);
            $imdbTopMovice = $this->initArrById($imdbTopMovice,"infoId",$imdbIdArr);
            //电影详细信息
            $imdbDetailInfo = $this->Backgroundadmin->getDetailInfo($imdbIdArr,0,true);
            $scoreArr = array();
            foreach($imdbDetailInfo as $imdbKey => $imdbVal) {
                $scoreArr[] = $imdbTopMovice[$imdbVal['id']]['score'];
                $imdbDetailInfo[$imdbKey]['score'] = $imdbTopMovice[$imdbVal['id']]['score'];
            }
            array_multisort($scoreArr,SORT_DESC,$imdbDetailInfo);
            $homeTotalDyInfo['imdbDetailInfo'] = $imdbDetailInfo;

            //时光网top电影
            $mtimeTopMovice = $this->Moviescore->getTopMoviceInfoByType(3,0,$this->_topLimit);
            $mtimeTopMovice = $this->initArrById($mtimeTopMovice,"infoId",$mtimeIdArr);
            //电影详细信息
            $mtimeDetailInfo = $this->Backgroundadmin->getDetailInfo($mtimeIdArr,0,true);
            $scoreArr = array();
            foreach($mtimeDetailInfo as $mtimeKey => $mtimeVal) {
                $scoreArr[] = $mtimeTopMovice[$mtimeVal['id']]['score'];
                $mtimeDetailInfo[$mtimeKey]['score'] = $mtimeTopMovice[$mtimeVal['id']]['score'];
            }
            array_multisort($scoreArr,SORT_DESC,$mtimeDetailInfo);
            $homeTotalDyInfo['mtimeDetailInfo'] = $mtimeDetailInfo;


            //今日推荐
            $todayMovieList = $this->Backgroundadmin->getDetailInfoByCondition("",0,$this->_todayLimit);
            $homeTotalDyInfo['todayMovieList'] = $todayMovieList;

            //新缓存起来，1天
            $this->cache->file->save($cacheKey, json_encode($homeTotalDyInfo), 86400);

            //删除老的缓存
            if ($this->cache->file->get($oldCacheKey) !== false) {
                $this->cache->file->delete($oldCacheKey);
            }
        } else {
            $homeTotalDyInfo = json_decode($homeTotalDyInfo,true);
            $topMovieInfos = $homeTotalDyInfo['topMovieInfos'];
            $newestDyInfo = $homeTotalDyInfo['newestDyInfo'];
            $willDyInfo = $homeTotalDyInfo['willDyInfo'];
            $classDyInfo = $homeTotalDyInfo['classDyInfo'];
            $baiduDetailInfo = $homeTotalDyInfo['baiduDetailInfo'];
            $imdbDetailInfo = $homeTotalDyInfo['imdbDetailInfo'];
            $mtimeDetailInfo = $homeTotalDyInfo['mtimeDetailInfo'];
            $doubanDetailInfo = $homeTotalDyInfo['doubanDetailInfo'];
            $todayMovieList = $homeTotalDyInfo['todayMovieList'];
        }

        //电影墙
        shuffle($topMovieInfos);
        $topMovieInfos = array_slice($topMovieInfos,0,$this->_topMovieLimit);
        $this->set_attr("topMovieInfos",$topMovieInfos);
        //最新上映
        $newestDyInfo = array_slice($newestDyInfo,0,$this->_limit);
        shuffle($newestDyInfo);
        foreach($newestDyInfo as $newestDyKey => $newestDyVal) {
            $newestDyInfo[$newestDyKey]['jieshao'] = $this->splitStr($newestDyVal['jieshao'],$this->_jieshaoLen);
        }
        $this->set_attr("newestDyInfo",$newestDyInfo);
        //即将上映
        $willDyInfo = array_slice($willDyInfo,0,$this->_limit);
        shuffle($willDyInfo);
        foreach($willDyInfo as $willDyKey => $willDyVal) {
            $willDyInfo[$willDyKey]['jieshao'] = $this->splitStr($willDyVal['jieshao'],$this->_jieshaoLen);
        }
        $this->set_attr("willDyInfo",$willDyInfo);
        //经典电影
        $classDyInfo = array_slice($classDyInfo,0,$this->_limit - 5);
        shuffle($classDyInfo);
        $this->set_attr("classDyInfo",$classDyInfo);

        //百度top10
        $this->set_attr("baiduDetailInfo",$baiduDetailInfo);
        //豆瓣top10
        $this->set_attr("doubanDetailInfo",$doubanDetailInfo);
        //imdb top10
        $this->set_attr("imdbDetailInfo",$imdbDetailInfo);
        //时光网top10
        $this->set_attr("mtimeDetailInfo",$mtimeDetailInfo);

        //今日推荐
        shuffle($todayMovieList);
        $todayMovieList = array_slice($todayMovieList,0,7);
        $this->set_attr("todayMovieList",$todayMovieList);

        //类型信息
        $this->set_attr("moviceType",$this->_movieType);
        //分类信息
        $this->set_attr("movieSortType",APF::get_instance()->get_config_value("movie_type"));
        $this->set_attr("baseNum",6);
        $this->load->set_css(array("/css/index/home2.css"));
        $this->load->set_js(array("/js/index/home2.js"));
        $this->load->set_title("首页 - " . $this->base_title . " - " . APF::get_instance()->get_config_value("base_name"));
        $this->set_view('index/home2','base2');

	}
}