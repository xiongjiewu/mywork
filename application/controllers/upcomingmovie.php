<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站即将上映列表页
 * added by xiongjiewu at 2013-3-4
 */
class Upcomingmovie extends CI_Controller {

    public function index()
    {
        $sortStr = $this->_movieSortType[7]['sort'];
        $sortS = "and time1 >" . time();
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
            $movieList[$movieListKey]['jieshao'] = $this->splitStr($movieListVal['jieshao'],60);//介绍截取50个字符
        }
        if (!empty($this->userId)) {
            $this->set_attr("userId",$this->userId);
            $this->load->model('Notice');
            $userNoticeInfos = $this->Notice->getNoticeInfoByInfoIds($this->userId,$ids);
            $userNoticeInfos = $this->initArrById($userNoticeInfos);
            $this->set_attr("userNoticeInfos",$userNoticeInfos);
        }
        $this->load->set_head_img(false);
        
        $this->set_attr("movieList",$movieList);
        $this->load->set_title("即将上映列表 - " . $this->base_title . " - " . APF::get_instance()->get_config_value("base_name"));
        $this->load->set_css(array("/css/dianying/upcomingmovie.css"));
        $this->load->set_js(array("/js/dianying/upcomingmovie.js"));
        $this->load->set_top_index(2);
        $this->set_attr("moviePlace",$this->_moviePlace);
        $this->set_attr("movieType",$this->_movieType);
        $this->set_view('dianying/upcomingmovie');
    }
}