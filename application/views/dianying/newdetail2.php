<input type="hidden" value="" name="currentDownId" id="currentDownId">
<div class="new_detail">
    <div class="guide">
        <a href="/">首页 </a>>
        <a href="<?php echo APF::get_instance()->get_real_url('/moviceguide','',array("type" => $dyInfo['type']));?>">
            <?php echo $movieType[$dyInfo['type']];?>
        </a>>
        <span> <?php echo $dyInfo['name'];?></span>
    </div>
    <div class="dy_detail">
        <div class="total_image">
            <div class="dy_image">
                <img class="info_image" alt="<?php echo $dyInfo['name'];?>" src="<?php echo APF::get_instance()->get_image_url($dyInfo['image']);?>">
            </div>
        </div>
        <div class="dy_right">
            <dl>
                <dd><h1><?php echo $dyInfo['name'];?></h1></dd>
                <dt>
                    <?php $zhuyan = str_replace("、","/",$dyInfo['zhuyan']);?>
                    <?php $zhuyaoArr = explode("/",$zhuyan);?>
                    <span>主演:</span>
                    <?php if (empty($zhuyan)):?>
                        暂无
                    <?php else:?>
                        <?php foreach($zhuyaoArr as $zhuyanVal):?>
                            <a href="<?php echo APF::get_instance()->get_real_url("/jump","",array("type" => 1,"key" => $zhuyanVal));?>"><?php echo $zhuyanVal?></a>&nbsp;&nbsp;
                        <?php endforeach;?>
                    <?php endif;?>
                </dt>
                <dt>
                    <span>导演:</span>
                    <?php $daoyan = str_replace("、","/",$dyInfo['daoyan']);?>
                    <?php $daoyanArr = explode("/",$daoyan);?>
                    <?php if (empty($daoyan)):?>
                        暂无
                    <?php else:?>
                        <?php foreach($daoyanArr as $daoyanVal):?>
                            <a href="<?php echo APF::get_instance()->get_real_url("/jump","",array("type" => 1,"key" => $daoyanVal));?>"><?php echo $daoyanVal;?></a>&nbsp;&nbsp;
                        <?php endforeach;?>
                    <?php endif;?>
                </dt>
                <dt>
                    <span>类型:</span>
                    <a href="/moviceguide/type/<?php echo $dyInfo['type'];?>/">
                        <?php echo $movieType[$dyInfo['type']];?>
                    </a>
                </dt>
                <dt>
                    <span>地区:</span>
                    <a href="/moviceguide/place/<?php echo $dyInfo['diqu'];?>/">
                        <?php echo $moviePlace[$dyInfo['diqu']];?>
                    </a>
                </dt>
                <dt>
                    <span>年份:</span>
                    <?php echo !empty($dyInfo['nianfen']) ? '<a href="/moviceguide/year/' . $dyInfo['nianfen'] . '">' . $dyInfo['nianfen'] . '</a>':"暂无";?>
                </dt>
                <dt class="shichang">
                    <span>时长:</span>
                    <?php echo !empty($dyInfo['shichang']) ? $dyInfo['shichang'] . "分钟":"暂无";?>
                </dt>
                <dt class="shichang">
                    <span>上映时间:</span>
                    <?php echo !empty($dyInfo['time1']) ? date("Y-m-d", $dyInfo['time1']):"暂无";?>
                </dt>
                <dt>
                    <span>简介:</span>
                    <span class="jieshao_list" s_jieshao="<?php echo $dyInfo['s_jieshao'];?>" l_jieshao="<?php echo $dyInfo['jieshao'];?>">
                        <?php echo $dyInfo['s_jieshao'];?>
                    </span>
                    <?php if ($dyInfo['s_jieshao'] != $dyInfo['jieshao']):?>
                        <a href="javascript:void(0);" class="jieshao_more">[更多]</a>
                    <?php endif;?>
                </dt>
            </dl>
            <div class="dy_hot_info">
                <input type="hidden" name="current_start" id="current_start" value="<?php echo $currentKey;?>">
                <div class="dafen<?php if (!empty($hasDafen)):?> hasDafen<?php endif;?>">
                    <?php if (empty($hasDafen)):?>
                        <span class="df">打分:</span>
                    <?php else:?>
                        <span class="df hasDafen">已打分:</span>
                    <?php endif;?>
                    <?php foreach($startInfo as $startKey => $startVal):?>
                        <a class="<?php echo $startKey;?>_start<?php if ($startVal['active']):?> current<?php endif;?><?php if (!empty($hasDafen)):?> hasDafen<?php endif;?>" type="<?php echo $startKey;?>" title="<?php echo $startVal['title'];?>"></a>
                    <?php endforeach;?>
                    <span><?php echo round($dyInfo['score'],1);?></span>分
                    <div class="df_count">(<?php echo $dyInfo['totalStartNum'];?>人)</div>
                </div>
            </div>
            <div class="dy_count">
                <div class="count_list">
                    <ul>
                        <li>播放:<?php echo $dyInfo['playNum'];?>次</li>
                        <li style="display: none;">收藏:<?php echo $dyInfo['playNum'];?>次</li>
                        <?php if (!empty($downLoadLinkInfo)):?>
                            <li class="">下载:<?php echo $dyInfo['downNum'];?>次</li>
                        <?php endif;?>
                        <li class="last">被搜:<?php echo $dyInfo['searchNum'];?>次</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="dy_button">
            <ul>
                <li class="yingping">
                    <a href="#createpost" title="写影评">
                        <i></i>
                        <br>
                        <em>影评</em>
                    </a>
                </li>
                <li class="dingyue" style="display: none;">
                    <a href="javascript:void(0);" title="订阅观看通知">
                        <i></i>
                        <br>
                        <em>订阅</em>
                    </a>
                </li>
                <li class="shoucang">
                    <?php if (empty($shoucangInfo)): ?>
                        <a href="javascript:void(0);" class="shoucang" title="点击收藏" val="<?php echo $dyInfo['id']; ?>">
                            <i></i>
                            <br>
                            <em>收藏</em>
                        </a>
                    <?php else:?>
                        <a href="javascript:void(0);" class="shoucang_do" title="已收藏">
                            <i></i>
                            <br>
                            <em>已收藏</em>
                        </a>
                    <?php endif;?>
                </li>
                <li class="fenxiang">
                    <a href="javascript:void(0);" class="fengxiangButton" title="与好友分享">
                        <i></i>
                        <br>
                        <em>分享</em>
                    </a>
                    <!-- Baidu Button BEGIN -->
                    <div class="baidufengxiang">
                        <div class="baidu_share">
                            <div class="fenxiang_t"></div>
                            <div id="bdshare" class="bdshare_t bds_tools get-codes-bdshare">
                                <span class="bds_more">分享到：</span>
                                <a class="bds_qzone"></a>
                                <a class="bds_tsina"></a>
                                <a class="bds_tqq"></a>
                                <a class="bds_renren"></a>
                                <a class="bds_t163"></a>
                                <a class="shareCount"></a>
                            </div>
                        </div>
                    </div>
                    <!-- Baidu Button END -->
                </li>
            </ul>
        </div>
        <div class="detail_bottom"></div>
    </div>
    <!-- 下载、观看、相关电影 start -->
    <div class="detail_about">
        <!-- 观看、下载链接 start  -->
        <?php if (!empty($watchLinkInfo) || !empty($downLoadLinkInfo)): ?>
            <div class="watch_down_link">
                <div class="tab">
                    <?php if (!empty($watchLinkInfo)):?>
                        <span class="watch_link current_tab" type="watch">观看地址</span>
                    <?php endif;?>
                    <?php if (!empty($watchLinkInfo) && !empty($downLoadLinkInfo)):?>
                        <i class="fenge_line"></i>
                    <?php endif;?>
                    <?php if (!empty($downLoadLinkInfo)):?>
                        <span class="down_link<?php if (empty($watchLinkInfo)):?> current_tab<?php endif;?>" type="down">下载地址</span>
                    <?php endif;?>
                </div>
                <?php if (!empty($watchLinkInfo)):?>
                <div class="watchLink_list watch">
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

                <?php if (!empty($downLoadLinkInfo)):?>
                <a name="downlink_list"></a>
                <div class="watchLink_list down" <?php if (!empty($watchLinkInfo)):?>style="display: none;"<?php endif;?>>
                    <?php $downI = 1;?>
                    <?php foreach ($downLoadLinkInfo as $downLinkInfoKey => $downLinkInfoVal): ?>
                        <?php $downLinkInfoVal['link'] = strip_tags($downLinkInfoVal['link']);?>
                        <span title="点击下载" class="downlink_list" val="<?php echo APF::get_instance()->encodeId($downLinkInfoVal['id']);?>">
                        下载<?php echo (count($downLoadLinkInfo) == 1)? "" : $downI;?>:
                            <?php if ($downLinkInfoVal['type'] == 5)://BT种子?>
                                <?php echo $dyInfo['name'];?>BT下载
                            <?php elseif (strpos($downLinkInfoVal['link'],"thunder://") !== false)://来自电影天堂?>
                                迅雷高速下载
                            <?php elseif ($downLinkInfoVal['type'] == 1)://来自电影天堂?>
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
        <!-- 观看、下载链接 end -->
        <!-- 相关电影推荐 start  -->
        <div class="watch_down_link movie_tuijian">
            <div class="tab">
                <?php if (!empty($caiNiXiHuanInfo)):?>
                    <span class="watch_link current_tab" type="caicai">好吧猜你喜欢</span>
                    <i class="fenge_line"></i>
                <?php endif;?>
                <?php if (!empty($daoyanMovieInfo)):?>
                <span class="down_link" type="daoyan">导演其他作品</span>
                <i class="fenge_line"></i>
                <?php endif;?>
                <?php if (!empty($zhuyanMovieInfo)):?>
                <span class="down_link" type="zhuyan">主演其他作品</span>
                <?php endif;?>
            </div>
            <?php if (!empty($caiNiXiHuanInfo)):?>
                <div class="movie_list caicai current_movie_list">
                    <ul>
                        <?php $movieI = 1;?>
                        <?php foreach($caiNiXiHuanInfo as $dyMovieVal):?>
                            <li <?php if ($movieI % 7 == 0):?>class="last_movie"<?php endif;?>>
                                <a href="<?php echo APF::get_instance()->get_real_url("detail",$dyMovieVal['id']);?>" class="image">
                                    <img class="info_image" alt="<?php echo $dyMovieVal['name'];?>" src="<?php echo APF::get_instance()->get_image_url($dyMovieVal['image']);?>">
                                </a>
                            <span class="name">
                                <a href="<?php echo APF::get_instance()->get_real_url("detail",$dyMovieVal['id']);?>"><?php echo $dyMovieVal['name'];?></a>
                            </span>
                            </li>
                            <?php $movieI++;?>
                        <?php endforeach;?>
                    </ul>
                </div>
            <?php endif;?>
            <?php if (!empty($daoyanMovieInfo)):?>
            <div class="movie_list daoyan">
                <ul>
                    <?php $movieI = 1;?>
                    <?php foreach($daoyanMovieInfo as $dyMovieVal):?>
                        <li <?php if ($movieI % 7 == 0):?>class="last_movie"<?php endif;?>>
                            <a href="<?php echo APF::get_instance()->get_real_url("detail",$dyMovieVal['id']);?>" class="image">
                                <img class="info_image" alt="<?php echo $dyMovieVal['name'];?>" src="<?php echo APF::get_instance()->get_image_url($dyMovieVal['image']);?>">
                            </a>
                            <span class="name">
                                <a href="<?php echo APF::get_instance()->get_real_url("detail",$dyMovieVal['id']);?>"><?php echo $dyMovieVal['name'];?></a>
                            </span>
                        </li>
                        <?php $movieI++;?>
                    <?php endforeach;?>
                </ul>
            </div>
            <?php endif;?>
            <?php if (!empty($zhuyanMovieInfo)):?>
                <div class="movie_list zhuyan">
                    <ul>
                        <?php $movieI = 1;?>
                        <?php foreach($zhuyanMovieInfo as $dyMovieVal):?>
                            <li <?php if ($movieI % 7 == 0):?>class="last_movie"<?php endif;?>>
                                <a href="<?php echo APF::get_instance()->get_real_url("detail",$dyMovieVal['id']);?>" class="image">
                                    <img class="info_image" alt="<?php echo $dyMovieVal['name'];?>" src="<?php echo APF::get_instance()->get_image_url($dyMovieVal['image']);?>">
                                </a>
                            <span class="name">
                                <a href="<?php echo APF::get_instance()->get_real_url("detail",$dyMovieVal['id']);?>"><?php echo $dyMovieVal['name'];?></a>
                            </span>
                            </li>
                            <?php $movieI++;?>
                        <?php endforeach;?>
                    </ul>
                </div>
            <?php endif;?>
        </div>
        <!-- 相关电影推荐 end  -->

        <!-- 评论框 start  -->
        <div class="pinglun_kuang">
            <a name="createpost" class="createpost"></a>
            <div class="pinglun_photo">
                <img src="<?php echo $userPhoto;?>" class="photo">
            </div>
            <div class="jiantou"></div>
            <form name="create_post" id="create_post" method="post" action="<?php echo get_url("/useraction/post") ?>">
            <input type="hidden" name="dyId" value="<?php echo $dyInfo['id']; ?>">
            <div class="text">
                <textarea name="content" id="content">请输入评论内容</textarea>
            </div>
            <div class="submit_post">
                <input type="submit" disabled="disabled" name="create_post_button" id="create_post_button" value="发&nbsp;布">
            </div>
            </form>
        </div>
        <!-- 评论框 end  -->

        <!-- 评论 start -->
        <?php if (!empty($YingpingInfo)): ?>
            <div class="p_c_t">好吧影评<span>(<?php echo $yingpingCount;?>条)</span></div>
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
                                    <?php $userImage = APF::get_instance()->get_image_url($userInfos[$infoVal['userId']]['photo']);?>
                                    <img class="lazy" style="display: inline;"
                                         src="<?php echo $userImage;?>"
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
        <!-- 评论end -->
    </div>
    <!-- 下载、观看、相关电影 end -->

    <!-- 右侧索引、排行榜 start -->
    <div class="detail_left">
        <div class="leixing">
            <div class="leixing_title">电影索引</div>
            <div class="leixing_list">
                <ul>
                    <li class="small_title">按类型</li>
                    <?php $typeI = 1;?>
                    <?php foreach($movieType as $typKey => $typeVal):?>
                        <li class="list<?php if ($typeI % 6 == 0):?> last_li<?php endif;?>">
                            <a href="/moviceguide?type=<?php echo $typKey;?>"><?php echo $typeVal;?></a>
                        </li>
                        <?php $typeI++;?>
                    <?php endforeach;?>
                    <li class="small_title">按地区</li>
                    <?php $typeI = 1;?>
                    <?php foreach($moviePlace as $typKey => $typeVal):?>
                        <li class="list diqu<?php if ($typeI % 6 == 0):?> last_li<?php endif;?>">
                            <a href="/moviceguide?place=<?php echo $typKey;?>"><?php echo $typeVal;?></a>
                        </li>
                        <?php $typeI++;?>
                    <?php endforeach;?>
                </ul>
            </div>
        </div>
        <!-- 排行榜 start -->

        <?php if (!empty($todayInfo)):?>
            <div class="today_new">
                <div class="paihang">
                    <ul>
                        <li class="paihang_title">
                            今日更新
                        </li>
                        <?php $tdI = 1;?>
                        <?php foreach($todayInfo as $tyMovieVal):?>
                            <li class="info<?php if ($tdI == count($todayInfo)):?> last_td<?php endif;?>">
                                <?php if ($tdI <= 3):?>
                                    <i><?php echo "0" . $tdI;?></i>
                                <?php else:?>
                                    <i class="more" ><?php echo ($tdI < 10)?"0" . $tdI:$tdI;?></i>
                                <?php endif;?>
                                <a class="dy_title" href="<?php echo APF::get_instance()->get_real_url("detail",$tyMovieVal['id']);?>" title="<?php echo $tyMovieVal['name'];?>">
                                    <?php echo $tyMovieVal['name'];?>
                                </a>
                                <span>更新于<?php echo date("H:i",$tyMovieVal['createtime']);?></span>
                            </li>
                            <?php $tdI++;?>
                        <?php endforeach;?>
                    </ul>
                </div>
            </div>
        <?php endif;?>
        <!-- 排行榜 end -->
    </div>
    <!-- 右侧索引、排行榜 start -->
    <div class="clear"></div>
</div>
<input type="hidden" name="YingpingInfoICount" id="YingpingInfoICount" value="<?php echo empty($yingpingCount) ? 0 : $yingpingCount;?>">
<input type="hidden" name="user_type" id="user_type" value="<?php echo empty($adminInfo) ? 0 : 1;?>">
<input type="hidden" name="limit" id="limit" value="<?php echo $limit;?>">
<input type="hidden" name="current_id" id="current_id" value="">
<input type="hidden" name="action" id="action" value="">
<input type="hidden" id="ding_url" name="ding_url" value="<?php echo get_url("/useraction/ding/"); ?>">
<input type="hidden" id="user_id" name="user_id" value="<?php echo $userId; ?>">
<input type="hidden" id="userStart" name="userStart" value="">
<input type="hidden" name="dy_id" id="dy_id" value="<?php echo $dyInfo['id'];?>">
<input type="hidden" name="pinglun_count" id="pinglun_count" value="<?php echo !empty($YingpingInfoICount) ? $YingpingInfoICount : 0;?>">
<script type="text/javascript" id="bdshare_js" data="type=tools&amp;uid=0" ></script>
<script type="text/javascript" id="bdshell_js"></script>
<script type="text/javascript">
    (function($){
        $(document).ready(function() {
            //下载链接
            var downSpan = $("div.watchLink_list span.downlink_list");
            downSpan.each(function() {
                var that = $(this);
                that.bind("click",function() {
                    var id = that.attr("val");
                    <?php if (empty($userId)):?>
                    window.location.href = "/login?bgurl=<?php echo base64_encode("/detail/index/{$endcodeId}#downlink_list");?>";
                    <?php else:?>
                    init.ajaxGetDownLink(id);
                    <?php endif;?>
                });
            });
        });
    })(jQuery);
    document.getElementById("bdshell_js").src = "http://bdimg.share.baidu.com/static/js/shell_v2.js?cdnversion=" + Math.ceil(new Date()/3600000)
</script>