<div class="header_main">
    <div class="header_search">
        <div class="form_info">
            <form name="search_dy" id="search_dy" onsubmit="return false;" action="/search" method="get" autocomplete="off">
                <input type="text" name="search" id="search" class="search" value="<?php if (empty($searchW)):?>搜您喜欢的影片、导演、演员、下载资源...<?php else:?><?php echo $searchW;?><?php endif;?>">
                <input type="submit" value="搜 索" class="submit" id="search_do" name="search_do">
            </form>
            <div class="about_search">
            </div>
        </div>
        <?php if (!empty($searchCacheInfo)):?>
            <div class="hot_search">
                <ul>
                    <?php foreach($searchCacheInfo as $searchVal):?>
                        <?php $searchVal = trim($searchVal);?>
                    <li>
                        <a href="/search?key=<?php echo $searchVal;?>&from=hot_search_word"><?php echo $searchVal;?></a>
                    </li>
                    <?php endforeach;?>
                </ul>
            </div>
        <?php endif;?>
    </div>
    <!-- 登录，注册、浏览记录 start   -->
    <div class="login_register_pan">
        <?php if (empty($userName) && empty($userId)):?>
            <div class="weibo_login">
                <a href="/weblogin/weibo/" title="微博帐号登录"></a>
            </div>
            <div class="QQ_login">
                <a href="/weblogin/qq/" title="QQ帐号登录"></a>
            </div>
            <div class="renren_login">
                <a href="/weblogin/renren/" title="人人帐号登录"></a>
            </div>
            <div class="login_pan">
                <a class="login_total_page" href="/login/">
                    <i></i>
                    登录
                </a>
            </div>
            <div class="resgister_pan">
                <a class="" href="/register/">
                    <i></i>
                    注册
                </a>
            </div>
        <?php else:?>
            <div class="login">
                <a class="" href="/usercenter/">
                    <i></i>
                    <?php echo $userName;?>(<?php echo empty($userNoReadMessageCount) ? $userNoReadMessageCount : "消息：" . $userNoReadMessageCount;?>)
                </a>
                <a class="logout" href="/logout/">[退出]</a>
            </div>
        <?php endif;?>
        <div class="look">
            <a class="" href="javascript:void(0);">
                <i></i>
                浏览记录
            </a>
            <div class="look_re">
                <a class="" href="javascript:void(0);">
                    <i></i>
                    浏览记录
                </a>

                <div class="look_info">
                   <?php if (!empty($userLookInfo)):?>
                        <?php $lookI = 1;?>
                        <?php $lookCount = count($userLookInfo);?>
                        <?php foreach($userLookInfo as $lookVal):?>
                            <?php $idStr = APF::get_instance()->encodeId($lookVal['id']);?>
                            <span <?php if ($lookI == $lookCount):?>class="last_span"<?php endif;?>>
                                <a href="/detail/index/<?php echo $idStr;?>/">
                                    <?php echo $lookVal['name'];?>
                                    <b>继续浏览</b>
                                </a>
                            </span>
                            <?php $lookI++;?>
                        <?php endforeach;?>
                    <?php else:?>
                        <span class="last_span nothing">
                            暂无浏览记录！
                        </span>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>
    <!-- 登录，注册、浏览记录 end   -->
</div>
<div class="header_tab">
    <ul>
        <li class="logo">
            <a class="logo1" href="/"></a>
        </li>
        <?php $menus = APF::get_instance()->get_config_value("menus")?>
        <?php $menusI = 1;?>
        <?php $index = $this->load->get_top_index();?>
        <?php foreach ($menus as $menuKey => $menuVal): ?>
            <li type="<?php echo $menuVal['class'];?>" class="<?php if ($menusI == 1):?>first_one<?php endif;?> <?php if ($index == $menuKey):?>current<?php endif;?>">
                <a href="<?php echo $menuVal['link']; ?>"><?php echo $menuVal['title'];?></a>
                <?php if (!empty($menuVal['new'])):?>
                    <i></i>
                <?php endif;?>
            </li>
            <?php $menusI++;?>
        <?php endforeach;?>
    </ul>
    <?php $rightMenus = APF::get_instance()->get_config_value("right_menus");?>
    <div class="menus_right">
        <?php $rmI = 1;?>
        <?php $rmCount = count($rightMenus);?>
        <?php foreach($rightMenus as $rMVal):?>
            <span <?php if($rmI == $rmCount):?>class="last_one"<?php endif;?>><a href="<?php echo $rMVal['link'];?>"><?php echo $rMVal['title'];?></a></span>
            <?php $rmI++;?>
        <?php endforeach;?>
    </div>
</div>