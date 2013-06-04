<div class="home_top">
    <ul>
       <?php foreach($topMovieInfos as $topVal):?>
            <?php $idStr = APF::get_instance()->encodeId($topVal['id']);?>
            <?php $zhuyan = str_replace("、","/",$topVal['zhuyan'])?>
            <?php $daoyan = str_replace("、","/",$topVal['daoyan'])?>
            <?php $nianfen = $topVal['nianfen'];?>
        <li title="点击查看详情">
            <a class="first_img" href="/detail/index/<?php echo $idStr;?>/">
                <img alt="<?php echo $topVal['name'];?>" src="<?php echo trim(APF::get_instance()->get_config_value("img_base_url"), "/") . $topVal['image'];?>">
            </a>
            <div class="home_top_dy_detail">
                <div class="home_top_dy_detail_info detail_name"><?php echo $topVal['name'];?></div>
                <div class="home_top_dy_detail_info">主演：<?php echo empty($zhuyan) ? "暂无" : $zhuyan;?></div>
                <div class="home_top_dy_detail_info">导演：<?php echo empty($daoyan) ? "暂无" : $daoyan;?></div>
                <div class="home_top_dy_detail_info">年份：<?php echo empty($nianfen) ? "暂无" : $nianfen;?></div>
            </div>
        </li>
        <?php endforeach;?>
    </ul>
    <div class="home_web">
        <div class="home_web_count">
            <span class="dy_count">
                <?php echo $dyCount;?>
            </span>
        </div>
    </div>
</div>
<div class="home_middel_main">
    <div class="tab_list">
        <ul>
            <li class="tab_list_li_1" title="摇一摇有惊喜">
                <a href="javascript:void(0);"></a>
            </li>
            <li class="tab_list_li_2">
                <a href="/usercenter/feedback/"></a>
            </li>
            <li class="tab_list_li_3">
                <a href="/upcomingmovie/"></a>
            </li>
            <li class="tab_list_li_4">
                <a href="/upcomingmovie/"></a>
            </li>
        </ul>
    </div>
    <!-- 最新上映 start   -->
    <div class="movice_info_list movice_info_list_list">
        <div class="title">
            <h2>
                <a class="" href="/latestmovie/">
                    最新上映
                </a>
            </h2>
        </div>
        <div class="info_list">
            <ul>
                <?php $newestDyInfoI = 0;?>
                <?php foreach($newestDyInfo as $dyInfoVal):?>
                    <?php $idStr = APF::get_instance()->encodeId($dyInfoVal['id']);?>
                    <?php $image = trim(APF::get_instance()->get_config_value("img_base_url"), "/") . $dyInfoVal['image'];?>
                    <?php $name = $dyInfoVal['name'];?>
                    <?php $jieshao = str_replace("　　","",trim($dyInfoVal['jieshao']));?>
                    <?php $type = $dyInfoVal['type'];?>
                    <?php $typeText = $moviceType[$dyInfoVal['type']];?>
                    <?php $zhuyan = str_replace("、","/",$dyInfoVal['zhuyan']);?>
                    <?php $zhuyaoArr = explode("/",$zhuyan);?>
                    <?php if ($newestDyInfoI == 0):?>
                        <li class="first_one_li" idStr="<?php echo $idStr;?>" zhuyan="<?php echo $zhuyan;?>" type="<?php echo $type;?>" typeText="<?php echo $typeText;?>" name="<?php echo $name;?>" img="<?php echo $image;?>" jieshao="<?php echo $jieshao;?>">
                            <a class="first_img" href="/detail/index/<?php echo $idStr;?>/">
                                <img alt="<?php echo $name;?>" src="<?php echo $image;?>">
                            </a>
                            <p class="first_name">
                                <a class="name" href="/detail/index/<?php echo $idStr;?>/">
                                    <?php echo $name;?>
                                </a>
                                <a class="dy_type" href="/moviceguide/type/<?php echo $type;?>/">
                                    [<?php echo $typeText;?>]
                                </a>
                            </p>
                            <p class="first_zhuyan">
                                <span>主演:</span>
                                <?php echo empty($zhuyan) ? "暂无" : $zhuyan;?>
                            </p>
                            <p class="first_jieshao">
                                <span>简介:</span><?php echo $jieshao;?>
                            </p>
                        </li>
                    <?php else:?>
                        <li class="other_li" type="<?php echo $type;?>" idStr="<?php echo $idStr;?>" zhuyan="<?php echo $zhuyan;?>" typeText="<?php echo $typeText;?>" name="<?php echo $name;?>" img="<?php echo $image;?>" jieshao="<?php echo $jieshao;?>">
                            <a class="img" href="/detail/index/<?php echo $idStr;?>/">
                                <img alt="<?php echo $name;?>" src="<?php echo $image;?>">
                            </a>
                            <p class="name">
                                <a href="">
                                    <?php echo $name;?>
                                </a>
                            </p>
                            <p class="zhuyan">
                                <?php if (empty($zhuyan)):?>
                                    暂无
                                <?php else:?>
                                    <?php foreach($zhuyaoArr as $zhuyanVal):?>
                                        <a href="/search?key=<?php echo $zhuyanVal?>"><?php echo $zhuyanVal?></a>
                                    <?php endforeach;?>
                                <?php endif;?>
                            </p>
                        </li>
                    <?php endif;?>
                    <?php $newestDyInfoI++;?>
                <?php endforeach;?>
            </ul>
        </div>
    </div>
    <!-- 最新上映 end   -->

    <!-- 今日推荐  start -->
    <?php if (!empty($todayMovieList)):?>
    <div class="movice_info_list">
        <div class="title">
            <h2>
                <a class="" href="/latestmovie/">
                    今日推荐
                </a>
            </h2>
        </div>
        <div class="info_list">
            <ul>
            <?php foreach($todayMovieList as $todayVal):?>
                    <?php $name = $todayVal['name'];?>
                    <?php $idStr = APF::get_instance()->encodeId($todayVal['id']);?>
                    <?php $image = trim(APF::get_instance()->get_config_value("img_base_url"), "/") . $todayVal['image'];?>
                    <?php $zhuyan = str_replace("、","/",$todayVal['zhuyan']);?>
                    <?php $zhuyaoArr = explode("/",$zhuyan);?>
                    <li class="today_info_li">
                        <a href="/detail/index/<?php echo $idStr;?>" class="img img_today">
                            <img alt="<?php echo $name;?>" src="<?php echo $image;?>">
                        </a>
                        <p class="today_name">
                            <a class="t_name" href="/detail/index/<?php echo $idStr;?>">
                                <?php echo $name;?>
                            </a>
                            <?php if (empty($zhuyan)):?>
                                暂无
                            <?php else:?>
                                <?php foreach($zhuyaoArr as $zhuyanVal):?>
                                    <a class="t_zhuyan" href="/search?key=<?php echo $zhuyanVal?>"><?php echo $zhuyanVal?></a>
                                <?php endforeach;?>
                            <?php endif;?>
                        </p>
                    </li>
            <?php endforeach;?>
            </ul>
        </div>
    </div>
    <?php endif;?>
    <!-- 今日推荐  end -->

    <!-- 即将上映 start   -->
    <div class="movice_info_list movice_info_list_list">
        <div class="title">
            <h2>
                <a class="" href="/latestmovie/">
                    即将上映
                </a>
            </h2>
        </div>
        <div class="info_list">
            <ul>
                <?php $willDyInfoI = 0;?>
                <?php foreach($willDyInfo as $dyInfoVal):?>
                    <?php $idStr = APF::get_instance()->encodeId($dyInfoVal['id']);?>
                    <?php $image = trim(APF::get_instance()->get_config_value("img_base_url"), "/") . $dyInfoVal['image'];?>
                    <?php $name = $dyInfoVal['name'];?>
                    <?php $jieshao = str_replace("　　","",trim($dyInfoVal['jieshao']));?>
                    <?php $type = $dyInfoVal['type'];?>
                    <?php $typeText = $moviceType[$dyInfoVal['type']];?>
                    <?php $zhuyan = str_replace("、","/",$dyInfoVal['zhuyan']);?>
                    <?php $zhuyaoArr = explode("/",$zhuyan);?>
                    <?php if ($willDyInfoI == 0):?>
                        <li class="first_one_li" idStr="<?php echo $idStr;?>" zhuyan="<?php echo $zhuyan;?>" type="<?php echo $type;?>" typeText="<?php echo $typeText;?>" name="<?php echo $name;?>" img="<?php echo $image;?>" jieshao="<?php echo $jieshao;?>">
                            <a class="first_img" href="/detail/index/<?php echo $idStr;?>/">
                                <img alt="<?php echo $name;?>" src="<?php echo $image;?>">
                            </a>
                            <p class="first_name">
                                <a class="name" href="/detail/index/<?php echo $idStr;?>/">
                                    <?php echo $name;?>
                                </a>
                                <a class="dy_type" href="/moviceguide/type/<?php echo $type;?>/">
                                    [<?php echo $typeText;?>]
                                </a>
                            </p>
                            <p class="first_zhuyan">
                                <span>主演:</span><?php echo empty($zhuyan) ? "暂无" : $zhuyan;?>
                            </p>
                            <p class="first_jieshao">
                                <span>简介:</span><?php echo $jieshao;?>
                            </p>
                        </li>
                    <?php else:?>
                        <li class="other_li" type="<?php echo $type;?>" idStr="<?php echo $idStr;?>" zhuyan="<?php echo $zhuyan;?>" typeText="<?php echo $typeText;?>" name="<?php echo $name;?>" img="<?php echo $image;?>" jieshao="<?php echo $jieshao;?>">
                            <a class="img" href="/detail/index/<?php echo $idStr;?>/">
                                <img alt="<?php echo $name;?>" src="<?php echo $image;?>">
                            </a>
                            <p class="name">
                                <a href="">
                                    <?php echo $name;?>
                                </a>
                            </p>
                            <p class="zhuyan">
                                <?php if (empty($zhuyan)):?>
                                    暂无
                                <?php else:?>
                                    <?php foreach($zhuyaoArr as $zhuyanVal):?>
                                        <a href="/search?key=<?php echo $zhuyanVal?>"><?php echo $zhuyanVal?></a>
                                    <?php endforeach;?>
                                <?php endif;?>
                        </li>
                    <?php endif;?>
                    <?php $willDyInfoI++;?>
                <?php endforeach;?>
            </ul>
        </div>
    </div>
    <!-- 即将上映 end   -->

    <!-- 重温经典 start   -->
    <div class="movice_info_list movice_class_info_list">
        <div class="title">
            <h2>
                <a class="" href="/classmovie/">
                    经典电影
                </a>
            </h2>
        </div>
        <div class="info_list">
            <ul>
                <li class="first_one_li">
                    <?php foreach($movieSortType as $sortVal):?>
                        <div>
                            <div class="sort_type">
                                <span class="type_text"><?php echo $sortVal['type'];?></span>
                            </div>
                            <div class="sort_list">
                            <?php $sortI = 1;?>
                            <?php foreach($sortVal['info'] as $typeInfoKey => $typeInfoVal):?>
                                <span class="type_info">
                                    <a href="<?php echo $sortVal['base_url'] . $typeInfoKey;?>"><?php echo $typeInfoVal;?></a>
                                </span>
                                <?php if ($sortI % 3 == 0):?>
                                </div>
                                <div class="sort_list">
                                <?php endif;?>
                                <?php $sortI++;?>
                            <?php endforeach;?>
                            </div>
                        </div>
                    <?php endforeach;?>
                </li>
                <?php foreach($classDyInfo as $dyInfoVal):?>
                    <?php $zhuyan = str_replace("、","/",$dyInfoVal['zhuyan']);?>
                    <?php $zhuyaoArr = explode("/",$zhuyan);?>
                    <?php $idStr = APF::get_instance()->encodeId($dyInfoVal['id']);?>
                    <li class="other_li">
                        <a class="img" href="/detail/index/<?php echo $idStr;?>/">
                            <img alt="<?php echo $dyInfoVal['name'];?>" src="<?php echo trim(APF::get_instance()->get_config_value("img_base_url"), "/") . $dyInfoVal['image'];?>">
                        </a>
                        <p class="name">
                            <a href="">
                                <?php echo $dyInfoVal['name'];?>
                            </a>
                        </p>
                        <p class="zhuyan">
                            <?php if (empty($zhuyan)):?>
                                暂无
                            <?php else:?>
                                <?php foreach($zhuyaoArr as $zhuyanVal):?>
                                    <a href="/search?key=<?php echo $zhuyanVal?>"><?php echo $zhuyanVal?></a>
                                <?php endforeach;?>
                            <?php endif;?>
                        </p>
                    </li>
                <?php endforeach;?>
            </ul>
        </div>
        <div class="top_info_list">
            <span class="top_title">
                豆瓣TOP10
                <em>
                    <a href="/classmovice/">更多</a>
                </em>
            </span>
            <?php $doubanValI = 1;?>
            <?php foreach($doubanDetailInfo as $doubanVal):?>
                <?php $idStr = APF::get_instance()->encodeId($doubanVal['id']);?>
                <?php if ($doubanValI == 1):?>
                    <span class="top_name_list top_first_list">
                        <b>01</b>
                        <a class="first_top" href="/detail/index/<?php echo $idStr;?>/">
                            <img alt="<?php echo $doubanVal['name'];?>" src="<?php echo trim(APF::get_instance()->get_config_value("img_base_url"), "/") . $doubanVal['image'];?>">
                        </a>
                        <i class="top_name_info">
                            <a href=""><?php echo $doubanVal['name'];?></a>
                            <em><?php echo $doubanTopMovice[$doubanVal['id']]['score'];?></em>
                        </i>
                    </span>
                <?php else:?>
                    <span class="top_name_list <?php if ($doubanValI == 10):?>last_one<?php endif;?>">
                        <b class="<?php if ($doubanValI > 3):?>last<?php endif;?>"><?php echo ($doubanValI < 10) ? "0" . $doubanValI : $doubanValI;?></b>
                        <a href="/detail/index/<?php echo $idStr;?>/"><?php echo $doubanVal['name'];?></a>
                        <i class="score"><?php echo $doubanTopMovice[$doubanVal['id']]['score'];?></i>
                    </span>
                <?php endif;?>
                <?php $doubanValI++;?>
            <?php endforeach;?>
        </div>
    </div>
    <!-- 重温经典 end   -->
</div>
<!-- 朦胧效果 整个页面覆盖 start -->
<div class="home_yaoyao_main"></div>
<!-- 朦胧效果 整个页面覆盖 end -->
<!-- 朦胧效果 电影信息展示 start -->
<div class="yaoyao_movice_info">
    <div class="yaoyao_movice_info_img">
        <a href="">
            <img src="">
        </a>
    </div>
    <div class="yaoyao_movice_info_list">

    </div>
</div>
<!-- 朦胧效果 电影信息展示 end -->