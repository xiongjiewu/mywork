<div class="bs-docs-example">
    <div class="movieList">
        <div class="actionList">
            <ul>
                <li>电影筛选：
                    <select name="type" id="type">
                        <option value="all" <?php if ($sort == "all"):?>selected="selected" <?php endif;?>>全部</option>
                        <option value="top" <?php if ($sort == "top"):?>selected="selected" <?php endif;?>>top全部</option>
                        <?php foreach($webInfo as $webKey => $webVal):?>
                            <option value="<?php echo $webKey;?>_all" <?php if ($sort == $webKey . "_all"):?>selected="selected" <?php endif;?>><?php echo $webVal['name'];?>全部</option>
                            <option value="<?php echo $webKey;?>_later" <?php if ($sort == $webKey . "_later"):?>selected="selected" <?php endif;?>><?php echo $webVal['name'];?>已经上映</option>
                            <option value="<?php echo $webKey;?>_comming" <?php if ($sort == $webKey . "_comming"):?>selected="selected" <?php endif;?>><?php echo $webVal['name'];?>即将上映</option>
                            <option value="<?php echo $webKey;?>_top" <?php if ($sort == $webKey . "_top"):?>selected="selected" <?php endif;?>><?php echo $webVal['name'];?>top电影</option>
                        <?php endforeach;?>
                    </select>
                </li>
                <li>
                    <div class="searchUser">
                        <form method="get" action="">
                            <input type="text" class="dyname" value="<?php if (isset($dyname)):?><?php echo $dyname;?><?php endif;?>" name="dyname" id="dyname">
                            <input type="submit" class="submit" value="搜索">
                        </form>
                    </div>
                </li>
                <li <?php if (!empty($webInfo[$grabInfoArr['name']])):?>style="display: none" <?php endif;?>>
                    抓取电影：
                    <select name="grabtype" id="grabtype">
                        <?php foreach($webInfo as $webKey => $webVal):?>
                            <?php foreach($webVal as $wKey => $wV):?>
                                <?php if ($wKey == "name" || $wKey == "type" || empty($wV)){continue;}?>
                                <option value="<?php echo $webKey;?>_<?php echo $wKey;?>"><?php echo $webVal['name'];?><?php echo $wKey;?>电影</option>
                            <?php endforeach;?>
                        <?php endforeach;?>
                    </select>
                    <input type="button" name="grabbutton" id="grabbutton" value="执行">
                </li>
                <li class="grab_info_text" <?php if (empty($webInfo[$grabInfoArr['name']])):?>style="display: none" <?php endif;?>>
                    <?php if (!empty($webInfo[$grabInfoArr['name']])):?>
                        正在抓取
                        <?php echo $webInfo[$grabInfoArr['name']]['name'];?>
                            <?php echo $grabInfoArr['urlType'];?>
                        电影信息中,请稍候...
                    <?php endif;?>
                </li>
            </ul>
            <ul class="movice_aciton">
                <li type="1" class="btn btn-success">放入电影列表</li>
                <li type="2" class="btn btn-primary">选择资料完善的电影</li>
                <li type="3" class="btn btn-info">选择资料未完善的电影</li>
                <li type="-1" class="btn btn">删除选择</li>
                <li type="0" class="btn btn">取消选择</li>
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
                    <th>资料</th>
                    <th>操作</th>
                </tr>
                <?php if (!empty($movieList)):?>
                    <?php $count = count($movieList);$i = 1;?>
                    <?php foreach($movieList as $infoVal):?>
                        <tr <?php if ($i == $count):?>class="lastOne"<?php elseif($i == 1):?>class="firstOne"<?php endif;?>>
                            <td class="chose">
                                <input type="checkbox" ch="<?php if ($infoVal['dataCheck']):?>1<?php else:?>0<?php endif;?>" v="<?php echo $infoVal['id'];?>" name="chose" id="chose1">
                            </td>
                            <td class="dy_name"><?php echo $infoVal['name'];?></td>
                            <td><?php echo $infoVal['daoyan'];?></td>
                            <td><?php echo $infoVal['nianfen'];?>年</td>
                            <td><?php echo $movieType[$infoVal['type']];?></td>
                            <td><?php echo $infoVal['shichang'];?>分</td>
                            <td class="zhuyan_info"><?php echo $infoVal['zhuyan'];?></td>
                            <td><?php if ($infoVal['time1'] > time()):?>否<?php else:?>是<?php endif;?></td>
                            <td>
                                <?php if ($infoVal['dataCheck']):?>
                                    <i class="yes">完善</i>
                                <?php else:?>
                                    <i class="no">未完善</i>
                                <?php endif;?>
                            </td>
                            <td>
                                <span>
                                    <a href="/background/editgrabmovice?id=<?php echo $infoVal['id'];?>">编辑</a>
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