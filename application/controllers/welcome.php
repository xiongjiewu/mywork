<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站首页
 * added by xiongjiewu at 2013-3-3
 */
class Welcome extends CI_Controller {

	public function index()
	{
        $this->load->model('Backgroundadmin');
        $theNewestInfo = $this->Backgroundadmin->getNewestInfo(1);
        if (!empty($theNewestInfo[0])) {
            $idStr = trim($theNewestInfo[0]['infoIdStr'],";");
            $ids = explode(";",$idStr);
            if (!empty($ids)) {
                shuffle($ids);
                $ids = array_slice($ids,0,12);
                $newestDyInfo = $this->Backgroundadmin->getDetailInfo($ids,null,true);
                $this->set_attr("newestDyInfo",$newestDyInfo);
            }
        }
        $willInfo = $this->Backgroundadmin->getNewestInfo(2);
        if (!empty($willInfo[0])) {
            $idStr = trim($willInfo[0]['infoIdStr'],";");
            $ids = explode(";",$idStr);
            if (!empty($ids)) {
                shuffle($ids);
                $ids = array_slice($ids,0,12);
                $willDyInfo = $this->Backgroundadmin->getDetailInfo($ids,null,true);
                $this->set_attr("willDyInfo",$willDyInfo);
            }
        }
        $classInfo = $this->Backgroundadmin->getNewestInfo(3);
        if (!empty($classInfo[0])) {
            $idStr = trim($classInfo[0]['infoIdStr'],";");
            $ids = explode(";",$idStr);
            if (!empty($ids)) {
                shuffle($ids);
                $ids = array_slice($ids,0,12);
                $classDyInfo = $this->Backgroundadmin->getDetailInfo($ids,null,true);
                $this->set_attr("classDyInfo",$classDyInfo);
            }
        }
        $this->set_attr("baseNum",6);
        $this->load->set_css(array("/css/index/home.css"));
        $this->load->set_js(array("/js/index/home.js"));
        $this->load->set_title("首页 - " . $this->base_title . " - " . APF::get_instance()->get_config_value("base_name"));
        $this->set_view('index/home','common');

	}
}