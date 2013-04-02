<div class="row">
    <?php if (!empty($movieList)): ?>
        <div class="span3 bs-docs-sidebar">
            <ul class="nav nav-list bs-docs-sidenav dy_bs-docs-sidenav" style="*width: 220px;">
                <?php $dI = 1;?>
                <?php foreach($monthArr as $monthKey => $monthVal):?>
                    <li><a <?php if ($dI == 1):?>class="click"<?php endif;?> name="<?php echo $monthVal;?>" href="javascript:void(0);" title="点击查看<?php echo $monthKey;?>影片"><i class="icon-chevron-right"></i> <?php echo $monthKey;?></a></li>
                    <?php $dI++;?>
                <?php endforeach;?>
            </ul>
        </div>
        <div class="dy_total">
            <?php foreach ($movieList as $movieKey => $movieVal): ?>
                <?php if (empty($movieVal)){continue;}?>
                <div class="bs-docs-example" id="<?php echo $monthArr[$movieKey];?>">
                    <div class="dy_info_list">
                        <div class="title">
                            <h3>
                                <?php echo $movieKey;?>
                            </h3>
                        </div>
                        <ul class="info_list">
                            <?php foreach ($movieVal as $mKey => $mVal): ?>
                                <li title="点击查看详情" class="dy_info_li">
                                    <div class="dy_name_l">
                                        <a class="dy_name"
                                           href="<?php echo get_url("/detail/index/{$mVal['id']}"); ?>">
                                            <img class="dy_img" src="<?php echo trim(get_config_value("img_base_url"),"/") . $mVal['image'];?>">
                                        </a>
                                        <a class="dy_name"
                                           href="<?php echo get_url("/detail/index/{$mVal['id']}"); ?>">
                                            <?php echo $mVal['name'];?>
                                        </a>
                                        <?php if (empty($shouCangInfo[$mVal['id']])):?>
                                            <span class="shoucang_action shoucang_dy" title="点击收藏" val="<?php echo $mVal['id'];?>"></span>
                                        <?php else:?>
                                            <span class="shoucang_action shoucang_dy_y" title="已收藏"></span>
                                        <?php endif;?>
                                    </div>
                                    <div class="dy_link_down">
                                        <?php if (!empty($watchLinkInfo[$mVal['id']])): ?>
                                        <div class="dy_watch_down">
                                            <div class="watch_down">观看：</div>
                                            <?php $watchLinkI = 1; ?>
                                            <?php foreach ($watchLinkInfo[$mVal['id']] as $watchKey => $watchVal): ?>
                                                <a title="点击观看" class="" href="<?php echo $watchVal['link']; ?>" target="_blank">链接<?php echo $watchLinkI++;?></a>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php endif;?>
                                        <?php if (!empty($downLoadLinkInfo[$mVal['id']])): ?>
                                        <div class="dy_watch_down">
                                            <div class="watch_down">下载：</div>
                                            <?php $downLinkI = 1; ?>
                                            <?php foreach ($downLoadLinkInfo[$mVal['id']] as $downKey => $downVal): ?>
                                                <a title="点击下载" class="" href="<?php echo $downVal['link']; ?>" target="_blank">链接<?php echo $downLinkI++;?></a>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php endif;?>
                                    </div>
                                </li>
                            <?php endforeach;?>
                        </ul>
                    </div>
                </div>
            <?php endforeach;?>
        </div>
    <?php endif; ?>
</div>