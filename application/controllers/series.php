<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站系列大片页面
 * added by xiongjiewu at 2013-07-21
 */
class Series extends CI_Controller {
    private $_limit = 50;
    function __construct() {
        parent::__construct();
        $this->load->model('Movietopic');
        $this->load->model('Movietopicmovie');
        $this->load->model("Movietopicimg");
    }

    public function index() {
        $diqu = $this->input->get("place");
        $type = $this->input->get("type");
        //系列列表
        $topicList = $fTopicList = $this->_initTopicInfo($diqu,$type);
        if (empty($topicList)) {
            $this->jump_to("/series");
            exit;
        }
        $this->set_attr("diqu",$diqu);
        $this->set_attr("topicList",$topicList);

        //顶部和热门推荐系列
        shuffle($fTopicList);
        $this->set_attr("topTopicList",array_slice($fTopicList,0,5));
        shuffle($fTopicList);
        $this->set_attr("rightTopicList",array_slice($fTopicList,0,12));

        $this->load->set_top_index(-1);
        $this->set_attr("tabIndex",4);
        $this->load->set_title("系列大片 - " . $this->base_title . " - " . APF::get_instance()->get_config_value("base_name"));
        $this->load->set_css(array("/css/dianying/series.css"));
        $this->load->set_js(array("/js/dianying/series.js"));
        $this->set_attr("moviePlace",$this->_moviePlace);
        $this->set_attr("movieType",$this->_movieType);
        $this->set_view('dianying/series','base3');
    }

    /**
     * 序列化系列信息
     * @param string $diqu
     * @param string $type
     */
    private function _initTopicInfo($diqu = "",$type = "") {
        $topicList = $topicIdArr = array();
        $diqu = intval($diqu);
        $type = intval($type);
        if ((empty($diqu) && empty($type) ) || (empty($this->_moviePlace[$diqu]) && empty($this->_movieType[$type]))) {
            $topicList = $this->Movietopic->getTopicInfoList(2,0,$this->_limit);
        } elseif (!empty($this->_moviePlace[$diqu])) {
            $topicList = $this->Movietopic->getTopicInfoListByDiQu($diqu,2,0,$this->_limit);
        } elseif (!empty($this->_movieType[$type])) {
            $topicList = $this->Movietopic->getTopicInfoListByType($type,2,0,$this->_limit);
        }
        //将id作为数组key
        $topicList = $this->initArrById($topicList,"id",$topicIdArr);
        //电影信息，用作计算电影总部数
        $topicMovieList = $this->Movietopicmovie->getTopicMovieListByTopicId($topicIdArr);
        $topicList = $this->_initMovieCount($topicList,$topicMovieList);
        return $topicList;
    }

    /**
     * 拼接电影部数
     * @param $topicList
     * @param $topicMovieList
     * @return mixed
     */
    private function _initMovieCount($topicList,$topicMovieList) {
        if (empty($topicList) || empty($topicMovieList)) {
            return $topicList;
        }
        foreach($topicMovieList as $topicVal) {
            if (!isset($topicList[$topicVal['topicId']]['movieCount'])) {
                $topicList[$topicVal['topicId']]['movieCount'] = 0;
            }
            $topicList[$topicVal['topicId']]['movieCount']++;
        }
        return $topicList;
    }

    /**
     * 系列详细页面
     * @param string $id
     */
    public function info($idStr = "") {
        $idStr = trim($idStr);
        $id = APF::get_instance()->decodeId($idStr);
        if (empty($id)) {
            $this->jump_to("/series");
            exit;
        }

        //系列信息
        $status = $this->input->get("status");
        $status = empty($status) ? 1 : $status;
        $status = ($status == -1) ? 0 : $status;
        $topicInfo = $this->Movietopic->getTopicMovieInfo($id,$status);
        if (empty($topicInfo)) {
            $this->jump_to("/series");
            exit;
        }
        $this->set_attr("topicInfo",$topicInfo);
        //系列电影信息
        $topicIdArr = array($id);
        $topicMovieList = $this->Movietopicmovie->getTopicMovieListByTopicId($topicIdArr);
        $topicMovieList = $this->initArrById($topicMovieList,"id",$tMovieIdArr);
        //系列电影图片
        $tMovieImg = $this->Movietopicimg->getTopicMovieImgByRelatedId($tMovieIdArr,2);
        $topicMovieList = $this->_initImgToMovie($topicMovieList,$tMovieImg);
        $this->set_attr("topicMovieList",$topicMovieList);

        $this->set_attr("tabIndex",4);
        $this->load->set_top_index(-1);
        $this->load->set_title($topicInfo['name'] . "系列 - " . $this->base_title . " - " . APF::get_instance()->get_config_value("base_name"));
        $this->load->set_css(array("/css/dianying/seriesdetail.css"));
        $this->load->set_js(array("/js/dianying/seriesdetail.js"));
        $this->set_attr("moviePlace",$this->_moviePlace);
        $this->set_attr("movieType",$this->_movieType);
        $this->set_view('dianying/seriesdetail','base3');
    }

    /**
     * 拼接系列电影图片
     * @param $topicMovieList
     * @param $tMovieImg
     * @return mixed
     */
    private function _initImgToMovie($topicMovieList,$tMovieImg) {
        if (empty($topicMovieList) || empty($tMovieImg)) {
            return $topicMovieList;
        }
        foreach($tMovieImg as $mImgVal) {
            $topicMovieList[$mImgVal['relatedId']]['img'][] = $mImgVal;
        }
        return $topicMovieList;
    }
}