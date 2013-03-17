<?php if (!empty($movieList)): ?>
    <div class="bs-docs-example">
        <table class="table">
            <?php $movieListI = 1;?>
            <?php $movieListCount = count($movieList);?>
            <?php foreach($movieList as $movieKey => $movieVal):?>
            <tr class="<?php if ($movieListI == $movieListCount):?>lastOne<?php endif;?>">
                <td class="dy_name">
                    <a href="<?php echo get_url("/detail/index/{$movieVal['id']}"); ?>"><?php echo $movieVal['name'];?></a>
                </td>
                <?php if (!empty($watchLinkInfo[$movieVal['id']])):?>
                    <?php $watchLinkI = 1;?>
                    <?php foreach($watchLinkInfo[$movieVal['id']] as $watchKey => $watchVal):?>
                        <td class="dy_watch"><a href="<?php echo $watchVal['link'];?>" target="_blank">观看链接<?php echo $watchLinkI++;?></a></td>
                    <?php endforeach;?>
                <?php endif;?>
                <?php if (!empty($downLoadLinkInfo[$movieVal['id']])):?>
                    <?php $downLinkI = 1;?>
                    <?php foreach($downLoadLinkInfo[$movieVal['id']] as $downKey => $downVal):?>
                        <td class="dy_down"><a href="<?php echo $downVal['link'];?>" target="_blank">下载链接<?php echo $downLinkI++;?></a></td>
                    <?php endforeach;?>
                <?php endif;?>
            </tr>
             <?php $movieListI++;?>
            <?php endforeach;?>
        </table>
    </div>
<?php endif; ?>