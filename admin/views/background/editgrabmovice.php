<div class="bs-docs-example">
    <div class="upmovie">
        <?php if (!empty($error)): ?>
            <div class="error">
                对不起，<?php echo $error;?>!
            </div>
        <?php elseif (!empty($success)): ?>
            <div class="success">
                编辑成功!
            </div>
        <?php endif;?>
        <form name="upmovie" id="upmovie" method="post" action="<?php echo get_url("/background/updategrabmoviceinfo/"); ?>"
              autocomplete="off" autocomplete="off" enctype="multipart/form-data">
            <input type="hidden" name="return" id="return"
                   value="<?php echo get_url(APF::get_instance()->get_config_value("image_upload_return")); ?>">
            <input type="hidden" name="upload_url" id="upload_url"
                   value="<?php echo get_url(APF::get_instance()->get_config_value("image_upload_url")); ?>">
            <input type="hidden" name="image_url" id="image_url" value="<?php echo $dyInfo['image']; ?>">
            <input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
            <input type="hidden" name="webId" id="webId" value="<?php echo $dyInfo['webId']; ?>">
            <input type="hidden" name="webType" id="webType" value="<?php echo $dyInfo['webType']; ?>">

            <table>
                <tr class="movie_name">
                    <td>名&nbsp;&nbsp;&nbsp;称：</td>
                    <td><input type="text" name="name" id="name"
                               value="<?php echo $dyInfo['name']; ?>">&nbsp;<code>*</code></td>
                </tr>
                <tr class="movie_daoyan">
                    <td>导&nbsp;&nbsp;&nbsp;演：</td>
                    <td><input type="text" name="daoyan" id="daoyan"
                               value="<?php echo $dyInfo['daoyan']; ?>">&nbsp;<code>*</code></td>
                </tr>
                <tr class="movie_nianfen">
                    <td>年&nbsp;&nbsp;&nbsp;份：</td>
                    <td><input type="text" class="Wdate" readonly="true" onclick="WdatePicker()" name="nianfen"
                               id="nianfen" value="<?php echo $dyInfo['nianfen']; ?>">&nbsp;年&nbsp;<code>*</code></td>
                </tr>
                <tr class="movie_diqu">
                    <td>类&nbsp;&nbsp;&nbsp;型：</td>
                    <td>
                        <select name="type">
                            <option
                                value="<?php echo $dyInfo['type']; ?>"><?php echo $movieType[$dyInfo['type']];?></option>
                            <?php foreach ($movieType as $typeKey => $typeVal): ?>
                                <?php if ($typeKey == $dyInfo['type']) {
                                    continue;
                                } ?>
                                <option value="<?php echo $typeKey; ?>"><?php echo $typeVal;?></option>
                            <?php endforeach;?>
                        </select>&nbsp;<code>*</code>
                    </td>
                </tr>
                <tr class="movie_diqu">
                    <td>地&nbsp;&nbsp;&nbsp;区：</td>
                    <td>
                        <select name="diqu">
                            <option
                                value="<?php echo $dyInfo['diqu']; ?>"><?php echo $moviePlace[$dyInfo['diqu']];?></option>
                            <?php foreach ($moviePlace as $diquKey => $diquVal): ?>
                                <?php if ($diquKey == $dyInfo['diqu']) {
                                    continue;
                                } ?>
                                <option value="<?php echo $diquKey; ?>"><?php echo $diquVal;?></option>
                            <?php endforeach;?>
                        </select>&nbsp;<code>*</code>
                    </td>
                </tr>
                <tr class="movie_shichang">
                    <td>时&nbsp;&nbsp;&nbsp;长：</td>
                    <td><input type="text" name="shichang" id="shichang" value="<?php echo $dyInfo['shichang']; ?>">&nbsp;&nbsp;分&nbsp;<code>*</code>
                    </td>
                </tr>
                <tr class="movie_zhuyan">
                    <td>主&nbsp;&nbsp;&nbsp;演：</td>
                    <td><input type="text" name="zhuyan" id="zhuyan" value="<?php echo $dyInfo['zhuyan']; ?>">&nbsp;&nbsp;以分号隔开&nbsp;<code>*</code>
                    </td>
                </tr>
                <tr class="movie_jianjie">
                    <td>简&nbsp;&nbsp;&nbsp;介：</td>
                    <td>
                        <textarea name="jieshao"
                                  id="jieshao"><?php echo $dyInfo['jieshao'];?></textarea>&nbsp;<code>*</code>
                    </td>
                </tr>
                <tr class="movie_tupian">
                    <td>图&nbsp;&nbsp;&nbsp;片：</td>
                    <td>
                        <div class="image">
                            <img
                                src="<?php echo trim(APF::get_instance()->get_config_value("img_base_url"), "/") . $dyInfo['image']; ?>">
                            <br>
                            <span class="uploadAgain">重新上传</span>
                        </div>
                        <div class="upimage" style=" display: none;">
                            <input type="file" name="image" id="image">
                            &nbsp;<code>*</code>&nbsp;&nbsp;最大2M，1024*768
                        </div>
                    </td>
                </tr>
                <tr class="movie_time0">
                    <td>本&nbsp;&nbsp;&nbsp;上：</td>
                    <td><input type="text" name="time0" id="time0" class="Wdate" readonly="true" onclick="WdatePicker()"
                               value="<?php echo !empty($dyInfo['time0']) ? date("Ymd", $dyInfo['time0']) : ""; ?>">&nbsp;本站能提供观看链接时间
                    </td>
                </tr>
                <tr class="movie_time">
                    <td>国&nbsp;&nbsp;&nbsp;上：</td>
                    <td><input type="text" name="time1" id="time1" class="Wdate" readonly="true" onclick="WdatePicker()"
                               value="<?php echo !empty($dyInfo['time1']) ? date("Ymd", $dyInfo['time1']) : ""; ?>">&nbsp;国内上映时间
                    </td>
                </tr>
                <tr class="movie_time">
                    <td>港&nbsp;&nbsp;&nbsp;上：</td>
                    <td><input type="text" name="time2" id="time2" class="Wdate" readonly="true" onclick="WdatePicker()"
                               value="<?php echo !empty($dyInfo['time2']) ? date("Ymd", $dyInfo['time2']) : ""; ?>">&nbsp;港台上映时间
                    </td>
                </tr>
                <tr class="movie_time">
                    <td>欧&nbsp;&nbsp;&nbsp;上：</td>
                    <td><input type="text" name="time3" id="time3" class="Wdate" readonly="true" onclick="WdatePicker()"
                               value="<?php echo !empty($dyInfo['time3']) ? date("Ymd", $dyInfo['time3']) : ""; ?>">&nbsp;欧美上映时间
                    </td>
                </tr>
                <tr>
                    <td class="up">&nbsp;</td>
                    <td>
                        <input type="submit" value="编辑" name="submit" id="submit">
                        <input type="button" value="放入电影列表" name="in_list" id="in_list">
                        <input type="reset" value="重置">
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <iframe name="upload_frame" id="upload_frame" style="width:0;height:0;display:none;"></iframe>
    <input type="hidden" name="clickVal" id="clickVal" value="1">
</div>