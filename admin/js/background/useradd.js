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
        ulLiActionDo:function(t,id) {
            if (!id || (id == undefined)) {
                alert("请选择！");
                return false;
            } else {
                if (confirm("确定？")) {
                    $.ajax({
                        url : "/background/delusergive/",
                        type : "post",
                        data :{id:id},
                        dataType: "json",
                        success:function (result){
                            if (result.code == 2) {
                                location.reload();
                            } else {
                                alert(result.info);
                            }
                        }
                    });
                }
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
        ch.each(function(){
            $(this).click(function() {
                init.checkBoxClick(this,ch,chose);
            });
        });
        $("div.actionList ul li").each(function(){
            $(this).click(function(){
                var type = $(this).attr("type");
                if (type <= 5) {
                    window.location.href = "/background/feedback/" + type;
                } else {
                    var id = init.initChId();
                    init.ulLiActionDo(this,id);
                }
            });
        });
        $("div.listTable table tr td span a.del").each(function(){
            $(this).click(function(){
                var id = $(this).attr("val");
                init.ulLiActionDo(this,id);
            });
        });
        $("div.actionList ul li.desc option").each(function(){
            $(this).click(function(){
                init.selectUrlJump(this);
            });
        });
    });
})(jQuery);