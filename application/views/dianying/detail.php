<div class="row">
    <div class="span3 bs-docs-sidebar">
        <ul class="nav nav-list bs-docs-sidenav dy_bs-docs-sidenav" style="*width: 220px;">
            <li><a href="javascript:void(0);" name="info" class="click" title="点击查看影片详情"><i class="icon-chevron-right"></i> 影片详情</a></li>
            <?php if (!empty($watchLinkInfo)): ?>
                <li><a href="javascript:void(0);" name="watchlink" title="点击查看观看链接"><i class="icon-chevron-right"></i> 观看链接</a></li>
            <?php endif;?>
            <?php if (!empty($downLoadLinkInfo)): ?>
                <li><a href="javascript:void(0);" name="downlink" title="点击查看下载链接"><i class="icon-chevron-right"></i> 下载链接</a></li>
            <?php endif;?>
            <li><a href="javascript:void(0);" name="createpost" title="点击发表评论"><i class="icon-chevron-right"></i> 发表评论</a></li>
        </ul>
    </div>
    <div class="span9">
        <section id="info" class="dy_detail">
            <h1 style="display: none;">
                <?php echo $dyInfo['name'];?>
            </h1>

            <div class="bs-docs-example">
                <table class="dy_detail_table">
                    <tr>
                        <td class="dy_info_img">
                            <img src="<?php echo trim(get_config_value("img_base_url"), "/") . $dyInfo['image']; ?>">
                            <?php if (empty($shoucangInfo)): ?>
                                <span class="btn shoucang" val="<?php echo $dyInfo['id']; ?>">
                                    <i class="icon-star"></i>收藏
                                </span>
                            <?php else: ?>
                                <span class="btn shoucang_do" val="<?php echo $dyInfo['id']; ?>">
                                    <i class="icon-star icon-white"></i>已收藏
                                </span>
                            <?php endif;?>

                            <?php if (empty($moticeInfo) && ($dyInfo['time1'] > time())): ?>
                                <span class="btn dy_notic" val="<?php echo $dyInfo['id']; ?>">
                                    <i class="icon-check"></i>
                                    订阅观看通知
                                </span>
                            <?php elseif ($dyInfo['time1'] > time()): ?>
                                <span class="btn dy_notic_btn" val="14">
                                    <i class="icon-check icon-white"></i>
                                    已订阅观看通知
                                </span>
                            <?php endif;?>

                        </td>
                        <td>
                            <table class="table dy_info_table">
                                <tr>
                                    <td>
                                        <strong>片名：</strong>
                                        <span class="name">
                                            <?php echo $dyInfo['name'];?>
                                        </span>
                                    </td>
                                </tr>
                                <tr class="">
                                    <td>
                                        <strong>主演：</strong><?php $zhuyao = preg_split("/;+|；+/", $dyInfo['zhuyan']);echo implode("、", $zhuyao);?>
                                    </td>
                                </tr>
                                <tr class="">
                                    <td><strong>导演：</strong><?php echo $dyInfo['daoyan'];?></td>
                                </tr>
                                <tr class="">
                                    <td><strong>年份：</strong><?php echo date("Y", strtotime($dyInfo['nianfen']));?>年</td>
                                </tr>
                                <tr class="">
                                    <td><strong>时长：</strong><?php echo $dyInfo['shichang'];?>分钟</td>
                                </tr>
                                <tr class="">
                                    <td><strong>类型：</strong><?php echo $movieType[$dyInfo['type']];?>片</td>
                                </tr>
                                <tr class="">
                                    <td><strong>地区：</strong><?php echo $moviePlace[$dyInfo['diqu']];?></td>
                                </tr>
                                <?php if (!empty($dyInfo['time1'])): ?>
                                    <tr class="">
                                        <td><strong>上映时间：</strong><?php echo date("Y-m-d", $dyInfo['time1']);?>
                                        </td>
                                    </tr>
                                <?php elseif (!empty($dyInfo['time0'])): ?>
                                    <tr class="">
                                        <td><strong>本站提供观看链接日期：</strong><?php echo date("Y-m-d", $dyInfo['time0']);?>
                                        </td>
                                    </tr>
                                <?php endif;?>
                                <tr class="">
                                    <td><strong>介绍：</strong><?php echo $dyInfo['jieshao'];?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
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
            &nbsp;&nbsp;
            <div class="user_add">
                +我要提供链接
            </div>
            <div class="add_link">
                <input type="text" class="add_input" name="add_link" id="add_link" value="">
                <input type="button" class="add_submit" name="add_submit" id="add_submit" value="提交">
                <input type="button" class="add_cancel" name="add_cancel" id="add_cancel" value="取消">
            </div>
        </section>
        <?php if (!empty($watchLinkInfo)): ?>
            <section id="watchlink">
                <br>
                <table class="table table-bordered">
                    <tr class="info">
                        <th>播放类型</th>
                        <th>来源网站</th>
                        <th>是否收费</th>
                        <th>高清程度</th>
                        <th>观看链接</th>
                    </tr>
                    <?php $watchLinkInfoI = 1;?>
                    <?php $watchLinkInfoCount = count($watchLinkInfo);?>
                    <?php foreach ($watchLinkInfo as $watchLinkInfoKey => $watchLinkInfoVal): ?>
                        <tr class="<?php if ($watchLinkInfoI++ % 2 == 1): ?><?php else: ?><?php endif; ?>">
                            <td><?php echo $bofangqiType[$watchLinkInfoVal['player']];?>播放器</td>
                            <td><?php echo $bofangqiType[$watchLinkInfoVal['player']];?>网</td>
                            <td><?php echo $shoufeiType[$watchLinkInfoVal['shoufei']];?></td>
                            <td><?php echo $qingxiType[$watchLinkInfoVal['qingxi']];?></td>
                            <td><a href="<?php echo $watchLinkInfoVal['link']; ?>">点击观看</a></td>
                        </tr>
                    <?php endforeach;?>
                </table>
            </section>
        <?php endif;?>
        <?php if (!empty($downLoadLinkInfo)): ?>
            <section id="downlink">
                <br>

                <p>
                    <?php $downLoadLinkInfoI = 0;?>
                    <?php foreach ($downLoadLinkInfo as $downLoadLinkInfoKey => $downLoadLinkInfoVal): ?>
                        <a href="<?php echo $downLoadLinkInfoVal['link']; ?>"
                           class="btn btn-small <?php if (($downLoadLinkInfoI % 4) == 0): ?>btn-primary<?php elseif (($downLoadLinkInfoI % 4) == 1): ?>btn-warning<?php elseif (($downLoadLinkInfoI % 4) == 2): ?>btn-success<?php elseif (($downLoadLinkInfoI % 4) == 3): ?>btn-danger<?php endif; ?>"><?php echo $downLoadType[$downLoadLinkInfoVal['type']];?>
                            下载(<?php echo $downLoadLinkInfoVal['size']?>M)</a>
                        <?php $downLoadLinkInfoI++; ?>
                    <?php endforeach;?>
                </p>
            </section>
        <?php endif;?>
        <form name="create_post" id="create_post" method="post" action="<?php echo get_url("/useraction/post") ?>">
            <input type="hidden" name="dyId" value="<?php echo $dyInfo['id']; ?>">
            <section id="createpost">
                <div class="bs-docs-example">
                    <textarea class="xheditor" name="content" id="content"></textarea>

                    <p></p>
                    <input type="submit" id="create_post_button" class="btn btn-large btn-primary" value="发表评论"><span
                        style="color: #aaa">（ctrl+enter快捷回复）</span>
                    <br>

                    <p>
                    <?php if (!empty($YingpingInfo)): ?>
                    <div id="pllist" class="pllist">
                        <?php $YingpingInfoI = 1; ?>
                        <?php $YingpingInfoICount = count($YingpingInfo); ?>
                        <?php foreach ($YingpingInfo as $infoKey => $infoVal): ?>
                            <table cellspacing="0" cellpadding="0" border="0">
                                <tbody>
                                <tr>
                                    <td class="userPro" valign="top">
                                        <div class="left">
                                            <img class="lazy" style="display: inline;"
                                                 src="<?php echo $userInfos[$infoVal['userId']]['photo'] ? trim(get_config_value("img_base_url"), "/") . $userInfos[$infoVal['userId']]['photo'] : trim(get_config_value("img_base_url"), "/") . get_config_value("user_photo"); ?>"
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
                                                    <em><?php echo $YingpingInfoI++;?>楼</em>
                                                </div>
                                            </div>
                                            <p class="word"><?php echo $infoVal['content'];?></p>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        <?php endforeach; ?>
                        <?php if ($YingpingInfoICount == 10):?>
                            <div class="read_more">点击查看更多...</div>
                        <?php else:?>
                            <div class="read_more" style="cursor: default">已没有更多评论</div>
                        <?php endif;?>
                    </div>
                    <?php endif;?>
                    </p>
                </div>
            </section>
        </form>
    </div>
</div>
<input type="hidden" id="ding_url" name="ding_url" value="<?php echo get_url("/useraction/ding/"); ?>">
<input type="hidden" id="user_id" name="user_id" value="<?php echo $userId; ?>">
<input type="hidden" name="dy_id" id="dy_id" value="<?php echo $dyInfo['id'];?>">
<input type="hidden" name="pinglun_count" id="pinglun_count" value="<?php echo !empty($YingpingInfoICount) ? $YingpingInfoICount : 0;?>">
<script type="text/javascript">
    var editor = $('.xheditor').xheditor(
        {
            tools: 'Cut,Copy,Paste,Pastetext,|,Flash,Media,Emot,Fontface,FontSize,Bold,Italic,Underline',
            skin: 'vista',
            showBlocktag: true,
            internalScript: true,
            internalStyle: true,
            width: 780,
            height: 200,
            fullscreen: false,
            sourceMode: false,
            forcePtag: true,
            emotMark: false,
            shortcuts: {'ctrl+enter': function () {
                <?php if (!empty($userId)):?>
                return $("#create_post_button").trigger("click");
                <?php else:?>
                alert("请先登录！");
                <?php endif;?>
            }}
        }
    );
    (function ($) {
        $(document).ready(function () {
            editor.addShortcuts("ctrl+enter");
            $("#create_post").submit(function () {
                <?php if (!empty($userId)):?>
                    return init.post_submit(editor);
                <?php else:?>
                    var url = "<?php echo get_url('/login?bgurl=') . base64_encode(get_url('/detail/index/'.$dyInfo['id'].'/'));?>";
                    window.location.href = url;
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
                        return $("#create_post_button").trigger("click");
                    <?php else:?>
                        var url = "<?php echo get_url('/login?bgurl=') . base64_encode(get_url('/detail/index/'.$dyInfo['id'].'/'));?>";
                        window.location.href = url;
                        return false;
                    <?php endif;?>
                }
            });
            $(".pllist table .info span").live("click",function () {
                init.ding(this);
            });
            $("span.shoucang").bind("click", function () {
                <?php if (empty($userId)):?>
                var url = "<?php echo get_url('/login?bgurl=') . base64_encode(get_url('/detail/index/'.$dyInfo['id'].'/'));?>";
                window.location.href = url;
                <?php else:?>
                init.ajaxShouCang($(this));
                <?php endif;?>
            });
            $("span.dy_notic").each(function () {
                $(this).bind("click", function () {
                    <?php if (empty($userId)):?>
                    var url = "<?php echo get_url('/login?bgurl=') . base64_encode(get_url('/detail/index/'.$dyInfo['id'].'/'));?>";
                    window.location.href = url;
                    <?php else:?>
                    init.ajaxInertNotice($(this));
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
            init.daoHangDingWei();
            var daohangObj = $("ul.dy_bs-docs-sidenav");
            daohangObj.bind("mouseover",function(){
                $(this).addClass("bs-docs-sidenav-over");
            });
            daohangObj.bind("mouseleave",function(){
                $(this).removeClass("bs-docs-sidenav-over");
            });
            daohangObj.find("a").each(function(){
                $(this).bind("click",function(){
                    var name = $(this).attr("name");
                    var sH = $("#"+name+"").offset().top;
                    $(window).scrollTop(sH - 50);
                });
            });
            $(window).bind("scroll", function() {//当滚动条滚动时
                init.daoHangDingWei();
            });
        })
    })(jQuery);
</script>