<?php
?>
<h1>"<?=($new) ? 'Add new' : $attributes['name']?>"</h1>
<a href="/?seo_proxy_action=list"> << To list</a> <hr />
<form action="/?seo_proxy_action=save<?= ($new) ? '&new=1' : '' ?>" method="POST">
    <?php foreach ($attributes as $name => $value) : ?>
       <b><?=$name?></b> : <br>
        <textarea cols="20" rows="1" name="proxy_form[<?=$name?>]" ><?=$value?></textarea> <br />
    <?php endforeach ?>
    <br />
    <input type="submit" value="save"/>
</form>
