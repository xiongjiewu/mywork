<div class="row">
    <?php $this->load->view("component/usercenterleft",array("userInfo" =>$userInfo,"index"=>($type =="want") ?3 : 5));?>
    <div class="right_container">
        <div class="main-tab">
            <?php if ($type =="want"):?>
                <a <?php if ($type =="want"):?>class="tab-focus"<?php endif;?> href="<?php echo get_url("/usercenter/feedback/want/")?>">我的反馈</a>
            <?php else:?>
                <a <?php if ($type =="suggest"):?>class="tab-focus"<?php endif;?> href="<?php echo get_url("/usercenter/feedback/suggest/")?>">我的投诉与建议</a>
            <?php endif;?>
        </div>
        <div id="usermain">
            <input type="hidden" name="type" id="type" value="<?php echo $type;?>">
            <div class="show mod-dist-r">
                <div class="select_create">
                    <select class="" name="shaixuan" id="shaixuan">
                        <?php foreach($selectData as $selectKey => $selectVal):?>
                            <option value="<?php echo $selectKey;?>"><?php echo $selectVal;?></option>
                        <?php endforeach;?>
                    </select>
                    <div class="<?php if ($type == "want"):?>iWant<?php else:?>iWant iWant_su<?php endif;?>">
                        <?php if ($type == "want"):?>
                            <a href="<?php echo get_url("/usercenter/createfeedback/{$type}/");?>" class="btn btn-warning">反馈我想看</a>
                        <?php else:?>
                            <a href="<?php echo get_url("/usercenter/createfeedback/{$type}/");?>" class="btn btn-warning">反馈投诉与建议</a>
                        <?php endif;?>
                    </div>
                </div>
                <div class="modbox2">
                    <table class="table">
                        <tr>
                            <th class="chose_all"><?php if (!empty($feedbackInfos)):?><span class="chose_all">全选</span><?php else:?>全选<?php endif;?></th>
                            <th class="title">标题</th>
                            <th class="action_do">操作</th>
                            <th class="system_reply">系统回复</th>
                        </tr>
                        <?php if (!empty($feedbackInfos)):?>
                            <?php foreach($feedbackInfos as $fVal):?>
                                <tr>
                                    <td><input type="checkbox" name="ids[]" value="<?php echo $fVal['id'];?>"></td>
                                    <td><?php echo $fVal['title'];?></td>
                                    <td class="action">
                                        <?php if ($fVal['reply'] == 0):?>
                                        <a href="<?php echo get_url("/usercenter/editfeedback/{$type}/{$fVal['id']}/")?>">编辑</a>
                                        <?php else:?>
                                        <span v="<?php echo $fVal['id'];?>">
                                            删除
                                        </span>
                                        <?php endif;?>
                                    </td>
                                    <td class="show_eply"><?php if ($fVal['reply'] == 0):?>未回复<?php else:?><a href="<?php echo get_url("/usercenter/messageinfo/{$fVal['reply']}/");?>" title="点击查看回复">有回复</a><?php endif;?></td>
                                </tr>
                            <?php endforeach;?>
                        <?php else:?>
                            <tr><td colspan="4">对不起，您查看的信息暂无！</td></tr>
                        <?php endif;?>
                    </table>
                </div>
                <?php if (!empty($feedbackInfos)):?>
                    <a href="javascript:void(0);" class="btn btn-info">批量删除</a>
                    <?php if ($feedBackCount > $limit):?>
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