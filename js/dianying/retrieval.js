(function($) {
    var firstI = 1;
    var init = {
        appendInfo:function(jsonResult,b) {//将内容追加到页面中
            var appendObj = $("div.retrieval_movie_list ul"),sHtml = "";
            $(jsonResult).each(function (index, val) {
                sHtml += '<li><a class="" href="' + val.url + '" title="' + val.name + '">' +
                    '<i class="' + b + '"></i>' + val.name + '</a></li>';
            });
            appendObj.append(sHtml);
        },
        ajaxGetInfo:function() {
            var b = $("#bType").val();
            var s = $("#sType").val();
            var nextOffset = $("#nextOffset").val();
            var infoCount = $("#infoCount").val();
            if (parseInt(infoCount) > parseInt(nextOffset)) {//总数大于当前位移量，表示还有信息可以读取
                $.ajax({
                    type : "post",
                    url : "/retrieval/ajaxgetInfo/",
                    data : {b:b,s:s,nextOffset:nextOffset},
                    dataType:"json",
                    success:function(result) {
                        if (result.code == "success") {
                            init.appendInfo(result.info,b);
                            //重设下一次读取的位移量
                            $("#nextOffset").val(result.nextOffset);
                        }
                    }
                });
            }
        }
    };
    $(document).ready(function() {
        $(window).scrollTop(0);//每次刷新页面重新置顶
        //字母定位
        var letterUlObj = $("div.retrieval_list ul");
        var initTop = 1000;
        $(window).bind("scroll", function() {//当滚动条滚动时
            var topF = $(window).scrollTop();
            if (topF > 123) {
                if (!letterUlObj.hasClass("top")) {
                    letterUlObj.addClass("top");
                }
            } else {
                letterUlObj.removeClass("top");
            }
            if (topF > firstI * initTop) {
                init.ajaxGetInfo();//ajax加载信息
                firstI++;
            }
        });
    })
})(jQuery);