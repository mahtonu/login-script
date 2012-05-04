<?php
namespace Dao;

/**
 * Database Abstract Class - Provides database connection 
 * and abstract functions to be implemented by the child DAO classes
 */
abstract class Database {
    //db connection handler
    private static $_db = null;
    
    /**
     * Connects to mysql database and returns connection 
     */
    final protected function getDb(){
        if(is_null(self::$_db)){
            self::$_db = mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD) or die(mysql_error());
            mysql_select_db(DB_NAME, self::$_db) or die(mysql_error());
        }
        
        return self::$_db;
    }
    
    /**
     * mysql_query wrapper
     */
    final protected function query($query){
        return mysql_query($query, self::$_db); 
    }
    
    /* Abstract methods, invoke the child classes to implement them */
    abstract protected static function get($uniqueKey);
    abstract protected static function getAll();
    abstract protected static function insert(array $values);
    abstract protected static function updateField($id, $field, $value);
    abstract protected static function delete($uniqueKey);
    abstract public static function getinstance();
}
?>
