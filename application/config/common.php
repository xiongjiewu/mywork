<?php
$config['base_name'] = "好吧";
$config['base_title'] = "我们只专注于电影，旨在为您打造最快速、最方便的电影观看与下载通道";

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
    13 => "冒险",
    14 => "灾难",
    15 => "动画",
    16 => "警匪",
    17 => "武侠",
    18 => "家庭",
    19 => "悬疑",
    20 => "伦理",
    21 => "惊悚",
    22 => "歌舞",
    23 => "文艺",
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
    11 => "荷兰",
    12 => "瑞典",
    13 => "丹麦",
    14 => "比利时",
    15 => "波兰",
    16 => "澳大利亚",
    17 => "巴西",
    10 => "其他",
);
$config['movieNianFen'] = array(
    2004 => 2004,
    2005 => 2005,
    2006 => 2006,
    2007 => 2007,
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
    3 => "预告",
    4 => "特辑",
    5 => "记录片"
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
    8 => array("title" => "","content" => "很抱歉，您的帐号已被管理员封禁！","return_url" => "/"),
);

$config['movie_type'] = array(
    array(
        "type" => "类型",
        "base_url" => APF::get_instance()->get_real_url("moviceguide"),
        "info" => $config['movieType'],
    ),
    array(
        "type" => "年份",
        "base_url" => APF::get_instance()->get_real_url("moviceguide"),
        "info" => $config['movieNianFen'],
    ),
    array(
        "type" => "地区",
        "base_url" => APF::get_instance()->get_real_url("moviceguide"),
        "info" => $config['moviePlace'],
    ),
);
$nTime = time();
$time = strtotime(date("Y-m-01",$nTime));
$monthCount = 6;
$monthArr = array();
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
        "new" => false,
    ),
//    array(
//        "index" => "list",
//        "title" => "系列大片",
//        "link" => get_url("/series/"),
//        "class" => "dy_sort",
//        "type_info" => "",
//        "new" => true,
//    ),
//    array(
//        "index" => "list",
//        "title" => "本周专题",
//        "link" => get_url("/series/"),
//        "class" => "dy_sort",
//        "type_info" => "",
//        "new" => false,
//    ),
);
$config['right_menus'] = array(
    array(
        "index" => "movie",
        "title" => "电影库",
        "link" => "/moviceguide/",
        "class" => "",
    ),
    //第二版暂时把检索分割位人物检索+电影检索充内容
    array(
        "index" => "people",
        "title" => "电影检索",
        "link" => "/retrieval?b=d&s=A",
        "class" => "",
    ),
    array(
        "index" => "people",
        "title" => "人物检索",
        "link" => "/retrieval?b=p&s=A",
        "class" => "",
    ),
//    array(
//        "index" => "character",
//        "title" => "检索",
//        "link" => "/retrieval/",
//        "class" => "",
//    ),
//    array(
//        "index" => "prefecture",
//        "title" => "互动专区",
//        "link" => "/prefecture/",
//        "class" => "",
//    ),
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
$config['last_movie_month'] = 2;//最新上映展示月个数
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
        4 => array(//百度搜索排行榜，后面调整，经过协商，说这个比较重要调整致上面
            "title" => "百度搜索排行榜",
            "s_title" => "百度",
            "htmlTitle" => "百度<em>搜索排行榜</em>",
            "base_url" => "/classmovice/index/top/4/",
        ),
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
        3 => array(//时光网
            "title" => "时光网 TOP100",
            "s_title" => "时光网",
            "htmlTitle" => "时光网<em>TOP100</em>",
            "base_url" => "/classmovice/index/top/3/",
        ),
    ),
    "prize" => array(//奖项排行榜
    ),
);
//流量统计开关，本地不打开
$config['web_statistics'] = false;

//摇摇电影介绍长度
$config['yaoyao_jieshao_len'] = 60;
//摇摇电影主演展示个数
$config['yaoyao_zhuyan_count'] = 3;

//电影库tab信息
$config['movie_tab_info'] = array(
    "like" => array(
        "title" => "猜你喜欢",
        "active" => false,
        "sort" => "like",
        "desc" => "order by yaoyaonum desc",
    ),
    "new" => array(
        "title" => "最新更新",
        "active" => false,
        "sort" => "new",
        "desc" => "order by createtime desc",
    ),
    "hot" => array(
        "title" => "最近热播",
        "active" => false,
        "sort" => "hot",
        "desc" => "order by playnum desc",
    ),
    "good" => array(
        "title" => "最受好评",
        "active" => false,
        "sort" => "good",
        "desc" => "order by score desc",
    ),
    "search" => array(
        "title" => "热门搜索",
        "active" => false,
        "sort" => "search",
        "desc" => "order by searchnum desc"
    ),
    "show" => array(
        "title" => "最新上映",
        "active" => false,
        "sort" => "show",
        "desc" => "and time1 <= " . time() . " order by time1 desc"
    ),
    "comming" => array(
        "title" => "即将上映",
        "active" => false,
        "sort" => "comming",
        "desc" => "and time1 > " . time() . " order by time1 asc"
    ),
    "down" => array(
        "title" => "下载专区",
        "active" => false,
        "sort" => "down",
        "desc" => "and exist_down = 1 order by downnum desc",
    ),
);

//星座对应信息
$config['constellatoryInfo'] = array(
    1 => "白羊座",
    2 => "金牛座",
    3 => "双子座",
    4 => "巨蟹座",
    5 => "狮子座",
    6 => "处女座",
    7 => "天秤座",
    8 => "天蝎座",
    9 => "射手座",
    10 => "摩羯座",
    11 => "宝瓶座",
    12 => "双鱼座",
);

//26字母展示
$config['letterList'] = array(
    "A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q",
    "R","S","T","U","V","W","X","Y","Z","@"
);

//电影库导演配置信息，这一版先写在配置文件下一版改为人工通过数据库配置
$config['dianyingku_daoyan'] = array(
    "杜琪峰","周星驰","查罗登科","许鞍华","李安","张艺谋","冯小刚","陈凯歌","姜文","吴宇森"
);
//电影库演员配置信息，这一版先写在配置文件下一版改为人工通过数据库配置
$config['dianyingku_yanyuan'] = array(
    "杨幂","郭碧婷","林志颖","郭采洁","王珞丹","文章","吴尊","言承旭","阮经天","斯嘉丽·约翰森",
);