<?php
/* @var $this \Qyk\Mm\Facade\Response */
?>
<form class="login" action="/login">
    <input type="text" name="username" placeholder="用户名">
    <input type="text" name="<?= $this->getCsrfPostKey() ?>" hidden value="<?= $this->createCsrfToken() ?>"/>
    <input type="submit" value="Log In">
</form>
