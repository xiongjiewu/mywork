<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    /**
     * 网站后台页面
     * added by xiongjiewu at 2013-3-4
     */
class Login extends CI_Controller {

    public function index()
    {
        $this->load->set_head_img(false);
        $this->load->set_top_index(-1);
        $this->load->set_css(array("/css/user/login.css"));
        $this->load->set_js(array("/js/user/login.js"));
        $this->set_view("user/login");
    }
}