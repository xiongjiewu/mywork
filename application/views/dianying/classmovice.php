<div class="classmovice_list">
    <ul class="list_info">
        <?php foreach($moviceList as $moviceVal):?>
            <li>
                <a href="/detail/index/<?php echo $moviceVal['id'];?>/">
                    <img src="<?php echo trim(APF::get_instance()->get_config_value("img_base_url"), "/") . $moviceVal['image']; ?>">
                </a>
                <div class="title">
                    <a href="/detail/index/<?php echo $moviceVal['id'];?>/" title="<?php echo $moviceVal['name'];?>">
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