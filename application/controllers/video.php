<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站播放页面，自己的播放页面，而不是通过iframe嵌入
 * added by xiongjiewu at 2013-08-10
 */
class Video extends CI_Controller
{

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->set_view("dianying/video","base3");
    }
}