<?php

/**
 * Created by PhpStorm.
 * User: Olijen
 * Date: 26.10.2015
 * Time: 12:56
 */
class SeoProxy
{
    /**
     * @var SeoProxy $proxy
     */
    public static $proxy;

    /**
     * @var $url string
     */
    protected $url;
    /**
     * @var DataBase $db
     */
    protected $db;

    public function __construct($url, $database)
    {
        $this->url = $url;
        $this->db = $database;
    }

    public function start()
    {
        if (!empty($_GET['seo_proxy_action'])) {
            $this->action($_GET['seo_proxy_action']);
            exit('<hr />Seo-proxy By olijen v 0.1');
        }
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
        $query = "SELECT * FROM `seo_proxy` WHERE `seo_url` = '{$this->url}';";
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

    public function action($action)
    {
        switch ($action) {
            case 'list':
                $query = "SELECT * FROM `seo_proxy`;";
                $data = $this->db->select($query);
                var_dump($data);
                $this->render('list', $data);
                break;
            case 'form':
                $new = empty($_GET['id']);
                $data = ($new) ?
                    array('name'=>'', 'url'=>'', 'seo_url'=>'', 'seo_title'=>'', 'seo_description'=>'', 'seo_text'=>'', 'seo_h1'=>'') :
                    $this->db->selectRow("SELECT * FROM `seo_proxy` WHERE `id` = {$_GET['id']};");
                if ($new)
                    unset($data['id']);
                $this->render('form', array('attributes' => $data, 'new' => $new));
                break;
            case 'save':
                if (empty($_POST['proxy_form'])) exit('Require POST proxy_form');
                $form = $_POST['proxy_form'];
                if (!empty($_GET['new'])) {
                    unset($form['id']);
                    $id = $this->db->insert('seo_proxy', $form);
                    if (!$id) exit('Insert with error');
                } else {
                    if (!$this->db->update('seo_proxy', 'id='.$form['id'], $form))
                        exit('Update with error');
                    $id = $form['id'];
                }
                header("Location: http://".$_SERVER['HTTP_HOST'].'/?seo_proxy_action=form&id='.$id);
                break;
            case 'install':
                $sql = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/seo-proxy/migration.sql');
                $sql = str_replace('<<database>>', DB_NAME, $sql);
                if (!empty($_GET['start']))
                    $this->install($sql);
                $this->render('install', array('sql'=>$sql));
                break;
        }
    }

    public function render($view, $data)
    {
        extract($data);
        include($_SERVER['DOCUMENT_ROOT'].'/seo-proxy/views/'.$view.'.php');
    }

    public function install($sql)
    {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PWD)
            or die("Can not connect to Database: " . mysqli_error($mysqli));
        echo "Driver connect to DB. Create database " . DB_NAME . '<br />';

        $mysqli->select_db(DB_NAME);
        $mysqli->query($sql)
            or die("Table does not create: " . mysqli_error($mysqli));
        echo "Apply migration...<br />";
        echo "Done! <a href='/'>[Back]</a><br />";
        echo "Now you may <a href='/?seo_proxy_action=form'>add new proxy field</a>";
    }

}