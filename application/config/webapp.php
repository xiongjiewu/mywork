<?php
/*
 * 第三放接口登录配置文件
 */
$config['web_app_info'] = array(
    'qq' => array(
        "type" => 1,//本站独自表示的接口类型
        "appId" => "100461606",
        "appKey" => "60b02b49654c5b743eb9a2a79621c438",
        "loginInfo" => array(//登录配置信息
            "baseUrl" => "https://graph.qq.com/oauth2.0/authorize",
            "method" => "get",//参数传递方式
            "params" => array(//登录参数
                "response_type" => "code",
                "client_id" => "100461606",
                "redirect_uri" => APF::get_instance()->get_config_value("base_url") . "/applogincallback/qq/",
                "state" => APF::get_instance()->encodeId(time()) . "[A]" . base64_encode($_SERVER['HTTP_REFERER']),//加密字符串+来源地址，防止第三方网站攻击，以及登录返回来源地址
            ),
        ),
        "getToken" => array(
            "baseUrl" => "https://graph.qq.com/oauth2.0/token",
            "method" => "get",//参数传递方式
            "params" => array(//登录参数
                "grant_type" => "authorization_code",
                "client_id" => "100461606",
                "client_secret" => "60b02b49654c5b743eb9a2a79621c438",
                "code" => "",//此code是登录时返回的code
                "redirect_uri" => APF::get_instance()->get_config_value("base_url") . "/applogincallback/qq/",
            ),
        ),
        "getOpenId" => array(
            "baseUrl" => "https://graph.qq.com/oauth2.0/me",
            "method" => "get",//参数传递方式
            "params" => array(//登录参数
                "access_token" => "",//此toker基于getToken返回的值
            ),
        ),
        "getUserInfo" => array(
            "baseUrl" => "https://graph.qq.com/user/get_user_info",
            "method" => "get",//参数传递方式
            "params" => array(//登录参数
                "access_token" => "",//此toker基于getToken返回的值
                "oauth_consumer_key" => "100461606",
                "openid" => "",//此openid基于getToken返回的值
            ),
        ),
        "codeTime" => 10 * 60,//code生效时间，10分钟
    ),

    'weibo' => array(
        "type" => 2,//本站独自表示的接口类型
        "appId" => "3388334580",
        "appKey" => "f76f5e0b941edd50667762784ab0cc46",
        "loginInfo" => array(//登录配置信息
            "baseUrl" => "https://api.weibo.com/oauth2/authorize",
            "method" => "get",//参数传递方式
            "params" => array(//登录参数
                "response_type" => "code",
                "client_id" => "3388334580",
                "redirect_uri" => APF::get_instance()->get_config_value("base_url") . "/applogincallback/weibo/",
                "state" => APF::get_instance()->encodeId(time()) . "[A]" . base64_encode($_SERVER['HTTP_REFERER']),//加密字符串+来源地址，防止第三方网站攻击，以及登录返回来源地址
            ),
        ),
        "getToken" => array(
            "baseUrl" => "https://api.weibo.com/oauth2/access_token",
            "method" => "post",//参数传递方式
            "params" => array(//登录参数
                "grant_type" => "authorization_code",
                "client_id" => "3388334580",
                "client_secret" => "f76f5e0b941edd50667762784ab0cc46",
                "code" => "",//此code是登录时返回的code
                "redirect_uri" => APF::get_instance()->get_config_value("base_url") . "/applogincallback/weibo/",
            ),
        ),
        "getUserInfo" => array(
            "baseUrl" => "https://api.weibo.com/2/users/show.json",
            "method" => "get",//参数传递方式
            "params" => array(//登录参数
                "source" => "3388334580",
                "access_token" => "",//此toker基于getToken返回的值
                "uid" => "",//此toker基于getToken返回的值
            ),
        ),
        "codeTime" => 10 * 60,//code生效时间，10分钟
    ),

    'renren' => array(
        "type" => 3,//本站独自表示的接口类型
        "appId" => "237415",
        "appKey" => "a8da7cecf2a84c5d8ad506e922ea243c",
        "secretKey" => "4e0460c7194441f392c7ea410bf117dc",
        "loginInfo" => array(//登录配置信息
            "baseUrl" => "https://graph.renren.com/oauth/authorize",
            "method" => "get",//参数传递方式
            "params" => array(//登录参数
                "response_type" => "code",
                "client_id" => "a8da7cecf2a84c5d8ad506e922ea243c",
                "display" => "page",
                "redirect_uri" => APF::get_instance()->get_config_value("base_url") . "/applogincallback/renren/",
                "state" => APF::get_instance()->encodeId(time()) . "[A]" . base64_encode($_SERVER['HTTP_REFERER']),//加密字符串+来源地址，防止第三方网站攻击，以及登录返回来源地址
            ),
        ),
        "getToken" => array(
            "baseUrl" => "https://graph.renren.com/oauth/token",
            "method" => "post",//参数传递方式
            "params" => array(//登录参数
                "grant_type" => "authorization_code",
                "client_id" => "a8da7cecf2a84c5d8ad506e922ea243c",
                "client_secret" => "4e0460c7194441f392c7ea410bf117dc",
                "code" => "",//此code是登录时返回的code
                "redirect_uri" => APF::get_instance()->get_config_value("base_url") . "/applogincallback/renren/",
            ),
        ),
        "getUserInfo" => array(
            "baseUrl" => "https://api.weibo.com/2/users/show.json",
            "method" => "get",//参数传递方式
            "params" => array(//登录参数
                "source" => "3388334580",
                "access_token" => "",//此toker基于getToken返回的值
                "uid" => "",//此toker基于getToken返回的值
            ),
        ),
        "codeTime" => 10 * 60,//code生效时间，10分钟
    ),
);