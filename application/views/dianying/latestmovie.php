<input type="hidden" name="current_id" id="current_id" value="">
<div class="row">
    <?php if (!empty($movieList)): ?>
        <div class="span3 bs-docs-sidebar">
            <ul class="nav nav-list bs-docs-sidenav dy_bs-docs-sidenav" style="*width: 220px;">
                <?php $dI = 1;?>
                <?php foreach($monthArr as $monthKey => $monthVal):?>
                    <li><a <?php if ($dI == 1):?>class="click"<?php endif;?> name="<?php echo $monthVal;?>" href="javascript:void(0);" title="点击查看<?php echo $monthKey;?>影片"><i class="icon-chevron-right"></i> <?php echo $monthKey;?></a></li>
                    <?php $dI++;?>
                <?php endforeach;?>
            </ul>
        </div>
        <div class="dy_total">
            <?php foreach ($movieList as $movieKey => $movieVal): ?>
                <?php if (empty($movieVal)){continue;}?>
                <div class="bs-docs-example" id="<?php echo $monthArr[$movieKey];?>">
                    <div class="dy_info_list">
                        <div class="title">
                            <h3>
                                <?php echo $movieKey;?>
                            </h3>
                        </div>
                        <ul class="info_list">
                            <?php foreach ($movieVal as $mKey => $mVal): ?>
                                <li title="点击查看详情" class="dy_info_li">
                                    <div class="dy_name_l">
                                        <a class="dy_name"
                                           href="<?php echo get_url("/detail/index/{$mVal['id']}"); ?>">
                                            <img class="dy_img" src="<?php echo trim(APF::get_instance()->get_config_value("img_base_url"),"/") . $mVal['image'];?>">
                                        </a>
                                        <a class="dy_name"
                                           href="<?php echo get_url("/detail/index/{$mVal['id']}"); ?>">
                                            <?php echo $mVal['name'];?>
                                        </a>
                                        <?php if (empty($shouCangInfo[$mVal['id']])):?>
                                            <span class="shoucang_action shoucang_dy" title="点击收藏" val="<?php echo $mVal['id'];?>"></span>
                                        <?php else:?>
                                            <span class="shoucang_action shoucang_dy_y" title="已收藏"></span>
                                        <?php endif;?>
                                    </div>
                                    <?php if (!empty($watchLinkInfo[$mVal['id']]) || !empty($downLoadLinkInfo[$mVal['id']])): ?>
                                        <div class="dy_link_down">
                                            <?php if (!empty($watchLinkInfo[$mVal['id']])): ?>
                                            <div class="dy_watch_down">
                                                <div class="watch_down">观看：</div>
                                                <?php $watchLinkI = 1; ?>
                                                <?php foreach ($watchLinkInfo[$mVal['id']] as $watchKey => $watchVal): ?>
                                                    <a title="点击观看" class="" href="<?php echo $watchVal['link']; ?>" target="_blank">链接<?php echo $watchLinkI++;?></a>
                                                <?php endforeach; ?>
                                            </div>
                                            <?php endif;?>
                                            <?php if (!empty($downLoadLinkInfo[$mVal['id']])): ?>
                                            <div class="dy_watch_down">
                                                <div class="watch_down">下载：</div>
                                                <?php $downLinkI = 1; ?>
                                                <?php foreach ($downLoadLinkInfo[$mVal['id']] as $downKey => $downVal): ?>
                                                    <a title="点击下载" class="" href="<?php echo $downVal['link']; ?>" target="_blank">链接<?php echo $downLinkI++;?></a>
                                                <?php endforeach; ?>
                                            </div>
                                            <?php endif;?>
                                        </div>
                                    <?php endif;?>
                                    <div class="dy_jianjie">
                                        <table>
                                            <tr>
                                                <td class="dy_jianjie_t">时长：</td>
                                                <td><?php echo $mVal['shichang'];?>分钟</td>
                                            </tr>
                                            <tr>
                                                <td class="dy_jianjie_t">类型：</td>
                                                <td><?php echo $movieType[$mVal['type']];?></td>
                                            </tr>
                                            <tr>
                                                <td class="dy_jianjie_t">地区：</td>
                                                <td><?php echo $moviePlace[$mVal['diqu']];?></td>
                                            </tr>
                                            <tr>
                                                <td class="dy_jianjie_t" valign="top">主演：</td>
                                                <td><?php echo $mVal['zhuyan'];?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </li>
                            <?php endforeach;?>
                        </ul>
                    </div>
                </div>
            <?php endforeach;?>
        </div>
    <?php endif; ?>
</div>
<script type="text/javascript">
    (function($){
        $(document).ready(function(){
            init.daoHangDingWei();
            var daohangObj = $("ul.dy_bs-docs-sidenav");
            daohangObj.bind("mouseover",function(){
                $(this).addClass("bs-docs-sidenav-over");
            });
            daohangObj.bind("mouseleave",function(){
                $(this).removeClass("bs-docs-sidenav-over");
            });
            daohangObj.find("li a").each(function(){
                $(this).bind("click",function(){
                    daohangObj.find("li a.click").removeClass("click");
                    $(this).addClass("click");
                });
            });
            var dyInfoLiObj = $("li.dy_info_li");
            dyInfoLiObj.each(function(){
                $(this).bind("mouseover",function(){
                    $(this).addClass("li_over");
                    $(this).find("span.shoucang_action").show();
                    init.showWatchAndDownLink($(this));
                });
                $(this).bind("click",function(){
                    var url = $($(this).find("a").get(0)).attr("href");
                    window.location.href = url;
                });
                $(this).find("a").each(function(){
                    $(this).bind("click",function(event){
                        event.stopPropagation();
                    });
                });
            });
            dyInfoLiObj.each(function(){
                $(this).bind("mouseleave",function(){
                    $(this).removeClass("li_over");
                    $(this).find("span.shoucang_action").hide();
                    init.hideWatchAndDownLink($(this));
                });
            });
            daohangObj.find("a").each(function(){
                $(this).bind("click",function(){
                    var name = $(this).attr("name");
                    var sH = $("#"+name+"").offset().top;
                    $(window).scrollTop(sH - 50);
                });
            });
            var shoucangObj = $("span.shoucang_dy");
            shoucangObj.each(function(){
                $(this).bind("click",function(event){
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
            $(window).bind("scroll", function() {//当滚动条滚动时
                init.daoHangDingWei();
            });
        });
    })(jQuery)
</script>
