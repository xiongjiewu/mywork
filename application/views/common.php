<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="description" content="电影最齐全的网站，我们只专于电影，用户想看的就是我们的宗旨。在这里，您可以找到任何您想看的电影，各种超清电影提供中。"/>
    <title><?php if ($this->load->get_title()):?><?php echo $this->load->get_title();?><?php else:?>电影吧<?php endif;?></title>

    <link href="/css/main/bootstrap.css" rel="stylesheet">
    <link href="/css/main/base.css" rel="stylesheet">
    <link href="/css/main/base-res.css" rel="stylesheet">
    <link href="/css/main/docs.css" rel="stylesheet">
    <link href="/css/main/code.css" rel="stylesheet">
    <link href="/css/main/base.css" rel="stylesheet">
    <link href="/css/main/main.css" rel="stylesheet">
    <?php $css = $this->load->get_css();?>
    <?php if (!empty($css)):?>
    <?php foreach($css as $cssKey => $cssVal):?>
        <link rel="stylesheet" rev="stylesheet" href="/<?php echo trim($cssVal,"/");?>" type="text/css" />
        <?php endforeach;?>
    <?php endif;?>
    <script type="text/javascript" src="/js/main/jquery-1.7.2.js"></script>
    <?php $js = $this->load->get_js();?>
    <?php if (!empty($js)):?>
    <?php foreach($js as $jsKey => $jsVal):?>
        <script type="text/javascript" src="/<?php echo trim($jsVal,"/");?>"></script>
        <?php endforeach;?>
    <?php endif;?>
</head>
<body>
<div class="main">
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
</body>
</html>