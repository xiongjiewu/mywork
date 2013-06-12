<a class="go_to_top" title="回到顶部"></a>
<div class="class_main">
    <div class="class_left">
        <ul>
            <?php foreach($paiHangInfo as $paihangKey => $paihangVal):?>
                <?php if (empty($paihangVal)){continue;}?>
                <li class="class_left_title title_<?php echo $paihangKey;?>"></li>
                <li class="class_left_fenge"></li>
                <?php foreach($paihangVal as $phK => $phV):?>
                    <li class="list <?php if ($listType == $paihangKey && $type == $phK):?>current<?php endif;?>">
                        <a href="<?php echo $phV['base_url'];?>"><?php echo $phV['htmlTitle'];?></a>
                    </li>
                <?php endforeach;?>
            <?php endforeach;?>
        </ul>
    </div>
    <div class="class_right">
        <ul>
            <?php $cI = 1;?>
            <?php foreach($moviceList as $moviceVal):?>
                <?php $idStr = APF::get_instance()->encodeId($moviceVal['id']);?>
                <li class="<?php if ($cI % 5 == 0):?>last<?php endif;?>" title="点击观看">
                    <a href="/detail/index/<?php echo $idStr;?>/" class="class_img">
                        <img alt="<?php echo $moviceVal['name'];?>" src="<?php echo trim(APF::get_instance()->get_config_value("img_base_url"), "/") . $moviceVal['image']; ?>">
                    </a>
                    <div class="class_dy_info">
                        <div class="class_dy_top">
                            <h1>
                                <a href="/detail/index/<?php echo $idStr;?>/">
                                    <?php echo $moviceVal['name'];?>
                                </a>
                            </h1>

                                <?php if ($listType == "top" && $type == 4):?>
                                    <div class="class_score class_search">
                                        <?php echo $paiHangInfo[$listType][$type]['s_title'];?>搜索量：<i><?php echo $moviceVal['search'];?></i>
                                    </div>
                                <?php else:?>
                                    <div class="class_score">
                                    <?php echo $paiHangInfo[$listType][$type]['s_title'];?>评分：<i><?php echo $moviceVal['score'];?></i>
                                    </div>
                                <?php endif;?>
                        </div>
                        <div>
                            <span>主演:</span>
                            <?php if (!empty($moviceVal['zhuyan']) && $moviceVal['zhuyan'] != "暂无"):?>
                                <?php $zhuyan = str_replace("/","、",$moviceVal['zhuyan'])?>
                                <?php $zhuyanArr = explode("、",$zhuyan);?>
                                <?php $zhuyanCount = count($zhuyanArr);?>
                                <?php $zhuyanI = 1;?>
                                <?php foreach($zhuyanArr as $zyVal):?>
                                    <a href="/search?key=<?php echo $zyVal;?>" class="class_user"><?php echo $zyVal;?></a>
                                    <?php if ($zhuyanI != $zhuyanCount):?>
                                        、
                                    <?php endif;?>
                                    <?php $zhuyanI++;?>
                                <?php endforeach;?>
                            <?php else:?>
                                暂无
                            <?php endif;?>
                        </div>
                        <div>
                            <span>导演:</span>
                            <?php if (!empty($moviceVal['daoyan']) && $moviceVal['daoyan'] != "暂无"):?>
                                <?php $daoyan = str_replace("/","、",$moviceVal['daoyan'])?>
                                <?php $daoyanArr = explode("、",$daoyan);?>
                                <?php $daoyanCount = count($daoyanArr);?>
                                <?php $daoyanI = 1;?>
                                <?php foreach($daoyanArr as $dyVal):?>
                                    <a href="/search?key=<?php echo $dyVal;?>" class="class_user"><?php echo $dyVal;?></a>
                                    <?php if ($daoyanI != $daoyanCount):?>
                                        、
                                    <?php endif;?>
                                    <?php $daoyanI++;?>
                                <?php endforeach;?>
                            <?php else:?>
                                暂无
                            <?php endif;?>
                        </div>
                        <div>
                            <span>年份:</span>
                            <?php if(empty($moviceVal['nianfen'])):?>
                                暂无
                            <?php else:?>
                                <a href="/moviceguide/index/year/<?php echo $moviceVal['nianfen']?>/"><?php echo $moviceVal['nianfen'];?></a>
                            <?php endif;?>
                        </div>
                        <div>
                            <span>类型:</span>
                            <a href="/moviceguide/type/1/<?php echo $moviceVal['type']?>/"><?php echo $movieType[$moviceVal['type']];?></a>
                        </div>
                        <div>
                            <span>简介:</span>
                            <span class="jieshao">
                                <?php echo $moviceVal['jieshao'];?><a href="/detail/index/<?php echo $idStr;?>/">[了解详情]</a>
                            </span>
                        </div>
                    </div>
                </li>
                <?php $cI++;?>
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

    <div class="clear"></div>
</div>