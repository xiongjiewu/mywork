<div class="search_main">
    <div class="search_left">
        <?php $typeCount = count($movieSortType);?>
        <?php $typeI = 1;?>
        <?php foreach ($movieSortType as $typeKey => $typeVal):?>
        <ul <?php if($typeI == $typeCount):?>class="last_one"<?php endif;?>>
            <li class="title_text"><?php echo $typeVal['type'];?></li>
            <li class="current">
                <a href="<?php echo $typeVal['base_url'];?>">全部</a>
            </li>
            <?php foreach ($typeVal['info'] as $typeValKey => $typeInfoVal): ?>
                <li>
                    <a href="<?php echo $typeVal['base_url'] . $typeValKey; ?>/">
                        <?php echo $typeInfoVal;?>
                    </a>
                </li>
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
                    <li <?php if ($i == 0):?>class="first_one"<?php endif;?> title="点击查看详情">
                        <div class="search_dy_img">
                            <a href="<?php echo get_url("/detail/index/{$idStr}");?>/">
                                <img alt="" src="<?php echo trim(APF::get_instance()->get_config_value("img_base_url"),"/") . $movieVal['image'];?>">
                            </a>
                        </div>
                        <div class="search_dy_detail">
                            <p class="title">
                                <a href="<?php echo get_url("/detail/index/{$idStr}");?>/">
                                    <?php echo $movieVal['name'];?>
                                </a>
                            </p>
                            <p class="zhuyan">
                                <span>主演：</span><?php echo $movieVal['zhuyan'];?>
                            </p>
                            <p class="other">
                                <span>导演：</span><?php echo $movieVal['daoyan'] ? $movieVal['daoyan'] : "暂无";?>
                            </p>
                            <p class="other">
                                <span>类型：</span><?php echo $movieType[$movieVal['type']];?>
                            </p>
                            <p class="jianjie">
                                <span>简介：</span><?php echo $movieVal['jieshao'];?>
                            </p>
                            <?php if (!empty($watchLinkInfo[$movieVal['id']])):?>
                            <p class="watch_link">
                                <?php $wRes = array();?>
                                <?php foreach ($watchLinkInfo[$movieVal['id']] as $watchKey => $watchVal): ?>
                                    <?php if (empty($wRes[$watchVal['player']])){$wRes[$watchVal['player']] = $watchVal;}?>
                                <?php endforeach;?>
                                <?php $countI = 1;?>
                                <?php foreach($wRes as $wInfo):?>
                                    <?php if ($countI > 4){break;}?>
                                    <a title="点击观看(<?php if ($wInfo['shoufei'] == 1):?>免费<?php else:?>收费<?php endif;?>)" target="_blank" href="<?php echo $wInfo['link'];?>">
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
            <?php endif;?>
        </ul>
    </div>
</div>