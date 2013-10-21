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
        var daoyan = $.trim($("#daoyan").val());
        if (!daoyan) {
            alert("导演不能为空！");
            return false;
        }
        var nianfen = $.trim($("#nianfen").val());
        if (nianfen == false) {
            alert("年份不能为空！");
            return false;
        }
        var shichang = $.trim($("#shichang").val());
        if (shichang == false) {
            alert("时长不能为空！");
            return false;
        }
        var reg1 =  /^\d+$/;
        if (!reg1.test(shichang)) {
            alert("时长只能为正整数！");
            return false;
        }
        var zhuyan = $.trim($("#zhuyan").val());
        if (!zhuyan) {
            alert("主演不能为空！");
            return false;
        }
        var jieshao = $.trim($("#jieshao").val());
        if (!jieshao) {
            alert("介绍不能为空！");
            return false;
        }
        var image_url = $.trim($("#image_url").val());
        if (!image_url) {
            alert("图片不能为空！");
            return false;
        }
        var watchLink = this.getWatchLink();
        if (watchLink && (watchLink != undefined)) {
            $("#watchLink").val(watchLink);
        }
        var downloadLink = this.getDownloadLink();
        if (downloadLink && (downloadLink != undefined)) {
            $("#downloadLink").val(downloadLink);
        }
        var bofangqi = this.getBofangqi();
        if (bofangqi && (bofangqi != undefined)) {
            $("#bofangqi").val(bofangqi);
        }
        var qingxi = this.getQingXi();
        if (qingxi && (qingxi != undefined)) {
            $("#qingxi").val(qingxi);
        }
        var shoufei = this.getShouFei();
        if (shoufei && (shoufei != undefined)) {
            $("#shoufei").val(shoufei);
        }
        var size = this.getSize();
        if (size && (size != undefined)) {
            $("#size").val(size);
        }
        var downLoadType = this.getDownLoadType();
        if (downLoadType && (downLoadType != undefined)) {
            $("#downloadType").val(downLoadType);
        }

        return this.checkDownLoadSize();
    },
    deleteLinkInput:function(t) {
        $(t).prev().remove();
        $(t).remove();
    },
    addLinkInput:function(t){
        var type = $(t).attr("type"),input;
        if (type == 1) {
            var bofangqiObj = $($("select.bofangqi").get(0));
            var bofangqiHtml = bofangqiObj.html();
            input = '<em>' +
                '<input type="text" class="link">' +
                '<select class="bofangqi">' + bofangqiHtml +
                '</select>' +
                '<select class="qingxi">' +
                '<option value="1">一般</option>' +
                '<option value="2">标清</option>' +
                '<option value="3">高清</option>' +
                '<option value="4">超清</option>' +
                '</select>' +
                '<select class="shoufei">' +
                '<option value="1">免费</option>' +
                '<option value="2">收费</option>' +
                '</select>' +
                '</em>' +
                '<label type="'+type+'">删除</label>';
        } else {
            input = '<em><input type="text" class="link">' +
                '&nbsp;大小：<input type="text" class="size" style="width: 60px">&nbsp;M' +
                '&nbsp;<select class="type">' +
                '<option value="1">迅雷</option>' +
                '<option value="2">快车</option>' +
                '<option value="3">电驴</option>' +
                '<option value="4">直接</option>' +
                '</select>' +
                '</em>' +
                '<label type="'+type+'">删除</label>';
        }
        $(input).insertBefore($(t));
    },
    getWatchLink:function() {
        var link = "";
        $("td.watchLink input.link").each(function(){
            var value = $.trim($(this).val());
            link += value;
            link += ";"
        });
        return link;
    },
    getDownloadLink:function() {
        var link = "";
        $("td.downloadLink input.link").each(function(){
            var value = $.trim($(this).val());
            link += value;
            link += ";"
        });
        return link;
    },
    getBofangqi:function() {
        var link = "";
        $("td.watchLink select.bofangqi").each(function(){
            var value = $.trim($(this).val());
            link += value;
            link += ";"
        });
        return link;
    },
    getQingXi:function() {
        var link = "";
        $("td.watchLink select.qingxi").each(function(){
            var value = $.trim($(this).val());
            link += value;
            link += ";"
        });
        return link;
    },
    getShouFei:function() {
        var link = "";
        $("td.watchLink select.shoufei").each(function(){
            var value = $.trim($(this).val());
            link += value;
            link += ";"
        });
        return link;
    },
    getSize:function() {
        var size = "";
        $("td.downloadLink input.size").each(function(){
            var value = $.trim($(this).val());
            size += value;
            size += ";"
        });
        return size;
    },
    checkDownLoadSize:function() {
        var res = true;
        $("td.downloadLink input.link").each(function(){
            var value = $.trim($(this).val());
            if (value && (value !=undefined)) {
                var size =  $.trim($(this).next("input.size").val());
                if (/^(?:[\+\-]?\d+(?:\.\d+)?)?$/.test(this.size) && (size > 0)) {

                } else {
                    alert("大小输入有误！");
                    res = false;
                    return false
                }
            }
        });
        return res;
    },
    getDownLoadType:function() {
        var type = "";
        $("td.downloadLink select.type").each(function(){
            var value = $.trim($(this).val());
            if (value && (value !=undefined)) {
                type += value;
                type += ";"
            }
        });
        return type;
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
            this.upload_image();
        }
    },
    upload_image:function() {
        $('#submit').disabled = true;
        var url = $("#upload_url").val();
        var form = $("form[name='upmovie']");
        form.attr("target",'upload_frame');
        form.attr("action",url);
        $('#submit').trigger("click");
        form.attr("target",'');
        form.attr("action","/background/upmovieaction/");
    },
    upload_finish:function(reault) {
        $('#submit').disabled = false;
        $("#clickVal").val(1);
        if (reault.status == "no") {
            alert(reault.error);
        } else {
            $("#image").val("");
            $("div.upimage").hide();
            $("#image_url").val(reault.path);
            $("tr.movie_tupian td div.image img").attr("src",reault.fullPath);
            $("tr.movie_tupian td div.image").show();
        }
    },
    resetImage:function() {
        $("#image_url").val("");
        $("tr.movie_tupian td div.image img").attr("src","");
        $("tr.movie_tupian td div.image").hide();
        $("div.upimage").show();
    }
};
(function($){
    $(document).ready(function() {
        $("#upmovie").submit(function(){
            return init.upmovie_submit();
        });
        $("tr.movie_link td label").live("click",function(){
            init.deleteLinkInput(this);
        });
        $("tr.movie_link td span").each(function(){
            $(this).bind("click",function(){
                init.addLinkInput(this);
            });
        });
        $("#image").live("change",function(){
            init.imageAtion(this);
        });
        $("tr.movie_tupian div.image span ").click(function(){
            init.resetImage();
        });
    });
})(jQuery);