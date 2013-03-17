<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php if ($this->load->get_title()):?><?php echo $this->load->get_title();?><?php else:?>电影吧<?php endif;?></title>
    <link rel="stylesheet" rev="stylesheet" href="/css/main/main.css" type="text/css" />
    <?php $css = $this->load->get_css();?>
    <?php if (!empty($css)):?>
        <?php foreach($css as $cssKey => $cssVal):?>
        <link rel="stylesheet" rev="stylesheet" href="/<?php echo $cssVal;?>" type="text/css" />
        <?php endforeach;?>
    <?php endif;?>
    <script type="text/javascript" src="/js/main/jquery-1.7.2.js"></script>
    <script type="text/javascript" src="/js/main/base.js"></script>
    <?php $js = $this->load->get_js();?>
    <?php if (!empty($js)):?>
    <?php foreach($js as $jsKey => $jsVal):?>
        <script type="text/javascript" src="/<?php echo $jsVal;?>"></script>
    <?php endforeach;?>
    <?php endif;?>
</head>
<body>
<div class="main">
    <div class="top">
        <div class="logo">

        </div>
        <div class="middel">

        </div>
        <div class="right">

        </div>
    </div>
    <div class="menus">
        <ul>
            <?php $menus = get_config_value("menus")?>
            <?php $index = $this->load->get_top_index();?>
            <?php foreach($menus as $menuKey => $menuVal):?>
                <li <?php if ($index == $menuKey):?>class="current"<?php endif;?>><a href="<?php echo $menuVal['link'];?>"><?php echo $menuVal['titlle'];?></a></li>
            <?php endforeach;?>
            <li class="search">
                <form name="search_dy" id="search_dy" action="">
                <input type="text" class="search_value" name="search" id="search" value="搜索您喜欢的影片...">
                <input type="submit" class="submit" name="search_submit" id="search_submit" value="">
                </form>
            </li>
            <li class="login_register">
                <a class="show_login_ui" href="<?php echo get_url("/login/");?>">登录</a>&nbsp;&nbsp;
                <a href="<?php echo get_url("/register/");?>">注册</a>
            </li>
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
<script type="text/javascript">
(function($){
    $(document).ready(function(){
        $("#search").bind("focus",function(){
            var search_val = $.trim($("#search").val());
            if (search_val == "搜索您喜欢的影片...") {
                $("#search").val("");
            }
        });
        $("#search").bind("blur",function(){
            var search_val = $.trim($("#search").val());
            if (!search_val || search_val == undefined) {
                $("#search").val("搜索您喜欢的影片...");
            }
        });
        $("input[name='username'],input[name='password']").val("");
        $("input[name='username'],input[name='password']").focus(function(){
            $(this).addClass("input_over");
            $(this).prev().hide();
            var c = $(this).prev().prev().attr("c");
            $(this).prev().prev().addClass(c);
        });
        $("input[name='username'],input[name='password']").blur(function(){
            $(this).removeClass("input_over");
            var val = $.trim($(this).val());
            if(!val){
                $(this).prev().show();
            }
            var c = $(this).prev().prev().attr("c");
            $(this).prev().prev().removeClass(c);
        });
        $("input[name='login_submit']").mouseover(function(){
            $(this).addClass("submit_over");
        });
        $("input[name='login_submit']").mouseleave(function(){
            $(this).removeClass("submit_over");
        });
        $("#").submit(function(){
            var username = $.trim($("input[name='username']").val());
            var password = $.trim($("input[name='password']").val());
            if(!username || !password){
                $("td.loginpan_error").html("账号(邮箱)或密码不能为空！");
            }else{
                username = base64encode(username);
                password = hex_md5(password);
                password = base64encode(password);
                var remember = 0;
                if($("input[name='checkbox']").attr("checked")){
                    remember = 1;
                }
                $.ajax({
                    type:"post",
                    url:"/ajax/login/",
                    data:{username:username,password:password,remember:remember},
                    dataType:"json",
                    success: function(result) {
                        if((result.code == "error") || (result.code == "illegal")){
                            $("td.loginpan_error").html(result.info);
                        }else{
                            location.reload();
                        }
                    }
                });
            }
            return false;
        });
    });
})(jQuery);
</script>
</body>
</html>