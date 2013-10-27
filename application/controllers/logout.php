<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站退出
 * added by xiongjiewu at 2013-3-4
 */
class Logout extends MY_Controller {

    public function index()
    {
        //退出操作
        echo '<html lang="en"><head><meta charset="utf-8"></head><body>Logout...</body>';
        $this->remove_login_cookie();
        $re_url = $_SERVER['HTTP_REFERER'];
        $re_url = isset($re_url)?$re_url:"/";
        $this->load->helper('url');
        redirect("{$re_url}", true);
    }
}