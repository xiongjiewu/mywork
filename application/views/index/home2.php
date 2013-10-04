<div class="home_top">
    <ul>
      <?php $topMovieInfosI = 0;?>
       <?php foreach($topMovieInfos as $topVal):?>
            <?php $idStr = APF::get_instance()->encodeId($topVal['id']);?>
            <?php $zhuyan = str_replace("、","/",$topVal['zhuyan'])?>
            <?php $daoyan = str_replace("、","/",$topVal['daoyan'])?>
            <?php $nianfen = $topVal['nianfen'];?>
            <?php if ($topMovieInfosI == 13):?>
                <li class="top_last_li yaoyao_action" title="摇一摇有惊喜">
                    <a class="first_img" href="javascript:void(0);">
                        <img alt="摇一摇有惊喜" src="/images/home/yaoyao.png">
                    </a>
                </li>
            <?php elseif ($topMovieInfosI == 27):?>
                <li class="top_last_li" title="意见反馈">
                    <a class="first_img" href="/usercenter/feedback/" target="_blank">
                        <img alt="意见反馈" src="/images/home/fankui3.png">
                    </a>
                </li>
            <?php elseif ($topMovieInfosI == 41):?>
            <li class="top_last_li head_action" title="求片留言">
                <a class="first_img" href="/usercenter/feedback/">
                    <img alt="求片留言" src="/images/home/qiupian.png">
                </a>
            </li>
            <?php else:?>
                <li <?php if ($topMovieInfosI == 0):?>class="top_first_li"<?php endif;?>>
                    <a title="<?php echo $topVal['name'];?>" class="first_img" href="/detail/index/<?php echo $idStr;?>/">
                        <img alt="<?php echo $topVal['name'];?>" src="<?php echo APF::get_instance()->get_image_url($topVal['image'],"dy",100);?>">
                    </a>
                </li>
            <?php endif;?>
            <?php $topMovieInfosI++;?>
        <?php endforeach;?>
    </ul>
</div>
<!-- 电影总数展示 start -->
<div class="movie_count_main">
    <div class="count_real">已收录<em><?php echo $dyCount;?></em>部电影</div>
</div>
<!-- 电影总数展示 end -->

<div class="home_middel_main">
    <!-- 中间索引条 start -->
    <div class="type_main">
        <div class="tab_type_list">
            <!-- 类型 start-->
            <div class="list_type type_info">
                <span class="type_type">按类型</span>
                <ul>
                    <?php $moviceTypeI = 1;?>
                    <?php foreach($moviceType as $typeKey => $typeVal):?>
                        <?php if ($moviceTypeI > 20){break;}?>
                        <li class="list_type_li">
                            <a title="<?php echo $typeVal;?>" href="<?php echo APF::get_instance()->get_real_url('/moviceguide','',array("type" => $typeKey));?>"><?php echo $typeVal;?></a>
                        </li>
                        <?php $moviceTypeI++;?>
                    <?php endforeach;?>
                    <li class="list_type_li">
                        <a href="<?php echo APF::get_instance()->get_real_url("moviceguide");?>">更多</a>
                    </li>
                </ul>
            </div>
            <!-- 类型 end-->
            <div class="middel_line"></div>
            <!-- 年份 start-->
            <div class="list_type list_place">
                <span class="type_type">按年份</span>
                <ul>
                    <?php $moviceNianfenInfo = array_slice($movieNianFen,0,8);?>
                    <?php foreach($moviceNianfenInfo as $nianfenKey => $nianfenVal):?>
                        <li class="list_type_li">
                            <a title="<?php echo $nianfenVal;?>" href="<?php echo APF::get_instance()->get_real_url('/moviceguide','',array("year" => $nianfenVal));?>"><?php echo $nianfenVal;?></a>
                        </li>
                    <?php endforeach;?>
                    <li class="list_type_li">
                        <a href="<?php echo APF::get_instance()->get_real_url("moviceguide");?>">更多</a>
                    </li>
                </ul>
            </div>
            <!-- 年份 end-->
            <div class="middel_line"></div>
            <!-- 地区 start-->
            <div class="list_type list_place">
                <span class="type_type">按地区</span>
                <ul>
                <?php $moviePlaceI = 1;?>
                <?php foreach($moviePlace as $placeKey => $placeVal):?>
                    <?php if ($moviePlaceI > 8){break;}?>
                    <li class="list_type_li">
                        <a title="<?php echo $placeVal;?>" href="<?php echo APF::get_instance()->get_real_url('/moviceguide','',array("place" => $placeKey));?>"><?php echo $placeVal;?></a>
                    </li>
                    <?php $moviePlaceI++;?>
                <?php endforeach;?>
                <li class="list_type_li">
                    <a href="<?php echo APF::get_instance()->get_real_url("moviceguide");?>">更多</a>
                </li>
                </ul>
            </div>
            <!-- 地区 end-->
            <div class="middel_line"></div>
            <!-- 演员 start-->
            <div class="list_type list_place">
                <span class="type_type">按演员</span>
                <ul>
                    <?php foreach($yanYuan as $yanYuanKey => $yanYuanVal):?>
                        <li class="list_type_li">
                            <a title="<?php echo $yanYuanVal;?>" href="<?php echo APF::get_instance()->get_real_url('/moviceguide','',array("y" => $yanYuanVal));?>"><?php echo $yanYuanVal;?></a>
                        </li>
                    <?php endforeach;?>
                    <li class="list_type_li">
                        <a href="<?php echo APF::get_instance()->get_real_url("moviceguide");?>">更多</a>
                    </li>
                </ul>
            </div>
            <!-- 演员 end-->
            <div class="middel_line"></div>
            <!-- 演员 start-->
            <div class="list_type list_place">
                <span class="type_type">按导演</span>
                <ul>
                    <?php foreach($daoYan as $daoYanKey => $daoYanVal):?>
                        <li class="list_type_li">
                            <a title="<?php echo $daoYanVal;?>" href="<?php echo APF::get_instance()->get_real_url('/moviceguide','',array("d" => $daoYanVal));?>"><?php echo $daoYanVal;?></a>
                        </li>
                    <?php endforeach;?>
                    <li class="list_type_li">
                        <a href="<?php echo APF::get_instance()->get_real_url("moviceguide");?>">更多</a>
                    </li>
                </ul>
            </div>
            <!-- 演员 end-->
        </div>
    </div>
    <!-- 中间索引条 start -->
    <!-- 最新上映 start   -->
    <div class="movice_info_list movice_info_list_list">
        <div class="title">
            <h2>
                <a class="" href="/latestmovie/" title="点击查看更多">
                    最新上映
                </a>
            </h2>
        </div>
        <div class="info_list">
            <ul>
                <?php $newestDyInfoI = 0;?>
                <?php foreach($newestDyInfo as $dyInfoVal):?>
                    <?php $idStr = APF::get_instance()->encodeId($dyInfoVal['id']);?>
                    <?php $image = APF::get_instance()->get_image_url($dyInfoVal['image'],"dy",200);?>
                    <?php $image2 = APF::get_instance()->get_image_url($dyInfoVal['image'],"dy",300);?>
                    <?php $name = $dyInfoVal['name'];?>
                    <?php $jieshao = str_replace("　　","",trim($dyInfoVal['jieshao']));?>
                    <?php $type = $dyInfoVal['type'];?>
                    <?php $typeText = $moviceType[$dyInfoVal['type']];?>
                    <?php $zhuyan = str_replace("、","/",$dyInfoVal['zhuyan']);?>
                    <?php $zhuyaoArr = explode("/",$zhuyan);?>
                    <?php if ($newestDyInfoI == 0):?>
                        <li class="first_one_li" idStr="<?php echo $idStr;?>" zhuyan="<?php echo $zhuyan;?>" type="<?php echo $type;?>" typeText="<?php echo $typeText;?>" name="<?php echo $name;?>" img="<?php echo $image2;?>" jieshao="<?php echo $jieshao;?>">
                            <a class="first_img" href="/detail/index/<?php echo $idStr;?>?from=home_last_movie">
                                <img alt="<?php echo $name;?>" src="<?php echo $image2;?>">
                            </a>
                            <p class="first_name">
                                <a class="name" href="/detail/index/<?php echo $idStr;?>?from=home_last_movie">
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
                        <li class="other_li" type="<?php echo $type;?>" idStr="<?php echo $idStr;?>" zhuyan="<?php echo $zhuyan;?>" typeText="<?php echo $typeText;?>" name="<?php echo $name;?>" img="<?php echo $image2;?>" jieshao="<?php echo $jieshao;?>">
                            <a class="img" href="/detail/index/<?php echo $idStr;?>?from=home_last_movie">
                                <img alt="<?php echo $name;?>" src="<?php echo $image;?>">
                            </a>
                            <p class="name">
                                <a href="/detail/index/<?php echo $idStr;?>?from=home_last_movie">
                                    <?php echo $name;?>
                                </a>
                            </p>
                            <p class="zhuyan">
                                <?php if (empty($zhuyan)):?>
                                    暂无
                                <?php else:?>
                                    <?php foreach($zhuyaoArr as $zhuyanVal):?>
                                        <a href="<?php echo APF::get_instance()->get_real_url("/jump","",array("type" => 1,"key" => $zhuyanVal));?>"><?php echo $zhuyanVal;?></a>
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

    <!-- 系列专题 这一版先隐藏 start   -->
    <div class="movice_info_list movice_info_list_list">
        <div class="title">
            <h2>
                <a class="" href="/latestmovie/" title="点击查看更多">
                    专题系列
                </a>
            </h2>
        </div>
        <div class="movie_serial">
            <div class="title_list">
                <div class="s_title"><h1>大片连连看</h1></div>
                <div class="s_movie_list">
                    <ul>
                        <?php foreach($topicInfo as $topic):?>
                            <li><a href="/series/info/<?php echo APF::get_instance()->encodeId($topic['id']);?>"><?php echo $topic['name'];?></a></li>
                        <?php endforeach;?>
                        <li><a href="/series/">更多</a></li>
                    </ul>
                </div>
            </div>
            <div class="middel_line"></div>

            <div class="title_list">
                <div class="s_title"><h1>人物系列</h1></div>
                <div class="s_movie_list">
                    <ul>
                        <?php foreach($peopleInfo as $peopel):?>
                            <li><a href="/people/index/<?php echo APF::get_instance()->encodeId($peopel['id']);?>"><?php echo $peopel['name'];?></a></li>
                        <?php endforeach;?>
                        <li><a href="/retrieval/?b=p&t=p&s=A">更多</a></li>
                    </ul>
                </div>
            </div>
            <div class="middel_line"></div>

            <div class="title_list jiangxiang">
                <div class="s_title"><h1>人气电影</h1></div>
                <div class="s_movie_list">
                    <ul>
                        <?php foreach($hotMovieInfo as $hotMovie):?>
                            <li><a href="/detail/index/<?php echo APF::get_instance()->encodeId($hotMovie['id']);?>"><?php echo $hotMovie['name'];?></a></li>
                        <?php endforeach;?>
                        <li><a href="/moviceguide/">更多</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- 系列专题 end   -->

    <!-- 重温经典 start   -->
    <div class="movice_info_list movice_class_info_list">
        <div class="title">
            <h2>
                <a class="" href="/classmovie/" title="点击查看更多">
                    经典电影
                </a>
            </h2>
        </div>
        <div class="info_list">
            <ul>
                <?php foreach($classDyInfo as $dyInfoVal):?>
                    <?php $zhuyan = str_replace("、","/",$dyInfoVal['zhuyan']);?>
                    <?php $zhuyaoArr = explode("/",$zhuyan);?>
                    <?php $idStr = APF::get_instance()->encodeId($dyInfoVal['id']);?>
                    <li class="other_li">
                        <a class="img" href="/detail/index/<?php echo $idStr;?>?from=home_class_movie">
                            <img alt="<?php echo $dyInfoVal['name'];?>" src="<?php echo APF::get_instance()->get_image_url($dyInfoVal['image'],"dy",200);?>">
                        </a>
                        <p class="name">
                            <a href="/detail/index/<?php echo $idStr;?>?from=home_class_movie">
                                <?php echo $dyInfoVal['name'];?>
                            </a>
                        </p>
                        <p class="zhuyan">
                            <?php if (empty($zhuyan)):?>
                                暂无
                            <?php else:?>
                                <?php foreach($zhuyaoArr as $zhuyanVal):?>
                                    <a href="<?php echo APF::get_instance()->get_real_url("/jump","",array("type" => 1,"key" => $zhuyanVal));?>"><?php echo $zhuyanVal?></a>
                                <?php endforeach;?>
                            <?php endif;?>
                        </p>
                    </li>
                <?php endforeach;?>
            </ul>
        </div>
        <div class="top_info_list">
            <div class="top_title">
                <h1>经典风云榜</h1>
                <div class="top_tab">
                    <em>
                        <a title="点击查看更多" class="current" type="baidu" href="/classmovice/index/top/4/">百度</a>
                    </em>
                    <em>
                        <a title="点击查看更多" type="douban" href="/classmovice/index/top/1/">豆瓣</a>
                    </em>
                    <em>
                        <a title="点击查看更多" type="imdb" href="/classmovice/index/top/2/">IMDB</a>
                    </em>
                    <em>
                        <a title="点击查看更多" type="mtime" href="/classmovice/index/top/3/">时光网</a>
                    </em>
                </div>
            </div>
            <!--     百度top start     -->
            <div class="top_get baidu">
                <?php $baiduValI = 1;?>
                <?php $baiduValCount = count($baiduDetailInfo);?>
                <?php foreach($baiduDetailInfo as $baiduVal):?>
                    <?php $idStr = APF::get_instance()->encodeId($baiduVal['id']);?>
                    <?php if ($baiduValI == 1):?>
                        <span class="top_name_list top_first_list">
                        <b>01</b>
                        <a class="first_top" href="/detail/index/<?php echo $idStr;?>?from=home_top_baidu">
                            <img alt="<?php echo $baiduVal['name'];?>" src="<?php echo APF::get_instance()->get_image_url($baiduVal['image'],"dy",100);?>">
                        </a>
                        <i class="top_name_info">
                            <a class="name" href="/detail/index/<?php echo $idStr;?>?from=home_top_baidu"><?php echo $baiduVal['name'];?></a>
                        </i>
                        <span class="fisrt_s">
                            <em>被搜</em><?php echo $baiduVal['search'];?>次
                        </span>
                        <span class="top_jieshao">简介：<?php echo APF::get_instance()->splitStr($baiduVal['jieshao'],60);?></span>
                    </span>
                    <?php else:?>
                        <span class="top_name_list <?php if ($baiduValI == $baiduValCount):?>last_one<?php endif;?>">
                        <b class="<?php if ($baiduValI > 3):?>last<?php endif;?>"><?php echo ($baiduValI < 10) ? "0" . $baiduValI : $baiduValI;?></b>
                        <a href="/detail/index/<?php echo $idStr;?>?from=home_top_baidu"><?php echo $baiduVal['name'];?></a>
                        <i class="score"><em>被搜</em><?php echo $baiduVal['search'];?>次</i>
                    </span>
                    <?php endif;?>
                    <?php $baiduValI++;?>
                <?php endforeach;?>
            </div>
            <!--     百度top end     -->

            <!--     豆瓣top start     -->
            <div class="top_get douban" style="display: none;">
                <?php $doubanValI = 1;?>
                <?php $doubanValCount = count($doubanDetailInfo);?>
                <?php foreach($doubanDetailInfo as $doubanVal):?>
                    <?php $idStr = APF::get_instance()->encodeId($doubanVal['id']);?>
                    <?php if ($doubanValI == 1):?>
                        <span class="top_name_list top_first_list">
                        <b>01</b>
                        <a class="first_top" href="/detail/index/<?php echo $idStr;?>?from=home_top_douban">
                            <img alt="<?php echo $doubanVal['name'];?>" src="<?php echo APF::get_instance()->get_image_url($doubanVal['image'],"dy",100);?>">
                        </a>
                        <i class="top_name_info">
                            <a class="name" href="/detail/index/<?php echo $idStr;?>?from=home_top_douban"><?php echo $doubanVal['name'];?></a>
                        </i>
                        <span class="fisrt_s">
                            <?php echo $doubanVal['score'];?>分
                        </span>
                        <span class="top_jieshao">简介：<?php echo APF::get_instance()->splitStr($doubanVal['jieshao'],60);?></span>
                    </span>
                    <?php else:?>
                        <span class="top_name_list <?php if ($doubanValI == $doubanValCount):?>last_one<?php endif;?>">
                        <b class="<?php if ($doubanValI > 3):?>last<?php endif;?>"><?php echo ($doubanValI < 10) ? "0" . $doubanValI : $doubanValI;?></b>
                        <a href="/detail/index/<?php echo $idStr;?>?from=home_top_douban"><?php echo $doubanVal['name'];?></a>
                        <i class="score"><?php echo $doubanVal['score'];?>分</i>
                    </span>
                    <?php endif;?>
                    <?php $doubanValI++;?>
                <?php endforeach;?>
            </div>
            <!--     豆瓣top end     -->

            <!--     imdb top start     -->
            <div class="top_get imdb" style="display: none;">
                <?php $imdbValI = 1;?>
                <?php $imdbValCount = count($imdbDetailInfo);?>
                <?php foreach($imdbDetailInfo as $imdbVal):?>
                    <?php $idStr = APF::get_instance()->encodeId($imdbVal['id']);?>
                    <?php if ($imdbValI == 1):?>
                        <span class="top_name_list top_first_list">
                        <b>01</b>
                        <a class="first_top" href="/detail/index/<?php echo $idStr;?>?from=home_top_imdb">
                            <img alt="<?php echo $imdbVal['name'];?>" src="<?php echo APF::get_instance()->get_image_url($imdbVal['image'],"dy",100);?>">
                        </a>
                        <i class="top_name_info">
                            <a class="name" href="/detail/index/<?php echo $idStr;?>?from=home_top_imdb"><?php echo $imdbVal['name'];?></a>
                        </i>
                        <span class="fisrt_s">
                            <?php echo $imdbVal['score'];?>分
                        </span>
                        <span class="top_jieshao">简介：<?php echo APF::get_instance()->splitStr($imdbVal['jieshao'],60);?></span>
                    </span>
                    <?php else:?>
                        <span class="top_name_list <?php if ($imdbValI == $imdbValCount):?>last_one<?php endif;?>">
                        <b class="<?php if ($imdbValI > 3):?>last<?php endif;?>"><?php echo ($imdbValI < 10) ? "0" . $imdbValI : $imdbValI;?></b>
                        <a href="/detail/index/<?php echo $idStr;?>?from=home_top_imdb"><?php echo $imdbVal['name'];?></a>
                        <i class="score"><?php echo $imdbVal['score'];?>分</i>
                    </span>
                    <?php endif;?>
                    <?php $imdbValI++;?>
                <?php endforeach;?>
            </div>
            <!--     imdb top end     -->

            <!--     时光网 top start     -->
            <div class="top_get mtime" style="display: none;">
                <?php $mtimeValI = 1;?>
                <?php $mtimeValCount = count($mtimeDetailInfo);?>
                <?php foreach($mtimeDetailInfo as $mtimeVal):?>
                    <?php $idStr = APF::get_instance()->encodeId($mtimeVal['id']);?>
                    <?php if ($mtimeValI == 1):?>
                        <span class="top_name_list top_first_list">
                        <b>01</b>
                        <a class="first_top" href="/detail/index/<?php echo $idStr;?>?from=home_top_itime">
                            <img alt="<?php echo $mtimeVal['name'];?>" src="<?php echo APF::get_instance()->get_image_url($mtimeVal['image'],"dy",100);?>">
                        </a>
                        <i class="top_name_info">
                            <a class="name" href="/detail/index/<?php echo $idStr;?>?from=home_top_itime"><?php echo $mtimeVal['name'];?></a>
                        </i>
                        <span class="fisrt_s">
                            <?php echo $mtimeVal['score'];?>分
                        </span>
                        <span class="top_jieshao">简介：<?php echo APF::get_instance()->splitStr($mtimeVal['jieshao'],60);?></span>
                    </span>
                    <?php else:?>
                        <span class="top_name_list <?php if ($mtimeValI == $mtimeValCount):?>last_one<?php endif;?>">
                        <b class="<?php if ($mtimeValI > 3):?>last<?php endif;?>"><?php echo ($mtimeValI < 10) ? "0" . $mtimeValI : $mtimeValI;?></b>
                        <a href="/detail/index/<?php echo $idStr;?>?from=home_top_itime"><?php echo $mtimeVal['name'];?></a>
                        <i class="score"><?php echo $mtimeVal['score'];?>分</i>
                    </span>
                    <?php endif;?>
                    <?php $mtimeValI++;?>
                <?php endforeach;?>
            </div>
            <!--     时光网 top end     -->
        </div>
    </div>
    <!-- 重温经典 end   -->

    <!-- 今日推荐  start -->
    <?php if (!empty($todayMovieList)):?>
        <div class="movice_info_list">
            <div class="title">
                <h2>
                    <a class="" href="/moviceguide/?sort=new" title="点击查看更多">
                        今日更新
                    </a>
                </h2>
            </div>
            <div class="info_list">
                <ul>
                    <?php foreach($todayMovieList as $todayVal):?>
                        <?php $name = $todayVal['name'];?>
                        <?php $idStr = APF::get_instance()->encodeId($todayVal['id']);?>
                        <?php $image = APF::get_instance()->get_image_url($todayVal['image'],"dy",200);?>
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
                                        <a class="t_zhuyan" href="<?php echo APF::get_instance()->get_real_url("/jump","",array("type" => 1,"key" => $zhuyanVal));?>"><?php echo $zhuyanVal?></a>
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
                <a class="" href="/latestmovie/" title="点击查看更多">
                    即将上映
                </a>
            </h2>
        </div>
        <div class="info_list">
            <ul>
                <?php $willDyInfoI = 0;?>
                <?php foreach($willDyInfo as $dyInfoVal):?>
                    <?php $idStr = APF::get_instance()->encodeId($dyInfoVal['id']);?>
                    <?php $image = APF::get_instance()->get_image_url($dyInfoVal['image'],"dy",200);?>
                    <?php $image2 = APF::get_instance()->get_image_url($dyInfoVal['image'],"dy",300);?>
                    <?php $name = $dyInfoVal['name'];?>
                    <?php $jieshao = str_replace("　　","",trim($dyInfoVal['jieshao']));?>
                    <?php $type = $dyInfoVal['type'];?>
                    <?php $typeText = $moviceType[$dyInfoVal['type']];?>
                    <?php $zhuyan = str_replace("、","/",$dyInfoVal['zhuyan']);?>
                    <?php $zhuyaoArr = explode("/",$zhuyan);?>
                    <?php if ($willDyInfoI == 0):?>
                        <li class="first_one_li" idStr="<?php echo $idStr;?>" zhuyan="<?php echo $zhuyan;?>" type="<?php echo $type;?>" typeText="<?php echo $typeText;?>" name="<?php echo $name;?>" img="<?php echo $image2;?>" jieshao="<?php echo $jieshao;?>">
                            <a class="first_img" href="/detail/index/<?php echo $idStr;?>/">
                                <img alt="<?php echo $name;?>" src="<?php echo $image2;?>">
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
                        <li class="other_li" type="<?php echo $type;?>" idStr="<?php echo $idStr;?>" zhuyan="<?php echo $zhuyan;?>" typeText="<?php echo $typeText;?>" name="<?php echo $name;?>" img="<?php echo $image2;?>" jieshao="<?php echo $jieshao;?>">
                            <a class="img" href="/detail/index/<?php echo $idStr;?>?from=home_comming_movie">
                                <img alt="<?php echo $name;?>" src="<?php echo $image;?>">
                            </a>
                            <p class="name">
                                <a href="/detail/index/<?php echo $idStr;?>?from=home_comming_movie">
                                    <?php echo $name;?>
                                </a>
                            </p>
                            <p class="zhuyan">
                                <?php if (empty($zhuyan)):?>
                                    暂无
                                <?php else:?>
                                    <?php foreach($zhuyaoArr as $zhuyanVal):?>
                                        <a href="<?php echo APF::get_instance()->get_real_url("/jump","",array("type" => 1,"key" => $zhuyanVal));?>"><?php echo $zhuyanVal?></a>
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
    <div class="clear"></div>
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
