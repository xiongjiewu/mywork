var init = {
    post_submit: function (editor) {
        var content = editor.getSource();
        if (!content || (content == undefined)) {
            alert("请输入内容!");
            return false;
        }
        return true;
    },
    reply: function (t) {
        var p = $(t).parent();
        var pF = p.prev();
        var userName = pF.find("a.user_name").html();
        var lou = $(t).next().html();
        var con = $("#content");
        con.val("回复" + lou + "的" + userName + ":");
        window.location.href = "#createpost";
        con.focus();
    },
    ding: function (t) {
        var pid = $(t).attr("pid");
        if (pid && (pid != undefined)) {
            var url = $("#ding_url").val();
            var ding = $(t).find(".up_cnt");
            var count = parseInt(ding.html());
            $.ajax({
                url: url,
                type: "post",
                data: {pid: pid},
                dataType: "json",
                success: function (result) {
                    if (result.code && (result.code == "error")) {
                        alert(result.info);
                    } else {
                        ding.html(count + 1);
                    }
                }
            })
        }
        return true;
    },
    ajaxDoScore:function(dyId,scoreStar,callBack) {
        if (dyId && scoreStar) {
            $.ajax({
                url: "/useraction/dafen/",
                type: "post",
                data: {dyId: dyId,scoreStar:scoreStar},
                dataType: "json",
                success: function (result) {
                    if (callBack) {
                        callBack(result);
                    }
                }
            })
        }
    },
    loginCallBack: function () {
        var id = $("#current_id").val();
        var action = $("#action").val();
        var scoreStart = $("#userStart").val();
        var dyId = $("#dy_id").val();
        switch (action) {
            case  "notice" :
                this.ajaxInertNotice(id, function (result) {
                    if (result.code && result.code == "error") {
                        alert(result.info);
                    }
                    window.location.reload();
                });
                break;
            case "shoucang" :
                this.ajaxShouCang(id, function (result) {
                    if (result.code && result.code == "error") {
                        alert(result.info);
                    }
                    window.location.reload();
                });
                break;
            case "post" :
                window.location.reload();
                break;
            case "score" ://打分
                this.ajaxDoScore(dyId,scoreStart,function(result) {
                    window.location.reload();
                });
                break;
            default :
                window.location.reload();
                break;
        }
    },
    ajaxShouCang: function (id, callBack) {
        if (id && (id != undefined)) {
            $.ajax({
                url: "/useraction/shoucang/",
                type: "post",
                data: {id: id},
                dataType: "json",
                success: function (result) {
                    if (callBack) {
                        callBack(result);
                    }
                }
            })
        }
    },
    shouCangDo: function (obj) {
        var id = obj.attr("val");
        this.ajaxShouCang(id, function (result) {
            if (result.error == "error") {
                alert(result.info);
            } else {
                obj.removeClass("shoucang").addClass("shoucang_do");
                obj.find("em").html('已收藏');
            }
        });
    },
    changeNoticeBtn: function (obj) {
        obj.removeClass("dy_notic").addClass("dy_notic_btn");
        obj.html('<i class="icon-check icon-white"></i>已订阅观看通知');
        return true;

    },
    ajaxInertNotice: function (id, callBack) {
        if (id) {
            $.ajax({
                url: "/useraction/insertnotice/",
                type: "post",
                data: {id: id},
                dataType: "json",
                success: function (result) {
                    if (callBack) {
                        callBack(result);
                    }
                }
            });
        }
    },
    insertNoticeDo: function (obj) {
        var id = obj.attr("val");
        this.ajaxInertNotice(id, function (result) {
            if (result.code && result.code == "error") {
                alert(result.info);
            } else {
                init.changeNoticeBtn(obj);
            }
        });
    },
    appendYingPing: function (count, result, obj) {
        var yingping = result.info.yingping;
        var userinfo = result.info.userinfo;
        var user_type = $("#user_type").val();
        var resultCount = $("#YingpingInfoICount").val();
        var limit = $("#limit").val();
        var sHtml = "";
        $("div.lastOne").removeClass("lastOne");
        if ((parseInt(count) + parseInt(limit)) >= resultCount) {
            obj.html("已没有更多评论");
            obj.css("cursor", "default");
        } else {
            obj.html("点击查看更多...");
        }
        var louCount = parseInt(resultCount) - parseInt(count);
        $(yingping).each(function (index, val) {
            var edit = "";
            if (user_type == 1) {
                edit = '<a href="/editpost/index/' + val.id + '/">编辑</a> | ';
            }
            sHtml += '<table cellspacing="0" cellpadding="0" border="0">' +
                '<tbody><tr><td class="userPro" valign="top">' +
                '<div class="left"><img class="lazy" style="display: inline;" ' +
                'src="' + userinfo[val.userId].photo + '" width="50" height="50">' +
                '</div></td><td class="commentTextList" valign="top"><div class="comment ' + val.c + ' notFirst">' +
                '<div class="info"><div class="left"><a class="user_name" id="">' + val.userName + '</a>' +
                '发表于' + val.date + ' <span class="up_btn" pid="' + val.id + '" ' +
                'style="cursor:pointer; color:#4E84AE;">顶(<font class="up_cnt">' + val.ding + '</font>)</span>' +
                '</div><div class="right">' + edit + '<a class="reply" href="javascript:void(0);">回复</a><em>' + (louCount--) + '楼' +
                '</em></div></div><p class="word">' + val.content + '</p></div></td></tr></tbody>' +
                '</table>';
            count++;
        });
        $("#pinglun_count").val(parseInt(count));
        obj.before(sHtml);
    },
    ajaxGetYingPingInfo: function (id, count, obj) {
        $.ajax({
            url: "/useraction/getyingping/",
            type: "post",
            data: {id: id, count: count},
            dataType: "json",
            success: function (result) {
                obj.removeClass("read_more_load");
                if (result.code == "error") {
                    obj.css("cursor", "default");
                } else {
                    if (result.info.count == count) {
                        obj.html("已没有更多评论");
                        obj.css("cursor", "default");
                    } else {
                        init.appendYingPing(count, result, obj);
                    }
                }
            }
        });
    },
    ajaxAddLink: function (id, type, url) {
        if (id && type && url) {
            $.ajax({
                url: "/useraction/addlink/",
                type: "post",
                data: {id: id, type: type, url: url},
                dataType: "json",
                success: function (result) {
                    alert(result.info);
                }
            });
        }
    },
    //初始化迅雷插件

    InitialActiveXObject: function () {
        var Thunder;
        try {

            Thunder = new ActiveXObject("ThunderAgent.Agent")
        } catch (e) {
            try {
                Thunder = new ActiveXObject("ThunderServer.webThunder.1");
            } catch (e) {
                try {
                    Thunder = new ActiveXObject("ThunderAgent.Agent.1");
                } catch (e) {
                    Thunder = null;
                }
            }
        }
        return Thunder;
    },
    //开始下载
    Download: function (url) {
        var Thunder = this.InitialActiveXObject();
        if (Thunder == null) {
            this.DownloadDefault(url);
            return;
        }
        try {
            Thunder.AddTask(url, "", "", "", "", 1, 1, 10);
            Thunder.CommitTasks();
        } catch (e) {
            try {
                Thunder.CallAddTask(url, "", "", 1, "", "");
            } catch (e) {
                this.DownloadDefault(url);
            }
        }
    },
    //容错函数，打开默认浏览器下载
    DownloadDefault: function (url) {
        alert('迅雷打开失败，请先按装迅雷或使用IE内核浏览器下载或直接复制下载：<' + url + '>！');
    },
    ajaxGetDownLink: function (id) {
        if (id) {
            $.ajax({
                url: "/useraction/getdownlink/",
                type: "post",
                data: {id: id},
                dataType: "json",
                success: function (result) {
                    if (result.code == "error") {
                        alert(result.info);
                    } else {
                        if (result.type == 5) {//BT下载
                            alert("BT下载地址为：<" + result.info + ">,请自行复制地址至浏览器中下载");
                        } else {
                            init.Download(result.info);//迅雷下载
                        }
                    }
                }
            });
        }
    },
    intiForm:function() {
        //表单提交事件
        var submitButtonObj = $("#create_post_button");
        var cententObj = $("#content");
        var user_id = $("#user_id").val();
        var dingObj = $(".pllist table .info span");
        if (user_id) {
            //激活表单提交事件
            submitButtonObj.attr("disabled",false);
            //表单提交事件
            submitButtonObj.bind("click",function() {
                var content = $.trim($("#content").val());
                if (!content || (content == "请输入评论内容")) {
                    alert("请输入评论内容");
                    return false;
                }
                return true;
            });
            //快捷键，ctrl+回车=发表评论
            $(document).keydown(function (event) {
                event = event || window.event;
                var e = event.keyCode || event.which;
                if (e == 13 && event.ctrlKey == true) {
                    submitButtonObj.trigger("click");
                }
            });
            //输入框focus事件
            cententObj.bind("focus",function() {
                var content = $.trim($(this).val());
                if (content == "请输入评论内容") {
                    $(this).val("");
                }
            });
            //输入框blur事件
            cententObj.bind("blur",function() {
                var content = $.trim($(this).val());
                if (!content) {
                    $(this).val("请输入评论内容");
                }
            });
            //顶
            dingObj.live("click",function () {
                init.ding(this);
            });
            //收藏
            $("a.shoucang").bind("click", function () {
                init.shouCangDo($(this));
            });
        } else {
            cententObj.bind("focus",function() {
                logPanInit.showLoginPan("init.loginCallBack");
                return false;
            });
            //顶
            dingObj.live("click",function () {
                logPanInit.showLoginPan("init.loginCallBack");
            });
            //收藏
            $("a.shoucang").bind("click", function () {
                var id = $(this).attr("val");
                $("#current_id").val(id);
                $("#action").val("shoucang");
                logPanInit.showLoginPan("init.loginCallBack");
            });
        }
    },
    startMouseOverAndLeave:function(startCount,scoreObj,changeScore) {
        for(var i = 1;i <= startCount;i++) {
            $("a." + i + "_start").addClass("current");
            if (changeScore) {
                scoreObj.html(i * 2);
            }
        }
        for(var j = 5;j > startCount;j--) {
            $("a." + j + "_start").removeClass("current");
        }
    }
};
(function ($) {
    $(document).ready(function () {
        var moreA = $("a.jieshao_more");
        var jieshaoMore = $("span.jieshao_list");
        var lJieShao = jieshaoMore.attr("l_jieshao");
        var sJieShao = jieshaoMore.attr("s_jieshao");
        moreA.bind("click",function() {
            var cT = $(this).html();
            if (cT == "[更多]") {
                jieshaoMore.html(lJieShao);
                $(this).html("[收起]");
            } else {
                jieshaoMore.html(sJieShao);
                $(this).html("[更多]");
            }
        });
        $("div.read_more").bind("click",function(){
            var count = $("#pinglun_count").val();
            var id = $("#dy_id").val();
            if ($(this).html() == "点击查看更多...") {
                $(this).addClass("read_more_load");
                init.ajaxGetYingPingInfo(id,count,$(this));
            }
        });
        var movieTabObj = $(".watch_down_link  .tab span");
        movieTabObj.each(function() {
            var that = $(this);
            that.bind("mouseover",function() {
                if (!that.hasClass("current_tab")) {
                    var removeObj = that.parent().find(".current_tab");
                    var removeType = "." + removeObj.attr("type");
                    if ($(removeType).length > 0) {
                        $(removeType).hide();
                    }
                    removeObj.removeClass("current_tab");
                    that.addClass("current_tab");
                    var thatType = "." + that.attr("type");
                    if ($(thatType).length > 0) {
                        $(thatType).show();
                    }
                }
            });
        });
        //初始化表单事件
        init.intiForm();
        //分享按钮事件
        var fengxiangAObj = $("li.fenxiang");
        var fenxiangButtonObj = $("div.baidufengxiang");
        fengxiangAObj.bind("mouseover",function() {
            fenxiangButtonObj.show();
        });
        fengxiangAObj.bind("mouseleave",function() {
            fenxiangButtonObj.hide();
        });
        fenxiangButtonObj.bind("mouseover",function() {
            $(this).show();
        });
        fenxiangButtonObj.bind("mouseleave",function() {
            $(this).hide();
        });

        //打分a标签,鼠标移过事件+鼠标移开事件
        var dfAObj = $("div.dafen a"),currentA = false,tOut = null;
        var scObj = $("#current_start");
        var scoreObj = $($("div.dafen").find("span").get(1));
        var currentCount = scObj.val();
        var currentScore = scoreObj.html();
        var userId = $("#user_id").val();
        dfAObj.each(function() {
            var that = $(this);
            if (!that.hasClass("hasDafen")) {
                var startCount = that.attr("type");
                that.bind("mouseover",function() {
                    init.startMouseOverAndLeave(startCount,scoreObj,true);
                    currentA = true;
                    if (tOut) {
                        clearTimeout(tOut);
                        tOut = null;
                    }
                });
                that.bind("mouseleave",function() {
                    currentA = false;
                    if (!currentA) {
                        tOut = setTimeout(function() {
                            init.startMouseOverAndLeave(currentCount,scoreObj,false);
                            scoreObj.html(currentScore);
                        },1000);
                    }
                });
                that.bind("click",function() {
                    if (!userId) {
                        $("#userStart").val(startCount);
                        $("#action").val("score");
                        logPanInit.showLoginPan("init.loginCallBack");
                    } else {
                        var dyId = $("#dy_id").val();
                        init.ajaxDoScore(dyId,startCount,function() {
                            window.location.reload();
                        });
                    }
                });
            }
        });
        //观看链接
        var watchSpan = $("div.watchLink_list span.watchlink_list");
        watchSpan.each(function() {
            var that = $(this);
            that.bind("click",function() {
                var url = $(that.find("a").get(0)).attr("href");
                window.open(url);
            });
            that.find("a").each(function() {
                $(this).bind("click",function(evant) {
                    evant.stopPropagation();
                });
            });
        });
        //回复按钮
        $(".info .right a.reply").live("click",function () {
            var user_id = $("#user_id").val();
            if (!user_id) {
                logPanInit.showLoginPan();
            } else {
                init.reply(this);
            }
        });
    })
})(jQuery);