(function($){
    $(document).ready(function(){
        $("div.search_dy_info ul li").each(function() {
            var aObj = $(this).find("a");
            var url = $(aObj.get(0)).attr("href");
            $(this).bind("click",function() {
                if (!$(this).hasClass("no_data")) {
                    window.location.href = url;
                }
            });
            aObj.each(function() {
                if ($(this).attr("href")) {
                    $(this).bind("click",function(event){
                        event.stopPropagation();
                    });
                }
            })
        });
        //人物点击跳转
        var peopleObj = $("div.peopel_info_list");
        var aObj = peopleObj.find("a");
        var url = $(aObj.get(0)).attr("href");
        peopleObj.bind("click",function() {
            window.location.href = url;
        });
        aObj.each(function() {
            if ($(this).attr("href")) {
                $(this).bind("click",function(event){
                    event.stopPropagation();
                });
            }
        })
    })
})(jQuery)