<div class="bs-docs-example">
    <div class="movieList">
        <div class="actionList">
            <ul>
                <li type="8" class="btn btn-danger">恢复选择</li>
                <li type="9" class="btn btn-warning">彻底删除</li>
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
                    <th class="chose"><span class="choseAll">全选</span></th>
                    <th>名称</th>
                    <th>导演</th>
                    <th>年份</th>
                    <th>类型</th>
                    <th>时长</th>
                    <th>主演</th>
                    <th>上映</th>
                    <th>操作</th>
                </tr>
                <?php if (!empty($movieList)): ?>
                    <?php $count = count($movieList);
                    $i = 1; ?>
                    <?php foreach ($movieList as $infoVal): ?>
                        <tr <?php if ($i == $count && $i != 1): ?>class="lastOne"
                            <?php elseif ($i != $count && $i == 1): ?><?php else: ?>class="last"<?php endif;?>>
                            <td class="chose"><input type="checkbox" v="<?php echo $infoVal['id']; ?>" name="chose"
                                                     id="chose1"></td>
                            <td><?php echo $infoVal['name'];?></td>
                            <td><?php echo $infoVal['daoyan'];?></td>
                            <td><?php echo $infoVal['nianfen'];?>年</td>
                            <td><?php echo $movieType[$infoVal['type']];?></td>
                            <td><?php echo $infoVal['shichang'];?>分</td>
                            <td><?php echo $infoVal['zhuyan'];?></td>
                            <td><?php if ($infoVal['time1'] > time()): ?>否<?php else: ?>是<?php endif;?></td>
                            <td><span><a href="/background/editmovie?id=<?php echo $infoVal['id']; ?>">编辑</a></span>
                            </td>
                        </tr>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                <?php endif;?>
            </table>
        </div>
    </div>
    <?php $this->load->view("component/pagenew", array("fenye" => $fenye));?>
</div>