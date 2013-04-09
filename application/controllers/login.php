<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    /**
     * 网站后台页面
     * added by xiongjiewu at 2013-3-4
     */
class Login extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->set_login_pan(false);
    }

    public function index()
    {
        if (!empty($this->userId)) {//已登录，跳转至首页
            $this->jump_to("/");
        }
        $bgUrl = $this->input->get("bgurl");
        if (isset($bgUrl)) {
            $this->set_attr("bgurl",base64_decode($bgUrl));
        }
        $this->load->set_title("用户登录 - " . ("base_title") . " - " . APF::get_instance()->get_config_value("base_name"));
        $this->load->set_head_img(false);
        $this->load->set_top_index(-1);
        $this->load->set_css(array("/css/member/login.css"));
        $this->load->set_js(array("/js/member/login.js"));
        $this->set_view("member/login");
    }
}