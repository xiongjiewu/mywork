(function($){
    $(document).ready(function() {
       var infoLiObj = $("div.class_main div.class_right li");
        infoLiObj.each(function() {
            var url = $($(this).find("a").get(0)).attr("href");
            $(this).bind("click",function() {
                window.location.href = url;
            });
            $(this).find("a").click(function(event) {
                event.stopPropagation();
            });
        })
    });
})(jQuery);