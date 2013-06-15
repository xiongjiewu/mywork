<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站电影详细信息页面
 * added by xiongjiewu at 2013-3-4
 */
class Detail extends CI_Controller {

    private $_caiLimit = 6;

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('Backgroundadmin');
    }
    public function index($id = null)
    {
        //将id字符串解密，转换成数字id
        $id = intval(APF::get_instance()->decodeId($id));
        if (empty($id)) {
            redirect("/" );
            exit;
        }

        $dyInfo = $this->Backgroundadmin->getDetailInfo($id,0);
        if (empty($dyInfo)) {
            $this->jump_to("/error/index/1/");
            exit;
        }
        //cookie名称+浏览记录
        $userLookInfo = $this->get_cookie($this->look_cookie_key);
        if (!empty($userLookInfo)) {
            $userLookInfo = json_decode($userLookInfo,true);
        }
        $lookArr = array("id" => $dyInfo['id'],"name" => $dyInfo['name']);
        if (!empty($userLookInfo) && !in_array($lookArr,$userLookInfo)) {
            $userLookInfo = array_merge(array($dyInfo['id'] => $lookArr),$userLookInfo);
            $userLookInfo  = array_slice($userLookInfo,0,5);
            $this->set_cookie($this->look_cookie_key,json_encode($userLookInfo));
        } elseif(empty($userLookInfo)) {
            $userLookInfo  = array();
            $userLookInfo[$dyInfo['id']] = array("id" => $dyInfo['id'],"name" => $dyInfo['name']);
            $this->set_cookie($this->look_cookie_key,json_encode($userLookInfo));
        }

        $this->set_attr("userId",$this->userId);

        //电影介绍
        $dyInfo['jieshao'] = str_replace("　　","",$dyInfo['jieshao']);
        $dyInfo['jieshao'] = str_replace("　　","",trim($dyInfo['jieshao']));
        $dyInfo['s_jieshao'] = $this->splitStr($dyInfo['jieshao'],110);

        //第一个导演信息+猜你喜欢信息
        $caiNiXiHuanInfo  = array();
        if ($dyInfo['daoyan'] != "暂无") {
            $daoyan = str_replace("/","、",$dyInfo['daoyan']);
            $daoyanArr = explode("、",$daoyan);
            $daoyanName = $daoyanArr[0];
            $conStr = " order by nianfen desc";
            $caiNiXiHuanInfo = $this->Backgroundadmin->getDetailInfoBySearchDaoYan(trim($daoyanName),$this->_caiLimit,$conStr);
        }
        //猜你喜欢不够数，则获取主演相关
        $xiHuanCount = count($caiNiXiHuanInfo);
        if ($xiHuanCount < $this->_caiLimit) {
            if ($dyInfo['zhuyan'] != "暂无") {
                $zhuyan = str_replace("/","、",$dyInfo['zhuyan']);
                $zhuyanArr = explode("、",$zhuyan);
                $zhuyanName = $zhuyanArr[0];
                $conStr = " order by nianfen desc";
                $caiNiXiHuanInfo2 = $this->Backgroundadmin->getDetailInfoBySearchZhuYan(trim($zhuyanName),$this->_caiLimit - $xiHuanCount,$conStr);
                $caiNiXiHuanInfo = array_merge($caiNiXiHuanInfo,$caiNiXiHuanInfo2);
                //去重
                $caiNiXiHuanInfo = $this->initArrById($caiNiXiHuanInfo,"id");
            }
        }
        //猜你喜欢不够数，则获取类型相关
        $xiHuanCount = count($caiNiXiHuanInfo);
        if ($xiHuanCount < $this->_caiLimit) {
            $conStr = " type = " . $dyInfo['type'];
            $caiNiXiHuanInfo3 = $this->Backgroundadmin->getDetailInfoByCondition($conStr,0,$this->_caiLimit,$conStr);
            $caiNiXiHuanInfo = array_merge($caiNiXiHuanInfo,$caiNiXiHuanInfo3);
            //去重
            $caiNiXiHuanInfo = $this->initArrById($caiNiXiHuanInfo,"id");
        }
        unset($caiNiXiHuanInfo[$dyInfo['id']]);
        $caiNiXiHuanInfo = array_slice($caiNiXiHuanInfo,0,$this->_caiLimit);
        $this->set_attr("caiNiXiHuanInfo",$caiNiXiHuanInfo);

        //观看链接
        $watchLinkInfo = $this->Backgroundadmin->getWatchLinkInfoByInfoId($id);
        //下载链接
        $downLoadLinkInfo = $this->Backgroundadmin->getDownLoadLinkInfoByInfoId($id);
        $this->load->model('Yingping');
        $limit = APF::get_instance()->get_config_value("post_show_count");
        $YingpingInfo = $this->Yingping->getYingPingInfoByDyId($id,0,$limit);
        if (!empty($YingpingInfo)) {
            $userIds = array();
            $preg = "/^(https?:\/\/)?(((www\.)?[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)?\.([a-zA-Z]+))|(([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5]))(\:\d{0,4})?)(\/[\w- .\/?%&=]*)?$/i";
            foreach($YingpingInfo as $InfoKey => $infoVal) {
                $YingpingInfo[$InfoKey]['content'] = $this->ubb2Html($infoVal['content']);

                //匹配内容中的链接
                preg_match($preg, $YingpingInfo[$InfoKey]['content'], $matches);
                //过滤非站内广告链接
                if (!empty($matches[0]) && strpos($matches[0],".dianying8.tv") === false && strpos($matches[0],".dianyingba.tv") === false) {
                    $YingpingInfo[$InfoKey]['content'] = str_replace($matches[0],"顶！",$YingpingInfo[$InfoKey]['content']);
                }
                $userIds[] = $infoVal['userId'];
            }
            $this->load->model('User');
            $userInfos = $this->User->getUserInfosByIds($userIds);
            $userInfos = $this->initArrById($userInfos,"id");
            $this->set_attr("userInfos",$userInfos);

            $yingpingCount = $this->Yingping->getYingPingCountByDyId($id);
            $this->set_attr("yingpingCount",$yingpingCount);
        }
        $this->set_attr("limit",$limit);
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
        $this->load->set_title("{$dyInfo['name']} - " . $this->base_title .  " - " . APF::get_instance()->get_config_value("base_name"));
        $this->load->set_css(array("css/dianying/newdetail.css"));
        $this->load->set_js(array("js/xheditor-1.2.1/xheditor-1.2.1.min.js","js/xheditor-1.2.1/xheditor_lang/zh-cn.js","js/dianying/newdetail.js"));
        $this->load->set_top_index(-1);
        $this->load->set_head_img(false);
        
        $this->set_attr("dyInfo",$dyInfo);
        $this->set_attr("watchLinkInfo",$watchLinkInfo);
        foreach($downLoadLinkInfo as $downLoadLinkKey => $downLoadLinkVal) {
            if (empty($downLoadLinkVal['link'])) {
                unset($downLoadLinkInfo[$downLoadLinkKey]);
            }
        }
        $this->set_attr("downLoadLinkInfo",$downLoadLinkInfo);
        $this->set_attr("downLoadType",$this->_downLoadType);
        $this->set_attr("qingxiType",$this->_qingxiType);
        $this->set_attr("shoufeiType",$this->_shoufeiType);
        $this->set_attr("bofangqiType",$this->_bofangqiType);
        $this->set_attr("moviePlace",$this->_moviePlace);
        $this->set_attr("movieType",$this->_movieType);
        $this->set_view('dianying/newdetail');
    }
}