<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 重温经典页面
 * added by xiongjiewu at 2013-5-1
 */
class Classmovice extends CI_Controller {
        public function index() {
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