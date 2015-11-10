<?php

$CONFIG = array(

    'SITENAME'   => 'http://'.$_SERVER['HTTP_HOST'],
    'ROOTDIR'    => $_SERVER['DOCUMENT_ROOT'].'/seo-proxy',
    'COMPONENTS' => $_SERVER['DOCUMENT_ROOT'].'/seo-proxy/components',
    'FRONTDIR'   => $_SERVER['DOCUMENT_ROOT'].'/seo-proxy/front',
    
    'DB_NAME'  => 'ledstorm',
    'DB_USER'  => 'root',
    'DB_HOST'  => 'localhost',
    'DB_PWD'   =>  '',
);

foreach ($CONFIG as $k => $v) {
    defined($k) or define($k, $v);
}

unset($CONFIG);