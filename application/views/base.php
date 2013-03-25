<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php if ($this->load->get_title()): ?><?php echo $this->load->get_title(); ?><?php else: ?>电影吧<?php endif;?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="/css/main/bootstrap.css" rel="stylesheet">
    <link href="/css/main/base.css" rel="stylesheet">
    <link href="/css/main/base-res.css" rel="stylesheet">
    <link href="/css/main/docs.css" rel="stylesheet">
    <link href="/css/main/code.css" rel="stylesheet">
    <link href="/css/main/main.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="/js/main/html5shiv.js"></script>
    <![endif]-->
    <!--[if IE 6]>
    <link href="/css/main/ie6.min.css" rel="stylesheet">
    <script src="/js/main/ie6.js"></script>
    <![endif]-->
    <?php $css = $this->load->get_css();?>
    <?php if (!empty($css)): ?>
        <?php foreach ($css as $cssKey => $cssVal): ?>
            <link rel="stylesheet" rev="stylesheet" href="/<?php echo trim($cssVal, "/"); ?>" type="text/css"/>
        <?php endforeach; ?>
    <?php endif;?>
    <script type="text/javascript" src="/js/main/jquery-1.7.2.js"></script>
    <script type="text/javascript" src="/js/main/base.js"></script>
    <?php $js = $this->load->get_js();?>
    <?php if (!empty($js)): ?>
        <?php foreach ($js as $jsKey => $jsVal): ?>
            <script type="text/javascript" src="/<?php echo trim($jsVal, "/"); ?>"></script>
        <?php endforeach; ?>
    <?php endif;?>

</head
<body data-spy="scroll" data-target=".bs-docs-sidebar">
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container top_head">
            <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="brand" href="/">电影吧</a>
            <div class="nav-collapse collapse head_top_menus">
                <ul class="nav">
                    <?php $menus = get_config_value("menus")?>
                    <?php $index = $this->load->get_top_index();?>
                    <?php foreach ($menus as $menuKey => $menuVal): ?>
                        <li class="<?php if ($index == $menuKey): ?>active<?php endif; ?> <?php echo $menuVal['class']; ?>">
                            <a href="<?php echo $menuVal['link']; ?>"><?php echo $menuVal['titlle'];?></a>
                            <?php if (!empty($menuVal['index']) && ($menuVal['index'] == "list")): ?>
                                <div class="dy_type_list">
                                    <table class="table">
                                        <?php foreach($menuVal['type_info'] as $infokey => $infoVal):?>
                                        <tr>
                                            <th><?php echo $infoVal['type'];?></th>
                                            <?php foreach ($infoVal['info'] as $key => $infoDetail): ?>
                                                <td><a <?php if ($index == $menuKey && $infokey == $data['bigtype'] && $key == $data['type']):?>class="active" <?php endif;?> href="<?php echo $infoVal['base_url'] . $key;?>"><?php echo $infoDetail;?></a></td>
                                            <?php endforeach;?>
                                        </tr>
                                        <?php endforeach;?>
                                    </table>
                                </div>
                            <?php endif;?>
                        </li>
                    <?php endforeach;?>
                    <li class="search" style="margin-left: 150px">
                        <form name="search_dy" id="search_dy" onsubmit="return false;" action="<?php echo get_url("/search/");?>">
                            <input type="text" class="search_value" name="search" id="search" value="<?php if (isset($data['searchW'])):?><?php echo $data['searchW'];?><?php else:?>搜索您喜欢的影片...<?php endif;?>">
                            <input type="submit" class="submit" name="search_submit" id="search_submit" value="">
                        </form>
                    </li>
                    </ul>
                    <?php if (!empty($userName)): ?>
                    <ul class="nav" style="float: right">
                        <li class="username">
                            <a href="<?php echo get_url("/usercenter/");?>"><i class="icon-user icon-user"></i><?php echo $userName;?>(<?php echo $userNoReadMessageCount;?>)</a>
                            <div class="user_in">
                                <table class="table">
                                    <tr><td><a href="<?php echo get_url("/usercenter/");?>"><i class="icon-user"></i><?php echo $userName;?></a><a <?php if ($userNoReadMessageCount > 0):?>class="message" href="<?php echo get_url("/usercenter/message/0/")?>" title="有新消息" <?php else:?>href="<?php echo get_url("/usercenter/message/")?>" class="icon-envelope"<?php endif;?>></a></td></tr>
                                    <tr><td><a href="<?php echo get_url("/usercenter/mycollect/");?>"><i class="icon-film"></i>我&nbsp;的&nbsp;收&nbsp;藏</a></td></tr>
                                    <tr><td><a href="<?php echo get_url("/usercenter/");?>"><i class="icon-edit"></i>反馈我想看</a></td></tr>
                                    <tr><td><a href="<?php echo get_url("/usercenter/");?>"><i class="icon-envelope"></i>投诉与建议</a></td></tr>
                                    <tr><td><a href="<?php echo get_url("/logout/");?>"><i class="icon-off"></i>退&nbsp;出&nbsp;登&nbsp;录</a></td></tr>
                                </table>
                            </div>
                        </li>
                    </ul>
                    <?php else: ?>
                    <ul class="nav" style="float: right">
                        <li class="loginr">
                            <a href="<?php echo get_url("/login/"); ?>" title="登录" style="padding-left: 5px;padding-right: 5px">
                                <i class="icon-user icon-user"></i>
                            </a>
                        </li>
                        <li class="loginr" style="margin-left: 0;">
                            <a href="<?php echo get_url("/register/"); ?>" title="注册" style="padding-left: 5px;padding-right: 5px">
                                <i class="icon-edit icon-user"></i>
                            </a>
                        </li>
                    </ul>
                    <?php endif;?>
            </div>
        </div>
    </div>
</div>
<?php if ($this->load->get_head_img()): ?>
    <header class="jumbotron subhead" id="overview">
        <div class="container">
            <h1>电影吧</h1>

            <p class="lead">您还在为找不到电影资源而苦恼嘛？你还在为大量的影片资源无法挑选而苦恼嘛？</p>
        </div>
    </header>
<?php endif;?>
<div class="container">
    <?php $view = $this->load->get_view();?>
    <?php if (!empty($data)): ?>
        <?php $this->load->view($view, $data); ?>
    <?php else: ?>
        <?php $this->load->view($view); ?>
    <?php endif;?>
</div>
<footer class="footer">
    <div class="container">
        <p>Designed and built with all the love in the world by <a href="http://twitter.com/mdo"
                                                                   target="_blank">@mdo</a> and <a
                href="http://twitter.com/fat" target="_blank">@fat</a>.</p>

        <p>Code licensed under <a href="http://www.apache.org/licenses/LICENSE-2.0" target="_blank">Apache License
                v2.0</a>, documentation under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a>.</p>

        <p><a href="http://glyphicons.com">Glyphicons Free</a> licensed under <a
                href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a>.</p>
        <ul class="footer-links">
            <li><a href="http://blog.getbootstrap.com">Blog</a></li>
            <li class="muted">&middot;</li>
            <li><a href="https://github.com/twitter/bootstrap/issues?state=open">Issues</a></li>
            <li class="muted">&middot;</li>
            <li><a href="https://github.com/twitter/bootstrap/blob/master/CHANGELOG.md">Changelog</a></li>
        </ul>
    </div>
</footer>
<script type="text/javascript">
    (function ($) {
        var initOjb = {
            addCladdToLi: function (obj, c) {
                obj.addClass(c);
            },
            removeClass: function (obj, c) {
                obj.removeClass(c);
            },
            removeSpecailStr:function (s) {
                var pattern = new RegExp("[`~!@#$^&*()=|{}':;'%+《》『』,\\[\\].<>/?~！@#￥……&*（）&mdash;—|{}【】‘；：”“'。，、？]");
                var rs = "";
                for (var i = 0; i < s.length; i++) {
                    rs = rs + s.substr(i, 1).replace(pattern, '');
                }
                return rs;
            }
        };
        $(document).ready(function () {
            $("#search").bind("focus", function () {
                var search_val = $.trim($(this).val());
                if (search_val == "搜索您喜欢的影片...") {
                    $("#search").val("");
                }
            });
            $("#search").bind("blur", function () {
                var search_val = $.trim($(this).val());
                if (!search_val || search_val == undefined) {
                    $("#search").val("搜索您喜欢的影片...");
                }
            });
            $("#search_dy").submit(function(){
                var search_val = $.trim($("#search").val());
                    search_val = initOjb.removeSpecailStr(search_val);
                if (!search_val || (search_val == "搜索您喜欢的影片...")) {
                    window.location.href = "<?php echo get_url("/classicmovie/");?>";
                    return false;
                } else {

                    window.location.href = "<?php echo get_url("/search/index/");?>" + search_val;
                    return false;
                }
            });
            $("div.head_top_menus ul.nav li.dy_sort").mouseover(function () {
                initOjb.addCladdToLi($(this), "show_sort");
                $("div.head_top_menus ul.nav  li.dy_sort div.dy_type_list").show();
            });
            $("div.head_top_menus ul.nav li.dy_sort").mouseleave(function () {
                initOjb.removeClass($(this), "show_sort");
                $("div.head_top_menus ul.nav  li.dy_sort div.dy_type_list").hide();
            })
            $("div.head_top_menus ul.nav  li.dy_sort div.dy_type_list").mouseover(function () {
                $(this).show();
                initOjb.addCladdToLi($("div.head_top_menus ul.nav li.dy_sort"), "show_sort");
            });
            $("div.head_top_menus ul.nav  li.dy_sort div.dy_type_list").mouseleave(function () {
                $(this).hide();
                initOjb.removeClass($("div.head_top_menus ul.nav li.dy_sort"), "show_sort");
            });
            $("div.head_top_menus ul.nav li.username,div.head_top_menus ul.nav li.username div.user_in").mouseover(function(){
                $("div.head_top_menus ul.nav li.username div.user_in").show();
            });
            $("div.head_top_menus ul.nav li.username,div.head_top_menus ul.nav li.username div.user_in").mouseleave(function(){
                $("div.head_top_menus ul.nav li.username div.user_in").hide();
            });
        });
    })(jQuery);
</script>
<?php if($this->load->get_move_js()):?>
<script src="/js/base/bootstrap-transition.js"></script>
<script src="/js/base/bootstrap-alert.js"></script>
<script src="/js/base/bootstrap-modal.js"></script>
<script src="/js/base/bootstrap-dropdown.js"></script>
<script src="/js/base/bootstrap-scrollspy.js"></script>
<script src="/js/base/bootstrap-tab.js"></script>
<script src="/js/base/bootstrap-tooltip.js"></script>
<script src="/js/base/bootstrap-popover.js"></script>
<script src="/js/base/bootstrap-button.js"></script>
<script src="/js/base/bootstrap-collapse.js"></script>
<script src="/js/base/bootstrap-carousel.js"></script>
<script src="/js/base/bootstrap-typeahead.js"></script>
<script src="/js/base/bootstrap-affix.js"></script>
<script src="/js/base/holder/holder.js"></script>
<script src="/js/base/google-code-prettify/prettify.js"></script>
<script src="/js/base/application.js"></script>
<?php endif;?>
</body>
</html>
