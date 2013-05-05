<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="description" content="电影最齐全的网站，我们只专于电影，用户想看的就是我们的宗旨。在这里，您可以找到任何您想看的电影，各种超清电影提供中。"/>
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
    <script type="text/javascript" src="/js/main/ie6.js"></script>
    <![endif]-->
    <?php $css = $this->load->get_css();?>
    <?php if (!empty($css)): ?>
        <?php foreach ($css as $cssKey => $cssVal): ?>
            <link rel="stylesheet" rev="stylesheet" href="/<?php echo trim($cssVal, "/"); ?>" type="text/css"/>
        <?php endforeach; ?>
    <?php endif;?>
    <script type="text/javascript" src="/js/main/jquery-1.7.2.js"></script>
    <script type="text/javascript" src="/js/main/base.js"></script>
    <?php if ($this->load->get_login_pan()):?>
        <link href="/css/member/loginpan.css" rel="stylesheet">
        <script type="text/javascript" src="/js/member/loginpan.js"></script>
    <?php endif;?>
    <?php $js = $this->load->get_js();?>
    <?php if (!empty($js)): ?>
        <?php foreach ($js as $jsKey => $jsVal): ?>
            <script type="text/javascript" src="/<?php echo trim($jsVal, "/"); ?>"></script>
        <?php endforeach; ?>
    <?php endif;?>
</head>
<body data-spy="scroll" data-target=".bs-docs-sidebar">
<div class="navbar navbar-inverse navbar-fixed-top" style="min-width: 100%;float: left;max-height: 44px;">
    <div class="navbar-inner" style="min-width: 100%;float: left">
        <div class="container top_head">
            <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="brand" style="color:#fff;float: left;" href="/">电影吧</a>

            <div class="nav-collapse collapse head_top_menus" style="float: left;width: 93%;">
                <ul class="nav">
                    <?php $menus = get_config_value("menus")?>
                    <?php $index = $this->load->get_top_index();?>
                    <?php foreach ($menus as $menuKey => $menuVal): ?>
                        <?php if (($menuVal['index'] != "research") || !empty($notDoResearch)):?>
                            <li class="<?php if ($index == $menuKey): ?>active<?php endif; ?> <?php echo $menuVal['class']; ?>">
                                <a href="<?php echo $menuVal['link']; ?>"><?php echo $menuVal['title'];?></a>
                                <?php if (!empty($menuVal['index']) && ($menuVal['index'] == "list")): ?>
                                    <div class="dy_type_list">
                                        <table class="table">
                                            <?php foreach ($menuVal['type_info'] as $infokey => $infoVal): ?>
                                                <tr>
                                                    <th><?php echo $infoVal['type'];?></th>
                                                    <?php foreach ($infoVal['info'] as $key => $infoDetail): ?>
                                                        <td>
                                                            <a <?php if ($index == $menuKey && $infokey == $data['bigtype'] && $key == $data['type']): ?>class="active" <?php endif;?>
                                                               href="<?php echo $infoVal['base_url'] . $key; ?>"><?php echo $infoDetail;?></a>
                                                        </td>
                                                    <?php endforeach;?>
                                                </tr>
                                            <?php endforeach;?>
                                        </table>
                                    </div>
                                <?php endif;?>
                            </li>
                        <?php endif;?>
                    <?php endforeach;?>
                    <li class="search" style="margin-left: 100px">
                        <form autocomplete="off" name="search_dy" id="search_dy" onsubmit="return false;"
                              action="<?php echo get_url("/search/"); ?>">
                            <input type="text" class="search_value" name="search" id="search"
                                   value="<?php if (isset($data['searchW'])): ?><?php echo $data['searchW']; ?><?php else: ?>搜索您喜欢的影片...<?php endif; ?>">
                            <input type="submit" class="submit" name="search_submit" id="search_submit" value="">
                        </form>
                        <div class="about_search">
                            <?php if (isset($data['searchW'])): ?>
                                <span><?php echo $data['searchW']; ?></span><?php endif;?>
                        </div>
                    </li>
                    <?php if (!empty($userName)): ?>
                        <li class="username" style="float: right">
                            <a href="<?php echo get_url("/usercenter/"); ?>"><i
                                    class="icon-user icon-user"></i><?php echo $userName;?>
                                (<?php echo $userNoReadMessageCount;?>)
                            </a>
                            <div class="user_in">
                                <table class="table">
                                    <tr>
                                        <td><a href="<?php echo get_url("/usercenter/"); ?>"><i
                                                    class="icon-user"></i><?php echo $userName;?></a><a
                                                <?php if ($userNoReadMessageCount > 0): ?>class="message"
                                                href="<?php echo get_url("/usercenter/message/0/") ?>" title="有新消息"
                                                <?php else: ?>href="<?php echo get_url("/usercenter/message/") ?>"
                                                class="icon-envelope"<?php endif;?>></a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="<?php echo get_url("/usercenter/mycollect/"); ?>"><i
                                                    class="icon-film"></i>我&nbsp;的&nbsp;收&nbsp;藏</a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="<?php echo get_url("/usercenter/feedback/"); ?>"><i
                                                    class="icon-edit"></i>反&nbsp;馈&nbsp;想&nbsp;看</a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="<?php echo get_url("/usercenter/notice/"); ?>"><i
                                                    class="icon-volume-up"></i>电&nbsp;影&nbsp;通&nbsp;知</a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="<?php echo get_url("/logout/"); ?>"><i class="icon-off"></i>退&nbsp;出&nbsp;登&nbsp;录</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </li>
                    <?php else: ?>
                        <li class="loginr" style="margin-left: 0;float: right">
                            <a href="<?php echo get_url("/register/"); ?>" title="注册"
                               style="padding-left: 5px;padding-right: 5px">
                                <i class="icon-pencil"></i>
                                注册
                            </a>
                        </li>
                        <li class="loginr" style="float: right">
                            <a href="<?php echo get_url("/login/"); ?>" title="登录"
                               style="padding-left: 5px;padding-right: 5px">
                                <i class="icon-user"></i>
                                登录
                            </a>
                        </li>
                    <?php endif;?>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php if ($this->load->get_head_img()): ?>
    <header class="jumbotron subhead" id="overview">
        <div class="container">
            <h1>电影吧</h1>
            <p class="lead">您还在为找不到电影资源而苦恼嘛？您还在为大量的影片资源无法挑选而苦恼嘛？</p>
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
<?php $this->load->view("component/footer");//底部?>
<?php if ($this->load->get_login_pan()):?>
    <?php $this->load->view("component/loginpan");//登录框?>
<?php endif;?>
<?php if (!empty($showResearchPan)):?>
    <?php $this->load->view("component/researchpan");//调查问卷?>
<?php endif;?>
</body>
</html>
