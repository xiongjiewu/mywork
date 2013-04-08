<div class="login_register_all"></div>
<div class="login_ui login_pan_ui">
    <div class="login_ui_title">
        <table>
            <tr>
                <td class="l_t">登录<?php echo get_config_value("base_name");?></td>
                <td class="l_c close_login_ui"></td>
            </tr>
        </table>
    </div>
    <form name="login_pan" onsubmit="return false" action="" id="login_pan" method="post">
        <div class="login_ui_table">
            <table>
                <tr><td class="ui_error loginpan_error"></td></tr>
                <tr>
                    <td class="user_input">
                        <span class="username_icon" c="username_icon_over"></span>
                        <label>登录账号</label>
                        <input type="text" class="username" name="username">
                    </td>
                </tr>
                <tr>
                    <td class="user_input">
                        <span class="password_icon" c="password_icon_over"></span>
                        <label>登录密码</label>
                        <input type="password" class="password" name="password">
                    </td>
                </tr>
                <tr><td class="ui_remember"><input type="checkbox" name="checkbox" class="remember">记住登录状态&nbsp;&nbsp;<a href="/password?r=<?php echo time();?>">忘记密码？</a></td></tr>
                <tr><td class="ui_login"><input type="submit" class="submit" name="login_submit" value="登&nbsp;录"></td></tr>
            </table>
        </div>
    </form>
    <div class="login_ui_other">
        <table>
            <tr class="login_ui_border">
                <td style="text-align: center;padding-bottom: 20px">还没有<?php echo get_config_value("base_name");?>账号？<a href="/register/" class="register">马上注册</a></td>
            </tr>
        </table>
    </div>
</div>