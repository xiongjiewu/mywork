<?php
/**
 * 人物信息补全job
 * 修复一些没有介绍或者没有头像的人物
 * added by xiongjiewu at 2013-07-11
 */
include("jobBase.php");
class FixPeopleInfo extends jobBase
{
    private $_idFile = "/home/www/logs/dianying/peopleInfo.id";

    private $_id;
    private $_limit = 500;

    //百度搜索链接
    private $_baiduSearchLink = "http://www.baidu.com/s?wd={A}&rsv_spt=1&issp=1&rsv_bp=0&ie=utf-8&tn=baiduhome_pg&rsv_n=2";

    function __construct() {
        parent::__construct();
    }
    public function run() {
        $this->_id = 0;
        if (file_exists($this->_idFile)) {
            $currentId = file_get_contents($this->_idFile);
            $currentId = intval($currentId);
            if (!empty($currentId)) {
                $this->_id = $currentId;
            }
        }

        $i = 0;
        while(true) {
            if ($i > 700) {//每处理700个退出job
                exit;
            }
            $peopleInfo = $this->_getCharacterInfo($this->_id,$this->_limit,"all");
            if (empty($peopleInfo)) {//没有要处理的信息
                var_dump($i);
                file_put_contents($this->_idFile,0);
                exit;
            }
            foreach($peopleInfo as $peopleVal) {
                $this->_id = $peopleVal['id'];
                file_put_contents($this->_idFile,$peopleVal['id']);
                //检查介绍是否需要处理
                if ($this->_checkJieshao($peopleVal['jieshao'])) {
                    list($jieshao,$baikeLink) = $this->_getJieShao($peopleVal['name']);
                    if (empty($jieshao) || ($jieshao == $peopleVal['jieshao'])) {//百度抓取不成功，使用搜狗抓取
                        list($jieshao,$baikeLink) = $this->_getJieShaoFromSogou($peopleVal['name']);
                    }
                    if (!empty($jieshao) && ($jieshao != $peopleVal['jieshao'])) {//介绍抓取成功，更新人物介绍信息
                        $upData['jieshao'] = $jieshao;
                        if (empty($peopleVal['baikeLink']) && !empty($baikeLink)) {//百科链接
                            $upData['baikeLink'] = trim($baikeLink);
                        }
                        $upRes = $this->_updateInfo(array("id" => $peopleVal['id']),$upData,"tbl_character");
                        if (!empty($upRes)) {
                            var_dump("人物[{$peopleVal['id']}]--[{$peopleVal['name']}]介绍处理成功!\n");
                        } else {
                            var_dump("人物[{$peopleVal['id']}]--[{$peopleVal['name']}]介绍处理失败!\n");
                        }
                    } else {
                        var_dump("人物[{$peopleVal['id']}]--[{$peopleVal['name']}]没有匹配到任何介绍!\n");
                    }
                }
                $i++;
            }
        }
    }

    /**
     * 检查图片是否需要处理
     * @param $image
     * @return bool
     */
    private function _checkJieshao($jieshao) {
        $jieshao = trim($jieshao);
        if (empty($jieshao)) {//=无介绍
            return true;
        } elseif ($jieshao == "暂无") {//或者=暂无
            return true;
        }elseif ($jieshao == "Array") {//或者=暂无
            return true;
        } else {
            return false;
        }
    }

    /**
     * 从百度抓取图片处理函数
     * @param $id
     * @param $name
     */
    private function _getJieShao($name) {
        //百科链接
        $baiduSearchLink = $this->_baiduSearchLink;
        $baiduSearchLink = str_replace("{A}",$name,$baiduSearchLink);//拼接百度搜索链接
        $baiduHmtl = $baiduTotalHmtl = $this->_getCurlInfo($baiduSearchLink,false,"http://www.baidu.com");
        $imageInfo = $this->_getPregMatch("/<div class=\"op_zx_renwu_baike_content\">(.*?)<h2>(.*?)<\/h2>/si", $baiduHmtl);
        if (empty($imageInfo[2])) {
            $imageInfo = $this->_getPregMatch("/_百度百科(.*?)}/si", $baiduHmtl);
            if (!empty($imageInfo[1])) {
                $imageInfo[2] = $imageInfo[1];
                $imageInfo[2] = str_replace("link : ","href=",$imageInfo[2]);
                $imageInfo[2] = str_replace("'",'"',$imageInfo[2]) . "_百度百科";
            }
        }

        if (!empty($imageInfo[2]) && strpos($imageInfo[2],"_百度百科") !== false) {
            $baikeLink = $this->_getPregMatch("/href=\"(.*?)\"/si",$imageInfo[2]);
            if (!empty($baikeLink[1])) {
                //获取百科详细url
                $baiduHmtl = $this->_getCurlInfo($baikeLink[1],false,"http://www.baidu.com");
                if (strpos($baiduHmtl,"The document has moved") !== false) {
                    return $this->_getBaiduJieShao($name,$baiduHmtl);
                }
            }
        }
        return array("","");
    }

    /**
     * 根据百度页面html，获取百度图片
     * @param $name
     * @param $baiduHmtl
     * @return bool|null|string
     */
    private function _getBaiduJieShao($name,$baiduHmtl) {
        $baikeLink = $this->_getPregMatch("/href=\"(.*?)\"/si",$baiduHmtl);
        if (!empty($baikeLink[1])) {//百度百科链接，开始抓取图片
            $baikeHmtl = $this->_getCurlInfo($baikeLink[1],false,"http://www.baidu.com");
            if (strlen($baikeHmtl) < 300) {
                $baikeLink = $this->_getPregMatch("/URL=(.*?)'/si",$baikeHmtl);
                if (!empty($baikeLink[1])) {
                    $baikeLink[1] = "http://baike.baidu.com" . $baikeLink[1];
                    $baikeHmtl = $this->_getCurlInfo($baikeLink[1],false,"http://www.baidu.com");
                } else {
                    return array("","");
                }
            }

            $jieshaoInfo = $this->_getPregMatch("/<div class=\"card-summary-content\"><div class=\"para\">(.*?)<\/div>/si", $baikeHmtl);
            if (!empty($jieshaoInfo[1])) {//介绍去除html标签
                $jieshao = strip_tags($jieshaoInfo[1]);
                return array(preg_replace("/\[[0-9]+\]/","",$jieshao),$baikeLink[1]);
            }
        }
        return array("","");
    }

    //sogou搜索地址
    private $_sogouSearchUlr = "http://www.sogou.com/web?query={A}&ie=utf8&_asf=";

    /**
     * 搜狗抓取图片，百度封禁则切换为搜狗抓取
     * @param $name
     */
    private function _getJieShaoFromSogou($name) {
        $sogouSearchLink = $this->_sogouSearchUlr . microtime();
        $sogouSearchLink = str_replace("{A}",$name,$sogouSearchLink);//拼接搜狗搜索链接
        $sougouHmtl = $this->_getCurlInfo($sogouSearchLink);
        if (!empty($sougouHmtl)) {
            $urlInfo = $this->_getPregMatchAll("/href=\"http:\/\/baike\.baidu\.com(.*?)\"(.*?)<\/a>/si", $sougouHmtl, PREG_PATTERN_ORDER);
            if (!empty($urlInfo[0])) {
                foreach($urlInfo[0] as $urlVal) {
                    $info = strip_tags($urlVal);
                    if (strpos($info,"百度百科") !== false) {
                        return $this->_getBaiduJieShao($name,$info);
                    }
                }
            }
        }
        return array("","");
    }
}
$do = new FixPeopleInfo();
$do->run();