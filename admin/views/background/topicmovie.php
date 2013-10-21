<div class="bs-docs-example">
    <div class="movieList">
        <div class="actionList">
            <ul>
                <?php if ($mtype == 1):;?>
                    <li type="1" class="btn btn-success">激活专题</li>
                <?php else:?>
                    <li type="1" class="btn btn-primary">激活系列</li>
                <?php endif;?>
                <li type="2" class="btn">把电影收入回收站</li>
            </ul>
        </div>
        <?php if (!empty($topicInfo)):?>
            <div class="add_to_topic">
                <div class="topic_list">
                    <?php $pArr = $params;?>
                    <?php unset($pArr['id']);?>
                    <?php unset($pArr['p']);?>
                    <select class="" name="topic" id="topic">
                        <option value="">请选择<?php echo ($mtype == 1) ? "专题" : "系列";?></option>
                        <?php foreach ($topicInfo as $topicVal):?>
                            <?php $pArr['id'] = $topicVal['id'];?>
                            <option <?php if ($params['id'] == $topicVal['id']):?>selected="selected" <?php endif;?> onclick="window.location.href='/topic/topicmovie?<?php echo http_build_query($pArr);?>'" value="<?php echo $topicVal['id'];?>">
                                <?php echo $topicVal['name'];?>
                                <?php if ($topicVal['status'] == 0):?>
                                    (未激活)
                                <?php else:?>
                                    (已激活)
                                <?php endif;?>
                            </option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
        <?php endif;?>

        <div class="add_to_topic">
            <div class="topic_list">
                <?php $pArr = $params;?>
                <?php unset($pArr['status']);?>
                <?php unset($pArr['p']);?>
                <select class="" name="topic" id="topic">
                    <option value="-1">电影状态</option>
                    <?php $pArr['status'] = 1;?>
                    <option <?php if ($params['status'] == 1):?>selected="selected" <?php endif;?> value="1" onclick="window.location.href='/topic/topicmovie?<?php echo http_build_query($pArr);?>'">
                        已激活电影
                    </option>
                    <?php $pArr['status'] = 0;?>
                    <option value="0" <?php if ($params['status'] == 0):?>selected="selected" <?php endif;?> onclick="window.location.href='/topic/topicmovie?<?php echo http_build_query($pArr);?>'">
                        未激活电影
                    </option>
                </select>
            </div>
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
                            <td>
                                <?php echo $infoVal['name'];?>
                                <?php if ($topicMovieInfo[$infoVal['id']]['status'] == 0):?>
                                    <font color="red">(未激活)</font>
                                <?php else:?>
                                    (已激活)
                                <?php endif;?>
                            </td>
                            <td>
                                <?php echo $infoVal['daoyan'];?>
                            </td>
                            <td>
                                <?php echo $infoVal['nianfen'];?>年
                            </td>
                            <td>
                                <?php echo $movieType[$infoVal['type']];?>
                            </td>
                            <td>
                                <?php echo $infoVal['shichang'];?>分
                            </td>
                            <td>
                                <?php echo $infoVal['zhuyan'];?>
                            </td>
                            <td>
                                <?php if ($infoVal['time1'] > time()):?>否<?php else:?>是<?php endif;?>
                            </td>
                            <td>
                                <span>
                                    <a target="_blank" href="/background/editmovie?id=<?php echo $infoVal['id'];?>">电影编辑</a>
                                </span>
                                <span>
                                    <a target="_blank" href="/topic/editmovie?id=<?php echo $topicMovieInfo[$infoVal['id']]['id'];?>">专题编辑</a>
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