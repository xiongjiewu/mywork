var logPanInit = {
    showLoginPan:function(callBack) {
        $("div.login_register_all").show();
        var obj = $("div.login_pan_ui");
        obj.css("top",((jQuery(window).height()-obj.height())/2)+"px");
        obj.css("left",((jQuery(window).width()-obj.width())/2)+"px");
        obj.css("display","block");
        if (callBack) {
            $("#login_call_back").val(callBack);
        }
        return true;
    },
    hideLoginPan:function() {
        $("div.login_register_all").hide();
        $("div.login_pan_ui").hide();
        return true;
    }
};
(function ($) {
    $(document).ready(function () {
        var userpasObj = $("input[name='username'],input[name='password']");
        //将登录朦胧效果高度设置为整个页面的高度
        var mainHeight = $("#total_info_main").height() + $("#footer_main_total").height() + 40;
        $("div.login_register_all").css("height",mainHeight + "px");
        userpasObj.val("");
        userpasObj.focus(function () {
            $(this).addClass("input_over");
            $(this).prev().hide();
            var c = $(this).prev().prev().attr("c");
            $(this).prev().prev().addClass(c);
        });
        userpasObj.blur(function () {
            $(this).removeClass("input_over");
            var val = $.trim($(this).val());
            if (!val) {
                $(this).prev().show();
            }
            var c = $(this).prev().prev().attr("c");
            $(this).prev().prev().removeClass(c);
        });
        var loginSubmitObj = $("input[name='login_submit']");
        loginSubmitObj.mouseover(function () {
            $(this).addClass("submit_over");
        });
        loginSubmitObj.mouseleave(function () {
            $(this).removeClass("submit_over");
        });
        $("#login_pan").submit(function () {
            var username = $.trim($("input[name='username']").val());
            var password = $.trim($("input[name='password']").val());
            if (!username || !password) {
                $("td.loginpan_error").html("登录邮箱或密码不能为空！");
            } else {
                var reg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
                if (!reg.test(username)) {
                    $("td.loginpan_error").html("登录邮箱格式不正确");
                    return false;
                }
                $("input[name='login_submit']").attr("disabled",true);
                $("div.doing").show();
                var remember = 0;
                if ($("input[name='checkbox']").attr("checked")) {
                    remember = 1;
                }
                $.ajax({
                    type: "post",
                    url: "/loginaction/login/",
                    data: {username: username, password: password, remember: remember},
                    dataType: "json",
                    success: function (result) {
                        $("input[name='login_submit']").attr("disabled",false);
                        $("div.doing").hide();
                        if ((result.code == "error")) {
                            $("td.loginpan_error").html(result.info);
                        } else {
                            logPanInit.hideLoginPan();
                            var callBack = $("#login_call_back").val();//回调函数
                            if (callBack) {
                                eval(callBack+"()");
                            }
                        }
                    }
                });
            }
            return false;
        });
        $(".close_login_ui").bind("click",function(){
            logPanInit.hideLoginPan();
        });
    });
})(jQuery);