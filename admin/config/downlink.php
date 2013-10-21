<?php
/**
 * 下载链接抓取配置文件
 */
//抓取网站信息配置文件
$config['zhuaqu_movice_id_file_path'] = "/home/www/logs/dianying/zhuaqu_down_movie.id";
//抓取错误log
$config['zhuaqu_movie_error_log'] = "/home/www/logs/dianying/zhuaqu_movie_down_link_error_url.id";
//抓取网站信息（url等）
$config['zhuaqu_web_info'] = array(
    "tiantang" => array(//电影天堂
        "name" => "电影天堂",
        "type" => 1,//网站type
        "new" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.dytt8.net/html/gndy/dyzz/list_23_{A}.html",
                "start" => 1,
                "end" => 131,
            ),
        ),
        "rihan" => array(//日韩
            array(
                "fenye" => true,
                "base_url" => "http://www.dytt8.net/html/gndy/rihan/list_6_{A}.html",
                "start" => 1,
                "end" => 18,
            ),
        ),
        "oumei" => array(//欧美
            array(
                "fenye" => true,
                "base_url" => "http://www.dytt8.net/html/gndy/oumei/list_7_{A}.html",
                "start" => 1,
                "end" => 152,
            ),
        ),
        "guonei" => array(//国内
            array(
                "fenye" => true,
                "base_url" => "http://www.dytt8.net/html/gndy/china/list_4_{A}.html",
                "start" => 1,
                "end" => 59,
            ),
        ),
        "zonghe" => array(//综合
            array(
                "fenye" => true,
                "base_url" => "http://www.dytt8.net/html/gndy/jddy/list_63_{A}.html",
                "start" => 1,
                "end" => 118,
            ),
        ),
    ),
    "piaohua" => array(//飘花
        "name" => "飘花",
        "type" => 2,//网站type
        "new" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.piaohua.com/list/1_{A}.htm",
                "start" => 2,
                "end" => 735,
            ),
        ),
        "hot" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.piaohua.com/list/1_{A}.htm",
                "start" => 1,
                "end" => 1,
            ),
        ),
    ),
    "piaohua2" => array(//飘花
        "name" => "飘花",
        "type" => 2,//网站type
        "dongzuo" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.piaohua.com/html/dongzuo/list_{A}.html",
                "start" => 1,
                "end" => 340,
            ),
        ),
        "xiju" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.piaohua.com/html/xiju/list_{A}.html",
                "start" => 1,
                "end" => 237,
            ),
        ),
        "aiqing" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.piaohua.com/html/aiqing/list_{A}.html",
                "start" => 1,
                "end" => 141,
            ),
        ),
        "kehuan" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.piaohua.com/html/kehuan/list_{A}.html",
                "start" => 1,
                "end" => 84,
            ),
        ),
        "juqing" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.piaohua.com/html/juqing/list_{A}.html",
                "start" => 1,
                "end" => 273,
            ),
        ),
        "xuannian" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.piaohua.com/html/xuannian/list_{A}.html",
                "start" => 1,
                "end" => 25,
            ),
        ),
        "wenyi" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.piaohua.com/html/wenyi/list_{A}.html",
                "start" => 1,
                "end" => 28,
            ),
        ),
        "zhanzheng" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.piaohua.com/html/zhanzheng/list_{A}.html",
                "start" => 1,
                "end" => 28,
            ),
        ),
        "kongbu" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.piaohua.com/html/kongbu/list_{A}.html",
                "start" => 1,
                "end" => 162,
            ),
        ),
        "zainan" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.piaohua.com/html/zainan/list_{A}.html",
                "start" => 1,
                "end" => 5,
            ),
        ),
    ),
    "xunleicang" => array(//迅雷仓
        "name" => "迅雷仓",
        "type" => 3,//网站type
        "dongzuo" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.xunleicang.com/vod-show-id-8-p-{A}.html",
                "start" => 1,
                "end" => 127,
            ),
        ),
        "xiju" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.xunleicang.com/vod-show-id-9-p-{A}.html",
                "start" => 1,
                "end" => 123,
            ),
        ),
        "aiqing" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.xunleicang.com/vod-show-id-10-p-{A}.html",
                "start" => 1,
                "end" => 53,
            ),
        ),
        "kehuan" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.xunleicang.com/vod-show-id-11-p-{A}.html",
                "start" => 1,
                "end" => 43,
            ),
        ),
        "kongbu" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.xunleicang.com/vod-show-id-12-p-{A}.html",
                "start" => 1,
                "end" => 65,
            ),
        ),
        "juqing" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.xunleicang.com/vod-show-id-13-p-{A}.html",
                "start" => 1,
                "end" => 151,
            ),
        ),
        "zhanzheng" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.xunleicang.com/vod-show-id-14-p-{A}.html",
                "start" => 1,
                "end" => 17,
            ),
        ),
        "qita" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.xunleicang.com/vod-show-id-7-p-{A}.html",
                "start" => 1,
                "end" => 25,
            ),
        ),
    ),
    "2tu" => array(//迅播
        "name" => "迅播",
        "type" => 4,//网站type
        "new" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.2tu.cc/GvodHtml/15_{A}.html",
                "start" => 2,
                "end" => 494,
            ),
        ),
        "new2" => array(
            "http://www.2tu.cc/GvodHtml/15.html"
        ),
    ),
    "bitfish8" => array(//比特鱼
        "name" => "比特鱼",
        "type" => 5,//网站type
        "dongzuo" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.bitfish8.com/btmovieseed/dongzuo/list_21_{A}.html",
                "start" => 1,
                "end" => 36,
            ),
        ),
        "lizhi" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.bitfish8.com/btmovieseed/lizhi/list_20_{A}.html",
                "start" => 1,
                "end" => 3,
            ),
        ),
        "zhanzheng" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.bitfish8.com/btmovieseed/zhanzheng/list_28_{A}.htmll",
                "start" => 1,
                "end" => 7,
            ),
        ),
        "xuanyi" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.bitfish8.com/btmovieseed/xuanyi/list_27_{A}.html",
                "start" => 1,
                "end" => 11,
            ),
        ),
        "kongbu" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.bitfish8.com/btmovieseed/kongbu/list_24_{A}.html",
                "start" => 1,
                "end" => 17,
            ),
        ),
        "lunli" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.bitfish8.com/btmovieseed/lunli/list_25_{A}.html",
                "start" => 1,
                "end" => 17,
            ),
        ),
        "aiqing" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.bitfish8.com/btmovieseed/aiqing/list_19_{A}.html",
                "start" => 1,
                "end" => 30,
            ),
        ),
        "kehuan" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.bitfish8.com/btmovieseed/kehuan/list_23_{A}.html",
                "start" => 1,
                "end" => 16,
            ),
        ),
        "xijv" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.bitfish8.com/btmovieseed/xijv/list_26_{A}.html",
                "start" => 1,
                "end" => 36,
            ),
        ),
        "jingsong" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.bitfish8.com/btmovieseed/jingsong/list_52_{A}.html",
                "start" => 1,
                "end" => 14,
            ),
        ),
        "fanzui" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.bitfish8.com/btmovieseed/fanzui/list_53_{A}.html",
                "start" => 1,
                "end" => 12,
            ),
        ),
        "jilu" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.bitfish8.com/btmovieseed/jilu/list_22_{A}.html",
                "start" => 1,
                "end" => 10,
            ),
        ),
    ),
);