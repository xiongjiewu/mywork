<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站首页
 * added by xiongjiewu at 2013-3-3
 */
class Welcome extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (empty($this->userId) || empty($this->userName) || empty($this->adminId)) {
            redirect(get_url("/login/"), true);//未登录
            exit;
        }
    }
	public function index()
	{
        $this->load->helper('url');
        redirect(get_url("/background/"), true);
	}
}