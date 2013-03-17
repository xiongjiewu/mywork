<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站后台页面
 * added by xiongjiewu at 2013-3-4
 */
class Usercenter extends CI_Controller {

    public function index()
    {
        if (empty($this->userId)) {//已登录，跳转至首页
            $this->jump_to("/login/");
        }
        $this->load->set_head_img(false);
        $this->load->set_move_js(false);
        $this->load->set_top_index(-1);
        $this->load->set_css(array("css/user/usercenter.css"));
        $this->load->set_js(array("js/user/usercenter.js"));
        $this->set_view("user/usercenter");
    }
}