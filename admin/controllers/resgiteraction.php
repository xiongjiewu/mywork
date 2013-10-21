<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站注册行为页面
 * added by xiongjiewu at 2013-3-4
 */
class Resgiteraction extends CI_Controller {

    public function checkusernameCommon($username)
    {
        $result = array(
            "code" => "error",
            "info" => "登录账号不能为空",
        );
        if (!isset($username)) {
            return $result;
        } elseif (mb_strlen($username,"UTF-8") < 2) {
            $result['info'] = '登录账号不能少于2个字符';
            return $result;
        } else if (mb_strlen($username,"UTF-8") > 20) {
            $result['info'] = '登录账号不能超过20个字符';
            return $result;
        } elseif(!preg_match("/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u",$username)) {
            $result['info'] = '登录账号只能由中英文、数字和下划线组成';
            return $result;
        }
        $this->load->model('User');
        $info = $this->User->getUserInfoByFiled(array("userName" => $username));
        if (!empty($info)) {
            $result['info'] = '登录帐号已存在';
            return $result;
        } else {
            $result['code'] = 'success';
            $result['info'] = 'success';
            return $result;
        }
    }

    public function checkusername()
    {
        $username = trim($this->input->post("username"));
        echo json_encode($this->checkusernameCommon($username));
    }

    public function checkemailCommon($email)
    {
        $result = array(
            "code" => "error",
            "info" => "安全邮箱不能为空",
        );
        if (!isset($email)) {
            return $result;
        } elseif(!preg_match("/^[0-9a-zA-Z]+(?:[\_\-][a-z0-9\-]+)*@[a-zA-Z0-9]+(?:[-.][a-zA-Z0-9]+)*\.[a-zA-Z]+$/i", $email)) {
            $result['info'] = '安全邮箱格式不正确';
            return $result;
        }
        $this->load->model('User');
        $info = $this->User->getUserInfoByFiled(array("email" => $email));
        if (!empty($info)) {
            $result['info'] = '安全邮箱已存在';
            return $result;
        } else {
            $result['code'] = 'success';
            $result['info'] = 'success';
            return $result;
        }
    }

    public function checkemail()
    {
        $email = trim($this->input->post("email"));
        echo json_encode($this->checkemailCommon($email));
    }

    public function checkcodeCommon($code)
    {
        $result = array(
            "code" => "error",
            "info" => "验证码不能为空",
        );
        if (!isset($code)) {
            return $result;
        }
        $cookie_code = $this->get_cookie(get_config_value('resgiter_code_cookie_name'));
        $cookie_code = self::get_idx($cookie_code);
        if (strtolower($cookie_code) != strtolower($code)) {
            $result['info'] = '验证码不正确';
            return $result;
        } else {
            $result['code'] = 'success';
            $result['info'] = 'success';
            return $result;
        }
    }

    public function checkcode()
    {
        $code = trim($this->input->post("code"));
        echo json_encode($this->checkcodeCommon($code));
    }

    public function resgiter()
    {
        $data = $this->input->post();
        $username = isset($data['username']) ? $data['username'] : false;
        $checkName = $this->checkusernameCommon($username);
        if ($checkName['code'] == 'error') {
            $checkName['type'] = 'user';
            echo json_encode($checkName);
            exit;
        }
        $email = isset($data['email']) ? $data['email'] : false;
        $checkEmail = $this->checkemailCommon($email);
        if ($checkEmail['code'] == 'error') {
            $checkEmail['type'] = 'email';
            echo json_encode($checkEmail);
            exit;
        }
        if (empty($data['password1'])) {
            echo json_encode(array("code" => "error","info" => '登录密码不能为空','type' => 'pass1'));
            exit;
        }
        if (empty($data['password2'])) {
            echo json_encode(array("code" => "error","info" => '确认密码不能为空','type' => 'pass2'));
            exit;
        }
        if ($data['password1'] != $data['password2']) {
            echo json_encode(array("code" => "error","info" => '确认密码和登录密码不一致','type' => 'pass2'));
            exit;
        }
        $code = isset($data['code']) ? $data['code'] : false;
        $checkCode = $this->checkcodeCommon($code);
        if ($checkCode['code'] == 'error') {
            $checkCode['type'] = 'code';
            echo json_encode($checkCode);
            exit;
        }
        $info['userName'] = trim($data['username']);
        $info['email'] = trim($data['email']);
        $info['password'] = base64_encode(md5($data['password2']));
        $info['ip'] = ip2long($this->getUserIP());
        $info['time'] = time();
        $info['photo'] = get_config_value("user_photo");
        $this->load->model('User');
        $id = $this->User->insertUserInfo($info);
        if (!empty($id)) {
            $this->setLoginCookie($info['userName'],$id);
            echo json_encode(array("code" => "success","info" => base64_encode($id)));
            exit;
        } else {
            echo json_encode(array("code" => "sorry","info" => "网络链接失败，请重新尝试！"));
            exit;
        }
    }
}