<?php
$config['base_name'] = "好吧";
$config['base_title'] = "我们只专注于电影，您想看的就是我们宗旨";

$config["image_upload_url"] = "/uploadimage";//图片上传回调URL
$config["image_upload_return"] = "/uploadfilereturn";//图片上传回调URL
$config['user_photo'] = "/images/re_su.png";

$config['img_base_url'] = "http://www.img.local.dianying8.tv";
$config['base_url'] = "http://www.local.dianying8.tv";

$config['max_post_time'] = 5;//允许5秒之内不能重复发表评论

$config['movieType'] = array(
    1 => "动作",
    2 => "爱情",
    3 => "科幻",
    4 => "魔幻",
    5 => "恐怖",
    6 => "喜剧",
    7 => "犯罪",
    8 => "剧情",
    9 => "战争",
    11 => "惊悚",
    12 => "励志",
    10 => "其他",
);

$config['moviePlace'] = array(
    1 => "中国",
    3 => "美国",
    8 => "加拿大",
    4 => "日本",
    6 => "法国",
    5 => "俄罗斯",
    7 => "英国",
    2 => "韩国",
    9 => "意大利",
    10 => "其他",
);
$config['movieNianFen'] = array(
    2004=>2004,
    2005=>2005,
    2006=>2006,
    2007=>2007,
    2008 => 2008,
    2009 => 2009,
    2010 => 2010,
    2011 => 2011,
    2012 => 2012,
    2013 => 2013
);

$config['bofangqiType'] = array(
    1 => "快播",
    2 => "百度影音",
    3 => "迅雷",
    4 => "奇艺",
    5 => "优酷",
    6 => "土豆",
    7 => "搜狐",
    8 => "新浪",
    9 => "风行",
    10 => "pptv",
    11 => "pps",
    12 => "其他",
    13 => "乐视",
    14 => "电影网",
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
    1 => array("title" => "","content" => "您访问的内容已被删除或网页不存在","return_url" => "/"),
    2 => array("title" => "","content" => "您需要登录才可以发表评论","return_url" => "/"),
    3 => array("title" => "","content" => "网络连接失败，请重新操作！","return_url" => "/"),
    4 => array("title" => "","content" => "您访问的页面已过期，请重新操作！","return_url" => "/"),
    5 => array("title" => "","content" => "为了防止灌水，在". $config['max_post_time']."秒之内不能连续发表评论","return_url" => "/"),
    6 => array("title" => "感谢您的参与！","content" => "您已经填写过调查问卷，感谢您对我们工作的支持！～","return_url" => "/"),
    7 => array("title" => "感谢您的参与！","content" => "调查问卷成功提交，感谢您对我们工作的支持！～","return_url" => "/"),
);

$config['movie_type'] = array(
    array(
        "type" => "类型",
        "base_url" => get_url("/moviceguide/type/"),
        "info" => $config['movieType'],
    ),
    array(
        "type" => "年份",
        "base_url" => get_url("/moviceguide/year/"),
        "info" => $config['movieNianFen'],
    ),
    array(
        "type" => "地区",
        "base_url" => get_url("/moviceguide/place/"),
        "info" => $config['moviePlace'],
    ),
);
$nTime = time();
$time = strtotime(date("Y-m-01",$nTime));
$monthCount = 6;
$monthArr  =array();
for($i = 1;$i <= $monthCount;$i++) {
    $monthArr[date("Y年m月",$time)] = date("Ym",$time);
    $time = strtotime(date("Y-m-01",$time));
    $time = strtotime("-1 month",$time);
}
$config['menus'] = array(
    array(
        "index" => "index",
        "title" => "首&nbsp;&nbsp;&nbsp;页",
        "link" => "/",
        "class" => "",
    ),
    array(
        "index" => "movie_last",
        "title" => "最新上映",
        "link" => get_url("/latestmovie/"),
        "class" => "movie_last",
        "type_info" => $monthArr,
    ),
    array(
        "index" => "upcoming_movie",
        "title" => "即将上映",
        "link" => get_url("/upcomingmovie/"),
        "class" => "",
        "type_info" => "",
    ),
    array(
        "index" => "top_movie",
        "title" => "排行榜",
        "link" => get_url("/classmovice/"),
        "class" => "",
        "new" => true,
    ),
    array(
        "index" => "list",
        "title" => "电影导航",
        "link" => get_url("/moviceguide/"),
        "class" => "dy_sort",
        "type_info" => $config['movie_type'],
    ),
);
$config['cookie_domain'] = ".local.dianying8.tv";//cookie域名
$config['cookie_path'] = '/';//cookie路径
$config['resgiter_code_cookie_name'] = 'local_register_code_answer';
$config['web_name'] = "好吧";
$config['AuthCookieName'] = "local_MyAuth_Dianying8Info";
$config['dianying8Secques']  = "localdianying8@cookie.com";
$config['notice_max_count'] = 20;//订阅通知最大个数
$config['shoucang_max_count'] = 30;//最多收藏个数
$config['changepassword_max_time'] = 600;//允许修改密码页面过期失效时间
$config['last_movie_month'] = 6;//最新上映展示月个数
$config['post_show_count'] = 20;//评论显示个数
$config['email_login_url'] = array(//邮箱类型以及登录链接
    "qq" => "http://mail.qq.com/",
    "163" => "http://mail.163.com/",
    "126" => "http://mail.126.com/",
    "139" => "http://mail.10086.cn/",
    "sina" => "http://mail.sina.com.cn/",
    "sohu" => "http://mail.sohu.com/",
    "tom" => "http://web.mail.tom.com/webmail/login/index.action"
);

//搜索缓存key
$config['search_cache_key'] = "search_cache_key_count";
//浏览记录cookie名
$config['look_cookie_key'] = "user_look_cookie_val_info";
//排行榜tab配置信息
$config['pai_hang'] = array(
    "top" => array(//top排行榜
        1 => array(//豆瓣
            "title" => "豆瓣TOP250",
            "s_title" => "豆瓣",
            "htmlTitle" => "豆&nbsp;&nbsp;&nbsp;瓣<em>TOP250</em>",
            "base_url" => "/classmovice/index/top/1/",
        ),
        2 => array(//imdb
            "title" => "IMDB TOP250",
            "s_title" => "IMDB",
            "htmlTitle" => "IMDB<em>TOP250</em>",
            "base_url" => "/classmovice/index/top/2/",
        ),
    ),
    "prize" => array(//奖项排行榜
    ),
);