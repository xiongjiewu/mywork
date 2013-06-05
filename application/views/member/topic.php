<div class="help_daohang">
    <ul>
        <?php $i  = 0;?>
        <?php foreach($helpInfos as $infoVal):?>
            <li class="<?php if ($infoVal['id'] == $info['id']):?>current<?php endif;?><?php if ($i == 0):?> fisrt_one<?php endif;?>">
                <a href="<?php echo get_url("/topic/index/{$infoVal['id']}/")?>"><?php echo $infoVal['title'];?></a>
            </li>
            <?php $i++;?>
        <?php endforeach;?>
    </ul>
</div>
<div class="help_content">
    <?php echo $info['content'];?>
</div>