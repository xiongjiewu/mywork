<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    /**
     * 网站后台页面
     * added by xiongjiewu at 2013-3-4
     */
class Login extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }
    public function index()
    {
        $this->load->helper('url');
        if (!empty($this->userId) && !empty($this->userName) && !empty($this->adminId)) {
            redirect(get_url("/background/"), true);//已登录，跳转至首页
            exit;
        }
        $bgUrl = $this->input->get("bgurl");
        if (isset($bgUrl)) {
            $this->set_attr("bgurl",base64_decode($bgUrl));
        }
        $this->load->set_head_img(false);
        $this->load->set_top_index(-1);
        $this->load->set_css(array("/css/member/login.css"));
        $this->load->set_js(array("/js/member/login.js"));
        $this->set_view("member/login","base");
    }
}