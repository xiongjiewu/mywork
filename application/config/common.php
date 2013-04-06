<?php
$config["image_upload_url"] = "/uploadimage";//图片上传回调URL
$config["image_upload_return"] = "/uploadfilereturn";//图片上传回调URL
$config['user_photo'] = "/images/re_su.png";
$config['img_base_url'] = "http://www.img.dianying8.tv";
$config['base_url'] = "http://www.dianying8.tv";
$config['base_name'] = "电影吧";

$config['max_post_time'] = 5;//允许5秒之内不能重复发表评论


$config['movieType'] = array(
    1 => "动作",
    2 => "爱情",
    3 => "科幻",
    4 => "魔幻",
    5 => "恐怖",
    6 => "其他"
);

$config['moviePlace'] = array(
    1 => "中国",
    2 => "日韩",
    3 => "欧美",
    4 => "其他"
);

$config['bofangqiType'] = array(
    1 => "快播",
    2 => "百度影音",
    3 => "迅雷",
    4 => "奇艺",
    5 => "优酷",
    6 => "土豆",
    7 => "其他",
);

$config['qingxiType'] = array(
    1 => "一般",
    2 => "标清",
    3 => "高清",
    4 => "超清",
);

$config['downLoadType'] = array(
    1 => "迅雷",
    2 => "快车",
    3 => "电驴",
    4 => "直接"
);

$config['movieSortType'] = array(
    1 => array(
        "title" => "全部默认排序",
        "sort" => "order by id asc",
    ),
    2 => array(
        "title" => "全部+时间远至近",
        "sort" => "order by time0 asc",
    ),
    3 => array(
        "title" => "全部+时间近至远",
        "sort" => "order by time1 desc",
    ),
    4 => array(
        "title" => "已上映+时间远至近",
        "sort" => "order by time1 asc",
    ),
    5 => array(
        "title" => "已上映+时间近至远",
        "sort" => "order by time1 desc",
    ),
    6 => array(
        "title" => "即将上映+时间远至近",
        "sort" => "order by time1 desc",
    ),
    7 => array(
        "title" => "即将上映+时间近至远",
        "sort" => "order by time1 asc",
    ),
);

$config['shoufeiType'] = array(
    1 => "免费",
    2 => "收费",
);

$config['base_uri'] = "";

$config['error_code'] = array(
    1 => array("content" => "您访问的内容已被删除或网页不存在","return_url" => "/"),
    2 => array("content" => "您需要登录才可以发表评论","return_url" => "/"),
    3 => array("content" => "网络连接失败，请重新操作！","return_url" => "/"),
    4 => array("content" => "您访问的页面已过期，请重新操作！","return_url" => "/"),
    5 => array("content" => "为了防止灌水，在". $config['max_post_time']."秒之内不能连续发表评论","return_url" => "/"),
);

$config['movie_type'] = array(
    array(
        "type" => "类型",
        "base_url" => get_url("/classicmovie/type/"),
        "info" => $config['movieType'],
    ),
    array(
        "type" => "年份",
        "base_url" => get_url("/classicmovie/year/"),
        "info" => array(2008 => 2008,2009 => 2009,2010 => 2010,2011 => 2011,2012 => 2012,2013 => 2013),
    ),
    array(
        "type" => "地区",
        "base_url" => get_url("/classicmovie/place/"),
        "info" => $config['moviePlace'],
    ),
);

$config['menus'] = array(
    array(
        "index" => "",
        "titlle" => "首&nbsp;&nbsp;&nbsp;页",
        "link" => "/",
        "class" => "",
    ),
    array(
        "index" => "",
        "titlle" => "最新上映",
        "link" => get_url("/latestmovie/"),
        "class" => "",
    ),
    array(
        "index" => "",
        "titlle" => "即将上映",
        "link" => get_url("/upcomingmovie/"),
        "class" => "",
    ),
    array(
        "index" => "list",
        "titlle" => "重温经典",
        "link" => get_url("/classicmovie/"),
        "class" => "dy_sort",
        "type_info" => $config['movie_type'],
    ),
    array(
        "index" => "",
        "titlle" => "反馈我想看",
        "link" => get_url("/usercenter/createfeedback/want/"),
        "class" => "",
    ),
);

$config['charset'] = "utf-8";

$config['cookie_domain'] = ".dianying8.tv";//cookie域名
$config['cookie_path'] = '/';//cookie路径
$config['resgiter_code_cookie_name'] = 'register_code_answer';

$config['web_name'] = "电影吧";

$config['AuthCookieName'] = "MyAuth_Dianying8Info";
$config['dianying8Secques']  = "dianying8@cookie.com";

$config['notice_max_count'] = 20;//订阅通知最大个数

$config['shoucang_max_count'] = 30;//最多收藏个数

$config['changepassword_max_time'] = 600;//允许修改密码页面过期失效时间

$config['last_movie_month'] = 6;//最新上映展示月个数


$config['post_show_count'] = 20;//评论显示个数