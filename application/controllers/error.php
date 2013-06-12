<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    /**
     * 信息错误提示页面
     * added by xiongjiewu at 2013-3-4
     */
class Error extends CI_Controller {

        public function index($index = null)
        {
            //不展示调查问卷提示框
            $this->_attr['showResearchPan'] = false;

            $index = empty($index) ? 1 : $index;
            $errorCode = APF::get_instance()->get_config_value("error_code");
            if (empty($errorCode[$index])) {
                $this->jump_to("/");
                exit;
            }
            $pageCode = $errorCode[$index];
            $bgurl = base64_decode($this->input->get("bgurl"));
	    $bgurl = htmlspecialchars($bgurl);
            if (!empty($bgurl)) {
                $pageCode['return_url'] = $bgurl;
            }
            $pageTitle = empty($pageCode['title']) ? "出错啦！" : $pageCode['title'];
            $this->load->set_title("{$pageTitle} - " . APF::get_instance()->get_config_value("base_title") ." - " . APF::get_instance()->get_config_value("base_name"));
            $this->set_attr("pageCode",$pageCode);
            $this->load->set_css(array("css/error/error.css"));
            $this->load->set_top_index(-1);
            $this->load->set_head_img(false);
            
            $this->set_view("error/error");
        }
}
