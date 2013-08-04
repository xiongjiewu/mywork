<div class="seriesdetail_top">
    <img src="<?php echo APF::get_instance()->get_image_url($topicInfo['bImg']);?>">
    <div class="series_name">
        <h1><?php echo $topicInfo['name'];?></h1>
        <p class="jieshao">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $topicInfo['bTitle'];?></p>
    </div>
    <div class="series_tag">
        <ul>
            <li>标签：</li>
            <li>
                <a href="<?php echo APF::get_instance()->get_real_url("/series","",array("place" => $topicInfo['diqu']));?>">
                    <?php echo $moviePlace[$topicInfo['diqu']];?>
                </a>
            </li>
            <li>
                <a href="<?php echo APF::get_instance()->get_real_url("/series","",array("type" => $topicInfo['type']));?>">
                    <?php echo $movieType[$topicInfo['type']];?>
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="seriesdetail_main">
    <div class="seriesdetail_movie">
        <?php if (!empty($topicMovieList)):?>
            <?php foreach($topicMovieList as $topicMv):?>
                <div class="movie_list">
                    <h1><?php echo $topicMv['name'];?></h1>
                    <ul>
                        <li class="movie_m">
                            <a title="点击观看" href="<?php echo APF::get_instance()->get_real_url("/detail",$topicMv['infoId']);?>" class="movie_img">
                                <img src="<?php echo APF::get_instance()->get_image_url($topicMv['image']);?>">
                            </a>
                            <div class="movie_j">
                                <h2><?php echo $topicMv['sTitle'];?></h2>
                                <div class="p">
                                    <?php echo $topicMv['bTitle'];?>
                                </div>
                            </div>
                        </li>
                        <?php if (!empty($topicMv['img'])):?>
                            <?php $imgCount = count($topicMv['img']);?>
                            <?php $imgI = 1;?>
                            <?php foreach($topicMv['img'] as $imgVal):?>
                                <li class="movie_o<?php if ($imgI == $imgCount || ($imgI > 0 && $imgI % 4 == 0)):?> movie_last<?php endif;?>">
                                    <a title="点击观看" class="movie_o_img" href="<?php echo APF::get_instance()->get_real_url("/detail",$topicMv['infoId']);?>">
                                        <img src="<?php echo APF::get_instance()->get_image_url($imgVal['image']);?>">
                                    </a>
                                    <?php if (!empty($imgVal['title'])):?>
                                        <span class="movie_o_name">
                                            <?php echo $imgVal['title'];?>
                                        </span>
                                    <?php endif;?>
                                </li>
                                <?php $imgI++;?>
                            <?php endforeach;?>
                        <?php endif;?>
                    </ul>
                </div>
            <?php endforeach;?>
        <?php endif;?>
        <div class="clear"></div>
    </div>
</div>