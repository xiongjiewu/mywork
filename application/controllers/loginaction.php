<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    /**
     * 网站注册行为页面
     * added by xiongjiewu at 2013-3-4
     */
class Loginaction extends CI_Controller {

    private function _checkCommon($username,$password,$remember)
    {
        $result = array(
            "code" => "error",
            "info" => "请输入登录帐号",
        );
        if (!isset($username)) {
            return $result;
        }
        if (!isset($password)) {
            $result['info'] = "请输入登录密码";
            return $result;
        }
        $this->load->model('User');
        $info = $this->User->getUserInfoByFiled(array("userName" => $username));
        if (empty($info)) {
            $result['info'] = "帐号或密码不正确";
            return $result;
        } elseif ($info['password'] != base64_encode(md5($password))) {
            $result['info'] = "输入的密码不正确";
            return $result;
        } else {
            $this->setLoginCookie($username,$info['id'],86400,$remember);
            $result['code'] = "success";
            $result['info'] = "success";
            return $result;
        }
    }

    public function login()
    {
        $username = trim($this->input->post("username"));
        $password = trim($this->input->post("password"));
        $remember = trim($this->input->post("remember"));
        echo json_encode($this->_checkCommon($username,$password,$remember));
    }
}