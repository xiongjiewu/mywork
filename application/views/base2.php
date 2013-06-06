<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="description" content="电影最齐全的网站，我们只专于电影，用户想看的就是我们的宗旨。在这里，您可以找到任何您想看的电影，各种超清电影提供中。"/>
    <meta name="keywords" content="<?php echo APF::get_instance()->get_config_value("base_name");?>电影库,最新电影,电影排行榜,<?php echo APF::get_instance()->get_config_value("base_name");?>网"/>
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
</head>
<body>
<div class="main" id="total_info_main">
    <?php if (!empty($data)):?>
        <?php $this->load->view("component/header",$data);//顶部?>
    <?php else:?>
        <?php $this->load->view("component/header");//顶部?>
    <?php endif;?>
    <div class="container">
        <?php $view = $this->load->get_view();?>
        <?php if (!empty($data)):?>
            <?php $this->load->view($view,$data);?>
        <?php else:?>
            <?php $this->load->view($view);?>
        <?php endif;?>
    </div>
</div>
<?php $this->load->view("component/footer");//底部?>
<?php if ($this->load->get_login_pan()):?>
    <?php $this->load->view("component/loginpan");//登录框?>
<?php endif;?>
<!-- 百度流量统计js start-->
<div style="display: none;">
    <script type="text/javascript">
        var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
        document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F105a468dfb25231597f17f7accf5c0c4' type='text/javascript'%3E%3C/script%3E"));
    </script>
    <a href="http://webscan.360.cn/index/checkwebsite/url/dianying8.tv"><img border="0" src="http://img.webscan.360.cn/status/pai/hash/d1bf605d9bdec73cb115ad967bac7918"/></a>
</div>
<!-- 百度流量统计js end-->
</body>
</html>
