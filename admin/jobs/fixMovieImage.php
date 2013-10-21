<?php
/**
 * 电影图片抓取
 * 修复一些没有图片或者图片是我们网站默认图片的电影
 * added by xiongjiewu at 2013-07-07
 */
include("jobBase.php");
class FixMovieImage extends jobBase
{
    private $_idFile = "/home/www/logs/dianying/zhuaqu_image.id";
    
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
            $movieInfo = $this->_getMoviceInfo($this->_id,$this->_limit,"all");
            if (empty($movieInfo)) {//没有要处理的信息
                var_dump($i);
                file_put_contents($this->_idFile,0);
                exit;
            }
            foreach($movieInfo as $movieVal) {
                $this->_id = $movieVal['id'];
                file_put_contents($this->_idFile,$movieVal['id']);
                //检查图片是否需要处理
                if ($this->_checkImage($movieVal['image'])) {
                    $img = $this->_getImage($movieVal['name']);
                    if (empty($img) || ($img == $movieVal['image'])) {//百度抓取不成功，使用搜狗抓取
                        $img = $this->_getImageFromSogou($movieVal['name']);
                    }
                    if (!empty($img) && ($img != $movieVal['image'])) {//图片抓取成功，更新电影图片信息
                        $upRes = $this->_updateInfo(array("id" => $movieVal['id']),array("image" => $img),"tbl_detailInfo");
                        if (!empty($upRes)) {
                            var_dump("电影[{$movieVal['id']}]--[{$movieVal['name']}]图片处理成功!\n");
                        } else {
                            var_dump("电影[{$movieVal['id']}]--[{$movieVal['name']}]图片处理失败!\n");
                        }
                    } else {
                        $i++;
                        var_dump("电影[{$movieVal['id']}]--[{$movieVal['name']}]没有匹配到任何图片!\n");
                    }
                }
            }
        }
    }

    /**
     * 检查图片是否需要处理
     * @param $image
     * @return bool
     */
    private function _checkImage($image) {
        if ($image == "/images/dy_common.jpg") {//=默认图片需要处理
            return true;
        } elseif (!file_exists(rtrim(IMGPATH, "/") . $image)) {//图片地址不存在需要处理
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
    private function _getImage($name) {
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
        $img = "";
        if (!empty($imageInfo[2]) && strpos($imageInfo[2],"_百度百科") !== false) {
            $baikeLink = $this->_getPregMatch("/href=\"(.*?)\"/si",$imageInfo[2]);
            if (!empty($baikeLink[1])) {
                //获取百科详细url
                $baiduHmtl = $this->_getCurlInfo($baikeLink[1],false,"http://www.baidu.com");
                if (strpos($baiduHmtl,"The document has moved") !== false) {
                    $img = $this->_getBaiduImg($name,$baiduHmtl);
                }
            }
        }
        if (empty($img)) {//百度抓取不成功，抓取时光网的
            $mtimeUrlInfo = $this->_getPregMatchAll("/http:\/\/movie\.mtime\.com\/[0-9]+(.*?)/si", $baiduTotalHmtl, PREG_PATTERN_ORDER);
            if (!empty($mtimeUrlInfo[0][0])) {
                $idArr = $this->_getPregMatch("/[0-9]+/si", $mtimeUrlInfo[0][0]);
                $mtimeImageUrl = str_replace("{A}",$idArr[0],$this->_mtimeImg);
                $imgUrl = $mtimeImageUrl;
                $imgInfo = explode(".", $imgUrl);
                $imagesName = md5($name . "_" . time()) . "." . $imgInfo[count($imgInfo) - 1];
                $img = $this->_downLoadImg($imagesName, $imgUrl); //下载图片并保存,并返回图片地址
            }
        }
        return $img;
    }

    /**
     * 根据百度页面html，获取百度图片
     * @param $name
     * @param $baiduHmtl
     * @return bool|null|string
     */
    private function _getBaiduImg($name,$baiduHmtl) {
        $baikeLink = $this->_getPregMatch("/href=\"(.*?)\"/si",$baiduHmtl);
        if (!empty($baikeLink[1])) {//百度百科链接，开始抓取图片
            $baikeHmtl = $this->_getCurlInfo($baikeLink[1],false,"http://www.baidu.com");
            if (strlen($baikeHmtl) < 300) {
                $baikeLink = $this->_getPregMatch("/URL=(.*?)'/si",$baikeHmtl);
                if (!empty($baikeLink[1])) {
                    $baikeHmtl = $this->_getCurlInfo("http://baike.baidu.com" . $baikeLink[1],false,"http://www.baidu.com");
                } else {
                    return "";
                }
            }
            $imageUrlInfo = $this->_getPregMatchAll("/http:\/\/[a-z0-9A-Z]+\.hiphotos\.baidu\.com\/baike\/(.*?)\/sign=[a-z0-9A-Z]+\/[a-z0-9A-Z]+\.jpg/i", $baikeHmtl, PREG_PATTERN_ORDER);
            if (!empty($imageUrlInfo[0][0])) {//拿匹配出来的第一张图片
                $imgUrl = $imageUrlInfo[0][0];
                $imgInfo = explode(".", $imgUrl);
                $imagesName = md5($name . "_" . time()) . "." . $imgInfo[count($imgInfo) - 1];
                return $this->_downLoadImg($imagesName, $imgUrl); //下载图片并保存,并返回图片地址
            }
        }
    }

    //sogou搜索地址
    private $_sogouSearchUlr = "http://www.sogou.com/web?query={A}&ie=utf8&_asf=";

    //mtime图片地址
    private $_mtimeImg = "http://img31.mtime.cn/mt/1100/{A}/{A}_300X500.jpg";

    /**
     * 搜狗抓取图片，百度封禁则切换为搜狗抓取
     * @param $name
     */
    private function _getImageFromSogou($name) {
        $sogouSearchLink = $this->_sogouSearchUlr . microtime();
        $sogouSearchLink = str_replace("{A}",$name,$sogouSearchLink);//拼接搜狗搜索链接
        $sougouHmtl = $this->_getCurlInfo($sogouSearchLink);
        if (!empty($sougouHmtl)) {
            $urlInfo = $this->_getPregMatchAll("/href=\"http:\/\/baike\.baidu\.com(.*?)\"(.*?)<\/a>/si", $sougouHmtl, PREG_PATTERN_ORDER);
            $img = "";
            if (!empty($urlInfo[0])) {
                foreach($urlInfo[0] as $urlVal) {
                    $info = strip_tags($urlVal);
                    if (strpos($info,"百度百科") !== false) {
                        $img =  $this->_getBaiduImg($name,$info);
                    }
                }
            }
            if (empty($img)) {//百度抓取不成功，抓取时光网的
                $mtimeUrlInfo = $this->_getPregMatchAll("/http:\/\/movie\.mtime\.com\/[0-9]+(.*?)/si", $sougouHmtl, PREG_PATTERN_ORDER);
                if (!empty($mtimeUrlInfo[0][0])) {
                    $idArr = $this->_getPregMatch("/[0-9]+/si", $mtimeUrlInfo[0][0]);
                    $mtimeImageUrl = str_replace("{A}",$idArr[0],$this->_mtimeImg);
                    $imgUrl = $mtimeImageUrl;
                    $imgInfo = explode(".", $imgUrl);
                    $imagesName = md5($name . "_" . time()) . "." . $imgInfo[count($imgInfo) - 1];
                    $img = $this->_downLoadImg($imagesName, $imgUrl); //下载图片并保存,并返回图片地址
                }
            }
            return $img;
        }
        return "";
    }
}
$do = new FixMovieImage();
$do->run();