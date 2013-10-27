<div class="bs-docs-example">
    <?php if (!empty($success)):?>
        <div style="color: #ff0000">
            发表成功！
        </div>
    <?php endif;?>
    <form method="post" action="" name="create_post" id="create_post">
        标题：<input name="title" id="title" type="text">
        <br>
        <textarea class="xheditor" name="content" id="content"></textarea>
        <p></p>
        <input type="submit" id="create_post_button" class="btn btn-large btn-primary" value="发表"><span
            style="color: #aaa">（ctrl+enter快捷回复）</span>
        <br>
    </form>
</div>