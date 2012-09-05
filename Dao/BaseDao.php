<?php
namespace My\Dao;

/**
 * BaseDao Abstract Class - Provides database connection 
 * and abstract functions to be implemented by the child DAO classes
 */
abstract class BaseDao {
    private $db         = null;

    const DB_SERVER     = "localhost";
    const DB_USER       = "root";
    const DB_PASSWORD   = "root";
    const DB_NAME       = "user";
    
    protected final function getDb(){
        $dsn = 'mysql:dbname='.self::DB_NAME.';host='.self::DB_SERVER;

        try {
            $this->db = new \PDO($dsn, self::DB_USER, self::DB_PASSWORD);
        } catch (PDOException $e) {
            throw new \Exception('Connection failed: ' . $e->getMessage());
        }
        
        return $this->db;
    }
    
    abstract protected function get($uniqueKey);
    abstract protected function insert(array $values);
    abstract protected function update($id, array $values);
    abstract protected function delete($uniqueKey);
}
?>
