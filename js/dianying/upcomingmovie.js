var init = {
    changeNoticeBtn:function(obj)
    {
        obj.removeClass("dy_notic").addClass("dy_notic_btn");
        obj.html('<i class="icon-check icon-white"></i>已订阅观看通知');
        return true;

    },
    ajaxInertNotice:function(obj)
    {
        var id = obj.attr("val");
        if (id) {
            $.ajax({
                url:"/useraction/insertnotice/",
                type:"post",
                data:{id:id},
                dataType:"json",
                success:function(result){
                    if (result.code && result.code == "error") {
                        alert(result.info);
                    } else {
                        init.changeNoticeBtn(obj);
                    }
                }
            });
        }
    }
};