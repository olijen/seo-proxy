<?php
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
    require_once 'config.php';
    require_once 'components/SeoProxy.php';
    require_once 'components/classes/database.php';

    $sp = new SeoProxy($_SERVER['REQUEST_URI'], Database::getDB());

    $sp->start();

?>