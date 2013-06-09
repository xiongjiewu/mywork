<div class="play_left">
    <div class="left_logo">
        <a href="/"></a>
    </div>
    <div class="play_dy_info">
        <h1>
            <a href="<?php echo APF::get_instance()->get_real_url("detail",$dyInfo['id']);?>"><?php echo $dyInfo['name'];?></a>
        </h1>
        <div class="play_dy_info_img">
            <a href="<?php echo APF::get_instance()->get_real_url("detail",$dyInfo['id']);?>">
                <img alt="<?php echo $dyInfo['name'];?>" src="<?php echo APF::get_instance()->get_image_url($dyInfo['image']);?>">
            </a>
        </div>
        <a class="play_button" href="javascript:void(0);"></a>
    </div>
    <?php if (!empty($watchLinkInfo)):?>
        <div class="play_link_list">
            <ul>
                <li class="link_title">其他观看地址</li>

                    <?php foreach ($watchLinkInfo as $watchLinkInfoKey => $watchLinkInfoVal): ?>
                        <li>
                        <a class="" href="<?php echo APF::get_instance()->get_real_url("play",$dyInfo['id'],array("id"=>$watchLinkInfoVal['id']));?>">
                            <?php echo $bofangqiType[$watchLinkInfoVal['player']];?>
                            <?php if (!empty($watchLinkInfoVal['beizhu'])):?>
                                <?php echo $watchLinkInfoVal['beizhu'];?>
                            <?php else:?>
                                <?php echo $qingxiType[$watchLinkInfoVal['qingxi']];?>
                                <?php if ($watchLinkInfoVal['shoufei'] == 2):?>
                                    <font color="#FA8072"><?php echo $shoufeiType[$watchLinkInfoVal['shoufei']];?></font>
                                <?php else:?>
                                    <?php echo $shoufeiType[$watchLinkInfoVal['shoufei']];?>
                                <?php endif;?>
                            <?php endif;?>
                        </a>
                        </li>
                    <?php endforeach;?>
                <li class="play_bottom">
                </li>
            </ul>
        </div>
    <?php endif;?>

    <?php if (!empty($userLookInfo)):?>
    <div class="play_link_list">
        <ul>
            <li class="link_title">我的播放记录</li>
        <?php $lookI = 1;?>
        <?php $lookCount = count($userLookInfo);?>
        <?php foreach($userLookInfo as $lookVal):?>
            <li>
            <?php $idStr = APF::get_instance()->encodeId($lookVal['id']);?>
                <a href="/detail/index/<?php echo $idStr;?>/">
                    <?php echo $lookVal['name'];?>
                </a>
            </li>
        <?php endforeach;?>
            <li class="play_bottom">
            </li>
        </ul>
    </div>
    <?php endif;?>
    <div class="play_action_list">
        <ul>
            <li>
                <!-- Baidu Button BEGIN -->
                <div id="bdshare" class="bdshare_t bds_tools_32 get-codes-bdshare">
                    <a class="bds_qzone"></a>
                    <a class="bds_tsina"></a>
                    <a class="bds_tqq"></a>
                    <a class="bds_renren"></a>
                    <a class="bds_t163"></a>
                    <span class="bds_more"></span>
                    <a class="shareCount"></a>
                </div>
                <script type="text/javascript" id="bdshare_js" data="type=tools&amp;uid=6709306" ></script>
                <script type="text/javascript" id="bdshell_js"></script>
                <script type="text/javascript">
                    document.getElementById("bdshell_js").src = "http://bdimg.share.baidu.com/static/js/shell_v2.js?cdnversion=" + Math.ceil(new Date()/3600000)
                </script>
                <!-- Baidu Button END -->
            </li>
        </ul>
    </div>
</div>
<div class="play_main">
    <iframe id="iframepage"  scrolling="yes" frameborder="0" src="<?php echo $watchInfo['link'];?>"></iframe>
</div>
<div class="play_bottom" >
    <ul>
        <li>
            <a href="<?php echo APF::get_instance()->get_real_url("detail",$dyInfo['id']);?>#createpost" target="_blank">点评</a>
            <?php if (empty($userName) && empty($userId)):?>
                <a href="/login/" target="_blank" class="play_login">登录</a>
                <a href="/register/" target="_blank">注册</a>
            <?php else:?>
                <a href="/usercenter/" target="_blank">
                    <?php echo $userName;?>(<?php echo empty($userNoReadMessageCount) ? $userNoReadMessageCount : "消息：" . $userNoReadMessageCount;?>)
                </a>
                <a href="/logout/">退出</a>
            <?php endif;?>
        </li>
    </ul>
</div>