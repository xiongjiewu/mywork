<div class="row">
    <?php $this->load->view("component/usercenterleft",array("userInfo" =>$userInfo,"index"=>($type =="want") ?3 : 5));?>
    <div class="right_container">
        <div class="main-tab">
            <?php if ($type =="want"):?>
                <a href="<?php echo get_url("/usercenter/feedback/want/")?>">我的反馈</a>
            <?php else:?>
                <a  href="<?php echo get_url("/usercenter/feedback/suggest/")?>">投诉与建议</a>
            <?php endif;?>
            <a class="tab-focus" href="javascript:void(0);" style="cursor: default"><?php echo $text;?></a>
        </div>
        <div id="usermain">
            <div class="show mod-dist-r">
                <div class="modbox2" style="border: none;">
                    <table class="table edit_success">
                        <tr>
                            <td class="image"><img src="/images/re_su.png"></td>
                            <td class="text">恭喜您，<?php echo $text;?>。感谢您的反馈，我们会在1-2天内通过系统消息答复您，敬请关注!</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>