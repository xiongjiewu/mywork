<div class="bs-docs-example">
    <div class="movieList">
        <div class="actionList">
            <ul>
                <li type="3" class="btn btn-success">删除<?php echo ($type == 1) ? "专题" : "系列";?></li>
            </ul>
        </div>
        <div class="searchUser">
            <form method="get" action="/topic/topiclist">
                <input type="hidden" value="<?php echo $type;?>" name="topicType">
                <input type="text" class="dyname" value="<?php if (isset($dyname)):?><?php echo $dyname;?><?php endif;?>" name="dyname" id="dyname">
                <input type="submit" class="submit" value="搜索">
            </form>
        </div>
        <div class="listTable">
            <table cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <th class="chose"><span class="choseAll">全选</span></th>
                    <th>名称</th>
                    <th>小标题</th>
                    <th>大标题</th>
                    <th>操作</th>
                </tr>
                <?php if (!empty($topicListInfo)):?>
                    <?php $count = count($topicListInfo);$i = 1;?>
                    <?php foreach($topicListInfo as $infoVal):?>
                        <tr <?php if ($i == $count):?>class="lastOne"<?php elseif($i == 1):?>class="firstOne"<?php endif;?>>
                            <td class="chose"><input type="checkbox" v="<?php echo $infoVal['id'];?>" name="chose" id="chose1"></td>
                            <td>
                                <a href="/background/movielist/?topicType=<?php echo $infoVal['topicType'];?>&topicId=<?php echo $infoVal['id'];?>"><?php echo $infoVal['name'];?></a>
                            </td>
                            <td><?php echo $infoVal['sTitle'];?></td>
                            <td><?php echo $infoVal['bTitle'];?></td>
                            <td>
                                <span>
                                    <a target="_blank" href="<?php echo APF::get_instance()->get_config_value("my_base_domain");?>/series/info/<?php echo APF::get_instance()->encodeId($infoVal['id']) . "?status=" . ($infoVal['status'] == 0 ? -1 : $infoVal['status']);?>">预览</a>
                                </span>
                                <span>
                                    <a href="/topic/edittopic?id=<?php echo $infoVal['id'];?>">编辑</a>
                                </span>
                            </td>
                        </tr>
                        <?php $i++;?>
                    <?php endforeach;?>
                <?php endif;?>
            </table>
        </div>
    </div>
    <?php $this->load->view("component/pagenew",array("fenye" => $fenye));?>
</div>