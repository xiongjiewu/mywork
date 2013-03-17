<div class="upmovie">
    <?php if (!empty($error)):?>
    <div class="error">
         对不起，<?php echo $error;?>!
    </div>
    <?php elseif (!empty($success)):?>
    <div class="success">
        上传成功![<a href="<?php echo get_url("/detail/index/{$success}");?>" target="_blank">访问</a>]或[<a href="<?php echo get_url("background/editmovie?id={$success}");?>">编辑</a>]
    </div>
    <?php endif;?>
    <form name="upmovie" id="upmovie" method="post" action="<?php echo get_url("/upmovieaction/");?>" autocomplete="off" enctype="multipart/form-data">
        <input type="hidden" name="watchLink" id="watchLink" value="">
        <input type="hidden" name="return" id="return" value="<?php echo get_url(get_config_value("image_upload_return"));?>">
        <input type="hidden" name="upload_url" id="upload_url" value="<?php echo get_url(get_config_value("image_upload_url"));?>">
        <input type="hidden" name="downloadLink" id="downloadLink" value="">
        <input type="hidden" name="image_url" id="image_url" value="">
        <input type="hidden" name="bofangqi" id="bofangqi" value="">
        <input type="hidden" name="qingxi" id="qingxi" value="">
        <input type="hidden" name="shoufei" id="shoufei" value="">
        <input type="hidden" name="size" id="size" value="">
        <input type="hidden" name="downloadType" id="downloadType" value="">
    <table>
        <tr class="movie_name">
            <td>名&nbsp;&nbsp;&nbsp;称：</td>
            <td><input type="text" name="name" id="name">&nbsp;<code>*</code></td>
        </tr>
        <tr class="movie_daoyan">
            <td>导&nbsp;&nbsp;&nbsp;演：</td>
            <td><input type="text" name="daoyan" id="daoyan">&nbsp;<code>*</code></td>
        </tr>
        <tr class="movie_nianfen">
            <td>年&nbsp;&nbsp;&nbsp;份：</td>
            <td><input type="text" name="nianfen" id="nianfen">&nbsp;年&nbsp;<code>*</code></td>
        </tr>
        <tr class="movie_diqu">
            <td>类&nbsp;&nbsp;&nbsp;型：</td>
            <td>
                <select name="type">
                    <option value="1">动作</option>
                    <option value="2">爱情</option>
                    <option value="3">科幻</option>
                    <option value="4">魔幻</option>
                    <option value="5">恐怖</option>
                    <option value="6">其他</option>
                </select>&nbsp;<code>*</code>
            </td>
        </tr>
        <tr class="movie_diqu">
            <td>地&nbsp;&nbsp;&nbsp;区：</td>
            <td>
                <select name="diqu">
                    <option value="1">中国</option>
                    <option value="2">日韩</option>
                    <option value="3">欧美</option>
                    <option value="4">其他</option>
                </select>&nbsp;<code>*</code>
            </td>
        </tr>
        <tr class="movie_shichang">
            <td>时&nbsp;&nbsp;&nbsp;长：</td>
            <td><input type="text" name="shichang" id="shichang">&nbsp;&nbsp;分&nbsp;<code>*</code></td>
        </tr>
        <tr class="movie_zhuyan">
            <td>主&nbsp;&nbsp;&nbsp;演：</td>
            <td><input type="text" name="zhuyan" id="zhuyan">&nbsp;&nbsp;以分号隔开&nbsp;<code>*</code></td>
        </tr>
        <tr class="movie_jianjie">
            <td>简&nbsp;&nbsp;&nbsp;介：</td>
            <td>
                <textarea name="jieshao" id="jieshao"></textarea>&nbsp;<code>*</code>
            </td>
        </tr>
        <tr class="movie_tupian">
            <td>图&nbsp;&nbsp;&nbsp;片：</td>
            <td>
                <div class="image" style="display: none;">
                    <img src="">
                    <br>
                    <span class="uploadAgain">重新上传</span>
                </div>
                <div class="upimage">
                    <input type="file" name="image" id="image">
                    &nbsp;<code>*</code>&nbsp;&nbsp;最大2M，1024*768
                </div>
            </td>
        </tr>
        <tr class="movie_time0">
            <td>本&nbsp;&nbsp;&nbsp;上：</td>
            <td><input type="text" name="time0" id="time0">&nbsp;本站能提供观看链接时间</td>
        </tr>
        <tr class="movie_time">
            <td>国&nbsp;&nbsp;&nbsp;上：</td>
            <td><input type="text" name="time1" id="time1">&nbsp;国内上映时间</td>
        </tr>
        <tr class="movie_time">
            <td>港&nbsp;&nbsp;&nbsp;上：</td>
            <td><input type="text" name="time2" id="time2">&nbsp;港台上映时间</td>
        </tr>
        <tr class="movie_time">
            <td>欧&nbsp;&nbsp;&nbsp;上：</td>
            <td><input type="text" name="time3" id="time3">&nbsp;欧美上映时间</td>
        </tr>

        <tr class="movie_link">
            <td valign="top">观&nbsp;&nbsp;&nbsp;链：</td>
            <td class="watchLink">
                <em>
                    <input type="text" class="link">
                    <select class="bofangqi">
                        <option value="1">快播</option>
                        <option value="2">百度影音</option>
                        <option value="3">迅雷</option>
                        <option value="4">奇艺</option>
                        <option value="5">优酷</option>
                        <option value="6">土豆</option>
                        <option value="7">其他</option>
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
                        <option value="1">快播</option>
                        <option value="2">百度影音</option>
                        <option value="3">迅雷</option>
                        <option value="4">奇艺</option>
                        <option value="5">优酷</option>
                        <option value="6">土豆</option>
                        <option value="7">其他</option>
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
                <span class="addwatchLink" type="1">+添加观看链接</span>
            </td>
        </tr>
        <tr class="movie_link">
            <td valign="top">下&nbsp;&nbsp;&nbsp;链：</td>
            <td class="downloadLink">
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
                <span class="adddownloadLink" type="2">+添加下载链接</span>
            </td>
        </tr>
        <tr>
            <td class="up">&nbsp;</td>
            <td>
                <input type="submit" value="上传" name="submit" id="submit">
                <input type="reset" value="重置">
            </td>
        </tr>
    </table>
    </form>
</div>
<iframe name="upload_frame" id="upload_frame" style="width:0;height:0;display:none;" ></iframe>
<input type="hidden" name="clickVal" id="clickVal" value="1">