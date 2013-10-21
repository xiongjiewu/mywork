<div class="bs-docs-example">
    <div class="movieList">
        <div class="actionList">
            <ul>
                <li>
                    <a class="btn btn-info" href="<?php echo get_url("/background/createhelp/")?>">
                        发新帮助文档
                    </a>
                </li>
            </ul>
        </div>
        <div class="listTable">
            <table cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <th class="chose"><span class="choseAll">全选</span></th>
                    <th>标题</th>
                    <th>操作</th>
                </tr>
                <?php if (!empty($helpList)):?>
                    <?php $count = count($helpList);$i = 1;?>
                    <?php foreach($helpList as $infoVal):?>
                        <tr <?php if ($i == $count):?>class="lastOne"<?php elseif($i == 1):?>class="firstOne"<?php endif;?>>
                            <td class="chose"><input type="checkbox" v="<?php echo $infoVal['id'];?>" name="chose" id="chose1"></td>
                            <td><?php echo $infoVal['title'];?></td>
                            <td><span><a href="<?php echo get_url("/background/edithelp/") . $infoVal['id'];?>">编辑</a></span></td>
                        </tr>
                        <?php $i++;?>
                    <?php endforeach;?>
                <?php endif;?>
            </table>
        </div>
    </div>
    <?php $this->load->view("component/pagenew",array("fenye" => $fenye));?>
</div>