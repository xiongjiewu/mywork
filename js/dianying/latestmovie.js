(function($){
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
        }
    };
    $(document).ready(function(){
        init.daoHangDingWei();
        var daohangObj = $("ul.dy_bs-docs-sidenav");
        daohangObj.bind("mouseover",function(){
            $(this).addClass("bs-docs-sidenav-over");
        });
        daohangObj.bind("mouseleave",function(){
            $(this).removeClass("bs-docs-sidenav-over");
        });
        daohangObj.find("li a").each(function(){
            $(this).bind("click",function(){
                daohangObj.find("li a.click").removeClass("click");
                $(this).addClass("click");
            });
        });
        var dyInfoLiObj = $("li.dy_info_li");
        dyInfoLiObj.each(function(){
            $(this).bind("mouseover",function(){
                $(this).addClass("li_over");
                $(this).find("span.shoucang_action").show();
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
        dyInfoLiObj.each(function(){
            $(this).bind("mouseleave",function(){
                $(this).removeClass("li_over");
                $(this).find("span.shoucang_action").hide();
            });
        });
        daohangObj.find("a").each(function(){
            $(this).bind("click",function(){
                var name = $(this).attr("name");
                var sH = $("#"+name+"").offset().top;
                $(window).scrollTop(sH - 50);
            });
        });
        var shoucangObj = $("span.shoucang_dy");
        shoucangObj.each(function(){
            $(this).bind("click",function(event){
                init.ajaxShouCang($(this));
                event.stopPropagation();
            });
        });
        $(window).bind("scroll", function() {//当滚动条滚动时
            init.daoHangDingWei();
        });
    });
})(jQuery);