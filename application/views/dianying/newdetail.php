<input type="hidden" value="" name="currentDownId" id="currentDownId">
<div class="detail_main">
    <div class="detail_info">
        <div class="detail_info_img">
            <img alt="<?php echo $dyInfo['name'];?>" src="<?php echo trim(APF::get_instance()->get_config_value("img_base_url"), "/") . $dyInfo['image']; ?>">
        </div>
        <div class="detail_info_list">
            <ul>
                <li class="dy_name"><?php echo $dyInfo['name'];?></li>
                <li class="zhuyan">
                    <span>主演：</span>
                    <?php echo empty($dyInfo['zhuyan']) ? "暂无" : str_replace("/","、",$dyInfo['zhuyan']);?>
                </li>
                <li class="daoyan">
                    <span>导演：</span>
                    <?php echo empty($dyInfo['daoyan']) ? "暂无" : str_replace("/","、",$dyInfo['daoyan']);?>
                </li>
                <li class="nianfen">
                    <span>年份：</span>
                    <?php echo empty($dyInfo['nianfen']) ? "暂无" : $dyInfo['nianfen'];?>
                </li>
                <li class="type">
                    <span>类型：</span>
                    <a href="/moviceguide/type/<?php echo $dyInfo['type'];?>/">
                        <?php echo $movieType[$dyInfo['type']];?>
                    </a>
                </li>
                <li class="diqu">
                    <span>地区：</span>
                    <a href="/moviceguide/place/<?php echo $dyInfo['diqu'];?>/">
                        <?php echo $moviePlace[$dyInfo['diqu']];?>
                    </a>
                </li>
                <?php if (!empty($dyInfo['time1'])): ?>
                <li class="time">
                    <span>上映时间：</span>
                    <?php echo date("Y-m-d", $dyInfo['time1']);?>
                </li>
                <?php endif;?>
                <li class="jianjie">
                    <span>简介：</span>
                    <span class="jieshao_list" s_jieshao="<?php echo $dyInfo['s_jieshao'];?>" l_jieshao="<?php echo $dyInfo['jieshao'];?>">
                        <?php echo $dyInfo['s_jieshao'];?>
                    </span>
                    <?php if ($dyInfo['s_jieshao'] != $dyInfo['jieshao']):?>
                        <a href="javascript:void(0);" class="jieshao_more">[更多]</a>
                    <?php endif;?>
                </li>
            </ul>
        </div>
    </div>
    <!-- Baidu Button BEGIN -->
    <div class="baidu_share">
        <div id="bdshare" class="bdshare_t bds_tools get-codes-bdshare">
            <span class="bds_more">分享到：</span>
            <a class="bds_qzone"></a>
            <a class="bds_tsina"></a>
            <a class="bds_tqq"></a>
            <a class="bds_renren"></a>
            <a class="bds_t163"></a>
            <a class="shareCount"></a>
        </div>
        <script type="text/javascript" id="bdshare_js" data="type=tools&amp;uid=0" ></script>
        <script type="text/javascript" id="bdshell_js"></script>
        <script type="text/javascript">
            document.getElementById("bdshell_js").src = "http://bdimg.share.baidu.com/static/js/shell_v2.js?cdnversion=" + Math.ceil(new Date()/3600000)
        </script>
    </div>
    <!-- Baidu Button END -->

    <!-- 观看链接 + 下载链接 start   -->
    <?php if (!empty($watchLinkInfo) || !empty($downLoadLinkInfo)): ?>
    <div class="watchLink">
        <?php if (!empty($watchLinkInfo)): ?>
        <div class="watch_title">
            <h1>观看链接</h1>
        </div>
        <div class="watchLink_list">
            <?php foreach ($watchLinkInfo as $watchLinkInfoKey => $watchLinkInfoVal): ?>
            <span title="点击观看" class="watchlink_list">
                <a class="" href="<?php echo APF::get_instance()->get_real_url("play",$dyInfo['id'],array("id"=>$watchLinkInfoVal['id']));?>" target="_blank">
                    <img alt="<?php echo $dyInfo['name'];?>" src="/images/webcon/icon<?php echo $watchLinkInfoVal['player'];?>.png">
                </a>
                <b>
                    <?php if (!empty($watchLinkInfoVal['beizhu'])):?>
                        <?php echo $watchLinkInfoVal['beizhu'];?>
                    <?php else:?>
                        <?php echo $qingxiType[$watchLinkInfoVal['qingxi']];?>
                        <?php if ($watchLinkInfoVal['shoufei'] == 2):?>
                            <font color="#FA8072"><?php echo $shoufeiType[$watchLinkInfoVal['shoufei']];?></font>
                        <?php else:?>
                            <?php echo $shoufeiType[$watchLinkInfoVal['shoufei']];?>
                        <?php endif;?>
                    <?php endif;?>
                </b>
            </span>
            <?php endforeach;?>
        </div>
        <?php endif;?>

        <?php if (!empty($downLoadLinkInfo)): ?>
            <a name="downlink_list"></a>
            <div class="watch_title">
                <h1>下载链接</h1>
            </div>
            <div class="watchLink_list">
                <?php $downI = 1;?>
                <?php foreach ($downLoadLinkInfo as $downLinkInfoKey => $downLinkInfoVal): ?>
                    <span title="点击下载" class="downlink_list" val="<?php echo APF::get_instance()->encodeId($downLinkInfoVal['id']);?>">
                        下载<?php echo (count($downLoadLinkInfo) == 1)? "" : $downI;?>:
                        <?php if ($downLinkInfoVal['type'] == 1)://来自电影天堂?>
                            <?php $linkArr = explode("]",$downLinkInfoVal['link']);?>
                            <?php echo $linkArr[count($linkArr) - 1];?>
                        <?php elseif ($downLinkInfoVal['type'] == 2)://来自飘花?>
                            <?php $linkArr = explode("com",$downLinkInfoVal['link']);?>
                            <?php $link = trim($linkArr[count($linkArr) - 1],"]");?>
                            <?php echo $link;?>
                        <?php endif;?>
                    </span>
                    <?php $downI++;?>
                <?php endforeach;?>
            </div>
        <?php endif;?>
    </div>
    <?php endif;?>
    <!-- 观看链接 + 下载链接 end   -->


    <div class="dy_bottom">
        <div class="post_main">
            <div class="post_form">
                <a id="createpost" name="createpost"></a>
                <form name="create_post" id="create_post" method="post" action="<?php echo get_url("/useraction/post") ?>">
                    <input type="hidden" name="dyId" value="<?php echo $dyInfo['id']; ?>">
                    <textarea class="xheditor" name="content" id="content"></textarea>
                    <div class="submit_post">
                        <input type="submit" name="create_post_button" id="create_post_button" value="发表评论">
                    </div>
                </form>
            </div>
        <!--   评论start     -->
            <?php if (!empty($YingpingInfo)): ?>
                <div class="yingpingCount">
                    已有<span><?php echo $yingpingCount;?></span>条评论
                </div>
                <div id="pllist" class="pllist">
                    <?php $YingpingInfoI = 1; ?>
                    <?php $YingpingInfoTotalCount = $yingpingCount; ?>
                    <?php $YingpingInfoICount = count($YingpingInfo); ?>
                    <?php foreach ($YingpingInfo as $infoKey => $infoVal): ?>
                        <table cellspacing="0" cellpadding="0" border="0">
                            <tbody>
                            <tr>
                                <td class="userPro" valign="top">
                                    <div class="left">
                                        <img class="lazy" style="display: inline;"
                                             src="<?php echo $userInfos[$infoVal['userId']]['photo'] ? trim(APF::get_instance()->get_config_value("img_base_url"), "/") . $userInfos[$infoVal['userId']]['photo'] : trim(APF::get_instance()->get_config_value("img_base_url"), "/") . APF::get_instance()->get_config_value("user_photo"); ?>"
                                             width="50" height="50">
                                    </div>
                                </td>
                                <td class="commentTextList"
                                    valign="top">
                                    <div
                                        class="comment <?php if ($YingpingInfoI == $YingpingInfoICount): ?>lastOne<?php endif; ?><?php if ($YingpingInfoI != 1): ?> notFirst<?php endif; ?>">
                                        <div class="info">
                                            <div class="left">
                                                <a class="user_name" id=""><?php echo $infoVal['userName'];?></a>
                                                发表于<?php echo date("Y-m-d H:i:s", $infoVal['time']);?>
                                                <span class="up_btn" pid="<?php echo $infoVal['id']; ?>"
                                                      style="cursor:pointer; color:#4E84AE;">顶(<font
                                                        class="up_cnt"><?php echo $infoVal['ding'];?></font>)</span>
                                            </div>
                                            <div class="right">
                                                <?php if (!empty($adminInfo)):?>
                                                    <a href="<?php echo get_url("/editpost/index/{$infoVal['id']}/");?>">编辑</a>&nbsp;|&nbsp;
                                                <?php endif;?>
                                                <a class="reply" href="javascript:void(0);">回复</a>
                                                <em><?php echo $YingpingInfoTotalCount--;?>楼</em>
                                            </div>
                                        </div>
                                        <p class="word"><?php echo $infoVal['content'];?></p>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <?php $YingpingInfoI++;?>
                    <?php endforeach; ?>
                    <?php if ($yingpingCount > $limit):?>
                        <div class="read_more">点击查看更多...</div>
                    <?php else:?>
                        <div class="read_more" style="cursor: default">已没有更多评论</div>
                    <?php endif;?>
                </div>
            <?php endif;?>
        <!--   评论end     -->
        </div>

        <div class="right_main">
            <div class="right_title">
                猜你喜欢
            </div>
            <?php if (!empty($caiNiXiHuanInfo)):?>
            <div class="right_dy">
                <ul>
                    <?php $caiI = 1;?>
                    <?php foreach($caiNiXiHuanInfo as $caiVal):?>
                        <?php $idStr = APF::get_instance()->encodeId($caiVal['id']);?>
                    <li <?php if ($caiI % 3 == 0):?>class="last_one" <?php endif;?>>
                        <a class="img_dy" href="/detail/index/<?php echo $idStr;?>/">
                            <img alt="<?php echo $caiVal['name'];?>" src="<?php echo trim(APF::get_instance()->get_config_value("img_base_url"), "/") . $caiVal['image']; ?>">
                        </a>
                        <p><a href=""><?php echo $caiVal['name'];?></a></p>
                        <?php $caiI++;?>
                    </li>
                    <?php endforeach;?>
                </ul>
            </div>
            <?php endif;?>
        </div>
    </div>

    <div class="clear"></div>
</div>
<input type="hidden" name="YingpingInfoICount" id="YingpingInfoICount" value="<?php echo empty($yingpingCount) ? 0 : $yingpingCount;?>">
<input type="hidden" name="user_type" id="user_type" value="<?php echo empty($adminInfo) ? 0 : 1;?>">
<input type="hidden" name="limit" id="limit" value="<?php echo $limit;?>">
<input type="hidden" name="current_id" id="current_id" value="">
<input type="hidden" name="action" id="action" value="">
<input type="hidden" id="ding_url" name="ding_url" value="<?php echo get_url("/useraction/ding/"); ?>">
<input type="hidden" id="user_id" name="user_id" value="<?php echo $userId; ?>">
<input type="hidden" name="dy_id" id="dy_id" value="<?php echo $dyInfo['id'];?>">
<input type="hidden" name="pinglun_count" id="pinglun_count" value="<?php echo !empty($YingpingInfoICount) ? $YingpingInfoICount : 0;?>">
<script type="text/javascript">
    var editor = $('.xheditor').xheditor(
        {
            tools: 'Cut,Copy,Paste,Pastetext,|,Flash,Media,Emot,Fontface,FontSize,Bold,Italic,Underline,Link',
            skin: 'vista',
            showBlocktag: true,
            internalScript: true,
            internalStyle: true,
            width: "95%",
            height: 200,
            fullscreen: false,
            sourceMode: false,
            forcePtag: true,
            emotMark: false,
            shortcuts: {'ctrl+enter': function () {
                <?php if (!empty($userId)):?>
                if (init.post_submit(editor)) {
                    $("#create_post").submit();
                }
                return true;
                <?php else:?>
                $("#current_id").val(0);
                $("#action").val("post");
                logPanInit.showLoginPan("init.loginCallBack");
                <?php endif;?>
            }}
        }
    );
    function post() {
        if (init.post_submit(editor)) {
            $("#create_post").submit();
        } else {
            window.location.reload();
        }
    }
    (function ($) {
        $(document).ready(function () {
            editor.addShortcuts("ctrl+enter");
            $("#create_post_button").bind("click",function () {
                <?php if (!empty($userId)):?>
                if (init.post_submit(editor)) {
                    $("#create_post").submit();
                } else {
                    return false;
                }
                <?php else:?>
                $("#current_id").val(0);
                $("#action").val("post");
                logPanInit.showLoginPan("init.loginCallBack");
                return false;
                <?php endif;?>
            });
            $(".info .right a.reply").live("click",function () {
                init.reply(this, editor);
            });
            $(document).keydown(function (event) {
                event = event || window.event;
                var e = event.keyCode || event.which;
                if (e == 13 && event.ctrlKey == true) {
                    <?php if (!empty($userId)):?>
                    if (init.post_submit(editor)) {
                        $("#create_post").submit();
                    }
                    return true;
                    <?php else:?>
                    $("#current_id").val(0);
                    $("#action").val("post");
                    logPanInit.showLoginPan("init.loginCallBack");
                    return false;
                    <?php endif;?>
                }
            });
            $(".pllist table .info span").live("click",function () {
                init.ding(this);
            });
            $("span.shoucang").bind("click", function () {
                <?php if (empty($userId)):?>
                var id = $(this).attr("val");
                $("#current_id").val(id);
                $("#action").val("shoucang");
                logPanInit.showLoginPan("init.loginCallBack");
                <?php else:?>
                init.shouCangDo($(this));
                <?php endif;?>
            });
            $("span.dy_notic").each(function () {
                $(this).bind("click", function () {
                    <?php if (empty($userId)):?>
                    var id = $(this).attr("val");
                    $("#current_id").val(id);
                    $("#action").val("notice");
                    logPanInit.showLoginPan("init.loginCallBack");
                    <?php else:?>
                    init.insertNoticeDo($(this));
                    <?php endif;?>
                });
            });
            $("div.read_more").bind("click",function(){
                var count = $("#pinglun_count").val();
                var id = $("#dy_id").val();
                if ($(this).html() == "点击查看更多...") {
                    $(this).addClass("read_more_load");
                    init.ajaxGetYingPingInfo(id,count,$(this));
                }
            });
            $("div.user_add").bind("click",function() {
                $(this).css("display","none");
                $("div.add_link").css("display","block");
            });
            $("input.add_cancel").bind("click",function(){
                $(this).parent().parent().css("display","none");
                $("div.user_add").css("display","block");
            });
            $("input.add_submit").bind("click",function(){
                var type = $("#link_type").val();
                var url = $.trim($("#add_link").val());
                var strRegex = "^((https|http|ftp|rtsp|mms)://)?[a-z0-9A-Z]{3}\.[a-z0-9A-Z][a-z0-9A-Z]{0,61}?[a-z0-9A-Z]\.com|net|cn|cc|tv|me (:s[0-9]{1-4})?/$";
                var re = new RegExp(strRegex);
                if (type == 0) {
                    alert("请选择链接类型！");
                } else if (!url || (url == undefined)) {
                    alert("请输入链接！");
                }
                else if (!re.test(url)) {
                    alert("请输入正确的链接！");
                } else {
                    var id = <?php echo $dyInfo['id'];?>;
                    init.ajaxAddLink(id,type,url);
                }
                return false;
            });
            var trObj = $("div.watchLink_info table.table tr.dy_link_info");
            trObj.each(function() {
                $(this).bind("mouseover",function(){
                    $(this).addClass("tr_over");
                });
                $(this).bind("mouseleave",function(){
                    $(this).removeClass("tr_over");
                });
                $(this).bind("click",function(){
                    var url = $($(this).find("a").get(0)).attr("href");
                    window.open(url);
                });
                $(this).find("a").each(function(){
                    $(this).bind("click",function(event){
                        event.stopPropagation();
                    });
                });
            });
            //观看链接
            var watchSpan = $("div.watchLink div.watchLink_list span.watchlink_list");
            watchSpan.each(function() {
                var that = $(this);
                that.bind("click",function() {
                    var url = $(that.find("a").get(0)).attr("href");
                    window.open(url);
                });
                that.find("a").each(function() {
                   $(this).bind("click",function(evant) {
                       evant.stopPropagation();
                   });
                });
            });
            //下载链接
            var downSpan = $("div.watchLink div.watchLink_list span.downlink_list");
            downSpan.each(function() {
                var that = $(this);
                that.bind("click",function() {
                    var id = that.attr("val");
                    $("#currentDownId").val(id);
                    <?php if (empty($userId)):?>
                        window.location.href = "/login?bgurl=<?php echo base64_encode("/detail/index/{$endcodeId}#downlink_list");?>";
                    <?php else:?>
                        init.ajaxGetDownLink();
                    <?php endif;?>
                });
            });
            var moreA = $("a.jieshao_more");
            var jieshaoMore = $("span.jieshao_list");
            var lJieShao = jieshaoMore.attr("l_jieshao");
            var sJieShao = jieshaoMore.attr("s_jieshao");
            moreA.bind("click",function() {
                var cT = $(this).html();
                if (cT == "[更多]") {
                    jieshaoMore.html(lJieShao);
                    $(this).html("[收起]");
                } else {
                    jieshaoMore.html(sJieShao);
                    $(this).html("[更多]");
                }
            });
        })
    })(jQuery);
</script>
