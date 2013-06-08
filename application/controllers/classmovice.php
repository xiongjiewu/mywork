<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 重温经典页面
 * added by xiongjiewu at 2013-5-1
 */
class Classmovice extends CI_Controller {
    private $_limit = 20;
    private $_paiHangInfo;

    public function __construct() {
        parent::__construct();
        $this->load->model('Backgroundadmin');
        $this->load->model('Moviescore');
        $this->load->model('Moviesearch');
        $this->_paiHangInfo = APF::get_instance()->get_config_value("pai_hang");
        $this->set_attr("paiHangInfo",$this->_paiHangInfo);
    }

    /**
     * 页面入口函数
     * @param string $listType 类型
     * @param int $type
     * @param int $page
     */
    public function index($listType = "top",$type = 1,$page = 1) {
        $listType = empty($this->_paiHangInfo[$listType]) ? "top" : $listType;//默认显示top排行
        $this->set_attr("listType",$listType);

        $type = empty($this->_paiHangInfo[$listType][$type]) ? 1 : $type;//默认显示豆瓣top排行
        $this->set_attr("type",$type);

        $page = intval($page);
        $page = empty($page) ? 1 : $page;

        $this->load->set_top_index(3);
        $this->set_attr("moviePlace",$this->_moviePlace);
        $this->set_attr("movieType",$this->_movieType);
        $this->set_attr("movieSortType",APF::get_instance()->get_config_value("movie_type"));

        $functionName = "_" . $listType . "List";//拼接处理函数
        return $this->$functionName($type,$page);
    }

    /**
     * top排行榜
     * @param int $type
     * @param int $page
     */
    private function _topList($type = 1,$page = 1) {
        $listType = "top";

        $this->set_attr("limit",$this->_limit);


        //电影列表
        if ($type == 4) {//百度搜索排行榜
            //总数
            $totalCount = $this->Moviesearch->getSearchMovieCount($type);
            $this->set_attr("totalCount",$totalCount);

            $page = ($page > ceil($totalCount / $this->_limit)) ? $totalCount / $this->_limit : $page;
            $moviceIds = $this->Moviesearch->getSearchMoviceInfoByType($type,($page - 1) * $this->_limit,$this->_limit);
            $moviceIds = $this->initArrById($moviceIds,"infoId",$idsArr);
            $this->set_attr("moviceIds",$moviceIds);

            //电影详细信息
            $moviceList = $this->Backgroundadmin->getDetailInfo($idsArr,0,true);
            $serchArr = array();
            foreach($moviceList as $topKey => $topVal) {
                $serchArr[] = $moviceIds[$topVal['id']]['search'];
                $moviceList[$topKey]['search'] = $moviceIds[$topVal['id']]['search'];
                $moviceList[$topKey]['jieshao'] = $this->splitStr($topVal['jieshao'],100);
            }
            array_multisort($serchArr,SORT_DESC,$moviceList);
            $this->load->set_title($this->_paiHangInfo[$listType][$type]['title'] . " - 重温经典 - " . $this->base_title . " - " . APF::get_instance()->get_config_value("base_name"));
        } else {//top排行榜
            //总数
            $totalCount = $this->Moviescore->getTopMovieCount($type);
            $this->set_attr("totalCount",$totalCount);

            $page = ($page > ceil($totalCount / $this->_limit)) ? $totalCount / $this->_limit : $page;
            $moviceIds = $this->Moviescore->getTopMoviceInfoByType($type,($page - 1) * $this->_limit,$this->_limit);
            $moviceIds = $this->initArrById($moviceIds,"infoId",$idsArr);
            $this->set_attr("moviceIds",$moviceIds);

            //电影详细信息
            $moviceList = $this->Backgroundadmin->getDetailInfo($idsArr,0,true);
            $scoreArr = array();
            foreach($moviceList as $topKey => $topVal) {
                $scoreArr[] = $moviceIds[$topVal['id']]['score'];
                $moviceList[$topKey]['score'] = $moviceIds[$topVal['id']]['score'];
                $moviceList[$topKey]['jieshao'] = $this->splitStr($topVal['jieshao'],100);
            }
            array_multisort($scoreArr,SORT_DESC,$moviceList);
            $this->load->set_title($this->_paiHangInfo[$listType][$type]['title'] . "排行榜 - 重温经典 - " . $this->base_title . " - " . APF::get_instance()->get_config_value("base_name"));
        }

        $this->set_attr("moviceList",$moviceList);

        //分页
        $base_url = $this->_paiHangInfo[$listType][$type]['base_url'];
        $fenye = $this->set_page_info($page,$this->_limit,$totalCount,$base_url);
        $this->set_attr("fenye",$fenye);

        $this->load->set_head_img(false);
        $this->load->set_css(array("/css/dianying/newclassmovie.css"));
        $this->load->set_js(array("/js/dianying/newclassmovie.js"));
        $this->set_view('dianying/newclassmovie');
    }
}
