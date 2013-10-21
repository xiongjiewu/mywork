<?php if (!empty($newestDyInfo)): ?>
    <div class="bs-docs-example" style=" width: 1130px;float: left;">
        <table class="new_dy">
            <tr>
                <td class="new_title"><strong>最新<p></p>上映</strong></td>
                <td>
                    <table class="table">
                        <?php $newestDyInfoCount = count($newestDyInfo);?>
                        <?php $baseCount = ($newestDyInfoCount > $baseNum) ? $baseNum : $newestDyInfoCount;?>
                        <?php $newestDyInfoI = ceil($newestDyInfoCount / $baseCount);?>
                        <?php for ($i=0;$i<$newestDyInfoI;$i++): ?>
                                <?php $class = ($i == ($newestDyInfoI - 1)) ? "lastOne" : '';?>
                                <tr class="<?php echo $class;?>">
                                <?php for($j=$i*$baseCount;$j < $newestDyInfoI*$baseCount;$j++):?>
                                    <td>
                                        <a href="<?php echo get_url("/detail/index/{$newestDyInfo[$j]['id']}"); ?>"><?php echo $newestDyInfo[$j]['name'];?></a>
                                    </td>
                                <?php endfor;?>
                                </tr>
                        <?php endfor;?>
                    </table>
                </td>
            </tr>
        </table>
    </div>
<?php endif; ?>
<?php if (!empty($willDyInfo)): ?>
    <div class="bs-docs-example">
        <table class="new_dy">
            <tr>
                <td class="new_title"><strong>即将<p></p>上映</strong></td>
                <td>
                    <table class="table">
                        <?php $willDyInfoCount = count($willDyInfo);?>
                        <?php $baseCount = ($willDyInfoCount > $baseNum) ? $baseNum : $willDyInfoCount;?>
                        <?php $willDyInfoI = ceil($willDyInfoCount / $baseCount);?>
                        <?php for ($i=0;$i<$willDyInfoI;$i++): ?>
                            <?php $class = ($i == ($willDyInfoI - 1)) ? "lastOne" : '';?>
                            <tr class="<?php echo $class;?>">
                                <?php for($j=$i*$baseCount;$j < $willDyInfoI*$baseCount;$j++):?>
                                    <td>
                                        <a href="<?php echo get_url("/detail/index/{$willDyInfo[$j]['id']}"); ?>"><?php echo $willDyInfo[$j]['name'];?></a>
                                    </td>
                                <?php endfor;?>
                            </tr>
                        <?php endfor;?>
                    </table>
                </td>
            </tr>
        </table>
    </div>
<?php endif; ?>