<?php
/**
 * 压缩图片处理
 * @ClassName: Resizeimage
 * @Description:
 * @author HanHor,2012-8-1 下午1:02:11
 * @website http://blog.hanhor.com
 * @email hanhor.wu#gmail.com
 */
class Resizeimage extends CI_Model
{
    /**
     * resize image
     * @Function: resizeImage
     * @Description:
     * @author xiongjiewu,2013-05-01 下午11:10:31
     * @param string $im
     * @param int $maxwidth
     * @param int $maxheight
     * @param string $path
     * @param string $name
     * @param string $filetype
     * @return string
     */
    function __construct()
    {

    }
    public function resizeImageDo($im, $maxwidth, $maxheight, $path, $name, $filetype)
    {
        $pic_width = imagesx($im);
        $pic_height = imagesy($im);

        if (($maxwidth && $pic_width > $maxwidth) || ($maxheight && $pic_height > $maxheight)) {
            $resizewidth_tag = $resizeheight_tag = false;
            if ($maxwidth && $pic_width > $maxwidth) {
                $widthratio = $maxwidth / $pic_width;
                $resizewidth_tag = true;
            }

            if ($maxheight && $pic_height > $maxheight) {
                $heightratio = $maxheight / $pic_height;
                $resizeheight_tag = true;
            }

            if ($resizewidth_tag && $resizeheight_tag) {
                if ($widthratio < $heightratio) {
                    $ratio = $widthratio;
                } else {
                    $ratio = $heightratio;
                }
            }

            if ($resizewidth_tag && !$resizeheight_tag)
                $ratio = $widthratio;
            if ($resizeheight_tag && !$resizewidth_tag)
                $ratio = $heightratio;

            $newwidth = $pic_width * $ratio;
            $newheight = $pic_height * $ratio;

            if (function_exists("imagecopyresampled")) {
                $newim = imagecreatetruecolor($newwidth, $newheight);
                imagecopyresampled($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $pic_width, $pic_height);
            } else {
                $newim = imagecreate($newwidth, $newheight);
                imagecopyresized($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $pic_width, $pic_height);
            }
            $file_name = $name . $filetype;
            $name = $path . $file_name;
            imagejpeg($newim, $name, 85);
            imagedestroy($newim);
            return $name;
        } else {
            $name = $path . $name . $filetype;
            imagejpeg($im, $name);
            return $name;
        }
    }
}