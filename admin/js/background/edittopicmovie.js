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

        var image = $.trim($("#image").val());
        if (!image) {
            alert("请上传默认剧照！");
            return false;
        }

        var addImageObj = $("div.juzhao");
        var addError = 0,errorImageStr = "";
        addImageObj.each(function() {
            var img = $.trim($(this).find("img").attr("src"));
            if (img && (img != undefined)) {
                var text = $.trim($(this).nextAll("input[type='text']").val());
                if (!text || (text == undefined)) {
                    addError++;
                    errorImageStr += addError + ","
                }
            }
        });

        if (addError > 0) {
            alert("有剧照必须写标题，请在第" + errorImageStr + "剧照处写上标题！");
            return false;
        }

        var addTextObj = $("input[v='text_add']");
        var addTextError = 0,errorTextStr = "";
        addTextObj.each(function() {
            var text = $.trim($(this).val());
            if (text && (text != undefined)) {
                var img = $.trim($(this).prevAll("div.juzhao").find("img").attr("src"));
                if (!img || (img == undefined)) {
                    addTextError++;
                    errorTextStr += addTextError + ","
                }
            }
        });

        if (addTextError > 0) {
            alert("有标题必须写剧照，请在第" + errorTextStr + "标题处上传剧照！");
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
        form.attr("action","/topic/edit_topicmovie_do/");
    },
    upload_finish:function(reault) {
        $('#submit').disabled = false;
        $("#clickVal").val(1);
        if (reault.status == "no") {
            alert(reault.error);
        } else {
            var currentId = $("#currentImgId").val();
            var currentObj = $("#" + currentId);
            var imageId = currentObj.attr("v");
            if (currentId == "sImgFile") {
                currentObj.val("");
                $("#" + imageId).val(reault.path);
            } else {
                var imgObj = $("#" + imageId);
                var imgStr = imgObj.val();
                if (imgStr) {
                    imgObj.val(imgStr + ";" + currentId + ":" + reault.path + ";");
                } else {
                    imgObj.val(currentId + ":" + reault.path + ";");
                }
            }
            currentObj.prev().find("img").attr("src",reault.fullPath);
        }
    }
};
(function($){
    $(document).ready(function() {
        $("#create_topic").submit(function() {
            return init.upmovie_submit();
        });

        //图片
        var imageFileObj = $("input[type='file']");
        imageFileObj.live("change",function() {
            init.imageAtion(this);
        });

        //增加剧照
        var addObj = $("div.add_juzhao");
        var addTrObj = $("tr.add_juzhao_to");
        var imageLenObj = $("#image_len");
        addObj.bind("click",function() {
            var fileObj = $("input[type='file']");
            var currentI = parseInt(fileObj.length);
            var sHtml = '<tr><td>剧照' + currentI + '：</td><td><div class="sImg juzhao"><img src=""></div>' +
                '<input type="file" name="file_' + currentI + '" id="file_' + currentI + '" v="image_add">' +
                '标题：<input type="text" name="text_' + currentI + '" id="text_' + currentI + '" v="text_add">' +
                '<input class="remove_juzhao" type="button" value="删除"></td></tr>';
            addTrObj.before(sHtml);
            imageLenObj.val(currentI);
        });

        //删除剧照
        var removeBObj = $("input.remove_juzhao");
        var addImgObj = $("#image_add");
        removeBObj.live("click",function() {
            var pp = $(this).parent().parent();
            pp.remove();
            var imgL = imageLenObj.val();
            imageLenObj.val(parseInt(imgL) - 1);
            var fileId = pp.find("input[type='file']").attr("id");
            var imgStr = addImgObj.val();
            var resStr = imgStr.replace(fileId,"");
            addImgObj.val(resStr);
        });
    });
})(jQuery);