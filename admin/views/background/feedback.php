<div class="bs-docs-example">
    <div class="feedbackList">
        <div class="actionList">
            <ul>
                <li type="1" class="btn btn-success">查看全部</li>
                <li type="2" class="btn btn-primary">意见未回复</li>
                <li type="3" class="btn btn-info">意见已回复</li>
                <li type="4" class="btn btn-warning">观看未回复</li>
                <li type="5" class="btn">观看已回复</li>&nbsp;&nbsp;&nbsp;&nbsp;
                <li type="6" class="btn">批量删除</li>
            </ul>
        </div>
        <div class="listTable">
            <table cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <th class="chose"><span class="choseAll">全选</span></th>
                    <th>标题</th>
                    <th>用户</th>
                    <th>时间</th>
                    <th>类型</th>
                    <th>操作</th>
                </tr>
                <?php if (!empty($feedbackInfoList)):?>
                    <?php $count = count($feedbackInfoList);$i = 1;?>
                    <?php foreach($feedbackInfoList as $infoVal):?>
                        <tr <?php if ($i == $count):?>class="lastOne"<?php elseif($i == 1):?>class="firstOne"<?php endif;?>>
                            <td class="chose"><input type="checkbox" v="<?php echo $infoVal['id'];?>" name="chose" id="chose1"></td>
                            <td><?php echo $infoVal['title'];?></td>
                            <td><?php echo $infoVal['userName'];?></td>
                            <td><?php echo date("Y-m-d H:i:s",$infoVal['time']);?></td>
                            <td>
                                <?php if ($infoVal['type'] == 1):?>
                                    观看反馈
                                <?php else:?>
                                    意见反馈
                                <?php endif;?>
                            </td>
                            <td>
                                <span>
                                    <?php if ($infoVal['reply'] == 0):?>
                                        <a href="<?php echo get_url("/background/editfeedback/{$infoVal['id']}/")?>">详情</a>|
                                    <?php endif;?>
                                    <a class="del" val="<?php echo $infoVal['id'];?>" href="javascript:void(0);">删除</a>
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