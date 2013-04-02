<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站详细信息页面
 * added by xiongjiewu at 2013-3-4
 */
class Detail extends CI_Controller {

    public function index($id = null)
    {
        $this->load->helper('url');
        $id = intval($id);
        if (empty($id)) {
            redirect("/" );
        }
        $this->load->model('Backgroundadmin');
        $dyInfo = $this->Backgroundadmin->getDetailInfo($id,0);
        if (empty($dyInfo)) {
            redirect("/" );
        }
        $this->set_attr("userId",$this->userId);
        $dyInfo['jieshao'] = $this->splitStr($dyInfo['jieshao'],200);
        $watchLinkInfo = $this->Backgroundadmin->getWatchLinkInfoByInfoId($id);
        $downLoadLinkInfo = $this->Backgroundadmin->getDownLoadLinkInfoByInfoId($id);
        $this->load->model('Yingping');
        $YingpingInfo = $this->Yingping->getYingPingInfoByDyId($id,0,10);
        if (!empty($YingpingInfo)) {
            $userIds = array();
            foreach($YingpingInfo as $InfoKey => $infoVal) {
                $YingpingInfo[$InfoKey]['content'] = $this->ubb2Html($infoVal['content']);
                $userIds[] = $infoVal['userId'];
            }
            $this->load->model('User');
            $userInfos = $this->User->getUserInfosByIds($userIds);
            $userInfos = $this->initArrById($userInfos,"id");
            $this->set_attr("userInfos",$userInfos);
        }
        $this->set_attr("YingpingInfo",$YingpingInfo);

        if (!empty($this->userId)) {//已登录
            $this->load->model('Admin');
            $adminInfo = $this->Admin->getAdminInfoByUserId($this->userId);
            $this->set_attr("adminInfo",$adminInfo);

            $this->load->model('Shoucang');
            $shoucangInfo = $this->Shoucang->getInfoByFiled(array("userId"=>$this->userId,"infoId"=>$id,"del"=>0));
            $this->set_attr("shoucangInfo",$shoucangInfo);
            $this->set_attr("userId",$this->userId);

            $this->load->model('Notice');
            $moticeInfo = $this->Notice->getNoticeInfoByFiled(array("userId"=>$this->userId,"infoId"=>$id,"del"=>0));
            $this->set_attr("moticeInfo",$moticeInfo);
        }
        $this->load->set_title("{$dyInfo['name']} - 我们只专注于电影 - " . get_config_value("base_name"));
        $this->load->set_css(array("css/dianying/detail.css"));
        $this->load->set_js(array("js/xheditor-1.2.1/xheditor-1.2.1.min.js","js/xheditor-1.2.1/xheditor_lang/zh-cn.js","js/dianying/detail.js"));
        $this->load->set_top_index(-1);
        $this->load->set_head_img(false);
        $this->load->set_move_js(false);
        $this->set_attr("dyInfo",$dyInfo);
        $this->set_attr("watchLinkInfo",$watchLinkInfo);
        $this->set_attr("downLoadLinkInfo",$downLoadLinkInfo);
        $this->set_attr("downLoadType",$this->_downLoadType);
        $this->set_attr("qingxiType",$this->_qingxiType);
        $this->set_attr("shoufeiType",$this->_shoufeiType);
        $this->set_attr("bofangqiType",$this->_bofangqiType);
        $this->set_attr("moviePlace",$this->_moviePlace);
        $this->set_attr("movieType",$this->_movieType);
        $this->set_view('dianying/detail');
    }
}