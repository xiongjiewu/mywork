(function($){
    $(document).ready(function(){
        $("div.search_dy_info ul li").each(function(){
            var aObj = $(this).find("a");
            var url = $(aObj.get(0)).attr("href");
            $(this).bind("click",function(){
                window.location.href = url;
            });
            aObj.each(function() {
                if ($(this).attr("href")) {
                    $(this).bind("click",function(event){
                        event.stopPropagation();
                    });
                }
            })
        });
    })
})(jQuery)