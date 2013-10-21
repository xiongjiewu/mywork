<?php
/**
 * 奥斯卡、金熊奖、金棕榈、金狮奖链接抓取配置文件
 */
//抓取网站信息配置文件
$config['zhuaqu_prize_id_file_path'] = "/home/www/logs/dianying/zhuaqu_prize.id";
//抓取错误log
$config['zhuaqu_prize_error_log'] = "/home/www/logs/dianying/zhuaqu_prize_error_url.id";
//抓取网站信息（url等）
$config['zhuaqu_web_info'] = array(
    "academyAward" => array(//奥斯卡
        "name" => "奥斯卡",
        "prizeType" => 1,//奖项类型
        "webType" => 1,//网站type,1=时光网
        "url" => array(//中文字母A人名
            array(
                "fenye" => true,
                "base_url" => "http://award.mtime.com/3/{A}/",
                "start" => 1929,
                "end" => date("Y"),//当前年份
            ),
        ),
    ),
);