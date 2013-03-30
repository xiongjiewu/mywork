<?php if (!empty($movieList)): ?>
    <?php $movieListI = 1; ?>
    <?php foreach ($movieList as $movieKey => $movieVal): ?>
        <table class="table table-bordered <?php if ($movieListI == 1): ?>firstOneT<?php endif; ?>">
            <tr>
                <td class="dy_name" rowspan="3" valign="middle">
                    <a href="<?php echo get_url("/detail/index/{$movieVal['id']}"); ?>"><?php echo $movieVal['name'];?></a>
                </td>
                <td>
                    <strong>导演：</strong><?php echo $movieVal['daoyan'];?>
                    <span>|</span>
                    <strong>主演：</strong><?php $zhuyao = preg_split("/;+|；+/", $movieVal['zhuyan']);echo implode("、", $zhuyao);?>
                    <span>|</span>
                    <strong>年份：</strong><?php echo date("Y", strtotime($movieVal['nianfen']));?>年
                    <span>|</span>
                    <strong>时长：</strong><?php echo $movieVal['shichang'];?>分
                    <span>|</span>
                    <strong>地区：</strong><?php echo $moviePlace[$movieVal['diqu']];?>
                    <span>|</span>
                    <strong>类型：</strong><?php echo $movieType[$movieVal['type']];?>片
                </td>
            </tr>
            <tr>
                <td>
                    <?php if (!empty($movieVal['time2'])): ?>
                        <strong>内陆上映时间：</strong><?php echo date("Y-m-d", $movieVal['time2']); ?>
                    <?php endif;?>
                    <?php if (!empty($movieVal['time2'])): ?>
                        <span>|</span>
                        <strong>港台上映时间：</strong><?php echo date("Y-m-d", $movieVal['time1']); ?>
                    <?php endif;?>
                    <?php if (!empty($movieVal['time3'])): ?>
                        <span>|</span>
                        <strong>欧美上映时间：</strong><?php echo date("Y-m-d", $movieVal['time3']); ?>
                    <?php endif;?>
                    <?php if (!empty($userNoticeInfos[$movieVal['id']])): ?>
                        <span class="btn dy_notic_btn">
                            <i class="icon-check icon-white"></i>
                            已订阅观看通知
                        </span>
                    <?php else: ?>
                        <span class="btn dy_notic" val="<?php echo $movieVal['id']; ?>">
                            <i class="icon-volume-up"></i>
                            订阅观看通知
                        </span>
                    <?php endif;?>
                </td>
            </tr>
            <tr>
                <td><strong>简介：</strong><?php echo $movieVal['jieshao'];?></td>
            </tr>
        </table>
        <?php $movieListI++; ?>
    <?php endforeach; ?>
<?php endif; ?>
<script type="text/javascript">
    (function($){
        $(document).ready(function(){
            $("table.table tr td span.btn").each(function(){
                $(this).bind("click",function(){
                    <?php if (empty($userId)):?>
                        var url = "<?php echo get_url('/login?bgurl=') . base64_encode(get_url('/upcomingmovie/'));?>";
                        window.location.href = url;
                    <?php else:?>
                        init.ajaxInertNotice($(this));
                    <?php endif;?>
                });
            });
        });
    })(jQuery);
</script>