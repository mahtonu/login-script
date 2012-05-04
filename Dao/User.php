<?php

namespace Dao;

/**
 * User DAO Class - Objects are meant to act as Data Access Objects. 
 * Performs select, insert, update & delete operations upon 'user' table.
 * Inherits form Database class.
 */
class User extends Database {

    //db connection handler
    private static $_db = null;
    //holds own instance
    private static $_instance = null;

    private function __construct() {
        self::$_db = $this->getDb();
    }

    /**
     * get - Returns the user details from database in an array
     */
    public static function get($useremail) {
        $query = "SELECT * FROM users WHERE useremail = '$useremail' ";
        $result = self::query($query);

        /* check if mysql row found, then return fetched result */
        if (mysql_num_rows($result) > 0) {
            /* Return result array */
            return mysql_fetch_array($result);
        }
    }

    /**
     * insert - Inserts the given user info 
     * array(username, useremail, password, phone)
     * into the table. Appropriate user level is set.
     * Returns true on success, false otherwise.
     */
    public static function insert(array $values) {
        $time = time();
        /* If admin sign up, give admin user level */
        $ulevel = (strcasecmp($values['useremail'], ADMIN_EMAIL) == 0) ? ADMIN_LEVEL : USER_LEVEL;

        $query = "INSERT INTO users VALUES(
                '','" .
                $values['useremail'] . "','" .
                $values['password'] . "','" .
                '' . "','" .
                $ulevel . "','" .
                $values['username'] . "','" .
                $values['phone'] . "', " .
                $time;
        return self::query($query);
    }

    /**
     * updateField - Updates a field, specified by the field
     * parameter, in the matching user's row of the database.
     */
    public static function updateField($id, $field, $value) {
        $query = "UPDATE users SET " . $field . " = '$value' WHERE id = '$id' ";
        return self::query($query);
    }

    /**
     * getAll - return all the users from the database
     */
    public static function getAll() {
        
    }

    public static function delete($uniqueKey) {
        //intentionally left blank; we may user it later
    }

    /**
     * useremailTaken - Returns true if the useremail has
     * been taken by another user, false otherwise.
     */
    public function useremailTaken($useremail) {
        $query = "SELECT id FROM users WHERE useremail = '$useremail' ";
        $result = self::query($query);

        return (mysql_num_rows($result) > 0);
    }

    /**
     * confirmPassword - Checks whether or not the given
     * useremail is in the database, if so it checks if the
     * given password is the same password in the database
     * for that user. If the user doesn't exist or if the
     * passwords don't match up, it returns an error code
     * (1 or 2). On success it returns 0.
     */
    public function confirmPassword($useremail, $password) {

        /* Verify that user is in database */
        $query = "SELECT password FROM users WHERE useremail = '$useremail'";
        $result = self::query($query);
        if (mysql_num_rows($result) < 1) {
            return 1; //Indicates useremail failure
        }

        /* Retrieve password from result, strip slashes */
        $row = mysql_fetch_array($result);

        /**
         * Validate that password is correct, 
         * returns 0 useremail and password confirmed or 2 otherwise
         */
        return (($password == $row['password']) ? 0 : 2);
    }

    /**
     * confirmUserHash - Checks whether or not the given
     * useremail is in the database, if so it checks if the
     * given userhash is the same userhash in the database
     * for that user. 
     */
    public function confirmUserHash($useremail, $userhash) {

        /* Verify that user is in database */
        $query = "SELECT userhash FROM users WHERE useremail = '$useremail' LIMIT 1";
        $result = self::query($query);
        if (mysql_num_rows($result) < 1) {
            return 1; //Indicates useremail failure
        }

        /* Retrieve userid from result, strip slashes */
        $row = mysql_fetch_array($result);

        /**
         * Validate that userhash is correct, 
         * returns 0 useremail and userhash confirmed or 2 otherwise
         */
        return (($userhash == $row['userhash']) ? 0 : 2);
    }

    /**
     * returns the single instance of own class each time
     */
    public static function getinstance() {
        if (is_null(self::$_instance)) {
            $c = __CLASS__;
            self::$_instance = new $c;
        }

        return self::$_instance;
    }

}

?>