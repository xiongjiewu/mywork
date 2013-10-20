(function($){
    $(document).ready(function(){
        var moreA = $("a.more");
        moreA.bind("click",function() {
            var moreHtml = $(this).html();
            if (moreHtml == "更多") {
                $(this).html("收起");
                $(this).addClass("less");
                $(this).parent().find("li.more_li").show();
            } else {
                $(this).html("更多");
                $(this).removeClass("less");
                $(this).parent().find("li.more_li").hide();
            }
        });
    });
})(jQuery);