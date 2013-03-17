var init = {
    post_submit:function(editor){
        var content = editor.getSource();
        if (!content || (content == undefined)) {
            alert("请输入内容!");
            return false;
        }
        return true;
    },
    reply:function(t,editor) {
        var p = $(t).parent();
        var pF = p.prev();
        var userName = pF.find("a.user_name").html();
        var lou = $(t).next().html();
        editor.setSource("回复"+lou+"的"+userName+":");
        editor.focus();
        window.location.href = "#createpost";
    },
    ding:function(t) {
        var pid = $(t).attr("pid");
        if (pid && (pid != undefined)) {
            var url = $("#ding_url").val();
            var ding = $(t).find(".up_cnt");
            var count = parseInt(ding.html());
            $.ajax({
                url:url,
                type:"post",
                data:{pid:pid},
                success:function(result) {
                    ding.html(count+1);
                }
            })
        }
        return true;
    }
};