<div class="bs-docs-example">
    <?php if (!empty($error)): ?>
        <div class="error">
            对不起，<?php echo $error;?>!
        </div>
    <?php elseif (!empty($success)): ?>
        <div class="success">
            创建成功![<a href="<?php echo get_url("/detail/index/{$success}"); ?>" target="_blank">访问</a>]或[<a
                href="<?php echo get_url("background/editmovie?id={$success}"); ?>">编辑</a>]
        </div>
    <?php endif;?>
    <form name="create_topic" id="create_topic" method="post" action="/topic/edit_topic_do"
          autocomplete="off" enctype="multipart/form-data">
        <input type="hidden" name="id" id="id" value="<?php echo $topicInfo['id'];?>">
        <input type="hidden" name="sImg" id="sImg" value="<?php echo $topicInfo['sImg'];?>">
        <input type="hidden" name="mImg" id="mImg" value="<?php echo $topicInfo['mImg'];?>">
        <input type="hidden" name="bImg" id="bImg" value="<?php echo $topicInfo['bImg'];?>">

        <div class="topic_create">
            <table>
                <tr>
                    <td>名称：</td>
                    <td><input type="text" name="name" id="name" value="<?php echo $topicInfo['name'];?>"></td>
                </tr>
                <tr>
                    <td>类型：</td>
                    <td>
                        <select id="type" name="type">
                            <option value="0">请选择类型</option>
                            <?php foreach($movieType as $typeKey => $typeVal):?>
                                <option <?php if ($topicInfo['type'] == $typeKey):?>selected="selected" <?php endif;?> value="<?php echo $typeKey;?>"><?php echo $typeVal;?></option>
                            <?php endforeach;?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>专题类型：</td>
                    <td>
                        <select id="topicType" name="topicType">
                            <?php foreach($zhuantiType as $topicTypeKey => $topicTypeVal):?>
                                <option <?php if ($topicInfo['topicType'] == $topicTypeKey):?>selected="selected" <?php endif;?> value="<?php echo $topicTypeKey;?>"><?php echo $topicTypeVal;?></option>
                            <?php endforeach;?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>地区：</td>
                    <td>
                        <select id="diqu" name="diqu">
                            <option value="0">请选择地区</option>
                            <?php foreach($moviePlace as $diquKey => $diquVal):?>
                                <option <?php if ($topicInfo['diqu'] == $diquKey):?>selected="selected" <?php endif;?> value="<?php echo $diquKey;?>"><?php echo $diquVal;?></option>
                            <?php endforeach;?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>小标题：</td>
                    <td><input type="text" name="sTitle" id="sTitle" value="<?php echo $topicInfo['sTitle'];?>"></td>
                </tr>
                <tr>
                    <td>大标题：</td>
                    <td class="bTitle">
                        <textarea name="bTitle" id="bTitle"><?php echo $topicInfo['bTitle'];?></textarea>
                    </td>
                </tr>
                <tr>
                    <td>小图：</td>
                    <td>
                        <div class="sImg">
                            <img src="<?php echo APF::get_instance()->get_config_value("img_base_url") . $topicInfo['sImg'];?>">
                        </div>
                        <input type="file" name="sImgFile" id="sImgFile" v="sImg">
                    </td>
                </tr>
                <tr>
                    <td>中图：</td>
                    <td>
                        <div class="mImg">
                            <img src="<?php echo APF::get_instance()->get_config_value("img_base_url") . $topicInfo['mImg'];?>">
                        </div>
                        <input type="file" name="mImgFile" id="mImgFile" v="mImg">
                    </td>
                </tr>
                <tr>
                    <td>背景图：</td>
                    <td>
                        <div class="bImg">
                            <img src="<?php echo APF::get_instance()->get_config_value("img_base_url") . $topicInfo['bImg'];?>">
                        </div>
                        <input type="file" name="bImgFile" id="bImgFile" v="bImg">
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="create">
                        <input type="submit" id="submit" value="编辑">
                    </td>
                </tr>
            </table>
        </div>
    </form>
</div>
<input type="hidden" name="upload_url" id="upload_url"
       value="/uploadimage/index/<?php echo time();?>/dy/">
<iframe name="upload_frame" id="upload_frame" style="width:0;height:0;display:none;"></iframe>
<input type="hidden" name="clickVal" id="clickVal" value="1">
<input type="hidden" name="currentImgId" id="currentImgId" value="">
<script type="text/javascript">
</script>