<?php

$CONFIG = array(

    'SITENAME' => 'http://'.$_SERVER['HTTP_HOST'],
    'ROOTDIR'  => $_SERVER['DOCUMENT_ROOT'].'seo-proxy',
    'APPDIR'   => $_SERVER['DOCUMENT_ROOT'].'/seo-proxy/application',
    'FRONTDIR' => $_SERVER['DOCUMENT_ROOT'].'/seo-proxy/front',
    
    'DB_NAME'  => 'ledstorm',
    'DB_USER'  => 'root',
    'DB_HOST'  => 'localhost',
    'DB_PWD'   =>  '',
    
);

foreach ($CONFIG as $k => $v) {
    defined($k) or define($k, $v);
}

unset($CONFIG);