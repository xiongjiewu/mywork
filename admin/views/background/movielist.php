<div class="bs-docs-example">
<div class="movieList">
    <div class="actionList">
        <ul>
            <li type="1" class="btn btn-success">更新最新上映</li>
            <li type="2" class="btn btn-primary">放入最新上映</li>
            <li type="3" class="btn btn-info">更新即将上映</li>
            <li type="4" class="btn btn-warning">放入即将上映</li>
            <li type="5" class="btn btn-primary">更新重温经典</li>
            <li type="6" class="btn btn-danger">放入重温经典</li>
            <li type="7" class="btn">收入回收站</li>
            <li type="8" class="desc">
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
    <div class="searchUser">
        <form method="get" action="">
            <input type="text" class="dyname" value="<?php if (isset($dyname)):?><?php echo $dyname;?><?php endif;?>" name="dyname" id="dyname">
            <input type="submit" class="submit" value="搜索">
        </form>
    </div>
    <?php if (!empty($topicInfo)):?>
        <div class="add_to_topic">
            <div class="topic_list">
                <select class="" name="topic" id="topic">
                    <option value="0">请选择专题</option>
                    <?php foreach ($topicInfo as $topicVal):?>
                        <option <?php if ($topicType == 2 && ($topicId == $topicVal['id'])):?>selected="selected" <?php endif;?> value="<?php echo $topicVal['id'];?>"><?php echo $topicVal['name'];?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="add_to_topic_botton" type="9">
                放入专题
            </div>
        </div>
    <?php endif;?>
    <?php if (!empty($xilieInfo)):?>
        <div class="add_to_topic">
            <div class="topic_list">
                <select class="" name="xilie" id="xilie">
                    <option value="0">请选择系列</option>
                    <?php foreach ($xilieInfo as $xilieVal):?>
                        <option <?php if ($topicType == 2 && ($topicId == $xilieVal['id'])):?>selected="selected" <?php endif;?> value="<?php echo $xilieVal['id'];?>"><?php echo $xilieVal['name'];?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="add_to_xilie_botton" type="10">
                放入系列
            </div>
        </div>
    <?php endif;?>
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
                <td>
                    <span>
                        <a href="/background/editmovie?id=<?php echo $infoVal['id'];?>">编辑</a>
                    </span>
                    <span>
                        <a href="/background/editmovie?id=<?php echo $infoVal['id'];?>&status=base">编辑基本信息</a>
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