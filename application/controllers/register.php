<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站注册页面
 * added by xiongjiewu at 2013-3-4
 */
class Register extends CI_Controller {

    public function index()
    {
        $this->load->set_head_img(false);
        $this->load->set_top_index(-1);
        $this->load->set_css(array("css/user/register.css"));
        $this->load->set_js(array("js/user/register.js"));
        $this->set_view("user/register");
    }
}