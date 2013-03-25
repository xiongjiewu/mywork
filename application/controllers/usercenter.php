<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站后台页面
 * added by xiongjiewu at 2013-3-4
 */
class Usercenter extends CI_Controller
{

    private $_feedBackLimit = 10;
    private $_feedBackMaxCount = 200;
    private $_noticeLimit = 10;
    private $_noticeMaxCount = 200;

    private function _checkLogin()
    {
        if (empty($this->userId)) { //未登录，跳转登录页
            $this->jump_to("/login/");
            exit;
        }
    }

    private function _getUserInfo()
    {
        $this->load->model('User');
        return $this->User->getUserInfoByFiled(array("id" => $this->userId));
    }

    /** 用户中心默认首页
     * @param string $type
     */
    public function index($type = "new")
    {
        $this->_checkLogin();
        $limit = 20;
        $this->load->model('Backgroundadmin');
        $type = (empty($type) || !in_array($type, array("new", "up", "hot"))) ? "new" : $type;
        $this->set_attr("type", $type);
        $more_url = null;
        if ($type == "new") {
            $more_url = get_url("/latestmovie/");
            $sortStr = $this->_movieSortType[5]['sort'];
            $sortS = "and time1 <=" . time();
            $sortStr = $sortS . "  " . $sortStr;
            $movieList = $this->Backgroundadmin->getDetailInfoList(0, $limit, 0, $sortStr);
        } elseif ($type == "up") {
            $more_url = get_url("/upcomingmovie/");
            $sortStr = $this->_movieSortType[7]['sort'];
            $sortS = "and time1 >" . time();
            $sortStr = $sortS . "  " . $sortStr;
            $movieList = $this->Backgroundadmin->getDetailInfoList(0, $limit, 0, $sortStr);
        } else {
            $limit = 10;
            $hotInfos = $this->Backgroundadmin->getHotYingDyInfos($limit);
            $idArr = array();
            foreach ($hotInfos as $hotVal) {
                $idArr[] = $hotVal['infoId'];
            }
            $movieList = $this->Backgroundadmin->getDetailInfo($idArr, false, true);
            $hotInfos = $this->initArrById($hotInfos, "infoId");
            $this->set_attr("hotInfos", $hotInfos);
        }
        $this->set_attr("more_url", $more_url);
        $this->set_attr("movieList", $movieList);
        $this->set_attr("userId", $this->userId);
        ;
        $userInfo = $this->_getUserInfo();
        $this->set_attr("userInfo", $userInfo);
        $this->load->set_head_img(false);
        $this->load->set_move_js(false);
        $this->load->set_top_index(-1);
        $this->load->set_css(array("css/user/usercenter.css"));
        $this->load->set_js(array("js/user/usercenter.js"));
        $this->set_attr("moviePlace", $this->_moviePlace);
        $this->set_attr("movieType", $this->_movieType);
        $this->set_view("user/usercenter");
    }

    /**
     * 我的收藏页面
     */
    public function mycollect()
    {
        $this->_checkLogin();
        $this->set_attr("userId", $this->userId);

        $userInfo = $this->_getUserInfo();
        $this->set_attr("userInfo", $userInfo);

        $this->load->model("Shoucang");
        $shouCangInfo = $this->Shoucang->getUserShoucangInfo($this->userId);
        if (!empty($shouCangInfo)) {
            $idArr = array();
            foreach ($shouCangInfo as $scVal) {
                $idArr[] = $scVal['infoId'];
            }
            $this->load->model("Backgroundadmin");
            $movieList = $this->Backgroundadmin->getDetailInfo($idArr, false, true);
            $this->set_attr("movieList", $movieList);
            $shouCangInfo = $this->initArrById($shouCangInfo, "infoId");
            $this->set_attr("shouCangInfo", $shouCangInfo);
        }
        $this->load->set_head_img(false);
        $this->load->set_move_js(false);
        $this->load->set_top_index(-1);
        $this->load->set_css(array("/css/user/usercenter.css"));
        $this->load->set_js(array("/js/user/mycollect.js"));
        $this->set_attr("moviePlace", $this->_moviePlace);
        $this->set_attr("movieType", $this->_movieType);
        $this->set_view("user/mycollect");
    }

    /**
     * 删除收藏操作
     */
    public function delshoucang()
    {
        $result = array(
            "code" => "error",
            "info" => "请先登录！",
        );
        if (empty($this->userId)) {
            echo json_encode($result);
            exit;
        }
        $id = trim($this->input->post("id"), ";");
        $idArr = explode(";", $id);
        if (empty($idArr)) {
            $result['info'] = "参数错误！";
            echo json_encode($result);
            exit;
        }

        $resIdArr = array();
        foreach ($idArr as $idV) {
            $idV = intval($idV);
            if (!empty($idV)) {
                $resIdArr[] = $idV;
            }
        }
        $this->load->model("Shoucang");
        $res = $this->Shoucang->updateUserShouCangInfoById($this->userId, $resIdArr);
        if (empty($res)) {
            $result['info'] = "网络连接失败，请重新尝试！";
            echo json_encode($result);
            exit;
        } else {
            $result['code'] = "success";
            $result['info'] = "操作成功！";
            echo json_encode($result);
            exit;
        }
    }

    /**
     * 更改资料页面
     */
    public function revised($type = "data")
    {
        $this->_checkLogin();
        $this->set_attr("userId", $this->userId);

        $userInfo = $this->_getUserInfo();
        $this->set_attr("userInfo", $userInfo);

        $type = in_array($type,array("data","picture","password")) ? $type : "data";
        $this->set_attr("type",$type);

        $this->load->set_head_img(false);
        $this->load->set_move_js(false);
        $this->load->set_top_index(-1);
        $this->load->set_css(array("/css/user/usercenter.css"));
        $this->load->set_js(array("/js/user/revised.js"));
        $this->set_attr("moviePlace", $this->_moviePlace);
        $this->set_attr("movieType", $this->_movieType);
        $this->set_view("user/revised");
    }

    public function resetpassword()
    {
        $oldPass = trim($this->input->post("oldPass"));
        $newPass1 = trim($this->input->post("newPass1"));
        $newPass2 = trim($this->input->post("newPass2"));
        $result = array(
            "code" => "error",
            "info" => "请先登录",
            "type" => "oldpass",
        );
        if (empty($this->userId)) {
            echo json_encode($result);
            exit;
        }
        if (!isset($oldPass)) {
            $result['info'] = "请输入旧密码";
            echo json_encode($result);
            exit;
        }
        if (!isset($newPass1)) {
            $result['info'] = "请输入新密码";
            $result['type'] = "newpass1";
            echo json_encode($result);
            exit;
        }
        if (!isset($newPass2)) {
            $result['info'] = "请输入确认密码";
            $result['type'] = "newpass2";
            echo json_encode($result);
            exit;
        }
        if ($newPass2 != $newPass1) {
            $result['info'] = "两次输入的密码不一致";
            $result['type'] = "newpass2";
            echo json_encode($result);
            exit;
        }
        $this->load->model('User');
        $info = $this->User->getUserInfoByFiled(array("userName" => $this->userName));
        if (empty($info)) {
            $result['info'] = "请重新登录";
            echo json_encode($result);
            exit;
        } elseif ($info['password'] != base64_encode(md5($oldPass))) {
            $result['info'] = "输入的密码不正确";
            echo json_encode($result);
            exit;
        } else {
            $this->User->updateUserInfo(array("password" => base64_encode(md5($newPass1))),array("id" => $this->userId));
            $this->remove_login_cookie();
            $result['code'] = "success";
            $result['info'] = "更改成功，请重新登录！";
            echo json_encode($result);
            exit;
        }
    }
    public function resetemail()
    {
        $email = trim($this->input->post("email"));
        $result = array(
            "code" => "error",
            "info" => "请先登录",
        );
        if (empty($this->userId)) {
            echo json_encode($result);
            exit;
        }
        if (!isset($email)) {
            $result['info'] = "请输入邮箱";
            echo json_encode($result);
            exit;
        } elseif(!preg_match("/^[0-9a-zA-Z]+(?:[\_\-][a-z0-9\-]+)*@[a-zA-Z0-9]+(?:[-.][a-zA-Z0-9]+)*\.[a-zA-Z]+$/i", $email)) {
            $result['info'] = '安全邮箱格式不正确';
            echo json_encode($result);
            exit;
        }
        $this->load->model('User');
        $info = $this->User->getUserInfoByFiled(array("email" => $email));
        if (!empty($info) && ($info['id'] != $this->userId)) {
            $result['info'] = "此邮箱已存在";
            echo json_encode($result);
            exit;
        } else {
            $this->User->updateUserInfo(array("email" => $email),array("id" => $this->userId));
            $result['code'] = "success";
            $result['info'] = "更改成功！";
            echo json_encode($result);
            exit;
        }
    }

    public function feedback($type = "want",$reply = null,$page = 1)
    {
        $this->_checkLogin();
        $this->set_attr("userId", $this->userId);

        $userInfo = $this->_getUserInfo();
        $this->set_attr("userInfo", $userInfo);

        $type = in_array($type,array("want","suggest")) ? $type : "want";
        $fY = ($type == "want") ? 1 : 2;
        $this->set_attr("type",$type);
        $selectData = array(
            "all" => "显示全部",
            "0" => "系统没回复的",
            "1" => "系统有回复的",
        );
        if (isset($reply) && ($reply != 0) && ($reply != 1 ) ) {
            $selectData = array(
                "all" => "显示全部",
                 "0" => "系统没回复的",
                "1" => "系统有回复的",
                );
            $reply = null;
        } elseif (isset($reply) && ($reply == "0")) {
            $selectData = array(
                "0" => "系统没回复的",
                "all" => "显示全部",
                "1" => "系统有回复的",
            );
        } elseif (isset($reply) && ($reply == "1")) {
            $selectData = array(
                "1" => "系统有回复的",
                "all" => "显示全部",
                "0" => "系统没回复的",
            );
        }
        $this->set_attr("selectData",$selectData);
        $this->load->model('Feedback');
        $page = empty($page) ? 1 : $page;
        $feedBackCount = $this->Feedback->getFeedbackInfoCountByUserId($this->userId,$reply,0,$fY);
        $feedBackCount = ($feedBackCount > $this->_feedBackMaxCount) ? $this->_feedBackMaxCount : $feedBackCount;

        if (($feedBackCount > 0) && ($page > ceil($feedBackCount / $this->_feedBackLimit))) {
            $page = ceil($feedBackCount / $this->_feedBackLimit);
        }
        $feedbackInfos = $this->Feedback->getFeedbackInfoListByUserId($this->userId,$reply,0,$fY,($page - 1) * $this->_feedBackLimit,$this->_feedBackLimit);
        $this->set_attr("feedbackInfos",$feedbackInfos);
        $this->set_attr("feedBackCount",$feedBackCount);
        $this->set_attr("limit",$this->_feedBackLimit);

        $base_url = get_url("/usercenter/feedback/{$type}/{$reply}/");
        $fenye = $this->set_page_info($page,$this->_feedBackLimit,$feedBackCount,$base_url);
        $this->set_attr("fenye",$fenye);

        $this->load->set_head_img(false);
        $this->load->set_move_js(false);
        $this->load->set_top_index(-1);
        $this->load->set_css(array("/css/user/usercenter.css"));
        $this->load->set_js(array("/js/user/feedback.js"));
        $this->set_attr("moviePlace", $this->_moviePlace);
        $this->set_attr("movieType", $this->_movieType);
        $this->set_view("user/feedback");
    }

    public function editfeedback($type = "want",$id = null)
    {
        $this->_checkLogin();
        $type = in_array($type,array("want","suggest")) ? $type : "want";
        if (empty($id)) {
            $this->jump_to("/usercenter/feedback/{$type}/");
            exit;
        }
        $this->load->model('Feedback');
        $feedbackInfo = $this->Feedback->getFeedBackInfosByIds($id);
        if (empty($feedbackInfo[0]) || ($feedbackInfo[0]['userId'] != $this->userId)) {
            $this->jump_to("/usercenter/feedback/{$type}/");
            exit;
        }
        $this->set_attr("feedbackInfo",$feedbackInfo[0]);
        $this->set_attr("type",$type);
        $this->set_attr("userId", $this->userId);
        $userInfo = $this->_getUserInfo();
        $this->set_attr("userInfo", $userInfo);
        $this->load->set_head_img(false);
        $this->load->set_move_js(false);
        $this->load->set_top_index(-1);
        $this->load->set_css(array("/css/user/usercenter.css"));
        $this->load->set_js(array("js/xheditor-1.2.1/xheditor-1.2.1.min.js","js/xheditor-1.2.1/xheditor_lang/zh-cn.js","js/dianying/detail.js","/js/user/editfeedback.js"));
        $this->set_view("user/editfeedback");
    }
    public function delfeedback()
    {
        $result = array(
            "code" => "error",
            "info" => "请先登录！",
        );
        if (empty($this->userId)) {
            echo json_encode($result);
            exit;
        }
        $id = trim($this->input->post("id"), ";");
        $idArr = explode(";", $id);
        if (empty($idArr)) {
            $result['info'] = "参数错误！";
            echo json_encode($result);
            exit;
        }

        $resIdArr = array();
        foreach ($idArr as $idV) {
            $idV = intval($idV);
            if (!empty($idV)) {
                $resIdArr[] = $idV;
            }
        }
        $this->load->model("Feedback");
        $res = $this->Feedback->updateUserFeedBackInfoById($this->userId, $resIdArr);
        if (empty($res)) {
            $result['info'] = "网络连接失败，请重新尝试！";
            echo json_encode($result);
            exit;
        } else {
            $result['code'] = "success";
            $result['info'] = "操作成功！";
            echo json_encode($result);
            exit;
        }
    }

    public function editfeedbacksubmit()
    {
        $this->_checkLogin();
        $id = trim($this->input->post("id"));
        if (empty($id)) {
            $this->jump_to("/usercenter/feedback/");
            exit;
        }
        $this->load->model('Feedback');
        $feedbackInfo = $this->Feedback->getFeedBackInfosByIds($id);
        if (empty($feedbackInfo[0]) || ($feedbackInfo[0]['userId'] != $this->userId)) {
            $this->jump_to("/usercenter/feedback/");
            exit;
        }
        $title = trim($this->input->post("title"));
        $content = trim($this->input->post("content"));
        if (!isset($title) || !isset($content)) {
            $this->jump_to("/usercenter/feedback/");
            exit;
        }
        $type = trim($this->input->post("type"));
        $this->Feedback->updateFeedbackInfo(array("title" => $title,"content" => $content),array("id"=>$id));
        $this->jump_to("/usercenter/editsuccess/{$type}/");
    }

    private function _showSuccess($type = "want",$index = null)
    {
        $this->_checkLogin();
        $userInfo = $this->_getUserInfo();
        $this->set_attr("userInfo", $userInfo);
        $text = $index ? "反馈提交成功" : "编辑成功";
        $this->set_attr("text",$text);
        $type = in_array($type,array("want","suggest")) ? $type : "want";
        $this->set_attr("type",$type);
        $this->load->set_head_img(false);
        $this->load->set_move_js(false);
        $this->load->set_top_index(-1);
        $this->load->set_css(array("/css/user/usercenter.css"));
        $this->set_view("user/editsuccess");
    }
    public function editsuccess($type = "want")
    {
        $this->_showSuccess($type);
    }

    public function createfeedback($type = "want")
    {
        $this->_checkLogin();
        $type = in_array($type,array("want","suggest")) ? $type : "want";

        $this->set_attr("type",$type);
        $this->set_attr("userId", $this->userId);
        $userInfo = $this->_getUserInfo();
        $this->set_attr("userInfo", $userInfo);
        $this->load->set_head_img(false);
        $this->load->set_move_js(false);
        $this->load->set_top_index(-1);
        $this->load->set_css(array("/css/user/usercenter.css"));
        $this->load->set_js(array("js/xheditor-1.2.1/xheditor-1.2.1.min.js","js/xheditor-1.2.1/xheditor_lang/zh-cn.js","js/dianying/detail.js","/js/user/createfeedback.js"));
        $this->set_view("user/createfeedback");
    }

    public function createfeedbacksubmit()
    {
        $this->_checkLogin();
        $title = trim($this->input->post("title"));
        $content = trim($this->input->post("content"));
        if (!isset($title) || !isset($content)) {
            $this->jump_to("/usercenter/feedback/");
            exit;
        }
        $this->load->model('Feedback');
        $type = trim($this->input->post("type"));
        $fY = ($type == "want") ? 1 : 2;
        $this->Feedback->insertFeedbackInfo(array("userId"=>$this->userId,"userName" => $this->userName,"time" => time(),"title" => $title,"content" => $content,"type" =>$fY));
        $this->jump_to("/usercenter/createsuccess/{$type}/");
        exit;
    }

    public function createsuccess($type = "want")
    {
        $this->_showSuccess($type,true);
    }

    public function notice($reply=null,$page = 1)
    {
        $this->_checkLogin();
        $this->set_attr("userId", $this->userId);

        $userInfo = $this->_getUserInfo();
        $this->set_attr("userInfo", $userInfo);

        $selectData = array(
            "all" => "显示全部",
            "0" => "系统没回复的",
            "1" => "系统有回复的",
        );
        $queryArr = array(
            "userId" => $this->userId,
            "del" => 0,
        );
        if (isset($reply) && ($reply != 0) && ($reply != 1 ) ) {
            $selectData = array(
                "all" => "显示全部",
                "0" => "系统没回复的",
                "1" => "系统有回复的",
            );
            $reply = "all";
        } elseif (isset($reply) && ($reply == "0")) {
            $selectData = array(
                "0" => "系统没回复的",
                "all" => "显示全部",
                "1" => "系统有回复的",
            );
            $queryArr['reply'] = 0;
        } elseif (isset($reply) && ($reply == "1")) {
            $selectData = array(
                "1" => "系统有回复的",
                "all" => "显示全部",
                "0" => "系统没回复的",
            );
            $queryArr['reply'] = 1;
        }
        $this->set_attr("selectData",$selectData);

        $page = intval($page);
        $page = empty($page) || ($page <=0) ? 1 : $page;
        $this->load->model("Notice");
        $userNoticeCount = $this->Notice->getNoticeCountByFiled($queryArr);
        $userNoticeCount = ($userNoticeCount > $this->_noticeMaxCount) ? $this->_noticeMaxCount : $userNoticeCount;
        if (($userNoticeCount > 0) && ($page > ceil($userNoticeCount/$this->_noticeLimit))) {
            $page = ceil($userNoticeCount/$this->_noticeLimit);
        }
        $userNoticeList = $this->Notice->getNoticeListByFiled($queryArr,($page-1) * $this->_noticeLimit,$this->_noticeLimit);
        if (!empty($userNoticeList)) {
            $infoIds = array();
            foreach($userNoticeList as $noticeVal) {
                $infoIds[] = $noticeVal['infoId'];
            }
            $this->load->model("Backgroundadmin");
            $infoList = $this->Backgroundadmin->getDetailInfo($infoIds,0,true);
            $infoList = $this->initArrById($infoList,"id");
            $this->set_attr("infoList",$infoList);
        }
        $this->set_attr("userNoticeList",$userNoticeList);
        $this->set_attr("userNoticeCount",$userNoticeCount);
        $this->set_attr("limit",$this->_noticeLimit);

        $base_url = get_url("/usercenter/notice/") . $reply . "/";
        $fenye = $this->set_page_info($page,$this->_noticeLimit,$userNoticeCount,$base_url);
        $this->set_attr("fenye",$fenye);

        $this->load->set_head_img(false);
        $this->load->set_move_js(false);
        $this->load->set_top_index(-1);
        $this->load->set_css(array("/css/user/usercenter.css"));
        $this->load->set_js(array("/js/user/notice.js"));
        $this->set_view("user/notice");
    }

    public function delnotice()
    {
        $result = array(
            "code" => "error",
            "info" => "请先登录！",
        );
        if (empty($this->userId)) {
            echo json_encode($result);
            exit;
        }
        $id = trim($this->input->post("id"), ";");
        $idArr = explode(";", $id);
        if (empty($idArr)) {
            $result['info'] = "参数错误！";
            echo json_encode($result);
            exit;
        }

        $resIdArr = array();
        foreach ($idArr as $idV) {
            $idV = intval($idV);
            if (!empty($idV)) {
                $resIdArr[] = $idV;
            }
        }
        $this->load->model("Notice");
        $res = $this->Notice->updateUserNoticeInfoById($this->userId, $resIdArr);
        if (empty($res)) {
            $result['info'] = "网络连接失败，请重新尝试！";
            echo json_encode($result);
            exit;
        } else {
            $result['code'] = "success";
            $result['info'] = "操作成功！";
            echo json_encode($result);
            exit;
        }
    }

    public function message()
    {

    }
}