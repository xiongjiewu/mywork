(function ($) {
    var init = {
        choseAll: function () {
            $("input[name='chose']").each(function () {
                $(this).attr("checked", true);
            });
            return true;
        },
        cancelChoseAll: function () {
            $("input[name='chose']").each(function () {
                $(this).attr("checked", false);
            });
            return true;
        },
        checkBoxClick: function (t, ch, chose) {
            var i = 0;
            ch.each(function () {
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
        initChId: function () {
            var id = "";
            $("input[name='chose']").each(function () {
                if (!$(this).attr("checked")) {
                    id += $(this).attr("v");
                    id += ";";
                }
            });
            return id;
        },
        delCache : function () {
            $.ajax({
                url: "/background/removecach/",
                type: "post",
                dataType: "json",
                success: function (result) {
                    alert(result.info);
                }
            });
            return true;
        },
        ulLiActionDo: function (t) {
            var type = $(t).attr("type");
            if (type > 7) {
                return false;
            }
            if (type == 0) {
                return this.delCache();
            }
            var i = 0;
            $("input[name='chose']").each(function () {
                if ($(this).attr("checked")) {
                    i++;
                }
            });
            if (i == 0) {
                alert("请选择!");
                return false;
            }
            var totalC = $("#moviecount").val();
            if (i >= totalC) {
                alert("禁止操作，删除后最新上映列表将为空！");
                return false;
            } else {
                var id = this.initChId();
                var val;
                if (type == 1) {
                    val = 1;
                } else if (type == 2) {
                    val = 3;
                } else if (type == 3) {
                    val = 5;
                }
                $.ajax({
                    url: "/background/action/",
                    type: "post",
                    data: {type: val, id: id},
                    dataType: "json",
                    success: function (result) {
                        if (result.code == 2) {
                            alert(result.info);
                            window.location.href = "/background/lastlist/" + type;
                        } else {
                            alert(result.info);
                        }
                    }
                });
            }
        },
        selectUrlJump: function (t) {
            var val = $(t).val();
            window.location.href = "/background/movielist?sort=" + val;
        }
    };
    $(document).ready(function () {
        var ch = $("input[name='chose']");
        var chose = $("div.listTable table tr th.chose span");
        ch.each(function () {
            $(this).attr("checked", false);
        });
        chose.click(function () {
            if ($(this).html() == "全选") {
                init.choseAll();
                $(this).html("取消");
            } else {
                init.cancelChoseAll();
                $(this).html("全选");
            }
        });
        ch.each(function () {
            $(this).click(function () {
                init.checkBoxClick(this, ch, chose);
            });
        });
        $("div.actionList ul li").each(function () {
            $(this).click(function () {
                init.ulLiActionDo(this);
            });
        });
        $("div.actionList ul li.desc option").each(function () {
            $(this).click(function () {
                init.selectUrlJump(this);
            });
        });
    });
})(jQuery);