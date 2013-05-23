<div class="lastmovice_main">
    <div class="time_daohang">
        <ul class="time_daohang_list">
            <?php $dI = 1;?>
            <?php foreach($monthArr as $monthKey => $monthVal):?>
                <li <?php if ($dI == 1):?>class="current"<?php endif;?> name="<?php echo $monthVal;?>" title="点击查看<?php echo $monthKey;?>影片"><?php echo $monthKey;?></li>
                <?php $dI++;?>
            <?php endforeach;?>
        </ul>
    </div>
    <div class="lastmovice_list">
        <?php foreach ($movieList as $movieKey => $movieVal): ?>
            <?php if (empty($movieVal)){continue;}?>
            <div class="month_list">
                <div class="title_text"><?php echo $movieKey;?></div>
                <ul class="info_list_do" id="<?php echo $monthArr[$movieKey];?>">
                    <?php foreach ($movieVal as $mKey => $mVal): ?>
                        <li title="点击查看详情">
                            <div class="info_img">
                                <?php $idStr = APF::get_instance()->encodeId($mVal['id']);?>
                                <a href="<?php echo get_url("/detail/index/{$idStr}"); ?>/">
                                    <img alt="<?php echo $mVal['name'];?>" src="<?php echo trim(APF::get_instance()->get_config_value("img_base_url"),"/") . $mVal['image'];?>">
                                </a>
                            </div>
                            <div class="info_detail">
                                <dl>
                                    <dt>
                                        <a href="<?php echo get_url("/detail/index/{$idStr}"); ?>/"><?php echo $mVal['name'];?></a>
                                    </dt>
                                    <dd>
                                        <span class="time"><?php echo date("Y.m.d",$mVal['time1'])?></span>
                                        <span><i>类型：</i><a href="/moviceguide/type/<?php echo $mVal['type'];?>"><?php echo $movieType[$mVal['type']];?></a></span>
                                        <span><i>导演：</i><?php echo $mVal['daoyan'];?></span>
                                        <span><i>主演：</i><?php echo str_replace("/","、",$mVal['zhuyan']);?></span>
                                    </dd>
                                </dl>
                                <?php if (!empty($watchLinkInfo[$mVal['id']])): ?>
                                <div class="watch_link_list">
                                    <?php $wRes = array();?>
                                    <?php foreach ($watchLinkInfo[$mVal['id']] as $watchKey => $watchVal): ?>
                                        <?php if (empty($wRes[$watchVal['player']])){$wRes[$watchVal['player']] = $watchVal;}?>
                                    <?php endforeach;?>
                                    <?php $countI = 1;?>
                                    <?php if (count($wRes) <= 2):?>
                                        <a></a>
                                        <a></a>
                                    <?php endif;?>
                                    <?php foreach ($wRes as $wVal): ?>
                                        <?php if ($countI > 4){break;}?>
                                        <a title="点击观看(<?php if ($wVal['shoufei'] == 1):?>免费<?php else:?>收费<?php endif;?>)" href="<?php echo $wVal['link'];?>" target="_blank">
                                            <img alt="<?php echo $mVal['name'];?>" src="/images/webcon/icon<?php echo $wVal['player'];?>.png">
                                        </a>
                                        <?php $countI++;?>
                                    <?php endforeach;?>
                                </div>
                                <?php else:?>
                                    <div class="watch_link_list">
                                        <i>暂无</i>
                                    </div>
                                <?php endif;?>
                            </div>
                        </li>
                    <?php endforeach;?>
                </ul>
            </div>
        <?php endforeach;?>
    </div>
</div>
<script type="text/javascript">
    (function($){
        $(document).ready(function(){
            init.daoHangDingWei();
            var daohangObj = $("ul.time_daohang_list");
            daohangObj.find("li").each(function(){
                $(this).bind("click",function(){
                    daohangObj.find("li.current").removeClass("current");
                    $(this).addClass("current");
                    var name = $(this).attr("name");
                    var sH = $("#"+name+"").offset().top;
                    $(window).scrollTop(sH - 130);
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
            $("div.month_list ul li").each(function(){
                var aObj = $(this).find("a");
                var url = $(aObj.get(0)).attr("href");
                $(this).bind("click",function(){
                    window.location.href = url;
                });
                aObj.each(function() {
                    if ($(this).attr("href")) {
                        $(this).bind("click",function(event){
                            event.stopPropagation();
                        });
                    }
                })
            });
            $(window).bind("scroll", function() {//当滚动条滚动时
                init.daoHangDingWei();
            });
        });
    })(jQuery)
</script>