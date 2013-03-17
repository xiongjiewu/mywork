<div class="row">
    <div class="span3 bs-docs-sidebar">
        <ul class="nav nav-list bs-docs-sidenav">
            <li><a href="#info"><i class="icon-chevron-right"></i> 影片详情</a></li>
            <?php if (!empty($watchLinkInfo)): ?>
            <li><a href="#watchlink"><i class="icon-chevron-right"></i> 观看链接</a></li>
            <?php endif;?>
            <?php if (!empty($downLoadLinkInfo)): ?>
            <li><a href="#downlink"><i class="icon-chevron-right"></i> 下载链接</a></li>
            <?php endif;?>
            <li><a href="#createpost"><i class="icon-chevron-right"></i> 发表评论</a></li>
        </ul>
    </div>
    <div class="span9">
        <section id="info" class="dy_detail">
            <h1><?php echo $dyInfo['name'];?></h1>

            <div class="bs-docs-example">
                <table>
                    <tr>
                        <td class="dy_info_img"><img src="<?php echo $dyInfo['image'];?>">
                        </td>
                        <td>
                            <table class="table table-bordered dy_info_table">
                                <tr class="">
                                    <td><strong>主演：</strong><?php $zhuyao = preg_split("/;+|；+/", $dyInfo['zhuyan']);echo implode("、", $zhuyao);?></td>
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
                                <?php if (!empty($dyInfo['time0'])): ?>
                                <tr class="">
                                    <td><strong>本站提供观看链接日期：</strong><?php echo date("Y-m-d", $dyInfo['time0']);?></td>
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
                <tr class="<?php if ($watchLinkInfoI++ % 2 == 1):?><?php else:?><?php endif;?>">
                    <td><?php echo $bofangqiType[$watchLinkInfoVal['player']];?>播放器</td>
                    <td><?php echo $bofangqiType[$watchLinkInfoVal['player']];?>网</td>
                    <td><?php echo $shoufeiType[$watchLinkInfoVal['shoufei']];?></td>
                    <td><?php echo $qingxiType[$watchLinkInfoVal['qingxi']];?></td>
                    <td><a href="<?php echo $watchLinkInfoVal['link'];?>">点击观看</a></td>
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
                <button type="button" onclick="window.open('<?php echo $downLoadLinkInfoVal['link'];?>')" class="btn btn-small <?php if (($downLoadLinkInfoI % 4) == 0):?>btn-primary<?php elseif(($downLoadLinkInfoI % 4) == 1):?>btn-warning<?php elseif(($downLoadLinkInfoI % 4) == 2):?>btn-success<?php elseif(($downLoadLinkInfoI % 4) == 3):?>btn-danger<?php endif;?>"><?php echo $downLoadType[$downLoadLinkInfoVal['type']];?>下载</button>
                <?php $downLoadLinkInfoI++;?>
                <?php endforeach;?>
            </p>
        </section>
        <?php endif;?>
        <form name="create_post" id="create_post" method="post" action="<?php echo get_url("/useraction/post")?>">
            <input type="hidden" name="dyId" value="<?php echo $dyInfo['id'];?>">
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
                                        <a target="_blank" href="http://bbs.zhibo8.cc/space.php?uid=108363">
                                            <img class="lazy" style="display: inline;"
                                                 src="http://plimg.zhibo8.cc/get.php?uid=108363">
                                        </a>
                                    </div>
                                </td>
                                <td class="commentTextList"
                                    valign="top">
                                    <div class="comment <?php if ($YingpingInfoI == $YingpingInfoICount): ?>lastOne<?php endif;?><?php if ($YingpingInfoI != 1): ?> notFirst<?php endif;?>">
                                        <div class="info">
                                            <div class="left">
                                                <a class="user_name" id="" target="_blank"
                                                   href="http://bbs.zhibo8.cc/space.php?uid=108363"><?php echo $infoVal['userName'];?></a>
                                                发表于<?php echo date("Y-m-d H:i:s", $infoVal['time']);?>
                                                <span class="up_btn" pid="<?php echo $infoVal['id'];?>"
                                                      style="cursor:pointer; color:#4E84AE;">顶(<font
                                                        class="up_cnt"><?php echo $infoVal['ding'];?></font>)</span>
                                            </div>
                                            <div class="right">
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
                    </div>
                    <?php endif;?>
                    </p>
                </div>
            </section>
        </form>
    </div>
</div>
<input type="hidden" id="ding_url" name="ding_url" value="<?php echo get_url("/useraction/ding/");?>">
<input type="hidden" id="user_id" name="user_id" value="<?php echo $userId;?>">
<script type="text/javascript">
    var editor = $('.xheditor').xheditor(
            {
                tools:'Cut,Copy,Paste,Pastetext,|,Flash,Media,Emot,Fontface,FontSize,Bold,Italic,Underline',
                skin:'vista',
                showBlocktag:true,
                internalScript:true,
                internalStyle:true,
                width:780,
                height:200,
                fullscreen:false,
                sourceMode:false,
                forcePtag:true,
                emotMark:false,
                shortcuts:{'ctrl+enter':function () {
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
                return init.post_submit(editor);
            });
            $(".info .right a.reply").each(function () {
                $(this).click(function () {
                    init.reply(this, editor);
                });
            });
            $(document).keydown(function (event) {
                event = event || window.event;
                var e = event.keyCode || event.which;
                if (e == 13 && event.ctrlKey == true) {
                    <?php if (!empty($userId)):?>
                    return $("div.create_post_button input").trigger("click");
                    <?php else:?>
                    alert("请先登录！");
                    <?php endif;?>
                }
            });
            $(".pllist table .info span").each(function () {
                $(this).click(function () {
                    init.ding(this);
                });
            });
        })
    })(jQuery);
</script>