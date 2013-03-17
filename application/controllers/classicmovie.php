<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站重温经典列表页
 * added by xiongjiewu at 2013-3-4
 */
class Classicmovie extends CI_Controller {

    private $_maxCount = 500;//最大允许显示电影个数
    private $_maxPage = 50;//最大允许页码

    public function index()
    {
        return $this->type();
    }

    public function type($type = 1,$page = 1)
    {
        $type = intval($type);
        $page = intval($page);
        $page = ($page > $this->_maxPage) ? $this->_maxPage : $page;
        $this->load->model('Backgroundadmin');
        $limit = 10;
        $movieList = $this->Backgroundadmin->getDetailInfoByType($type,($page - 1) * $limit,$limit);
        $mouvieCount = $this->Backgroundadmin->getDetailInfoCountByType($type,($page - 1) * $limit,$limit);
        $this->load->set_head_img(false);
        $this->set_attr("movieList",$movieList);
        $this->load->set_title("电影吧，国内最强阵容");
        $this->load->set_css(array("/css/dianying/detail.css","/css/dianying/classicmovie.css"));
        $this->load->set_js(array("/js/dianying/classicmovie.js"));
        $this->load->set_top_index(3);
        $this->set_attr("moviePlace",$this->_moviePlace);
        $this->set_attr("movieType",$this->_movieType);
        $this->set_view('dianying/classicmovie');
    }
}