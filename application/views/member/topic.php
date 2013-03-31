<div class="help_daohang">
    <ul>
        <?php foreach($helpInfos as $infoVal):?>
            <li <?php if ($infoVal['id'] == $info['id']):?>class="current"<?php endif;?>>
                <a href="<?php echo get_url("/topic/index/{$infoVal['id']}/")?>"><?php echo $infoVal['title'];?></a>
            </li>
        <?php endforeach;?>
    </ul>
</div>
<div class="help_content">
    <?php echo $info['content'];?>
</div>