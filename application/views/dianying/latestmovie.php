<div class="row">
    <?php if (!empty($movieList)): ?>
        <div class="span3 bs-docs-sidebar">
            <ul class="nav nav-list bs-docs-sidenav dy_bs-docs-sidenav" style="*width: 220px;">
                <?php foreach($monthArr as $monthKey => $monthVal):?>
                    <li><a href="#<?php echo $monthVal;?>" title="点击查看影片详情"><i class="icon-chevron-right"></i> <?php echo $monthKey;?></a></li>
                <?php endforeach;?>
            </ul>
        </div>
        <div class="dy_total">
            <?php foreach ($movieList as $movieKey => $movieVal): ?>
                <?php if (empty($movieVal)){continue;}?>
                <a id="<?php echo $monthArr[$movieKey];?>"></a>
                <div class="bs-docs-example">
                    <div class="dy_info_list">
                        <div class="title">
                            <?php echo $movieKey;?>
                        </div>
                        <ul class="info_list">
                            <?php $movieListI = 1;?>
                            <?php $movieListCount = count($movieVal);?>
                            <?php foreach ($movieVal as $mKey => $mVal): ?>
                                <li class="<?php if ($movieListI == $movieListCount): ?>lastOne<?php endif; ?>">
                                    <div class="dy_name_l">
                                        <a class="dy_name"
                                           href="<?php echo get_url("/detail/index/{$mVal['id']}"); ?>"><?php echo $mVal['name'];?></a>
                                    </div>
                                    <?php if (!empty($watchLinkInfo[$mVal['id']])): ?>
                                        <?php $watchLinkI = 1; ?>
                                        <?php foreach ($watchLinkInfo[$mVal['id']] as $watchKey => $watchVal): ?>
                                            <a class="dy_watch" href="<?php echo $watchVal['link']; ?>" target="_blank">观看链接<?php echo $watchLinkI++;?></a>
                                        <?php endforeach; ?>
                                    <?php endif;?>
                                    <?php if (!empty($downLoadLinkInfo[$mVal['id']])): ?>
                                        <?php $downLinkI = 1; ?>
                                        <?php foreach ($downLoadLinkInfo[$mVal['id']] as $downKey => $downVal): ?>
                                            <a class="dy_down" href="<?php echo $downVal['link']; ?>" target="_blank">下载链接<?php echo $downLinkI++;?></a>
                                        <?php endforeach; ?>
                                    <?php endif;?>
                                </li>
                                <?php $movieListI++; ?>
                            <?php endforeach;?>
                        </ul>
                    </div>
                </div>
            <?php endforeach;?>
        </div>
    <?php endif; ?>
</div>