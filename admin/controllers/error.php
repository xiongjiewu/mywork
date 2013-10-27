<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    /**
     * 信息错误提示页面
     * added by xiongjiewu at 2013-3-4
     */
class Error extends MY_Controller {

        public function index($index = null)
        {
            $index = empty($index) ? 1 : $index;
            $errorCode = get_config_value("error_code");
            $pageCode = $errorCode[$index];
            $bgurl = base64_decode($this->input->get("bgurl"));
            if (!empty($bgurl)) {
                $pageCode['return_url'] = $bgurl;
            }
            $this->set_attr("pageCode",$pageCode);
            $this->load->set_css(array("css/error/error.css"));
            $this->load->set_top_index(-1);
            $this->set_view("error/error","base");
        }
}