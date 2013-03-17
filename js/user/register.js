(function ($) {
    var init = {
        check_mouseover:function (t) {
            var p = t.parent();
            var e = p.next();
            e.find("div.register_error").show();
        },
        check_mouseleave:function (t) {
            var p = t.parent();
            var e = p.next();
            e.find("div.register_error").hide();
        },
        check_focus:function (t) {
            var p = t.parent();
            var e = p.next();
            e.find("div.register_error1").removeClass("error1");
            e.find("div.register_error1").removeClass("error2");
            e.find("div.register_error1").addClass("error3");
            e.find("div.register_error1").html("");
        },
        error_common:function(e,title) {
            e.find("div.register_error1").addClass("error2");
            e.find("div.register_error1").html(title);
            return false;
        },
        check_blur:function (t) {
            var p = t.parent();
            var e = p.next();
            var title = t.prev().html();
            e.find("div.register_error").hide();
            var value = $.trim(t.val());
            e.find("div.register_error1").removeClass("error3");
            if (!value) {
                return this.error_common(e,title + "不能为空");
            } else {
                var name = t.attr("name");
                if (name == "user") {
                    if (value.length < 2) {
                        return this.error_common(e,title + "不能少于2个字符");
                    } else if (value.length > 20) {
                        return this.error_common(e,title + "不能超过20个字符");
                    } else {
                        var reg = /[^\u0391-\uFFE5\w_]/;
                        if (reg.test(value)) {
                            return this.error_common(e,title + "只能由中英文、数字和下划线组成");
                        } else {
                            return true;
                        }
                    }
                } else if (name == "email") {
                    var reg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
                    if (!reg.test(value)) {
                        return this.error_common(e,title + "格式不正确");
                    } else {
                        return true;
                    }
                } else if (name == "pass1") {
                    if (value.length < 6) {
                        return this.error_common(e,title + "不能少于6个字符");
                    } else {
                        e.find("div.register_error1").addClass("error1");
                        return true;
                    }
                } else if (name == "pass2") {
                    var v = $.trim($("input[name='pass1']").val());
                    if (v != value) {;
                        return this.error_common(e,title + "和登录密码不一致");
                    } else {
                        e.find("div.register_error1").addClass("error1");
                        return true;
                    }
                } else if (name == "code") {
                    if (!value) {
                        return this.error_common(e,title + "不能为空");
                    } else {
                        return true;
                    }
                }
            }
        },
        checkUsername:function (obj, callback) {//登录帐号检查
            if (!this.check_blur(obj)) {
                if (callback) {
                    callback(false);
                }
            } else {
                var username = obj.val();
                $.ajax({
                    type:"post",
                    data:{username:username},
                    url:"/resgiteraction/checkusername/",
                    dataType:"json",
                    success:function (result) {
                        var c;
                        var p = obj.parent();
                        var e = p.next();
                        if (result.code == "error") {
                            e.find("div.register_error1").addClass("error2");
                            e.find("div.register_error1").html(result.info);
                            c = false;
                        } else {
                            e.find("div.register_error1").addClass("error1");
                            c = true;
                        }
                        if (callback) {
                            callback(c);
                        }
                    }
                });
            }
        },
        checkEmail:function(obj, callback) {
            if (!this.check_blur(obj)) {
                if (callback) {
                    callback(false);
                }
            } else {
                var email = obj.val();
                $.ajax({
                    type:"post",
                    data:{email:email},
                    url:"/resgiteraction/checkemail/",
                    dataType:"json",
                    success:function (result) {
                        var c;
                        var p = obj.parent();
                        var e = p.next();
                        if ((result.code == "error")) {
                            e.find("div.register_error1").addClass("error2");
                            e.find("div.register_error1").html(result.info);
                            c = false;
                        } else {
                            e.find("div.register_error1").addClass("error1");
                            c = true;
                        }
                        if (callback) {
                            callback(c);
                        }
                    }
                });
            }
        },
        checkCode:function(obj, callback) {
            if (!this.check_blur(obj)) {
                if (callback) {
                    callback(false);
                }
            } else {
                var code = obj.val();
                $.ajax({
                    type:"post",
                    data:{code:code},
                    url:"/resgiteraction/checkcode/",
                    dataType:"json",
                    success:function (result) {
                        var c;
                        var p = obj.parent();
                        var e = p.next();
                        if ((result.code == "error")) {
                            e.find("div.register_error1").addClass("error2");
                            e.find("div.register_error1").html(result.info);
                            c = false;
                        } else {
                            e.find("div.register_error1").addClass("error1");
                            c = true;
                        }
                        if (callback) {
                            callback(c);
                        }
                    }
                });
            }
        },
        resgiterAction:function(username,email,password1,password2,code) {
            $.ajax({
                type:"post",
                url:"/resgiteraction/resgiter/",
                data:{username:username,email:email,password1:password1,password2:password2,code:code},
                dataType:"json",
                success:function (result) {
                    if (result.code == "sorry") {
                        alert(result.info);
                    } else {
                        var obj = $("input[name='"+result.type+"']");
                        var c;
                        var p = obj.parent();
                        var e = p.next();
                        if ((result.code == "error")) {
                            e.find("div.register_error1").addClass("error2");
                            e.find("div.register_error1").html(result.info);
                            c = false;
                        } else {
                            alert("成功注册！");
                        }
                    }
                }
            });
        }
    };
    $(document).ready(function () {
        $("input[name='user'],input[name='email'],input[name='pass1'],input[name='pass2'],input[name='code']").val("");
        $("input[name='user'],input[name='email'],input[name='pass1'],input[name='pass2'],input[name='code']").focus(function () {
            $(this).addClass("input_over");
            $(this).prev().hide();
            var c = $(this).prev().prev().attr("c");
            $(this).prev().prev().addClass(c);
            init.check_focus($(this));
        });
        $("input[name='user'],input[name='email'],input[name='pass1'],input[name='pass2'],input[name='code']").blur(function () {
            $(this).removeClass("input_over");
            var val = $.trim($(this).val());
            if (!val) {
                $(this).prev().show();
            }
            var c = $(this).prev().prev().attr("c");
            $(this).prev().prev().removeClass(c);
            var name = $(this).attr("name");
            if (name == 'user') {
                init.checkUsername($(this));
            } else if (name == 'email') {
                init.checkEmail($(this));
            } else if (name == 'code') {
                init.checkCode($(this));
            } else {
                init.check_blur($(this));
            }
        });
        $("input[name='user'],input[name='email'],input[name='pass1'],input[name='pass2'],input[name='code']").mouseover(function () {
            init.check_mouseover($(this));
        });
        $("input[name='user'],input[name='email'],input[name='pass1'],input[name='pass2'],input[name='code']").mouseleave(function () {
            init.check_mouseleave($(this));
        });
        $("input[name='register_submit']").mouseover(function () {
            $(this).addClass("submit_over");
        });
        $("input[name='register_submit']").mouseleave(function () {
            $(this).removeClass("submit_over");
        });
        $("img.code_img,a.change").click(function () {//点击验证码或者换一张事件
            $('img.code_img').attr('src', '/codeimg/?id=' + Math.round(Math.random() * 1000));
            $("input[name='code']").val("");
            $("input[name='code']").focus();
        });
        $("#register_form").submit(function () {
            var check = 0;
            init.checkUsername($("input[name='user']"),function(result){
                check += result;
                init.checkEmail($("input[name='email']"),function(result){
                    check += result;
                    $("input[name='pass1'],input[name='pass2']").each(function () {
                        check  += init.check_blur($(this));
                    });
                    init.checkCode($("input[name='code']"),function(result){
                        check += result;
                        if (check == 5) {
                            var username = $.trim($("input[name='user']").val());
                            var email = $.trim($("input[name='email']").val());
                            var password1 = $.trim($("input[name='pass1']").val());
                            var password2 = $.trim($("input[name='pass2']").val());
                            var code = $.trim($("input[name='code']").val());
                            return init.resgiterAction(username,email,password1,password2,code);
                        } else {
                            return false;
                        }
                    });
                });
            });
            return false;
        });
    });
})(jQuery);