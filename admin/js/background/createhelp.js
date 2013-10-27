;
(function ($) {
    var init = {
        post_submit:function(editor){
            var title = $.trim($("#title").val());
            var content = editor.getSource();
            if (!title || (content == title) || !content || (content == undefined)) {
                alert("标题或内容不能为空!");
                return false;
            }
            return true;
        }
    };
    $(document).ready(function () {
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
            return true;
        });
    })
})(jQuery);