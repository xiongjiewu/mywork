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
            $idStr = explode(";",$theNewestInfo[0]['infoIdStr']);
            $ids = array();
            foreach($idStr as $idVal) {
                if (empty($idVal)) {
                    continue;
                }
                $ids[] = $idVal;
            }
            if (!empty($ids)) {
                $newestDyInfo = $this->Backgroundadmin->getDetailInfo($ids,null,true);
                $this->set_attr("newestDyInfo",$newestDyInfo);
            }
        }
        $willInfo = $this->Backgroundadmin->getNewestInfo(2);
        if (!empty($willInfo[0])) {
            $idStr = explode(";",$willInfo[0]['infoIdStr']);
            $ids = array();
            foreach($idStr as $idVal) {
                if (empty($idVal)) {
                    continue;
                }
                $ids[] = $idVal;
            }
            if (!empty($ids)) {
                $willDyInfo = $this->Backgroundadmin->getDetailInfo($ids,null,true);
                $this->set_attr("willDyInfo",$willDyInfo);
            }
        }
        $classInfo = $this->Backgroundadmin->getNewestInfo(3);
        if (!empty($classInfo[0])) {
            $idStr = explode(";",$classInfo[0]['infoIdStr']);
            $ids = array();
            foreach($idStr as $idVal) {
                if (empty($idVal)) {
                    continue;
                }
                $ids[] = $idVal;
            }
            if (!empty($ids)) {
                $classDyInfo = $this->Backgroundadmin->getDetailInfo($ids,null,true);
                $this->set_attr("classDyInfo",$classDyInfo);
            }
        }
        $this->set_attr("baseNum",6);
        $this->load->set_css(array("/css/index/index.css"));
        $this->load->set_title("我们只专注于电影，您想看的就是我们宗旨 - " . get_config_value("base_name"));
        $this->set_view('index/index','base');

	}
}