(function ($) {
    var aa;
    var init = {
        initLiHtml: function (obj, name, jieshao, type, typeText, img, zhuyan, idStr) {
            var htmlStr = '<a class="first_img" href="/detail/index/' + idStr + '/">' +
                '<img alt="' + name + '" src="' + img + '"></a>' +
                '<p class="first_name">' +
                '<a href="/detail/index/' + idStr + '/">' + name + '</a><a class="dy_type" href="/moviceguide/type/' + type + '/">' +
                '[' + typeText + ']</a></p><p class="first_zhuyan">' +
                '<span>主演:</span>' + zhuyan + '</p><p class="first_jieshao">' +
                '<span>简介:</span>' + jieshao + '</p>';
            obj.html(htmlStr);
        },
        initYaoyaoInfo: function (result) {
            var yaoyaoObj = $("div.yaoyao_movice_info_img");
            var aObj = yaoyaoObj.find("a");
            var url = "/detail/index/" + result.idStr;
            var nianFenStr;
            if (result.nianfen == 0) {
                nianFenStr = '<td><span class="">年份：</span>暂无</td>';
            } else {
                nianFenStr = '<td><span class="">年份：</span>' + result.nianfen + '</td>';
            }
            aObj.attr("href", url);
            aObj.find("img").attr("src", result.image);
            var moviceInfoList = $("div.yaoyao_movice_info_list");
            var htmlStr = '<table><tr><th><a href="' + url + '" class="movice_name">' + result.name + '</a><span class="close_yaoyao"></span></th></tr><tr><td class="movice">' +
                '<span class="">主演：</span>' + result.zhuyan + '</td></tr><tr><td><span class="">导演：</span>' +
                result.daoyan + '</td></tr><tr><td><span class="">类型：</span>' +
                '<a class="" href="/moviceguide/type/' + result.type + '/">' + result.typeText +
                '</a></td></tr><tr>' + nianFenStr + '</tr><tr><td class="jieshao">' +
                '<span class="">简介：</span>' + result.jieshao +
                '</td></tr><tr><td><a class="play_now" href="' + url + '"></a><span class="yaoyao_again">好吧，不给力，重新摇！</span></td></tr></table>';
            moviceInfoList.html(htmlStr);
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
        //居中展示摇摇电影，从上往下慢慢走下来到中间
        showYaoYaoMovice: function () {
            var obj = $("div.yaoyao_movice_info");
            obj.css("left", ((jQuery(window).width() - obj.width()) / 2) + "px");
            obj.show();
            var top = (jQuery(window).height() - obj.height()) / 2;
            obj.animate({top: top + "px"}, 200);
        },
        yaoyaoAction: function (topObj) {
            $("div.home_yaoyao_main").hide();
            $("div.yaoyao_movice_info").css("top", "-330px");
            var cishu = 1;
            $(window).scrollTop(0);
            init.ajaxGetYaoYaoMoice(function (result) {
                init.initYaoyaoInfo(result);
            });
            clearInterval(aa);
            aa = setInterval(function () {
                init.yaoyao(topObj, cishu);
                cishu++;
            }, cishu * 200);
        },
        //随机打乱
        yaoyao: function (topObj, cishu) {
            var liObj = topObj.find("li");
            liObj.each(function() {
                $(this).find("div.home_top_dy_detail").removeClass("home_top_dy_detail_left");
                $(this).find("div.home_top_dy_detail").hide();
                $(this).unbind("mouseover");
            });
            var liLen = liObj.length - 1;
            var arr = new Array(liLen);
            var i = 0;
            liObj.each(function () {
                var that = $(this);
                if (!that.hasClass("web_count_main")) {
                    arr[i] = $(this).html();
                    i++;
                }
            });
            var outputArr = arr.slice();
            var len = outputArr.length;
            while (len) {
                outputArr.push(outputArr.splice(parseInt(Math.random() * len--), 1)[0]);
            }
            var j = 0;
            liObj.each(function () {
                var that = $(this);
                if (!that.hasClass("web_count_main")) {
                    $(this).html(outputArr[j]);
                    j++;
                }
            });
            if (cishu > 10) {
                $("div.home_yaoyao_main").show();
                this.showYaoYaoMovice();
                //运行完毕则清除
                clearInterval(aa);
            }
        }
    };
    $(document).ready(function () {
        var topObj = $("div.home_top ul");
        var dyInfoListObj = $("div.movice_info_list_list div.info_list ul");
        dyInfoListObj.find("li").each(function () {
            var that = $(this);
            var url = $(that.find("a").get(0)).attr("href");
            //点击整块li，跳转至电影详细页
            that.bind("click", function () {
                window.location.href = url;
            });
            //点击a链接阻止冒泡
            that.find("a").each(function () {
                $(this).bind("click", function (event) {
                    event.stopPropagation();
                });
            });
            if (!that.hasClass("first_one_li")) {
                var thatname = that.attr("name");
                var thatjieshao = that.attr("jieshao");
                var thattype = that.attr("type");
                var thattypeText = that.attr("typeText");
                var thatimg = that.attr("img");
                var thatzhuyan = that.attr("zhuyan");
                var thatidStr = that.attr("idStr");
                that.bind("mouseover", function () {
                    var fisrtLi = that.parent().find("li.first_one_li");
                    init.initLiHtml(fisrtLi, thatname, thatjieshao, thattype, thattypeText, thatimg, thatzhuyan, thatidStr);
                });
                that.bind("mouseleave", function () {
                    var fisrtLi = that.parent().find("li.first_one_li");
                    var name = fisrtLi.attr("name");
                    var jieshao = fisrtLi.attr("jieshao");
                    var type = fisrtLi.attr("type");
                    var typeText = fisrtLi.attr("typeText");
                    var img = fisrtLi.attr("img");
                    var zhuyan = fisrtLi.attr("zhuyan");
                    var idStr = fisrtLi.attr("idStr");
                    init.initLiHtml(fisrtLi, name, jieshao, type, typeText, img, zhuyan, idStr);
                });
            }
        });
        var yaoyaoObj = $("div.tab_list ul li.tab_list_li_1 a");
        yaoyaoObj.bind("click", function () {
            init.yaoyaoAction(topObj);
        });
        $("span.yaoyao_again").live("click", function () {
            init.yaoyaoAction(topObj);
        });
        $("span.close_yaoyao").live("click",function() {
            $("div.home_yaoyao_main").hide();
            $("div.yaoyao_movice_info").hide();
        });
        //设置顶部电影墙li宽度与长度，为了兼容所有浏览器，只能用js控制
        var homeTopObj = $("div.home_top");
        var totalWitch = homeTopObj.width();
        var totalHeigth = homeTopObj.height();
        var oneWidth = Math.ceil(totalWitch / 14);
        var oneHeigth = Math.ceil(totalHeigth / 3);
        topObj.find("li").each(function() {
            var that = $(this);
            if (that.hasClass("web_count_main")) {
                var currentW = totalWitch - oneWidth * 10 - 2;
                var currentH = totalHeigth - oneHeigth - 1;
                that.width(currentW);
                that.height(currentH);
            } else if (that.hasClass("top_last_li")) {
                that.width(totalWitch - oneWidth * 13 - 2);
                that.height(currentH);
            } else {
                that.width(oneWidth - 2);
                that.height(oneHeigth - 2 );
            }
        });
    })
})(jQuery);