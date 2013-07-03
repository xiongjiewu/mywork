<?php $this->load->view("component/ideapan");//返回顶部与提出意见标签?>
<input type="hidden" name="current_id" id="current_id" value="">
<div class="classmovice_main">
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
                    <div class="dy_img">
                        <a href="/detail/index/<?php echo $idStr;?>?from=movieguide_movie_list">
                            <img src="<?php echo trim(APF::get_instance()->get_config_value("img_base_url"), "/") . $moviceVal['image']; ?>">
                        </a>
                        <?php if (empty($shouCangInfo[$moviceVal['id']])):?>
                            <span class="shoucang_action shoucang_dy" title="点击收藏" val="<?php echo $moviceVal['id'];?>"></span>
                        <?php else:?>
                            <span class="shoucang_action shoucang_dy_y" title="已收藏"></span>
                        <?php endif;?>
                    </div>
                    <div class="title">
                        <a href="/detail/index/<?php echo $idStr;?>?from=movieguide_movie_list" title="<?php echo $moviceVal['name'];?>">
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
    <div class="clear"></div>
</div>
<script type="text/javascript">
    (function($){
        $(document).ready(function(){
            var shoucangObj = $("span.shoucang_dy");
            shoucangObj.each(function(){
                $(this).bind("click",function(event) {
                    <?php if (empty($userId)):?>
                    var id = $(this).attr("val");
                    $("#current_id").val(id);
                    logPanInit.showLoginPan("init.loginCallBack");
                    event.stopPropagation();
                    <?php else:?>
                    init.shouCangDo($(this));
                    event.stopPropagation();
                    <?php endif;?>
                });
            });
        });
    })(jQuery)
</script>