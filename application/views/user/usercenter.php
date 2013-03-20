<div class="row">
    <?php $this->load->view("component/usercenterleft",array("userInfo" =>$userInfo,"index"=>1));?>
    <div class="right_container">
        <div class="main-tab">
            <a <?php if ($type == "new"):?>class="tab-focus"<?php endif;?> href="<?php echo get_url("/usercenter/index/new/")?>">最新上映</a>
            <a <?php if ($type == "up"):?>class="tab-focus"<?php endif;?> href="<?php echo get_url("/usercenter/index/up/")?>">即将上映</a>
            <a <?php if ($type == "hot"):?>class="tab-focus"<?php endif;?> href="<?php echo get_url("/usercenter/index/hot/")?>">最近热评</a>
        </div>
        <div id="usermain">
            <div class="show mod-dist-r">
                <div class="modbox2">
                    <table class="table table-bordered">
                        <tr>
                            <th>电影名</th>
                            <th>主演</th>
                            <th>导演</th>
                            <th>年份</th>
                            <th>地区</th>
                            <th>类型</th>
                            <?php if ($type == "hot"):?>
                                <th>评论数</th>
                            <?php else:?>
                            <th>时长</th>
                            <?php endif;?>
                        </tr>
                        <?php foreach($movieList as $movieVal):?>
                            <tr>
                                <td class="name"><a href="<?php echo get_url("/detail/index/{$movieVal['id']}/")?>"><?php echo $movieVal['name'];?></a></td>
                                <td><?php echo $movieVal['zhuyan'];?></td>
                                <td class="daoyan"><?php echo $movieVal['daoyan'];?></td>
                                <td class="nianfen"><?php echo date("Y",strtotime($movieVal['nianfen']));?>年</td>
                                <td class="diqu"><?php echo $moviePlace[$movieVal['diqu']];?></td>
                                <td class="type"><?php echo $movieType[$movieVal['type']];?>片</td>
                                <?php if ($type == "hot"):?>
                                    <td class="shichang"><?php echo $hotInfos[$movieVal['id']]['cn'];?>条</td>
                                <?php else:?>
                                    <td class="shichang"><?php echo $movieVal['shichang'];?>分</td>
                                <?php endif;?>
                            </tr>
                        <?php endforeach;?>
                    </table>
                    <?php if (!empty($more_url)):?>
                    <a href="<?php echo $more_url;?>" class="more">更多>></a>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>

    <div class="span9">
        <div class="bs-docs-example">
            <div class="user_image">
                <img src="<?php echo $userInfo['photo'];?>">
                <div class="doing"></div>
                <span class="btn shangchuan">上传头像</span>
                <span class="btn upload">上传</span>
                <span class="btn cancel">取消</span>
                <form name="userphone" id="userphone" method="post" action="<?php echo rtrim(get_url(get_config_value("image_upload_url")),"/") . "/index/{$userId}/user";?>" autocomplete="off" enctype="multipart/form-data">
                <input type="file" name="image" id="image">
                <input type="hidden" name="moren_img" id="moren_img" value="<?php echo $userInfo['photo'];?>">
                <input type="hidden" name="userphoto" id="userphoto" value="">
                <iframe name="upload_frame" id="upload_frame" style="width:0;height:0;display:none;" ></iframe>
                <input type="submit" name="submit" id="submit" style="display: none;">
                </form>
            </div>
        </div>
    </div>
</div>