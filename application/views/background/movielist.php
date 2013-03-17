<div class="movieList">
    <div class="actionList">
        <ul>
            <li type="1">更新最新上映</li>
            <li type="2">放入最新上映</li>
            <li type="3">更新即将上映</li>
            <li type="4">放入即将上映</li>
            <li type="5">标记为重温经典</li>
            <li type="6">收入回收站</li>
            <li type="7" class="desc">
                <select>
                    <option value="<?php echo $sort;?>"><?php echo $movieSortType[$sort]['title'];?></option>
                    <?php foreach($movieSortType as $movieSortTypeKey => $movieSortTypeVal):?>
                    <?php if ($movieSortTypeKey == $sort) {continue;}?>
                    <option value="<?php echo $movieSortTypeKey;?>"><?php echo $movieSortTypeVal['title'];?></option>
                    <?php endforeach;?>
                </select>
            </li>
        </ul>
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
            <?php if (!empty($movieList)):?>
            <?php $count = count($movieList);$i = 1;?>
            <?php foreach($movieList as $infoVal):?>
            <tr <?php if ($i == $count):?>class="lastOne"<?php elseif($i == 1):?>class="firstOne"<?php endif;?>>
                <td class="chose"><input type="checkbox" v="<?php echo $infoVal['id'];?>" name="chose" id="chose1"></td>
                <td><?php echo $infoVal['name'];?></td>
                <td><?php echo $infoVal['daoyan'];?></td>
                <td><?php echo $infoVal['nianfen'];?>年</td>
                <td><?php echo $movieType[$infoVal['type']];?></td>
                <td><?php echo $infoVal['shichang'];?>分</td>
                <td><?php echo $infoVal['zhuyan'];?></td>
                <td><?php if ($infoVal['time1'] > time()):?>否<?php else:?>是<?php endif;?></td>
                <td><span><a href="/index.php/background/editmovie?id=<?php echo $infoVal['id'];?>">编辑</a></span></td>
            </tr>
                <?php $i++;?>
            <?php endforeach;?>
            <?php endif;?>
        </table>
    </div>
</div>
<?php $this->load->view("component/page",array("fenye" => $fenye));?>
