<?php $this->load->view("component/ideapan");//返回顶部与提出意见标签?>
<input type="hidden" name="current_id" id="current_id" value="">
<div class="last_movie_top">
    <div class="last_movie_top_list">
        <dl>
            <dt>
                <?php $idStr = APF::get_instance()->encodeId($totalMovieInfo[0]['id']);?>
                <a class="top_img" href="<?php echo get_url("/detail/index/{$idStr}"); ?>/">
                    <img alt="<?php echo $totalMovieInfo[0]['name'];?>" src="<?php echo trim(APF::get_instance()->get_config_value("img_base_url"),"/") . $totalMovieInfo[0]['image'];?>">
                </a>
            </dt>
            <dd><h1><a class="top_title" href="<?php echo get_url("/detail/index/{$idStr}"); ?>/"><?php echo $totalMovieInfo[0]['name'];?></a></h1></dd>
            <dd>
                <?php $zhuYanArr = explode("、",$totalMovieInfo[0]['zhuyan']);?>
                <span>主演：</span><?php echo implode("、",array_slice($zhuYanArr,0,APF::get_instance()->get_config_value("yaoyao_zhuyan_count")));?>
            </dd>
            <dd>
                <span>类型：</span><a class="top_title"><?php echo $movieType[$totalMovieInfo[0]['type']];?></a>
            </dd>
            <dd>
                <span>导演：</span><a class="top_title"><?php echo empty($totalMovieInfo[0]['daoyan']) ? "暂无" : $totalMovieInfo[0]['daoyan'];?></a>
            </dd>
            <dd>
                <span>年份：</span><a class="top_title"><?php echo empty($totalMovieInfo[0]['nianfen']) ? "暂无" : $totalMovieInfo[0]['nianfen'];?></a>
            </dd>
            <dd>
                <span>简介：</span><?php echo APF::get_instance()->splitStr($totalMovieInfo[0]['jieshao'],APF::get_instance()->get_config_value("yaoyao_jieshao_len"));?>
            </dd>
            <dd class="play_now">
                <a href="<?php echo get_url("/detail/index/{$idStr}"); ?>/"></a>
                <span class="yaoyao_again">好吧，不给力，重新摇！</span>
            </dd>
        </dl>
    </div>
    <div class="last_movie_top_right">
        <ul>
            <?php for($i = 0;$i < count($totalMovieInfo);$i++):?>
                <?php $idStr = APF::get_instance()->encodeId($totalMovieInfo[$i]['id']);?>
                <?php $zhuYanArr = explode("、",$totalMovieInfo[$i]['zhuyan']);?>
                <?php $nianfen = empty($totalMovieInfo[$i]['nianfen']) ? "暂无" : $totalMovieInfo[$i]['nianfen'];?>
                <?php $daoyan = empty($totalMovieInfo[$i]['daoyan']) ? "暂无" : $totalMovieInfo[$i]['daoyan'];?>
                <li class="<?php if ($i == 0):?>current<?php endif;?>">
                    <a title="<?php echo $totalMovieInfo[$i]['name'];?>" name="<?php echo $totalMovieInfo[$i]['name'];?>" daoyan="<?php echo $daoyan;?>" nianfen="<?php echo $nianfen;?>" zhuyan="<?php echo implode("、",array_slice($zhuYanArr,0,APF::get_instance()->get_config_value("yaoyao_zhuyan_count")));?>" type="<?php echo $movieType[$totalMovieInfo[$i]['type']];?>" jieshao="<?php echo APF::get_instance()->splitStr($totalMovieInfo[$i]['jieshao'],APF::get_instance()->get_config_value("yaoyao_jieshao_len"));?>" class="image_a" href="<?php echo get_url("/detail/index/{$idStr}"); ?>/">
                        <img alt="<?php echo $totalMovieInfo[$i]['name'];?>" src="<?php echo trim(APF::get_instance()->get_config_value("img_base_url"),"/") . $totalMovieInfo[$i]['image'];?>">
                    </a>
                </li>
            <?php endfor;?>
        </ul>
    </div>

    <div class="last_movie_top_right right2">
        <ul>
            <?php for($i = 0;$i < count($totalMovieInfo);$i++):?>
                <?php $idStr = APF::get_instance()->encodeId($totalMovieInfo[$i]['id']);?>
                <?php $zhuYanArr = explode("、",$totalMovieInfo[$i]['zhuyan']);?>
                <?php $nianfen = empty($totalMovieInfo[$i]['nianfen']) ? "暂无" : $totalMovieInfo[$i]['nianfen'];?>
                <?php $daoyan = empty($totalMovieInfo[$i]['daoyan']) ? "暂无" : $totalMovieInfo[$i]['daoyan'];?>
                <li class="<?php if ($i == 0):?>current<?php endif;?>">
                    <a title="<?php echo $totalMovieInfo[$i]['name'];?>" name="<?php echo $totalMovieInfo[$i]['name'];?>" daoyan="<?php echo $daoyan;?>" nianfen="<?php echo $nianfen;?>" zhuyan="<?php echo implode("、",array_slice($zhuYanArr,0,APF::get_instance()->get_config_value("yaoyao_zhuyan_count")));?>" type="<?php echo $movieType[$totalMovieInfo[$i]['type']];?>" jieshao="<?php echo APF::get_instance()->splitStr($totalMovieInfo[$i]['jieshao'],APF::get_instance()->get_config_value("yaoyao_jieshao_len"));?>" class="image_a" href="<?php echo get_url("/detail/index/{$idStr}"); ?>/">
                        <img alt="<?php echo $totalMovieInfo[$i]['name'];?>" src="<?php echo trim(APF::get_instance()->get_config_value("img_base_url"),"/") . $totalMovieInfo[$i]['image'];?>">
                    </a>
                </li>
            <?php endfor;?>
        </ul>
    </div>
</div>
<div class="last_movie_main">
    <!-- 月份展示 start -->
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
    <!-- 月份展示 end -->

    <!-- 月份电影展示 start -->
    <div class="month_dy_main">
        <?php $sortDyInfo1 = $sortDyInfo2 = $sortDyInfo3 = $playS = $searchS = $downS = array();?>
        <?php $dyI = 0;?>
        <?php foreach ($movieList as $movieKey => $movieVal): ?>
            <?php if (empty($movieVal)){continue;}?>
            <div class="month_dy_list">
                <ul>
                    <li class="title"><h1><?php echo $movieKey;?></h1></li>
                    <?php $valI = 1;?>
                    <?php foreach ($movieVal as $mKey => $mVal): ?>
                        <?php $sortDyInfo1[$dyI] = $sortDyInfo2[$dyI] = $sortDyInfo3[$dyI] = $mVal;?>
                        <?php $playS[$dyI] = $mVal['playNum'];?>
                        <?php $searchS[$dyI] = $mVal['searchNum'];?>
                        <?php $downS[$dyI++] = $mVal['downNum'];?>
                        <?php $zhuyan = str_replace("、","/",$mVal['zhuyan']);?>
                        <?php $zhuyaoArr = explode("/",$zhuyan);?>
                        <?php $idStr = APF::get_instance()->encodeId($mVal['id']);?>
                        <li class="info<?php if ($valI <= 6):?> first<?php endif;?><?php if ($valI % 6 == 0):?> last<?php endif;?>">
                            <a class="image_a" href="<?php echo get_url("/detail/index/{$idStr}"); ?>/">
                                <img alt="<?php echo $mVal['name'];?>" src="<?php echo trim(APF::get_instance()->get_config_value("img_base_url"),"/") . $mVal['image'];?>">
                            </a>
                            <p class="name"><a href="<?php echo get_url("/detail/index/{$idStr}"); ?>/"><?php echo $mVal['name'];?></a></p>
                            <p class="jieshao">
                                <?php if (empty($zhuyan)):?>
                                    暂无
                                <?php else:?>
                                    <?php foreach($zhuyaoArr as $zhuyanVal):?>
                                        <a href="/search?key=<?php echo $zhuyanVal?>"><?php echo $zhuyanVal?></a>
                                    <?php endforeach;?>
                                <?php endif;?>
                            </p>
                        </li>
                        <?php $valI++;?>
                    <?php endforeach;?>
                </ul>
            </div>
        <?php endforeach;?>
    </div>
    <!-- 月份电影展示 end -->

    <!--右侧排行榜展示start -->
        <div class="last_left_main">
            <!-- 播放排行榜 start     -->
            <div class="top_info_list">
                <div class="top_title">
                    <h1>播放风云榜</h1>
                </div>
                <?php array_multisort($playS,SORT_DESC,$sortDyInfo1);?>
                <?php $playDyInfo = array_slice($sortDyInfo1,0,15);?>
                <div class="top_get">
                    <?php $playValI = 1;?>
                    <?php $playValCount = count($playDyInfo);?>
                    <?php foreach($playDyInfo as $playVal):?>
                        <?php $idStr = APF::get_instance()->encodeId($playVal['id']);?>
                        <?php if ($playValI == 1):?>
                            <span class="top_name_list top_first_list">
                        <b>01</b>
                        <a class="first_top" href="/detail/index/<?php echo $idStr;?>/">
                            <img alt="<?php echo $playVal['name'];?>" src="<?php echo trim(APF::get_instance()->get_config_value("img_base_url"), "/") . $playVal['image'];?>">
                        </a>
                        <i class="top_name_info">
                            <a class="name" href="/detail/index/<?php echo $idStr;?>/"><?php echo $playVal['name'];?></a>
                        </i>
                        <span class="fisrt_s">
                            <em>被播放</em><?php echo $playVal['playNum'];?>次
                        </span>
                        <span class="top_jieshao">简介：<?php echo APF::get_instance()->splitStr($playVal['jieshao'],60);?></span>
                    </span>
                        <?php else:?>
                            <span class="top_name_list <?php if ($playValI == $playValCount):?>last_one<?php endif;?>">
                        <b class="<?php if ($playValI > 3):?>last<?php endif;?>"><?php echo ($playValI < 10) ? "0" . $playValI : $playValI;?></b>
                        <a href="/detail/index/<?php echo $idStr;?>/"><?php echo $playVal['name'];?></a>
                        <i class="score"><em>被播放</em><?php echo $playVal['playNum'];?>次</i>
                    </span>
                        <?php endif;?>
                        <?php $playValI++;?>
                    <?php endforeach;?>
                </div>
            </div>
            <!--播放排行榜 end -->

            <!-- 搜索排行榜 start     -->
            <div class="top_info_list">
                <div class="top_title">
                    <h1>搜索风云榜</h1>
                </div>
                <?php array_multisort($searchS,SORT_DESC,$sortDyInfo2);?>
                <?php $searchDyInfo = array_slice($sortDyInfo2,0,15);?>
                <div class="top_get">
                    <?php $searchValI = 1;?>
                    <?php $searchValCount = count($searchDyInfo);?>
                    <?php foreach($searchDyInfo as $searchVal):?>
                        <?php $idStr = APF::get_instance()->encodeId($searchVal['id']);?>
                        <?php if ($searchValI == 1):?>
                            <span class="top_name_list top_first_list">
                        <b>01</b>
                        <a class="first_top" href="/detail/index/<?php echo $idStr;?>/">
                            <img alt="<?php echo $searchVal['name'];?>" src="<?php echo trim(APF::get_instance()->get_config_value("img_base_url"), "/") . $searchVal['image'];?>">
                        </a>
                        <i class="top_name_info">
                            <a class="name" href="/detail/index/<?php echo $idStr;?>/"><?php echo $searchVal['name'];?></a>
                        </i>
                        <span class="fisrt_s">
                            <em>被搜</em><?php echo $searchVal['searchNum'];?>次
                        </span>
                        <span class="top_jieshao">简介：<?php echo APF::get_instance()->splitStr($searchVal['jieshao'],60);?></span>
                    </span>
                        <?php else:?>
                            <span class="top_name_list <?php if ($searchValI == $searchValCount):?>last_one<?php endif;?>">
                        <b class="<?php if ($searchValI > 3):?>last<?php endif;?>"><?php echo ($searchValI < 10) ? "0" . $searchValI : $searchValI;?></b>
                        <a href="/detail/index/<?php echo $idStr;?>/"><?php echo $searchVal['name'];?></a>
                        <i class="score"><em>被搜</em><?php echo $searchVal['searchNum'];?>次</i>
                    </span>
                        <?php endif;?>
                        <?php $searchValI++;?>
                    <?php endforeach;?>
                </div>
            </div>
            <!--搜索排行榜 end -->

            <!-- 下载排行榜 start     -->
            <div class="top_info_list">
                <div class="top_title">
                    <h1>下载风云榜</h1>
                </div>
                <?php array_multisort($downS,SORT_DESC,$sortDyInfo3);?>
                <?php $downDyInfo = array_slice($sortDyInfo3,0,15);?>
                <div class="top_get">
                    <?php $downValI = 1;?>
                    <?php $downValCount = count($downDyInfo);?>
                    <?php foreach($downDyInfo as $downVal):?>
                        <?php $idStr = APF::get_instance()->encodeId($downVal['id']);?>
                        <?php if ($downValI == 1):?>
                            <span class="top_name_list top_first_list">
                        <b>01</b>
                        <a class="first_top" href="/detail/index/<?php echo $idStr;?>/">
                            <img alt="<?php echo $downVal['name'];?>" src="<?php echo trim(APF::get_instance()->get_config_value("img_base_url"), "/") . $downVal['image'];?>">
                        </a>
                        <i class="top_name_info">
                            <a class="name" href="/detail/index/<?php echo $idStr;?>/"><?php echo $downVal['name'];?></a>
                        </i>
                        <span class="fisrt_s">
                            <em>被下载</em><?php echo $downVal['downNum'];?>次
                        </span>
                        <span class="top_jieshao">简介：<?php echo APF::get_instance()->splitStr($downVal['jieshao'],60);?></span>
                    </span>
                        <?php else:?>
                            <span class="top_name_list <?php if ($downValI == $downValCount):?>last_one<?php endif;?>">
                        <b class="<?php if ($downValI > 3):?>last<?php endif;?>"><?php echo ($downValI < 10) ? "0" . $downValI : $downValI;?></b>
                        <a href="/detail/index/<?php echo $idStr;?>/"><?php echo $downVal['name'];?></a>
                        <i class="score"><em>被下载</em><?php echo $downVal['downNum'];?>次</i>
                    </span>
                        <?php endif;?>
                        <?php $downValI++;?>
                    <?php endforeach;?>
                </div>
            </div>
            <!--下载排行榜 end -->
        </div>
    <!--右侧排行榜展示end -->
    <div class="clear"></div>
</div>