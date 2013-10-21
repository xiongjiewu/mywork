<div class="bs-docs-example">
    <?php if (!empty($success)):?>
        <div style="color: #ff0000">
            发表成功！
        </div>
    <?php endif;?>
    <form method="post" action="" name="create_post" id="create_post">
        标题：<input name="title" id="title" type="text">
        <br>
        <textarea class="xheditor" name="content" id="content"></textarea>
        <p></p>
        <input type="submit" id="create_post_button" class="btn btn-large btn-primary" value="发表"><span
            style="color: #aaa">（ctrl+enter快捷回复）</span>
        <br>
    </form>
</div>
<script type="text/javascript">
    var editor = $('.xheditor').xheditor(
        {
            tools: 'Cut,Copy,Paste,Pastetext,|,Fontface,FontSize,Bold,Italic,Underline',
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
            $("#create_post").submit(function () {
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