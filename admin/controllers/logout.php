<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站退出
 * added by xiongjiewu at 2013-3-4
 */
class Logout extends CI_Controller {

    public function index()
    {
        //退出操作
        $this->remove_login_cookie();
        $this->load->helper('url');
        redirect(get_url("/login/","base"), true);
    }
}