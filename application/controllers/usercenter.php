<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站后台页面
 * added by xiongjiewu at 2013-3-4
 */
class Usercenter extends CI_Controller
{

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
    public function revised($type = "picture")
    {
        $this->_checkLogin();
        $this->set_attr("userId", $this->userId);

        $userInfo = $this->_getUserInfo();
        $this->set_attr("userInfo", $userInfo);

        $type = in_array($type,array("picture","password")) ? $type : "picture";
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
}