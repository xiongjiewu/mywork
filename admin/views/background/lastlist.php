<input type="hidden" value="<?php echo $moviecount?>" name="moviecount" id="moviecount">
<div class="bs-docs-example">
    <div class="movieList">
        <div class="actionList">
            <ul>
                <li type="<?php echo $type;?>" class="btn btn-success">删除</li>
                <li type="0" class="btn btn-primary">删除缓存</li>
                <?php if (isset($dyname)):?>
                <li><a href="/background/lastlist/<?php echo $type;?>/">返回列表</a></li>
                <?php endif;?>
            </ul>
        </div>
        <div class="searchUser">
            <form method="get" action="">
                <input type="text" class="dyname" value="<?php if (isset($dyname)):?><?php echo $dyname;?><?php endif;?>" name="dyname" id="dyname">
                <input type="submit" class="submit" value="搜索">
            </form>
        </div>
        <div class="listTable">
            <table cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <th class=""><span class="choseAll">选项</span></th>
                    <th>名称</th>
                    <th>导演</th>
                    <th>年份</th>
                    <th>类型</th>
                    <th>时长</th>
                    <th>主演</th>
                    <th>上映</th>
                    <th>操作</th>
                </tr>
                <?php if (!empty($movieList)):?>
                    <?php $count = count($movieList);$i = 1;?>
                    <?php foreach($movieList as $infoVal):?>
                        <tr <?php if (!empty($infoVal['disabled'])):?>style="display: none;" <?php endif;?> <?php if ($i == $count):?>class="lastOne"<?php elseif($i == 1):?>class="firstOne"<?php endif;?>>
                            <td class="chose"><input type="checkbox" v="<?php echo $infoVal['id'];?>" name="chose" id="chose1"></td>
                            <td><?php echo $infoVal['name'];?></td>
                            <td><?php echo $infoVal['daoyan'];?></td>
                            <td><?php echo $infoVal['nianfen'];?>年</td>
                            <td><?php echo $movieType[$infoVal['type']];?></td>
                            <td><?php echo $infoVal['shichang'];?>分</td>
                            <td><?php echo $infoVal['zhuyan'];?></td>
                            <td><?php if ($infoVal['time1'] > time()):?>否<?php else:?>是<?php endif;?></td>
                            <td><span><a target="_blank" href="/background/editmovie?id=<?php echo $infoVal['id'];?>">编辑</a></span></td>
                        </tr>
                        <?php $i++;?>
                    <?php endforeach;?>
                <?php endif;?>
            </table>
        </div>
    </div>
</div>