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
        checkChIdVal:function() {
            var id = "";
            var ch = true;
            var resArr = new Array(2);
            $("input[name='chose']").each(function() {
                if ($(this).attr("checked")) {
                    var check = $(this).attr("ch");
                    if (check == 0) {
                        ch = false;
                        $(this).parent().parent().addClass("error_tr");
                    }
                    id += $(this).attr("v");
                    id += ";";
                }
            });
            resArr[0] = ch;
            resArr[1] = id;
            return resArr;
        },
        initChSelected:function(type) {
            $("input[name='chose']").each(function() {
                var check = $(this).attr("ch");
                if (check == type) {
                    $(this).attr("checked","checked");
                }
            });
        },
        cacelSeletedCh:function() {
            $("input[name='chose']").each(function() {
                $(this).attr("checked",false);
                $("div.listTable table tr th.chose span").html("全选");
            });
        },
        selectUrlJump:function(t) {
            var val = $(t).val();
            window.location.href = "/background/grablist/?sort=" + val;
        },
        grabMoviceAtion:function(grabVal,callback) {
            if (grabVal) {
                $.ajax({
                    type:"post",
                    url:"/background/grabdo/",
                    data:{grabVal:grabVal},
                    dataType:"json",
                    success:function(result) {
                        if (callback) {
                            callback(result);
                        }
                    }
                });
            } else {
                alert("请选择抓取的信息");
            }
        },
        deleteGradMovice:function(id){
            if (id && (id != undefined)) {
                $.ajax({
                    url : "/background/deletegradmovicebyid/",
                    type : "post",
                    data :{id:id},
                    dataType: "json",
                    success:function (result) {
                        if (result.code == "success") {
                            alert(result.info);
                            location.reload();
                        } else {
                            alert(result.info);
                        }
                    }
                });
            }
        },
        ulLiActionDo:function(id) {
            if (id && (id != undefined)) {
                $.ajax({
                    url : "/background/upgradmovicebyid/",
                    type : "post",
                    data :{id:id},
                    dataType: "json",
                    success:function (result) {
                        if (result.code == "success") {
                            alert(result.info);
                            location.reload();
                        } else {
                            alert(result.info);
                        }
                    }
                });
            }
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
        $("div.actionList ul li select#type").bind("change",function(){
            init.selectUrlJump(this);
        });
        $("#grabbutton").bind("click",function() {
            var grabVal = $("#grabtype").val();
            init.grabMoviceAtion(grabVal,function(result){
                if (result.code == "error") {
                    alert(result.info);
                } else {
                    var p = $("#grabbutton").parent();
                    p.hide();
                    var pp = p.next();
                    pp.html("抓取中,请稍候...");
                    pp.show();
                }
            });
        });
        var moviceActionLi = $("ul.movice_aciton li");
        moviceActionLi.each(function(){
            $(this).bind("click",function() {
                var type = $(this).attr("type");
                if (type == 1) {
                    var resArr = init.checkChIdVal();
                    if (!resArr[1] || (resArr[1] == undefined)) {
                        alert("请先选择！");
                    } else if(!resArr[0]) {
                        alert("选择的电影中含有资料未完善的电影，请完善资料后再做操作！");
                    } else {
                        init.ulLiActionDo(resArr[1]);
                    }
                } else if (type == 2) {
                    init.initChSelected(1);
                } else if (type == 3) {
                    init.initChSelected(0);
                } else if (type == 0) {
                    init.cacelSeletedCh();
                } else if (type == -1) {
                    var id = init.initChId();
                    if (id && (id != undefined)) {
                        init.deleteGradMovice(id);
                    } else {
                        alert("请先选择！");
                    }
                }
            });
        });
    });
})(jQuery);