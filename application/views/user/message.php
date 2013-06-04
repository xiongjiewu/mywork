<div class="user_main">
<div class="row">
    <?php $this->load->view("component/usercenterleft", array("userInfo" => $userInfo, "index" => 0));?>
    <div class="right_container">
        <div class="main-tab">
            <a class="tab-focus" href="<?php echo get_url("/usercenter/message/") ?>">我的消息</a>
        </div>
        <div id="usermain">
            <div class="show mod-dist-r">
                <div class="select_create">
                    <select class="" name="shaixuan" id="shaixuan">
                        <?php foreach ($selectData as $selectKey => $selectVal): ?>
                            <option value="<?php echo $selectKey; ?>"><?php echo $selectVal;?></option>
                        <?php endforeach;?>
                    </select>
                    <?php if (!empty($userMessageList)): ?>
                        <div class="chose_all">
                            <span>全选</span>
                        </div>
                    <?php endif;?>
                </div>
                <div class="modbox2">
                    <?php if (!empty($userMessageList)): ?>
                        <?php $userMessageI = 1; ?>
                        <?php foreach ($userMessageList as $messageVal): ?>
                            <table class="table<?php if ($userMessageI == 1):?> firstOne<?php endif;?>">
                                <tr class="content<?php if ($messageVal['is_read'] == 0): ?> no_read content<?php endif; ?>">
                                    <td colspan="2">
                                        <?php echo $messageVal['content'];?>
                                        <span class="time">
                                            (
                                            <?php if (date("Ymd", $messageVal['time']) == date("Ymd")):?>
                                                今天
                                            <?php elseif(date("Ymd", $messageVal['time']) == date("Ymd",strtotime("-1 day"))):?>
                                                昨天
                                            <?php elseif(date("Ymd", $messageVal['time']) == date("Ymd",strtotime("-2 day"))):?>
                                                前天
                                            <?php else:?>
                                                <?php echo date("Y-m-d H:i:s", $messageVal['time']);?>
                                            <?php endif;?>
                                            )
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="chose">
                                        <input type="checkbox" name="ids[]" value="<?php echo $messageVal['id']; ?>">
                                    </th>
                                    <th class="action_do">
                                        操作:
                                        <span class="del" v="<?php echo $messageVal['id']; ?>">删除</span>
                                        |
                                        <span class="read" is_read="<?php echo $messageVal['is_read']; ?>"
                                              v="<?php echo $messageVal['id']; ?>">
                                           <?php if ($messageVal['is_read'] == 0): ?>
                                                标已读
                                            <?php else: ?>
                                                标未读
                                            <?php endif;?>
                                        </span>
                                    </th>
                                </tr>
                            </table>
                            <?php $userMessageI++; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <table class="table">
                            <tr>
                                <td colspan="3" style="border: none;">目前，您查看的信息暂无！</td>
                            </tr>
                        </table>
                    <?php endif;?>

                </div>
                <?php if (!empty($userMessageList)): ?>
                    <a href="javascript:void(0);" class="btn btn-info">批量删除</a>
                    <?php if ($userMessageCount > $limit): ?>
                        <table class="page">
                            <tr>
                                <td>
                                    <?php $this->load->view("component/pagenew", array("fenye" => $fenye));?>
                                </td>
                            </tr>
                        </table>
                    <?php endif; ?>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>
</div>