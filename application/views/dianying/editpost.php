<div class="edit_post">
    <form name="create_post" id="create_post" method="post" action="">
        <textarea class="xheditor" name="content" id="content"><?php echo $YingpingInfo['content'];?></textarea>
        <p></p>
        <input type="submit" id="create_post_button" class="btn btn-large btn-primary" value="提交编辑"><span
            style="color: #aaa">（ctrl+enter快捷编辑）</span>
    </form>
</div>

<script type="text/javascript">
    var editor = $('.xheditor').xheditor(
        {
            tools: 'Cut,Copy,Paste,Pastetext,|,Flash,Media,Emot,Fontface,FontSize,Bold,Italic,Underline',
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
    (function($){
        $(document).ready(function(){
            editor.addShortcuts("ctrl+enter");
            $("#create_post").submit(function () {
                return init.post_submit(editor);
            });
        });
        $(document).keydown(function (event) {
            event = event || window.event;
            var e = event.keyCode || event.which;
            if (e == 13 && event.ctrlKey == true) {
                return $("#create_post_button").trigger("click");
            }
        });
    })(jQuery);
</script>