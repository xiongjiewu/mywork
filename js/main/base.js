(function($){
    function show_ui(obj){
        if(!obj) return false;
        var j = obj;
        j.css("top",((jQuery(window).height()-j.height())/2)+"px");
        j.css("left",((jQuery(window).width()-j.width())/2)+"px");
        j.css("display","block");
    }
    function show_loginpan(){
        $("td.loginpan_error").html("");
        $(".login_register_all").show();
        $("#login_ui_pan").show();
        show_ui($(".login_ui"));
    }
    function hide_loginpan(){
        $("#login_ui_pan").hide();
        $(".login_register_all").hide()
    }
    $(document).ready(function(){
        $(".show_login_ui").bind("click",show_loginpan);
        $("td.close_login").bind("click",hide_loginpan);
    });
})(jQuery);