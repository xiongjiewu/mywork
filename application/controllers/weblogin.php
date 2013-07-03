<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站第三方接口登录页面
 * added by xiongjiewu at 2013-06-22
 */
class Weblogin extends CI_Controller
{
    private $_webLoginInfo;
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->_webLoginInfo = APF::get_instance()->get_config_value("web_app_info","webapp");
    }
    public function index() {
        $this->jump_to("/error/");
    }

    /**
     * QQ接口登录
     */
    public function qq() {
        $loginUrl = $this->_webLoginInfo['qq']['loginInfo']['baseUrl'] . "?" . http_build_query($this->_webLoginInfo['qq']['loginInfo']['params']);
        redirect("{$loginUrl}", true);
        exit;
    }

    /**
     * 微博接口登录
     */
    public function weibo() {
        $loginUrl = $this->_webLoginInfo['weibo']['loginInfo']['baseUrl'] . "?" . http_build_query($this->_webLoginInfo['weibo']['loginInfo']['params']);
        redirect("{$loginUrl}", true);
        exit;
    }
    /**
     * 人人接口登录
     */
    public function renren() {
        $loginUrl = $this->_webLoginInfo['renren']['loginInfo']['baseUrl'] . "?" . http_build_query($this->_webLoginInfo['renren']['loginInfo']['params']);
        redirect("{$loginUrl}", true);
        exit;
    }

}