<div class="row">
    <?php $this->load->view("component/usercenterleft",array("userInfo" =>$userInfo,"index"=>0));?>
    <div class="right_container">
        <div class="main-tab">
            <a <?php if ($type =="data"):?>class="tab-focus"<?php endif;?> href="<?php echo get_url("/usercenter/revised/data/")?>">基本资料</a>
            <a <?php if ($type =="picture"):?>class="tab-focus"<?php endif;?> href="<?php echo get_url("/usercenter/revised/picture/")?>">更换头像</a>
            <a <?php if ($type =="password"):?>class="tab-focus"<?php endif;?> href="<?php echo get_url("/usercenter/revised/password/")?>">修改密码</a>
        </div>
        <div id="usermain">
            <div class="show mod-dist-r">
                <div class="modbox2">
                    <?php if ($type == "data"):?>
                        <table class="change_pass_table">
                            <tr>
                                <td>
                                    <code>*</code>安全邮箱：
                                </td>
                                <td>
                                    <input type="text" name="email" id="email" value="<?php echo $userInfo['email'];?>">
                                </td>
                                <td class="error_text">
                                </td>
                            </tr>
                            <tr class="submit">
                                <td colspan="2">
                                    <input style="margin-left: 86px;" type="button" name="change_email" id="change_email" value="确认更改" class="btn btn-primary">
                                </td>
                            </tr>
                        </table>
                    <?php elseif ($type =="picture"):?>
                    <div class="span9">
                        <div class="bs-docs-example">
                            <div class="user_image">
                                <img src="<?php echo trim(get_config_value("img_base_url"),"/") . $userInfo['photo'];?>">
                                <div class="doing"></div>
                                <span class="btn shangchuan">上传头像</span>
                                <span class="btn upload">上传</span>
                                <span class="btn cancel">取消</span>
                                <form name="userphone" id="userphone" method="post" action="<?php echo rtrim(get_url(get_config_value("image_upload_url")),"/") . "/index/{$userId}/user";?>" autocomplete="off" enctype="multipart/form-data">
                                    <input type="file" name="image" id="image">
                                    <input type="hidden" name="moren_img" id="moren_img" value="<?php echo trim(get_config_value("img_base_url"),"/") . $userInfo['photo'];?>">
                                    <input type="hidden" name="userphoto" id="userphoto" value="">
                                    <iframe name="upload_frame" id="upload_frame" style="width:0;height:0;display:none;" ></iframe>
                                    <input type="submit" name="submit" id="submit" style="display: none;">
                                </form>
                            </div>
                            <div class="zhushi">
                                只能传格式为png、gif、jpg的图片,最大不能超过2M!
                            </div>
                        </div>
                    </div>
                    <?php else:?>
                        <table class="change_pass_table">
                            <tr>
                                <td>
                                    <code>*</code>旧密码：
                                </td>
                                <td>
                                    <input type="password" name="odlpass" id="oldpass" value="">
                                </td>
                                <td class="error_text">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <code>*</code>新密码：
                                </td>
                                <td>
                                    <input type="password" name="newpass1" id="newpass1">
                                </td>
                                <td class="error_text">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <code>*</code>再确认：
                                </td>
                                <td>
                                    <input type="password" name="newpass2" id="newpass2">
                                </td>
                                <td class="error_text">
                                </td>
                            </tr>
                            <tr class="submit">
                                <td colspan="3">
                                    <input type="button" name="action_change" id="action_change" value="确认更改" class="btn btn-primary">
                                </td>
                            </tr>
                        </table>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>
</div>