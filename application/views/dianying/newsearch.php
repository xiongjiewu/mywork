<?php $this->load->view("component/ideapan");//返回顶部与提出意见标签?>
<div class="search_main">
    <div class="guide">
        <a href="/">首页 </a>>
        <span> 搜'<?php echo $searchW;?>'相关的影片</span>
    </div>
    <div class="search_left">
        <?php $typeCount = count($movieSortType);?>
        <?php $typeI = 1;?>
        <?php foreach ($movieSortType as $typeKey => $typeVal):?>
            <ul <?php if($typeI == $typeCount):?>class="last_one"<?php endif;?>>
                <li class="title_text"><?php echo $typeVal['type'];?></li>
                <?php if ($typeKey == 0 ):?>
                    <li <?php if ($type == "all"):?>class="current"<?php endif;?>>
                        <a href="/search/index/all/<?php echo $year;?>/<?php echo $diqu;?>?key=<?php echo $searchW;?>">全部</a>
                    </li>
                <?php elseif($typeKey == 1):?>
                    <li <?php if ($year == "all"):?>class="current"<?php endif;?>>
                        <a href="/search/index/<?php echo $type;?>/all/<?php echo $diqu;?>?key=<?php echo $searchW;?>">全部</a>
                    </li>
                <?php else:?>
                    <li <?php if ($diqu == "all"):?>class="current"<?php endif;?>>
                        <a href="/search/index/<?php echo $type;?>/<?php echo $year;?>/all?key=<?php echo $searchW;?>">全部</a>
                    </li>
                <?php endif;?>
                <?php foreach ($typeVal['info'] as $typeValKey => $typeInfoVal): ?>
                    <?php if ($typeKey == 0 ):?>
                        <li <?php if ($type == $typeValKey):?>class="current"<?php endif;?>>
                            <a href="/search/index/<?php echo $typeValKey;?>/<?php echo $year;?>/<?php echo $diqu;?>?key=<?php echo $searchW;?>"><?php echo $typeInfoVal;?></a>
                        </li>
                    <?php elseif($typeKey == 1):?>
                        <li <?php if ($year == $typeValKey):?>class="current"<?php endif;?>>
                            <a href="/search/index/<?php echo $type;?>/<?php echo $typeValKey;?>/<?php echo $diqu;?>?key=<?php echo $searchW;?>"><?php echo $typeInfoVal;?></a>
                        </li>
                    <?php else:?>
                        <li <?php if ($diqu == $typeValKey):?>class="current"<?php endif;?>>
                            <a href="/search/index/<?php echo $type;?>/<?php echo $year;?>/<?php echo $typeValKey;?>?key=<?php echo $searchW;?>"><?php echo $typeInfoVal;?></a>
                        </li>
                    <?php endif;?>
                <?php endforeach;?>
            </ul>
            <?php $typeI++;?>
       <?php endforeach;?>
    </div>
    <div class="search_dy_info">
        <ul>
            <?php if (!empty($searchMovieInfo)):?>
                <?php $i = 0;?>
                <?php foreach($searchMovieInfo as $movieVal):?>
                    <?php $idStr = APF::get_instance()->encodeId($movieVal['id']);?>
                    <?php $zhuyaoArr = explode("、",$movieVal['zhuyan']);?>
                    <?php $daoyanArr = explode("、",$movieVal['daoyan']);?>
                    <li <?php if ($i == 0):?>class="first_one"<?php endif;?> title="点击查看详情">
                        <div class="search_dy_img">
                            <a href="<?php echo get_url("/detail/index/{$idStr}?from=search");?>">
                                <img alt="<?php echo $movieVal['name'];?>" src="<?php echo trim(APF::get_instance()->get_config_value("img_base_url"),"/") . $movieVal['image'];?>">
                            </a>
                        </div>
                        <div class="search_dy_detail">
                            <p class="title">
                                <a href="<?php echo get_url("/detail/index/{$idStr}?from=search");?>">
                                    <?php echo $movieVal['s_name'];?>
                                </a>
                                <?php if (!empty($movieVal['nianfen'])):?>
                                <span class="nianfen">年份：<?php echo $movieVal['nianfen'];?></span>
                                <?php endif;?>
                                <?php if ($movieVal['exist_down'] == 1):?>
                                    <span class="nianfen down_link">
                                        [有下载链接]
                                    </span>
                                <?php endif;?>
                            </p>
                            <p class="zhuyan">
                                <span>主演：</span>
                                <?php if (empty($zhuyaoArr)):?>
                                    暂无
                                <?php else:?>
                                    <?php foreach($zhuyaoArr as $zhuyanVal):?>
                                        <a href="<?php echo APF::get_instance()->get_real_url("/jump","",array("type" => 1,"key" => strip_tags($zhuyanVal)));?>"><?php echo $zhuyanVal?></a>&nbsp;&nbsp;
                                    <?php endforeach;?>
                                <?php endif;?>
                            </p>
                            <p class="other">
                                <span>导演：</span>
                                <?php if (empty($daoyanArr)):?>
                                    暂无
                                <?php else:?>
                                    <?php foreach($daoyanArr as $daoyanVal):?>
                                        <a href="<?php echo APF::get_instance()->get_real_url("/jump","",array("type" => 1,"key" => strip_tags($daoyanVal)));?>"><?php echo $daoyanVal;?></a>&nbsp;&nbsp;
                                    <?php endforeach;?>
                                <?php endif;?>
                            </p>
                            <p class="other">
                                <span>类型：</span><?php echo $movieType[$movieVal['type']];?>
                            </p>
                            <p class="jianjie">
                                <span>简介：</span><?php echo strip_tags($movieVal['jieshao']);?>
                            </p>
                            <?php if (!empty($watchLinkInfo[$movieVal['id']])):?>
                            <p class="watch_link">
                                <?php $wRes = array();?>
                                <?php foreach ($watchLinkInfo[$movieVal['id']] as $watchKey => $watchVal): ?>
                                    <?php if (empty($wRes[$watchVal['player']])){$wRes[$watchVal['player']] = $watchVal;}?>
                                <?php endforeach;?>
                                <?php $countI = 1;?>
                                <?php foreach($wRes as $wInfo):?>
                                    <?php if ($countI > 8){break;}?>
                                    <?php $url = APF::get_instance()->get_real_url("play",$wInfo['infoId'],array("id"=>$wInfo['id']));?>
                                    <a title="点击观看(<?php if ($wInfo['shoufei'] == 1):?>免费<?php else:?>收费<?php endif;?>)" target="_blank" href="<?php echo $url;?>">
                                        <img alt="<?php echo $movieVal['name'];?>" src="/images/webcon/icon<?php echo $wInfo['player'];?>.png">
                                    </a>
                                    <?php $countI++;?>
                                <?php endforeach;?>
                            </p>
                            <?php endif;?>
                        </div>
                    </li>
                    <?php $i++;?>
                <?php endforeach;?>
                <?php else:?>
                    <li class="no_data">
                        <div class="error_imga">
                            <img src="/images/error.png">
                        </div>
                        <div class="error_text">
                            糟糕，您搜索的信息暂无，您可以尝试换个方式搜索！
                        </div>
                    </li>
            <?php endif;?>
        </ul>
    </div>
    <div class="clear"></div>
</div>