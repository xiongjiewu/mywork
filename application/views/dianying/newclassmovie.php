<div class="class_main">
    <div class="class_left">
        <ul>
            <?php foreach($paiHangInfo as $paihangKey => $paihangVal):?>
                <?php if (empty($paihangVal)){continue;}?>
                <li class="class_left_title title_<?php echo $paihangKey;?>"></li>
                <li class="class_left_fenge"></li>
                <?php foreach($paihangVal as $phK => $phV):?>
                    <li class="list <?php if ($listType == $paihangKey && $type == $phK):?>current<?php endif;?>">
                        <a href="<?php echo $phV['base_url'];?>"><?php echo $phV['htmlTitle'];?></a>
                    </li>
                <?php endforeach;?>
            <?php endforeach;?>
        </ul>
    </div>
    <div class="class_right">
        <ul>
            <?php $cI = 1;?>
            <?php foreach($moviceList as $moviceVal):?>
                <?php $idStr = APF::get_instance()->encodeId($moviceVal['id']);?>
                <li class="<?php if ($cI % 5 == 0):?>last<?php endif;?>">
                    <a href="/detail/index/<?php echo $idStr;?>/" class="class_img">
                        <img alt="<?php echo $moviceVal['name'];?>" src="<?php echo trim(APF::get_instance()->get_config_value("img_base_url"), "/") . $moviceVal['image']; ?>">
                    </a>
                    <span class="class_name">
                        <a href="/detail/index/<?php echo $idStr;?>/" class="class_name"><?php echo $moviceVal['name'];?></a>
                        <i class="score"><?php echo $moviceVal['score'];?></i>
                    </span>
                    <span class="class_user">
                        <?php if (!empty($moviceVal['zhuyan']) && $moviceVal['zhuyan'] != "暂无"):?>
                            <?php $zhuyan = str_replace("/","、",$moviceVal['zhuyan'])?>
                            <?php $zhuyanArr = explode("、",$zhuyan);?>
                            <?php $zhuyanCount = count($zhuyanArr);?>
                            <?php $zhuyanI = 1;?>
                            <?php foreach($zhuyanArr as $zyVal):?>
                                <a href="/search?key=<?php echo $zyVal;?>" class="class_user"><?php echo $zyVal;?></a>
                                <?php if ($zhuyanI != $zhuyanCount):?>
                                、
                                <?php endif;?>
                                <?php $zhuyanI++;?>
                            <?php endforeach;?>
                        <?php endif;?>
                    </span>
                </li>
                <?php $cI++;?>
            <?php endforeach;?>
        </ul>
        <?php if ($totalCount > $limit):?>
            <div class="page_info">
                <table class="page">
                    <tr>
                        <td>
                            <?php $this->load->view("component/pagenew", array("fenye" => $fenye));?>
                        </td>
                    </tr>
                </table>
            </div>
        <?php endif;?>
    </div>

    <div class="clear"></div>
</div>