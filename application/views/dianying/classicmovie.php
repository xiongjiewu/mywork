<div class="row">
    <div class="span3 bs-docs-sidebar">
        <ul class="nav nav-list bs-docs-sidenav">
            <?php foreach ($movieSortType as $typeKey => $typeVal):?>
                <li class="dy_title_text">
                    <a href="javascript:void(0);">
                        <i class="icon-chevron-right"></i> <strong><?php echo $typeVal['type'];?>检索</strong>
                    </a>
                </li>
                <?php foreach ($typeVal['info'] as $typeValKey => $typeInfoVal): ?>
                    <li <?php if ($typeKey == $bigtype && $typeValKey == $type):?>class="active" <?php endif;?>>
                        <a href="<?php echo $typeVal['base_url'] . $typeValKey; ?>">
                            <i class="icon-chevron-right"></i>
                            <?php echo $typeInfoVal;?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endforeach;?>
        </ul>
    </div>
    <div class="span9">
        <?php if (!empty($movieList)):?>
        <?php foreach($movieList as $movieVal):?>
            <table class="table table-bordered <?php echo $movieVal['class'];?>">
                <tr>
                    <td class="info_image"><a href="<?php echo get_url("/detail/index/{$movieVal['id']}");?>"><img src="<?php echo $movieVal['image'];?>"></a></td>
                </tr>
                <tr>
                    <td>片名：<a href="<?php echo get_url("/detail/index/{$movieVal['id']}");?>"><?php echo $movieVal['name'];?></a></td>
                </tr>
                <tr>
                    <td>导演：<?php echo $movieVal['daoyan'];?></td>
                </tr>
            </table>
        <?php endforeach;?>
        <?php if ($mouvieCount > $limit):?>
        <table class="page">
            <tr>
                <td>
                    <?php $this->load->view("component/pagenew", array("fenye" => $fenye));?>
                </td>
            </tr>
        </table>
        <?php endif;?>
    </div>
    <?php else:?>
        
    <?php endif;?>
</div>