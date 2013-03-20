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
    });
})(jQuery);