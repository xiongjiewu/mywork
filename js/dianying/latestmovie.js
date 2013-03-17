(function($){
    $(document).ready(function(){
        $("div.dy_detail_info_about").each(function(){
            $(this).bind("mouseover",function(){
                $(this).addClass("on");
            });
            $(this).bind("mouseleave",function(){
                $(this).removeClass("on");
            });
            $(this).bind("click",function(){
                var url = $(this).find("a.name").attr("href");
                window.location.href = url;
            });
            $(this).find("a").each(function(){
                $(this).click(function(event){
                    event.stopPropagation();
                });
            });
        });
    });
})(jQuery);