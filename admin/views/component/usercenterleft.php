<div class="left_container">
    <div class="side-boxer">
        <div class="user-info">
            <img src="<?php echo $userInfo['photo'];?>">
            <dl>
                <dt><?php echo $userInfo['userName'];?></dt>
                <dd style="margin-left: 0">
                    <a href="<?php echo get_url("/usercenter/revised/")?>">修改资料</a>
                </dd>
            </dl>
        </div>
        <div class="side-menu">
            <a class="menu-item <?php if ($index == 1):?>menu-focus<?php endif;?>" href="<?php echo get_url("/usercenter/");?>">
                <i class="collection"></i>
                我的电影吧
                <span class="arrow-icon"></span>
            </a>
            <a class="menu-item <?php if ($index == 2):?>menu-focus<?php endif;?>" href="<?php echo get_url("/usercenter/mycollect/");?>">
                <i class="recommend"></i>
                我的收藏
                <span class="arrow-icon"></span>
            </a>
            <a class="menu-item <?php if ($index == 3):?>menu-focus<?php endif;?>" href="<?php echo get_url("/usercenter/feedback/");?>">
                <i class="icon-edit"></i>
                反馈我想看
                <span class="arrow-icon"></span>
            </a>
            <a class="menu-item <?php if ($index == 4):?>menu-focus<?php endif;?>" href="<?php echo get_url("/usercenter/mycollect/");?>">
                <i class="manpropcond"></i>
                订阅电影通知
                <span class="arrow-icon"></span>
            </a>
            <a class="menu-item <?php if ($index == 5):?>menu-focus<?php endif;?>" href="<?php echo get_url("/usercenter/feedback/suggest/");?>">
                <i class="ask"></i>
                投诉与建议
                <span class="arrow-icon"></span>
            </a>
        </div>
        <div class="shadow-boxer"></div>
    </div>
</div>