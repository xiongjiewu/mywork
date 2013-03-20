<div class="row">
    <?php $this->load->view("component/usercenterleft",array("userInfo" =>$userInfo,"index"=>2));?>
    <div class="right_container">
        <div class="main-tab">
            <a class="tab-focus" href="<?php echo get_url("/usercenter/mycollect/")?>">我的收藏</a>
        </div>
        <div id="usermain">
            <div class="show mod-dist-r">
                <div class="modbox2">
                    <table class="table table-bordered">
                        <tr>
                            <th><span class="chose_all">全选</span></th>
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
                                <td class="chose"><input type="checkbox" name="ids[]" value="<?php echo $movieVal['id'];?>"></td>
                                <td class="name"><a href="<?php echo get_url("/detail/index/{$movieVal['id']}/")?>"><?php echo $movieVal['name'];?></a></td>
                                <td><?php echo $movieVal['zhuyan'];?></td>
                                <td class="daoyan"><?php echo $movieVal['daoyan'];?></td>
                                <td class="nianfen"><?php echo date("Y",strtotime($movieVal['nianfen']));?>年</td>
                                <td class="diqu"><?php echo $moviePlace[$movieVal['diqu']];?></td>
                                <td class="type"><?php echo $movieType[$movieVal['type']];?>片</td>
                                <td class="shichang"><?php echo $movieVal['shichang'];?>分</td>
                                <td class="action"><span v="<?php echo $movieVal['id'];?>">删除</span></td>
                            </tr>
                        <?php endforeach;?>
                        <?php endif;?>
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