<?php
namespace My\Dao;

/**
 * User DAO Class - Objects are meant to act as Data Access Objects. 
 * Performs select, insert, update & delete operations upon 'users' table.
 * Inherits form BaseDao class.
 */
class UserDao extends BaseDao {
    private $db         = null;
    
    public function __construct() {
        
        $this->db = $this->getDb();
    }

    public function get($useremail) {
        $statement = $this->db->prepare("SELECT * FROM users WHERE useremail = :useremail LIMIT 1 ");
        $statement->bindParam(':useremail', $useremail);
        $statement->execute();
                
        if ($statement->rowCount() > 0) {
            return $statement->fetch();
        }
        
        return false;
    }

    public function insert(array $values) {
        $sql = "INSERT INTO users ";
        $fields = array_keys($values);
        $vals = array_values($values);
        
        $sql .= '('.implode(',', $fields).') ';
        
        $arr = array();
        foreach ($fields as $f) {
            $arr[] = '?';
        }
        $sql .= 'VALUES ('.implode(',', $arr).') ';
        
        $statement = $this->db->prepare($sql);
        
        foreach ($vals as $i=>$v) {
            $statement->bindValue($i+1, $v);
        }
        
        return $statement->execute(); 
    }

    public function update($id, array $values) {
        $sql = "UPDATE users SET ";
        $fields = array_keys($values);
        $vals = array_values($values);
        
        foreach ($fields as $i=>$f) {
            $fields[$i] .= ' = ? ';
        }
        
        $sql .= implode(',', $fields);
        $sql .= " WHERE id = " . (int)$id ." LIMIT 1 ";
        
        $statement = $this->db->prepare($sql);
        foreach ($vals as $i=>$v) {
            $statement->bindValue($i+1, $v);
        }
        
        $statement->execute(); 
    }

    public function delete($uniqueKey) {}

    public function useremailTaken($useremail) {
        $statement = $this->db->prepare("SELECT id FROM users WHERE useremail = :useremail LIMIT 1 ");
        $statement->bindParam(':useremail', $useremail);
        $statement->execute(); 
        
        return ($statement->rowCount() > 0 );
    }

    public function checkPassConfirmation($useremail, $password) {
        
        $statement = $this->db->prepare("SELECT password FROM users WHERE useremail = :useremail LIMIT 1 ");
        $statement->bindParam(':useremail', $useremail);
        $statement->execute();
                
        if ($statement->rowCount() > 0) {
            $row = $statement->fetch();
            
            return ($password == $row['password']);
        } 
        
        return false; 
    }

    public function checkHashConfirmation($useremail, $userhash) {

        $statement = $this->db->prepare("SELECT userhash FROM users WHERE useremail = :useremail LIMIT 1");
        $statement->bindParam(':useremail', $useremail);
        $statement->execute();
                
        if ($statement->rowCount() > 0) {
            $row = $statement->fetch();

            return ($userhash == $row['userhash']);
        } 
        
        return false; 
    }

}

$userDao = new \My\Dao\UserDao;
?>