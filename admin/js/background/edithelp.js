;
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