<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站首页
 * added by xiongjiewu at 2013-3-3
 */
class Welcome extends CI_Controller {

    private $_limit = 18;//最新上映和即将上映电影个数
    private $_topMovieLimit = 42;//电影墙电影个数
    private $_cacheP = "home_total_dy_info_";//缓存前缀
    private $_jieshaoLen = 45;
    private $_todayLimit = 20;//今日推荐
    private $_topLimit = 15;
    private $_topicLimit = 14;
    private $_peopelLimit = 16;
    private $_hotMovieLimit = 11;

    public function __construct() {
        parent::__construct();
        $this->load->model('Backgroundadmin');
        $this->load->model('Moviesearch');
        $this->load->model('Moviescore');
        $this->load->model('Movietopic');
        $this->load->model('Movietopicmovie');
        $this->load->model('Character');
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
            $conStr = "exist_watch = 1 and del = 0 order by createtime desc";
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

            //系列片
            $topicConStr = "topicType = 2 and status = 1 and del = 0 order by clickNum desc";
            $topicInfo = $this->Movietopic->getTopicInfoByCondition($topicConStr,0,$this->_topicLimit);
            $topicInfo = $this->_initTopicInfo($topicInfo);
            $homeTotalDyInfo['topicInfo'] = $topicInfo;

            //人物信息
            $peopleConStr = "del = 0 order by clickNum desc";
            $peopleInfo = $this->Character->getCharacterInfoByCondition($peopleConStr,0,$this->_peopelLimit);
            $homeTotalDyInfo['peopleInfo'] = $peopleInfo;

            //人气电影
            $hotMovieConStr = "del = 0 order by score desc,playNum desc";
            $hotMovieInfo = $this->Backgroundadmin->getDetailInfoByCondition($hotMovieConStr,0,$this->_hotMovieLimit);
            $homeTotalDyInfo['hotMovieInfo'] = $hotMovieInfo;

            //即将上映
            $willInfo = $this->Backgroundadmin->getNewestInfo(2);
            if (!empty($willInfo[0])) {
                $idStr = trim($willInfo[0]['infoIdStr'],";");
                $ids = explode(";",$idStr);
                if (!empty($ids)) {
                    $willDyInfo = $this->Backgroundadmin->getDetailInfo($ids,null,true);
                    $homeTotalDyInfo['willDyInfo'] = $willDyInfo;
                }
            }

            //经典电影
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
            $moviceIds = $this->Moviesearch->getSearchMoviceInfoByType(4,0,$this->_topLimit - 1);
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
            $todayConStr = "del = 0 order by createtime desc";
            $todayMovieList = $this->Backgroundadmin->getDetailInfoByCondition($todayConStr,0,$this->_todayLimit);
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
            $topicInfo = $homeTotalDyInfo['topicInfo'];
            $peopleInfo = $homeTotalDyInfo['peopleInfo'];
            $hotMovieInfo = $homeTotalDyInfo['hotMovieInfo'];
        }

        //电影墙
        shuffle($topMovieInfos);
        $topMovieInfos = array_slice($topMovieInfos,0,$this->_topMovieLimit);
        $this->set_attr("topMovieInfos",$topMovieInfos);
        //最新上映
        $newestDyInfo = array_slice($newestDyInfo,0,$this->_limit);
        foreach($newestDyInfo as $newestDyKey => $newestDyVal) {
            $newestDyInfo[$newestDyKey]['jieshao'] = $this->splitStr($newestDyVal['jieshao'],$this->_jieshaoLen);
        }
        $this->set_attr("newestDyInfo",$newestDyInfo);

        //系列大片
        $this->set_attr("topicInfo",$topicInfo);
        //人物系列
        $this->set_attr("peopleInfo",$peopleInfo);
        //人气电影
        $this->set_attr("hotMovieInfo",$hotMovieInfo);

        //即将上映
        $willDyInfo = array_slice($willDyInfo,0,$this->_limit);
        foreach($willDyInfo as $willDyKey => $willDyVal) {
            $willDyInfo[$willDyKey]['jieshao'] = $this->splitStr($willDyVal['jieshao'],$this->_jieshaoLen);
        }
        $this->set_attr("willDyInfo",$willDyInfo);

        //经典电影
        $classDyInfo = array_slice($classDyInfo,0,$this->_limit - 3);
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
        //年份信息
        $movieNianFen = $this->_movieNianFen;
        rsort($movieNianFen);
        $this->set_attr("movieNianFen",$movieNianFen);
        //地区信息
        $this->set_attr("moviePlace",$this->_moviePlace);
        //分类信息
        $this->set_attr("movieSortType",APF::get_instance()->get_config_value("movie_type"));
        //演员
        $yanYuan = APF::get_instance()->get_config_value("dianyingku_yanyuan");
        $this->set_attr("yanYuan",array_slice($yanYuan,0,8));
        //导演
        $daoYan = APF::get_instance()->get_config_value("dianyingku_daoyan");
        $this->set_attr("daoYan",array_slice($daoYan,0,8));

        $this->load->set_top_index(-1);
        $this->set_attr("tabIndex",0);
        $this->set_attr("baseNum",6);
        $this->load->set_css(array("/css/index/home2.css"));
        $this->load->set_js(array("/js/index/home2.js"));
        $this->load->set_title("首页 - " . $this->base_title . " - " . APF::get_instance()->get_config_value("base_name"));
        $this->set_view('index/home2','base3');

	}

    /**
     * 拼接系列大片电影总数
     * @param $topicInfo
     * @return array
     */
    private function _initTopicInfo($topicInfo) {
        //将id作为数组key
        $topicList = $this->initArrById($topicInfo,"id",$topicIdArr);
        //电影信息，用作计算电影总部数
        $topicMovieCountList = $this->Movietopicmovie->getTopicMovieCountByTopicId($topicIdArr);
        foreach($topicMovieCountList as $topicVal) {
            $topicList[$topicVal['topicId']]['movieCount'] = $topicVal['cn'];
        }
        return $topicList;
    }
}