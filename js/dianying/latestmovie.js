(function($){
    $(document).ready(function(){
        var daohangObj = $("ul.dy_bs-docs-sidenav");
        daohangObj.bind("mouseover",function(){
            $(this).addClass("bs-docs-sidenav-over");
        });
        daohangObj.bind("mouseleave",function(){
            $(this).removeClass("bs-docs-sidenav-over");
        });
    });
})(jQuery);