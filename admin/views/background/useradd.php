<div class="link_ag">

</div>
<div class="bs-docs-example">
    <div class="usergiveList">
        <div class="actionList">
            <ul>
                <li type="6" class="btn">批量删除</li>
            </ul>
        </div>
        <div class="listTable">
            <table cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <th class="chose"><span class="choseAll">全选</span></th>
                    <th>电影</th>
                    <th>时间</th>
                    <th>类型</th>
                    <th>操作</th>
                </tr>
                <?php if (!empty($userGiveList)):?>
                    <?php $count = count($userGiveList);$i = 1;?>
                    <?php foreach($userGiveList as $infoVal):?>
                        <tr <?php if ($i == $count):?>class="lastOne"<?php elseif($i == 1):?>class="firstOne"<?php endif;?>>
                            <td class="chose"><input type="checkbox" v="<?php echo $infoVal['id'];?>" name="chose" id="chose1"></td>
                            <td><?php echo $infoArr[$infoVal['infoId']]['name'];?></td>
                            <td><?php echo date("Y-m-d H:i:s",$infoVal['time']);?></td>
                            <td>
                                <?php if ($infoVal['type'] == 1):?>
                                    观看
                                <?php else:?>
                                    下载
                                <?php endif;?>
                            </td>
                            <td>
                                <span>
                                    <a href="<?php echo get_url("/background/edituseradd/{$infoVal['id']}/")?>" class="ag">采纳</a> |
                                    <a class="del" val="<?php echo $infoVal['id'];?>" href="javascript:void(0);">删除</a> |
                                    <a href="<?php echo $infoVal['link'];?>" target="_blank">查看</a>
                                </span></td>
                        </tr>
                        <?php $i++;?>
                    <?php endforeach;?>
                <?php endif;?>
            </table>
        </div>
    </div>
    <?php $this->load->view("component/pagenew",array("fenye" => $fenye));?>
</div>