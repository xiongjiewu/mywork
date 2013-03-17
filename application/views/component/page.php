<div class="fenye">
    <ul>
        <?php if (!empty($fenye)):?>
        <?php foreach($fenye as $val):?>
            <li><a href="<?php echo $val['link'];?>" <?php if ($val['current']):?>class="current"<?php endif;?>><?php echo $val['page'];?></a></li>
        <?php endforeach;?>
        <?php endif;?>
    </ul>
</div>