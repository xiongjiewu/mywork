(function($){
    $(document).ready(function(){
        $("input[name='username'],input[name='password']").val("");
        $("input[name='username'],input[name='password']").focus(function(){
            $(this).addClass("input_over");
            $(this).prev().hide();
            var c = $(this).prev().prev().attr("c");
            $(this).prev().prev().addClass(c);
        });
        $("input[name='username'],input[name='password']").blur(function(){
            $(this).removeClass("input_over");
            var val = $.trim($(this).val());
            if(!val){
                $(this).prev().show();
            }
            var c = $(this).prev().prev().attr("c");
            $(this).prev().prev().removeClass(c);
        });
        $("input[name='login_submit']").mouseover(function(){
            $(this).addClass("submit_over");
        });
        $("input[name='login_submit']").mouseleave(function(){
            $(this).removeClass("submit_over");
        });
        $("#login_form").submit(function(){
            var username = $.trim($("input[name='username']").val());
            var password = $.trim($("input[name='password']").val());
            if(!username || !password){
                $("td.loginpan_error").html("账号或密码不能为空！");
            }else{
                var remember = 0;
                if($("input[name='checkbox']").attr("checked")){
                    remember = 1;
                }
                $.ajax({
                    type:"post",
                    url:"/loginaction/login/",
                    data:{username:username,password:password,remember:remember},
                    dataType:"json",
                    success: function(result) {
                        if((result.code == "error")){
                            $("td.loginpan_error").html(result.info);
                        }else{
                            window.location.href = "/usercenter/";;
                        }
                    }
                });
            }
            return false;
        });

    });
})(jQuery);