<?php
/**
 * 人物链接抓取配置文件
 */
//抓取网站信息配置文件
$config['zhuaqu_people_id_file_path'] = "/home/www/logs/dianying/zhuaqu_people.id";
//抓取错误log
$config['zhuaqu_people_error_log'] = "/home/www/logs/dianying/zhuaqu_people_error_url.id";
//抓取网站信息（url等）
$config['zhuaqu_web_info'] = array(
    "mtime" => array(//时光网
        "name" => "时光网",
        "type" => 1,//网站type
        "url_1" => array(
            array(
                "fenye" => true,
                "base_url" => "http://people.mtime.com/{A}/",
                "start" => 892742,
                "end" => 2200000,
            ),
        ),
    ),
);