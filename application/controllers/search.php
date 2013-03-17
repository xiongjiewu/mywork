<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站后台页面
 * added by xiongjiewu at 2013-3-4
 */
class Search extends CI_Controller {

    public function index($searchW = null)
    {
        $searchW = preg_replace('/[^\w\d\x80-\xff]+/','',rawurldecode($searchW));//过滤特殊字符
        if (empty($searchW)) {
            $this->jump_to("/classicmovie/");
            exit;
        }
        $this->set_attr("searchW",$searchW);
        $this->load->model('Backgroundadmin');
        $searchMovieInfo = $this->Backgroundadmin->getDetailInfoBySearchW(trim($searchW));
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
        $this->load->set_title("电影吧，国内最强阵容");
        $this->load->set_css(array("/css/dianying/detail.css","/css/dianying/classicmovie.css","/css/dianying/search.css"));
        $this->load->set_js(array("/js/dianying/search.js"));
        $this->load->set_top_index(-1);
        $this->set_attr("moviePlace",$this->_moviePlace);
        $this->set_attr("movieType",$this->_movieType);
        $this->set_attr("movieSortType",get_config_value("movie_type"));
        $this->set_view('dianying/search');
    }
}