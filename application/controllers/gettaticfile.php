<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站css/js压缩控制class
 * added by xiongjiewu at 2013-3-4
 */
class Gettaticfile extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->css();
    }

    /**
     * css压缩
     */
    public function css() {
        $path = $this->input->get("path");
        if (empty($path)) {
            echo "";
            exit;
        }
        $path = base64_decode($path);
        $pathArr = explode(";",$path);
        $pathArr = array_filter($pathArr);
        if (empty($pathArr)) {
            echo "";
            exit;
        }

        ob_start();
        foreach($pathArr as $cssPath) {
            include("." . $cssPath);
        }
        $cssText = ob_get_contents();

        //输出CSS
        header('Content-type: text/css');
        echo $cssText;
    }

    /**
     * 压缩js文件
     */
    public function js() {
        $path = $this->input->get("path");
        if (empty($path)) {
            echo "";
            exit;
        }
        $path = base64_decode($path);
        $pathArr = explode(";",$path);
        $pathArr = array_filter($pathArr);
        if (empty($pathArr)) {
            echo "";
            exit;
        }

        ob_start();
        foreach($pathArr as $jsPath) {
            include("." . $jsPath);
        }
        $jsText = ob_get_contents();

        //输出Javascript
        header('Content-type: text/javascript');
        echo $jsText;
    }
}