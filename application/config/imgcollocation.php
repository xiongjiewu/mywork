<?php
/**
 * 上传图片配置文件
 * added by xiongjiewu at 2013-08-17
 */
$config['dy'] = array(//电影图片配置
    "bucket" => "hao8static",//空间名称
    "user" => "xiongjiewu",//用户名
    "password" => "19920808wuxiongjie",//密码
    "root" => "/images/dy",//图片路径
    "size" => array(
        "100" => "!hao8img",
        "200" => "!hao8img200",
        "300" => "!hao8img300",
    ),
);
$config['user'] = array(//用户图片配置
    "bucket" => "hao8static",//空间名称
    "user" => "xiongjiewu",//用户名
    "password" => "19920808wuxiongjie",//密码
    "root" => "/images/user",//图片路径
    "size" => array(
        "100" => "!hao8img",
        "200" => "!hao8img200",
        "300" => "!hao8img300",
    ),
);
//图片类型
$config['img_type'] = array("image/gif","image/pjpeg","image/jpeg","image/png","image/jpg",'image/bmp','image/x-png');
//图片大小
$config['img_max_size'] = 10 * 1024 * 1024;//10M