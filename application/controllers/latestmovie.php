<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站电影列表页
 * added by xiongjiewu at 2013-3-4
 */
class Latestmovie extends CI_Controller {

    public function index()
    {
        $this->load->model('Backgroundadmin');
        $sortStr = $this->_movieSortType[5]['sort'];
        $time = strtotime(date("Y-m-01",time()));
        $movieList = array();
        $monthArr = array();
        for($i = 1;$i <= get_config_value("last_movie_month");$i++) {
            $sTime = strtotime(date("Y-m-01 00:00:00",$time));//当前月开始时间
            $nMonth = strtotime("+1 month",$time);//下个月
            $nMonthFirstDayTime = strtotime(date("Y-m-01",$nMonth));//下个月第一天
            $eTime = strtotime(date("Y-m-d 23:59:59",$nMonthFirstDayTime - 86400));//当前月最后时间
            $infoList  = $this->Backgroundadmin->getDetailInfoListByTime($sTime,$eTime,0,$sortStr);
            if (!empty($infoList)) {
                $movieList[date("y年m月",$time)] = $infoList;
                $monthArr[date("y年m月",$time)] = date("Ym",$time);
            }
            $time = strtotime("-1 month",$time);
        }
        if (empty($movieList)) {//当查询电影信息不存在
            $this->jump_to("/error/");
        }
        $ids = array();
        foreach($movieList as $movieListKey => $movieListVal) {
            if (!empty($movieListVal)) {
                foreach($movieListVal as $mKey => $mVal) {
                    $ids[] = $mVal['id'];
                    $movieListVal[$mKey]['jieshao'] = $this->splitStr($mVal['jieshao'],50);
                }
                $movieList[$movieListKey] = $movieListVal;
            }
        }
        $this->set_attr("movieList",$movieList);
        $this->set_attr("monthArr",$monthArr);
        $watchLinkInfo = $this->Backgroundadmin->getWatchLinkInfoByInfoId($ids);
        $watchLinkInfo = $this->_initArr($watchLinkInfo);
        $this->set_attr("watchLinkInfo",$watchLinkInfo);
        $downLoadLinkInfo = $this->Backgroundadmin->getDownLoadLinkInfoByInfoId($ids);
        $downLoadLinkInfo = $this->_initArr($downLoadLinkInfo);
        $this->set_attr("downLoadLinkInfo",$downLoadLinkInfo);
        $this->load->set_head_img(false);
        $this->load->set_move_js(false);
        $this->load->set_title("电影吧，国内最强阵容");
        $this->load->set_css(array("css/dianying/latestmovie.css"));
        $this->load->set_js(array("js/dianying/latestmovie.js"));
        $this->load->set_top_index(1);
        $this->set_view('dianying/latestmovie');
    }

    private function _initArr($nfo)
    {
        if (empty($nfo)) {
            return $nfo;
        }
        $result = array();
        foreach($nfo as $infoKey => $infoVal) {
            $result[$infoVal['infoId']][] = $infoVal;
        }
        return $result;
    }
}