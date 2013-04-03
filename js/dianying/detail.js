var init = {
    post_submit: function (editor) {
        var content = editor.getSource();
        var user_id = $.trim($("#user_id").val());
        if (!user_id || (user_id == undefined)) {
            alert("请先登录!");
            return false;
        }
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
                dataType:"json",
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
    ajaxShouCang: function (obj) {
        var id = obj.attr("val");
        if (id && (id != undefined)) {
            $.ajax({
                url: "/useraction/shoucang/",
                type: "post",
                data: {id: id},
                dataType: "json",
                success: function (result) {
                    if (result.error == "error") {
                        alert(result.info);
                    } else {
                        obj.removeClass("shoucang").addClass("shoucang_do");
                        obj.html('<i class="icon-star icon-white"></i>已收藏');
                    }
                }
            })
        }
    },
    changeNoticeBtn:function(obj)
    {
        obj.removeClass("dy_notic").addClass("dy_notic_btn");
        obj.html('<i class="icon-check icon-white"></i>已订阅观看通知');
        return true;

    },
    ajaxInertNotice:function(obj)
    {
        var id = obj.attr("val");
        if (id) {
            $.ajax({
                url:"/useraction/insertnotice/",
                type:"post",
                data:{id:id},
                dataType:"json",
                success:function(result){
                    if (result.code && result.code == "error") {
                        alert(result.info);
                    } else {
                        init.changeNoticeBtn(obj);
                    }
                }
            });
        }
    },
    appendYingPing:function(count,result,obj) {
        var yingping = result.info.yingping;
        var userinfo = result.info.userinfo;
        var resultCount = result.info.count;
        var sHtml = "";
        $("div.lastOne").removeClass("lastOne");
        if ((parseInt(count) + 10) != resultCount) {
            obj.html("已没有更多评论");
            obj.css("cursor","default");
        } else {
            obj.html("点击查看更多...");
        }
        count++;
        $(yingping).each(function(index,val) {
            var c = "";
            if (count == resultCount) {
                c = "lastOne";
            }
            sHtml += '<table cellspacing="0" cellpadding="0" border="0">' +
                '<tbody><tr><td class="userPro" valign="top">' +
                '<div class="left"><img class="lazy" style="display: inline;" ' +
                'src="'+userinfo[val.userId].photo+'" width="50" height="50">' +
                '</div></td><td class="commentTextList" valign="top"><div class="comment '+c+' notFirst">' +
                '<div class="info"><div class="left"><a class="user_name" id="">'+val.userName+'</a>' +
                '发表于'+val.date+' <span class="up_btn" pid="'+val.id+'" ' +
                'style="cursor:pointer; color:#4E84AE;">顶(<font class="up_cnt">'+val.ding+'</font>)</span>' +
                '</div><div class="right"><a class="reply" href="javascript:void(0);">回复</a><em>'+(count++)+'楼' +
                '</em></div></div><p class="word">'+val.content+'</p></div></td></tr></tbody>' +
                '</table>';
        });
        $("#pinglun_count").val(parseInt(count) - 1);
        obj.before(sHtml);
    },
    ajaxGetYingPingInfo:function(id,count,obj)
    {
        $.ajax({
            url:"/useraction/getyingping/",
            type:"post",
            data:{id:id,count:count},
            dataType:"json",
            success:function(result){
                obj.removeClass("read_more_load");
                if (result.code == "error") {
                    obj.css("cursor","default");
                } else {
                   if (result.info.count == count) {
                       obj.html("已没有更多评论");
                       obj.css("cursor","default");
                   } else {
                       init.appendYingPing(count,result,obj);
                   }
                }
            }
        });
    },
    daoHangDingWei:function() {
        var dyInfoObj = $("div.span9");
        var daohangObj = $("ul.dy_bs-docs-sidenav");
        var daohangTop = daohangObj.offset().top,id;
        dyInfoObj.find("section").each(function(){
            var dyInfoTop = $(this).offset().top;
            var dyInfoH = $(this).height();
            if (parseInt(daohangTop) >= parseInt(dyInfoTop) && parseInt(daohangTop) <= (parseInt(dyInfoTop) + parseInt(dyInfoH))) {
                id = $(this).attr("id");
            }
        });
        daohangObj.find("li a.click").removeClass("click");
        daohangObj.find("li a[name='"+id+"']").addClass("click");
    },
    ajaxAddLink:function(id,type,url) {
        if (id && type && url) {
            $.ajax({
                url:"/useraction/addlink/",
                type:"post",
                data:{id:id,type:type,url:url},
                dataType:"json",
                success:function(result){
                    alert(result.info);
                }
            });
        }
    }
};