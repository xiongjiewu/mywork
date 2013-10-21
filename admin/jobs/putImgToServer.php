<?php
/**
 * 将图片推送致图片服务器
 * added by xiongjiewu at 2013-08-17
 */
include("jobBase.php");
class PutImgToServer extends jobBase
{

    private $_imgDyPath;
    private $_imgUserPath;
    public function __construct()
    {
        parent::__construct();
        $this->_imgDyPath = rtrim(IMGPATH,"/") . "/images/dy/";
        $this->_imgUserPath = rtrim(IMGPATH,"/") . "/images/user/";
    }

    public function run() {
        //读取电影图片
        $dir_res = opendir($this->_imgDyPath);
        $cI = 1;
        while($filen = readdir($dir_res)) {
            if ($filen != "." && $filen != "..") {
                $newFilePath = "/images/dy/" . $filen;
                $upRes = $this->_downLoadImg($newFilePath,$this->_imgDyPath . $filen);
                if (empty($upRes)) {
                    var_dump("【电影】" . $upRes . "推送失败！！\n");
                } else {
                    var_dump("【电影】" . $upRes . "推送成功！！---" . $cI . "\n");
                }
                $cI++;
            }
        }

        //读取用户图片
        $dir_res = opendir($this->_imgUserPath);
        while($filen = readdir($dir_res)) {
            if ($filen != "." && $filen != "..") {
                $newFilePath = "/images/user/" . $filen;
                $upRes = $this->_downLoadImg($newFilePath,$this->_imgUserPath . $filen);
                if (empty($upRes)) {
                    var_dump("【用户】" . $upRes . "推送失败！！\n");
                } else {
                    var_dump("【用户】" . $upRes . "推送成功！！\n");
                }
            }
        }
        exit;
    }
}
$do = new PutImgToServer();
$do->run();