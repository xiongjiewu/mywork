<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站电影导航页
 * added by xiongjiewu at 2013-3-4
 */
class Moviceguide extends CI_Controller {

    private $_maxCount = 5000;//最大允许显示电影个数
    private $_maxPage = 125;//最大允许页码
    private $_limit = 40;

    public function index() {
        $this->type();
    }

    /** 拼接页面类型、地区、年份链接
     * @param $movieSortType
     * @param $type
     * @param $page
     * @param $field1
     * @param $field2
     * @return array
     */
    private function _initMoviceSortInfo($movieSortType,$param1,$param2,$param3,$type,$page,$field1,$field2) {
        $resultArr = array();
        foreach($movieSortType as $key => $val) {
            $resultArr[$key]['type'] = $val['type'];
            foreach($val['info'] as $infoKey => $infoVal) {
                if (strpos($val['base_url'],$param1) !== false && $infoKey == $type) {
                    $active = true;
                } elseif (strpos($val['base_url'],$param2) !== false && $infoKey == $field1) {
                    $active = true;
                } elseif (strpos($val['base_url'],$param3) !== false && $infoKey == $field2) {
                    $active = true;
                } else {
                    $active = false;
                }
                switch(strtolower($param1)) {
                    case "type" :
                        if (strpos($val['base_url'],$param1) !== false) {
                            $resultArr[$key]['base_url'] = $val['base_url']  . "all/" .  $field1 . "/" . $field2 . "/" . $page;
                            $url = $val['base_url'] . $infoKey . "/" .  $field1 . "/" . $field2  . "/" . $page;
                        } elseif (strpos($val['base_url'],$param2) !== false) {
                            $resultArr[$key]['base_url'] = $val['base_url']  . "all/" .  $type . "/" . $field2  . "/" . $page;
                            $url = $val['base_url'] . $infoKey . "/" .  $type . "/" . $field2  . "/" . $page;
                        } elseif (strpos($val['base_url'],$param3) !== false) {
                            $resultArr[$key]['base_url'] = $val['base_url']  . "all/".  $type . "/" . $field1  . "/" . $page;
                            $url = $val['base_url'] . $infoKey . "/" .  $type . "/" . $field1 . "/" . $page;
                        } else {
                            $url = "";
                        }
                        break;
                    case "year" :
                        if (strpos($val['base_url'],$param1) !== false) {
                            $resultArr[$key]['base_url'] = $val['base_url']  . "all/" .  $field1 . "/" . $field2  . "/" . $page;
                            $url = $val['base_url'] . $infoKey . "/" . $field1 . "/" . $field2  . "/" . $page;
                        } elseif (strpos($val['base_url'],$param2) !== false) {
                            $resultArr[$key]['base_url'] = $val['base_url']  . "all/" . "/" .  $type . "/" . $field2  . "/" . $page;
                            $url = $val['base_url'] . $infoKey . "/" . "/" .  $type . "/" . $field2  . "/" . $page;
                        } elseif (strpos($val['base_url'],$param3) !== false) {
                            $resultArr[$key]['base_url'] = $val['base_url']  . "all/" . $field1 . "/" . $type  . "/" . $page;
                            $url = $val['base_url'] . $infoKey . "/" .  $field1 . "/" . $type  . "/" . $page;
                        } else {
                            $url = "";
                        }
                        break;
                    case "place" :
                    default:
                        if (strpos($val['base_url'],$param1) !== false) {
                            $resultArr[$key]['base_url'] = $val['base_url']  . "all/" .  $field1 . "/" . $field2  . "/" . $page;
                            $url = $val['base_url'] . $infoKey . "/" .  $field1 . "/" . $field2  . "/" . $page;
                        } elseif (strpos($val['base_url'],$param2) !== false) {
                            $resultArr[$key]['base_url'] = $val['base_url']  . "all/" .  $field2 . "/" . $type  . "/" . $page;
                            $url = $val['base_url'] . $infoKey . "/" .  $field2 . "/" . $type;
                        } elseif (strpos($val['base_url'],$param3) !== false) {
                            $resultArr[$key]['base_url'] = $val['base_url']  . "all/".  $field1 . "/" . $type  . "/" . $page;
                            $url = $val['base_url'] . $infoKey . "/" . $field1 . "/" . $type  . "/" . $page;
                        } else {
                            $url = "";
                        }
                        break;
                }
                $resultArr[$key]['info'][$infoKey] = array("name" => $infoVal,"url" => $url,"active" => $active);
            }
        }
        return $resultArr;
    }

    public function type($type = null,$year = "all",$diqu = "all",$page = 1)
    {
        $condition = array();
        if (empty($type) || $type == "all" || empty($this->_movieType[$type])) {
            $type = "all";
            $typeS = null;
        } else {
            $typeS = intval($type);
            $condition[] = "type = " . $typeS;
        }

        $page = intval($page);
        $page = ($page > $this->_maxPage) ? $this->_maxPage : $page;
        $this->set_attr("bigtype",0);
        $this->set_attr("type",$type);

        $year = intval($year);
        $diqu = intval($diqu);
        if (empty($year)) {
            $year = "all";
        } else {
            $condition[] = "nianfen = " . $year;
        }

        if (empty($diqu)) {
            $diqu = "all";
        } else {
            $condition[] = "diqu = " . $diqu;
        }

        $param[1] = $year;
        $param[2] = $diqu;
        $this->set_attr("param",$param);

        //拼接条件字符串
        $conditionStr = implode(" and ",$condition);

        $this->load->model('Backgroundadmin');
        $limit = $this->_limit;
        $this->set_attr("limit",$limit);
        $mouvieCount = $this->Backgroundadmin->getDetailInfoCountByCondition($conditionStr);
        $mouvieCount = ($mouvieCount > $this->_maxCount) ? $this->_maxCount : $mouvieCount;
        if ($mouvieCount > 0 && $page > ceil($mouvieCount / $limit)) {
            $page = ceil($mouvieCount / $limit);
        }
        $movieList = $this->Backgroundadmin->getDetailInfoByCondition($conditionStr,($page - 1) * $limit,$limit);
        foreach($movieList as $infoKey => $infoVal) {
            if ($infoKey < 4) {
                $movieList[$infoKey]['class'] = "firstRow";
            } else {
                $movieList[$infoKey]['class'] = "";
            }
            $movieList[$infoKey]['daoyan'] = $this->splitStr($infoVal['daoyan'],9);
        }
        $this->set_attr("movieList",$movieList);
        $this->set_attr("mouvieCount",$mouvieCount);
        $base_url = get_url("/moviceguide/type/{$type}/{$year}/{$diqu}/");
        $fenye = $this->set_page_info($page,$limit,$mouvieCount,$base_url);
        $this->set_attr("fenye",$fenye);
        $this->load->set_head_img(false);
        
        $this->load->set_title("电影导航 - " . $this->base_title . " - " . APF::get_instance()->get_config_value("base_name"));
        $this->load->set_css(array("/css/dianying/moviceguide.css"));
        $this->load->set_js(array("/js/dianying/moviceguide.js"));
        $this->load->set_top_index(4);
        $this->set_attr("moviePlace",$this->_moviePlace);
        $this->set_attr("movieType",$this->_movieType);

        //分类信息拼接
        $typeSort = APF::get_instance()->get_config_value("movie_type");
        $movieSortType = $this->_initMoviceSortInfo($typeSort,"type","year","place",$type,1,$year,$diqu);
        $this->set_attr("movieSortType",$movieSortType);
        $this->set_view('dianying/moviceguide');
    }

    public function year($type = null,$ty = "all",$diqu = "all",$page = 1) {
        $condition = array();
        if (empty($type) || ($type == "all")) {
            $type = "all";
            $typeS = null;
        } else {
            $type = $typeS = intval($type);
            $condition[] = "nianfen = " . $type;
        }
        $page = intval($page);
        $page = ($page > $this->_maxPage) ? $this->_maxPage : $page;
        $this->set_attr("bigtype",1);
        $this->set_attr("type",$type);

        $ty = intval($ty);
        $diqu = intval($diqu);
        if (empty($ty)) {
            $ty = "all";
        } else {
            $condition[] = "type = " . $ty;
        }

        if (empty($diqu)) {
            $diqu = "all";
        } else {
            $condition[] = "diqu = " . $diqu;
        }

        $param[0] = $ty;
        $param[2] = $diqu;
        $this->set_attr("param",$param);

        //拼接条件字符串
        $conditionStr = implode(" and ",$condition);

        $this->load->model('Backgroundadmin');
        $limit = $this->_limit;
        $this->set_attr("limit",$limit);
        $mouvieCount = $this->Backgroundadmin->getDetailInfoCountByCondition($conditionStr);
        $mouvieCount = ($mouvieCount > $this->_maxCount) ? $this->_maxCount : $mouvieCount;
        if ($mouvieCount > 0 && $page > ceil($mouvieCount / $limit)) {
            $page = ceil($mouvieCount / $limit);
        }
        $movieList = $this->Backgroundadmin->getDetailInfoByCondition($conditionStr,($page - 1) * $limit,$limit);
        foreach($movieList as $infoKey => $infoVal) {
            if ($infoKey < 4) {
                $movieList[$infoKey]['class'] = "firstRow";
            } else {
                $movieList[$infoKey]['class'] = "";
            }
            $movieList[$infoKey]['daoyan'] = $this->splitStr($infoVal['daoyan'],9);
        }
        $this->set_attr("movieList",$movieList);
        $this->set_attr("mouvieCount",$mouvieCount);
        $base_url = get_url("/moviceguide/year/{$type}/{$ty}/{$diqu}/");
        $fenye = $this->set_page_info($page,$limit,$mouvieCount,$base_url);
        $this->set_attr("fenye",$fenye);
        $this->load->set_head_img(false);
        
        $this->load->set_title("电影导航 - " . $this->base_title . " - " . APF::get_instance()->get_config_value("base_name"));
        $this->load->set_css(array("/css/dianying/moviceguide.css"));
        $this->load->set_js(array("/js/dianying/moviceguide.js"));
        $this->load->set_top_index(4);
        $this->set_attr("moviePlace",$this->_moviePlace);
        $this->set_attr("movieType",$this->_movieType);

        //分类信息拼接
        $typeSort = APF::get_instance()->get_config_value("movie_type");
        $movieSortType = $this->_initMoviceSortInfo($typeSort,"year","type","place",$type,1,$ty,$diqu);

        $this->set_attr("movieSortType",$movieSortType);

        $this->set_view('dianying/moviceguide');
    }

    public function place($type = null,$ty = "all",$year = "all",$page = 1) {
        $condition = array();
        if (empty($type) || ($type == "all") || empty($this->_moviePlace[$type])) {
            $type = "all";
            $typeS = null;
        } else {
            $type = $typeS = intval($type);
            $condition[] = "diqu = " . $typeS;
        }
        $page = intval($page);
        $page = ($page > $this->_maxPage) ? $this->_maxPage : $page;
        $this->set_attr("bigtype",2);
        $this->set_attr("type",$type);

        $ty = intval($ty);
        $year = intval($year);
        if (empty($ty)) {
            $ty = "all";
        } else {
            $condition[] = "type = " . $ty;
        }

        if (empty($year)) {
            $year = "all";
        } else {
            $condition[] = "nianfen = " . $year;
        }

        $param[0] = $ty;
        $param[1] = $year;
        $this->set_attr("param",$param);

        //拼接条件字符串
        $conditionStr = implode(" and ",$condition);

        $this->load->model('Backgroundadmin');
        $limit = $this->_limit;
        $this->set_attr("limit",$limit);
        $mouvieCount = $this->Backgroundadmin->getDetailInfoCountByCondition($conditionStr);
        $mouvieCount = ($mouvieCount > $this->_maxCount) ? $this->_maxCount : $mouvieCount;
        if ($mouvieCount > 0 && $page > ceil($mouvieCount / $limit)) {
            $page = ceil($mouvieCount / $limit);
        }
        $movieList = $this->Backgroundadmin->getDetailInfoByCondition($conditionStr,($page - 1) * $limit,$limit);
        foreach($movieList as $infoKey => $infoVal) {
            if ($infoKey < 4) {
                $movieList[$infoKey]['class'] = "firstRow";
            } else {
                $movieList[$infoKey]['class'] = "";
            }
            $movieList[$infoKey]['daoyan'] = $this->splitStr($infoVal['daoyan'],9);
        }
        $this->set_attr("movieList",$movieList);
        $this->set_attr("mouvieCount",$mouvieCount);
        $base_url = get_url("/moviceguide/place/{$type}/{$ty}/{$year}/");
        $fenye = $this->set_page_info($page,$limit,$mouvieCount,$base_url);
        $this->set_attr("fenye",$fenye);
        $this->load->set_head_img(false);
        
        $this->load->set_title("电影导航 - " . $this->base_title . " - " . APF::get_instance()->get_config_value("base_name"));
        $this->load->set_css(array("/css/dianying/moviceguide.css"));
        $this->load->set_js(array("/js/dianying/moviceguide.js"));
        $this->load->set_top_index(4);
        $this->set_attr("moviePlace",$this->_moviePlace);
        $this->set_attr("movieType",$this->_movieType);

        //分类信息拼接
        $typeSort = APF::get_instance()->get_config_value("movie_type");
        $movieSortType = $this->_initMoviceSortInfo($typeSort,"place","type","year",$type,1,$ty,$year);
        $this->set_attr("movieSortType",$movieSortType);

        $this->set_view('dianying/moviceguide');
    }
}
