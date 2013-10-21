<form method="post" action="" name="usergive" id="usergive">
    <input type="hidden" name="type" id="type" value="<?php echo $info['type'];?>">
<div class="bs-docs-example">
    <?php if (!empty($error)):?>
    <div class="error" style="color: red">
        <?php echo $error;?>
    </div>
    <?php endif;?>
    <table class="table">
        <?php if ($info['type'] == 1):?>
        <tr>
            <td style="border: none">观看链接</td>
            <td style="border: none">
                <input type="text" name="link" id="link" value="<?php echo $info['link'];?>">
            </td>
        </tr>
        <tr>
            <td>播放器</td>
            <td>
                <select name="watchType">
                    <?php foreach($bofangqiType as $key=>$val):?>
                        <option value="<?php echo $key;?>"><?php echo $val?></option>
                    <?php endforeach;?>
                </select>
            </td>
        </tr>
        <tr>
            <td>清晰程度</td>
            <td>
                <select name="qingxi">
                    <?php foreach($qingxiType as $key=>$val):?>
                        <option value="<?php echo $key;?>"><?php echo $val?></option>
                    <?php endforeach;?>
                </select>
            </td>
        </tr>
        <tr>
            <td>收费</td>
            <td>
                <select name="shoufei">
                    <?php foreach($shoufeiType as $key=>$val):?>
                        <option value="<?php echo $key;?>"><?php echo $val?></option>
                    <?php endforeach;?>
                </select>
            </td>
        </tr>
        <?php else:?>
            <tr>
                <td style="border: none">下载链接</td>
                <td style="border: none">
                    <input type="text" name="link" id="link" value="<?php echo $info['link'];?>">
                </td>
            </tr>
            <tr>
                <td>下载方式</td>
                <td>
                    <select name="downloadType">
                        <?php foreach($downLoadType as $key=>$val):?>
                            <option value="<?php echo $key;?>"><?php echo $val?></option>
                        <?php endforeach;?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>大小</td>
                <td>
                    <input type="text" name="size" id="size">M
                </td>
            </tr>
        <?php endif;?>
        <tr>
           <td colspan="2">
               <input type="submit" value="提交">
           </td>
        </tr>
    </table>
</div>
</form>