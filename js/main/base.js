var initOjb = {
    addCladdToLi: function (obj, c) {
        obj.addClass(c);
    },
    removeClass: function (obj, c) {
        obj.removeClass(c);
    },
    removeSpecailStr: function (s) {
        var pattern = new RegExp("[`~!@#$^&*()=|{}':;'%+《》『』,\\[\\]<>/?~！@#￥……&*（）&mdash;—|{}【】‘；：”“'。，、？]");
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
    getSearchInfoDo:function(searchObj,searchAbount) {
        var search_val = $.trim(searchObj.val());
        search_val = this.removeSpecailStr(search_val);
        if (search_val && (search_val != undefined) && (search_val != "搜索您喜欢的影片...")) {
            this.ajaxGetSearchInfo(search_val, function (result) {
                if (result.code == "success") {
                    var sHtml = "";
                    $(result.info).each(function (index, val) {
                        sHtml += "<span class='word' onclick='initOjb.jumpSearch(this)'>"+ $.trim(val.name)+"</span>";
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
    }
};
(function ($) {
    $(document).ready(function () {
        var searchObj = $("#search");
        var searchAbount = $("div.about_search");
        searchObj.bind("keyup", function () {
            initOjb.getSearchInfoDo(searchObj,searchAbount);
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
            var target  = $(e.target);
            alert(target.html() + "sad" + searchAbount.html());
            if (target.html() != searchAbount.html() && target.attr("id") != searchObj.attr("id")) {
                searchAbount.hide();
            } else if (target.html() == searchAbount.html()) {
                searchAbount.show();
            } else {

                initOjb.getSearchInfoDo(searchObj,searchAbount);
            }
            e.stopPropagation();
        });
        $("#search_dy").submit(function () {
            var search_val = $.trim($("#search").val());
            search_val = initOjb.removeSpecailStr(search_val);
            if (!search_val || (search_val == "搜索您喜欢的影片...")) {
                window.location.href = "/classicmovie/";
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
    });
})(jQuery);