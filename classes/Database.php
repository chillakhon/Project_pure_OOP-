<?php
class Database {
    private static  $instance = null;

    private  $pdo, $stm, $error=false, $result, $count;

    private function __construct()
    {
        try {
           $this->pdo = new PDO("mysql:host=" . Config::get('mysql.host') . ";dbname=" . Config::get('mysql.database'), "" . Config::get('mysql.username'), "" . Config::get('mysql.password'));
        }catch (PDOException $exception){
            die($exception->getMessage());
        }
    }

    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function query ($sql,$params = [])
    {
        $this->error = false;
        $this->stm = $this->pdo->prepare($sql);

        if (count($params))
        {
            $i = 1;
            foreach ($params as $param)
            {
                $this->stm->bindValue($i,$param);
                $i++;
            }
        }
        if (!$this->stm->execute())
        {
            $this->error = true;
        }else
        {
            $this->result = $this->stm->fetchAll(PDO::FETCH_OBJ);
            $this->count = $this->stm->rowCount();
        }
        return $this;
    }

    public function error()
    {
        return $this->error;
    }

    public function result ()
    {
            return $this->result;
    }

    public function count()
    {
        return $this->count;
    }

    public function get($table,$where = [])
    {
        return $this->action('SELECT *',$table,$where);
    }

    public function delete($table,$where = [])
    {
        return $this->action('DELETE',$table,$where);
    }

    public function insert($table,$fields = [])
    {
        $values = "";
        foreach ($fields as $field)
        {
            $values .= "?,";
        }
        $values = rtrim($values,',');

        $sql = "INSERT INTO {$table} (`" . implode('`,`', array_keys($fields))."`) VALUES (".$values.")";
       $this->query($sql,$fields);
    }

    public function update($table,$id,$fields = [])
    {
        $set = "";
        foreach ($fields as $key=>$field)
        {
            $set .= "{$key} = ?,";
        }
        $set = rtrim($set,',');
        $sql = "UPDATE {$table} SET {$set} WHERE id={$id}";
        if (!$this->query($sql,$fields)->error())
        {
            return true;
        }
        return false;
    }

    public function first()
    {
        if ($this->result){
            return $this->result()[0];
        }
        return false;
    }

    public function action($action,$table,$where = [])
    {
        if (count($where)===3)
        {
            $operators = ['=','>','<','<=','>='];
            $filed = $where[0];
            $operator = $where[1];
            $value = $where[2];
            if (in_array($operator,$operators))
            {
                $sql = "$action FROM {$table} WHERE {$filed} {$operator} ?";
                if (!$this->query($sql,[$value])->error())
                {
                    return $this;
                }
            }
        }
        return false;
    }
}
