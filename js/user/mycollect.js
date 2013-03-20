(function ($) {
    var init = {
        cancelChose: function () {
            $("input[name='ids[]']").each(function () {
                $(this).attr("checked", false);
            });
        },
        checkedAll: function () {
            $("input[name='ids[]']").each(function () {
                $(this).attr("checked", "checked");
            });
        },
        choseAll: function (obj) {
            if (obj.html() == "全选") {
                this.checkedAll();
                obj.html("取消");
            } else {
                this.cancelChose();
                obj.html("全选");
            }
        }
    };
    $(document).ready(function () {
        init.cancelChose();
        $(".modbox2 table.table tr th span.chose_all").bind("click", function () {
            init.choseAll($(this));
        });
    });
})(jQuery);