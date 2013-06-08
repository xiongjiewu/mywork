var initOjb = {
    addCladdToLi: function (obj, c) {
        obj.addClass(c);
    },
    removeClass: function (obj, c) {
        obj.removeClass(c);
    },
    removeSpecailStr: function (s) {
        var pattern = new RegExp("[`~!@#$^&*()=|{}';'%+《》『』:,：\\[\\]<>/?~！@#￥……&*（）;—|{}【】‘；：”“'。，、？]");
        var rs = "";
        for (var i = 0; i < s.length; i++) {
            rs = rs + s.substr(i, 1).replace(pattern, '');
        }
        return rs;
    },
    ajaxGetSearchInfo: function (word, callBack) {
        if (word) {
            $.ajax({
                url: "/search/ajaxgetdyinfo/",
                type: "post",
                data: {word: word},
                dataType: "json",
                success: function (result) {
                    if (callBack) {
                        callBack(result);
                    }
                }
            });
        }
    },
    jumpSearch:function(t) {
        var word = $.trim($($(t).find("span").get(0)).html());
        if (word) {
            window.location.href = "/search?key=" + word;
        }
        event.stopPropagation();
    },
    wordSpanMouseOver:function(t) {
        t.parent().find("li.mouse_hover").removeClass("mouse_hover");
        t.addClass("mouse_hover");
        return true;
    },
    wordSpanMouseLeave:function(t) {
        t.removeClass("mouse_hover");
        return true;
    },
    getSearchInfoDo:function(searchObj,searchAbount) {
        var search_val = $.trim(searchObj.val());
        if (search_val && (search_val != undefined) && (search_val != "搜您喜欢的影片、导演、演员...")) {
            this.ajaxGetSearchInfo(search_val, function (result) {
                if (result.code == "success") {
                    var sHtml = "<ul>";
                    $(result.info).each(function (index, val) {
                        var watchHtml;
                        if (val.exist_watch == 0) {
                            watchHtml = "查看详情 >>";
                        } else {
                            watchHtml = "查看观看链接 >>";
                        }
                        var infoList = '<div class="about_info_list"><div class="about_info_list_img">' +
                            '<a href="' + val.url + '"><img alt="' + val.name + '" src="' + val.image + '"></a></div><div class="about_info_list_detail"><table>' +
                            '<tr><th><a href=" ' + val.url + ' ">' + val.name + '</a></th></tr><tr><td><span>主演：</span>' +
                            val.zhuyan + '</td></tr><tr><td><span>导演：</span>' + val.daoyan +
                            '</td></tr><tr><td><span>类型：</span><a href="' + val.typeUrl + '">' + val.typeText + '</a></td></tr><tr><td>' +
                            '<span>年份：</span>' + val.nianfen + '</td></tr><tr><td class="jianjie"><span>简介：</span>' +
                            val.jieshao + '</td></tr><tr><td>' +
                            '<a href="' + val.url + '" class="about_info_list_detail_play">' + watchHtml + '</a>' +
                            '</td></tr></table></div></div>';
                        sHtml += "<li onclick='initOjb.jumpSearch(this,event)' onmouseout='initOjb.wordSpanMouseLeave($(this))' onmouseover='initOjb.wordSpanMouseOver($(this))'>" +
                            "<span class='word'>"
                            + $.trim(val.name)+"</span>" + infoList + "</li>";
                    });
                    sHtml += "</ul>";
                    searchAbount.html(sHtml);
                    searchAbount.show();
                } else {
                    searchAbount.html("");
                    searchAbount.hide();
                }
            });
        } else {
            searchAbount.html("");
            searchAbount.hide();
        }
    },
    ajaxCloseResearchPan:function() {
        $.ajax({
            type:"post",
            url:"/closeresearchpan/",
            dataType:"json",
            success:function() {
                window.location.reload();
            }
        });
    }
};
(function ($) {
    $(document).ready(function () {
        var searchObj = $("#search");
        var searchAbount = $("div.about_search");
        var ki = 0,firstVal;
        searchObj.bind("keyup", function (e) {
            var search_val = $.trim($(this).val());
            if (ki == 0) {//记录原始值
                firstVal = search_val;
            }
            var event = e || window.event;
            var keycode = event.which || event.keyCode;
            if (keycode == 40 || keycode == 38 || keycode == 37 || keycode == 39 || keycode == 13) {//向下箭头被触发
                var searchHtml = $.trim(searchAbount.html());
                if (searchHtml.length > 0) {
                    var currentSpan = $("div.about_search li.mouse_hover");
                    var firstSpan = $(searchAbount.find("li").get(0));
                    var lastSpan = $(searchAbount.find("li").get(searchAbount.find("li").length - 1));
                    if (keycode == 40) {
                        var nextSpan = currentSpan.next();
                        if (currentSpan.length > 0 && nextSpan.length > 0) {
                            initOjb.wordSpanMouseLeave(currentSpan);
                            initOjb.wordSpanMouseOver(nextSpan);
                            searchObj.val(nextSpan.find("span").html());
                        } else if (currentSpan.length > 0 && nextSpan.length == 0) {
                            initOjb.wordSpanMouseLeave(currentSpan);
                            searchObj.val(firstVal);
                        } else {
                            initOjb.wordSpanMouseOver(firstSpan);
                            searchObj.val(firstSpan.find("span").html());
                        }
                    } else if (keycode == 38) {
                        var preSpan = currentSpan.prev();
                        if (currentSpan.length > 0 && preSpan.length > 0) {
                            initOjb.wordSpanMouseLeave(currentSpan);
                            initOjb.wordSpanMouseOver(preSpan);
                            searchObj.val(preSpan.find("span").html());
                        } else if (currentSpan.length > 0 && preSpan.length == 0) {
                            initOjb.wordSpanMouseLeave(currentSpan);
                            searchObj.val(firstVal);
                        } else {
                            initOjb.wordSpanMouseOver(lastSpan);
                            searchObj.val(lastSpan.find("span").html());
                        }
                    }
                }
                ki++;
            } else {
                ki = 0;
                initOjb.getSearchInfoDo(searchObj,searchAbount);
            }
        });
        searchObj.bind("focus", function () {
            var search_val = $.trim($(this).val());
            if (search_val == "搜您喜欢的影片、导演、演员...") {
                $("#search").val("");
            }
        });
        searchObj.bind("blur", function () {
            var search_val = $.trim($(this).val());
            if (!search_val || search_val == undefined) {
                $("#search").val("搜您喜欢的影片、导演、演员...");
            }
        });
        $(document).bind("click",function(e){
            var target = $(e.target);
            if (target.attr("id") && target.attr("id") == "search") {
                initOjb.getSearchInfoDo(searchObj, searchAbount);
            } else if (target.attr("class") && target.attr("class") == "about_search") {
                searchAbount.show();
            } else {
                searchAbount.html("");
                searchAbount.hide();
            }
            e.stopPropagation();
        });
        $("#search_dy").submit(function () {
            var search_val = $.trim($("#search").val());
            if (!search_val || (search_val == "搜您喜欢的影片、导演、演员...")) {
                window.location.href = "/moviceguide/";
                return false;
            } else {
                window.location.href = "/search?key=" + search_val;
                return false;
            }
        });
        $(window).bind("scroll", function() {//当滚动条滚动时
            if ($(window).scrollTop() > 50) {
                $("a.go_to_top").show();
            } else {
                $("a.go_to_top").hide();
            }
        });
        $("a.go_to_top").bind("click",function(){
            $(window).scrollTop(0);
        });
        var topTabLiObj = $("div.header_tab ul li");
        topTabLiObj.each(function() {
            var that = $(this);
            that.bind("mouseover",function() {
                var type = that.attr("type");
                if (type) {
                    var smallMenusObj = that.parent().parent().find("div." + type);
                    if (smallMenusObj.length > 0) {
                        smallMenusObj.show();
                    }
                }
            });
            that.bind("mouseleave",function() {
                var type = that.attr("type");
                if (type) {
                    var smallMenusObj = that.parent().parent().find("div." + type);
                    if (smallMenusObj.length > 0) {
                        smallMenusObj.hide();
                    }
                }
            });
        });
        var smallMenusObj = $("div.small_menus_list");
        smallMenusObj.each(function() {
            var that = $(this);
            that.bind("mouseover",function() {
                that.show();
            });
            that.bind("mouseleave",function() {
                that.hide();
            });
        });
        $("a").each(function() {
           $(this).bind("focus",function() {
               $(this).blur();
           });
        });
        //登录按钮点击事件
        $(".login_total_page").bind("click",function() {
            logPanInit.showLoginPan();
        });
    });
})(jQuery);
