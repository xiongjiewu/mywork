<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站后台页面
 * added by xiongjiewu at 2013-3-4
 */
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

    /** 根据主演搜索电影名
     * @param $name
     * @return mixed
     */
    private function _getDetailInfoBySearchDaoYan($name,$type = '',$year = '',$diqu = '',$limit = 50) {
        $name = mysql_real_escape_string($name);
        $conStr = $this->_getMoviceCon($type,$year,$diqu);
        $conStr .= " order by nianfen desc";
        $searchMovieInfo = $this->Backgroundadmin->getDetailInfoBySearchDaoYan(trim($name),$limit,$conStr);
        return $searchMovieInfo;
    }

    /** 根据主演搜索电影名
     * @param $name
     * @return mixed
     */
    private function _getDetailInfoBySearchName($name,$type = '',$year = '',$diqu = '',$limit = 50) {
        $name = mysql_real_escape_string($name);
        $conStr = $this->_getMoviceCon($type,$year,$diqu);
        $conStr .= " order by nianfen desc";
        $searchMovieInfo = $this->Backgroundadmin->getDetailInfoBySearchZhuYan(trim($name),$limit,$conStr);
        return $searchMovieInfo;
    }

    /** 根据电影名称关键字搜索电影
     * @param $searchW
     * @param int $limit
     * @return mixed
     */
    private function _getDetailInfoBySearchW($searchW,$type = '',$year = '',$diqu = '',$limit  = 50) {
        $searchW = mysql_real_escape_string($searchW);
        $conStr = $this->_getMoviceCon($type,$year,$diqu);
        $conStr .= " order by nianfen desc";
        $searchMovieInfo = $this->Backgroundadmin->getDetailInfoBySearchW(trim($searchW),$limit,$conStr);
        return $searchMovieInfo;
    }

    /** 根据电影名称关键字搜索电影
     * @param $searchW
     * @param int $limit
     * @return mixed
     */
    private function _getDetailInfoByDyName($searchW,$type = '',$year = '',$diqu = '',$limit  = 50) {
        $searchW = mysql_real_escape_string($searchW);
        $conStr = $this->_getMoviceCon($type,$year,$diqu);
        $conStr .= " order by nianfen desc";
        $searchMovieInfo = $this->Backgroundadmin->getDetailInfoByDyName(trim($searchW),$limit,$conStr);
        return $searchMovieInfo;
    }

    /** 根据类型、年份、地区拼接筛选条件
     * @param string $type
     * @param string $year
     * @param string $diqu
     * @return string
     */
    private function _getMoviceCon($type = '',$year = '',$diqu = '') {
        $type = intval($type);
        $year = intval($year);
        $diqu = intval($diqu);
        $con = array();
        if (!empty($type)) {
            $con[] = "type = " . $type;
        }
        if (!empty($year)) {
            $con[] = "nianfen = " . $year;
        }
        if (!empty($diqu)) {
            $con[] = "diqu = " . $diqu;
        }
        return empty($con) ? "" : " and " . implode(" and ",$con);
    }

    public function index($type = "all",$year = "all",$diqu = "all") {
        $searchW = $this->input->get("key");
        $searchW = htmlspecialchars($searchW);
        if (empty($searchW)) {
            $this->jump_to("/moviceguide/");
            exit;
        }
        //过滤特殊字符
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

        //类型、年份、地区筛选
        $type = empty($this->_movieType[intval($type)]) ? "all" : $type;
        $year = empty($this->_movieNianFen[intval($year)]) ? "all" : $year;
        $diqu = empty($this->_moviePlace[intval($diqu)]) ? "all" : $diqu;
        $this->set_attr("type",$type);
        $this->set_attr("year",$year);
        $this->set_attr("diqu",$diqu);

        //开始匹配搜索关键字的电影
        $searchMovieInfo = $firstMoviceInfo = array();
        $wordArr[0] = array_unique($wordArr[0]);

        //开始查询信息
        $moviceI = 0;
        foreach($wordArr[0] as $wordVal) {
            $str = "<em>" . $wordVal . "</em>";
            //电影名搜索
            if ($moviceI == 0) {//全匹配信息数组,第一次名称全匹配整个词
                $searchInfo = $this->_getDetailInfoByDyName($wordVal,$type,$year,$diqu);
            } else {
                $searchInfo = $this->_getDetailInfoBySearchW($wordVal,$type,$year,$diqu);
            }
            if (!empty($searchInfo)) {
                foreach($searchInfo as $sKey => $sInfo) {
                    //替换名称中的搜索关键字
                    $sInfo['s_name'] = str_replace($wordVal,$str,$sInfo['name']);
                    //替换主演中的搜索关键字
                    if (!empty($sInfo['zhuyan'])) {
                        $sInfo['zhuyan'] = str_replace("/","、",$sInfo['zhuyan']);
                        $sInfo['zhuyan'] = str_replace($wordVal,$str,$sInfo['zhuyan']);
                    }
                    //替换导演中的搜索关键字
                    if (!empty($sInfo['daoyan'])) {
                        $sInfo['daoyan'] = str_replace("/","、",$sInfo['daoyan']);
                        $sInfo['daoyan'] = str_replace($wordVal,$str,$sInfo['daoyan']);
                    }
                    $sInfo['jieshao'] = str_replace("&nbsp;","",$sInfo['jieshao']);
                    $sInfo['jieshao'] = str_replace("\t","",$sInfo['jieshao']);
                    $searchInfo[$sKey] = $sInfo;
                }
                if ($moviceI == 0) {//全匹配信息数组
                    $firstMoviceInfo = array_merge($firstMoviceInfo,$searchInfo);
                } else {
                    $searchMovieInfo = array_merge($searchMovieInfo,$searchInfo);
                }
            }

            //电影主演搜索
            $searchInfo1 = $this->_getDetailInfoBySearchName($wordVal,$type,$year,$diqu);
            if (!empty($searchInfo1)) {
                foreach($searchInfo1 as $sKey1 => $sInfo1) {
                    //替换名称中的搜索关键字
                    $sInfo1['s_name'] = str_replace($wordVal,$str,$sInfo1['name']);
                    //替换主演中的搜索关键字
                    $sInfo1['zhuyan'] = str_replace("/","、",$sInfo1['zhuyan']);
                    $sInfo1['zhuyan'] = str_replace($wordVal,$str,$sInfo1['zhuyan']);
                    //替换导演中的搜索关键字
                    if (!empty($sInfo1['daoyan'])) {
                        $sInfo1['daoyan'] = str_replace("/","、",$sInfo1['daoyan']);
                        $sInfo1['daoyan'] = str_replace($wordVal,$str,$sInfo1['daoyan']);
                    }
                    $sInfo1['jieshao'] = str_replace("&nbsp;","",$sInfo1['jieshao']);
                    $sInfo1['jieshao'] = str_replace("\t","",$sInfo1['jieshao']);
                    $searchInfo1[$sKey1] = $sInfo1;
                }
                $searchMovieInfo = array_merge($searchMovieInfo,$searchInfo1);
            }

            //电影导演搜索
            $searchInfo2 = $this->_getDetailInfoBySearchDaoYan($wordVal,$type,$year,$diqu);
            if (!empty($searchInfo2)) {
                foreach($searchInfo2 as $sKey2 => $sInfo2) {
                    //替换名称中的搜索关键字
                    $sInfo2['s_name'] = str_replace($wordVal,$str,$sInfo2['name']);
                    //替换名称中的搜索关键字
                    if (!empty($sInfo2['zhuyan'])) {
                        $sInfo2['zhuyan'] = str_replace("/","、",$sInfo2['zhuyan']);
                        $sInfo2['zhuyan'] = str_replace($wordVal,$str,$sInfo2['zhuyan']);
                    }
                    $sInfo2['daoyan'] = str_replace("/","、",$sInfo2['daoyan']);
                    $sInfo2['daoyan'] = str_replace($wordVal,$str,$sInfo2['daoyan']);
                    $sInfo2['jieshao'] = str_replace("&nbsp;","",$sInfo2['jieshao']);
                    $sInfo2['jieshao'] = str_replace("\t","",$sInfo2['jieshao']);
                    $searchInfo2[$sKey2] = $sInfo2;
                }
                $searchMovieInfo = array_merge($searchMovieInfo,$searchInfo2);
            }
            $moviceI++;
        }

        $ids = $nianfenArr = $existWatch = array();
        foreach($firstMoviceInfo as $searchVal) {
            $existWatch[] = $searchVal['exist_watch'];
        }
        //按是否有观看链接排序
        array_multisort($existWatch, SORT_DESC,$firstMoviceInfo);

        foreach($searchMovieInfo as $infoKey => $infoVal) {
            $infoVal['jieshao'] = str_replace("","",$infoVal['jieshao']);
            $infoVal['jieshao'] = str_replace("　　","",$infoVal['jieshao']);
            $infoVal['jieshao'] = str_replace("&nbsp;","",$infoVal['jieshao']);
            $searchMovieInfo[$infoKey]['jieshao'] = $this->splitStr($infoVal['jieshao'],95);
            //年份，用作按年份排序
            $nianfenArr[] = empty($infoVal['nianfen']) ? 0 : $infoVal['nianfen'];
        }
        //按年份排序
        array_multisort($nianfenArr, SORT_DESC,$searchMovieInfo);
        $searchMovieInfo = array_merge($firstMoviceInfo,$searchMovieInfo);
        //去掉重复电影
        $searchMovieInfo = $this->_initArr($searchMovieInfo,$ids);
        $this->set_attr("searchMovieInfo",$searchMovieInfo);

        //观看链接
        $watchLinkInfo = $this->Backgroundadmin->getWatchLinkInfoByInfoId($ids);
        $watchLinkInfo = $this->_getNewArrById($watchLinkInfo);
        $this->set_attr("watchLinkInfo",$watchLinkInfo);

        $this->load->set_head_img(false);
        $this->load->set_title("搜'{$searchW}'相关的影片 - " . $this->base_title . " - " . APF::get_instance()->get_config_value("base_name"));
        $this->load->set_css(array("/css/dianying/newsearch.css"));
        $this->load->set_js(array("/js/dianying/newsearch.js"));

        $this->load->set_top_index(-1);
        $this->set_attr("searchW",$searchW);
        $this->set_attr("moviePlace",$this->_moviePlace);
        $this->set_attr("movieType",$this->_movieType);
        $this->set_attr("movieSortType",APF::get_instance()->get_config_value("movie_type"));
        $this->set_view('dianying/newsearch');
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

    private function _initArr($arr,&$ids = array(),$filed = "id") {
        if (empty($arr)) {
            return $arr;
        }
        $result = array();
        foreach($arr as $aV) {
            if (empty($result[$aV[$filed]])) {
                $result[$aV[$filed]] = $aV;
                $ids[] = $aV[$filed];
            }
        }
        return $result;
    }

    private function _getNewArrById($nfo)
    {
        if (empty($nfo)) {
            return $nfo;
        }
        $result = array();
        foreach($nfo as $infoVal) {
            $result[$infoVal['infoId']][] = $infoVal;
        }
        return $result;
    }

}
