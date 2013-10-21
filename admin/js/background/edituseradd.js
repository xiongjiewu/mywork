(function($){
    var init = {
        editSubmit:function() {
            var type = $("#type").val();
            var link = $("#link").val();
            if (!link || (link == undefined)) {
                alert("链接不能为空！");
                return false;
            }
            if (type == 2) {
                var size = $("#size").val();
                if (!size || (size == undefined)) {
                    alert("大小不能为空！");
                    return false;
                }
            }
            return true;
        }
    };
    $(document).ready(function(){
        $("#usergive").submit(function(){
            init.editSubmit();
        })
    });
})(jQuery);