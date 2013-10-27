<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 记录页面来源
 * 用于后期网站功能分析
 * added by xiongjiewu at 2013-06-19
 */
class Notepageindex extends MY_Controller {
    private $_pageIndexInfo;
    public function __construct() {
        parent::__construct();
        $this->load->model('Pageaccessindex');
        $this->load->model('Backgroundadmin');
        $this->load->model('Userviewingrecords');
        //配置信息
        $this->_pageIndexInfo = APF::get_instance()->get_config_value("page_index_open","pageindex");
    }

    public function index() {
        $from = $this->input->post("from");//from字符串
        $from = htmlspecialchars($from);
        $referUrl = $this->input->post("refer");//来源
        $currentUrl = $this->input->post("current");//被访问页面
        if (empty($from) || empty($referUrl) || empty($currentUrl)) {
            echo json_encode(array("code" => "error","info" => "参数错误"));
            exit;
        }
        //判断配置
        if (empty($this->_pageIndexInfo[$from])) {
            echo json_encode(array("code" => "error","info" => "配置已关闭"));
            exit;
        }
        if (!strstr($referUrl,"http://")) {
            $referUrl = "http://{$referUrl}";
        }
        if (!strstr($currentUrl,"http://")) {
            $currentUrl = "http://{$currentUrl}";
        }
        //判断url是否合法
        if (!filter_var($referUrl, FILTER_VALIDATE_URL) || !filter_var($currentUrl, FILTER_VALIDATE_URL)) {
            echo json_encode(array("code" => "error","info" => "来源或者当前url参数格式错误"));
            exit;
        }
        //插入信息
        $this->Pageaccessindex->insertPageIndexInfo(array("referUrl" => $referUrl,"currentUrl" => $currentUrl,"ip" => $this->getUserIP(),"fromStr" => $from,"createTime" => time()));
        echo json_encode(array("code" => "success","info" => "success"));
        exit;
    }

    /**
     * 用户观看记录
     */
    public function userviewinfo() {
        if (empty($this->userId)) {
            echo json_encode(array("code" => "error","info" => "未登录"));
            exit;
        }
        $id = $this->input->post("id");//id字符串
        $id = intval($id);
        if (empty($id)) {
            echo json_encode(array("code" => "error","info" => "参数错误"));
            exit;
        }
        $dyInfo = $this->Backgroundadmin->getDetailInfo($id,0);
        if (empty($dyInfo)) {
            echo json_encode(array("code" => "error","info" => "参数错误"));
            exit;
        }

        //查询，相同电影3小时之内不可再写入
        $nowTime = time();
        $info = $this->Userviewingrecords->getUserViewingRecordsLastInfo($this->userId,$id);
        if (empty($info) || ($info["createTime"] - $nowTime) > 3 * 3600) {
            //写入记录
            $this->Userviewingrecords->insertUserViewingRecordsInfo(array("userId" => $this->userId,"infoId" => $id,"ip" => $this->getUserIP(),"createTime" => $nowTime));
        }
        echo json_encode(array("code" => "success","info" => "success"));
        exit;
    }
}