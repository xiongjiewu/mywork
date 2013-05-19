var init = {
    post_submit: function (editor) {
        var content = editor.getSource();
        if (!content || (content == undefined)) {
            alert("请输入内容!");
            return false;
        }
        return true;
    }
}