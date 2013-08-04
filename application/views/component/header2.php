<div class="new_head">
    <div class="new_head_mian">
        <ul>
            <li class="new_logo">
               <a href="/" title="返回首页"></a>
            </li>
            <?php $menus = APF::get_instance()->get_config_value("menus");?>
            <?php $index = !isset($tabIndex) ? -1 : $tabIndex;?>
            <?php foreach ($menus as $menuKey => $menuVal): ?>
                <li class="<?php echo $menuVal['class'];?>">
                    <a href="<?php echo $menuVal['link']; ?>" class="<?php if($index == $menuKey):?>current<?php endif;?>"><?php echo $menuVal['title'];?></a>
                    <?php if (!empty($menuVal['new'])):?>
                        <i class="new"></i>
                    <?php endif;?>
                    <?php if (!empty($menuVal['list'])):?>
                        <i class="list"></i>
                        <div class="menu_list">
                            <?php foreach($menuVal['list'] as $listKey => $listVal):?>
                                <span class="<?php if (isset($sTabIndex) && ($listKey == $sTabIndex) && ($index == $menuKey)):?>current_list<?php endif;?>">
                                    <a href="<?php echo $listVal['url'];?>">
                                        <?php echo $listVal['title'];?>
                                    </a>
                                </span>
                            <?php endforeach;?>
                        </div>
                    <?php endif;?>
                </li>
            <?php endforeach;?>
            <li>
                <span class="fenge_line"></span>
            </li>
            <?php $rightMenus = APF::get_instance()->get_config_value("right_menus");?>
            <?php $index = $this->load->get_top_index();?>
            <?php foreach($rightMenus as $rKey => $rMVal):?>
                <li>
                    <a class="<?php if($index == $rKey):?>current<?php endif;?>" href="<?php echo $rMVal['link']; ?>"><?php echo $rMVal['title'];?></a>
                </li>
            <?php endforeach;?>
            <li class="search_k">
                <div class="search_l"></div>
                <form name="search_dy" id="search_dy" onsubmit="return false;" autocomplete="off" method="get">
                    <input type="text" class="search_n" name="search" id="search" value="<?php if (empty($searchW)):?>搜影片、人物或下载资源<?php else:?><?php echo $searchW;?><?php endif;?>">
                    <div class="submit_do">
                        <input type="submit" class="su_button" value="">
                    </div>
                </form>
                <div class="search_r"></div>
                <div class="about_search">
                </div>
            </li>
            <?php if (empty($userName) && empty($userId)):?>
                <li class="login">
                    <a href="/login/" class="login_total_page"><i></i>登录</a>
                </li>
                <li class="register">
                    <a href="/register/"><i></i>注册</a>
                </li>
            <?php else:?>
                <li class="username">
                    <a href="<?php echo APF::get_instance()->get_real_url("/usercenter/");?>" class="">
                        <i></i>
                        <?php echo $userName;?>
                    </a>
                    <a class="logout" href="/logout/">[退出]</a>
                    <div class="user_account">
                    </div>
                </li>
            <?php endif;?>
            <li class="look">
                <a href="javascript:void(0);">浏览记录</a>
                <i></i>
                <div class="look_info">
                    <ul>
                        <?php if (!empty($userLookInfo)):?>
                            <?php $lookI = 1;?>
                            <?php $lookCount = count($userLookInfo);?>
                            <?php foreach($userLookInfo as $lookVal):?>
                                <li>
                                    <?php $idStr = APF::get_instance()->encodeId($lookVal['id']);?>
                                    <a href="/detail/index/<?php echo $idStr;?>"> <?php echo $lookVal['name'];?></a>
                                </li>
                                <?php $lookI++;?>
                            <?php endforeach;?>
                        <?php else:?>
                            <li>
                                <span class="last_span nothing">
                                暂无浏览记录！
                                </span>
                            </li>
                        <?php endif;?>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</div>