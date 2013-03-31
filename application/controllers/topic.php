<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 信息错误提示页面
 * added by xiongjiewu at 2013-3-4
 */
class Topic extends CI_Controller {

    public function index($id = null)
    {
        if (empty($id)) {
            $this->jump_to("/");
            exit;
        }
        $this->load->model('Help');
        $info = $this->Help->getHelpInfoByFiled(array("id"=>$id));
        if (empty($info)) {
            if (empty($id)) {
                $this->jump_to("/");
                exit;
            }
        }
        $info['content'] = $this->ubb2Html($info['content']);
        $this->set_attr("info",$info);
        $helpInfos = $this->Help->getHelpInfoList(0,50);
        $this->set_attr("helpInfos",$helpInfos);
        $this->load->set_css(array("css/member/topic.css"));
        $this->load->set_top_index(-1);
        $this->load->set_head_img(false);
        $this->load->set_move_js(false);
        $this->set_view("member/topic");
    }
}