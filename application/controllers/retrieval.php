<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 检索页面
 * added by xiongjiewu at 2013-06-30
 */
class Retrieval extends CI_Controller {

    private $_movieCount = 300;//按列表一次展示的电影个数
    private $_moviePCount = 100;//按图文一次展示的电影个数
    public function __construct() {
        parent::__construct();
        $this->load->model('Backgroundadmin');
        $this->load->model('Character');
        $this->_letterList = APF::get_instance()->get_config_value("letterList");
        $this->set_attr("userId",$this->userId);
    }

    //大检索
    private $_bR = array(
        "d" => array("title" => "电影索引","type" => "movie","url" => ""),
        "p" => array("title" => "人物索引","type" => "people","url" => ""),
    );

    //字母信息
    private $_letterList;

    //排列方式
    private $_tR = array("p" => "p","w" => "w");

    /** 入口主函数
     * @param null $id
     */
    public function index() {
        $params = $this->input->get();
        $b = empty($params['b']) || empty($this->_bR[$params['b']]) ? "d" : $params['b'];
        $t = empty($params['t']) || empty($this->_tR[$params['t']]) ? "p" : $params['t'];
        ($b == "d") ? $this->load->set_top_index(1) : $this->load->set_top_index(2);
        $s = empty($params['s']) || !in_array($params['s'],$this->_letterList) ? "A" : $params['s'];
        list($infoList,$letterList,$bR,$infoCount) = $this->_initMovieParamsInfo($this->_letterList,$b,$s,$this->_bR,$t);
        $this->set_attr("b",$b);//当前检索方式：电影或人物
        $this->set_attr("t",$t);//页面排列方式
        $this->set_attr("s",$s);//当前字母
        $this->set_attr("infoList",$infoList);//信息
        $this->set_attr("letterList",$letterList);//字母信息
        $this->set_attr("bR",$bR);//大title信息
        $this->set_attr("infoCount",$infoCount);//信息总数
        $this->set_attr("nextOffset",$this->_movieCount);//下一个读取信息的开始位移量，给下拉ajax加载信息用

        $this->load->set_title("检索列表页 - "  . $this->base_title .  " - " . APF::get_instance()->get_config_value("base_name"));
        $this->load->set_css(array("css/dianying/retrieval.css"));
        $this->load->set_js(array("js/dianying/retrieval.js"));
        $this->set_attr("qingxiType",$this->_qingxiType);
        $this->set_attr("shoufeiType",$this->_shoufeiType);
        $this->set_attr("bofangqiType",$this->_bofangqiType);
        if ($t == "p") {//按图文
            $this->set_view('dianying/retrieval','base3');
        } else {//按列表
            $this->set_view('dianying/retrieval2','base3');
        }
    }

    /**
     * 初始化电影和字母信息
     * @param $letterList
     * @param $params
     */
    private function _initMovieParamsInfo($letterList,$b,$s,$bR,$t) {
        $limit = ($t == "p") ? $this->_moviePCount : $this->_movieCount;
        if ($b == "d") {
            list($infoCount,$infoList) = $this->_getMovieInfo($s,0,$limit);
        } else {
            list($infoCount,$infoList) = $this->_getPeopleInfo($s,0,$limit);
        }
        //拼接字母url等信息
        $letterInfoArr = array();
        foreach($letterList as $letterVal) {
            $letterInfoArr[] = array(
                "title" => $letterVal,
                "url" => APF::get_instance()->get_real_url("/retrieval","",array("b" => $b,"t" => $t,"s" => $letterVal)),
                "active" => ($s == $letterVal) ? true : false,
            );
        }
        //拼接电影索引+人物索引信息
        foreach($bR as $bKey => $bRVal) {
            $bR[$bKey]['url'] = APF::get_instance()->get_real_url("/retrieval","",array("b" => $bKey,"t" => $t,"s" => $s));
            $bR[$bKey]['active'] = ($b == $bKey) ? true : false;
        }
        return array($infoList,$letterInfoArr,$bR,$infoCount);
    }

    /**
     * 获取电影信息
     * @param string $letter
     * @param int $offset
     * @param int $limit
     * bool $count 是否获取总数
     */
    private function _getMovieInfo($letter = "A",$offset = 0,$limit = 300,$count = true) {
        $letter = empty($letter) || !in_array($letter,$this->_letterList) ? "A" : $letter;
        if ($letter == "@") {
            $conditionCountStr = "firstLetter in ('0','1','2','3','4','5','6','7','8','9','') and del = 0";
            $conditionStr = $conditionCountStr . " limit {$offset},{$limit}";
        } else {
            $conditionCountStr = "firstLetter = '{$letter}' and del = 0";
            $conditionStr = $conditionCountStr . " limit {$offset},{$limit}";
        }
        if ($count) {
            $movieInfoCount = $this->Backgroundadmin->getDetailInfoCountByCondition($conditionCountStr);
            $movieInfo = $this->Backgroundadmin->getMovieInfoByCon($conditionStr);
            return array($movieInfoCount,$movieInfo);
        } else {
            $movieInfo = $this->Backgroundadmin->getMovieInfoByCon($conditionStr);
            return $movieInfo;
        }
    }

    /**
     * 获取人物信息
     * @param string $letter
     * @param int $offset
     * @param int $limit
     * @param bool $count 是否获取总数
     * @return mixed
     */
    private function _getPeopleInfo($letter = "A",$offset = 0,$limit = 300,$count = true) {
        $letter = empty($letter) || !in_array($letter,$this->_letterList) ? "A" : $letter;
        if ($letter == "@") {
            $conditionCountStr = "firstLetter in ('0','1','2','3','4','5','6','7','8','9','') and del = 0";
            $conditionStr = $conditionCountStr . " order by id desc limit {$offset},{$limit}";
        } else {
            $conditionCountStr = "firstLetter = '{$letter}' and del = 0";
            $conditionStr = $conditionCountStr . " order by id desc limit {$offset},{$limit}";
        }
        if ($count) {
            $peopleInfoCount = $this->Character->getCharacterCountByCon($conditionCountStr);
            $peopleInfo = $this->Character->getCharacterInfoByCon($conditionStr);
            return array($peopleInfoCount,$peopleInfo);
        } else {
            $peopleInfo = $this->Character->getCharacterInfoByCon($conditionStr);
            return $peopleInfo;
        }
    }

    /*
     * ajax获取信息主函数
     */
    public function ajaxgetInfo() {
        $b = $this->input->post("b");
        $t = $this->input->post("t");
        $s = $this->input->post("s");
        $nextOffset = $this->input->post("nextOffset");
        if (empty($b) || empty($s) || empty($nextOffset)) {
            echo json_encode(array("code" => "error","info" => "","nextOffset" => ""));
            exit;
        }
        $limit = ($t == "p") ? $this->_moviePCount : $this->_movieCount;
        if ($b == "d") {//电影信息读取
            $infoList = $this->_getMovieInfo($s,$nextOffset,$limit,false);
        } else {//人物信息读取
            $infoList = $this->_getPeopleInfo($s,$nextOffset,$limit,false);
        }
        if (empty($infoList)) {//无信息
            echo json_encode(array("code" => "error","info" => "","nextOffset" => ""));
            exit;
        } else {
            foreach($infoList as $infoKey => $infoVal) {
                if ($b == "d") {
                    $infoList[$infoKey]['url'] = APF::get_instance()->get_real_url("/detail",$infoVal['id']);
                    $infoList[$infoKey]['imageUrl'] = APF::get_instance()->get_image_url($infoVal['image']);
                } else {
                    $infoList[$infoKey]['url'] = APF::get_instance()->get_real_url("/people",$infoVal['id']);
                    $infoList[$infoKey]['imageUrl'] = APF::get_instance()->get_image_url($infoVal['photo']);
                }

            }
            echo json_encode(array("code" => "success","info" => $infoList,"nextOffset" => count($infoList) + $nextOffset));
            exit;
        }
    }
}