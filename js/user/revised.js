var init = {
    imageAtion: function (t) {
        $("div.user_image").find("div.doing").show();
        var imageVal = $(t).val();
        var image = imageVal.split(".");
        if (!image[1] || (image[1] != 'png') && (image[1] != 'gif') && (image[1] != 'jpg')) {
            alert("只能传格式为png\|gif\|jpg的图片!");
            $(t).val("");
            $("div.user_image").find("div.doing").hide();
            return true;
        } else {
            this.upload_image();
        }
    },
    upload_image: function () {
        $('#submit').disabled = true;
        var url = $("#upload_url").val();
        var form = $("form[name='userphone']");
        form.attr("target", 'upload_frame');
        form.attr("action", url);
        $('#submit').trigger("click");
    },
    upload_finish: function (reault) {
        if (reault.status == "no") {
            alert(reault.error);
        } else {
            $("#image").val("");
            $("#image").hide();
            $("#userphoto").val(reault.path);
            $("div.user_image").find("img").attr("src", reault.path);
            $("div.user_image").find("span.shangchuan").hide();
            $("div.user_image").find("span.upload").show();
            $("div.user_image").find("span.cancel").show();
        }
        $("div.user_image").find("div.doing").hide();
    },
    cancelUploadImg: function (obj) {
        obj.hide();
        obj.prev().hide();
        obj.prev().prev().show();
        $("#userphoto").val("");
        $("#image").show();
        var img_url = $("#moren_img").val();
        $("div.user_image").find("img").attr("src", img_url);
    },
    uploadUserPho:function(obj){
        var img = $("#userphoto").val();
        if (!img || (img == undefined)) {
            alert("网络连接失败，请重新尝试！");
        } else {
            $.ajax({
                url:"/useraction/uploadpho/",
                type:"post",
                data:{userpho:img},
                dataType:"json",
                success:function(result){
                    alert(result.info);
                    obj.hide();
                    obj.prev().hide();
                    obj.prev().prev().show();
                }
            });
        }
    },
    setInputNpHtml:function(obj,text){
        obj.parent().next().html(text);
    },
    checkEmail:function(){
        var obj = $("#email");
        var val = $.trim(obj.val());
        var reg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
        if (!val || (val == undefined)) {
            this.setInputNpHtml(obj,"请输入邮箱");
            return false;
        } else if (!reg.test(val)) {
            this.setInputNpHtml(obj,"邮箱格式不正确");
            return false;
        } else {
            return val;
        }
    },
    emailBtnCheck:function() {
        var email = this.checkEmail();
        if (email && (email != undefined)) {
            $.ajax({
                url:"/usercenter/resetemail/",
                type:"post",
                data:{email:email},
                dataType:"json",
                success:function(result){
                    if (result.code == "error") {
                        init.setInputNpHtml($("#email"),result.info);
                    } else {
                        alert(result.info);
                    }
                }
            });
        }
    },
    checkOldPass:function()
    {
        var obj = $("#oldpass");
        var val = $.trim(obj.val());
        if (!val || (val == undefined)) {
            this.setInputNpHtml(obj,"请输入旧密码");
            return false;
        } else {
            return val;
        }
    },
    checkNewPass1:function()
    {
        var obj = $("#newpass1");
        var val = $.trim(obj.val());
        if (!val || (val == undefined)) {
            this.setInputNpHtml(obj,"请输入新密码");
            return false;
        } else if (val.length < 6 || val.length > 20) {
            this.setInputNpHtml(obj,"密码长度为6-20个字符");
            return false;
        } else {
            return val;
        }
    },
    checkNewPass2:function()
    {
        var nObj = $("#newpass1");
        var obj = $("#newpass2");
        var nVal = $.trim(nObj.val());
        var val = $.trim(obj.val());
        if (!val || (val == undefined)) {
            this.setInputNpHtml(obj,"请输入确认密码");
            return false;
        } else if (nVal && (nVal != undefined) && (val != nVal)) {
            this.setInputNpHtml(obj,"两次输入的密码不一致");
            return false;
        } else {
            return val;
        }
    },
    passBtnClick:function()
    {
        var oldPass = this.checkOldPass();
        var newPass1 = this.checkNewPass1();
        var newPass2 = this.checkNewPass2();
        if (oldPass && newPass1 && newPass2) {
            $.ajax({
                url:"/usercenter/resetpassword/",
                type:"post",
                data:{oldPass:oldPass,newPass1:newPass1,newPass2:newPass2},
                dataType:"json",
                success:function(result){
                    if (result.code == "error") {
                        init.setInputNpHtml($("#"+result.type),result.info);
                    } else {
                        alert(result.info);
                        window.location.href = "/login/";
                    }
                }
            });
        }
        return check;
    }
};
(function ($) {
    $(document).ready(function () {
        $("#image").live("change", function () {
            init.imageAtion(this);
        });
        $("div.user_image").find("span.cancel").bind("click", function () {
            init.cancelUploadImg($(this));
        });
        $("div.user_image").find("span.upload").bind("click", function () {
            init.uploadUserPho($(this));
        });
        $("span.shangchuan").bind("click",function(){//点击上传头像按钮时触发file点击事件
            $("#image").trigger("click");
        });
        $("#action_change").bind("click",function(){
            init.passBtnClick();
        });
        $("table.change_pass_table tr td input").each(function(){
            $(this).focus(function(){
                init.setInputNpHtml($(this),"");
            });
        });
        $("#change_email").bind("click",function(){
            init.emailBtnCheck();
        });
    });
})(jQuery);