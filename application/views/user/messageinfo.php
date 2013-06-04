<div class="user_main">
<div class="row">
    <?php $this->load->view("component/usercenterleft",array("userInfo" =>$userInfo,"index"=>0));?>
    <div class="right_container">
        <div class="main-tab">
            <a href="<?php echo get_url("/usercenter/message/")?>">我的消息</a>
            <a class="tab-focus" href="<?php echo get_url("/usercenter/messageinfo/{$messageInfo['id']}/")?>">消息详情</a>
        </div>
        <div id="usermain">
            <div class="show mod-dist-r">
                <div class="modbox2">
                    <table class="table">
                        <tr>
                            <td style="text-align: left;border: none">
                                <?php echo $messageInfo['content'];?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>