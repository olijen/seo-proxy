<?php

class Users extends ActiveRecord
{
	public $id          = NULL;
	public $username    = NULL;
    public $fio         = NULL;
	public $password    = NULL;
	public $status      = NULL;
    public $email       = NULL;


    public function save($new = false, $primary = 'id')
    {
        if (parent::save($new, $primary)) {
            $_SESSION['usr']['upd'] = 1;
            return true;
        } else return false;
    }
    
	public static function getUserObj($value, $what = '*', $column = '`id`')
	{
	    $DB = DataBase::getDB();
        $query = 
        " SELECT ".$what.
        " FROM `users` ".
        " WHERE ".$column." = {?}";
        
        $params = array($value);
        
		$userData = $DB->selectRow($query, $params);
        
		if (!empty($userData)) {
			return new Users($userData);
		} else {
			return false;
		}
	}
 }