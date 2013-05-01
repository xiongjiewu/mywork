<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 关闭问卷调查窗口
 * added by xiongjiewu at 2013-5-1
 */
class Closeresearchpan extends CI_Controller {
    public function index() {
        $ip = ip2long($this->getUserIP());
        if (empty($this->Researchguide)) {
            $this->load->model("Researchguide");
        }
        $ipInfo = $this->Researchguide->getResearchGuideInfoByFiled(array("ip"=>$ip));
        if (empty($ipInfo)) {
            $id = $this->Researchguide->insertResearchGuideInfo(array("ip"=>$ip));
        } else {
            $id = $ipInfo['id'];
        }
        echo $id;
        exit;
    }
}