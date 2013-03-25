<div class="row">
    <?php $this->load->view("component/usercenterleft",array("userInfo" =>$userInfo,"index"=>4));?>
    <div class="right_container">
        <div class="main-tab">
            <a class="tab-focus" href="<?php echo get_url("/usercenter/notice/")?>">我订阅的电影通知</a>
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
                            <th class="chose_all"><?php if (!empty($userNoticeList)):?><span class="chose_all">全选</span><?php else:?>全选<?php endif;?></th>
                            <th class="title">电影名</th>
                            <th class="action_do">操作</th>
                            <th class="system_reply">系统回复</th>
                        </tr>
                        <?php if (!empty($userNoticeList)):?>
                            <?php foreach($userNoticeList as $noticeVal):?>
                                <tr>
                                    <td><input type="checkbox" name="ids[]" value="<?php echo $noticeVal['id'];?>"></td>
                                    <td><?php echo $infoList[$noticeVal['infoId']]['name'];?></td>
                                    <td class="action">
                                        <span v="<?php echo $noticeVal['id'];?>">删除</span>
                                    </td>
                                    <td class="show_eply"><?php if ($noticeVal['reply'] == 0):?>未回复<?php else:?>已回复<?php endif;?></td>
                                </tr>
                            <?php endforeach;?>
                        <?php else:?>
                            <tr><td colspan="4">目前，您查看的信息暂无！</td></tr>
                        <?php endif;?>
                    </table>
                    <?php if (!empty($userNoticeList)):?>
                        <a href="javascript:void(0);" class="btn btn-info">批量删除</a>
                        <?php if ($userNoticeCount > $limit):?>
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