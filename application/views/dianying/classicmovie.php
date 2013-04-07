<a class="go_to_top" title="回到顶部"></a>
<div class="row">
    <div class="span3 bs-docs-sidebar">
        <ul class="nav nav-list bs-docs-sidenav">
            <li <?php if ($type == "all"):?>class="active" <?php endif;?>>
                <a href="<?php echo get_url("/classicmovie/");?>">
                    <i class="icon-chevron-right"></i> 全部
                </a>
            </li>
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
                    <td class="info_image"><a href="<?php echo get_url("/detail/index/{$movieVal['id']}");?>"><img src="<?php echo trim(get_config_value("img_base_url"),"/") . $movieVal['image'];?>"></a></td>
                </tr>
                <tr>
                    <td>片名：<a href="<?php echo get_url("/detail/index/{$movieVal['id']}");?>"><?php echo $movieVal['name'];?></a></td>
                </tr>
                <tr>
                    <td>导演：<?php echo $movieVal['daoyan'];?></td>
                </tr>
                <tr>
                    <td>地区：<?php echo $moviePlace[$movieVal['diqu']];?></td>
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

        <?php else:?>
            <table class="table error_table">
                <tr>
                    <td class="image"><img src="/images/error.png"></td>
                    <td class="text">糟糕，您检索的信息暂无或者信息有误，您可以检索其他信息！</td>
                </tr>
            </table>
        <?php endif;?>
    </div>
</div>