<div class="bs-docs-example">
<div class="upmovie">
    <?php if (!empty($error)):?>
    <div class="error">
        对不起，<?php echo $error;?>!
    </div>
    <?php elseif (!empty($success)):?>
    <div class="success">
        编辑成功![<a href="<?php echo get_config_value("my_base_domain") . get_url("/detail/index/{$success}/")?>" target="_blank">访问</a>]
    </div>
    <?php endif;?>
    <form name="upmovie" id="upmovie" method="post" action="<?php echo get_url("/background/updatedy/");?>" autocomplete="off" autocomplete="off" enctype="multipart/form-data">
        <input type="hidden" name="watchLink" id="watchLink" value="">
        <input type="hidden" name="status" id="status" value="<?php echo empty($status) ? 0 : 1;?>">
        <input type="hidden" name="return" id="return" value="<?php echo get_url(APF::get_instance()->get_config_value("image_upload_return"));?>">
        <input type="hidden" name="upload_url" id="upload_url" value="<?php echo get_url(APF::get_instance()->get_config_value("image_upload_url"));?>">
        <input type="hidden" name="downloadLink" id="downloadLink" value="">
        <input type="hidden" name="image_url" id="image_url" value="<?php echo $dyInfo['image'];?>">
        <input type="hidden" name="bofangqi" id="bofangqi" value="">
        <input type="hidden" name="qingxi" id="qingxi" value="">
        <input type="hidden" name="shoufei" id="shoufei" value="">
        <input type="hidden" name="size" id="size" value="">
        <input type="hidden" name="downloadType" id="downloadType" value="">
        <input type="hidden" name="id" id="id" value="<?php echo $id;?>">

        <table>
            <tr class="movie_name">
                <td>名&nbsp;&nbsp;&nbsp;称：</td>
                <td><input type="text" name="name" id="name" value="<?php echo $dyInfo['name'];?>">&nbsp;<code>*</code></td>
            </tr>
            <tr class="movie_daoyan">
                <td>导&nbsp;&nbsp;&nbsp;演：</td>
                <td><input type="text" name="daoyan" id="daoyan" value="<?php echo $dyInfo['daoyan'];?>">&nbsp;<code>*</code></td>
            </tr>
            <tr class="movie_nianfen">
                <td>年&nbsp;&nbsp;&nbsp;份：</td>
                <td><input type="text" class="Wdate" readonly="true" onclick="WdatePicker()" name="nianfen" id="nianfen" value="<?php echo $dyInfo['nianfen'];?>">&nbsp;年&nbsp;<code>*</code></td>
            </tr>
            <tr class="movie_diqu">
                <td>类&nbsp;&nbsp;&nbsp;型：</td>
                <td>
                    <select name="type">
                        <option value="<?php echo $dyInfo['type'];?>"><?php echo $movieType[$dyInfo['type']];?></option>
                        <?php foreach($movieType as $typeKey => $typeVal):?>
                            <?php if ($typeKey == $dyInfo['type']) {continue;}?>
                            <option value="<?php echo $typeKey;?>"><?php echo $typeVal;?></option>
                        <?php endforeach;?>
                    </select>&nbsp;<code>*</code>
                </td>
            </tr>
            <tr class="movie_diqu">
                <td>地&nbsp;&nbsp;&nbsp;区：</td>
                <td>
                    <select name="diqu">
                        <option value="<?php echo $dyInfo['diqu'];?>"><?php echo $moviePlace[$dyInfo['diqu']];?></option>
                        <?php foreach($moviePlace as $diquKey => $diquVal):?>
                        <?php if ($diquKey == $dyInfo['diqu']) {continue;}?>
                        <option value="<?php echo $diquKey;?>"><?php echo $diquVal;?></option>
                        <?php endforeach;?>
                    </select>&nbsp;<code>*</code>
                </td>
            </tr>
            <tr class="movie_shichang">
                <td>时&nbsp;&nbsp;&nbsp;长：</td>
                <td><input type="text" name="shichang" id="shichang" value="<?php echo $dyInfo['shichang'];?>">&nbsp;&nbsp;分&nbsp;<code>*</code></td>
            </tr>
            <tr class="movie_zhuyan">
                <td>主&nbsp;&nbsp;&nbsp;演：</td>
                <td><input type="text" name="zhuyan" id="zhuyan" value="<?php echo $dyInfo['zhuyan'];?>">&nbsp;&nbsp;以分号隔开&nbsp;<code>*</code></td>
            </tr>
            <tr class="movie_jianjie">
                <td>简&nbsp;&nbsp;&nbsp;介：</td>
                <td>
                    <textarea name="jieshao" id="jieshao"><?php echo $dyInfo['jieshao'];?></textarea>&nbsp;<code>*</code>
                </td>
            </tr>
            <tr class="movie_tupian">
                <td>图&nbsp;&nbsp;&nbsp;片：</td>
                <td>
                    <div class="image">
                        <img src="<?php echo trim(APF::get_instance()->get_config_value("img_base_url"),"/") . $dyInfo['image'];?>">
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
                <td><input type="text" name="time0" id="time0" class="Wdate" readonly="true" onclick="WdatePicker()" value="<?php echo !empty($dyInfo['time0'])? date("Ymd",$dyInfo['time0']) : "";?>">&nbsp;本站能提供观看链接时间</td>
            </tr>
            <tr class="movie_time">
                <td>国&nbsp;&nbsp;&nbsp;上：</td>
                <td><input type="text" name="time1" id="time1" class="Wdate" readonly="true" onclick="WdatePicker()" value="<?php echo !empty($dyInfo['time1']) ? date("Ymd",$dyInfo['time1']) : "";?>">&nbsp;国内上映时间</td>
            </tr>
            <tr class="movie_time">
                <td>港&nbsp;&nbsp;&nbsp;上：</td>
                <td><input type="text" name="time2" id="time2" class="Wdate" readonly="true" onclick="WdatePicker()" value="<?php echo !empty($dyInfo['time2']) ? date("Ymd",$dyInfo['time2']) : "";?>">&nbsp;港台上映时间</td>
            </tr>
            <tr class="movie_time">
                <td>欧&nbsp;&nbsp;&nbsp;上：</td>
                <td><input type="text" name="time3" id="time3" class="Wdate" readonly="true" onclick="WdatePicker()" value="<?php echo !empty($dyInfo['time3']) ? date("Ymd",$dyInfo['time3']) : "";?>">&nbsp;欧美上映时间</td>
            </tr>

            <?php if (empty($status)):?>
            <tr class="movie_link">
                <td valign="top">观&nbsp;&nbsp;&nbsp;链：</td>
                <td class="watchLink">
                    <?php if (empty($watchLinkInfo)):?>
                    <em>
                        <input type="text" class="link">
                        <select class="bofangqi">
                            <?php foreach($bofangqiType as $bofangqiKey => $bofangqiVal):?>
                                <option value="<?php echo $bofangqiKey;?>"><?php echo $bofangqiVal;?></option>
                            <?php endforeach;?>
                        </select>
                        <select class="qingxi">
                            <option value="1">一般</option>
                            <option value="2">标清</option>
                            <option value="3">高清</option>
                            <option value="4">超清</option>
                        </select>
                        <select class="shoufei">
                            <option value="1">免费</option>
                            <option value="2">收费</option>
                        </select>
                    </em>
                    <em>
                        <input type="text" class="link">
                        <select class="bofangqi">
                            <?php foreach($bofangqiType as $bofangqiKey => $bofangqiVal):?>
                                <option value="<?php echo $bofangqiKey;?>"><?php echo $bofangqiVal;?></option>
                            <?php endforeach;?>
                        </select>
                        <select class="qingxi">
                            <option value="1">一般</option>
                            <option value="2">标清</option>
                            <option value="3">高清</option>
                            <option value="4">超清</option>
                        </select>
                        <select class="shoufei">
                            <option value="1">免费</option>
                            <option value="2">收费</option>
                        </select>
                    </em>
                    <label type="1">删除</label>
                     <?php else:?>
                     <?php $watchLinkInfoCount = count($watchLinkInfo);?>
                    <?php foreach($watchLinkInfo as $watchLinkInfoKey => $watchLinkInfoVal):?>
                        <em>
                            <input type="text" class="link" value="<?php echo $watchLinkInfoVal['link'];?>">
                            <select class="bofangqi">
                                <option value="<?php echo $watchLinkInfoVal['player'];?>"><?php echo $bofangqiType[$watchLinkInfoVal['player']];?></option>
                                <?php foreach($bofangqiType as $bofangqiTypeKey => $bofangqiTypeVal):?>
                                <?php if ($bofangqiTypeKey == $watchLinkInfoVal['player']) {continue;}?>
                                <option value="<?php echo $bofangqiTypeKey;?>"><?php echo $bofangqiTypeVal;?></option>
                                <?php endforeach;?>
                            </select>
                            <select class="qingxi">
                                <option value="<?php echo $watchLinkInfoVal['qingxi'];?>"><?php echo $qingxiType[$watchLinkInfoVal['qingxi']];?></option>
                                <?php foreach($qingxiType as $qingxiTypeKey => $qingxiTypeVal):?>
                                <?php if ($qingxiTypeKey == $watchLinkInfoVal['qingxi']) {continue;}?>
                                <option value="<?php echo $qingxiTypeKey;?>"><?php echo $qingxiTypeVal;?></option>
                                <?php endforeach;?>
                            </select>
                            <select class="shoufei">
                                <option value="<?php echo $watchLinkInfoVal['shoufei'];?>"><?php echo $shoufeiType[$watchLinkInfoVal['shoufei']];?></option>
                                <?php foreach($shoufeiType as $shoufeiTypeKey => $shoufeiTypeVal):?>
                                <?php if ($shoufeiTypeKey == $watchLinkInfoVal['shoufei']) {continue;}?>
                                <option value="<?php echo $shoufeiTypeKey;?>"><?php echo $shoufeiTypeVal;?></option>
                                <?php endforeach;?>
                            </select>
                        </em>
                         <?php if ($watchLinkInfoKey > 0):?>
                            <label type="1">删除</label>
                         <?php endif;?>
                    <?php endforeach;?>
                    <?php if ($watchLinkInfoCount == 1):?>
                        <em>
                            <input type="text" class="link">
                            <select class="bofangqi">
                                <?php foreach($bofangqiType as $bofangqiKey => $bofangqiVal):?>
                                    <option value="<?php echo $bofangqiKey;?>"><?php echo $bofangqiVal;?></option>
                                <?php endforeach;?>
                            </select>
                            <select class="qingxi">
                                <option value="1">一般</option>
                                <option value="2">标清</option>
                                <option value="3">高清</option>
                                <option value="4">超清</option>
                            </select>
                            <select class="shoufei">
                                <option value="1">免费</option>
                                <option value="2">收费</option>
                            </select>
                        </em>
                        <label type="1">删除</label>
                    <?php endif;?>
                     <?php endif;?>
                    <span class="addwatchLink" type="1">+添加观看链接</span>
                </td>
            </tr>
            <tr class="movie_link">
                <td valign="top">下&nbsp;&nbsp;&nbsp;链：</td>
                <td class="downloadLink">
                    <?php if (empty($downLoadLinkInfo)):?>
                    <em>
                        <input type="text" class="link">&nbsp;大小：<input type="text" class="size" style="width: 60px">&nbsp;M
                        &nbsp;<select class="type">
                        <option value="1">迅雷</option>
                        <option value="2">快车</option>
                        <option value="3">电驴</option>
                        <option value="4">直接</option>
                    </select>
                    </em>
                    <em>
                        <input type="text" class="link">&nbsp;大小：<input type="text" class="size"  style="width: 60px">&nbsp;M
                        &nbsp;<select class="type">
                        <option value="1">迅雷</option>
                        <option value="2">快车</option>
                        <option value="3">电驴</option>
                        <option value="4">直接</option>
                    </select>
                    </em>
                    <label type="2">删除</label>
                    <?php else:?>
                    <?php foreach($downLoadLinkInfo as $downLoadLinkInfoKey => $downLoadLinkInfoVal):?>
                    <em>
                        <input type="text" class="link" value="<?php echo $downLoadLinkInfoVal['link'];?>">&nbsp;大小：<input type="text" value="<?php echo $downLoadLinkInfoVal['size'];?>" class="size" style="width: 60px">&nbsp;M
                        &nbsp;
                        <select class="type">
                        <option value="<?php echo $downLoadLinkInfoVal['type'];?>"><?php echo $downLoadType[$downLoadLinkInfoVal['type']];?></option>
                        <?php foreach($downLoadType as $downLoadTypeKey => $downLoadTypeVal):?>
                            <?php if ($downLoadTypeKey == $downLoadLinkInfoVal['type']) {continue;}?>
                            <option value="<?php echo $downLoadTypeKey;?>"><?php echo $downLoadTypeVal;?></option>
                        <?php endforeach;?>
                        </select>
                    </em>
                     <?php if ($downLoadLinkInfoKey > 0):?>
                            <label type="2">删除</label>
                     <?php endif;?>
                     <?php endforeach;?>
                    <?php if (count($downLoadLinkInfo) == 1):?>
                        <em>
                            <input type="text" class="link">&nbsp;大小：<input type="text" class="size"  style="width: 60px">&nbsp;M
                            &nbsp;<select class="type">
                            <option value="1">迅雷</option>
                            <option value="2">快车</option>
                            <option value="3">电驴</option>
                            <option value="4">直接</option>
                        </select>
                        </em>
                        <label type="2">删除</label>
                    <?php endif;?>
                    <?php endif;?>
                    <span class="adddownloadLink" type="2">+添加下载链接</span>
                </td>
            </tr>
            <?php endif;?>
            <tr>
            <td class="up">&nbsp;</td>
            <td>
                <input type="submit" value="编辑" name="submit" id="submit">
                <input type="reset" value="重置">
            </td>
            </tr>
        </table>
    </form>
</div>
<iframe name="upload_frame" id="upload_frame" style="width:0;height:0;display:none;" ></iframe>
<input type="hidden" name="clickVal" id="clickVal" value="1">
</div>