<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站更改密码页
 * added by xiongjiewu at 2013-3-4
 */
class Password extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!empty($this->userId)) {//已登录，跳转至首页
            $this->jump_to("/");
            exit;
        }
        //不展示调查问卷提示框
        $this->_attr['showResearchPan'] = false;
    }

    public function index()
    {
        $r = $this->input->get("r");
        if (empty($r)) {
            $this->jump_to("/");
        }
        $this->load->set_title("密码更改 - " . $this->base_title . " - " . APF::get_instance()->get_config_value("base_name"));
        $this->load->set_head_img(false);
        $this->load->set_login_pan(false);
        $this->load->set_top_index(-1);
        $this->load->set_css(array("/css/member/password.css"));
        $this->load->set_js(array("/js/member/password.js"));
        $this->set_view("member/password");
    }

    public function sendsuccess($id = null) {
        $id = intval($id);
        $r = $this->input->get("r");
        if (empty($id) || empty($r)) {
            $this->jump_to("/");
            exit;
        }
        $this->load->model('User');
        $userInfo = $this->User->getUserInfoByFiled(array("id"=>$id));
        if (empty($userInfo)) {
            $this->jump_to("/");
            exit;
        }
        $emailTypeArr = explode("@",$userInfo['email']);
        $emailTypeStr = explode(".",$emailTypeArr[1]);
        $emailLoginUrl = APF::get_instance()->get_config_value("email_login_url");
        if (!empty($emailLoginUrl[strtolower($emailTypeStr[0])])) {
            $this->set_attr("emailUrl",$emailLoginUrl[strtolower($emailTypeStr[0])]);
        }
        $this->load->set_title("密码更改 - " . $this->base_title . " - " . APF::get_instance()->get_config_value("base_name"));
        $this->set_attr("email",$userInfo['email']);
        $this->load->set_head_img(false);
        $this->load->set_top_index(-1);
        $this->load->set_login_pan(false);
        $this->load->set_css(array("/css/member/sendsuccess.css"));
        $this->set_view("member/sendsuccess");
    }

    public function change() {
        $key = $this->input->get("key");
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
        $maxTime = APF::get_instance()->get_config_value("changepassword_max_time");
        if (time() > ($info['time'] + $maxTime)) {//页面已过期
            $this->Changepassword->updateInfoByFiled(array("del" => 0),array("hash_key" => "'{$key}'"));
            $this->jump_to("/error/index/4?bgurl=" . base64_encode(get_url("/password?r=" . time())));
            exit;
        }
        $this->load->set_title("密码更改 - " . $this->base_title . " - " . APF::get_instance()->get_config_value("base_name"));
        $this->set_attr("key",$key);
        $this->load->set_head_img(false);
        $this->load->set_login_pan(false);
        $this->load->set_top_index(-1);
        $this->load->set_css(array("/css/member/password.css"));
        $this->load->set_js(array("/js/member/password.js"));
        $this->set_view("member/changepassword");
    }
}