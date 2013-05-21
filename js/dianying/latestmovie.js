var init = {
    daoHangDingWei:function() {
        var dyInfoObj = $("div.lastmovice_list");
        var daohangObj = $("ul.time_daohang_list");
        var daohangTop = daohangObj.offset().top,id;
        dyInfoObj.find("ul.info_list_do").each(function(){
            var dyInfoTop = $(this).offset().top;
            var dyInfoH = $(this).height();
            if (parseInt(daohangTop) >= (parseInt(dyInfoTop) - 130) && parseInt(daohangTop) <= (parseInt(dyInfoTop) + parseInt(dyInfoH))) {
                id = $(this).attr("id");
            }
        });
        daohangObj.find("li.current").removeClass("current");
        daohangObj.find("li[name='"+id+"']").addClass("current");
    },
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
    },
    showWatchAndDownLink:function(obj){
        var p = obj.find("div.dy_link_down");
        if (p && (p != undefined)) {
            p.show();
            p.animate({right:"0"});
        }
    },
    hideWatchAndDownLink:function(obj){
        var p = obj.find("div.dy_link_down");
        if (p && (p != undefined)) {
            p.css("background-color","#fff");
            p.animate({right:"-48%"},"fast");
            p.css("background-color","#E0EEEE");
            p.hide();
        }
    }
};