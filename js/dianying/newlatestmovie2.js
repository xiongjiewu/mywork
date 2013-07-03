(function($) {
    var aa,cishuC = 50,time = 5;
    var init = {
        initTopHtml:function(name,zhuyan,type,jieshao,nianfen,daoyan,href,img,yaoyao_again) {
            var topMainObj = $("div.last_movie_top_list"),again_style = "none";
            if (yaoyao_again) {
                again_style = "block";
            }
            var sHtml = '<dl><dt><a class="top_img" href="' + href + '"><img alt="' + name + '" src="' + img + '"></a>' +
                '</dt><dd><h1><a class="top_title" href="' + href + '">' + name + '</a></h1></dd><dd>' +
                '<span>主演：</span>' + zhuyan + '</dd><dd><span>类型：</span>' +
                '<a class="top_title">' + type + '</a></dd><dd><span>导演：</span>' +
                '<a class="top_title">' + daoyan + '</a></dd><dd><span>年份：</span>' +
                '<a class="top_title">' + nianfen + '</a></dd><dd><span>简介：</span>' + jieshao + '</dd>' +
                '<dd class="play_now"><a href="' + href + '"></a>' +
                '<span class="yaoyao_again" style="display: ' + again_style + '">好吧，不给力，重新摇！</span></dd></dl>';
            topMainObj.html(sHtml);
        },
        yaoyaoAction: function () {
            var cishu = 1,yaoyaoResult;
            $(window).scrollTop(0);
            init.ajaxGetYaoYaoMoice(function (result) {
                yaoyaoResult = result;
            });
            clearInterval(aa);
            aa = setInterval(function () {
                init.yaoyao(cishu,yaoyaoResult);
                cishu++;
            }, cishu * time);
        },
        //随机打乱
        yaoyao: function (cishu,yaoyaoResult) {
            var liObj = $("div.right2 ul li");
            var liLen = liObj.length;
            var arr = new Array(liLen);
            var i = 0;
            liObj.each(function () {
                var that = $(this);
                arr[i] = that.html();
                i++;
            });
            var outputArr = arr.slice();
            var len = outputArr.length;
            while (len) {
                outputArr.push(outputArr.splice(parseInt(Math.random() * len--), 1)[0]);
            }
            var j = 0;
            liObj.each(function () {
                var that = $(this);
                that.html(outputArr[j]);
                j++;
            });
            var currentliObj = $("div.right2 ul li.current");
            this.initTopHmtlDo(currentliObj);
            if (yaoyaoResult && cishu > cishuC) {
                var nianFenStr;
                if (yaoyaoResult.nianfen == 0) {
                    nianFenStr = '暂无';
                } else {
                    nianFenStr = yaoyaoResult.nianfen;
                }
                var href = "/detail/index/" + yaoyaoResult.idStr + "?from=yaoyao";
                this.initTopHtml(yaoyaoResult.name,yaoyaoResult.zhuyan,yaoyaoResult.typeText,yaoyaoResult.jieshao,nianFenStr,yaoyaoResult.daoyan,href,yaoyaoResult.image,true);
                this.initCurrentLi(yaoyaoResult.name,yaoyaoResult.zhuyan,yaoyaoResult.typeText,yaoyaoResult.jieshao,nianFenStr,yaoyaoResult.daoyan,href,yaoyaoResult.image);
                //运行完毕则清除
                clearInterval(aa);
            }
        },
        initCurrentLi:function(name,zhuyan,type,jieshao,nianfen,daoyan,href,img) {
            var currentLi = $("div.last_movie_top_right ul li.current");
            var tA = currentLi.find("a");
            tA.attr("name",name);
            tA.attr("title",name);
            tA.attr("zhuyan",zhuyan);
            tA.attr("type",type);
            tA.attr("jieshao",jieshao);
            tA.attr("nianfen",nianfen);
            tA.attr("daoyan",daoyan);
            tA.attr("href",href);
            tA.find("img").attr("src",img);
            tA.find("img").attr("alt",name);
        },
        ajaxGetYaoYaoMoice: function (callback) {
            $.ajax({
                url: "/useraction/getyaoyaomovice",
                type: "post",
                dataType: "json",
                success: function (result) {
                    if (callback) {
                        callback(result);
                    }
                }
            });
        },
        initTopHmtlDo:function(that) {
            var tA = that.find("a");
            var name = tA.attr("name");
            var zhuyan = tA.attr("zhuyan");
            var type = tA.attr("type");
            var jieshao = tA.attr("jieshao");
            var nianfen = tA.attr("nianfen");
            var daoyan = tA.attr("daoyan");
            var href = tA.attr("href");
            var img = tA.find("img").attr("src");
            this.initTopHtml(name,zhuyan,type,jieshao,nianfen,daoyan,href,img,false);
        }
    };
    $(document).ready(function() {
        var topLiObj = $("div.last_movie_top_right ul li");
        topLiObj.each(function() {//头部小图片鼠标移过事件
            var that = $(this);
            that.bind("mouseover",function() {
                that.parent().find("li.current").removeClass("current");
                that.addClass("current");
                init.initTopHmtlDo(that);
            });
        });
        //摇一摇
        var yaoyaoObj = $("div.tab_list ul li.tab_list_li_1 a");
        yaoyaoObj.bind("click", function () {
            init.yaoyaoAction();
        });
        $("span.yaoyao_again").live("click", function () {
            init.yaoyaoAction();
        });
        //排行榜点击事件
        var topSpanObj = $("div.top_info_list span.top_name_list");
        topSpanObj.each(function() {
            var url = $($(this).find("a").get(0)).attr("href");
            $(this).bind("click",function() {
                window.location.href = url;
            });
            $(this).find("a").click(function(event) {
                event.stopPropagation();
            });
        });
        //月份电影点击事件
        var liListObj = $(".month_dy_list ul li.info");
        liListObj.each(function() {
            var url = $($(this).find("a").get(0)).attr("href");
            $(this).bind("click",function() {
                window.location.href = url;
            });
            $(this).find("a").click(function(event) {
                event.stopPropagation();
            });
        });
    });
})(jQuery);