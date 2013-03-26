(function($){
    var init = {
        ajaxcheckUserNameAndEmail:function(username,email){
            if (username && (username != undefined) && email && (email != undefined)) {
                $.ajax({
                    url:"/useraction/changepassword/",
                    type:"post",
                    data:{username:username,email:email},
                    dataType:"json",
                    success:function(result){
                        if (result.code == "error") {
                            alert(result.info);
                        } else {
                            window.location.href = "/password/change?key=" + result.info;
                        }
                    }
                });
            }
        },
        changePassword:function()
        {
            var username = $.trim($("#username").val());
            var email = $.trim($("#email").val());
            if (!username || (username == undefined) || !email || (email == undefined)) {
                alert("登录帐号或安全邮箱不能为空！");
            } else {
                var reg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
                if (!reg.test(email)) {
                    alert("邮箱格式不正确");
                } else {
                    this.ajaxcheckUserNameAndEmail(username,email);
                }
            }
            return false;
        },
        ajaxchangePassword:function(password1,password2,key){
            if (password1 && (password1 != undefined) && password2 && (password2 != undefined) && key && (key != undefined)) {
                $.ajax({
                    url:"/useraction/changepassworddo/",
                    type:"post",
                    data:{password1:password1,password2:password2,key:key},
                    dataType:"json",
                    success:function(result){
                        alert(result.code);
                        window.location.href = result.info;
                    }
                });
            }
        },
        changePasswordSubmit:function(){
            var password1 = $.trim($("#password1").val());
            var password2 = $.trim($("#password2").val());
            if (!password1 || (password1 == undefined) || !password2 || (password2 == undefined)) {
                alert("登录密码或确认密码不能为空！");
            } else if (password1.length < 6 || password1.length > 20) {
                alert("登录密码长度必须为6-20个字符！");
            } else if (password1 != password2) {
                alert("两次输入的密码不一致！");
            } else {
                var key = $("#key").val();
                init.ajaxchangePassword(password1,password2,key);
            }
            return false;
        }
    };
    $(document).ready(function(){
        $("#submit").bind("click",function(){
            init.changePassword();
        });
        $("#submitchange").bind("click",function(){
            init.changePasswordSubmit();
        });
    });
})(jQuery);