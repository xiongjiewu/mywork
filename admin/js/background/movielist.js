(function($) {
    var init = {
        choseAll:function() {
            $("input[name='chose']").each(function() {
                $(this).attr("checked",true);
            });
            return true;
        },
        cancelChoseAll:function() {
            $("input[name='chose']").each(function() {
                $(this).attr("checked",false);
            });
            return true;
        },
        checkBoxClick:function(t,ch,chose) {
            var i = 0;
            ch.each(function() {
                if ($(this).attr("checked")) {
                    i++;
                }
            });
            if (i == ch.length) {
                chose.html("取消");
            } else {
                chose.html("全选");
            }
            return true;
        },
        initChId:function () {
            var id = "";
            $("input[name='chose']").each(function() {
                if ($(this).attr("checked")) {
                    id += $(this).attr("v");
                    id += ";";
                }
            });
            return id;
        },
        ulLiActionDo:function(t) {
            var type = $(t).attr("type");
            if (type > 7) {
                return false;
            }
            var id = this.initChId();
            if (!id || (id == undefined)) {
                alert("请选择！");
                return false;
            } else {
                $.ajax({
                    url : "/background/action/",
                    type : "post",
                    data :{type:type,id:id},
                    dataType: "json",
                    success:function (result){
                        if (result.code == 2) {
                            alert(result.info);
                            location.reload();
                        } else {
                            alert(result.info);
                        }
                    }
                });
            }
        },
        addToTopicActionDo:function(t) {
            var topicId;
            var type = t.attr("type"),tt,txt;
            if (type == 9) {
                tt = 1;
                txt = "专题";
                topicId = $("#topic").val();
            } else {
                tt = 2;
                txt = "系列";
                topicId = $("#xilie").val();
            }

            if (!topicId || (topicId == undefined) || (topicId == 0)) {
                alert("请选择" + txt + "！");
                return false;
            }
            var id = this.initChId();
            if (!id || (id == undefined)) {
                alert("请选择电影！");
                return false;
            } else {
                $.ajax({
                    url : "/topic/addtotopic/",
                    type : "post",
                    data :{topic:topicId,id:id,tt:tt},
                    dataType: "json",
                    success:function (result){
                        if (result.code == 2) {
                            alert(result.info);
                            window.location.href = "/topic/topicmovie?movieType=" + tt + "&status=-1&id=" + topicId;
                        } else {
                            alert(result.info);
                        }
                    }
                });
            }
        },
        selectUrlJump:function(t) {
            var val = $(t).val();
            window.location.href = "/background/movielist?sort=" + val;
        }
    };
    $(document).ready(function() {
        var ch = $("input[name='chose']");
        var chose = $("div.listTable table tr th.chose span");
        ch.each(function() {
            $(this).attr("checked",false);
        });
        chose.click(function() {
            if ($(this).html() == "全选") {
                init.choseAll();
                $(this).html("取消");
            } else {
                init.cancelChoseAll();
                $(this).html("全选");
            }
        });
        ch.each(function() {
            $(this).click(function() {
                init.checkBoxClick(this,ch,chose);
            });
        });
        $("div.actionList ul li").each(function() {
            $(this).click(function(){
                init.ulLiActionDo(this);
            });
        });

        //加入专题
        $("div.add_to_topic_botton,div.add_to_xilie_botton").bind("click",function() {
            init.addToTopicActionDo($(this));
        });
        $("div.actionList ul li.desc option").each(function() {
            $(this).click(function(){
                init.selectUrlJump(this);
            });
        });
    });
})(jQuery);