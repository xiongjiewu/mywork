<div class="row">
    <?php $this->load->view("component/usercenterleft",array("userInfo" =>$userInfo,"index"=>0));?>
    <div class="right_container">
        <div class="main-tab">
            <a class="tab-focus" href="<?php echo get_url("/usercenter/message/")?>">我的消息</a>
        </div>
        <div id="usermain">
            <div class="show mod-dist-r">
                <div class="modbox2">
                    <div class="select_create">
                        <select class="" name="shaixuan" id="shaixuan">
                            <?php foreach($selectData as $selectKey => $selectVal):?>
                                <option value="<?php echo $selectKey;?>"><?php echo $selectVal;?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                    <table class="table table-bordered">
                        <tr>
                            <th class="chose_all"><?php if (!empty($userMessageList)):?><span class="chose_all">全选</span><?php else:?>全选<?php endif;?></th>
                            <th class="title">内容</th>
                            <th class="action_do">时间</th>
                            <th class="action_do">操作</th>
                            <th class="system_reply">查看消息</th>
                        </tr>
                        <?php if (!empty($userMessageList)):?>
                            <?php foreach($userMessageList as $messageVal):?>
                                <tr <?php if ($messageVal['is_read'] == 0):?>class="no_read"<?php endif;?>>
                                    <td><input type="checkbox" name="ids[]" value="<?php echo $messageVal['id'];?>"></td>
                                    <td>
                                        <?php echo $messageVal['content'];?>
                                    </td>
                                    <td class="message_time">
                                        <?php echo date("Y-m-d H:i:s",$messageVal['time']);?>
                                    </td>
                                    <td class="action message_action">
                                        <span class="del" v="<?php echo $messageVal['id'];?>">删除</span>
                                        |
                                        <span class="read" is_read="<?php echo $messageVal['is_read'];?>" v="<?php echo $messageVal['id'];?>">
                                           <?php if ($messageVal['is_read'] == 0):?>
                                                标已读
                                            <?php else:?>
                                                标未读
                                            <?php endif;?>
                                        </span>
                                    </td>
                                    <td class="show_reply">
                                        <a href="<?php echo get_url("/usercenter/messageinfo/{$messageVal['id']}/");?>">
                                            详情
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach;?>
                        <?php else:?>
                            <tr><td colspan="5">目前，您查看的信息暂无！</td></tr>
                        <?php endif;?>
                    </table>
                    <?php if (!empty($userMessageList)):?>
                        <a href="javascript:void(0);" class="btn btn-info">批量删除</a>
                        <?php if ($userMessageCount > $limit):?>
                            <table class="page">
                                <tr>
                                    <td>
                                        <?php $this->load->view("component/pagenew", array("fenye" => $fenye));?>
                                    </td>
                                </tr>
                            </table>
                        <?php endif;?>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>
</div>