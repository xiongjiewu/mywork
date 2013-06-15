var init = {
    post_submit: function (editor) {
        var content = editor.getSource();
        if (!content || (content == undefined)) {
            alert("请输入内容!");
            return false;
        }
        return true;
    },
    reply: function (t, editor) {
        var p = $(t).parent();
        var pF = p.prev();
        var userName = pF.find("a.user_name").html();
        var lou = $(t).next().html();
        editor.setSource("回复" + lou + "的" + userName + ":");
        editor.focus();
        window.location.href = "#createpost";
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
    loginCallBack: function () {
        var id = $("#current_id").val();
        var action = $("#action").val();
        if (id && action) {
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
                    post();
                    break;
                default :
                    break;
            }
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
                obj.html('<i class="icon-star icon-white"></i>已收藏');
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
    DownloadDefault: function () {
        alert('迅雷打开失败，请先按装迅雷或使用IE内核浏览器下载！');
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
                        init.Download(result.info);//迅雷下载
//                        window.location.href = result.info;
                    }
                }
            });
        }
    }
};
