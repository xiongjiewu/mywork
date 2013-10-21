<?php
/**
 * 观看链接抓取配置文件
 */
//抓取网站信息配置文件
$config['zhuaqu_movice_id_file_path'] = "/home/www/logs/dianying/zhuaqu_movice.id";
//抓取错误log
$config['zhuaqu_movie_error_log'] = "/home/www/logs/dianying/zhuaqu_movice_error_url.id";
//抓取网站信息（url等）
$config['zhuaqu_web_info'] = array(
    "douban" => array(//豆瓣网
        "name" => "豆瓣网",
        "open" => "yes",//是否打开
        "type" => 1,//网站type
        "comming" => array(
            "http://movie.douban.com/coming"
        ),
        "later" => array(
            "http://movie.douban.com/nowplaying/beijing/",
            "http://movie.douban.com/nowplaying/shanghai/"
        ),
        "top" => array(//top（经典电影）
            "http://movie.douban.com/top250?start=0&filter=&format=",
            "http://movie.douban.com/top250?start=25&filter=&format=",
            "http://movie.douban.com/top250?start=50&filter=&format=",
            "http://movie.douban.com/top250?start=75&filter=&format=",
            "http://movie.douban.com/top250?start=100&filter=&format=",
            "http://movie.douban.com/top250?start=125&filter=&format=",
            "http://movie.douban.com/top250?start=150&filter=&format=",
            "http://movie.douban.com/top250?start=175&filter=&format=",
            "http://movie.douban.com/top250?start=200&filter=&format=",
            "http://movie.douban.com/top250?start=225&filter=&format=",
        ),
        "piaofang" => array(//豆瓣票房榜
            "http://movie.douban.com/doulist/1389671/",
            "http://movie.douban.com/doulist/1389671/?start=25&filter=",
            "http://movie.douban.com/doulist/1389671/?start=50&filter=",
        ),
    ),
    "mtime" => array(//时光网
        "name" => "时光网",
        "open" => "yes",//是否打开
        "type" => 2,
        "comming" => array(//即将上映电影url
            "http://movie.mtime.com/recent/",//中国
            "http://movie.mtime.com/recent/USA/index.html",//美国
            "http://movie.mtime.com/recent/Japan/index.html",//日本
            "http://movie.mtime.com/recent/France/index.html",//法国
            "http://movie.mtime.com/recent/UK/index.html",//英国
            "http://movie.mtime.com/recent/Russia/index.html",//俄罗斯
            "http://movie.mtime.com/recent/Canada/index.html",//加拿大
            "http://movie.mtime.com/recent/Italy/index.html",//意大利
            "http://movie.mtime.com/recent/South_Korea/index.html",//韩国
        ),
        "top" => array(//top（经典电影）
            "http://www.mtime.com/top/movie/top100/",
            "http://www.mtime.com/top/movie/top100/index-2.html",
            "http://www.mtime.com/top/movie/top100/index-3.html",
            "http://www.mtime.com/top/movie/top100/index-4.html",
            "http://www.mtime.com/top/movie/top100/index-5.html",
            "http://www.mtime.com/top/movie/top100/index-6.html",
            "http://www.mtime.com/top/movie/top100/index-7.html",
            "http://www.mtime.com/top/movie/top100/index-8.html",
            "http://www.mtime.com/top/movie/top100/index-9.html",
            "http://www.mtime.com/top/movie/top100/index-10.html",
        ),
        "benzhoupiaofang" => array(//本周票房电影
            "http://movie.mtime.com/boxoffice/#CN",//中国
            "http://movie.mtime.com/boxoffice/#US",//北美
            "http://movie.mtime.com/boxoffice/#KR",//韩国
            "http://movie.mtime.com/boxoffice/#JP",//日本
        ),
    ),
    "sohu" => array(//时光网
        "name" => "搜狐",
        "open" => "yes",//是否打开
        "type" => 3,
        "comming" => array(//即将上映电影url
            "http://movie.mtime.com/recent/"
        ),
        "new" => array(
            array(
                "fenye" => true,
                "base_url" => "http://so.tv.sohu.com/list_p11_p2_p3_p4-1_p5_p6_p73_p82_p9-1_p10{A}_p11.html",
                "start" => 1,
                "end" => 127,
            ),
        ),
        "hot" => array(
            array(
                "fenye" => true,
                "base_url" => "http://so.tv.sohu.com/list_p1100_p20_p3_p40_p5_p6_p75_p82_p9_2d1_p10{A}_p110.html",
                "start" => 1,
                "end" => 127,
            ),
        ),
    ),
    "2tu" => array(//迅播网
        "name" => "迅播",
        "open" => "yes",//是否打开
        "type" => 4,
        "new" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.2tu.cc/GvodHtml/15_{A}.html",
                "start" => 2,
                "end" => 494,
            ),
        ),
        "hot" => array(
            "http://www.2tu.cc/GvodHtml/15.html",
        ),
    ),
    "tudou" => array(//土豆网
        "name" => "土豆",
        "open" => "yes",//是否打开
        "type" => 5,
        "new" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.tudou.com/cate/ach22a-2b-2c-2d-2e-2f-2g-2h-2i-2j-2k-2l-2m-2n-2o-2so2pe-2pa{A}.html",
                "start" => 1,
                "end" => 30,
            ),
        ),
        "hot" => array(//人气最旺
            array(
                "fenye" => true,
                "base_url" => "http://www.tudou.com/cate/chach22a-2b-2c-2d-2e-2f-2g-2h-2i-2j-2k-2l-2m-2n-2o-2so1pe3pa{A}.html",
                "start" => 1,
                "end" => 30,
            ),
        ),
        "many" => array(//评论最多
            array(
                "fenye" => true,
                "base_url" => "http://www.tudou.com/cate/chach22a-2b-2c-2d-2e-2f-2g-2h-2i-2j-2k-2l-2m-2n-2o-2so3pe3pa{A}.html",
                "start" => 1,
                "end" => 30,
            ),
        ),
        "get" => array(//挖的最多
            array(
                "fenye" => true,
                "base_url" => "http://www.tudou.com/cate/chach22a-2b-2c-2d-2e-2f-2g-2h-2i-2j-2k-2l-2m-2n-2o-2so5pe3pa{A}.html",
                "start" => 1,
                "end" => 30,
            ),
        ),
        "dalu" => array(//大陆
            array(
                "fenye" => true,
                "base_url" => "http://www.tudou.com/cate/chach22a1000002b-2c-2d-2e-2f-2g-2h-2i-2j-2k-2l-2m-2n-2o-2so5pe3pa{A}.html",
                "start" => 1,
                "end" => 30,
            ),
        ),
        "xianggang" => array(//香港最多
            array(
                "fenye" => true,
                "base_url" => "http://www.tudou.com/cate/chach22a1000004b-2c-2d-2e-2f-2g-2h-2i-2j-2k-2l-2m-2n-2o-2so5pe3pa{A}.html",
                "start" => 1,
                "end" => 30,
            ),
        ),
        "hanguo" => array(//韩国
            array(
                "fenye" => true,
                "base_url" => "http://www.tudou.com/cate/chach22a1000005b-2c-2d-2e-2f-2g-2h-2i-2j-2k-2l-2m-2n-2o-2so5pe3pa{A}.html",
                "start" => 1,
                "end" => 29,
            ),
        ),
        "meiguo" => array(//美国
            array(
                "fenye" => true,
                "base_url" => "http://www.tudou.com/cate/chach22a1000007b-2c-2d-2e-2f-2g-2h-2i-2j-2k-2l-2m-2n-2o-2so5pe3pa{A}.html",
                "start" => 1,
                "end" => 30,
            ),
        ),
        "faguo" => array(//法国
            array(
                "fenye" => true,
                "base_url" => "http://www.tudou.com/cate/chach22a1000008b-2c-2d-2e-2f-2g-2h-2i-2j-2k-2l-2m-2n-2o-2so5pe3pa{A}.html",
                "start" => 1,
                "end" => 23,
            ),
        ),
        "yidali" => array(//意大利
            array(
                "fenye" => true,
                "base_url" => "http://www.tudou.com/cate/chach22a1000009b-2c-2d-2e-2f-2g-2h-2i-2j-2k-2l-2m-2n-2o-2so5pe3pa{A}.html",
                "start" => 1,
                "end" => 6,
            ),
        ),
        "yingguo" => array(//英国
            array(
                "fenye" => true,
                "base_url" => "http://www.tudou.com/cate/chach22a1000010b-2c-2d-2e-2f-2g-2h-2i-2j-2k-2l-2m-2n-2o-2so5pe3pa{A}.html",
                "start" => 1,
                "end" => 17,
            ),
        ),
        "taiwan" => array(//台湾
            array(
                "fenye" => true,
                "base_url" => "http://www.tudou.com/cate/chach22a1000003b-2c-2d-2e-2f-2g-2h-2i-2j-2k-2l-2m-2n-2o-2so5pe3pa{A}.html",
                "start" => 1,
                "end" => 11,
            ),
        ),
        "taiguo" => array(//泰国
            array(
                "fenye" => true,
                "base_url" => "http://www.tudou.com/cate/chach22a1000000b-2c-2d-2e-2f-2g-2h-2i-2j-2k-2l-2m-2n-2o-2so5pe3pa{A}.html",
                "start" => 1,
                "end" => 6,
            ),
        ),
        "jianada" => array(//加拿大
            array(
                "fenye" => true,
                "base_url" => "http://www.tudou.com/cate/chach22a1000014b-2c-2d-2e-2f-2g-2h-2i-2j-2k-2l-2m-2n-2o-2so5pe3pa{A}.html",
                "start" => 1,
                "end" => 6,
            ),
        ),
        "deguo" => array(//德国
            array(
                "fenye" => true,
                "base_url" => "http://www.tudou.com/cate/chach22a1000011b-2c-2d-2e-2f-2g-2h-2i-2j-2k-2l-2m-2n-2o-2so5pe3pa{A}.html",
                "start" => 1,
                "end" => 10,
            ),
        ),
        "eluosi" => array(//俄罗斯
            array(
                "fenye" => true,
                "base_url" => "http://www.tudou.com/cate/chach22a1000013b-2c-2d-2e-2f-2g-2h-2i-2j-2k-2l-2m-2n-2o-2so5pe3pa{A}.html",
                "start" => 1,
                "end" => 4,
            ),
        ),
        "yindu" => array(//印度
            array(
                "fenye" => true,
                "base_url" => "http://www.tudou.com/cate/chach22a1000012b-2c-2d-2e-2f-2g-2h-2i-2j-2k-2l-2m-2n-2o-2so5pe3pa{A}.html",
                "start" => 1,
                "end" => 5,
            ),
        ),
        "qita" => array(//其他
            array(
                "fenye" => true,
                "base_url" => "http://www.tudou.com/cate/chach22a999000b-2c-2d-2e-2f-2g-2h-2i-2j-2k-2l-2m-2n-2o-2so5pe3pa{A}.html",
                "start" => 1,
                "end" => 30,
            ),
        ),
    ),
    "letv" => array(//乐视网
        "name" => "乐视",
        "open" => "yes",//是否打开
        "type" => 6,
        "new" => array(
            array(
                "fenye" => true,
                "base_url" => "http://so.letv.com/list/c1_t-1_a-1_y-1_f-1_at1_o1_i-1_p{A}.html",
                "start" => 1,
                "end" => 200,
            ),
        ),
    ),
    "funshion" => array(//风行网
        "name" => "风行",
        "open" => "yes",//是否打开
        "type" => 7,
        "new" => array(
            array(
                "fenye" => true,
                "base_url" => "http://www.funshion.com/list/movie/o-mo.pg-{A}.pt-vp.ta-nea",
                "start" => 1,
                "end" => 187,
            ),
        ),
    ),
    "kankan" => array(//迅雷看看网
        "name" => "迅雷看看",
        "open" => "yes",//是否打开
        "type" => 8,
        "new" => array(
            array(
                "fenye" => true,
                "base_url" => "http://movie.kankan.com/type,order/movie,update/page{A}/",
                "start" => 1,
                "end" => 62,
            ),
        ),
    ),
    "youku" => array(//优酷网
        "name" => "优酷",
        "open" => "yes",//是否打开
        "type" => 9,
        "new" => array(
            array(
                "fenye" => true,
                "base_url" => "http://movie.youku.com/search/index2/_page63561_{A}_cmodid_63561",
                "start" => 1,
                "end" => 34,
            ),
        ),
        "dalu" => array(
            array(
                "fenye" => true,
                "base_url" => "http://movie.youku.com/search/index2/_page63561_{A}_cmodid_63561?cc-showdivid=no&srcmid=63560&srcidx=1&linkage=%7B%22show_catalogs_q_63560.area%22%3A+%22area%3A%E5%A4%A7%E9%99%86%22%2C+%22show_catalogs_q_63560.genre%22%3A+%22%22%2C+%22show_catalogs_q_63560.releaseyear%22%3A+%22%22%2C+%22show_catalogs_q_63560.orderby%22%3A+%227%22%2C+%22show_catalogs_fd_63560%22%3A+%22%22%7D&cmodid=63561&__rt=1&__ro=m13382821540",
                "start" => 1,
                "end" => 34,
            ),
        ),
        "xianggang" => array(
            array(
                "fenye" => true,
                "base_url" => "http://movie.youku.com/search/index2/_page63561_{A}_cmodid_63561?cc-showdivid=no&srcmid=63560&srcidx=1&linkage={%22show_catalogs_q_63560.area%22%3A+%22area%3A%E9%A6%99%E6%B8%AF%22%2C+%22show_catalogs_q_63560.genre%22%3A+%22%22%2C+%22show_catalogs_q_63560.releaseyear%22%3A+%22%22%2C+%22show_catalogs_q_63560.orderby%22%3A+%227%22%2C+%22show_catalogs_fd_63560%22%3A+%22%22}&cmodid=63561&__rt=1&__ro=m13382821540",
                "start" => 1,
                "end" => 34,
            ),
        ),
        "taiwan" => array(
            array(
                "fenye" => true,
                "base_url" => "http://movie.youku.com/search/index2/_page63561_{A}_cmodid_63561?cc-showdivid=no&srcmid=63560&srcidx=1&linkage=%7B%22show_catalogs_q_63560.area%22%3A+%22area%3A%E5%8F%B0%E6%B9%BE%22%2C+%22show_catalogs_q_63560.genre%22%3A+%22%22%2C+%22show_catalogs_q_63560.releaseyear%22%3A+%22%22%2C+%22show_catalogs_q_63560.orderby%22%3A+%227%22%2C+%22show_catalogs_fd_63560%22%3A+%22%22%7D&cmodid=63561&__rt=1&__ro=m13382821540",
                "start" => 1,
                "end" => 34,
            ),
        ),
        "hanguo" => array(
            array(
                "fenye" => true,
                "base_url" => "http://movie.youku.com/search/index2/_page63561_{A}_cmodid_63561?cc-showdivid=no&srcmid=63560&srcidx=1&linkage=%7B%22show_catalogs_q_63560.area%22%3A+%22area%3A%E9%9F%A9%E5%9B%BD%22%2C+%22show_catalogs_q_63560.genre%22%3A+%22%22%2C+%22show_catalogs_q_63560.releaseyear%22%3A+%22%22%2C+%22show_catalogs_q_63560.orderby%22%3A+%227%22%2C+%22show_catalogs_fd_63560%22%3A+%22%22%7D&cmodid=63561&__rt=1&__ro=m13382821540",
                "start" => 1,
                "end" => 34,
            ),
        ),
        "meiguo" => array(
            array(
                "fenye" => true,
                "base_url" => "http://movie.youku.com/search/index2/_page63561_{A}_cmodid_63561?cc-showdivid=no&srcmid=63560&srcidx=1&linkage=%7B%22show_catalogs_q_63560.area%22%3A+%22area%3A%E7%BE%8E%E5%9B%BD%22%2C+%22show_catalogs_q_63560.genre%22%3A+%22%22%2C+%22show_catalogs_q_63560.releaseyear%22%3A+%22%22%2C+%22show_catalogs_q_63560.orderby%22%3A+%227%22%2C+%22show_catalogs_fd_63560%22%3A+%22%22%7D&cmodid=63561&__rt=1&__ro=m13382821540",
                "start" => 1,
                "end" => 34,
            ),
        ),
        "faguo" => array(
            array(
                "fenye" => true,
                "base_url" => "http://movie.youku.com/search/index2/_page63561_{A}_cmodid_63561?cc-showdivid=no&srcmid=63560&srcidx=1&linkage=%7B%22show_catalogs_q_63560.area%22%3A+%22area%3A%E6%B3%95%E5%9B%BD%22%2C+%22show_catalogs_q_63560.genre%22%3A+%22%22%2C+%22show_catalogs_q_63560.releaseyear%22%3A+%22%22%2C+%22show_catalogs_q_63560.orderby%22%3A+%227%22%2C+%22show_catalogs_fd_63560%22%3A+%22%22%7D&cmodid=63561&__rt=1&__ro=m13382821540",
                "start" => 1,
                "end" => 34,
            ),
        ),
        "yingguo" => array(
            array(
                "fenye" => true,
                "base_url" => "http://movie.youku.com/search/index2/_page63561_{A}_cmodid_63561?cc-showdivid=no&srcmid=63560&srcidx=1&linkage=%7B%22show_catalogs_q_63560.area%22%3A+%22area%3A%E8%8B%B1%E5%9B%BD%22%2C+%22show_catalogs_q_63560.genre%22%3A+%22%22%2C+%22show_catalogs_q_63560.releaseyear%22%3A+%22%22%2C+%22show_catalogs_q_63560.orderby%22%3A+%227%22%2C+%22show_catalogs_fd_63560%22%3A+%22%22%7D&cmodid=63561&__rt=1&__ro=m13382821540",
                "start" => 1,
                "end" => 34,
            ),
        ),
        "deguo" => array(
            array(
                "fenye" => true,
                "base_url" => "http://movie.youku.com/search/index2/_page63561_{A}_cmodid_63561?cc-showdivid=no&srcmid=63560&srcidx=1&linkage=%7B%22show_catalogs_q_63560.area%22%3A+%22area%3A%E5%BE%B7%E5%9B%BD%22%2C+%22show_catalogs_q_63560.genre%22%3A+%22%22%2C+%22show_catalogs_q_63560.releaseyear%22%3A+%22%22%2C+%22show_catalogs_q_63560.orderby%22%3A+%227%22%2C+%22show_catalogs_fd_63560%22%3A+%22%22%7D&cmodid=63561&__rt=1&__ro=m13382821540",
                "start" => 1,
                "end" => 34,
            ),
        ),
        "yidali" => array(
            array(
                "fenye" => true,
                "base_url" => "http://movie.youku.com/search/index2/_page63561_{A}_cmodid_63561?cc-showdivid=no&srcmid=63560&srcidx=1&linkage=%7B%22show_catalogs_q_63560.area%22%3A+%22area%3A%E6%84%8F%E5%A4%A7%E5%88%A9%22%2C+%22show_catalogs_q_63560.genre%22%3A+%22%22%2C+%22show_catalogs_q_63560.releaseyear%22%3A+%22%22%2C+%22show_catalogs_q_63560.orderby%22%3A+%227%22%2C+%22show_catalogs_fd_63560%22%3A+%22%22%7D&cmodid=63561&__rt=1&__ro=m13382821540",
                "start" => 1,
                "end" => 34,
            ),
        ),
        "jianada" => array(
            array(
                "fenye" => true,
                "base_url" => "http://movie.youku.com/search/index2/_page63561_{A}_cmodid_63561?cc-showdivid=no&srcmid=63560&srcidx=1&linkage=%7B%22show_catalogs_q_63560.area%22%3A+%22area%3A%E5%8A%A0%E6%8B%BF%E5%A4%A7%22%2C+%22show_catalogs_q_63560.genre%22%3A+%22%22%2C+%22show_catalogs_q_63560.releaseyear%22%3A+%22%22%2C+%22show_catalogs_q_63560.orderby%22%3A+%227%22%2C+%22show_catalogs_fd_63560%22%3A+%22%22%7D&cmodid=63561&__rt=1&__ro=m13382821540",
                "start" => 1,
                "end" => 34,
            ),
        ),
        "yindu" => array(
            array(
                "fenye" => true,
                "base_url" => "http://movie.youku.com/search/index2/_page63561_{A}_cmodid_63561?cc-showdivid=no&srcmid=63560&srcidx=1&linkage=%7B%22show_catalogs_q_63560.area%22%3A+%22area%3A%E5%8D%B0%E5%BA%A6%22%2C+%22show_catalogs_q_63560.genre%22%3A+%22%22%2C+%22show_catalogs_q_63560.releaseyear%22%3A+%22%22%2C+%22show_catalogs_q_63560.orderby%22%3A+%227%22%2C+%22show_catalogs_fd_63560%22%3A+%22%22%7D&cmodid=63561&__rt=1&__ro=m13382821540",
                "start" => 1,
                "end" => 34,
            ),
        ),
        "taiguo" => array(
            array(
                "fenye" => true,
                "base_url" => "http://movie.youku.com/search/index2/_page63561_{A}_cmodid_63561?cc-showdivid=no&srcmid=63560&srcidx=1&linkage=%7B%22show_catalogs_q_63560.area%22%3A+%22area%3A%E6%B3%B0%E5%9B%BD%22%2C+%22show_catalogs_q_63560.genre%22%3A+%22%22%2C+%22show_catalogs_q_63560.releaseyear%22%3A+%22%22%2C+%22show_catalogs_q_63560.orderby%22%3A+%227%22%2C+%22show_catalogs_fd_63560%22%3A+%22%22%7D&cmodid=63561&__rt=1&__ro=m13382821540",
                "start" => 1,
                "end" => 34,
            ),
        ),
        "eluosi" => array(
            array(
                "fenye" => true,
                "base_url" => "http://movie.youku.com/search/index2/_page63561_{A}_cmodid_63561?cc-showdivid=no&srcmid=63560&srcidx=1&linkage=%7B%22show_catalogs_q_63560.area%22%3A+%22area%3A%E4%BF%84%E7%BD%97%E6%96%AF%22%2C+%22show_catalogs_q_63560.genre%22%3A+%22%22%2C+%22show_catalogs_q_63560.releaseyear%22%3A+%22%22%2C+%22show_catalogs_q_63560.orderby%22%3A+%227%22%2C+%22show_catalogs_fd_63560%22%3A+%22%22%7D&cmodid=63561&__rt=1&__ro=m13382821540",
                "start" => 1,
                "end" => 34,
            ),
        ),
        "qita" => array(
            array(
                "fenye" => true,
                "base_url" => "http://movie.youku.com/search/index2/_page63561_{A}_cmodid_63561?cc-showdivid=no&srcmid=63560&srcidx=1&linkage=%7B%22show_catalogs_q_63560.area%22%3A+%22-area%3A%E5%A4%A7%E9%99%86%2C%E9%A6%99%E6%B8%AF%2C%E5%8F%B0%E6%B9%BE%2C%E9%9F%A9%E5%9B%BD%2C%E7%BE%8E%E5%9B%BD%2C%E6%B3%95%E5%9B%BD%2C%E8%8B%B1%E5%9B%BD%2C%E5%BE%B7%E5%9B%BD%2C%E6%84%8F%E5%A4%A7%E5%88%A9%2C%E5%8A%A0%E6%8B%BF%E5%A4%A7%2C%E5%8D%B0%E5%BA%A6%2C%E4%BF%84%E7%BD%97%E6%96%AF%2C%E6%B3%B0%E5%9B%BD%22%2C+%22show_catalogs_q_63560.genre%22%3A+%22%22%2C+%22show_catalogs_q_63560.releaseyear%22%3A+%22%22%2C+%22show_catalogs_q_63560.orderby%22%3A+%227%22%2C+%22show_catalogs_fd_63560%22%3A+%22%22%7D&cmodid=63561&__rt=1&__ro=m13382821540",
                "start" => 1,
                "end" => 34,
            ),
        ),
    ),
    "iqiyi" => array(//爱奇艺
        "name" => "爱奇艺",
        "open" => "yes",//是否打开
        "type" => 10,
        "new" => array(
            array(
                "fenye" => true,
                "base_url" => "http://list.iqiyi.com/www/1/------------2-2-{A}-1---.html",
                "start" => 1,
                "end" => 107,
            ),
        ),
    ),

    "m1905" => array(//电影网
        "name" => "电影网",
        "open" => "yes",//是否打开
        "type" => 11,
        "neidi" => array(//内地电影
            array(
                "fenye" => true,
                "base_url" => "http://www.m1905.com/vod/list/a_1/o5u1l0p{A}.html",
                "start" => 1,
                "end" => 99,
                "diqu" => 1,
            ),
        ),
        "gangtai" => array(//港台电影
            array(
                "fenye" => true,
                "base_url" => "http://www.m1905.com/vod/list/a_2/o5u1l0p{A}.html",
                "start" => 1,
                "end" => 56,
                "diqu" => 1,
            ),
        ),
        "rihan" => array(//日韩电影
            array(
                "fenye" => true,
                "base_url" => "http://www.m1905.com/vod/list/a_3/o5u1l0p{A}.html",
                "start" => 1,
                "end" => 5,
                "diqu" => 2,
            ),
        ),
        "oumei" => array(//欧美电影
            array(
                "fenye" => true,
                "base_url" => "http://www.m1905.com/vod/list/a_4/o5u1l0p{A}.html",
                "start" => 1,
                "end" => 13,
                "diqu" => 3,
            ),
        ),
        "qita" => array(//其他电影
            array(
                "fenye" => true,
                "base_url" => "http://www.m1905.com/vod/list/a_5/o5u{A}.html",
                "start" => 1,
                "end" => 1,
                "diqu" => 10,
            ),
        ),
    ),
    "pps" => array(//pps
        "name" => "pps",
        "open" => "yes",//是否打开
        "type" => 12,
        "new" => array(
            array(
                "fenye" => true,
                "base_url" => "http://v.pps.tv/v_list/c_movie_o_3_p_{A}.html",
                "start" => 1,
                "end" => 246,
            ),
        ),
    ),
    "pptv" => array(//pptv，此处抓取程序还没完成
        "name" => "pptv",
        "open" => "yes",//是否打开
        "type" => 13,
        "new" => array(
            array(
                "fenye" => true,
                "base_url" => "http://list.pptv.com/sort_list/1---------{A}.html",
                "start" => 1,
                "end" => 216,
            ),
        ),
    ),
    "qire" => array(//奇热
        "name" => "奇热",
        "open" => "yes",//是否打开
        "type" => 14,
        "action" => array(
            array(
                "fenye" => true,
                "base_url" => "http://ajax.qire123.com/vod-showlist-id-8-order-time-c-2873-p-{A}.html",
                "start" => 1,
                "end" => 144,
            ),
        ),
        "drama" => array(
            array(
                "fenye" => true,
                "base_url" => "http://ajax.qire123.com/vod-showlist-id-14-order-hits-c-5558-p-{A}.html",
                "start" => 1,
                "end" => 278,
            ),
        ),
        "comedy" => array(
            array(
                "fenye" => true,
                "base_url" => "http://ajax.qire123.com/vod-showlist-id-9-order-time-c-2991-p-{A}.html",
                "start" => 1,
                "end" => 150,
            ),
        ),
        "romance" => array(
            array(
                "fenye" => true,
                "base_url" => "http://ajax.qire123.com/vod-showlist-id-10-order-time-c-1527-p-{A}.html",
                "start" => 1,
                "end" => 77,
            ),
        ),
        "horror" => array(
            array(
                "fenye" => true,
                "base_url" => "http://ajax.qire123.com/vod-showlist-id-12-order-time-c-2355-p-{A}.html",
                "start" => 1,
                "end" => 118,
            ),
        ),
        "fiction" => array(
            array(
                "fenye" => true,
                "base_url" => "http://ajax.qire123.com/vod-showlist-id-11-order-time-c-851-p-{A}.html",
                "start" => 1,
                "end" => 43,
            ),
        ),
        "war" => array(//战争片
            array(
                "fenye" => true,
                "base_url" => "http://ajax.qire123.com/vod-showlist-id-13-order-time-c-591-p-{A}.html",
                "start" => 1,
                "end" => 30,
            ),
        ),
    ),

    "imdb" => array(//imdb
        "name" => "imdb",
        "open" => "yes",//是否打开
        "type" => 15,//网站type
        "top" => array(//top（经典电影）
            array(
                "fenye" => true,
                "base_url" => "http://www.imdb.cn/imdb250/{A}",
                "start" => 1,
                "end" => 9,
            ),
        ),
    ),
    "baidu" => array(//百度
        "name" => "百度",
        "open" => "yes",//是否打开
        "type" => 16,//网站type
        "click" => array(//百度点击排行榜（经典电影）
            "http://top.baidu.com/buzz/movie.html",
        ),
    ),
);