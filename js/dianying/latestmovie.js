(function($){
    var init = {
        daoHangDingWei:function() {
            var dyInfoObj = $("div.dy_total");
            var daohangObj = $("ul.dy_bs-docs-sidenav");
            var daohangTop = daohangObj.offset().top,id;
            console.log(daohangTop);
            dyInfoObj.find("div.bs-docs-example").each(function(){
                var dyInfoTop = $(this).offset().top;
                console.log(dyInfoTop);
                var dyInfoH = $(this).height();
                console.log(dyInfoH);
                if (parseInt(daohangTop) >= (parseInt(dyInfoTop) - 50) && parseInt(daohangTop) <= (parseInt(dyInfoTop) + parseInt(dyInfoH))) {
                    id = $(this).attr("id");
                }
            });
            daohangObj.find("li a.click").removeClass("click");
            daohangObj.find("li a[name='"+id+"']").addClass("click");
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
        var dyInfoLiObj = $("div.dy_total ul li");
        dyInfoLiObj.each(function(){
            $(this).bind("mouseover",function(){
                $(this).addClass("li_over");
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
            });
        });
        $(window).bind("scroll", function() {//当滚动条滚动时
            init.daoHangDingWei();
        });
    });
})(jQuery);