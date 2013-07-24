<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站系列大片页面
 * added by xiongjiewu at 2013-07-21
 */
class Series extends CI_Controller {
    function __construct() {
        parent::__construct();
    }

    public function index() {


        $this->load->set_top_index(-1);
        $this->set_attr("tabIndex",4);
        $this->load->set_title("系列大片 - " . $this->base_title . " - " . APF::get_instance()->get_config_value("base_name"));
        $this->load->set_css(array("/css/dianying/series.css"));
        $this->load->set_js(array("/js/dianying/series.js"));
        $this->set_attr("moviePlace",$this->_moviePlace);
        $this->set_attr("movieType",$this->_movieType);
        $this->set_view('dianying/series','base3');
    }
}