<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="nofollow">
    <meta name="description" content="电影观看与电影下载资源最齐全的网站，我们只专注于电影，旨在为您致力打造提供最快速、最方便的电影观看与下载通道。在这里，您可以找到任何您想要看与想要下载的电影，各种超清电影提供中。"/>
    <meta name="keywords" content="<?php echo APF::get_instance()->get_config_value("base_name");?>电影库,最新电影,电影排行榜,<?php echo APF::get_instance()->get_config_value("base_name");?>网"/>
    <meta property="qc:admins" content="5271500601611670646" />
    <meta property="wb:webmaster" content="80c06dc12155cdbd" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php if ($this->load->get_title()):?><?php echo $this->load->get_title();?><?php else:?>好吧<?php endif;?></title>
    <link rel="Shortcut Icon" href="/images/common/logohao.png" />
    <?php $css = $this->load->get_css();?>
    <?php $cssArr[] = "/css/main/bootstrap.css";?>
    <?php $cssArr[] = "/css/main/base.css";?>
    <?php $cssArr[] = "/css/main/base-res.css";?>
    <?php $cssArr[] = "/css/main/docs.css";?>
    <?php $cssArr[] = "/css/main/code.css";?>
    <?php $cssArr[] = "/css/main/main.css";?>

    <?php $cssArr[] = "/css/main/base2.css";?>
    <?php $cssArr[] = "/css/main/base3.css";?>
    <?php if (!empty($css)):?>
        <?php foreach($css as $cssKey => $cssVal):?>
            <?php $cssArr[] = "/" . trim($cssVal,"/");?>
        <?php endforeach;?>
    <?php endif;?>
    <?php $jsArr[] = "/js/main/jquery-1.7.2.js";?>
    <?php $jsArr[] = "/js/main/base.js";?>
    <?php $js = $this->load->get_js();?>
    <?php if (!empty($js)):?>
        <?php foreach($js as $jsKey => $jsVal):?>
            <?php $jsArr[] = "/" . trim($jsVal,"/");?>
        <?php endforeach;?>
    <?php endif;?>
    <link rel="stylesheet" rev="stylesheet" href="/gettaticfile/css.css?path=<?php echo base64_encode(implode(";",$cssArr));?>" type="text/css" />
    <script type="text/javascript" src="/gettaticfile/js.js?path=<?php echo base64_encode(implode(";",$jsArr));?>"></script>

</head>
<body data-spy="scroll" data-target=".bs-docs-sidebar">
<?php if ($this->load->get_head_img()):?>
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container top_head">
            <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="brand" href="http://www.dianying8.tv" target="_blank">电影吧</a>
            <div class="nav-collapse collapse head_top_menus">
                <ul class="nav menus_list">
                    <?php $menus = get_config_value("background_menus")?>
                    <?php $index = $this->load->get_top_index();?>
                    <?php foreach ($menus as $menuKey => $menuVal): ?>
                        <li class="<?php if ($index == $menuKey): ?>active<?php else:?><?php echo $menuVal['class'];?><?php endif; ?>">
                            <a href="<?php echo $menuVal['link']; ?>"><?php echo $menuVal['title'];?></a>
                            <?php if (!empty($menuVal['list'])):?>
                                <div class="menus_info_list">
                                    <table class="menus_list_table">
                                        <?php $listI = 1;?>
                                        <?php foreach($menuVal['list'] as $listVal):?>
                                            <tr>
                                                <td <?php if($listI == 1):?>class="fisrt_one"<? elseif ($listI == count($menuVal['list'])):?>class="last_one" <?php endif;?>>
                                                    <a href="<?php echo $listVal['url'];?>">
                                                        <?php echo $listVal['name'];?>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php $listI++;?>
                                        <?php endforeach;?>
                                    </table>
                                </div>
                            <?php endif;?>
                        </li>
                    <?php endforeach;?>
                    </ul>
                    <?php if (!empty($userName)): ?>
                    <ul class="nav" style="float: right">
                        <li class="username">
                            <a href="<?php echo get_url("/background/");?>"><i class="icon-user icon-user"></i><?php echo $userName;?></a>
                        </li>
                        <li>
                            <a class="logout" href="<?php echo get_url("/logout/")?>">[退出]</a>
                        </li>
                    </ul>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>
<?php endif;?>
<div class="container">
    <?php $view = $this->load->get_view();?>
    <?php if (!empty($data)): ?>
        <?php $this->load->view($view, $data); ?>
    <?php else: ?>
        <?php $this->load->view($view); ?>
    <?php endif;?>
</div>
</body>
</html>
