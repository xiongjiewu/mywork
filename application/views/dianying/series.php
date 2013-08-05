<div class="wrapper">
    <div id="ei-slider" class="ei-slider">
        <ul class="ei-slider-large">
            <?php foreach($topTopicList as $tTopicVal):?>
                <li>
                    <a href="<?php echo APF::get_instance()->get_real_url("/series/info/",$tTopicVal['id'],array(),true);?>">
                        <img src="<?php echo APF::get_instance()->get_image_url($tTopicVal['sImg']);?>" alt="<?php echo $tTopicVal['name'];?>" alt="<?php echo $tTopicVal['name'];?>"/>
                    </a>
                    <div class="ei-title">
                        <h2><?php echo $tTopicVal['name'];?></h2>
                        <h3><?php echo APF::get_instance()->splitStr($tTopicVal['bTitle'],50);?></h3>
                    </div>
                </li>
            <?php endforeach;?>
        </ul><!-- ei-slider-large -->
        <ul class="ei-slider-thumbs">
            <li class="ei-slider-element">Current</li>
            <?php foreach($topTopicList as $tTopicVal):?>
                <li>
                    <a href="<?php echo APF::get_instance()->get_real_url("/series/info/",$tTopicVal['id'],array(),true);?>">
                        <?php echo $tTopicVal['name'];?>
                    </a>
                    <img src="<?php echo APF::get_instance()->get_image_url($tTopicVal['sImg']);?>" alt="<?php echo $tTopicVal['name'];?>" />
                </li>
            <?php endforeach;?>
        </ul><!-- ei-slider-thumbs -->
    </div><!-- ei-slider -->
</div><!-- wrapper -->
<div class="series_main">
    <div class="series_tab">
        <ul>
            <li>
                <a href="<?php echo APF::get_instance()->get_real_url("/series")?>" class="<?php if (empty($diqu)):?>current<?php endif;?>">#全部</a>
            </li>
            <li>
                <a class="<?php if (!empty($diqu) && ($diqu == 1)):?>current<?php endif;?>" href="<?php echo APF::get_instance()->get_real_url("/series","",array("place" => 1))?>">#中国</a>
            </li>
            <li>
                <a class="<?php if (!empty($diqu) && ($diqu == 3)):?>current<?php endif;?>" href="<?php echo APF::get_instance()->get_real_url("/series","",array("place" => 3))?>">#美国</a>
            </li>
        </ul>
    </div>

    <div class="series_list">
        <?php $ulStr0 = $ulStr1 = $ulStr2 = "<ul>";?>
        <?php $ulStr3 = "<ul class='last_series'>"?>
        <?php $topicI = 0;?>
        <?php foreach($topicList as $topicVal):?>
            <?php $valStr = '<li><a href="' . APF::get_instance()->get_real_url("/series/info/",$topicVal['id'],array(),true) . '" class="img">
                    <img src="' . APF::get_instance()->get_image_url($topicVal['mImg']) . '">
                </a>
                <div class="title">
                    <a href="' . APF::get_instance()->get_real_url("/series/info/",$topicVal['id'],array(),true) . '">' . $topicVal['name'] . '</a>
                    <span class="count">
                        <em>' . (empty($topicVal['movieCount']) ? 0 : $topicVal['movieCount']) . '</em>&nbsp;部
                    </span>
                </div>
                <span class="miaoshu">' . $topicVal['bTitle'] . '</span>
                <span class="bg"></span>
            </li>';?>
            <?php if ($topicI % 4 == 0):?>
                <?php $ulStr0 .= $valStr;?>
            <?php elseif ($topicI % 4 == 1):?>
                <?php $ulStr1 .= $valStr;?>
            <?php elseif ($topicI % 4 == 2):?>
                <?php $ulStr2 .= $valStr;?>
            <?php else:?>
                <?php $ulStr3 .= $valStr;?>
            <?php endif;?>
            <?php $topicI++;?>
        <?php endforeach;?>
        <?php echo $ulStr0 . "</ul>"?>
        <?php echo $ulStr1 . "</ul>"?>
        <?php echo $ulStr2 . "</ul>"?>
        <?php echo $ulStr3 . "</ul>"?>
    </div>
    <div class="clear"></div>
</div>

<script type="text/javascript" src="/js/main/jquery.eislideshow.js"></script>
<script type="text/javascript" src="/js/main/jquery.easing.1.3.js"></script>
<script type="text/javascript">
    $(function() {
        $('#ei-slider').eislideshow({
            animation			: 'center',
            autoplay			: true,
            slideshow_interval	: 3000,
            titlesFactor		: 0
        });
        $(".ei-slider-large li img").each(function() {
           $(this).css("height","500px");
        });
    });
</script>