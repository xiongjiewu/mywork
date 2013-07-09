<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站电影详细信息页面
 * added by xiongjiewu at 2013-3-4
 */
class Detail extends CI_Controller {

    private $_caiLimit = 21;
    private $_todayLimit = 20;

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('Backgroundadmin');
        $this->load->model('Actinfo');
        $this->load->model('Directorinfo');
        $this->load->model('User');
        $this->load->model('Userscoringrecords');
        $this->load->set_top_index(-1);
    }
    public function index($id = null)
    {
        $this->set_attr("endcodeId",$id);
        //将id字符串解密，转换成数字id
        $id = intval(APF::get_instance()->decodeId($id));
        if (empty($id)) {
            redirect("/" );
            exit;
        }

        $dyInfo = $this->Backgroundadmin->getDetailInfo($id,0);
        if (empty($dyInfo)) {
            $this->jump_to("/error/index/1/");
            exit;
        }

        //来自页面搜索页，则更新电影搜索次数
        $from = $this->input->get("from");
        $from = htmlspecialchars($from);
        if (!empty($from) && ($from == "search")) {
            //更新影片播放次数
            $this->Backgroundadmin->updateDetailInfo($id,array("searchNum" => $dyInfo['searchNum'] + 1));
        } elseif (!empty($from) && ($from == "yaoyao")) {//来自页面摇一摇，则更新电影摇一摇次数
            //更新影片播放次数
            $this->Backgroundadmin->updateDetailInfo($id,array("yaoyaoNum" => $dyInfo['yaoyaoNum'] + 1));
        }

        //cookie名称+浏览记录
        $userLookInfo = $this->get_cookie($this->look_cookie_key);
        if (!empty($userLookInfo)) {
            $userLookInfo = json_decode($userLookInfo,true);
        }
        $lookArr = array("id" => $dyInfo['id'],"name" => $dyInfo['name']);
        if (!empty($userLookInfo) && !in_array($lookArr,$userLookInfo)) {
            $userLookInfo = array_merge(array($dyInfo['id'] => $lookArr),$userLookInfo);
            $userLookInfo  = array_slice($userLookInfo,0,5);
            $this->set_cookie($this->look_cookie_key,json_encode($userLookInfo));
        } elseif(empty($userLookInfo)) {
            $userLookInfo  = array();
            $userLookInfo[$dyInfo['id']] = array("id" => $dyInfo['id'],"name" => $dyInfo['name']);
            $this->set_cookie($this->look_cookie_key,json_encode($userLookInfo));
        }

        $this->set_attr("userId",$this->userId);

        //电影介绍
        $dyInfo['jieshao'] = strip_tags($dyInfo['jieshao']);
        $dyInfo['jieshao'] = str_replace(">",")",$dyInfo['jieshao']);
        $dyInfo['jieshao'] = str_replace("<","(",$dyInfo['jieshao']);
        $dyInfo['jieshao'] = str_replace('"',"'",$dyInfo['jieshao']);
        $dyInfo['jieshao'] = str_replace("　　","",$dyInfo['jieshao']);
        $dyInfo['jieshao'] = str_replace("　　","",trim($dyInfo['jieshao']));
        $dyInfo['s_jieshao'] = $this->splitStr($dyInfo['jieshao'],180);

        //电影年份
        if (!empty($dyInfo['time1'])) {
            $dyInfo['nianfen'] = date("Y",$dyInfo['time1']);
        } elseif (!empty($dyInfo['time2'])) {
            $dyInfo['nianfen'] = date("Y",$dyInfo['time2']);
        }

        //观看链接
        $watchLinkInfo = $this->Backgroundadmin->getWatchLinkInfoByInfoId($id);
        //下载链接
        $downLoadLinkInfo = $this->Backgroundadmin->getDownLoadLinkInfoByInfoId($id);
        $this->load->model('Yingping');
        $limit = APF::get_instance()->get_config_value("post_show_count");
        $YingpingInfo = $this->Yingping->getYingPingInfoByDyId($id,0,$limit);
        if (!empty($YingpingInfo)) {
            $userIds = array();
            $preg = "/^(https?:\/\/)?(((www\.)?[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)?\.([a-zA-Z]+))|(([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5])\.([0-1]?[0-9]?[0-9]|2[0-5][0-5]))(\:\d{0,4})?)(\/[\w- .\/?%&=]*)?$/i";
            foreach($YingpingInfo as $InfoKey => $infoVal) {
                $infoVal['content'] = htmlspecialchars($infoVal['content']);
                $YingpingInfo[$InfoKey]['content'] = $this->ubb2Html($infoVal['content']);

                //匹配内容中的链接
                preg_match($preg, $YingpingInfo[$InfoKey]['content'], $matches);
                //过滤非站内广告链接
                if (!empty($matches[0]) && strpos($matches[0],".dianying8.tv") === false && strpos($matches[0],".dianyingba.tv") === false) {
                    $YingpingInfo[$InfoKey]['content'] = str_replace($matches[0],"顶！",$YingpingInfo[$InfoKey]['content']);
                }
                $userIds[] = $infoVal['userId'];
            }
            $this->load->model('User');
            $userInfos = $this->User->getUserInfosByIds($userIds);
            $userInfos = $this->initArrById($userInfos,"id");
            $this->set_attr("userInfos",$userInfos);

            $yingpingCount = $this->Yingping->getYingPingCountByDyId($id);
            $this->set_attr("yingpingCount",$yingpingCount);
        }
        $this->set_attr("limit",$limit);
        $this->set_attr("YingpingInfo",$YingpingInfo);

        $userPhoto = "";
        if (!empty($this->userId)) {//已登录
            $this->set_attr("userId",$this->userId);
            //用户信息
            $userInfo = $this->User->getUserInfosByIds(array($this->userId));
            $userInfo = $userInfo[0];
            $userPhoto = $userInfo['photo'];
            $this->set_attr("userInfo",$userInfo);

            $this->load->model('Admin');
            $adminInfo = $this->Admin->getAdminInfoByUserId($this->userId);
            $this->set_attr("adminInfo",$adminInfo);

            $this->load->model('Shoucang');
            $shoucangInfo = $this->Shoucang->getInfoByFiled(array("userId"=>$this->userId,"infoId"=>$id,"del"=>0));
            $this->set_attr("shoucangInfo",$shoucangInfo);

            $this->load->model('Notice');
            $moticeInfo = $this->Notice->getNoticeInfoByFiled(array("userId"=>$this->userId,"infoId"=>$id,"del"=>0));
            $this->set_attr("moticeInfo",$moticeInfo);

            //查询用户是否打过分
            $scoreStr = "infoId = " . $dyInfo['id'] . " and userId = " . $this->userId . " and del = 0 limit 1";
            $scoreInfo = $this->Userscoringrecords->getUserscoringrecordsInfoByCon($scoreStr);
            $this->set_attr("hasDafen",empty($scoreInfo[0]) ? false : true);
        }
        //用户头像
        $userPhoto = APF::get_instance()->get_image_url($userPhoto);
        $this->set_attr("userPhoto",$userPhoto);

        $this->load->set_title("{$dyInfo['name']} - " . $this->base_title .  " - " . APF::get_instance()->get_config_value("base_name"));
        $this->load->set_css(array("css/dianying/newdetail2.css"));
        $this->load->set_js(array("js/dianying/newdetail2.js"));
        
        $this->set_attr("dyInfo",$dyInfo);
        $this->set_attr("watchLinkInfo",$watchLinkInfo);
        foreach($downLoadLinkInfo as $downLoadLinkKey => $downLoadLinkVal) {
            if (empty($downLoadLinkVal['link'])) {
                unset($downLoadLinkInfo[$downLoadLinkKey]);
            }
        }

        //猜你喜欢，获取同类型电影
        $caiNiXiHuanConStr = "type = " . $dyInfo['type'] . " and del = 0 order by nianfen desc limit " . ($this->_caiLimit + 50);
        $caiNiXiHuanInfo = $this->Backgroundadmin->getMovieInfoByCon($caiNiXiHuanConStr);
        if (!empty($caiNiXiHuanInfo)) {
            //把当前电影信息从导演作品中过滤掉
            $caiNiXiHuanInfo = $this->initArrById($caiNiXiHuanInfo,"id");
            unset($caiNiXiHuanInfo[$dyInfo['id']]);
            shuffle($caiNiXiHuanInfo);
            $caiNiXiHuanInfo = array_slice($caiNiXiHuanInfo,0,$this->_caiLimit);
        }
        $this->set_attr("caiNiXiHuanInfo",$caiNiXiHuanInfo);

        //导演作品信息
        $daoyanMovieInfo  = array();
        if ($dyInfo['daoyan'] != "暂无") {
            $daoyan = str_replace("/","、",$dyInfo['daoyan']);
            $daoyanArr = explode("、",$daoyan);
            foreach($daoyanArr as $daoyanName) {
                $movieInfo = $this->Directorinfo->getDirectorinfoByDirectorNameLimit(trim($daoyanName),0,$this->_caiLimit + 1);
                $daoyanMovieInfo = array_merge($daoyanMovieInfo,$movieInfo);
                if (count($daoyanMovieInfo) > $this->_caiLimit) {//够筛选个数，则跳出
                    break;
                }
            }
        }
        if (!empty($daoyanMovieInfo)) {
            //把当前电影信息从导演作品中过滤掉
            $this->initArrById($daoyanMovieInfo,"infoId",$daoyanMovieId);
            $daoyanMovieInfo = $this->Backgroundadmin->getDetailInfo($daoyanMovieId,0,true);
            $daoyanMovieInfo = $this->initArrById($daoyanMovieInfo,"id");
            unset($daoyanMovieInfo[$dyInfo['id']]);
            $daoyanMovieInfo = array_slice($daoyanMovieInfo,0,$this->_caiLimit);
        }
        $this->set_attr("daoyanMovieInfo",$daoyanMovieInfo);

        //主演作品信息
        $zhuyanMovieInfo  = array();
        if ($dyInfo['zhuyan'] != "暂无") {
            $zhuyan = str_replace("/","、",$dyInfo['zhuyan']);
            $zhuyanArr = explode("、",$zhuyan);
            foreach($zhuyanArr as $zhuyanName) {
                $movieInfo = $this->Actinfo->getActinfoByActinNameLimit(trim($zhuyanName),0,$this->_caiLimit + 1);
                $zhuyanMovieInfo = array_merge($zhuyanMovieInfo,$movieInfo);
                if (count($zhuyanMovieInfo) > $this->_caiLimit) {//够筛选个数，则跳出
                    break;
                }
            }
        }
        if (!empty($zhuyanMovieInfo)) {
            //把当前电影信息从主演作品中过滤掉
            $this->initArrById($zhuyanMovieInfo,"infoId",$zhuyanMovieId);
            $zhuyanMovieInfo = $this->Backgroundadmin->getDetailInfo($zhuyanMovieId,0,true);
            $zhuyanMovieInfo = $this->initArrById($zhuyanMovieInfo,"id");
            unset($zhuyanMovieInfo[$dyInfo['id']]);
            $zhuyanMovieInfo = array_slice($zhuyanMovieInfo,0,$this->_caiLimit);
        }
        $this->set_attr("zhuyanMovieInfo",$zhuyanMovieInfo);

        //今日更新
        $todayConStr = "del = 0 order by createtime desc limit " . ($this->_todayLimit + 1);
        $todayInfo = $this->Backgroundadmin->getMovieInfoByCon($todayConStr);
        if (!empty($todayInfo)) {
            //把当前电影信息从导演作品中过滤掉
            $todayInfo = $this->initArrById($todayInfo,"id");
            unset($todayInfo[$dyInfo['id']]);
            $todayInfo = array_slice($todayInfo,0,$this->_todayLimit);
        }
        $this->set_attr("todayInfo",$todayInfo);

        //分数五角星信息
        list($startInfo,$currentKey) = $this->_initStartInfo($dyInfo['score']);
        $this->set_attr("startInfo",$startInfo);
        $this->set_attr("currentKey",$currentKey);

        $this->set_attr("downLoadLinkInfo",$downLoadLinkInfo);
        $this->set_attr("downLoadType",$this->_downLoadType);
        $this->set_attr("qingxiType",$this->_qingxiType);
        $this->set_attr("shoufeiType",$this->_shoufeiType);
        $this->set_attr("bofangqiType",$this->_bofangqiType);
        $this->set_attr("moviePlace",$this->_moviePlace);
        $this->set_attr("movieType",$this->_movieType);
        $this->set_view('dianying/newdetail2','base3');
    }

    private $_startInfo = array(
        1 => array("title" => "靠，烂片","score" => 2),
        2 => array("title" => "不给力啊","score" => 4),
        3 => array("title" => "勉强可以看","score" => 6),
        4 => array("title" => "奈斯，值得看","score" => 8),
        5 => array("title" => "very棒，爽","score" => 10),
    );

    /**
     * 初始化打分五角星
     */
    private function _initStartInfo($score) {
        $currentKey = 0;
        foreach($this->_startInfo as $startKey => $startVal) {
            if ($score > 0 && $score >= $startVal['score']) {
                $this->_startInfo[$startKey]['active'] = true;
                $currentKey = $startKey;
            } else {
                $this->_startInfo[$startKey]['active'] = false;
            }
        }
        return array($this->_startInfo,$currentKey);
    }
}