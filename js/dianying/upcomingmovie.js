var init = {
    changeNoticeBtn:function(obj) {
        obj.removeClass("dy_notic").addClass("dy_notic_btn");
        obj.html('<i class="icon-check icon-white"></i>已订阅观看通知');
        return true;

    },
    loginCallBack:function(){
        var id = $("#current_id").val();
        if (id) {
            this.ajaxInertNotice(id,function(result){
                if (result.code && result.code == "error") {
                    alert(result.info);
                } else {
                    window.location.reload();
                }
            });
        }
    },
    ajaxInertNotice:function(id,callBack) {
        $.ajax({
            url:"/useraction/insertnotice/",
            type:"post",
            data:{id:id},
            dataType:"json",
            success:function(result){
                if (callBack) {
                    callBack(result);
                }
            }
        });
    },
    insertNoticeDo:function(obj,event) {
        var id = obj.attr("val");
        this.ajaxInertNotice(id,function(result){
            if (result.code && result.code == "error") {
                alert(result.info);
            } else {
                init.changeNoticeBtn(obj);
            }
        });
        event.stopPropagation();
    }
};