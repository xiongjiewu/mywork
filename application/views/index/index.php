<?php if (!empty($newestDyInfo)): ?>
    <div class="bs-docs-example">
        <table class="new_dy">
            <tr>
                <td class="new_title"><strong>最新<p></p>热门</strong></td>
                <td>
                    <ul>
                        <?php foreach ($newestDyInfo as $nesInfoVal): ?>
                            <li>
                                <a href="<?php echo get_url("/detail/index/{$nesInfoVal['id']}"); ?>"><?php echo $nesInfoVal['name'];?></a>
                            </li>
                        <?php endforeach;?>
                    </ul>
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
                    <ul>
                        <?php foreach ($willDyInfo as $willInfoVal): ?>
                            <li>
                                <a href="<?php echo get_url("/detail/index/{$willInfoVal['id']}"); ?>"><?php echo $willInfoVal['name'];?></a>
                            </li>
                        <?php endforeach;?>
                    </ul>
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
                    <ul>
                        <?php foreach ($classDyInfo as $classInfoVal): ?>
                            <li>
                                <a href="<?php echo get_url("/detail/index/{$classInfoVal['id']}"); ?>"><?php echo $classInfoVal['name'];?></a>
                            </li>
                        <?php endforeach;?>
                    </ul>
                </td>
            </tr>
        </table>
    </div>
<?php endif; ?>