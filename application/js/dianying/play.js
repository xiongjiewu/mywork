var init = {
    loginCallBack:function() {
        window.location.reload();
    }
};
(function($) {
    $(document).ready(function() {
        var aPlay = $("a.play_button");
        var playLeft = $("div.play_left");
        aPlay.bind("click",function() {
            if (aPlay.hasClass("hide")) {
                playLeft.animate({left:"0"},500,function() {
                    aPlay.removeClass("hide");
                });
            } else {
                playLeft.animate({left:"-143px"},500,function() {
                    aPlay.addClass("hide");
                });
            }

        });
//        //登录按钮点击事件
//        $("a.play_login").bind("click",function() {
//            logPanInit.showLoginPan("init.loginCallBack");
//        });
    })
})(jQuery);