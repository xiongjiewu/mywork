<?php if (!empty($newestDyInfo)): ?>
    <div class="bs-docs-example" style=" width: 1130px;float: left;">
        <table class="new_dy">
            <tr>
                <td class="new_title"><strong>最新<p></p>热门</strong></td>
                <td>
                    <table class="table">
                        <?php $newestDyInfoCount = count($newestDyInfo);?>
                        <?php $baseCount = ($newestDyInfoCount > $baseNum) ? $baseNum : $newestDyInfoCount;?>
                        <?php $newestDyInfoI = ceil($newestDyInfoCount / $baseCount);?>
                        <?php $totalCount = ($newestDyInfoCount > $newestDyInfoI*$baseCount) ? $newestDyInfoI*$baseCount : $newestDyInfoCount;?>
                        <?php for ($i=0;$i<$newestDyInfoI;$i++): ?>
                                <?php $class = ($i == ($newestDyInfoI - 1)) ? "lastOne" : '';?>
                                <tr class="<?php echo $class;?>">
                                <?php for($j=$i*$baseCount;$j < $newestDyInfoCount;$j++):?>
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
                        <?php $totalCount = ($willDyInfoCount > $willDyInfoI*$baseCount) ? $willDyInfoI*$baseCount : $willDyInfoCount;?>
                        <?php for ($i=0;$i<$willDyInfoI;$i++): ?>
                            <?php $class = ($i == ($willDyInfoI - 1)) ? "lastOne" : '';?>
                            <tr class="<?php echo $class;?>">
                                <?php for($j=$i*$baseCount;$j < $totalCount;$j++):?>
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

<?php if (!empty($classDyInfo)): ?>
    <div class="bs-docs-example">
        <table class="new_dy">
            <tr>
                <td class="new_title"><strong>重温<p></p>经典</strong></td>
                <td>
                    <table class="table">
                        <?php $classDyInfoCount = count($classDyInfo);?>
                        <?php $baseCount = ($classDyInfoCount > $baseNum) ? $baseNum : $classDyInfoCount;?>
                        <?php $classDyInfoI = ceil($classDyInfoCount / $baseCount);?>
                        <?php $totalCount = ($classDyInfoCount > $classDyInfoI*$baseCount) ? $classDyInfoI*$baseCount : $classDyInfoCount;?>
                        <?php for ($i=0;$i<$classDyInfoI;$i++): ?>
                            <?php $class = ($i == ($classDyInfoI - 1)) ? "lastOne" : '';?>
                            <tr class="<?php echo $class;?>">
                                <?php for($j=$i*$baseCount;$j < $totalCount;$j++):?>
                                    <td>
                                        <a href="<?php echo get_url("/detail/index/{$classDyInfo[$j]['id']}"); ?>"><?php echo $classDyInfo[$j]['name'];?></a>
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
