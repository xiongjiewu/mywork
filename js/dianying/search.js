(function($){
    var init = {
        tableOver:function(obj) {

        }
    };
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
        })
    })
})(jQuery);