<div class="content">
    <div class="password_main">
        <div class="error_img">
            <div class="error_mes">
                <i></i>
                一封包含"重新设置密码" 链接的email已经发送到你的邮箱：<span><?php echo $email;?></span>，请在<?php echo APF::get_instance()->get_config_value("changepassword_max_time") / 60;?>分钟内完成密码更改！
            </div>
            <?php if (!empty($emailUrl)):?>
                <a class="login_url" href="<?php echo $emailUrl;?>" target="_blank"></a>
            <?php endif;?>
            <div class="other">
                <table>
                    <tr>
                        <th>没有收到激活邮件？</th>
                    </tr>
                    <tr>
                        <td>1. 如果没有收到<?php echo APF::get_instance()->get_config_value("base_name");?>系统发送的email，请去垃圾邮件目录里找找看。</td>
                    </tr>
                    <tr>
                        <td>2. 还是没有，在途中？等不及， <a href="<?php echo get_url("/password?r=" . time());?>">返回重新申请</a></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
