<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php if ($this->load->get_title()):?><?php echo $this->load->get_title();?><?php else:?>电影吧<?php endif;?></title>
    <link rel="stylesheet" rev="stylesheet" href="/css/background/backgroundmain.css" type="text/css" />
    <?php $css = $this->load->get_css();?>
    <?php if (!empty($css)):?>
    <?php foreach($css as $cssKey => $cssVal):?>
        <link rel="stylesheet" rev="stylesheet" href="/<?php echo $cssVal;?>" type="text/css" />
        <?php endforeach;?>
    <?php endif;?>
    <script type="text/javascript" src="/js/main/jquery-1.7.2.js"></script>
    <?php $js = $this->load->get_js();?>
    <?php if (!empty($js)):?>
    <?php foreach($js as $jsKey => $jsVal):?>
        <script type="text/javascript" src="/<?php echo $jsVal;?>"></script>
    <?php endforeach;?>
    <?php endif;?>
</head>
<body>
<div class="main">
    <div class="menus">
        <ul>
            <?php $menus = get_config_value("background_menus")?>
            <?php $index = $this->load->get_top_index();?>
            <?php foreach($menus as $menuKey => $menuVal):?>
            <li <?php if ($index == $menuKey):?>class="current"<?php endif;?>><a href="<?php echo $menuVal['link'];?>"><?php echo $menuVal['titlle'];?></a></li>
            <?php endforeach;?>
            <li class="welcome">欢迎来到电影吧管理后台![<a href="">退出</a>]</li>
        </ul>
    </div>
    <div class="container">
        <?php $view = $this->load->get_view();?>
        <?php if (!empty($data)):?>
            <?php $this->load->view($view,$data);?>
        <?php else:?>
            <?php $this->load->view($view);?>
        <?php endif;?>
    </div>
</div>
</body>
</html>