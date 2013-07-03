<meta charset="utf-8">
<meta name="description" content="电影观看与电影下载资源最齐全的网站，我们只专注于电影，旨在为您致力打造提供最快速、最方便的电影观看与下载通道。在这里，您可以找到任何您想要看与想要下载的电影，各种超清电影提供中。"/>
<meta name="keywords" content="<?php echo APF::get_instance()->get_config_value("base_name");?>电影库,最新电影,电影排行榜,<?php echo APF::get_instance()->get_config_value("base_name");?>网"/>
<meta property="qc:admins" content="5271500601611670646" />
<meta property="wb:webmaster" content="80c06dc12155cdbd" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php if ($this->load->get_title()):?><?php echo $this->load->get_title();?><?php else:?>好吧<?php endif;?></title>
<link rel="Shortcut Icon" href="/images/common/logohao.png" />
<link href="/css/main/base2.css" rel="stylesheet">
<?php $css = $this->load->get_css();?>
<?php if (!empty($css)):?>
    <?php foreach($css as $cssKey => $cssVal):?>
        <link rel="stylesheet" rev="stylesheet" href="/<?php echo trim($cssVal,"/");?>" type="text/css" />
    <?php endforeach;?>
<?php endif;?>
<script type="text/javascript" src="/js/main/jquery-1.7.2.js"></script>
<script type="text/javascript" src="/js/main/base2.js"></script>
<?php if ($this->load->get_login_pan()):?>
    <link href="/css/member/loginpan.css" rel="stylesheet">
    <script type="text/javascript" src="/js/member/loginpan.js"></script>
<?php endif;?>
<?php $js = $this->load->get_js();?>
<?php if (!empty($js)):?>
    <?php foreach($js as $jsKey => $jsVal):?>
        <script type="text/javascript" src="/<?php echo trim($jsVal,"/");?>"></script>
    <?php endforeach;?>
<?php endif;?>