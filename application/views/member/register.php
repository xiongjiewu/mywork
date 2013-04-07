<div class="doing"></div>
<div class="register_main">
    <div class="register_top_top"></div>
    <form action="" id="register_form" method="post" onsubmit="return false;">
        <div class="register_top_middel">
            <div class="register_main_table">
                <table>
                    <tr><th colspan="2">注册<?php echo get_config_value("base_name");?></th></tr>
                    <tr><td class="ui_error"></td></tr>
                    <tr>
                        <td class="user_input">
                            <span class="username_icon" c="username_icon_over"></span>
                            <label>登录账号</label>
                            <input type="text" class="username" name="user" maxlength="20">
                        </td>
                        <td>
                            <div class="username_ts register_error">
                                <span class="username_ts_top"></span>
                                <span class="username_ts_main">
                                    用户名只能由中英文、数字和下划线“_”组成，2--20个字符
                                </span>
                            </div>
                            <div class="register_error1"></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="user_input">
                            <span class="email_icon" c="email_icon_over"></span>
                            <label>安全邮箱</label>
                            <input type="text" class="username" name="email">
                        </td>
                        <td>
                            <div class="email_ts register_error">
                                <span class="email_ts_top"></span>
                                <span class="email_ts_main">
                                    请输入正确的安全邮箱，不需验证，用于找回密码、接收电影订阅通知等
                                </span>
                            </div>
                            <div class="register_error1">

                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="user_input">
                            <span class="password_icon" c="password_icon_over"></span>
                            <label>登录密码</label>
                            <input type="password" class="password" name="pass1" maxlength="20">
                        </td>
                        <td>
                            <div class="password_ts register_error">
                                <span class="password_ts_top"></span>
                                <span class="password_ts_main">
                                    6--20个字符，请勿使用非法字符及空格
                                </span>
                            </div>
                            <div class="register_error1">

                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="user_input">
                            <span class="password_icon" c="password_icon_over"></span>
                            <label>确认密码</label>
                            <input type="password" class="password" name="pass2" maxlength="20">
                        </td>
                        <td>
                            <div class="pass2_ts register_error">
                                <span class="pass2_ts_top"></span>
                                <span class="pass2_ts_main">
                                    6--20个字符，切记与登录密码保持一致
                                </span>
                            </div>
                            <div class="register_error1">

                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="pass2_ts register_error">
                                <span class="pass2_ts_top"></span>
                                <span class="pass2_ts_main">
                                    6--20个字符，切记与登录密码保持一致
                                </span>
                            </div>
                            <div class="register_error1">

                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="user_input">
                            <span class="code_icon" c="code_icon_over"></span>
                            <label>验证码</label>
                            <input type="text" class="username re_code" name="code" maxlength="4">
                            <span>
                                <img src="<?php echo get_url("/codeimg/");?>" class="code_img" border="none">
                            </span>
                            <em><a href="javascript:void(0);" class="change">换一张</a></em>
                        </td>
                        <td>
                            <div class="code_ts register_error">
                                <span class="code_ts_top"></span>
                                <span class="code_ts_main">
                                    请输入如图所示的验证码，不区分大小写
                                </span>
                            </div>
                            <div class="register_error1">

                            </div>
                        </td>
                    </tr>
                    <tr><td class="ui_register"><input type="submit" class="submit" name="register_submit" value="注&nbsp;册"></td></tr>
                    <tr>
                        <td class="ui_remember">
                            <input type="checkbox" class="remember" name="register_check" checked="checked">同意《<a href="<?php echo get_url("/topic/index/1/");?>">电影吧网络协议</a>》
                        </td>
                    </tr>
                </table>
            </div>
            <div class="register_main_right">
                <table>
                    <tr>
                        <td>
                            已有<?php echo get_config_value("base_name");?>账号,立即登录
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a class="jump_login" href="<?php echo get_url("/login/")?>">
                                <i></i>
                                登录
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </form>
    <div class="register_top_bottom"></div>
</div>