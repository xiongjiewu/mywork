(function ($) {
    $(document).ready(function () {
        var userpasObj = $("input[name='username'],input[name='password']");
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
        $("#login_form").submit(function () {
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
                            var bgurl = $("#bgurl").val();
                            if (bgurl && (bgurl != undefined)) {
                                window.location.href = bgurl;
                            } else {
                                window.location.href = "/usercenter/";
                            }
                        }
                    }
                });
            }
            return false;
        });

    });
})(jQuery);