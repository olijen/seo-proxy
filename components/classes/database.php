<?php
    class DataBase {
    
    private static $db = null; // Единственный экземпляр класса, чтобы не создавать множество подключений
    private $mysqli; // Идентификатор соединения
    private $sym_query = "{?}"; // "Символ значения в запросе"
    
    public static $queryLog = false;
    
    /* Получение экземпляра класса. Если он уже существует, то возвращается, если его не было, то создаётся и возвращается (паттерн Singleton) */
    public static function getDB()
    {
        if (self::$db == null) self::$db = new DataBase();
        return self::$db;
    }
    
    public static function log($status = true)
    {
        self::$queryLog = $status;
    }
    /* private-конструктор, подключающийся к базе данных, устанавливающий локаль и кодировку соединения */
    private function __construct()
    {
        $this->mysqli = new mysqli(/*'p:'.*/DB_HOST, DB_USER, DB_PWD, DB_NAME);
        $this->mysqli->query("SET lc_time_names = 'ru_RU'");
        $this->mysqli->query("SET NAMES 'utf8'");
    }

    /* Вспомогательный метод, который заменяет "символ значения в запросе" на конкретное значение, которое проходит через "функции безопасности" */
    private function getQuery($query, $params)
    {
        if ($params) {
            for ($i = 0; $i < count($params); $i++) {
                $pos   = strpos($query, $this->sym_query);
                $arg   = "'".$this->mysqli->real_escape_string($params[$i])."'";
                $query = substr_replace($query, $arg, $pos, strlen($this->sym_query));
            }
        }
        if (self::$queryLog) {
            echo $query.'<hr>';
        }
        return $query;
    }

    /* SELECT-метод, возвращающий таблицу результатов */
    public function select($query, $params = false)
    {
        $result_set = $this->mysqli->query($this->getQuery($query, $params));
        if (!$result_set) return false;
        return $this->resultSetToArray($result_set);
    }

    /* SELECT-метод, возвращающий одну строку с результатом */
    public function selectRow($query, $params = false)
    {
        $result_set = $this->mysqli->query($this->getQuery($query, $params));
        if ($result_set->num_rows != 1) return false;
        else return $result_set->fetch_assoc();
    }

    /* SELECT-метод, возвращающий значение из конкретной ячейки */
    public function selectCell($query, $params = false)
    {
        $result_set = $this->mysqli->query($this->getQuery($query, $params));
        if ((!$result_set) || ($result_set->num_rows != 1)) return false;
        else {
          $arr = array_values($result_set->fetch_assoc());
          return $arr[0];
        }
    }

    /* НЕ-SELECT методы (INSERT, UPDATE, DELETE). Если запрос INSERT, то возвращается id последней вставленной записи */
    public function query($query, $params = false)
    {
        $success = $this->mysqli->query($this->getQuery($query, $params));
        if ($success) {
          if ($this->mysqli->insert_id === 0) return true;
          else return $this->mysqli->insert_id;
        }
        else return false;
    }
    
    //UPDATE
    public function update($table, $where, $data)
    {
        $params = array();
        $col_val = NULL;
    	foreach ($data as $column => $value){
    		//$sql = "UPDATE $table SET $column = $value WHERE $where";
    		//mysql_query($sql) or die(mysql_error());
            $col_val .= '`'.$column.'` = {?},';
            $params[] = $value;
    	}
        $query = 'UPDATE `'.$table.'` SET '.substr($col_val, 0, strlen($col_val)-1).' WHERE '.$where;
        $success = $this->mysqli->query($this->getQuery($query, $params));
        if ($success) return true;
                 else return false;
    }
    
    //UPDATE CELL
    public function updateCell($table, $column, $value, $where)
    {
        $params = array();

        $query = 'UPDATE `'.$table.'` SET `'.$column.'` = '.$value.' WHERE '.$where;

        $success = $this->mysqli->query($this->getQuery($query, array($value)));
        if ($success) return true;
                 else return false;
    }
    
    //DELETE
    public function delete($table, $where)
	{//DELETE
    	$query = 'DELETE FROM `'.$table.'` WHERE '.$where;
        $success = $this->mysqli->query($this->getQuery($query, array($value)));
        if ($success) return true;
        else return false;
    }
    
    
    public function insert($table, $data)
	{//Добавление новой строки в БД
    	$columns = "";
    	$values = "";
        $params = array();
    	foreach  ($data as $column => $value) {
    		      $columns .= ($columns == "") ? "" : ", ";
    		      $columns .= '`'.$column.'`';
    		      $values  .= ($values == "") ? "" : ", ";
    		      $values  .= '{?}';
                  $params[] = $value;
    	}
    	$query = 'INSERT INTO `'.$table.'` ('.$columns.') values ('.$values.')';

        $success = $this->mysqli->query($this->getQuery($query, $params));
        if ($success) {
            return $this->mysqli->insert_id;
        }
        else {
            return false;
        }
    }
    
    
    public function inDB($col, $table, $cel, $value)
    {
        $query = 
        "SELECT `".$this->oneWord($col)."` 
        FROM `".$this->oneWord($table)."` 
        WHERE `".$this->oneWord($cel)."` = {?}";
        
        $params = array($value);
        $res = $this->select($query, $params);
        if (count($res) == 0) return false;
        else return true;
    }
    
    /* Преобразование result_set в двумерный массив */
    private function resultSetToArray($result_set) {
    $array = array();
    while (($row = $result_set->fetch_assoc()) != false) {
      $array[] = $row;
    }
    return $array;
    }
    
    /* При уничтожении объекта закрывается соединение с базой данных */
    public function __destruct() {
        if ($this->mysqli) $this->mysqli->close();
    }
    
    public function oneWord($str)
    {
        return str_replace(' ', '', $str);
    }
}
?>