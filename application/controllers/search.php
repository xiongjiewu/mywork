<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站后台页面
 * added by xiongjiewu at 2013-3-4
 */
define("APP_ROOT",dirname(__FILE__) . "/../split");
class Search extends CI_Controller {
    private $_maxLen = 25;
    public function __construct() {
        parent::__construct();
        $this->load->model('Backgroundadmin');
    }

    /** 过滤特殊字符
     * @param $searchW
     * @return mixed
     */
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
        $searchW = htmlspecialchars($searchW);
        if (empty($searchW)) {
            $this->jump_to("/moviceguide/");
            exit;
        }
        $searchW = $this->_pregReplacespeaStr($searchW);
        $this->set_attr("searchW",$searchW);
        //长度截取
        if (mb_strlen($searchW,"utf8") > $this->_maxLen) {
            $searchW = mb_substr($searchW,0,$this->_maxLen);
        }
        //分词数组
        $wordArr = array();
        $this->load->model("Wordsplit");
        $wordArr[] = array_merge(array($searchW),$this->Wordsplit->get_tags_arr($searchW));

        //开始匹配搜索关键字的电影
        $searchMovieInfo = array();
        foreach($wordArr[0] as $wordVal) {
            $searchInfo = $this->_getDetailInfoBySearchW($wordVal);
            if (!empty($searchInfo)) {
                foreach($searchInfo as $sKey => $sInfo) {
                    //替换名称中的搜索关键字
                    $searchInfo[$sKey]['name'] = str_replace($wordVal,"<em>{$wordVal}</em>",$sInfo['name']);
                }
                $searchMovieInfo = array_merge($searchMovieInfo,$searchInfo);
            }
        }

        //去掉重复电影
        $searchMovieInfo = $this->initArr($searchMovieInfo);
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
        
        $this->load->set_title("搜'{$searchW}'相关的影片 - " . $this->base_title . " - " . APF::get_instance()->get_config_value("base_name"));
        $this->load->set_css(array("/css/dianying/search.css"));
        $this->load->set_js(array("/js/dianying/search.js"));
        $this->load->set_top_index(-1);
        $this->set_attr("moviePlace",$this->_moviePlace);
        $this->set_attr("movieType",$this->_movieType);
        $this->set_attr("movieSortType",APF::get_instance()->get_config_value("movie_type"));
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
        $searchMovieInfo = $this->_getDetailInfoBySearchW($word,20);
        $searchMovieInfo = $this->_getMoviceNameInfos($searchMovieInfo);
        if (!empty($searchMovieInfo)) {
            $result["code"] = "success";
            $result["info"] = array();
            $result["info"] = $searchMovieInfo;
        }
        echo json_encode($result);
        exit;
    }

    /** 拼接电影名字数组，去掉重复名字
     * @param $searchMovieInfo
     */
    private function _getMoviceNameInfos($searchMovieInfo) {
        if (empty($searchMovieInfo)) {
            return false;
        }
        $resultArr = array();
        foreach($searchMovieInfo as $sVal) {
            $resultArr[] = $sVal['name'];
        }
        $resultArr = array_unique($resultArr);
        $newResArr = array();
        foreach($resultArr as $resVal) {
            $newResArr[]['name'] = $resVal;
        }
        return $newResArr;
    }

    private function initArr($arr,$filed = "id") {
        if (empty($arr)) {
            return $arr;
        }
        $result = array();
        foreach($arr as $aV) {
            if (empty($result[$aV[$filed]])) {
                $result[$aV[$filed]] = $aV;
            }
        }
        return $result;
    }
}
