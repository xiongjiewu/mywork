<?php $this->load->view("component/ideapan");//返回顶部与提出意见标签?>
<div class="dy_ku_main">
    <div class="guide">
        <a href="/">首页 </a>>
        <span> 电影库</span>
    </div>
    <div class="dy_ku_list">
        <div class="weizhi">
            <span class="left">
                <a>电影库</a>
            </span>
            <span class="right">
                <a href="<?php echo APF::get_instance()->get_real_url("/retrieval/");?>">随便逛逛</a>
            </span>
        </div>
        <div class="type_info_list">
            <?php foreach ($movieSortType as $typeKey => $typeVal):?>
                <div class="list">
                    <label>按<?php echo $typeVal['type'];?>:</label>
                    <?php $typeI = 1;$typeLimit = ($typeVal['type'] == "地区") ? ($oneLineCount - 1) : $oneLineCount?>
                    <?php if (count($typeVal['info']) > $typeLimit):?>
                        <a class="more <?php echo $typeVal['moreClass'];?>"><?php echo $typeVal['moreText'];?></a>
                    <?php endif;?>
                    <ul>
                        <li><a class="total <?php if (!empty($typeVal['active'])):?>active<?php endif;?>" href="<?php echo $typeVal['base_url'];?>">全部</a></li>
                        <li class="line"><span>|</span></li>
                        <?php foreach ($typeVal['info'] as $typeValKey => $typeInfoVal):?>
                            <li class="<?php if ($typeI > $typeLimit):?>more_li <?php echo $typeVal['moreTextClass'];?><?php endif;?>">
                                <a class="<?php if (!empty($typeInfoVal['active'])):?>active<?php endif;?>" href="<?php echo $typeInfoVal['url']; ?>">
                                    <?php echo $typeInfoVal['name'];?>
                                </a>
                            </li>
                            <li class="line<?php if ($typeI > $typeLimit):?> more_li <?php echo $typeVal['moreTextClass'];?><?php endif;?>"><span>|</span></li>
                            <?php $typeI++;?>
                        <?php endforeach;?>
                    </ul>
                </div>
                <?php endforeach;?>

                <div class="list">
                    <label>按导演:</label>
                    <a class="more" href="<?php echo APF::get_instance()->get_real_url("/retrieval","",array("b" => "p","s"=> "A"))?>">更多</a>
                    <ul>
                        <?php $daoYanI = 1;?>
                        <?php foreach($daoyanInfo as $daoyanVal):?>
                            <li><a href="<?php echo $daoyanVal['url'];?>" class="<?php if ($daoYanI == 1):?>total <?php endif;?><?php if (!empty($daoyanVal['active'])):?>active<?php endif;?>"><?php echo $daoyanVal['title'];?></a></li>
                            <li class="line"><span>|</span></li>
                            <?php $daoYanI++;?>
                        <?php endforeach;?>
                    </ul>
                </div>

                <div class="list last_list">
                    <label>按演员:</label>
                    <a class="more" href="<?php echo APF::get_instance()->get_real_url("/retrieval","",array("b" => "p","s"=> "A"))?>">更多</a>
                    <ul>
                        <?php $yanYuanI = 1;?>
                        <?php foreach($yanyuanInfo as $yanyuanVal):?>
                            <li><a href="<?php echo $yanyuanVal['url'];?>" class="<?php if ($yanYuanI == 1):?>total <?php endif;?><?php if (!empty($yanyuanVal['active'])):?>active<?php endif;?>"><?php echo $yanyuanVal['title'];?></a></li>
                            <li class="line"><span>|</span></li>
                            <?php $yanYuanI++;?>
                        <?php endforeach;?>
                    </ul>
                </div>

                <!-- 这一版先隐藏专题 start  -->
                <div class="list last_list" style="display: none;">
                    <label>热门专题:</label>
                    <a class="more">更多</a>
                    <ul>
                        <li><a href="">全部</a></li>
                        <li class="line"><span>|</span></li>
                        <li class="">
                            <a href="">
                                专题1
                            </a>
                        </li>
                        <li class="line"><span>|</span></li>
                        <li class="">
                            <a href="">
                                专题2
                            </a>
                        </li>
                        <li class="line"><span>|</span></li>
                    </ul>
                </div>
                <!-- 这一版先隐藏专题 end  -->
            </div>
        <!-- tab列表 start-->
        <div class="movie_sort_tab">
            <ul>
                <?php $tabI = 1;?>
                <?php foreach($movieTabInfo as $tabVal):?>
                    <li>
                        <a href="<?php echo $tabVal['url'];?>" class="<?php if (!empty($tabVal['active'])):?>current_a<?php endif;?><?php if ($tabI == count($movieTabInfo)):?> last_a<?php endif;?>"><?php echo $tabVal['title'];?></a>
                    </li>
                    <?php $tabI++;?>
                <?php endforeach;?>
                </li>
            </ul>
        </div>
        <!-- tab列表 end-->

        <!-- 电影个数展示 start -->
        <?php if (!empty($movieInfos)):?>
        <div class="movie_count">
            共有<span><?php echo $dyCount;?></span>部符合条件的电影
            <div class="movie_sort" style="display: none">
                <span class="sort current_sort">
                    <a>按热门</a>
                    <i></i>
                </span>
                <div class="sort_list">
                    <span class="sort">按搜索</span>
                    <span class="sort">按时间</span>
                    <span class="sort">按播放</span>
                </div>
            </div>
            <span class="title" style="display: none">排序:</span>
        </div>
        <?php endif;?>
        <!-- 电影个数展示 end -->

        <!-- 电影展示开始 start-->
        <div class="movie_display">
                <?php if (!empty($movieInfos)):?>
                    <ul>
                    <?php $dyI = 1;?>
                    <?php foreach($movieInfos as $mVal):?>
                        <?php $url = APF::get_instance()->get_real_url("detail",$mVal['id'],array("from" => "moviceguide_" . $sort));?>
                        <li class="<?php if ($dyI % 5 == 0):?>last_m<?php endif;?>" title="<?php echo $mVal['name'];?>">
                            <a href="<?php echo $url;?>" class="tupian">
                                <img src="<?php echo APF::get_instance()->get_image_url($mVal['image']);?>" alt="<?php echo $mVal['name'];?>">
                            </a>
                            <span class="name">
                                <a href="<?php echo $url;?>"><?php echo $mVal['name'];?></a>
                            </span>
                            <?php $zhuyan = str_replace("、","/",$mVal['zhuyan']);?>
                            <?php $zhuyaoArr = explode("/",$zhuyan);?>
                            <span class="zhuyan">
                                <?php if (empty($zhuyan)):?>
                                    暂无
                                <?php else:?>
                                    <?php foreach($zhuyaoArr as $zhuyanVal):?>
                                        <a href="<?php echo APF::get_instance()->get_real_url("/jump","",array("type" => 1,"key" => $zhuyanVal));?>"><?php echo $zhuyanVal?></a>&nbsp;
                                    <?php endforeach;?>
                                <?php endif;?>
                            </span>
                            <div class="dy_info">
                                <?php if ($sort == "hot"):?>
                                    <div>播&nbsp;放:</div><em>&nbsp<?php echo $mVal['playNum'];?>次</em>
                                <?php elseif($sort == "search"):?>
                                    <div>被&nbsp;搜:</div><em>&nbsp<?php echo $mVal['searchNum'];?>次</em>
                                <?php elseif($sort == "good"):?>
                                    <div>评&nbsp;分:</div><em>&nbsp<?php echo round($mVal['score'],1);?>分</em>
                                <?php elseif($sort == "show" || $sort == "comming"):?>
                                    <div>上&nbsp;映:</div><em>&nbsp<?php echo date("Y-m-d",$mVal['time1']);?></em>
                                <?php elseif($sort == "like"):?>
                                    <div>被摇到:</div><em>&nbsp<?php echo $mVal['yaoyaoNum'];?>次</em>
                                <?php elseif($sort == "down"):?>
                                    <div>被下载:</div><em>&nbsp<?php echo $mVal['downNum'];?>次</em>
                                <?php elseif($sort == "new"):?>
                                    <?php $day = date("Ymd",$mVal['createtime']);?>
                                    <?php if ($day == date("Ymd")):?>
                                        <div>更新于:</div><em>&nbsp<?php echo "今天" . date("H:i:s",$mVal['createtime']);?></em>
                                    <?php elseif ($day == (date("Ymd") - 1)):?>
                                        <div>更新于:</div><em>&nbsp<?php echo "昨天" . date("H:i:s",$mVal['createtime']);?></em>
                                    <?php else:?>
                                        <div>更新于:</div><em>&nbsp<?php echo date("Y-m-d",$mVal['createtime']);?></em>
                                    <?php endif;?>
                                <?php else:?>
                                    <div>评&nbsp;分:</div><em>&nbsp<?php echo round($mVal['score'],1);?>分</em>
                                <?php endif;?>
                            </div>
                        </li>
                        <?php $dyI++;?>
                    <?php endforeach;?>
                    </ul>
                <?php else:?>
                    <ul class="no_result">
                        悲剧了，暂无搜索结果。请尝试其他检索方式或使用搜索找您喜欢的内容。<br><br>
                        或
                        <?php if (empty($userId)):?>
                            <a target="_blank" href="<?php echo APF::get_instance()->get_real_url("/login",'',array("bgurl" => base64_encode("/usercenter/feedback/")));?>">反馈你想看</a>
                        <?php else:?>
                            <a target="_blank" href="<?php echo APF::get_instance()->get_real_url("/usercenter/feedback/");?>">反馈你想看</a>
                        <?php endif;?>。
                    </ul>
                <?php endif;?>

            <?php if ($dyCount > $limit):?>
                <div class="page_info">
                    <table class="page">
                        <tr>
                            <td>
                                <?php $this->load->view("component/pagenew", array("fenye" => $fenye));?>
                            </td>
                        </tr>
                    </table>
                </div>
            <?php endif;?>
        </div>
        <!-- 电影展示开始 end-->
    </div>

    <!--右侧展示start -->
    <div class="guide_right">
        <!-- 今日推荐 todo:这一版先隐藏 start  -->
        <div class="tuijian" style="display: none;">
            <span class="tuijian_title">今日推荐</span>
            <ul>
                <li title="">
                    <a class="tuijian_img">
                        <img src="">
                    </a>
                    <span class="tuijian_name">
                        <a class="">逆爱</a>
                    </span>
                    <span class="tuijian_name">2个女同的凄美爱情</span>
                </li>
                <li class="two">
                    <a class="tuijian_img">
                        <img src="">
                    </a>
                    <span class="tuijian_name">
                        <a class="">逆爱</a>
                    </span>
                    <span class="tuijian_name">2个女同的凄美爱情</span>
                </li>
                <li>
                    <a class="tuijian_img">
                        <img src="">
                    </a>
                    <span class="tuijian_name">
                        <a class="">逆爱</a>
                    </span>
                    <span class="tuijian_name">2个女同的凄美爱情</span>
                </li>
                <li class="two">
                    <a class="tuijian_img">
                        <img src="">
                    </a>
                    <span class="tuijian_name">
                        <a class="">逆爱</a>
                    </span>
                    <span class="tuijian_name">2个女同的凄美爱情</span>
                </li>
            </ul>
        </div>
        <!-- 今日推荐 end  -->

        <!-- 明星专题 todo:这一版先隐藏 start  -->
        <div class="tuijian mingxingzhuanti" style="display: none;">
            <span class="tuijian_title">明星专题</span>
            <dl title="">
                <dt>
                    <a class="zhuanti_img">
                        <img src="">
                    </a>
                </dt>
                <dd class="name">
                    成龙
                </dd>
                <dd class="icon"></dd>
            </dl>

            <dl>
                <dt>
                    <a class="zhuanti_img">
                        <img src="">
                    </a>
                </dt>
                <dd class="name">
                    成龙
                </dd>
                <dd class="icon"></dd>
            </dl>

            <dl>
                <dt>
                    <a class="zhuanti_img">
                        <img src="">
                    </a>
                </dt>
                <dd class="name">
                    成龙
                </dd>
                <dd class="icon"></dd>
            </dl>

            <dl class="last_dl">
                <dt>
                    <a class="zhuanti_img">
                        <img src="">
                    </a>
                </dt>
                <dd class="name">
                    成龙
                </dd>
                <dd class="icon"></dd>
            </dl>
        </div>
        <!-- 明星专题 end  -->

        <!-- 本周票房榜,这一版先把本周票房榜放着，充内容  start-->
        <div class="tuijian">
            <span class="tuijian_title">本周票房榜</span>
            <ul>
                <?php $weekPiaofangI = 1;?>
                <?php foreach($weekPiaofangMovieInfo as $weekPiaofangVal):?>
                    <?php $url = APF::get_instance()->get_real_url("detail",$weekPiaofangVal['id']);?>
                    <li class="<?php if ($weekPiaofangI % 2 == 0):?>two<?php endif;?>" title="<?php echo $weekPiaofangVal['name'];?>">
                        <a class="tuijian_img" href="<?php echo $url;?>">
                            <img src="<?php echo APF::get_instance()->get_image_url($weekPiaofangVal['image']);?>" alt="<?php echo $weekPiaofangVal['name'];?>">
                        </a>
                    <span class="tuijian_name">
                        <a class="" href="<?php echo $url;?>"><?php echo $weekPiaofangVal['name'];?></a>
                    </span>
                        <span class="tuijian_name">票房:<?php echo ($weekPiaofangVal['piaofang'] > 10000) ? round($weekPiaofangVal['piaofang'] / 10000,2) . "亿" : round($weekPiaofangVal['piaofang'],2) . "万";?></span>
                    </li>
                    <?php $weekPiaofangI++;?>
                <?php endforeach;?>
            </ul>
        </div>
        <!-- 本周票房榜 end  -->

        <!-- 历史票房榜  start-->
        <div class="tuijian mingxingzhuanti">
            <span class="tuijian_title">历史票房榜</span>
            <ul>
                <?php $piaofangI = 1;?>
                <?php foreach($piaofangMovieInfo as $piaofangVal):?>
                    <?php $url = APF::get_instance()->get_real_url("detail",$piaofangVal['id']);?>
                    <li class="<?php if ($piaofangI % 2 == 0):?>two<?php endif;?>" title="<?php echo $piaofangVal['name'];?>">
                        <a class="tuijian_img" href="<?php echo $url;?>">
                            <img src="<?php echo APF::get_instance()->get_image_url($piaofangVal['image']);?>" alt="<?php echo $piaofangVal['name'];?>">
                        </a>
                    <span class="tuijian_name">
                        <a class="" href="<?php echo $url;?>"><?php echo $piaofangVal['name'];?></a>
                    </span>
                        <span class="tuijian_name">票房:<?php echo ($piaofangVal['piaofang'] > 10000) ? round($piaofangVal['piaofang'] / 10000,2) . "亿" : round($piaofangVal['piaofang'],2) . "万";?></span>
                    </li>
                    <?php $piaofangI++;?>
                <?php endforeach;?>
            </ul>
        </div>
        <!-- 历史票房榜 end  -->

    </div>
    <!--右侧展示end -->

    <div class="clear"></div>
</div>