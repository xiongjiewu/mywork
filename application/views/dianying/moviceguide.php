<div class="classmovice_list">
    <div class="type_list">
        <?php foreach ($movieSortType as $typeKey => $typeVal):?>
            <div class="item">
                <label><?php echo $typeVal['type'];?>：</label>
                <ul>
                    <li <?php if (($typeKey != $bigtype && ($param[$typeKey] == "all")) || ($typeKey == $bigtype && $type == "all")):?>class="active" <?php endif;?>>
                        <a href="<?php echo $typeVal['base_url'];?>">全部</a>
                    </li>
                    <?php foreach ($typeVal['info'] as $typeValKey => $typeInfoVal):?>
                        <li <?php if ($typeInfoVal['active']):?>class="active" <?php endif;?>>
                            <a href="<?php echo $typeInfoVal['url']; ?>">
                                <?php echo $typeInfoVal['name'];?>
                            </a>
                        </li>
                    <?php endforeach;?>
                </ul>
            </div>
        <?php endforeach;?>
    </div>
    <ul class="list_info">
        <?php foreach($movieList as $moviceVal):?>
            <li>
                <?php $idStr = APF::get_instance()->encodeId($moviceVal['id']);?>
                <a href="/detail/index/<?php echo $idStr;?>/">
                    <img src="<?php echo trim(APF::get_instance()->get_config_value("img_base_url"), "/") . $moviceVal['image']; ?>">
                </a>
                <div class="title">
                    <a href="/detail/index/<?php echo $idStr;?>/" title="<?php echo $moviceVal['name'];?>">
                        <?php echo $moviceVal['name'];?>
                    </a>
                </div>
                <?php if (!empty($moviceVal['daoyan'])):?>
                    <div class="title">
                        导演：<?php echo trim($moviceVal['daoyan']);?>
                    </div>
                <?php else:?>
                    <div class="title">
                        地区：<?php echo $moviePlace[$moviceVal['diqu']];?>
                    </div>
                <?php endif;?>
                <div class="title">
                    类型：<?php echo $movieType[$moviceVal['type']];?>
                </div>
            </li>
        <?php endforeach;?>
    </ul>

    <?php if ($mouvieCount > $limit):?>
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
