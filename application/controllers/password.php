<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站更改密码页
 * added by xiongjiewu at 2013-3-4
 */
class Password extends CI_Controller {

    public function index()
    {
        $r = $this->input->get("r");
        if (!empty($this->userId) || empty($r)) {//已登录，跳转至首页
            $this->jump_to("/");
        }
        $this->load->set_head_img(false);
        $this->load->set_top_index(-1);
        $this->load->set_css(array("/css/member/password.css"));
        $this->load->set_js(array("/js/member/password.js"));
        $this->set_view("member/password");
    }

    public function change()
    {
        $key = $this->input->get("key");
        if (!empty($this->userId)) {//已登录，跳转至首页
            $this->jump_to("/");
            exit;
        }
        if (empty($key)) {
            $this->jump_to("/error/index/4?bgurl=" . base64_encode(get_url("/password?r=" . time())));
            exit;
        }
        $this->load->model('Changepassword');
        $info = $this->Changepassword->getInfoByFiled(array("hash_key" => $key,"del" => 1));
        if (empty($info)) {
            $this->jump_to("/error/index/4?bgurl=" . base64_encode(get_url("/password?r=" . time())));
            exit;
        }
        $maxTime = get_config_value("changepassword_max_time");
        if (time() > ($info['time'] + $maxTime)) {//页面已过期
            //$this->Changepassword->updateInfoByFiled(array("del" => 0),array("hash_key" => "'{$key}'"));
            //$this->jump_to("/error/index/4?bgurl=" . base64_encode(get_url("/password?r=" . time())));
            //exit;
        }
        $this->set_attr("key",$key);
        $this->load->set_head_img(false);
        $this->load->set_top_index(-1);
        $this->load->set_css(array("/css/member/password.css"));
        $this->load->set_js(array("/js/member/password.js"));
        $this->set_view("member/changepassword");
    }
}