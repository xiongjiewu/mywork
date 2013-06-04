<div class="user_main">
<div class="row">
    <?php $this->load->view("component/usercenterleft",array("userInfo" =>$userInfo,"index"=>($type =="want") ?3 : 5));?>
    <div class="right_container">
        <div class="main-tab">
            <?php if ($type =="want"):?>
                <a href="<?php echo get_url("/usercenter/feedback/want/")?>">我的反馈</a>
            <?php else:?>
                <a  href="<?php echo get_url("/usercenter/feedback/suggest/")?>">投诉与建议</a>
            <?php endif;?>
            <a class="tab-focus" href="<?php echo get_url("/usercenter/editfeedback/{$type}/{$feedbackInfo['id']}/")?>">编辑</a>
        </div>
        <div id="usermain">
            <div class="show mod-dist-r">
                <div class="modbox2">
                    <form id="edit_feedback" name="edit_feedback" action="<?php echo get_url("/usercenter/editfeedbacksubmit/");?>" method="post">
                    <table class="edit_xheditor_table">
                        <tr>
                            <td>
                                <code>*</code>标题：
                            </td>
                            <td>
                                <input type="text" name="title" id="title" value="<?php echo $feedbackInfo['title'];?>">
                            </td>
                            <td class="error_text">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <code>*</code>内容：
                            </td>
                            <td class="edit_xheditor">
                                <input type="hidden" name="id" id="id" value="<?php echo $feedbackInfo['id'];?>">
                                <input type="hidden" name="type" id="type" value="<?php echo $type;?>">
                                <textarea class="xheditor" name="content" id="content"><?php echo $feedbackInfo['content'];?></textarea>
                                <p></p>
                                <input type="submit" id="create_post_button" class="btn btn-primary" value="提交编辑"><span
                                    style="color: #aaa">（ctrl+enter快捷回复）</span>
                            </td>
                            <td class="error_text">
                            </td>
                        </tr>
                    </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<script type="text/javascript">
    var editor = $('.xheditor').xheditor(
        {
            tools: 'Cut,Copy,Paste,Pastetext,|,Emot,Fontface,FontSize,Bold,Italic,Underline,Link',
            skin: 'vista',
            showBlocktag: true,
            internalScript: true,
            internalStyle: true,
            width: 780,
            height: 200,
            fullscreen: false,
            sourceMode: false,
            forcePtag: true,
            emotMark: false,
            shortcuts: {'ctrl+enter': function () {
                return $("#create_post_button").trigger("click");
            }}
        }
    );
    (function ($) {
        $(document).ready(function () {
            editor.addShortcuts("ctrl+enter");
            $("#edit_feedback").submit(function () {
                return init.post_submit(editor);
            });
            $(document).keydown(function (event) {
                event = event || window.event;
                var e = event.keyCode || event.which;
                if (e == 13 && event.ctrlKey == true) {
                    return $("#create_post_button").trigger("click");
                }
            });
        })
    })(jQuery);
</script>