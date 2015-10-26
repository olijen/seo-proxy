<?php

/**
 * Created by PhpStorm.
 * User: Olijen
 * Date: 26.10.2015
 * Time: 12:56
 */
class SeoProxy
{
    public static $proxy;

    protected $url;
    protected $db;

    public function __construct($url, $database)
    {
        $this->url = $url;
        $this->db = $database;
    }

    public function start()
    {
        if (!empty($_GET['seo_proxy'])) {
            $query = "
              SELECT *
              FROM `seo_proxy`
              WHERE `id` = {$_GET['seo_proxy']};
            ";
            $filter = $this->db->selectRow($query);

            if (empty($filter)) exit('No filter in database');
            self::$proxy = $filter;
            return;
        }
        $exp = explode('/', $this->url);
        if ($exp[1] == 'f') {
            $this->checkFilterInDb();
        } else {
            $this->checkUrlInDb();
        }
    }

    public function checkUrlInDb()
    {
        $query = "
          SELECT `seo_url`
          FROM `seo_proxy`
          WHERE `url` = '{$this->url}';
        ";
        $filter = $this->db->selectRow($query);
        unset ($this->db);
        if (empty($filter['seo_url'])) return;
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: http://".$_SERVER['HTTP_HOST'].$filter['seo_url']);
        exit();
    }

    public function checkFilterInDb()
    {
        $query = "
          SELECT *
          FROM `seo_proxy`
          WHERE `seo_url` = '{$this->url}';
        ";
        $filter = $this->db->selectRow($query);
        if (empty($filter['url'])) exit('No filter in database');
        unset ($this->db);
        $delim = (strpos($filter['url'], '?') === false) ? '?' : '&';
        //echo 'http://'.$_SERVER['HTTP_HOST'].$filter['url'].$delim.'seo_proxy='.$filter['id'];
        echo file_get_contents('http://'.$_SERVER['HTTP_HOST'].$filter['url'].$delim.'seo_proxy='.$filter['id']);
        exit;
    }

    public static function get($name, $real)
    {
        if (!empty(self::$proxy[$name]))
            return self::$proxy[$name];
        return $real;
    }

    public function checkAction()
    {
        echo 'action';
    }
}