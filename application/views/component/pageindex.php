<!-- 用户访问页面记录，小心不能删除哦，不然可有大麻烦js start-->
<script type="text/javascript">
    (function ($) {
        var init = {
            notePageIndex: function (referUrl,currentUrl,fromStr) {//记录用户访问页面与来源url
                if (referUrl && currentUrl && fromStr) {
                    $.ajax({
                        url: "/notepageindex/",
                        type: "post",
                        data: {refer:referUrl,current:currentUrl,from:fromStr},
                        dataType: "json",
                        success: function (result) {
                            //noting
                        }
                    });
                }
            },
            getParam: function (name) {
                var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
                var r = window.location.search.substr(1).match(reg);
                if (r != null) return unescape(r[2]); return null;
            }
        };
        $(document).ready(function () {
            var from = init.getParam("from");
            if (from && (from != undefined)) {
                var referUrl = '<?php echo empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER'];?>';//来源url
                var currentUrl = window.location.href;//当前url
                //记录
                init.notePageIndex(referUrl,currentUrl,from);
            }
        });
    })(jQuery);
</script>
<!-- 用户访问页面记录，小心不能删除哦，不然可有大麻烦js end-->