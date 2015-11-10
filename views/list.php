<?php
?>
<h1>Seo URL list</h1>
<a href="/?seo_proxy_action=form">New</a><hr>
<div class="row">
    <?php
    foreach ($data as $k => $v)
        $this->render('_field', $v);?>
</div>