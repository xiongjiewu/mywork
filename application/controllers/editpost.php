<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站编辑评论页面
 * added by xiongjiewu at 2013-3-4
 */
class Editpost extends CI_Controller {
    public function index($id = null) {
        if (empty($id) || empty($this->userId)) {
            $this->jump_to("/");
            exit;
        }
        $this->load->model('Admin');
        $adminInfo = $this->Admin->getAdminInfoByUserId($this->userId);
        if (empty($adminInfo)) {//不是管理员
            $this->jump_to("/");
            exit;
        }
        $this->load->model('Yingping');
        $YingpingInfo = $this->Yingping->getYingPingInfoByFiled(array("id"=>$id));
        if (empty($YingpingInfo) || ($YingpingInfo['del'] == 1)) {
            $this->jump_to("/");
            exit;
        }
        $content = $this->input->post("content");
        if ($content != "") {
            $this->Yingping->updateYingpingInfoById($id,array("content"=>"'{$content}'"));
            $this->jump_to("/detail/index/{$YingpingInfo['infoId']}/");
            exit;
        }
        $this->set_attr("YingpingInfo",$YingpingInfo);
        $this->load->set_title("电影吧，国内最强阵容");
        $this->load->set_css(array("css/dianying/editpost.css"));
        $this->load->set_js(array("js/xheditor-1.2.1/xheditor-1.2.1.min.js","js/xheditor-1.2.1/xheditor_lang/zh-cn.js","js/dianying/editpost.js"));
        $this->load->set_top_index(-1);
        $this->load->set_head_img(false);
        $this->load->set_move_js(false);
        $this->set_attr("YingpingInfo",$YingpingInfo);
        $this->set_view('dianying/editpost');
    }
}