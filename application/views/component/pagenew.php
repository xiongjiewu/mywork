<?php if (!empty($fenye)):?>
    <div class="btn-group">
        <?php foreach($fenye as $pageVal):?>
            <a class="btn <?php if (empty($pageVal['able'])):?>enable<?php endif;?> <?php if (!empty($pageVal['current'])):?>btn-primary<?php endif;?>" href="<?php echo $pageVal['link'];?>"><?php echo $pageVal['page'];?></a>
        <?php endforeach;?>
    </div>
<?php endif;?>