<?php
/**
 * 给图片添加水印
 * added by xiongjiewu at 2013-05-22
 */
$do = new AddWaterToImg();
$do->run();

class AddWaterToImg extends myPdo
{
    private $_imgPath;//需要加水印的图片文件夹路径
    protected $_waterString;//水印文字
    protected $_waterImg;//水印图片

    function __construct()
    {
        $this->_imgPath = IMGPATH . "images/test/";
        $this->_waterString = $this->get_config_value("dy_water_text");
        $this->_waterImg = IMGPATH . $this->get_config_value("dy_water_img");
    }

    public function run()
    {
        //读取图片
        $dir_res = opendir($this->_imgPath);
        while($filen = readdir($dir_res)) {
            if ($filen != "." && $filen != "..") {
                $this->addWaterDo(1,$this->_imgPath . $filen);
            }
        }
        exit;
    }
    /** 给图片添加水印
     * @param $watertype 水印类型，1=文字，2=图片
     * @param $img
     */
    public function addWaterDo($watertype,$img)
    {
        $image_size = getimagesize($img);
        $iinfo = getimagesize($img);
        $nimage = imagecreatetruecolor($image_size[0], $image_size[1]);
        $white = imagecolorallocate($nimage, 255, 255, 255);
        $black = imagecolorallocate($nimage, 0, 0, 0);
        $red = imagecolorallocate($nimage, 255, 0, 0);
        imagefill($nimage, 0, 0, $white);
        switch ($iinfo[2]) {
            case 1:
                $simage = imagecreatefromgif($img);
                break;
            case 2:
                $simage = imagecreatefromjpeg($img);
                break;
            case 3:
                $simage = imagecreatefrompng($img);
                break;
            case 6:
                $simage = imagecreatefromwbmp($img);
                break;
            default:
                return false;
        }

        imagecopy($nimage, $simage, 0, 0, 0, 0, $image_size[0], $image_size[1]);
        //水印加背景图片
        //imagefilledrectangle($nimage, 1, $image_size[1] - 15, 80, $image_size[1], $white);

        switch ($watertype) {
            case 1: //加水印字符串
                $rN = rand(1,2);
                if ($rN == 1) {
                    imagestring($nimage, 3, 2, 0, $this->_waterString, $black);
                } else {
                    imagestring($nimage, 3, 2, $image_size[1] - 15, $this->_waterString, $black);
                }
                break;
            case 2: //加水印图片
                $simage1 = $this->_createWaterImg($this->_waterImg);
                imagecopy($nimage, $simage1, 0, 0, 0, 0, 200, 15);
                imagedestroy($simage1);
                break;
        }

        switch ($iinfo[2]) {
            case 1:
                imagejpeg($nimage, $img);
                break;
            case 2:
                imagejpeg($nimage, $img);
                break;
            case 3:
                imagepng($nimage, $img);
                break;
            case 6:
                imagewbmp($nimage, $img);
                break;
        }

        //覆盖原上传文件
        imagedestroy($nimage);
        imagedestroy($simage);
    }

    private function _createWaterImg() {
        $imgArr = explode(".",$this->_waterImg);
        switch(strtolower($imgArr[count($imgArr) - 1])) {
            case "gif" :
                $simage1 = imagecreatefromgif($this->_waterImg);
                break;
            case "png" :
                $simage1 = imagecreatefrompng($this->_waterImg);
                break;
            case "jpeg" :
                $simage1 = imagecreatefromjpeg($this->_waterImg);
                break;
            default :
                die("水印图片类型不符合！");
        }
        return $simage1;
    }
}