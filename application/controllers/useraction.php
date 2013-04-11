<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站用户行为controller,需登录
 * added by xiongjiewu at 2013-3-3
 */
class Useraction extends CI_Controller
{
    private $_cookiePre = __CLASS__;
    public function index() {


    }

    private function _checkLogin()
    {
        if (empty($this->userId)) {
            return false;
        }
        return true;
    }

    public function post()
    {
        $data = $this->input->post();
        if (!$this->_checkLogin()) {//未登录
            $this->jump_to("/error/index/2?bgurl=" . base64_encode(get_url("/detail/index/{$data['dyId']}")));
            exit;
        }
        if (empty($data['dyId'])) {
            echo "参数错误";
            exit;
        } elseif (!isset($data['content']) || ($data['content'] == "")) {
            echo "请输入内容！";
            exit;
        }
        $cookieName = $this->_cookiePre . "_" . $data['dyId'] . "_" . $this->userId;
        $cookieVal = $this->get_cookie($cookieName);
        $time = time();
        //在规定时间之内不能连续发表评论
        if (!empty($cookieVal) && ($time <= ($cookieVal + APF::get_instance()->get_config_value("max_post_time")))) {
            $this->jump_to("/error/index/5?bgurl=" . base64_encode(get_url("/detail/index/{$data['dyId']}/")));
            exit;
        }
        $info['infoId'] = $data['dyId'];
        $info['userName'] = $this->userName;
        $info['userId'] = $this->userId;
        $info['content'] = trim($data['content']);
        $info['time'] = time();
        $this->load->model('Yingping');
        $res = $this->Yingping->insertYingpingInfo($info);
        if (empty($res)) {
            $this->jump_to("/error/index/3?bgurl=" . base64_encode(get_url("/detail/index/{$data['dyId']}")));
            exit;
        } else {
            $this->set_cookie($cookieName,$time,600);
            $this->jump_to("/detail/index/{$data['dyId']}#createpost");
        }
    }

    public function ding()
    {
        $data = $this->input->post();
        if (empty($data['pid'])) {
            echo json_encode(array("code" => "error","info" => "非法操作"));
            exit;
        }
        $ip  = ip2long($this->getUserIP());
        $dingCookieName = $this->_cookiePre . "_" . $ip . "_" . $data['pid'];
        if (!empty($ip)) {
            $dingCookieVal = $this->get_cookie($dingCookieName);
            if (!empty($dingCookieVal) && ($dingCookieVal == $data['pid'])) {
                echo json_encode(array("code" => "error","info" => "同一个评论只能顶1次"));
                exit;
            }
        }
        $this->load->model('Yingping');
        $res = $this->Yingping->updateYingpingInfoById($data['pid'],array("ding" => "ding + 1"));
        if (empty($res)) {
            echo json_encode(array("code" => "error","info" => "网络连接失败，清重新操作！"));
            exit;
        } else {
            $this->set_cookie($dingCookieName,$data['pid'],365 * 86400);
            echo json_encode(array("code" => "ok","info" => "操作成功"));
            exit;
        }
    }
    public function uploadpho()
    {
        $img = $this->input->post("userpho");
        if (empty($img)) {
            echo json_encode(array("code" => "error","info" => "参数错误"));
            exit;
        }
        if (!$this->_checkLogin()) {
            echo json_encode(array("code" => "error","info" => "请先登录"));
            exit;
        }
        $this->load->model('User');
        $result = $this->User->updateUserInfo(array("photo" => $img),array("id" => $this->userId));
        if (empty($result)) {
            echo json_encode(array("code" => "success","info" => "网络连接失败，请重新尝试！"));
            exit;
        } else {
            echo json_encode(array("code" => "success","info" => "上传成功"));
            exit;
        }
    }

    public function insertnotice()
    {
        $result = array(
            "code" => "error",
            "info" => "请先登录",
        );
        if (!$this->_checkLogin()) {
            echo json_encode($result);
            exit;
        }
        $this->load->model('Notice');
        $userNoticeCount = $this->Notice->getNoticeCountByFiled(array("userId" => $this->userId,"reply"=>0,"del"=>0));
        if ($userNoticeCount >= APF::get_instance()->get_config_value("notice_max_count")) {
            $result['info'] = "最多可预订" . ("notice_max_count") . "个通知";
            echo json_encode($result);
            exit;
        }
        $id = trim($this->input->post("id"));
        $idArr = explode(";",$id);
        $this->load->model('User');
        $userInfo = $this->User->getUserInfoByFiled(array("id"=>$this->userId));
        foreach($idArr as $idVal) {
            if (empty($idVal)) {
                continue;
            }
            $info = $this->Notice->getNoticeInfoByFiled(array("userId" => $this->userId,"infoId" => $idVal,"reply"=>0,"del"=>0));
            if (empty($info)) {
                $dataArr = array(
                    "userId" => $this->userId,
                    "infoId"=>$idVal,
                    "time" => time(),
                    "email" => $userInfo['email'],
                );
                $this->Notice->insertNoticeInfo($dataArr);
            }
        }
        $result['code'] = "success";
        $result['info'] = "success";
        echo json_encode($result);
        exit;
    }

    public function changepassword() {
        $result = array(
            "code" => "error",
            "info" => "请先退出登录",
        );
        if ($this->_checkLogin()) {
            echo json_encode($result);
            exit;
        }
        $username = $this->input->post("username");
        $email = $this->input->post("email");
        if (empty($username) || empty($email)) {
            $result['info'] = "参数错误";
            echo json_encode($result);
            exit;
        } elseif(!preg_match("/^[0-9a-zA-Z]+(?:[\_\-][a-z0-9\-]+)*@[a-zA-Z0-9]+(?:[-.][a-zA-Z0-9]+)*\.[a-zA-Z]+$/i", $email)) {
            $result['info'] = '安全邮箱格式不正确';
            echo json_encode($result);
            exit;
        } else {
            $this->load->model('User');
            $userInfo = $this->User->getUserInfoByFiled(array("userName"=>$username,"email"=>$email));
            if (empty($userInfo)) {
                $result['info'] = '登录帐号或安全邮箱不正确';
                echo json_encode($result);
                exit;
            } else {
                $time = time();
                $key = md5($username . $email . $time);
                $data = array(
                    "userId" => $userInfo['id'],
                    "hash_key" => $key,
                    "time" => $time,
                    "del" => 1,
                );
                $this->load->model('Changepassword');
                $this->Changepassword->insertInfo($data);
                $this->load->model('Email');
                $emailData['email'] = $email;
                $emailData['userName'] = $username;
                $emailData['time'] = time();
                $emailData['title'] = "密码改更-" . APF::get_instance()->get_config_value("base_name");
                $time = date("Y-m-d H:i:s");
                $url = trim(("base_url"),"/") . "/password/change?key={$key}";
                $emailData['content'] = "尊敬的{$userInfo['userName']}用户，您在{$time}发出更改密码行为，请点击链接[url={$url}]{$url}[/url]完成更改。如非您本人操作，请与" . APF::get_instance()->get_config_value("base_name") . "管理员联系或者更改安全邮箱。谢谢！";
                $this->Email->insertEmailInfo($emailData);
                $result['code'] = $userInfo['id'];
                $result['info'] = time();
                echo json_encode($result);
                exit;
            }
        }

    }

    public function changepassworddo() {
        $result = array(
            "code" => "请先退出登录",
            "info" => get_url("/"),
        );
        if ($this->_checkLogin()) {
            echo json_encode($result);
            exit;
        }
        $password1 = $this->input->post("password1");
        $password2 = $this->input->post("password2");
        $key = $this->input->post("key");
        if (empty($password1) || empty($password2) || empty($key)) {
            $result['code'] = "参数错误";
            echo json_encode($result);
            exit;
        } elseif (strlen($password1) < 6 || strlen($password1) > 20) {
            $result['code'] = '登录密码长度必须为6-20个字符！';
            echo json_encode($result);
            exit;
        } elseif ($password1 != $password2) {
            $result['code'] = '两次输入的密码不一致';
            echo json_encode($result);
            exit;
        } else {
            $this->load->model('Changepassword');
            $info = $this->Changepassword->getInfoByFiled(array("hash_key" => $key,"del" => 1));
            if (empty($info)) {
                $result['code'] = '页面已过期';
                $result['info'] = get_url("/password?r=" . time());
                echo json_encode($result);
                exit;
            } else {
                $maxTime = APF::get_instance()->get_config_value("changepassword_max_time");
                if (time() > ($info['time'] + $maxTime)) {//页面已过期
                    $result['code'] = '页面已过期';
                    $result['info'] = get_url("/password?r=" . time());
                    echo json_encode($result);
                    exit;
                } else {
                    $this->load->model('User');
                    $this->User->updateUserInfo(array("password"=>base64_encode(md5($password1))),array("id"=>$info['userId']));
                    $this->Changepassword->updateInfoByFiled(array("del" => 0),array("hash_key" => "'{$key}'"));
                    $result['code'] = "更改成功！";
                    $result['info'] = get_url("/login?r=" . time());
                    echo json_encode($result);
                    exit;
                }
            }
        }
    }

    public function shoucang()
    {
        if (!$this->_checkLogin()) {
            echo json_encode(array("code" => "error","info" => "请先登录"));
            exit;
        }
        $id = $this->input->post("id");
        if (empty($id)) {
            echo json_encode(array("code" => "error","info" => "非法操作"));
            exit;
        }
        $this->load->model('Shoucang');
        $userShouCangCount = $this->Shoucang->getInfoCountByFiled(array("userId"=>$this->userId,"del"=>0));
        if ($userShouCangCount >= APF::get_instance()->get_config_value("shoucang_max_count")) {
            echo json_encode(array("code" => "error","info" => "最多可收藏{$userShouCangCount}部电影"));
            exit;
        }
        $info = $this->Shoucang->getInfoByFiled(array("userId"=>$this->userId,"infoId"=>$id,"del"=>0));
        if (empty($info)) {
            $this->Shoucang->insertShouCangInfo(array("userId"=>$this->userId,"infoId"=>$id,"del"=>0,"time"=>time()));
        }
        echo json_encode(array("code" => "success","info" => "success"));
        exit;
    }

    public function getyingping()
    {
        $result = array(
            "code" => "error",
            "info" => "参数错误",
        );
        $id = $this->input->post("id");
        $count = $this->input->post("count");
        if (empty($id) || !isset($count)) {
            echo json_encode($result);
            exit;
        }
        $this->load->model('Yingping');
        $limit = APF::get_instance()->get_config_valueF("post_show_count");
        $YingpingInfo = $this->Yingping->getYingPingInfoByDyId($id,$count,$limit);
        $result['info'] = array();
        if (!empty($YingpingInfo)) {
            $userIds = array();
            foreach($YingpingInfo as $InfoKey => $infoVal) {
                $YingpingInfo[$InfoKey]['content'] = $this->ubb2Html($infoVal['content']);
                $YingpingInfo[$InfoKey]['date'] = date("Y-m-d H:i:s",$infoVal['time']);
                $userIds[] = $infoVal['userId'];
            }
            $this->load->model('User');
            $userInfos = $this->User->getUserInfosByIds($userIds);
            foreach($userInfos as $userKey => $userVal) {
                $userInfos[$userKey]['photo'] = empty($userVal['photo']) ? trim(("img_base_url"), "/") . ("user_photo") : trim(("img_base_url"), "/") . $userVal['photo'];
            }
            $userInfos = $this->initArrById($userInfos,"id");
            $result['info']['yingping'] = $YingpingInfo;
            $result['info']['userinfo'] = $userInfos;
        }
        $result['code']  = 'success';
        $result['info']['count']  = $count + count($YingpingInfo);
        echo json_encode($result);
        exit;
    }

    public function addlink() {
        $result = array(
            "code" => "error",
            "info" => "参数错误",
        );
        $id = $this->input->post("id");
        $type = $this->input->post("type");
        $url = $this->input->post("url");
        if (empty($id) || !isset($type) || ($type != 1 && $type != 2) || empty($url)) {
            echo json_encode($result);
            exit;
        }
        if (!strstr($url,"http://")) {
            $url = "http://{$url}";
        }
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $result['info'] = "请输入正确的链接";
            echo json_encode($result);
            exit;
        }
        $this->load->model("Addlink");
        $url = mysql_real_escape_string($url);
        $info = $this->Addlink->getLinkInfoByFiled(array("link"=>$url));
        if (empty($info)) {
            $this->Addlink->insertLinkInfo(array("infoId"=>$id,"link"=>$url,"type"=>$type,"time"=>time()));
        }
        $result['code'] = "success";
        $result['info'] = "提交成功，感谢您对我们工作的支持！";
        echo json_encode($result);
        exit;
    }
}