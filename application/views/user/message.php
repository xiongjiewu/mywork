<div class="row">
    <?php $this->load->view("component/usercenterleft",array("userInfo" =>$userInfo,"index"=>0));?>
    <div class="right_container">
        <div class="main-tab">
            <a class="tab-focus" href="<?php echo get_url("/usercenter/message/")?>">我的消息</a>
        </div>
        <div id="usermain">
            <div class="show mod-dist-r">
                <div class="select_create">
                    <select class="" name="shaixuan" id="shaixuan">
                        <?php foreach($selectData as $selectKey => $selectVal):?>
                            <option value="<?php echo $selectKey;?>"><?php echo $selectVal;?></option>
                        <?php endforeach;?>
                    </select>
                </div>
                <div class="modbox2">
                        <?php if (!empty($userMessageList)):?>
                            <?php $userMessageI = 1;?>
                            <?php foreach($userMessageList as $messageVal):?>
                                <table class="table">
                                <tr>
                                    <th class="chose">
                                        <input type="checkbox" name="ids[]" value="<?php echo $messageVal['id'];?>">
                                    </th>
                                    <th class="time">
                                        时间:<?php echo date("Y-m-d H:i:s",$messageVal['time']);?>
                                    </th>
                                    <th class="action_do">
                                        操作:
                                        <span class="del" v="<?php echo $messageVal['id'];?>">删除</span>
                                        |
                                        <span class="read" is_read="<?php echo $messageVal['is_read'];?>" v="<?php echo $messageVal['id'];?>">
                                           <?php if ($messageVal['is_read'] == 0):?>
                                                标已读
                                            <?php else:?>
                                                标未读
                                            <?php endif;?>
                                        </span>
                                    </th>
                                </tr>
                                <tr class="content<?php if ($messageVal['is_read'] == 0):?> no_read content<?php endif;?>">
                                    <td colspan="3">
                                        <?php echo $messageVal['content'];?>
                                    </td>
                                </tr>
                            </table>
                            <?php $userMessageI++;?>
                            <?php endforeach;?>
                        <?php else:?>
                        <table>
                            <tr>
                                <td colspan="3">目前，您查看的信息暂无！</td>
                            </tr>
                        </table>
                        <?php endif;?>

                </div>
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