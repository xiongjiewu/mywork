<?php $this->load->view("component/ideapan");//返回顶部与提出意见标签?>
<div class="people_main">
    <!--  左侧信息展示 start -->
    <div class="people_left">
        <div class="people_photo">
            <img src="<?php echo APF::get_instance()->get_image_url($characterInfo['photo']);?>">
        </div>
        <div class="people_info">
            <ul>
                <li class="name">
                    <?php echo $characterInfo['name'];?>&nbsp;&nbsp;<?php echo $characterInfo['EnglishName'];?>
                </li>
                <li class="">
                    生日：
                    <?php if (empty($characterInfo['birthday'])):?>
                        暂无
                    <?php else:?>
                        <?php if (strlen($characterInfo['birthday']) == 4):?>
                            <?php echo $characterInfo['birthday'] . "年";?>
                        <?php elseif (strlen($characterInfo['birthday']) == 5):?>
                            <?php echo substr($characterInfo['birthday'],0,4) . "年" . substr($characterInfo['birthday'],4,1) . "月";?>
                        <?php elseif (strlen($characterInfo['birthday']) == 6):?>
                            <?php echo substr($characterInfo['birthday'],0,4) . "年" . substr($characterInfo['birthday'],4,1) . "月" . substr($characterInfo['birthday'],5,1) . "日";?>
                        <?php elseif (strlen($characterInfo['birthday']) == 7):?>
                            <?php echo substr($characterInfo['birthday'],0,4) . "年" . substr($characterInfo['birthday'],4,1) . "月" . substr($characterInfo['birthday'],5,2) . "日";?>
                        <?php else:?>
                            <?php echo date("Y年m月d日",strtotime($characterInfo['birthday']));?>
                        <?php endif;?>
                    <?php endif;?>
                </li>
                <li class="">
                    星座：<?php echo empty($characterInfo['constellatory']) ? "暂无" : $xingzuoInfo[$characterInfo['constellatory']];?>
                </li>
                <li class="">
                    身高：<?php echo (!empty($characterInfo['height']) && $characterInfo['height'] > 0) ? round($characterInfo['height']) . "cm" : "暂无";?>
                </li>
                <li class="last_info">
                    <?php $characterInfo['birthplace'] = trim($characterInfo['birthplace']);?>
                    出生地：<?php echo empty($characterInfo['birthplace']) ? "暂无" : $characterInfo['birthplace'];?>
                </li>
            </ul>
        </div>
    </div>
    <!--  左侧信息展示 end -->

    <!--  右侧信息展示 start -->
    <div class="people_right">
        <div class="right_top" <?php if ($showImg):?>style="display: none;"<?php endif;?>>
            <div class="jieshao">
                <dl>
                    <dt><?php echo $characterInfo['name'];?>&nbsp;简介</dt>
                    <dd><?php echo empty($characterInfo['jieshao']) ? "暂无" : $characterInfo['jieshao'];?></dd>
                </dl>
            </div>
            <!-- 右侧图片start -->
            <?php if (!empty($peopleImgInfo)):?>
                <div class="peopel_ph">
                    <ul>
                        <li class="title"><?php echo $characterInfo['name'];?>图片集 <a class="" href="<?php echo APF::get_instance()->get_real_url("/people",$characterInfo['id'],array("type" => "img"));?>">了解详情</a></li>
                        <li class="photo_info">
                            <a title="<?php echo $characterInfo['name'];?>" class="" href="<?php echo APF::get_instance()->get_real_url("/people",$characterInfo['id'],array("type" => "img"));?>">
                                <img src="<?php echo APF::get_instance()->get_image_url($peopleImgInfo[0]['photo']);?>">
                            </a>
                        </li>
                        <li class="img_count">目前共有<span><?php echo count($peopleImgInfo);?></span>张图片</li>
                    </ul>
                </div>
            <?php endif;?>
            <!-- 右侧图片end -->
            <?php if (!empty($characterInfo['awardRecording'])):?>
                <?php $awardRecording = json_decode($characterInfo['awardRecording'],true);?>
                <?php if (!empty($awardRecording)):?>
                <div class="jieshao prize">
                    <dl>
                        <dt>获奖经历</dt>
                        <?php $awardRecording = json_decode($characterInfo['awardRecording'],true);?>
                        <?php foreach($awardRecording as $awardVal):?>
                            <dd><?php echo preg_replace("/\[DY\](.*?)\[DY\]/","<a href='/jump?key=$1&type=2'>$1</a>",$awardVal);?></dd>
                        <?php endforeach;?>
                    </dl>
                </div>
                <?php endif;?>
            <?php endif;?>
        </div>

        <!-- 电影展示 start -->
        <?php if (!empty($movieTotalInfos)):?>
            <div class="movie_list" <?php if ($showImg):?>style="display: none;"<?php endif;?>>
                <ul>
                    <li class="movie_title">TA的电影</li>
                    <?php $movieI = 1;?>
                    <?php foreach($movieTotalInfos as $movieVal):?>
                        <?php $url = APF::get_instance()->get_real_url("/detail",$movieVal['id'],array("from" => "people_dy"));?>
                        <li class="<?php if ($movieI % 6 ==0):?>last<?php endif;?>">
                            <a href="<?php echo $url;?>" class="img">
                                <img title="<?php echo $movieVal['name'];?>" src="<?php echo APF::get_instance()->get_image_url($movieVal['image']);?>">
                            </a>
                            <span><a href="<?php echo $url;?>"><?php echo $movieVal['name'];?></a></span>
                        </li>
                        <?php $movieI++;?>
                    <?php endforeach;?>
                </ul>
            </div>
        <?php endif;?>
        <!-- 电影展示 end -->
        <?php if ($showImg):?>
            <div class="movie_list img_list">
                <ul>
                    <li class="movie_title">TA的图片集<a href="<?php echo APF::get_instance()->get_real_url("/people",$characterInfo['id']);?>">返回</a></li>
                    <?php $imgI = 1;?>
                    <?php foreach($peopleImgInfo as $imgIVal):?>
                        <li class="info <?php if ($imgI % 6 ==0):?>last<?php endif;?>">
                            <img title="<?php echo $characterInfo['name'];?>" src="<?php echo APF::get_instance()->get_image_url($imgIVal['photo']);?>">
                        </li>
                        <?php $imgI++;?>
                    <?php endforeach;?>
                </ul>
            </div>
        <?php endif;?>
    </div>
    <!--  右侧信息展示 end -->
    <div class="clear"></div>
</div>