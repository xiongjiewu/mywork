<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站编辑评论页面
 * added by xiongjiewu at 2013-3-4
 */
class Editpost extends CI_Controller {
    public function index($id = null) {
        if (empty($id) || empty($this->userId)) {
            $this->jump_to("/");
            exit;
        }

    }
}