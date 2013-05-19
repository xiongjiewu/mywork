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
        var word = $.trim($(t).html());
        if (word) {
            window.location.href = "/search?key=" + word;
        }
    },
    wordSpanMouseOver:function(t) {
        t.addClass("mouse_hover");
        return true;
    },
    wordSpanMouseLeave:function(t) {
        t.removeClass("mouse_hover");
        return true;
    },
    getSearchInfoDo:function(searchObj,searchAbount) {
        var search_val = $.trim(searchObj.val());
        search_val = this.removeSpecailStr(search_val);
        if (search_val && (search_val != undefined) && (search_val != "搜索您喜欢的影片...")) {
            this.ajaxGetSearchInfo(search_val, function (result) {
                if (result.code == "success") {
                    var sHtml = "";
                    $(result.info).each(function (index, val) {
                        sHtml += "<span class='word' onmouseout='initOjb.wordSpanMouseLeave($(this))' onmouseover='initOjb.wordSpanMouseOver($(this))' onclick='initOjb.jumpSearch(this)'>"+ $.trim(val.name)+"</span>";
                    });
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
                    var currentSpan = $("div.about_search span.mouse_hover");
                    var firstSpan = $(searchAbount.find("span").get(0));
                    var lastSpan = $(searchAbount.find("span").get(searchAbount.find("span").length - 1));
                    if (keycode == 40) {
                        var nextSpan = currentSpan.next();
                        if (currentSpan.length > 0 && nextSpan.length > 0) {
                            currentSpan.removeClass("mouse_hover");
                            nextSpan.addClass("mouse_hover");
                            searchObj.val(nextSpan.html());
                        } else if (currentSpan.length > 0 && nextSpan.length == 0) {
                            currentSpan.removeClass("mouse_hover");
                            searchObj.val(firstVal);
                        } else {
                            firstSpan.addClass("mouse_hover");
                            searchObj.val(firstSpan.html());
                        }
                    } else if (keycode == 38) {
                        var preSpan = currentSpan.prev();
                        if (currentSpan.length > 0 && preSpan.length > 0) {
                            currentSpan.removeClass("mouse_hover");
                            preSpan.addClass("mouse_hover");
                            searchObj.val(preSpan.html());
                        } else if (currentSpan.length > 0 && preSpan.length == 0) {
                            currentSpan.removeClass("mouse_hover");
                            searchObj.val(firstVal);
                        } else {
                            lastSpan.addClass("mouse_hover");
                            searchObj.val(lastSpan.html());
                        }
                    }
                }
            } else {
                initOjb.getSearchInfoDo(searchObj,searchAbount);
            }
            ki++;
        });
        searchObj.bind("focus", function () {
            var search_val = $.trim($(this).val());
            if (search_val == "搜索您喜欢的影片...") {
                $("#search").val("");
            }
        });
        searchObj.bind("blur", function () {
            var search_val = $.trim($(this).val());
            if (!search_val || search_val == undefined) {
                $("#search").val("搜索您喜欢的影片...");
            }
        });
        $(document).bind("click",function(e){
            var target = $(e.target);
            if (target.attr("id") && target.attr("id") == "search") {
                initOjb.getSearchInfoDo(searchObj, searchAbount);
            } else if (target.attr("class") && target.attr("class") == "about_search") {
                searchAbount.show();
            } else {
                searchAbount.hide();
            }
            e.stopPropagation();
        });
        $("#search_dy").submit(function () {
            var search_val = $.trim($("#search").val());
            search_val = initOjb.removeSpecailStr(search_val);
            if (!search_val || (search_val == "搜索您喜欢的影片...")) {
                window.location.href = "/moviceguide/";
                return false;
            } else {
                window.location.href = "/search?key=" + search_val;
                return false;
            }
        });
        var dySortObj = $("div.head_top_menus ul.nav li.dy_sort");
        var typeListObj = $("div.head_top_menus ul.nav  li.dy_sort div.dy_type_list");
        dySortObj.mouseover(function () {
            initOjb.addCladdToLi($(this), "show_sort");
            typeListObj.show();
        });
        dySortObj.mouseleave(function () {
            initOjb.removeClass($(this), "show_sort");
            typeListObj.hide();
        });
        typeListObj.mouseover(function () {
            $(this).show();
            initOjb.addCladdToLi(dySortObj, "show_sort");
        });
        typeListObj.mouseleave(function () {
            $(this).hide();
            initOjb.removeClass(dySortObj, "show_sort");
        });
        var topMenusObj = $("div.head_top_menus ul.nav li.username,div.head_top_menus ul.nav li.username div.user_in");
        topMenusObj.mouseover(function () {
            $("div.head_top_menus ul.nav li.username div.user_in").show();
        });
        topMenusObj.mouseleave(function () {
            $("div.head_top_menus ul.nav li.username div.user_in").hide();
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
        $("ul.nav li.loginr a").each(function() {
            var i = $(this).find("i");
            $(this).bind("mouseover",function() {
                i.addClass("icon-white");
            });
            $(this).bind("mouseleave",function() {
                i.removeClass("icon-white");
            });
        });
        $(".close_research").each(function() {
            $(this).bind("click",function() {
                initOjb.ajaxCloseResearchPan();
            });
        })
    });
})(jQuery);
