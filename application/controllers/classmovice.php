<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 重温经典页面
 * added by xiongjiewu at 2013-5-1
 */
class Classmovice extends CI_Controller {
    private $_limit = 28;
    public function index($page = 1) {
        $page = intval($page);
        $page = empty($page) ? 1 : $page;
        $this->load->model('Backgroundadmin');

        //总数
        $totalCount = $this->Backgroundadmin->getTopMoviceInfoCount();
        $this->set_attr("totalCount",$totalCount);
        $this->set_attr("limit",$this->_limit);

        $page = ($page > ceil($totalCount / $this->_limit)) ? $totalCount / $this->_limit : $page;
        //电影列表
        $moviceList = $this->Backgroundadmin->getTopMoviceInfo(($page - 1) * $this->_limit,$this->_limit);
        $this->set_attr("moviceList",$moviceList);

        //分页
        $base_url = get_url("/classmovice/index/");
        $fenye = $this->set_page_info($page,$this->_limit,$totalCount,$base_url);
        $this->set_attr("fenye",$fenye);

        $this->load->set_head_img(false);
        $this->load->set_title("重温经典 - " . $this->base_title . " - " . APF::get_instance()->get_config_value("base_name"));
        $this->load->set_css(array("/css/dianying/classmovice.css"));
        $this->load->set_js(array("/js/dianying/classmovice.js"));
        $this->load->set_top_index(3);
        $this->set_attr("moviePlace",$this->_moviePlace);
        $this->set_attr("movieType",$this->_movieType);
        $this->set_attr("movieSortType",APF::get_instance()->get_config_value("movie_type"));
        $this->set_view('dianying/classmovice');
    }
}