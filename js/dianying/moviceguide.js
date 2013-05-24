var init = {
    loginCallBack:function(){
        var id = $("#current_id").val();
        if (id) {
            this.ajaxShouCang(id,function(result){
                if (result.code && result.code == "error") {
                    alert(result.info);
                }
                window.location.reload();
            });
        }
    },
    ajaxShouCang: function (id,callBack) {
        if (id) {
            $.ajax({
                url: "/useraction/shoucang/",
                type: "post",
                data: {id: id},
                dataType: "json",
                success: function (result) {
                    if (callBack) {
                        callBack(result);
                    }
                }
            });
        }
    },
    shouCangDo:function(obj) {
        var id = obj.attr("val");
        this.ajaxShouCang(id,function(result){
            if (result.error == "error") {
                alert(result.info);
            } else {
                obj.removeClass("shoucang_dy").addClass("shoucang_dy_y");
                obj.attr("title","已收藏");
            }
        })
    }
};
(function($){
    $(document).ready(function(){
        var tableObj = $("div.span9 table.table-bordered");
        tableObj.each(function(){
            $(this).bind("mouseover",function(){
                $(this).addClass("table_over");
            });
            $(this).bind("mouseleave",function(){
                $(this).removeClass("table_over");
            });
            $(this).bind("click",function(){
               var url = $($(this).find("a").get(0)).attr("href");
                window.location.href = url;
            });
            $(this).find("a").each(function(){
                $(this).bind("click",function(event){
                    event.stopPropagation();
                });
            });
        });
        $("div.classmovice_list ul.list_info li").each(function() {
            $(this).bind("mouseover",function(){
                $(this).find("span.shoucang_action").show();
            });
            $(this).bind("mouseleave",function(){
                $(this).find("span.shoucang_action").hide();
            });
        });
    })
})(jQuery);