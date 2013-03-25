<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站用户行为controller,需登录
 * added by xiongjiewu at 2013-3-3
 */
class Useraction extends CI_Controller
{
    public function index()
    {


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
        } elseif (!isset($data['content'])) {
            echo "请输入内容！";
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
            $this->jump_to("/detail/index/{$data['dyId']}#createpost");
        }
    }

    public function ding()
    {
        if (!$this->_checkLogin()) {
            return json_encode(array("code" => "error","info" => "请先登录"));
        }
        $data = $this->input->post();
        if (empty($data['pid'])) {
            return array("code" => "error","info" => "非法操作");
        }
        $this->load->model('Yingping');
        $res = $this->Yingping->updateYingpingInfoById($data['pid'],array("ding" => "ding + 1"));
        if (empty($res)) {
            return json_encode(array("code" => "error","info" => "网络连接失败，清重新操作！"));
        } else {
            return json_encode(array("code" => "ok","info" => "操作成功"));
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
        if ($userNoticeCount >= get_config_value("notice_max_count")) {
            $result['info'] = "最多可预订" . get_config_value("notice_max_count") . "个通知";
            echo json_encode($result);
            exit;
        }
        $id = trim($this->input->post("id"));
        $idArr = explode(";",$id);
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
                );
                $this->Notice->insertNoticeInfo($dataArr);
            }
        }
        $result['code'] = "success";
        $result['info'] = "success";
        echo json_encode($result);
        exit;
    }
}