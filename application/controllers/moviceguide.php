<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站电影导航页
 * added by xiongjiewu at 2013-3-4
 */
class Moviceguide extends MY_Controller {

    private $_maxCount = 1200;//最大允许显示电影个数
    private $_limit = 30;
    private $_oneLineCount = 14;//按类型、按地区、按年份一行显示分类个数

    private $_daoyanInfo;//导演
    private $_yanyuanInfo;//演员
    private $_renwuLimit = 6;

    function __construct() {
        parent::__construct();
        $this->set_attr("oneLineCount",$this->_oneLineCount);
        $this->set_attr("userId",$this->userId);
        $this->load->model("Shoucang");
        $this->load->model('Backgroundadmin');
        $this->load->model('Boxofficeinfo');
        $this->load->set_top_index(0);
        //导演+演员，大乱，取有限个
        $this->_daoyanInfo = APF::get_instance()->get_config_value("dianyingku_daoyan");
        $this->_daoyanInfo = array_merge(array("全部"),$this->_daoyanInfo);

        $this->_yanyuanInfo = APF::get_instance()->get_config_value("dianyingku_yanyuan");
        $this->_yanyuanInfo = array_merge(array("全部"),$this->_yanyuanInfo);
    }

    public function index() {
        $this->_get();
    }

    /**
     * 处理主函数
     */
    private function _get() {
        $params = $this->input->get();
        //页码
        $params['p'] = empty($params['p']) ? 1 : $params['p'];
        $p = intval($params['p']) ? intval($params['p']) : 1;

        //设置猜你喜欢、最新更新等tab信息
        $movieTabInfo = APF::get_instance()->get_config_value("movie_tab_info");
        $movieTabInfo = $this->_setMovieTabInfo($params,$movieTabInfo);
        $this->set_attr("movieTabInfo",$movieTabInfo);
        $this->set_attr("sort",empty($params['sort']) ? "like" : $params['sort']);

        //拼接查询条件
        $conStr = $this->_initConditionStr($params,$movieTabInfo);
        //电影总数
        $dyCount = $this->Backgroundadmin->getDetailInfoCountByCondition($conStr);
        $this->set_attr("dyCount",$dyCount);
        $dyCount = ($dyCount > $this->_maxCount) ? $this->_maxCount : $dyCount;
        //超出最大页则赋值为最大页
        $p = ($p > ceil($dyCount / $this->_limit)) ? ceil($dyCount / $this->_limit) : $p;
        if (!empty($dyCount)) {
            //电影信息
            $movieInfos = $this->Backgroundadmin->getDetailInfoByCondition($conStr,($p - 1) * $this->_limit,$this->_limit);
            $this->set_attr("movieInfos",$movieInfos);
        }

        //分页
        unset($params['p']);
        $params['p'] = '';
        $base_url = APF::get_instance()->get_real_url('/moviceguide','',$params);
        $fenye = $this->set_page_info($p,$this->_limit,$dyCount,$base_url);
        $this->set_attr("limit",$this->_limit);
        $this->set_attr("fenye",$fenye);

        //分类信息拼接
        $typeSort = APF::get_instance()->get_config_value("movie_type");
        $movieSortType = $this->_initMoviceSortInfo($typeSort,$params);
        $this->set_attr("movieSortType",$movieSortType);

        //导演+演员信息拼接
        list($daoyanInfo,$yanyuanInfo) = $this->_initDaoYanAndYanYuanInfo($params);
        $this->set_attr("daoyanInfo",$daoyanInfo);
        $this->set_attr("yanyuanInfo",$yanyuanInfo);

        //本周票房排行榜
        $weekPiaoFangConditionStr = "type = 1 and diqu = 1 and del = 0 order by updateTime desc limit 10";
        $weekPiaofangInfo = $this->Boxofficeinfo->getBoxofficeInfoByCondition($weekPiaoFangConditionStr);
        $weekPiaofangInfo = $this->initArrById($weekPiaofangInfo,"infoId",$weekPiaofangIds);
        //本周票房电影详细信息
        $weekPiaofangMovieInfo = $this->Backgroundadmin->getDetailInfo($weekPiaofangIds,0,true);
        $weekPiaofangArr = array();
        foreach($weekPiaofangMovieInfo as $weekPiaofangKey => $weekPiaofangVal) {
            $weekPiaofangArr[] = $weekPiaofangInfo[$weekPiaofangVal['id']]['piaofang'];
            $weekPiaofangMovieInfo[$weekPiaofangKey]['piaofang'] = $weekPiaofangInfo[$weekPiaofangVal['id']]['piaofang'];
        }
        //根据票房排序
        array_multisort($weekPiaofangArr,SORT_DESC,$weekPiaofangMovieInfo);
        $this->set_attr("weekPiaofangMovieInfo",$weekPiaofangMovieInfo);

        //历史票房榜信息
        $piaofangConditionStr = "type = 0 and diqu = 0 and del = 0 order by piaofang desc limit 100";
        $piaofangInfo = $this->Boxofficeinfo->getBoxofficeInfoByCondition($piaofangConditionStr);
        $piaofangInfo = $this->initArrById($piaofangInfo,"infoId",$piaofangIds);
        //票房电影详细信息
        $piaofangMovieInfo = $this->Backgroundadmin->getDetailInfo($piaofangIds,0,true);
        $piaofangArr = array();
        foreach($piaofangMovieInfo as $piaofangKey => $piaofangVal) {
            $piaofangArr[] = $piaofangInfo[$piaofangVal['id']]['piaofang'];
            $piaofangMovieInfo[$piaofangKey]['piaofang'] = $piaofangInfo[$piaofangVal['id']]['piaofang'];
        }
        //根据票房排序
        array_multisort($piaofangArr,SORT_DESC,$piaofangMovieInfo);
        $piaofangMovieInfo = array_slice($piaofangMovieInfo,0,10);
        $this->set_attr("piaofangMovieInfo",$piaofangMovieInfo);

        $this->load->set_title("电影库 - " . $this->base_title . " - " . APF::get_instance()->get_config_value("base_name"));
        $this->load->set_css(array("/css/dianying/newmoviceguide.css"));
        $this->load->set_js(array("/js/dianying/newmoviceguide.js"));
        $this->set_attr("moviePlace",$this->_moviePlace);
        $this->set_attr("movieType",$this->_movieType);
        $this->set_view('dianying/newmoviceguide','base3');
    }

    /**
     * url字段对应数据库字段
     * @var array
     */
    private $_sortKey = array(
        "type" => "type",
        "year" => "nianfen",
        "place" => "diqu",
        "d" => "daoyan",
        "y" => "zhuyan",
    );

    /**
     * 根据URL参数拼接数据条件字符串
     * @param $params
     * @return string
     */
    private function _initConditionStr($params,$movieTabInfo) {
        unset($params['p']);
        $params['del'] = 0;
        if (!empty($params)) {
            $strArr = array();
            foreach($params as $pK => $pV) {
                if ($pK == "sort") {
                    continue;
                }
                $key = empty($this->_sortKey[$pK]) ? $pK : $this->_sortKey[$pK];
                //导演和主演用like语句
                if ($key == "daoyan" || $key == "zhuyan") {
                    $pV = htmlspecialchars($pV);
                    $pV = mysql_real_escape_string($pV);
                    if ($pV != "@") {//不等于全部
                        $strArr[] = $key . " like'%{$pV}%'";
                    }
                } else {
                    $pV = is_numeric($pV) ? $pV : 1;
                    $strArr[] = $key . " = " . $pV;
                }
            }
            $conditionStr = implode(" and ",$strArr);
            $params['sort'] = (!empty($params['sort']) && !empty($movieTabInfo[$params['sort']])) ? $params['sort'] : 'like';//默认显示最新更新
            //排序字段
            if (!empty($movieTabInfo[$params['sort']]['desc'])) {
                $conditionStr .= " {$movieTabInfo[$params['sort']]['desc']}";
            }
            return $conditionStr;
        }
        return '';
    }

    /**
     * 拼接页面类型、地区、年份链接
     * @param $movieSortType
     * @param $type
     * @param $page
     * @param $field1
     * @param $field2
     * @return array
     */
    private function _initMoviceSortInfo($movieSortType,$params,$sub = "moviceguide") {
        $resultArr = array();
        unset($params['p']);
        $params1 = $params2 = $params3 = $params;
        unset($params1['type']);
        unset($params2['year']);
        unset($params3['place']);

        foreach($movieSortType as $key => $val) {
            $resultArr[$key]['type'] = $val['type'];
            //各个类型中‘全部’url拼接
            if ($val['type'] == "类型") {
                $oneLineCount = $this->_oneLineCount;
                $resultArr[$key]['base_url'] = APF::get_instance()->get_real_url($sub,'',$params1);
                $resultArr[$key]['active'] = empty($params['type']) ? true : false;
            } elseif ($val['type'] == "年份") {
                $oneLineCount = $this->_oneLineCount;
                $resultArr[$key]['base_url'] = APF::get_instance()->get_real_url($sub,'',$params2);
                $resultArr[$key]['active'] = empty($params['year']) ? true : false;
            } elseif ($val['type'] == "地区") {
                $oneLineCount = $this->_oneLineCount - 1;//地区由于有比较长的地区名，所以一行显示个数-1，以免把样式往下撑
                $resultArr[$key]['base_url'] = APF::get_instance()->get_real_url($sub,'',$params3);
                $resultArr[$key]['active'] = empty($params['place']) ? true : false;
            } else {
                $oneLineCount = $this->_oneLineCount;
                $resultArr[$key]['base_url'] = APF::get_instance()->get_real_url($sub);
            }

            $infoI = $infoJ = 1;
            foreach($val['info'] as $infoKey => $infoVal) {
                $active = false;
                if ($val['type'] == "类型") {
                    $params1['type'] = $infoKey;
                    if (!empty($params['type']) && $params['type'] == $infoKey) {
                        $active = true;
                        $infoI = $infoJ;
                    }
                    $url = APF::get_instance()->get_real_url($sub,'',$params1);
                } elseif ($val['type'] == "年份") {
                    $params2['year'] = $infoVal;
                    if (!empty($params['year']) && $params['year'] == $infoVal) {
                        $active = true;
                        $infoI = $infoJ;
                    }
                    $url = APF::get_instance()->get_real_url($sub,'',$params2);
                } elseif ($val['type'] == "地区") {
                    $params3['place'] = $infoKey;
                    if (!empty($params['place']) && $params['place'] == $infoKey) {
                        $active = true;
                        $infoI = $infoJ;
                    }
                    $url = APF::get_instance()->get_real_url($sub,'',$params3);
                } else {
                    $url = APF::get_instance()->get_real_url($sub);
                }
                $resultArr[$key]['info'][$infoKey] = array("name" => $infoVal,"url" => $url,"active" => $active);
                $infoJ++;
            }
            //每个分类更多样式控制
            if ($infoI > $oneLineCount) {
                $resultArr[$key]['moreText'] = "收起";
                $resultArr[$key]['moreClass'] = "less";
                $resultArr[$key]['moreTextClass'] = "";
            } else {
                $resultArr[$key]['moreText'] = "更多";
                $resultArr[$key]['moreClass'] = "";
                $resultArr[$key]['moreTextClass'] = "none_li";
            }
        }
        return $resultArr;
    }

    /**
     * 设置用户电影收藏信息
     * @param $movieList
     */
    private function _setShouCangInfo($movieList) {
        if (!empty($this->userId)) {
            $idArr = array();
            foreach($movieList as $moviceVal) {
                $idArr[] = $moviceVal['id'];
            }
            $shouCangInfo = $this->Shoucang->getUserShoucangInfoByInfoIds($this->userId,$idArr);
            $shouCangInfo = $this->initArrById($shouCangInfo,"infoId");
            $this->set_attr("shouCangInfo",$shouCangInfo);
        }
    }

    /**
     * 设置电影库tab信息
     * @param $params
     * @param $movieTabInfo
     */
    private function _setMovieTabInfo($params,$movieTabInfo) {
        unset($params['p']);
        $params['sort'] = (!empty($params['sort']) && !empty($movieTabInfo[$params['sort']])) ? $params['sort'] : 'like';//默认显示最新更新
        $movieTabInfo[$params['sort']]['active'] = true;
        foreach($movieTabInfo as $tabKey => $tabVal) {
            if (in_array($tabVal['sort'],array("show","comming"))) {
                $movieTabInfo[$tabKey]['url'] = APF::get_instance()->get_real_url("/moviceguide",'',array("sort" => $tabVal['sort']));
            } else {
                $params['sort'] = $tabVal['sort'];
                $movieTabInfo[$tabKey]['url'] = APF::get_instance()->get_real_url("/moviceguide",'',$params);
            }
        }
        return $movieTabInfo;
    }

    /**
     * 拼接导演和演员信息
     * @param $params
     */
    private function _initDaoYanAndYanYuanInfo($params) {
        unset($params['p']);
        $params1 = $params2 = $params;
        unset($params1['d']);
        unset($params2['y']);
        $daoyanArr = $yanyuanArr = array();
        //导演
        $params['d'] = empty($params['d']) ? "@" : $params['d'];
        foreach($this->_daoyanInfo as $daoyanKey => $daoyan) {
            $val = $daoyan;
            if ($val == "全部") {
                $val = "@";
            }
            $params1['d'] = $val;
            $daoyanArr[$daoyanKey]['title'] = $daoyan;
            $daoyanArr[$daoyanKey]['url'] = APF::get_instance()->get_real_url("/moviceguide","",$params1);
            if (!empty($params['d']) && ($val == $params['d'])) {//被选中
                $daoyanArr[$daoyanKey]['active'] = true;
            } else {
                $daoyanArr[$daoyanKey]['active'] = false;
            }
        }
        //演员
        $params['y'] = empty($params['y']) ? "@" : $params['y'];
        foreach($this->_yanyuanInfo as $yanyuanKey => $yanyuan) {
            $val = $yanyuan;
            if ($val == "全部") {
                $val = "@";
            }
            $params2['y'] = $val;
            $yanyuanArr[$yanyuanKey]['title'] = $yanyuan;
            $yanyuanArr[$yanyuanKey]['url'] = APF::get_instance()->get_real_url("/moviceguide","",$params2);
            if (!empty($params['y']) && ($val == $params['y'])) {//被选中
                $yanyuanArr[$yanyuanKey]['active'] = true;
            } else {
                $yanyuanArr[$yanyuanKey]['active'] = false;
            }
        }
        return array($daoyanArr,$yanyuanArr);
    }
}
