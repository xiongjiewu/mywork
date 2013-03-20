<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站注册成功提示页面
 * added by xiongjiewu at 2013-3-4
 */
class Registersuccess extends CI_Controller {

    public function index()
    {
        $code = $this->input->get("code");
        if (empty($this->userId) || !isset($code) || ($this->userId != base64_decode($code))) {//验证不通过跳转至首页
            $this->jump_to("/");
        }
        $this->load->set_head_img(false);
        $this->load->set_top_index(-1);
        $this->load->set_css(array("css/user/register.css","css/user/registersuccess.css"));
        $this->set_view("user/registersuccess");
    }
}