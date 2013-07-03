<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 信息错误提示页面
 * added by xiongjiewu at 2013-3-4
 */
class Topic extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->set_top_index(-1);
    }

    public function index($id = null)
    {
        //不展示调查问卷提示框
        $this->_attr['showResearchPan'] = false;

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
        $this->load->set_title("{$info['title']} - 帮助中心 - " . $this->base_title . " - " . APF::get_instance()->get_config_value("base_name"));
        $info['content'] = $this->ubb2Html($info['content']);
        $this->set_attr("info",$info);
        $helpInfos = $this->Help->getHelpInfoList(0,50);
        $this->set_attr("helpInfos",$helpInfos);
        $this->load->set_css(array("css/member/topic.css"));
        $this->load->set_top_index(-1);
        $this->load->set_head_img(false);
        
        $this->set_view("member/topic","base3");
    }
}