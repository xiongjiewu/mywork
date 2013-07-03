<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站注册页面
 * added by xiongjiewu at 2013-3-4
 */
class Register extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!empty($this->userId)) {//已登录，跳转至首页
            $this->jump_to("/");
            exit;
        }
        $this->load->set_top_index(-1);
    }

    public function index()
    {
        $this->load->set_title("用户注册 - " . $this->base_title . " - " . APF::get_instance()->get_config_value("base_name"));
        $this->load->set_css(array("css/member/register.css"));
        $this->load->set_js(array("js/member/register.js"));
        $this->set_view("member/register","base3");
    }
}