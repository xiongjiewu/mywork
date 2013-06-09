<!DOCTYPE html>
<html lang="en">
<head>
    <?php $this->load->view("component/headerhtml");//页面标题，js，css?>
</head>
<body>
<div class="main" id="total_info_main">
    <div class="container">
        <?php $view = $this->load->get_view();?>
        <?php if (!empty($data)):?>
            <?php $this->load->view($view,$data);?>
        <?php else:?>
            <?php $this->load->view($view);?>
        <?php endif;?>
    </div>
</div>
<?php if ($this->load->get_login_pan()):?>
    <?php $this->load->view("component/loginpan");//登录框?>
<?php endif;?>
<?php $this->load->view("component/footerhtml");//底部相关?>
</body>
</html>
