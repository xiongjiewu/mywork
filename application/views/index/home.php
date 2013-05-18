<div class="home_top">
    <div class="top_img"></div>
    <div class="middel_img">
        <img src="/images/head/home_top2.jpg">
        <div class="top_bottom"></div>
    </div>
</div>
<div class="home_middel">
    <div class="home_middel_main">
        <div class="home_middel_middel_mian">
            <div class="home_middel_middel">
                <ul>
                    <?php $menus = APF::get_instance()->get_config_value("menus")?>
                    <?php $index = $this->load->get_top_index();?>
                    <?php foreach($menus as $menuKey => $menuVal):?>
                        <?php if ($menuVal['index'] == "index"){continue;}?>
                        <li class="<?php echo $menuVal['class']; ?>">
                            <a class="menus_title" href="<?php echo $menuVal['link']; ?>"><?php echo $menuVal['title'];?></a>
                            <?php if (!empty($menuVal['index']) && ($menuVal['index'] == "list")): ?>
                                <div class="dy_type_list">
                                    <table class="table">
                                        <?php foreach ($menuVal['type_info'] as $infokey => $infoVal): ?>
                                            <tr>
                                                <th><?php echo $infoVal['type'];?></th>
                                                <?php foreach ($infoVal['info'] as $key => $infoDetail): ?>
                                                    <td>
                                                        <a <?php if ($index == $menuKey && $infokey == $data['bigtype'] && $key == $data['type']): ?>class="active" <?php endif;?>
                                                           href="<?php echo $infoVal['base_url'] . $key; ?>"><?php echo $infoDetail;?></a>
                                                    </td>
                                                <?php endforeach;?>
                                            </tr>
                                        <?php endforeach;?>
                                    </table>
                                </div>
                            <?php endif;?>
                        </li>
                    <?php endforeach;?>
                    <?php if (!empty($userName)): ?>
                        <li class="username" style="float: right">
                            <a href="<?php echo get_url("/usercenter/"); ?>"><i
                                    class="icon-user icon-user"></i><?php echo $userName;?>
                                (<?php echo $userNoReadMessageCount;?>)
                            </a>
                            <div class="user_in">
                                <table class="table">
                                    <tr>
                                        <td><a href="<?php echo get_url("/usercenter/"); ?>"><i
                                                    class="icon-user"></i><?php echo $userName;?></a><a
                                                <?php if ($userNoReadMessageCount > 0): ?>class="message"
                                                href="<?php echo get_url("/usercenter/message/0/") ?>" title="有新消息"
                                                <?php else: ?>href="<?php echo get_url("/usercenter/message/") ?>"
                                                class="icon-envelope"<?php endif;?>></a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="<?php echo get_url("/usercenter/mycollect/"); ?>"><i
                                                    class="icon-film"></i>我&nbsp;的&nbsp;收&nbsp;藏</a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="<?php echo get_url("/usercenter/feedback/"); ?>"><i
                                                    class="icon-edit"></i>反&nbsp;馈&nbsp;想&nbsp;看</a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="<?php echo get_url("/usercenter/notice/"); ?>"><i
                                                    class="icon-volume-up"></i>电&nbsp;影&nbsp;通&nbsp;知</a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="<?php echo get_url("/logout/"); ?>"><i class="icon-off"></i>退&nbsp;出&nbsp;登&nbsp;录</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </li>
                    <?php else: ?>
                        <li class="loginr">
                            <a href="<?php echo get_url("/register/"); ?>" title="注册" >
                                <i class="icon-pencil"></i>
                                注册
                            </a>
                        </li>
                        <li class="loginr">
                            <a href="<?php echo get_url("/login/"); ?>" title="登录" >
                                <i class="icon-user"></i>
                                登录
                            </a>
                        </li>
                    <?php endif;?>
                    <li class="home_search">
                        <form autocomplete="off" name="search_dy" id="search_dy" onsubmit="return false;"
                              action="<?php echo get_url("/search/"); ?>">
                            <input type="text" class="search_value" name="search" id="search"
                                   value="<?php if (isset($data['searchW'])): ?><?php echo $data['searchW']; ?><?php else: ?>搜索您喜欢的影片...<?php endif; ?>">
                            <input type="submit" class="submit" name="search_submit" id="search_submit" value="">
                        </form>
                        <div class="about_search">
                            <?php if (isset($data['searchW'])): ?>
                                <span><?php echo $data['searchW']; ?></span><?php endif;?>
                        </div>
                    </li>
                </ul>
            </div>

            <?php if (!empty($newestDyInfo)):?>
                <div class="home_middel_dy_list">
                    <div class="tag">
                    </div>
                    <ul>
                        <?php foreach($newestDyInfo as $dyInfoVal):?>
                            <li>
                                <a href="/detail/index/<?php echo $dyInfoVal['id'];?>/">
                                    <img alt="<?php echo $dyInfoVal['name'];?>" src="<?php echo trim(APF::get_instance()->get_config_value("img_base_url"), "/") . $dyInfoVal['name'];?>">
                                </a>
                                <div class="dy_title">
                                    <a href="/detail/index/<?php echo $dyInfoVal['id'];?>/">
                                        <?php echo $dyInfoVal['name'];?>
                                    </a>
                                </div>
                                <div class="dy_title">
                                    <?php echo str_replace("/","、",$dyInfoVal['zhuyan']);?>
                                </div>
                            </li>
                        <?php endforeach;?>
                    </ul>
                </div>
            <?php endif;?>

            <?php if (!empty($willDyInfo)):?>
                <div class="home_middel_dy_list">
                    <div class="tag will">
                    </div>
                    <ul>
                        <?php foreach($willDyInfo as $dyInfoVal):?>
                            <li>
                                <a href="/detail/index/<?php echo $dyInfoVal['id'];?>/">
                                    <img alt="<?php echo $dyInfoVal['name'];?>" src="<?php echo trim(APF::get_instance()->get_config_value("img_base_url"), "/") . $dyInfoVal['name'];?>">
                                </a>
                                <div class="dy_title">
                                    <a href="/detail/index/<?php echo $dyInfoVal['id'];?>/">
                                        <?php echo $dyInfoVal['name'];?>
                                    </a>
                                </div>
                                <div class="dy_title">
                                    <?php echo str_replace("/","、",$dyInfoVal['zhuyan']);?>
                                </div>
                            </li>
                        <?php endforeach;?>
                    </ul>
                </div>
            <?php endif;?>

            <?php if (!empty($classDyInfo)):?>
                <div class="home_middel_dy_list">
                    <div class="tag class">
                    </div>
                    <ul>
                        <?php foreach($classDyInfo as $dyInfoVal):?>
                            <li>
                                <a href="/detail/index/<?php echo $dyInfoVal['id'];?>/">
                                    <img alt="<?php echo $dyInfoVal['name'];?>" src="<?php echo trim(APF::get_instance()->get_config_value("img_base_url"), "/") . $dyInfoVal['name'];?>">
                                </a>
                                <div class="dy_title">
                                    <a href="/detail/index/<?php echo $dyInfoVal['id'];?>/">
                                        <?php echo $dyInfoVal['name'];?>
                                    </a>
                                </div>
                                <div class="dy_title">
                                    <?php echo str_replace("/","、",$dyInfoVal['zhuyan']);?>
                                </div>
                            </li>
                        <?php endforeach;?>
                    </ul>
                </div>
            <?php endif;?>
        </div>
    </div>
</div>