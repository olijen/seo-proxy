<?php
class Tools
{
    public static function checkFilterInDatabase()
    {
        $DB = DataBase::getDB();
        $query =
            " SELECT `id` ".
            " FROM `seo_proxy` ";
        $filter = count($DB->select($query));
    }

    public static function checkUrlInDatabase()
    {
        $DB = DataBase::getDB();
        $query =
            " SELECT `id` ".
            " FROM `seo_proxy` ";
        $filter = count($DB->select($query));
    }
    
    public static function checkDatabase()
    {
        $mysqli = @mysqli_connect(DB_HOST, DB_USER, DB_PWD, DB_NAME);
        if (mysqli_connect_errno($mysqli))
            Registry::set('mysql_error', "Не удалось подключиться к MySQL: " . mysqli_connect_error());
        
        Registry::set('mysqli', $mysqli);
    }

    public static function getSiteConfig()
    {
        require $_SERVER['DOCUMENT_ROOT'].'/seo-proxy/application/config.php';
    }
}