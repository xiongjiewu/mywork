<div class="row">
    <?php $this->load->view("component/usercenterleft",array("userInfo" =>$userInfo,"index"=>2));?>
    <div class="right_container">
        <div class="main-tab">
            <a class="tab-focus" href="<?php echo get_url("/usercenter/mycollect/")?>">我的收藏</a>
        </div>
        <div id="usermain">
            <div class="show mod-dist-r">
                <div class="modbox2">
                    <table class="table">
                        <tr>
                            <th><?php if (!empty($movieList)):?><span class="chose_all">全选</span><?php else:?>全选<?php endif;?></th>
                            <th>电影名</th>
                            <th>主演</th>
                            <th>导演</th>
                            <th>年份</th>
                            <th>地区</th>
                            <th>类型</th>
                            <th>时长</th>
                            <th>操作</th>
                        </tr>
                        <?php if (!empty($movieList)):?>
                        <?php foreach($movieList as $movieVal):?>
                            <tr>
                                <?php $idStr = APF::get_instance()->encodeId($movieVal['id']);?>
                                <td class="chose"><input type="checkbox" name="ids[]" value="<?php echo $shouCangInfo[$movieVal['id']]['id'];?>"></td>
                                <td class="name"><a href="<?php echo get_url("/detail/index/{$idStr}/")?>"><?php echo $movieVal['name'];?></a></td>
                                <td><?php echo $movieVal['zhuyan'];?></td>
                                <td class="daoyan"><?php echo $movieVal['daoyan'];?></td>
                                <td class="nianfen"><?php echo date("Y",strtotime($movieVal['nianfen']));?>年</td>
                                <td class="diqu"><?php echo $moviePlace[$movieVal['diqu']];?></td>
                                <td class="type"><?php echo $movieType[$movieVal['type']];?>片</td>
                                <td class="shichang"><?php echo $movieVal['shichang'];?>分</td>
                                <td class="action"><span v="<?php echo $shouCangInfo[$movieVal['id']]['id'];?>">删除</span></td>
                            </tr>
                        <?php endforeach;?>
                        <?php else:?>
                            <tr><td colspan="9">目前，您还没有收藏任何影片，您可以去<a href="<?php echo get_url("/latestmovie/");?>">最新上映</a>列表看看</td></tr>
                        <?php endif;?>
                    </table>
                </div>
                <?php if (!empty($movieList)):?>
                    <a href="javascript:void(0);" class="btn btn-info">批量删除</a>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>