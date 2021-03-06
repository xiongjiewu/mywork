<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站注册成功提示页面
 * added by xiongjiewu at 2013-3-4
 */
class Registersuccess extends MY_Controller {

    public function index()
    {
        //不展示调查问卷提示框
        $this->_attr['showResearchPan'] = false;

        $code = $this->input->get("code");
        if (empty($this->userId) || !isset($code) || ($this->userId != base64_decode($code))) {//验证不通过跳转至首页
            $this->jump_to("/");
        }
        $this->load->set_title("注册成功 - " . $this->base_title . " - " . APF::get_instance()->get_config_value("base_name"));
        $this->load->set_head_img(false);
        $this->load->set_top_index(-1);
        $this->load->set_css(array("css/member/register.css","css/member/registersuccess.css"));
        $this->set_view("member/registersuccess");
    }
}