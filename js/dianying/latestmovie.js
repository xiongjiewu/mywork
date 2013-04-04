var init = {
    daoHangDingWei:function() {
        var dyInfoObj = $("div.dy_total");
        var daohangObj = $("ul.dy_bs-docs-sidenav");
        var daohangTop = daohangObj.offset().top,id;
        dyInfoObj.find("div.bs-docs-example").each(function(){
            var dyInfoTop = $(this).offset().top;
            var dyInfoH = $(this).height();
            if (parseInt(daohangTop) >= (parseInt(dyInfoTop) - 50) && parseInt(daohangTop) <= (parseInt(dyInfoTop) + parseInt(dyInfoH))) {
                id = $(this).attr("id");
            }
        });
        daohangObj.find("li a.click").removeClass("click");
        daohangObj.find("li a[name='"+id+"']").addClass("click");
    },
    ajaxShouCang: function (obj) {
        var id = obj.attr("val");
        if (id && (id != undefined)) {
            $.ajax({
                url: "/useraction/shoucang/",
                type: "post",
                data: {id: id},
                dataType: "json",
                success: function (result) {
                    if (result.error == "error") {
                        alert(result.info);
                    } else {
                        obj.removeClass("shoucang_dy").addClass("shoucang_dy_y");
                        obj.attr("title","已收藏");
                    }
                }
            })
        }
    },
    showWatchAndDownLink:function(obj){
        var p = obj.find("div.dy_link_down");
        p.show();
        p.animate({right:"0"});
    },
    hideWatchAndDownLink:function(obj){
        var p = obj.find("div.dy_link_down");
        p.css("background-color","#fff");
        p.animate({right:"-137px"},function(){
            p.css("background-color","#E0EEEE");
            p.hide();
        });
    }
};