<?php
/**
 * @author olijen (olijenius@gmail.com)
 * abstract implementation for ActiveRecord pattern
 */
 
abstract class ActiveRecord extends BaseComponent
{
	//---Get columns of table and insert values into object
	function __construct($data = array())
	{
	   if (!empty($data)) {
            $vars = get_object_vars($this);
            foreach ($vars as $k => $v) {
                if (!empty($data[$k])) {
                    if (is_numeric($data[$k])) {
                        //
                    }
                    if ($this->isJson($data[$k])) {
                        $data[$k] = json_decode($data[$k], true);
                    }
                    $this->$k = $data[$k];
                }
            }
        }
	}
    
	//---Return table name
    public static function table()
    {
        return strtolower(get_called_class());
    }
    
	//---Save record or return false
	public function save($new = false, $primary = 'id')
	{
	    $this->beforeSave();
        $validData = array();
		$data = get_object_vars($this);
        $DB = DataBase::getDB();
        //var_dump($data);
        foreach ($data as $k => $v) {
            if ($v) {
                if       (is_numeric($v)) {
                    if ((int)$v == $v) {
                        $validData[$k] = (int)$v;
                    } else {
                        $validData[$k] = (float)$v;
                    }  
                } elseif (is_string($v)) {
                    $validData[$k] = $v;//mysql_real_escape_string($v);
                } elseif (is_array($v) || is_object($v)) {
                    $validData[$k] = json_encode($v);
                }
            }
        }
        unset($validData['id']);
		if ($new) {
			$this->id = $DB->insert(self::table(), $validData);
			return $this->id;
		} else {
            //var_dump($validData);
			return $DB->update($this->table(), '`'.$primary.'` = '.$this->$primary, $validData);
		}
        $this->afterSave();
	}
    
	//---Events
    public function beforeSave()
    {
        return;
    }
    
	//---Events
    public function afterSave()
    {
        return;
    }
    
	//---Return object of record
    public static function getObj($value, $what = '*', $column = '`id`')
	{
	    $DB = DataBase::getDB();
        $query = 
        " SELECT ".$what.
        " FROM `".self::table()."` ".
        " WHERE ".$column." = {?}";
        $params = array($value);
        
		$data = $DB->selectRow($query, $params);
		if (!empty($data)) {
            $class = get_called_class();
			return new $class($data);
		} else {
			return false;
		}
	}
    
    public static function getAll($what = '*', $where = array(), $filter = array())
    {
        $sql_where = '';
        $orderBy   = '';
        $desc 	   = '';
        $limit 	   = '';
        
        $DB 		= DataBase::getDB();
        $params 	= array();
        $sql_where  = '';
		
        if (!empty($where)) {
            $sql_where = " WHERE ";
            $first = true;
            foreach ($where as $k => $v) {
                $_and = " AND ";
                if ($first) {
                    $_and  = "";
                    $first = false;
                }
                $sql_where .= $_and." `".$k."` = {?} ";
                $params[] = $v;
            }
        }
		
		
        if (!empty($filter['orderBy'])) {
            $orderBy = "ORDER BY `".$filter['orderBy']."` ";
        }
        if (!empty($filter['desc'])) {
            $desc = ' DESC ';
        } else {
            $desc = '';
        }
        if (isset($filter['min']) && isset($filter['max'])) {
            $limit = ' LIMIT '.$filter['min'].', '.$filter['max'];
        }
		
        $query = 
        " SELECT ".$what.
        " FROM `".self::table()."` ".
		$sql_where.
		$orderBy.
		$desc.
		$limit;
		
		$data = $DB->select($query, $params);
        
		if (!empty($data)) {
			return $data;
		} else {
			return false;
		}
    }
    
    public function unique($column, $value)
    {
        $DB = DataBase::getDB();
        $query = 
        " SELECT * ".
        " FROM `".self::table()."` ".
        " WHERE `".$column."` = {?}";
        $params = array($this->$value);
        if (count($DB->select($query, $params)) == 0) {
            return true;
        } else return false;
    }
    
    public function isJson($string)
    {
        return ((is_string($string) && 
        (is_object(json_decode($string)) || 
        is_array(json_decode($string))))) ? true : false;
    }
}