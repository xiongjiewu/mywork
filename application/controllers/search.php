<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站后台页面
 * added by xiongjiewu at 2013-3-4
 */
class Search extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Backgroundadmin');
    }
    private  function _pregReplacespeaStr($searchW) {
        $searchW = preg_replace('/[^\w\d\x80-\xff]+/','',rawurldecode($searchW));//过滤特殊字符
        return $searchW;
    }

    private function _getDetailInfoBySearchW($searchW,$limit  = 10) {
        $searchW = mysql_real_escape_string($searchW);
        $searchMovieInfo = $this->Backgroundadmin->getDetailInfoBySearchW(trim($searchW),$limit);
        return $searchMovieInfo;
    }
    public function index() {
        $searchW = $this->input->get("key");
        $searchW = $this->_pregReplacespeaStr($searchW);;//过滤特殊字符
        if (empty($searchW)) {
            $this->jump_to("/classicmovie/");
            exit;
        }
        $this->set_attr("searchW",$searchW);
        $searchMovieInfo = $this->_getDetailInfoBySearchW($searchW);
        foreach($searchMovieInfo as $infoKey => $infoVal) {
            if ($infoKey < 4) {
                $searchMovieInfo[$infoKey]['class'] = "firstRow";
            } else {
                $searchMovieInfo[$infoKey]['class'] = "";
            }
            $searchMovieInfo[$infoKey]['daoyan'] = $this->splitStr($infoVal['daoyan'],9);
        }
        $this->set_attr("searchMovieInfo",$searchMovieInfo);
        $this->load->set_head_img(false);
        $this->load->set_move_js(false);
        $this->load->set_title("搜'{$searchW}'相关的影片 - " . get_config_value("base_title") . " - " . get_config_value("base_name"));
        $this->load->set_css(array("/css/dianying/search.css"));
        $this->load->set_js(array("/js/dianying/search.js"));
        $this->load->set_top_index(-1);
        $this->set_attr("moviePlace",$this->_moviePlace);
        $this->set_attr("movieType",$this->_movieType);
        $this->set_attr("movieSortType",get_config_value("movie_type"));
        $this->set_view('dianying/search');
    }

    public function ajaxgetdyinfo(){
        $result = array(
            "code" => "error",
            "info" => "",
        );
        $word = $this->input->post("word");
        $word = $this->_pregReplacespeaStr($word);
        if (!isset($word)) {
            echo json_encode($result);
            exit;
        }
        $searchMovieInfo = $this->_getDetailInfoBySearchW($word);
        if (!empty($searchMovieInfo)) {
            $result["code"] = "success";
            $result["info"] = array();
            $result["info"] = $searchMovieInfo;
        }
        echo json_encode($result);
        exit;
    }
}