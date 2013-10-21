;
(function($){
    $(document).ready(function(){
        var liObj = $("ul.menus_list li");
        liObj.each(function() {
            var tObj = $(this);
            var listInfoObj = tObj.find("div.menus_info_list");//查找子menu
            var c = "";
            if (tObj.hasClass("active")) {
                c = "active";
            }
            if (listInfoObj.length > 0) {//是否存在子menu
                tObj.bind("mouseover",function() {
                    tObj.removeClass(c).addClass("mouse_over");
                    listInfoObj.show();
                });
                tObj.bind("mouseleave",function() {
                    tObj.removeClass("mouse_over").addClass(c);
                    listInfoObj.hide();
                });
                listInfoObj.bind("mouseover",function() {
                    tObj.addClass("mouse_over");
                    $(this).show();
                });
                listInfoObj.bind("mouseleave",function() {
                    tObj.removeClass("mouse_over");
                    $(this).hide();
                });
            }
        })
    })
})(jQuery);