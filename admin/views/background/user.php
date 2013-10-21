<div class="bs-docs-example">
    <div class="userList">
        <div class="actionList">
            <ul>
                <li type="0" class="btn btn-success">批量封禁</li>
                <li type="1" class="btn btn-primary">批量解封</li>
            </ul>
        </div>
        <div class="searchUser">
            <form method="get" action="">
            <input type="text" class="username" value="<?php if (isset($username)):?><?php echo $username;?><?php endif;?>" name="username" id="username">
            <input type="submit" class="submit" value="搜索">
            </form>
        </div>
        <div class="listTable">
            <table cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <th class="chose"><span class="choseAll">全选</span></th>
                    <th>用户名</th>
                    <th>注册时间</th>
                    <th>操作</th>
                </tr>
                <?php if (!empty($userInfoList)):?>
                    <?php $count = count($userInfoList);$i = 1;?>
                    <?php foreach($userInfoList as $infoVal):?>
                        <tr <?php if ($i == $count):?>class="lastOne"<?php elseif($i == 1):?>class="firstOne"<?php endif;?>>
                            <td class="chose">
                                <?php if (empty($userAdminInfo[$infoVal['id']])):?>
                                    <input type="checkbox" v="<?php echo $infoVal['id'];?>" name="chose" id="chose1">
                                <?php endif;?>
                            </td>
                            <td><?php echo $infoVal['userName'];?></td>
                            <td><?php echo date("Y-m-d H:i:s",$infoVal['time']);?></td>
                            <td>
                                <?php if (!empty($userAdminInfo[$infoVal['id']])):?>
                                    <span style="cursor: default">
                                        <?php if ($userAdminInfo[$infoVal['id']]['type'] == 1):?>
                                            超级管理员
                                        <?php else:?>
                                            管理员
                                            <?php if (!empty($loginAdminInfo) && ($loginAdminInfo['type'] == 1)):?>
                                                |<a class="cancelAdmin" type="0" val="<?php echo $infoVal['id'];?>" href="javascript:void(0);">解除管理员</a>
                                            <?php endif?>
                                        <?php endif;?>
                                    </span>
                                <?php else:?>
                                    <span>
                                    <?php if ($infoVal['status'] == 0):?>
                                            <a class="adminAction" val="<?php echo $infoVal['id'];?>" type="1" href="javascript:void(0);">封禁</a>
                                            <?php if (!empty($loginAdminInfo) && ($loginAdminInfo['type'] == 1)):?>
                                                |<a class="sheAdmin" type="1" val="<?php echo $infoVal['id'];?>" href="javascript:void(0);">设为管理员</a>
                                            <?php endif?>
                                        <?php else:?>
                                            <a class="adminAction" val="<?php echo $infoVal['id'];?>" type="0" href="javascript:void(0);">解禁</a>
                                        <?php endif;?>
                                        |
                                        <a class="send_email" href="<?php echo get_url("/background/sendemail/{$infoVal['id']}/")?>">
                                            给他发邮件
                                        </a>
                                </span>
                                <?php endif;?>
                            </td>
                        </tr>
                        <?php $i++;?>
                    <?php endforeach;?>
                <?php endif;?>
            </table>
        </div>
    </div>
    <?php $this->load->view("component/pagenew",array("fenye" => $fenye));?>
</div>