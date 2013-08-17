<div class="classmovice_main">
    <div class="classmovice_list">
        <ul class="list_info">
            <?php foreach($moviceList as $moviceVal):?>
                <li>
                    <?php $idStr = APF::get_instance()->encodeId($moviceVal['id']);?>
                    <a href="/detail/index/<?php echo $idStr;?>/">
                        <img src="<?php echo APF::get_instance()->get_image_url($moviceVal['image'],"dy",200); ?>">
                    </a>
                    <div class="title">
                        <a href="/detail/index/<?php echo $idStr;?>/" title="<?php echo $moviceVal['name'];?>">
                            <?php echo $moviceVal['name'];?>
                        </a>
                    </div>
                </li>
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
</div>