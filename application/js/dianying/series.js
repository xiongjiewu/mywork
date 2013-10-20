(function($) {
    var timeOut = null;
    var fisrtIndex = 0;
    var init = {
        imgMove:function(index,imgUlObj,prevChangeObj,currentChangeObj,imgLiLen) {
            if (index >= imgLiLen) {
                index = 0;
            }
            var left = index * 850;
            prevChangeObj.removeClass("current");
            currentChangeObj.addClass("current");
            imgUlObj.animate({left: "-" + left + "px"}, 500);
        },
        setTimeMove:function(imgUlObj,tabChangeObj,imgLiLen) {//定时切换幻灯片
            timeOut = setInterval(function () {
                fisrtIndex++;
                if (fisrtIndex >= imgLiLen) {
                    fisrtIndex = 0;
                }
                init.imgMove(fisrtIndex,imgUlObj,tabChangeObj.find("span.current"),$(tabChangeObj.find("span").get(fisrtIndex)));
            }, 2 * 1000);
        }
    };
    $(document).ready(function() {
        //头部幻灯片切换事件
        var tabChangeObj = $("div.img_change");
        var imgUlObj = $("div.series_top_left ul");
        var imgLiLen = tabChangeObj.find("span").length;
        tabChangeObj.find("span").each(function() {
            var that = $(this);
            var index = that.attr("index");
            that.bind("mouseover",function() {
                if (timeOut) {
                    clearInterval(timeOut);
                }
                fisrtIndex = index;
                init.imgMove(index,imgUlObj,tabChangeObj.find("span.current"),that,imgLiLen);
            });
            that.bind("mouseleave",function() {
                init.setTimeMove(imgUlObj,tabChangeObj,imgLiLen);
            });
        });

        //鼠标停在图片中，停止自动切换幻灯片
        imgUlObj.find("li").each(function() {
            var that = $(this);
            that.bind("mouseover",function() {
                if (timeOut) {
                    clearInterval(timeOut);
                }
            });
            that.bind("mouseleave",function() {
                init.setTimeMove(imgUlObj,tabChangeObj,imgLiLen);
            });
        });

        //定时切换幻灯片
        init.setTimeMove(imgUlObj,tabChangeObj,imgLiLen);
    });
})(jQuery);