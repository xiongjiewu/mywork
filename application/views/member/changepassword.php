<div class="password">
    <form method="post" action="" onsubmit="return false;" autocomplete="off">
        <input type="hidden" name="key" id="key" value="<?php echo $key;?>">
        <fieldset>
            <legend>更改密码</legend>
            <label>新密码</label>
            <input type="password" name="password1" id="password1" placeholder="新密码">
            <label>确认密码</label>
            <input type="password" name="password2" id="password2" placeholder="确认密码">
            <label class="checkbox">
            </label>
            <button type="submit" name="submitchange" id="submitchange" class="btn">提交</button>
        </fieldset>
    </form>
</div>
