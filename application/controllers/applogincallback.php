<?php
/**
 * 网站第三方接口登录回调页面
 * added by xiongjiewu at 2013-06-22
 */
class Applogincallback extends MY_Controller
{
    private $_webLoginInfo;
    private $_nowTime;
    private $_loginBase64;
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->_webLoginInfo = APF::get_instance()->get_config_value("web_app_info","webapp");
        $this->_nowTime = time();
        $this->_loginBase64 = base64_encode("/login/");
        $this->load->model('Appuser');
        $this->load->model('User');
    }
    public function index() {
        $this->jump_to("/error/");
    }

    /**
     * QQ登录处理接口
     */
    public function qq() {
        $code = $this->input->get("code");
        if (!empty($code)) {//code验证
            $this->_echoLogin();
            $stateStr = $this->input->get("state");
            $stateArr = explode("[A]",$stateStr);
            $state = APF::get_instance()->decodeId($stateArr[0]);
            $state = intval($state);
            //判断页面是否失效
            if (empty($state) || ($this->_nowTime - $state) > $this->_webLoginInfo['qq']['codeTime']) {
                $this->jump_to("/error/index/4?bgurl=" . $this->_loginBase64);//页面已过期
                exit;
            }

            $this->_webLoginInfo['qq']['getToken']['params']['code'] = $code;
            $tokenInfo = APF::get_instance()->myCurl($this->_webLoginInfo['qq']['getToken']['baseUrl'],$this->_webLoginInfo['qq']['getToken']['params']);
            parse_str($tokenInfo,$tokenArr);
            //信息不存在
            if (empty($tokenArr['access_token']) || empty($tokenArr['expires_in'])) {
                $this->jump_to("/error/index/4?bgurl=" . $this->_loginBase64);//页面已过期
                exit;
            }

            //获取openid
            $this->_webLoginInfo['qq']['getOpenId']['params']['access_token'] = $tokenArr['access_token'];
            $openInfo = APF::get_instance()->myCurl($this->_webLoginInfo['qq']['getOpenId']['baseUrl'],$this->_webLoginInfo['qq']['getOpenId']['params']);
            preg_match('/\((.*?)\)/i',$openInfo,$openArr);
            if (empty($openArr[1])) {
                $this->jump_to("/error/index/4?bgurl=" . $this->_loginBase64);//页面已过期
                exit;
            }
            $openIdStr = json_decode(trim($openArr[1]),true);
            if (empty($openIdStr['openid'])) {
                $this->jump_to("/error/index/4?bgurl=" . $this->_loginBase64);//页面已过期
                exit;
            }

            //获取用户信息
            $this->_webLoginInfo['qq']['getUserInfo']['params']['access_token'] = $tokenArr['access_token'];
            $this->_webLoginInfo['qq']['getUserInfo']['params']['openid'] = $openIdStr['openid'];
            $userInfo = APF::get_instance()->myCurl($this->_webLoginInfo['qq']['getUserInfo']['baseUrl'],$this->_webLoginInfo['qq']['getUserInfo']['params'],true);
            if (!isset($userInfo['ret']) || $userInfo['ret'] != 0) {//返回信息错误
                $this->jump_to("/error/index/4?bgurl=" . $this->_loginBase64);//页面已过期
                exit;
            }

            //查询用户是否存在，不存在则插入，存在则更新
            $type = $this->_webLoginInfo['qq']['type'];
            $appUserInfo = $this->Appuser->getAppUserInfoByKeyAndType($openIdStr['openid'],$type);
            if (empty($appUserInfo)) {
                $insertInfo = array();
                $insertInfo['nickName'] = $userInfo['nickname'];
                $insertInfo['appKey'] = $openIdStr['openid'];
                $insertInfo['type'] = $type;
                $userId = $this->_insertUserInfo($insertInfo,$userInfo['figureurl_1']);
                if (empty($userId)) {
                    $this->jump_to("/error/index/3?bgurl=" . $this->_loginBase64);//页面已过期
                    exit;
                }
            } else {
                $userId = $appUserInfo['userId'];

                //查询用户是否被封禁
                $uInfo = $this->User->getUserInfoByFiled(array("id" => $userId));
                if ($uInfo['status'] == 1) {//被封禁
                    $this->jump_to("/error/index/8?bgurl=" . $this->_loginBase64);//封禁提示页
                    exit;
                }

                //更新昵称
                if ($userInfo['nickname'] != $appUserInfo['nickName']) {
                    $this->User->updateUserInfo(array("userName" => $userInfo['nickname']),array("id" => $userId));
                    $this->Appuser->updateAppUserInfo(array("nickName" => $userInfo['nickname']),array("id" => $appUserInfo['id']));
                }
            }
            $this->setLoginCookie($userInfo['nickname'],$userId,86400);
            $refererUrl = empty($stateArr[1]) ? "/" : base64_decode($stateArr[1]);
            $this->jump_to($refererUrl);//登录成功跳转致来源链接
            exit;
        }
        $this->jump_to("/");//失败跳转致首页
        exit;
    }

    /**
     * 微博登录处理接口
     */
    public function weibo() {
        $code = $this->input->get("code");
        if (!empty($code)) {//code验证
            $this->_echoLogin();
            $stateStr = $this->input->get("state");
            $stateArr = explode("[A]",$stateStr);
            $state = APF::get_instance()->decodeId($stateArr[0]);
            $state = intval($state);
            //判断页面是否失效
            if (empty($state) || ($this->_nowTime - $state) > $this->_webLoginInfo['weibo']['codeTime']) {
                $this->jump_to("/error/index/4?bgurl=" . $this->_loginBase64);//页面已过期
                exit;
            }
            $this->_webLoginInfo['weibo']['getToken']['params']['code'] = $code;
            $tokenInfo = APF::get_instance()->myCurl($this->_webLoginInfo['weibo']['getToken']['baseUrl'],$this->_webLoginInfo['weibo']['getToken']['params'],true,false);
            //信息不存在
            if (empty($tokenInfo['access_token']) || empty($tokenInfo['uid'])) {
                $this->jump_to("/error/index/4?bgurl=" . $this->_loginBase64);//页面已过期
                exit;
            }

            //获取用户信息
            $this->_webLoginInfo['weibo']['getUserInfo']['params']['access_token'] = $tokenInfo['access_token'];
            $this->_webLoginInfo['weibo']['getUserInfo']['params']['uid'] = $tokenInfo['uid'];
            $userInfo = APF::get_instance()->myCurl($this->_webLoginInfo['weibo']['getUserInfo']['baseUrl'],$this->_webLoginInfo['weibo']['getUserInfo']['params'],true);
            if (!empty($userInfo['error_code'])) {//返回信息错误
                $this->jump_to("/error/index/4?bgurl=" . $this->_loginBase64);//页面已过期
                exit;
            }

            //查询用户是否存在，不存在则插入，存在则更新
            $type = $this->_webLoginInfo['weibo']['type'];
            $appUserInfo = $this->Appuser->getAppUserInfoByKeyAndType($tokenInfo['uid'],$type);
            if (empty($appUserInfo)) {
                $insertInfo = array();
                $insertInfo['nickName'] = $userInfo['name'];
                $insertInfo['appKey'] = $tokenInfo['uid'];
                $insertInfo['type'] = $type;
                $userId = $this->_insertUserInfo($insertInfo,$userInfo['profile_image_url']);
                if (empty($userId)) {
                    $this->jump_to("/error/index/3?bgurl=" . $this->_loginBase64);//页面已过期
                    exit;
                }
            } else {
                $userId = $appUserInfo['userId'];

                //查询用户是否被封禁
                $uInfo = $this->User->getUserInfoByFiled(array("id" => $userId));
                if ($uInfo['status'] == 1) {//被封禁
                    $this->jump_to("/error/index/8?bgurl=" . $this->_loginBase64);//封禁提示页
                    exit;
                }

                //更新昵称
                if ($userInfo['name'] != $appUserInfo['nickName']) {
                    $this->User->updateUserInfo(array("userName" => $userInfo['name']),array("id" => $userId));
                    $this->Appuser->updateAppUserInfo(array("nickName" => $userInfo['name']),array("id" => $appUserInfo['id']));
                }
            }
            $this->setLoginCookie($userInfo['name'],$userId,86400);
            $refererUrl = empty($stateArr[1]) ? "/" : base64_decode($stateArr[1]);
            $this->jump_to($refererUrl);//登录成功跳转致来源链接
            exit;
        }
        $this->jump_to("/");//失败跳转致首页
        exit;
    }

    /**
     * 人人登录接口处理函数
     */
    public function renren() {
        $code = $this->input->get("code");
        if (!empty($code)) {//code验证
            $this->_echoLogin();
            $stateStr = $this->input->get("state");
            $stateArr = explode("[A]",$stateStr);
            $state = APF::get_instance()->decodeId($stateArr[0]);
            $state = intval($state);
            //判断页面是否失效
            if (empty($state) || ($this->_nowTime - $state) > $this->_webLoginInfo['renren']['codeTime']) {
                $this->jump_to("/error/index/4?bgurl=" . $this->_loginBase64);//页面已过期
                exit;
            }
            $this->_webLoginInfo['renren']['getToken']['params']['code'] = $code;
            $tokenInfo = APF::get_instance()->myCurl($this->_webLoginInfo['renren']['getToken']['baseUrl'],$this->_webLoginInfo['renren']['getToken']['params'],true,false);
            //信息不存在
            if (!empty($tokenInfo['error']) || empty($tokenInfo['user'])) {
                $this->jump_to("/error/index/4?bgurl=" . $this->_loginBase64);//页面已过期
                exit;
            }

            //查询用户是否存在，不存在则插入，存在则更新
            $type = $this->_webLoginInfo['renren']['type'];
            $appUserInfo = $this->Appuser->getAppUserInfoByKeyAndType($tokenInfo['user']['id'],$type);
            if (empty($appUserInfo)) {
                $insertInfo = array();
                $insertInfo['nickName'] = $tokenInfo['user']['name'];
                $insertInfo['appKey'] = $tokenInfo['user']['id'];
                $insertInfo['type'] = $type;
                $userId = $this->_insertUserInfo($insertInfo,$tokenInfo['user']['avatar'][0]['url']);
                if (empty($userId)) {
                    $this->jump_to("/error/index/3?bgurl=" . $this->_loginBase64);//页面已过期
                    exit;
                }
            } else {
                $userId = $appUserInfo['userId'];

                //查询用户是否被封禁
                $uInfo = $this->User->getUserInfoByFiled(array("id" => $userId));
                if ($uInfo['status'] == 1) {//被封禁
                    $this->jump_to("/error/index/8?bgurl=" . $this->_loginBase64);//封禁提示页
                    exit;
                }

                //更新昵称
                if ($tokenInfo['user']['name'] != $appUserInfo['nickName']) {
                    $this->User->updateUserInfo(array("userName" => $tokenInfo['user']['name']),array("id" => $userId));
                    $this->Appuser->updateAppUserInfo(array("nickName" => $tokenInfo['user']['name']),array("id" => $appUserInfo['id']));
                }
            }
            $this->setLoginCookie($tokenInfo['user']['name'],$userId,86400);
            $refererUrl = empty($stateArr[1]) ? "/" : base64_decode($stateArr[1]);
            $this->jump_to($refererUrl);//登录成功跳转致来源链接
            exit;
        }
        $this->jump_to("/");//失败跳转致首页
        exit;
    }

    /**
     * 输出登录中
     */
    private function _echoLogin() {
        echo '<html lang="en"><head><meta charset="utf-8"></head><body>Loging...</body>';
    }

    /**
     * 插入用户信息
     * @param $appUserInfo
     * @param null $photo
     * @param null $email
     * @return bool
     */
    private function _insertUserInfo($appUserInfo,$photo = null,$email = null) {
        if (empty($appUserInfo)) {
            return false;
        }
        //用户表信息
        $userInfo = array();
        $userInfo['userName'] = $appUserInfo['nickName'];
        $userInfo['password'] = base64_encode(md5(123456));//初始密码为123456
        $userInfo['time'] = $this->_nowTime;
        $userInfo['ip'] = $this->getUserIP();
        $userInfo['photo'] = empty($photo) ? "" : $photo;
        $userInfo['email'] = empty($email) ? "" : $email;
        $userInfo['type'] = $appUserInfo['type'];
        $userId = $this->User->insertUserInfo($userInfo);
        if (!empty($userId)) {
            //appUser表信息
            $appUserInfo['userId'] = $userId;
            $appUserInfo['createTime'] = $this->_nowTime;
            $appUserInfo['ip'] = $this->getUserIP();
            $appUserId = $this->Appuser->insertAppuserInfo($appUserInfo);
            if (empty($appUserId)) {
                return false;
            }
            return $userId;
        }
        return false;
    }
}