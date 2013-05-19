var init = {
    post_submit:function(editor){
        var title = $.trim($("#title").val());
        var content = editor.getSource();
        if (!title || (title == undefined)) {
            alert("请输入标题!");
            return false;
        }
        if (!content || (content == undefined)) {
            alert("请输入内容!");
            return false;
        }
        return true;
    }
};