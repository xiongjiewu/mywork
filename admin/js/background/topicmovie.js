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
            if (type > 2) {
                return false;
            }
            var id;
            if (type == 1) {//激活专题或系列
                id = $("#topic").val();
            } else if (type == 2) {//把电影收入回收站
                id = this.initChId();
            }
            if (!id || (id == undefined)) {
                alert("请选择！");
                return false;
            } else {
                $.ajax({
                    url : "/topic/updateaction/",
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
        addToTopicActionDo:function() {
            var topicId = $("#topic").val();
            if (!topicId || (topicId == undefined) || (topicId == 0)) {
                alert("请选择专题！");
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
                    data :{topic:topicId,id:id},
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
        $("div.add_to_topic_botton").bind("click",function() {
            init.addToTopicActionDo();
        });
        $("div.actionList ul li.desc option").each(function() {
            $(this).click(function(){
                init.selectUrlJump(this);
            });
        });
    });
})(jQuery);