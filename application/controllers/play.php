<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 视频播放页面
 * added by xiongjiewu at 2013-06-09
 */
class Play extends CI_Controller {

    private $_userWatchHistoryKey;//历史观看记录cookie名

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('Backgroundadmin');
        $this->_userWatchHistoryKey = "_userWatchHistory_info";
    }

    /** 入口主函数
     * @param null $id
     */
    public function index($id = null) {
        //将id字符串解密，转换成数字id
        $id = intval(APF::get_instance()->decodeId($id));
        if (empty($id)) {
            redirect("/" );
            exit;
        }

        $dyInfo = $this->Backgroundadmin->getDetailInfo($id,0);
        if (empty($dyInfo)) {//电影信息不存在
            $this->jump_to("/error/index/1/");
            exit;
        }
        $this->set_attr("dyInfo",$dyInfo);

        //观看链接
        $watchLinkInfo = $this->Backgroundadmin->getWatchLinkInfoByInfoId($id);
        $watchLinkInfo = $this->initArrById($watchLinkInfo,"id");
        $watchId = $this->input->get("id");
        //观看链接不存在或者不是电影观看链接
        if (empty($watchLinkInfo[$watchId]['link']) || $watchLinkInfo[$watchId]['infoId'] != $id) {
            $this->jump_to("/error/index/1/");
            exit;
        }
        $this->set_attr("watchInfo",$watchLinkInfo[$watchId]);
        unset($watchLinkInfo[$watchId]);
        $this->set_attr("watchLinkInfo",$watchLinkInfo);

        //cookie名称+观看记录
        $userWatchInfo = $this->get_cookie($this->_userWatchHistoryKey);
        if (!empty($userWatchInfo)) {
            $userWatchInfo = json_decode($userWatchInfo,true);
        }
        $lookArr = array("id" => $dyInfo['id'],"name" => $dyInfo['name']);
        if (!empty($userWatchInfo) && !in_array($lookArr,$userWatchInfo)) {
            $userWatchInfo = array_merge(array($dyInfo['id'] => $lookArr),$userWatchInfo);
            $userWatchInfo  = array_slice($userWatchInfo,0,5);
            $this->set_cookie($this->_userWatchHistoryKey,json_encode($userWatchInfo));
        } elseif(empty($userWatchInfo)) {
            $userWatchInfo  = array();
            $userWatchInfo[$dyInfo['id']] = array("id" => $dyInfo['id'],"name" => $dyInfo['name']);
            $this->set_cookie($this->_userWatchHistoryKey,json_encode($userWatchInfo));
        }
        $this->set_attr("userWatchInfo",$userWatchInfo);

        //更新影片播放次数
        $this->Backgroundadmin->updateDetailInfo($id,array("playNum" => $dyInfo['playNum'] + 1));

        $this->load->set_title("{$dyInfo['name']} 在线观看 - "  . $this->base_title .  " - " . APF::get_instance()->get_config_value("base_name"));
        $this->load->set_css(array("css/dianying/play.css"));
        $this->load->set_js(array("js/dianying/play.js"));
        $this->set_attr("qingxiType",$this->_qingxiType);
        $this->set_attr("shoufeiType",$this->_shoufeiType);
        $this->set_attr("bofangqiType",$this->_bofangqiType);
        $this->set_view('dianying/play','common');
    }
}