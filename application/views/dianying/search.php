<div class="row">
    <div class="navbar">
        <div class="navbar-inner show_s_count">
            <a class="brand" href="javascript:void(0);">共为您搜索到<span class="count"><?php echo count($searchMovieInfo);?></span>部相关资源,<span class="sugg">您可以通过如下检索快速找到您想找的资源！</span></a></ul>
        </div>
    </div>
    <div class="span3 bs-docs-sidebar">
        <ul class="nav nav-list bs-docs-sidenav">
            <?php foreach ($movieSortType as $typeKey => $typeVal):?>
                <li class="dy_title_text">
                    <a href="javascript:void(0);">
                        <i class="icon-chevron-right"></i> <strong><?php echo $typeVal['type'];?>检索</strong>
                    </a>
                </li>
                <?php foreach ($typeVal['info'] as $typeValKey => $typeInfoVal): ?>
                    <li>
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
        <?php if (!empty($searchMovieInfo)):?>
            <?php foreach($searchMovieInfo as $movieVal):?>
                <table class="table table-bordered <?php echo $movieVal['class'];?>">
                    <tr>
                        <td class="info_image">
                            <a href="<?php echo get_url("/detail/index/{$movieVal['id']}");?>">
                                <img src="<?php echo trim(get_config_value("img_base_url"),"/") . $movieVal['image'];?>">
                            </a>
                        </td>
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

        <?php else:?>
            <table class="table error_table">
                <tr>
                    <td class="image"><img src="/images/error.png"></td>
                    <td class="text">糟糕，您搜索的信息暂无，您可以尝试换个方式搜索！</td>
                </tr>
            </table>
        <?php endif;?>
    </div>
</div>