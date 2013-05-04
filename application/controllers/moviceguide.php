<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站电影导航页
 * added by xiongjiewu at 2013-3-4
 */
class Moviceguide extends CI_Controller {

    private $_maxCount = 500;//最大允许显示电影个数
    private $_maxPage = 50;//最大允许页码
    private $_limit = 21;

    public function index() {
        $this->type(null,1);
    }

    public function type($type = null,$page = 1)
    {
        if (empty($type) || $type == "all") {
            $type = "all";
            $typeS = null;
        } else {
            $typeS = intval($type);
        }
        $page = intval($page);
        $page = ($page > $this->_maxPage) ? $this->_maxPage : $page;
        $this->set_attr("bigtype",0);
        $this->set_attr("type",$type);
        $this->load->model('Backgroundadmin');
        $limit = $this->_limit;
        $this->set_attr("limit",$limit);
        $mouvieCount = $this->Backgroundadmin->getDetailInfoCountByType($typeS);
        $mouvieCount = ($mouvieCount > $this->_maxCount) ? $this->_maxCount : $mouvieCount;
        if ($mouvieCount > 0 && $page > ceil($mouvieCount / $limit)) {
            $page = ceil($mouvieCount / $limit);
        }
        $movieList = $this->Backgroundadmin->getDetailInfoByType($typeS,($page - 1) * $limit,$limit);
        foreach($movieList as $infoKey => $infoVal) {
            if ($infoKey < 4) {
                $movieList[$infoKey]['class'] = "firstRow";
            } else {
                $movieList[$infoKey]['class'] = "";
            }
            $movieList[$infoKey]['daoyan'] = $this->splitStr($infoVal['daoyan'],9);
        }
        $this->set_attr("movieList",$movieList);
        $this->set_attr("mouvieCount",$mouvieCount);
        $base_url = get_url("/moviceguide/type/{$type}/");
        $fenye = $this->set_page_info($page,$limit,$mouvieCount,$base_url);
        $this->set_attr("fenye",$fenye);
        $this->load->set_head_img(false);
        
        $this->load->set_title((($type != "all")? $this->_movieType[$type] . "片" : "按类型检索") . " - " . $this->base_title . " - " . APF::get_instance()->get_config_value("base_name"));
        $this->load->set_css(array("/css/dianying/moviceguide.css"));
        $this->load->set_js(array("/js/dianying/moviceguide.js"));
        $this->load->set_top_index(4);
        $this->set_attr("moviePlace",$this->_moviePlace);
        $this->set_attr("movieType",$this->_movieType);
        $this->set_attr("movieSortType",APF::get_instance()->get_config_value("movie_type"));
        $this->set_view('dianying/moviceguide');
    }

    public function year($type = null,$page = 1) {
        if (empty($type) || ($type == "all")) {
            $type = "all";
            $typeS = null;
        } else {
            $type = $typeS = intval($type);
        }
        $page = intval($page);
        $page = ($page > $this->_maxPage) ? $this->_maxPage : $page;
        $this->set_attr("bigtype",1);
        $this->set_attr("type",$type);
        $this->load->model('Backgroundadmin');
        $limit = $this->_limit;
        $this->set_attr("limit",$limit);
        $mouvieCount = $this->Backgroundadmin->getDetailInfoCountByNianFen($typeS);
        $mouvieCount = ($mouvieCount > $this->_maxCount) ? $this->_maxCount : $mouvieCount;
        if ($mouvieCount > 0 && $page > ceil($mouvieCount / $limit)) {
            $page = ceil($mouvieCount / $limit);
        }
        $movieList = $this->Backgroundadmin->getDetailInfoByNianFen($typeS,($page - 1) * $limit,$limit);
        foreach($movieList as $infoKey => $infoVal) {
            if ($infoKey < 4) {
                $movieList[$infoKey]['class'] = "firstRow";
            } else {
                $movieList[$infoKey]['class'] = "";
            }
            $movieList[$infoKey]['daoyan'] = $this->splitStr($infoVal['daoyan'],9);
        }
        $this->set_attr("movieList",$movieList);
        $this->set_attr("mouvieCount",$mouvieCount);
        $base_url = get_url("/moviceguide/year/{$type}/");
        $fenye = $this->set_page_info($page,$limit,$mouvieCount,$base_url);
        $this->set_attr("fenye",$fenye);
        $this->load->set_head_img(false);
        
        $this->load->set_title((($type == "all") ? "按年份检索" : $type) ."年 - " . $this->base_title . " - " . APF::get_instance()->get_config_value("base_name"));
        $this->load->set_css(array("/css/dianying/moviceguide.css"));
        $this->load->set_js(array("/js/dianying/moviceguide.js"));
        $this->load->set_top_index(4);
        $this->set_attr("moviePlace",$this->_moviePlace);
        $this->set_attr("movieType",$this->_movieType);
        $this->set_attr("movieSortType",APF::get_instance()->get_config_value("movie_type"));
        $this->set_view('dianying/moviceguide');
    }

    public function place($type = null,$page = 1) {
        if (empty($type) || ($type == "all")) {
            $type = "all";
            $typeS = null;
        } else {
            $type = $typeS = intval($type);
        }
        $page = intval($page);
        $page = ($page > $this->_maxPage) ? $this->_maxPage : $page;
        $this->set_attr("bigtype",2);
        $this->set_attr("type",$type);
        $this->load->model('Backgroundadmin');
        $limit = $this->_limit;
        $this->set_attr("limit",$limit);
        $mouvieCount = $this->Backgroundadmin->getDetailInfoCountByDiQu($typeS);
        $mouvieCount = ($mouvieCount > $this->_maxCount) ? $this->_maxCount : $mouvieCount;
        if ($mouvieCount > 0 && $page > ceil($mouvieCount / $limit)) {
            $page = ceil($mouvieCount / $limit);
        }
        $movieList = $this->Backgroundadmin->getDetailInfoByDiQ($typeS,($page - 1) * $limit,$limit);
        foreach($movieList as $infoKey => $infoVal) {
            if ($infoKey < 4) {
                $movieList[$infoKey]['class'] = "firstRow";
            } else {
                $movieList[$infoKey]['class'] = "";
            }
            $movieList[$infoKey]['daoyan'] = $this->splitStr($infoVal['daoyan'],9);
        }
        $this->set_attr("movieList",$movieList);
        $this->set_attr("mouvieCount",$mouvieCount);
        $base_url = get_url("/moviceguide/place/{$type}/");
        $fenye = $this->set_page_info($page,$limit,$mouvieCount,$base_url);
        $this->set_attr("fenye",$fenye);
        $this->load->set_head_img(false);
        
        $this->load->set_title((($type == "all") ? "按地区检索" : $this->_moviePlace[$type]) . "片 - " . $this->base_title . " - " . APF::get_instance()->get_config_value("base_name"));
        $this->load->set_css(array("/css/dianying/moviceguide.css"));
        $this->load->set_js(array("/js/dianying/moviceguide.js"));
        $this->load->set_top_index(4);
        $this->set_attr("moviePlace",$this->_moviePlace);
        $this->set_attr("movieType",$this->_movieType);
        $this->set_attr("movieSortType",APF::get_instance()->get_config_value("movie_type"));
        $this->set_view('dianying/moviceguide');
    }
}