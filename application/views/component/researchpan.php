<div class="research_main">
</div>
<div class="research_table">
    <table class="research_table_info">
        <tr class="close_tr">
            <td class="close" colspan="2">
                <span class="close_research">X</span>
            </td>
        </tr>
        <tr>
            <td class="smile_img" rowspan="2">
                <img src="/images/re_su.png">
            </td>
        </tr>
        <tr class="research_text_tr">
            <td class="research_text">
                尊敬的用户您好，欢迎来到<?php echo APF::get_instance()->get_config_value("base_name");?>!
                <br>
                由于我们网站正处于测试阶段，为了更好地服务于您，我们开设了&nbsp;<a href="/research?uu=<?php echo substr(time(),0,9);?>">功能问卷调查</a>&nbsp入口，恳请您百忙之中抽出点时间写下您的宝贵意见。
                如果弹框影响了您的正常浏览，您可以点击不再显示或者右上角的关闭按钮。谢谢！～
            </td>
        </tr>
        <tr class="research_botton_tr">
            <td class="research_botton">
                <a class="btn btn-warning" href="/research?uu=<?php echo substr(time(),0,9);?>">现在去填写</a>
                <a class="btn btn-danger close_research" href="javascript:void(0);">不再显示</a>
            </td>
        </tr>
    </table>
</div>