<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站后台页面
 * added by xiongjiewu at 2013-3-4
 */
class Search extends MY_Controller {
    private $_maxLen = 25;
    public function __construct() {
        parent::__construct();
        $this->load->model('Backgroundadmin');
        $this->load->model('Actinfo');
        $this->load->model('Directorinfo');
        $this->load->model('Character');
        $this->load->model("Wordsplit");
        $this->load->set_top_index(-1);
    }

    /** 过滤sql特殊字符
     * @param $str
     * @return string
     */
    private function _repeatSpeStr($str) {
        return mysql_real_escape_string($str);
    }

    /** 过滤特殊字符
     * @param $searchW
     * @return mixed
     */
    private  function _pregReplacespeaStr($searchW) {
        $searchW = preg_replace('/\'+|"+/','',rawurldecode($searchW));//过滤特殊字符
        return $searchW;
    }

    /** 根据主演搜索电影名
     * @param $name
     * @return mixed
     */
    private function _getDetailInfoBySearchDaoYan($name,$type = '',$year = '',$diqu = '',$limit = 50) {
        $name = $this->_repeatSpeStr($name);
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
        $name = $this->_repeatSpeStr($name);
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
    private function _getDetailInfoBySearchW($searchW,$type = '',$year = '',$diqu = '',$limit  = 50,$pinyin = false) {
        $searchW = $this->_repeatSpeStr($searchW);
        $conStr = $this->_getMoviceCon($type,$year,$diqu);
        $conStr .= " order by nianfen desc";
        if ($pinyin) {//拼音搜索
            $searchMovieInfo = $this->Backgroundadmin->getDetailInfoBySearchPinYin(trim($searchW),$limit,$conStr);
        } else {
            $searchMovieInfo = $this->Backgroundadmin->getDetailInfoBySearchW(trim($searchW),$limit,$conStr);
        }
        return $searchMovieInfo;
    }

    /** 根据电影名称关键字搜索电影
     * @param $searchW
     * @param int $limit
     * @return mixed
     */
    private function _getDetailInfoByDyName($searchW,$type = '',$year = '',$diqu = '',$limit  = 50,$pinyin = false) {
        $searchW = $this->_repeatSpeStr($searchW);
        $conStr = $this->_getMoviceCon($type,$year,$diqu);
        $conStr .= " order by nianfen desc";
        if ($pinyin) {
            $searchMovieInfo = $this->Backgroundadmin->getDetailInfoByDyPinYin(trim($searchW),$limit,$conStr);
        } else {
            $searchMovieInfo = $this->Backgroundadmin->getDetailInfoByDyName(trim($searchW),$limit,$conStr);
        }
        return $searchMovieInfo;
    }

    /**
     * 根据演员名称获取电影信息
     * @param $searchW
     * @param string $type
     * @param string $year
     * @param string $diqu
     */
    private function _getMovieInfoBYYanYuan($searchW,$type = '',$year = '',$diqu = '',$pinyin = false) {
        $searchW = $this->_repeatSpeStr($searchW);
        if ($pinyin) {//拼音搜索
            $movieIdInfo = $this->Actinfo->getActinfoByActinPinYin($searchW);
        } else {
            $movieIdInfo = $this->Actinfo->getActinfoByActinName($searchW);
        }

        $movieInfos = $ids = array();
        $zhongwenName = "";
        if (!empty($movieIdInfo)) {
            //中文名
            $zhongwenName = $movieIdInfo[0]['name'];

            foreach($movieIdInfo as $movieIdVal) {
                $ids[] = $movieIdVal['infoId'];
            }
            $conStr = $this->_getMoviceCon($type,$year,$diqu);
            if (empty($conStr)) {
                $conditionStr = " id in(" . implode(",",$ids) . ") and del = 0  order by nianfen desc";
            } else {
                $conStr .= " order by nianfen desc";
                $conditionStr = " id in(" . implode(",",$ids) . ") and del = 0 " . $conStr;
            }
            $movieInfos = $this->Backgroundadmin->getMovieInfoByCon($conditionStr);
        }
        return array($movieInfos,$zhongwenName);
    }

    /**
     * 根据导演名称获取电影信息
     * @param $searchW
     * @param string $type
     * @param string $year
     * @param string $diqu
     */
    private function _getMovieInfoBYDaoYuan($searchW,$type = '',$year = '',$diqu = '',$pinyin = false) {
        $searchW = $this->_repeatSpeStr($searchW);
        if ($pinyin) {
            $movieIdInfo = $this->Directorinfo->getDirectorinfoByDirectorPinYin($searchW);
        } else {
            $movieIdInfo = $this->Directorinfo->getDirectorinfoByDirectorName($searchW);
        }

        $movieInfos = $ids = array();
        $zhongwenName = "";
        if (!empty($movieIdInfo)) {
            //中文名
            $zhongwenName = $movieIdInfo[0]['name'];

            foreach($movieIdInfo as $movieIdVal) {
                $ids[] = $movieIdVal['infoId'];
            }
            $conStr = $this->_getMoviceCon($type,$year,$diqu);
            if (empty($conStr)) {
                $conditionStr = " id in(" . implode(",",$ids) . ") and del = 0  order by nianfen desc";
            } else {
                $conStr .= " order by nianfen desc";
                $conditionStr = " id in(" . implode(",",$ids) . ") and del = 0  " . $conStr;
            }
            $movieInfos = $this->Backgroundadmin->getMovieInfoByCon($conditionStr);
        }
        return array($movieInfos,$zhongwenName);
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

    /**
     * 搜索主函数
     */
    private function _searchMian($searchW,$type,$year,$diqu,$limit = 50) {
        //开始匹配搜索关键字的电影
        $searchMovieInfo = $firstMoviceInfo = array();

        if (ctype_alnum($searchW)) {//如果是字母和数字组合，不分词并采用拼音搜索
            $wordArr[0] = array_merge(array($searchW),array($searchW));
            $pinyin = true;
        } else {
            //分词数组
            $wordArr = array();

            //空格分割数组
            $sK = explode(" ",$searchW);
            $sK = array_merge(array($searchW),$sK);

            //分词
            $wordArr[] = array_merge($sK,$this->Wordsplit->get_tags_arr($searchW));

            $wordArr[0] = array_unique($wordArr[0]);
            $wordArr[0] = array_merge(array($searchW),$wordArr[0]);
            $pinyin = false;
        }

        //开始查询信息
        $moviceI = 0;
        foreach($wordArr[0] as $wordVal) {
	        $wordVal =  $wordValKey = htmlspecialchars($wordVal);
            $str = "<em>" . $wordVal . "</em>";
            //电影名搜索
            if ($moviceI == 0) {//全匹配信息数组,第一次名称全匹配整个词
                $searchInfo = $this->_getDetailInfoByDyName($wordVal,$type,$year,$diqu,$limit,$pinyin);
            } else {
                $searchInfo = $this->_getDetailInfoBySearchW($wordVal,$type,$year,$diqu,$limit,$pinyin);
            }
            if (!empty($searchInfo)) {
                foreach($searchInfo as $sKey => $sInfo) {
                    //替换名称中的搜索关键字，如果是拼音搜索则名称直接替换
                    if ($pinyin) {
                        $str = "<em>{$sInfo['name']}</em>";
                        $wordValKey = $sInfo['name'];
                    }
                    $sInfo['s_name'] = str_replace($wordValKey,$str,$sInfo['name']);

                    //替换主演中的搜索关键字
                    if (!empty($sInfo['zhuyan'])) {
                        $sInfo['zhuyan'] = str_replace("/","、",$sInfo['zhuyan']);
                        $sInfo['zhuyan'] = str_replace($wordValKey,$str,$sInfo['zhuyan']);
                    }
                    //替换导演中的搜索关键字
                    if (!empty($sInfo['daoyan'])) {
                        $sInfo['daoyan'] = str_replace("/","、",$sInfo['daoyan']);
                        $sInfo['daoyan'] = str_replace($wordValKey,$str,$sInfo['daoyan']);
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

            if ($moviceI > 0 && ($wordVal == $searchW)) {//演员、导演整词匹配只需要查询一次
                continue;
            }

            //电影主演搜索
            list($searchInfo1,$zhongwenName) = $this->_getMovieInfoBYYanYuan($wordVal,$type,$year,$diqu,$pinyin);
            if (!empty($searchInfo1)) {
                if ($pinyin) {
                    $str = "<em>{$zhongwenName}</em>";
                    $wordValKey = $zhongwenName;
                }

                foreach($searchInfo1 as $sKey1 => $sInfo1) {
                    //替换名称中的搜索关键字
                    $sInfo1['s_name'] = str_replace($wordValKey,$str,$sInfo1['name']);
                    //替换主演中的搜索关键字
                    $sInfo1['zhuyan'] = str_replace("/","、",$sInfo1['zhuyan']);
                    $sInfo1['zhuyan'] = str_replace($wordValKey,$str,$sInfo1['zhuyan']);
                    //替换导演中的搜索关键字
                    if (!empty($sInfo1['daoyan'])) {
                        $sInfo1['daoyan'] = str_replace("/","、",$sInfo1['daoyan']);
                        $sInfo1['daoyan'] = str_replace($wordValKey,$str,$sInfo1['daoyan']);
                    }
                    $sInfo1['jieshao'] = str_replace("&nbsp;","",$sInfo1['jieshao']);
                    $sInfo1['jieshao'] = str_replace("\t","",$sInfo1['jieshao']);
                    $searchInfo1[$sKey1] = $sInfo1;
                }
                $searchMovieInfo = array_merge($searchMovieInfo,$searchInfo1);
            }

            //电影导演搜索
            list($searchInfo2,$zhongwenName) = $this->_getMovieInfoBYDaoYuan($wordVal,$type,$year,$diqu,$pinyin);
            if (!empty($searchInfo2)) {
                if ($pinyin) {
                    $str = "<em>{$zhongwenName}</em>";
                    $wordValKey = $zhongwenName;
                }

                foreach($searchInfo2 as $sKey2 => $sInfo2) {
                    //替换名称中的搜索关键字
                    $sInfo2['s_name'] = str_replace($wordValKey,$str,$sInfo2['name']);
                    //替换名称中的搜索关键字
                    if (!empty($sInfo2['zhuyan'])) {
                        $sInfo2['zhuyan'] = str_replace("/","、",$sInfo2['zhuyan']);
                        $sInfo2['zhuyan'] = str_replace($wordValKey,$str,$sInfo2['zhuyan']);
                    }
                    $sInfo2['daoyan'] = str_replace("/","、",$sInfo2['daoyan']);
                    $sInfo2['daoyan'] = str_replace($wordValKey,$str,$sInfo2['daoyan']);
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

        foreach($searchMovieInfo as $infoVal) {
            $infoVal['jieshao'] = str_replace("","",$infoVal['jieshao']);
            $infoVal['jieshao'] = str_replace("　　","",$infoVal['jieshao']);
            $infoVal['jieshao'] = str_replace("&nbsp;","",$infoVal['jieshao']);
            //年份，用作按年份排序
            $nianfenArr[] = empty($infoVal['nianfen']) ? 0 : $infoVal['nianfen'];
        }
        //按年份排序
        array_multisort($nianfenArr, SORT_DESC,$searchMovieInfo);
        $searchMovieInfo = array_merge($firstMoviceInfo,$searchMovieInfo);
        //去掉重复电影
        $searchMovieInfo = $this->_initArr($searchMovieInfo,$ids);
        //介绍截取
        foreach($searchMovieInfo as $infoKey => $infoVal) {
            $searchMovieInfo[$infoKey]['jieshao'] = $this->splitStr($infoVal['jieshao'],100);
        }
        return array($searchMovieInfo,$ids);
    }

    /**
     * 页面入口函数
     * @param string $type
     * @param string $year
     * @param string $diqu
     */
    public function index($type = "all",$year = "all",$diqu = "all") {
        $searchW = trim($this->input->get("key"));
        $searchW = urldecode($searchW);
        //过滤特殊字符
        $searchW = htmlspecialchars($searchW);
        $searchW = $this->_pregReplacespeaStr($searchW);
        if (empty($searchW)) {
            $this->jump_to("/moviceguide/");
            exit;
        }
        $this->set_attr("searchW",$searchW);

        //长度截取
        if (mb_strlen($searchW,"utf8") > $this->_maxLen) {
            $searchW = mb_substr($searchW,0,$this->_maxLen);
        }
        $searchW = htmlspecialchars($searchW);
        //类型、年份、地区筛选
        $type = empty($this->_movieType[intval($type)]) ? "all" : $type;
        $year = empty($this->_movieNianFen[intval($year)]) ? "all" : $year;
        $diqu = empty($this->_moviePlace[intval($diqu)]) ? "all" : $diqu;
        $this->set_attr("type",$type);
        $this->set_attr("year",$year);
        $this->set_attr("diqu",$diqu);

        //开始搜索人物信息
        $peopleConditionStr = "name = '" . $searchW . "' and del = 0 limit 1";
        $peopleInfo = $this->Character->getCharacterInfoByCon($peopleConditionStr);
        if (!empty($peopleInfo[0])) {
            $this->set_attr("peopleInfo",$peopleInfo[0]);
            //星座信息
            $xingzuoInfo = APF::get_instance()->get_config_value("constellatoryInfo");
            $this->set_attr("xingzuoInfo",$xingzuoInfo);
        }

        //搜索处理
        list($searchMovieInfo,$ids) = $this->_searchMian($searchW,$type,$year,$diqu,50);
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
        $this->set_view('dianying/newsearch','base3');
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
        $searchMovieInfo = $this->_searchMian($word,'','','',20);
        $searchMovieInfo = array_slice($searchMovieInfo[0],0,20);
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
            if (empty($resultArr[$sVal['name']])) {
                $sVal['name'] = trim($sVal['name']);
                $resultArr[$sVal['name']] = $sVal;
            }
        }

        $newResArr = array();
        $i = 0;
        foreach($resultArr as $resultVal) {
            $resultVal['image'] = trim(APF::get_instance()->get_config_value("img_base_url"), "/") . $resultVal['image'];
            $resultVal['typeText'] = $this->_movieType[$resultVal['type']];
            $resultVal['zhuyan'] = empty($resultVal['zhuyan']) ? "暂无" : str_replace("、","/",$resultVal['zhuyan']);;
            $resultVal['daoyan'] = empty($resultVal['daoyan']) ? "暂无" : str_replace("、","/",$resultVal['daoyan']);
            $resultVal['nianfen'] = empty($resultVal['nianfen']) ? "暂无" : $resultVal['nianfen'];
            $resultVal['url'] = "/detail/index/" . APF::get_instance()->encodeId($resultVal['id']);
            $resultVal['typeUrl'] = "/moviceguide/type/" . $resultVal['type'] . "/";
            $resultVal['jieshao'] = str_replace("　　","",trim($resultVal['jieshao']));
            $newResArr[$i++] = $resultVal;
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
