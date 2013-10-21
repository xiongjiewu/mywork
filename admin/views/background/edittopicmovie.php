<div class="bs-docs-example">
    <?php if (!empty($error)): ?>
        <div class="error">
            对不起，<?php echo $error;?>!
        </div>
    <?php elseif (!empty($success)): ?>
        <div class="success">
            编辑成功![<a href="<?php echo get_url("/detail/index/{$success}"); ?>" target="_blank">访问</a>]或[<a
                href="<?php echo get_url("background/editmovie?id={$success}"); ?>">编辑</a>]
        </div>
    <?php endif;?>
    <form name="create_topic" id="create_topic" method="post" action="/topic/edit_topicmovie_do/"
          autocomplete="off" enctype="multipart/form-data">
        <input type="hidden" id="id" name="id" value="<?php echo $id;?>">
        <input type="hidden" name="image" id="image" value="<?php echo $movieInfo['image'];?>">
        <input type="hidden" name="image_len" id="image_len" value="<?php echo count($movieImgInfo);?>">
        <?php if (empty($movieImgInfo)):?>
            <input type="hidden" name="image_add" id="image_add" value="">
        <?php else:?>
            <?php $imgStr = "";?>
            <?php foreach($movieImgInfo as $imgKey => $imgVal):?>
                <?php $imgStr .= "file_" . ($imgKey + 1) . ":" . $imgVal['image'] . ";";?>
            <?php endforeach;?>
            <input type="hidden" name="image_add" id="image_add" value="<?php echo $imgStr;?>">
        <?php endif;?>

        <div class="topic_create">
            <table>
                <tr>
                    <td>名称：</td>
                    <td><input type="text" name="name" id="name" value="<?php echo $movieInfo['name'];?>"></td>
                </tr>
                <tr>
                    <td>小标题：</td>
                    <td><input type="text" name="sTitle" id="sTitle" value="<?php echo $movieInfo['sTitle'];?>"></td>
                </tr>
                <tr>
                    <td>大标题：</td>
                    <td class="bTitle">
                        <textarea name="bTitle" id="bTitle"><?php echo $movieInfo['bTitle'];?></textarea>
                    </td>
                </tr>
                <tr>
                    <td>剧照：</td>
                    <td>
                        <div class="sImg">
                            <img src="<?php echo APF::get_instance()->get_config_value("img_base_url") . $movieInfo['image'];?>">
                        </div>
                        <input type="file" name="sImgFile" id="sImgFile" v="image">
                    </td>
                </tr>
                <?php if (!empty($movieImgInfo)):?>
                    <?php foreach($movieImgInfo as $imgKey => $imgVal):?>
                        <tr>
                            <td>剧照<?php echo ($imgKey + 1);?>：</td>
                            <td>
                                <div class="sImg juzhao">
                                    <img src="<?php echo APF::get_instance()->get_config_value("img_base_url") . $imgVal['image'];?>">
                                </div>
                                <input id="file_<?php echo ($imgKey + 1);?>" value="<?php echo $imgVal['image'];?>" type="file" v="image_add" name="file_<?php echo ($imgKey + 1);?>">
                                标题：
                                <input id="text_<?php echo ($imgKey + 1);?>" type="text" v="text_add" value="<?php echo $imgVal['title'];?>" name="text_<?php echo ($imgKey + 1);?>">
                                <input class="remove_juzhao" type="button" value="删除">
                            </td>
                        </tr>
                    <?php endforeach;?>
                <?php endif;?>

                <tr class="add_juzhao_to">
                    <td class="" colspan="2">
                        <div class="add_juzhao">
                            增加剧照
                        </div>
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