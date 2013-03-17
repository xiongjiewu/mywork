<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站电影列表页
 * added by xiongjiewu at 2013-3-4
 */
class Latestmovie extends CI_Controller {

    public function index()
    {
        $sortStr = $this->_movieSortType[5]['sort'];
        $sortS = "and time1 <=" . time();
        $sortStr = $sortS . "  " . $sortStr;
        $this->load->model('Backgroundadmin');
        $limit = 50;
        $movieList = $this->Backgroundadmin->getDetailInfoList(0,$limit,0,$sortStr) ;
        if (empty($movieList)) {//当查询电影信息不存在
            $this->jump_to("/error/");
        }
        $ids = array();
        foreach($movieList as $movieListKey => $movieListVal) {
            $ids[] = $movieListVal['id'];
            $movieList[$movieListKey]['jieshao'] = $this->splitStr($movieListVal['jieshao'],50);
        }
        $watchLinkInfo = $this->Backgroundadmin->getWatchLinkInfoByInfoId($ids);
        $watchLinkInfo = $this->initArrById($watchLinkInfo);
        $this->set_attr("watchLinkInfo",$watchLinkInfo);
        $downLoadLinkInfo = $this->Backgroundadmin->getDownLoadLinkInfoByInfoId($ids);
        $downLoadLinkInfo = $this->initArrById($downLoadLinkInfo);
        $this->set_attr("downLoadLinkInfo",$downLoadLinkInfo);
        $this->load->set_head_img(false);
        $this->set_attr("movieList",$movieList);
        $this->load->set_title("电影吧，国内最强阵容");
        $this->load->set_css(array("css/dianying/detail.css","css/dianying/latestmovie.css"));
        $this->load->set_js(array("js/dianying/latestmovie.js"));
        $this->load->set_top_index(1);
        $this->set_attr("moviePlace",$this->_moviePlace);
        $this->set_attr("movieType",$this->_movieType);
        $this->set_view('dianying/latestmovie');
    }
}