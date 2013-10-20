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
        getCheckedCount:function(){
            var count = 0;
            $("input[name='ids[]']").each(function () {
                if ($(this).attr("checked")) {
                    count++;
                }
            });
            return count;
        },
        checkboxClick:function(choseAllObj,checkBoxObj){
            if (this.getCheckedCount() == checkBoxObj.length) {
                choseAllObj.html("取消");
            } else {
                choseAllObj.html("全选");
            }
        },
        choseAll: function (obj) {
            if (obj.html() == "全选") {
                this.checkedAll();
                obj.html("取消");
            } else {
                this.cancelChose();
                obj.html("全选");
            }
        },
        ajaxDel:function(id){
            if (id) {
                $.ajax({
                    url:"/usercenter/delfeedback/",
                    type:"post",
                    data:{id:id},
                    dataType:"json",
                    success:function(result){
                        if (result.code == "error") {
                            alert(result.info);
                        } else {
                            location.reload();
                        }
                    }
                });
            }
        },
        delOne:function(obj){
            if (confirm("确认删除嘛？")) {
                var id = obj.attr("v");
                this.ajaxDel(id);
            }
        },
        getCheckedIds:function(checkBoxObj){
            var id = '';
            checkBoxObj.each(function(){
                if ($(this).attr("checked")) {
                    id += ($(this).val() + ";")
                }
            });
            return id;
        },
        mulDel:function(checkBoxObj) {
            var id = this.getCheckedIds(checkBoxObj);
            if (!id || (id == undefined)) {
                alert("请先选择！");
            } else if (confirm("确认删除嘛？")) {
                this.ajaxDel(id);
            }
        },
        jumpSelect:function(val) {
            var type = $("#type").val();
            window.location.href = "/usercenter/feedback/"+type+"/" + val;
        }
    };
    $(document).ready(function () {
        init.cancelChose();
        var choseAllObj = $(".modbox2 table.table tr th span.chose_all");
        var checkBoxObj = $("input[name='ids[]']");
        var delObj = $(".modbox2 table.table tr td.action span");
        var mulDelObj = $("a.btn-info");
        choseAllObj.bind("click", function () {
            init.choseAll($(this));
        });
        checkBoxObj.each(function () {
            $(this).bind("click",function(){
                init.checkboxClick(choseAllObj,checkBoxObj);
            });
        });
        delObj.each(function(){
            $(this).bind("click",function(){
                init.delOne($(this));
            });
        });
        mulDelObj.bind("click",function(){
            init.mulDel(checkBoxObj);
        });
        var selectObj = $("#shaixuan");
        selectObj.bind("change",function(){
            init.jumpSelect($(this).val());
        });
    });
})(jQuery);