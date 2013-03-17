<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站退出
 * added by xiongjiewu at 2013-3-4
 */
class Logout extends CI_Controller {

    public function index()
    {
        //退出操作
        $cookie_name = get_config_value('AuthCookieName');
        $cookie_path = get_config_value('cookie_path');
        $cookie_domain = get_config_value('cookie_domain');
        $this->remove_cookie($cookie_name,$cookie_path,$cookie_domain);
        $re_url = $_SERVER['HTTP_REFERER'];
        $re_url = isset($re_url)?$re_url:"/";
        $this->load->helper('url');
        redirect("{$re_url}", true);
    }
}