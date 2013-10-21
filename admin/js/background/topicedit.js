var init = {
    upmovie_submit:function() {
        if ($("#clickVal").val() == 2) {
            return true;
        }

        var name = $.trim($("#name").val());
        if (!name) {
            alert("名称不能为空！");
            return false;
        }

        var type = $.trim($("#type").val());
        if (type == 0) {
            alert("请选择类型！");
            return false;
        }

        var diqu = $.trim($("#diqu").val());
        if (diqu == 0) {
            alert("请选择地区！");
            return false;
        }

        var sTitle = $.trim($("#sTitle").val());
        if (!sTitle) {
            alert("小标题不能为空！");
            return false;
        }

        var bTitle = $.trim($("#bTitle").val());
        if (!bTitle) {
            alert("大标题不能为空！");
            return false;
        }

        var sImg = $.trim($("#sImg").val());
        if (!sImg) {
            alert("请上传小图片！");
            return false;
        }

        var mImg = $.trim($("#mImg").val());
        if (!mImg) {
            alert("请上传中图片！");
            return false;
        }

        var bImg = $.trim($("#bImg").val());
        if (!bImg) {
            alert("请上传背景图！");
            return false;
        }

        return true;
    },
    imageAtion:function(t) {
        var imageVal = $(t).val();
        var image = imageVal.split(".");
        var type = image[image.length - 1];
        if (!type || (type.toLowerCase() != 'png') && (type.toLowerCase() != 'gif') && (type.toLowerCase() != 'jpg') && (type.toLowerCase() != 'bmp') && (type.toLowerCase() != 'jpeg') && (type.toLowerCase() != 'pjpeg') && (type.toLowerCase() != 'x-png')) {
            alert("只能传格式为png\|gif\|jpg\|pjpeg\|jpeg\|bmp\|x-png的图片!");
            $(t).val("");
            $("#clickVal").val(1);
            return true;
        } else {
            $("#clickVal").val(2);
            $("#currentImgId").val($(t).attr("id"));
            this.upload_image();
        }
    },
    upload_image:function() {
        $('#submit').disabled = true;
        var url = $("#upload_url").val() + $("#currentImgId").val();
        var form = $("form[name='create_topic']");
        form.attr("target",'upload_frame');
        form.attr("action",url);
        $('#submit').trigger("click");
        form.attr("target",'');
        form.attr("action","/topic/edit_topic_do/");
    },
    upload_finish:function(reault) {
        $('#submit').disabled = false;
        $("#clickVal").val(1);
        if (reault.status == "no") {
            alert(reault.error);
        } else {
            var currentId = $("#currentImgId").val();
            var currentObj = $("#" + currentId);
            currentObj.val("");
            var imageId = currentObj.attr("v");
            $("#" + imageId).val(reault.path);
            currentObj.prev().find("img").attr("src",reault.fullPath);
        }
    }
};
(function($){
    $(document).ready(function() {
        $("#create_topic").submit(function(){
            return init.upmovie_submit();
        });

        //图片
        var imageFileObj = $("input[type='file']");
        imageFileObj.each(function() {
            $(this).live("change",function() {
                init.imageAtion(this);
            });
        });
    });
})(jQuery);