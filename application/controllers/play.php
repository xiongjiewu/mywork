<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 视频播放页面
 * added by xiongjiewu at 2013-06-09
 */
class Play extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('Backgroundadmin');
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
        if (empty($watchLinkInfo[$watchId]) || $watchLinkInfo[$watchId]['infoId'] != $id) {
            $this->jump_to("/error/index/1/");
            exit;
        }
        $this->set_attr("watchInfo",$watchLinkInfo[$watchId]);
        unset($watchLinkInfo[$watchId]);
        $this->set_attr("watchLinkInfo",$watchLinkInfo);

        $this->load->set_title("{$dyInfo['name']} 在线观看 - "  . $this->base_title .  " - " . APF::get_instance()->get_config_value("base_name"));
        $this->load->set_css(array("css/dianying/play.css"));
        $this->load->set_js(array("js/xheditor-1.2.1/xheditor-1.2.1.min.js","js/xheditor-1.2.1/xheditor_lang/zh-cn.js","js/dianying/play.js"));
        $this->load->set_top_index(-1);
        $this->load->set_head_img(false);
        $this->set_attr("qingxiType",$this->_qingxiType);
        $this->set_attr("shoufeiType",$this->_shoufeiType);
        $this->set_attr("bofangqiType",$this->_bofangqiType);
        $this->set_view('dianying/play','common');
    }
}