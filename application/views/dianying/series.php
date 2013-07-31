<div class="series_main">
    <div class="series_top">
        <div class="series_top_left">
            <ul>
                <?php foreach($topTopicList as $tTopicVal):?>
                    <li>
                        <a href="<?php echo APF::get_instance()->get_real_url("/series/info/",$tTopicVal['id'],array(),true);?>">
                            <img src="<?php echo APF::get_instance()->get_image_url($tTopicVal['bImg']);?>">
                        </a>
                        <div class="info" style="display: none;">
                            <h3>钢铁下1</h3>
                            <span>
                                钢铁下1钢铁下1钢铁下1
                            </span>
                        </div>
                    </li>
                <?php endforeach;?>
            </ul>
            <div class="img_change">
                <?php for($i = 0;$i < count($topTopicList);$i++):?>
                    <span class="<?php if ($i == 0):?>current<?php endif;?>" index="<?php echo $i;?>"></span>
                <?php endfor;?>
            </div>
        </div>

        <div class="series_top_right">
            <h2>热门系列推荐</h2>
            <ul>
                <?php foreach($rightTopicList as $rTopicVal):?>
                    <li class="">
                        <a href="<?php echo APF::get_instance()->get_real_url("/series/info/",$rTopicVal['id'],array(),true);?>">
                            <img src="<?php echo APF::get_instance()->get_image_url($rTopicVal['sImg']);?>">
                        </a>
                    </li>
                <?php endforeach;?>
            </ul>
        </div>
    </div>

    <div class="series_tab">
        <ul>
            <li>
                <a href="<?php echo APF::get_instance()->get_real_url("/series")?>" class="<?php if (empty($diqu)):?>current<?php endif;?>">#全部</a>
            </li>
            <li>
                <a class="<?php if (!empty($diqu) && ($diqu == 3)):?>current<?php endif;?>" href="<?php echo APF::get_instance()->get_real_url("/series","",array("place" => 3))?>">#美国</a>
            </li>
        </ul>
    </div>

    <div class="series_list">
        <?php $ulStr0 = $ulStr1 = $ulStr2 = $ulStr3 = "<ul>";?>
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