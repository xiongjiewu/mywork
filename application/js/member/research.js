(function($){
    var init = {
        checkFormParams:function(){
            var radioCount = $("input.radio").length;
            var checkCount = 0;
            for(var i = 1;i<=radioCount;i++) {
                var radioObj = $("input[name='question"+i+"']");
                radioObj.each(function() {
                    if ($(this).attr("checked")) {
                        checkCount++;
                    }
                });
            }
            if (parseInt(checkCount) != parseInt(radioCount)) {
                alert("^_^，您还有"+(parseInt(radioCount) - parseInt(checkCount))+"道选择题还没有回答，麻烦您答完，谢谢哈！～");
                return false;
            }
            return true;
        }
    };
    $(document).ready(function(){
       $("form[name='research']").submit(function(){
           return init.checkFormParams();
       });
    });
})(jQuery);