<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 问卷调查页面
 * added by xiongjiewu at 2013-5-1
 */
class Research extends CI_Controller {

    public function index() {
        //不展示调查问卷提示框
        $this->_attr['showResearchPan'] = false;

        //用户已参加过调查问卷
        if (empty($this->_attr['notDoResearch'])) {
            $this->jump_to("/error/index/6/");
            exit;
        }

        $ip = ip2long($this->getUserIP());
        $params = $this->input->post();
        if (!empty($params) && is_array($params)) {
            $params['content'] = mysql_real_escape_string($params['content']);
            if (empty($this->Researchinsert)) {
                $this->load->model("Researchinsert");
            }
            $this->Researchinsert->insertResearchInfo(array("ip"=>$ip,"answer"=>json_encode($params)));
            if (empty($this->Researchguide)) {
                $this->load->model("Researchguide");
            }
            $ipInfo = $this->Researchguide->getResearchGuideInfoByFiled(array("ip"=>$ip));
            if (empty($ipInfo)) {
                $this->Researchguide->insertResearchGuideInfo(array("ip"=>$ip));
            }
            $this->jump_to("/error/index/7/");
            exit;
        }

        $this->load->set_head_img(false);
        $this->load->set_top_index(5);
        $this->load->set_title("功能问卷调查 - " . $this->base_title .  " - " . APF::get_instance()->get_config_value("base_name"));
        $this->load->set_css(array("/css/member/research.css"));
        $this->load->set_js(array("js/member/research.js"));
        $this->set_view('member/research');
    }
}