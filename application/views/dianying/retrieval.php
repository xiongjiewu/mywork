<?php $this->load->view("component/ideapan");//返回顶部与提出意见标签?>
<input type="hidden" name="bType" id="bType" value="<?php echo $b;?>">
<input type="hidden" name="tType" id="tType" value="<?php echo $t;?>">
<input type="hidden" name="sType" id="sType" value="<?php echo $s;?>">
<input type="hidden" name="nextOffset" id="nextOffset" value="<?php echo $nextOffset;?>">
<input type="hidden" name="infoCount" id="infoCount" value="<?php echo $infoCount;?>">
<div class="retrieval_main">
    <div class="guide">
        <a href="/">首页 </a>>
        <span> 检索</span>
    </div>
    <div class="retrieval_type">
        <ul>
            <?php $bRI = 1;?>
            <?php foreach($bR as $bRVal):?>
                <li>
                    <a class="<?php echo $bRVal['type'];?>_retrieval<?php echo $bRVal['active'] ? " current" : "";?>" href="<?php echo $bRVal['url'];?>">
<!--                        <i></i>-->
                        <?php echo $bRVal['title'];?>
                    </a>
                </li>
                <?php if ($bRI < count($bR)):?>
                    <li class="line">|</li>
                <?php endif;?>
                <?php $bRI++;?>
            <?php endforeach;?>
        </ul>
    </div>
    <div class="retrieval_list">
        <ul>
            <?php foreach($letterList as $letterVal):?>
                <li>
                    <a class="<?php echo $letterVal['active'] ? "new" : "";?>" href="<?php echo $letterVal['url'];?>"><?php echo ($letterVal['title'] == "@") ? "其他" : $letterVal['title'];?></a>
                </li>
            <?php endforeach;?>
        </ul>
    </div>
    <div class="retrieval_title">
        <h1>
            <?php echo ($s == "@") ? "其他" : $s;?>
            <span>
                <a class="p<?php if ($t == "p"):?> current<?php endif;?>" href="<?php echo APF::get_instance()->get_real_url("/retrieval","",array("b" => $b,"t" => "p","s" => $s));?>">按图文</a>
                <a class="w<?php if ($t == "w"):?> current<?php endif;?>" href="<?php echo APF::get_instance()->get_real_url("/retrieval","",array("b" => $b,"t" => "w","s" => $s));?>">按列表</a>
            </span>
        </h1>
    </div>
    <div class="retrieval_movie_list">
            <?php if (!empty($infoList)):?>
                <ul>
                    <?php foreach($infoList as $infoVal):?>
                        <?php
                            if ($b == "d") {
                                $imgUrl = APF::get_instance()->get_image_url($infoVal['image']);
                                $url = APF::get_instance()->get_real_url("/detail",$infoVal['id'],array("from" => "retrieval_dy"));
                            } else {
                                $imgUrl = APF::get_instance()->get_image_url($infoVal['photo']);
                                $url = APF::get_instance()->get_real_url("/people",$infoVal['id'],array("from" => "retrieval_people"));
                            };
                        ?>
                        <li class="list_by_img">
                            <a href="<?php echo $url;?>" class="img_info">
                                <img src="<?php echo $imgUrl;?>">
                            </a>
                            <span>
                                <a class="" href="<?php echo $url;?>" title="<?php echo $infoVal['name'];?>">
                                    <?php echo $infoVal['name'];?>
                                </a>
                            </span>
                        </li>
                    <?php endforeach;?>
                </ul>
                <?php else:?>
                    <ul class="no_result">
                       悲剧了，暂无搜索结果。请尝试其他检索方式或使用搜索找您喜欢的内容。<br><br>
                        或
                        <?php if (empty($userId)):?>
                            <a target="_blank" href="<?php echo APF::get_instance()->get_real_url("/login",'',array("bgurl" => base64_encode("/usercenter/feedback/")));?>">反馈你想看</a>
                        <?php else:?>
                            <a target="_blank" href="<?php echo APF::get_instance()->get_real_url("/usercenter/feedback/");?>">反馈你想看</a>
                        <?php endif;?>。
                    </ul>
            <?php endif;?>
    </div>
    <div class="clear"></div>
</div>