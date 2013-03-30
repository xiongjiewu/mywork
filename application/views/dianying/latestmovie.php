<div class="row">
    <?php if (!empty($movieList)): ?>
        <div class="span3 bs-docs-sidebar">
            <ul class="nav nav-list bs-docs-sidenav dy_bs-docs-sidenav" style="*width: 220px;">
                <?php $dI = 1;?>
                <?php foreach($monthArr as $monthKey => $monthVal):?>
                    <li><a <?php if ($dI == 1):?>class="click"<?php endif;?> name="<?php echo $monthVal;?>" href="#<?php echo $monthVal;?>" title="点击查看<?php echo $monthKey;?>影片"><i class="icon-chevron-right"></i> <?php echo $monthKey;?></a></li>
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
                                <li>
                                    <div class="dy_name_l">
                                        <a class="dy_name"
                                           href="<?php echo get_url("/detail/index/{$mVal['id']}"); ?>">
                                            <img class="dy_img" src="<?php echo trim(get_config_value("img_base_url"),"/") . $mVal['image'];?>">
                                        </a>
                                        <a class="dy_name"
                                           href="<?php echo get_url("/detail/index/{$mVal['id']}"); ?>">
                                            <?php echo $mVal['name'];?>
                                        </a>
                                    </div>
                                    <div class="dy_link_down">
                                        <div class="dy_watch_down">
                                            <div class="watch_down">观看：</div>
                                            <div class="watch_down_link">
                                                <a href="">链接1</a>
                                                <a href="">链接2</a>
                                                <a href="">链接3</a>
                                                <a href="">链接4</a>
                                            </div>
                                        </div>
                                        <div class="dy_watch_down">
                                            <div class="watch_down">下载：</div>
                                            <div class="watch_down_link">
                                                <a href="">链接1</a>
                                                <a href="">链接2</a>
                                                <a href="">链接3</a>
                                                <a href="">链接4</a>
                                            </div>
                                        </div>
                                    </div>
<!--                                    --><?php //if (!empty($watchLinkInfo[$mVal['id']])): ?>
<!--                                        --><?php //$watchLinkI = 1; ?>
<!--                                        --><?php //foreach ($watchLinkInfo[$mVal['id']] as $watchKey => $watchVal): ?>
<!--                                            <a class="dy_watch" href="--><?php //echo $watchVal['link']; ?><!--" target="_blank">观看链接--><?php //echo $watchLinkI++;?><!--</a>-->
<!--                                        --><?php //endforeach; ?>
<!--                                    --><?php //endif;?>
<!--                                    --><?php //if (!empty($downLoadLinkInfo[$mVal['id']])): ?>
<!--                                        --><?php //$downLinkI = 1; ?>
<!--                                        --><?php //foreach ($downLoadLinkInfo[$mVal['id']] as $downKey => $downVal): ?>
<!--                                            <a class="dy_down" href="--><?php //echo $downVal['link']; ?><!--" target="_blank">下载链接--><?php //echo $downLinkI++;?><!--</a>-->
<!--                                        --><?php //endforeach; ?>
<!--                                    --><?php //endif;?>
                                </li>
                            <?php endforeach;?>
                        </ul>
                    </div>
                </div>
            <?php endforeach;?>
        </div>
    <?php endif; ?>
</div>