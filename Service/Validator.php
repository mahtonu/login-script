<?php
namespace Service;

/**
 * Validator Class - validates user inputs, authentication credentials 
 * and holds & delivers errors found.
 */
class Validator {

    var $values = array();  //Holds submitted form field values
    var $errors = array();  //Holds submitted form error messages
    var $statusMsg = null;     //Holds submitted status message
    var $num_errors;        //The number of errors in submitted form
    //holds own instance
    private static $_instance = null;
    
    /* Class constructor */
    private function __construct() {
        /**
         * checks & loads values and error arrays, if any
         */
        if (isset($_SESSION['value_array']) && isset($_SESSION['error_array'])) {
            $this->values = $_SESSION['value_array'];
            $this->errors = $_SESSION['error_array'];
            $this->num_errors = count($this->errors);

            unset($_SESSION['value_array']);
            unset($_SESSION['error_array']);
        } else {
            /* reset the number of errors to zero */
            $this->num_errors = 0;
        }
        /**
         * check temporary status messages
         */
        if (isset($_SESSION['statusMsg'])) {
            $this->statusMsg = $_SESSION['statusMsg'];
            unset($_SESSION['statusMsg']);
        }
    }

    /**
     * setValue - stores the field value
     */
    public function setValue($field, $value) {
        $this->values[$field] = $value;
    }

    /**
     * getValue - returns field value
     */
    public function getValue($field) {
        if (array_key_exists($field, $this->values)) {
            return htmlspecialchars(stripslashes($this->values[$field]));
        } else {
            return "";
        }
    }

    /**
     * setError - stores field value error message 
     * and incrments number of errors
     */
    private function _setError($field, $errmsg) {
        $this->errors[$field] = $errmsg;
        $this->num_errors = count($this->errors);
    }

    /**
     * getError - returns field value error message
     */
    public function getError($field) {
        if (array_key_exists($field, $this->errors)) {
            return $this->errors[$field];
        } else {
            return "";
        }
    }

    /** 
     * getErrorArray - Returns the array of error messages 
     */

    public function getErrorArray() {
        return $this->errors;
    }
    
    /**
     * validate - validates a field value 
     */
    public function validate($field, $value) {
        $valid = false;

        /* first, check for emptyness */
        if ($valid == $this->_isEmpty($field, $value)) {

            $valid = true;
            /* check for length limit */
            if ($field == "name")
                $valid = $this->_checkSize($field, $value, NAME_LENGTH_MIN, NAME_LENGTH_MAX);

            if ($field == "password" || $field == "newpassword")
                $valid = $this->_checkSize($field, $value, PASS_LENGTH_MIN, PASS_LENGTH_MAX);

            /* check for valid format */
            if ($valid)
                $valid = $this->_checkFormat($field, $value);
        }

        return $valid;
    }

    /**
     * _isEmpty - checks a field value is empty and sets error
     */
    private function _isEmpty($field, $value) {
        $value = trim($value);
        if (empty($value)) {
            $this->_setError($field, "Field value not entered");
            return true;
        }

        return false;
    }
    
    /**
     * _checkFormat - tests a field value format using regular expression
     *  and sets error (if any)
     */
    private function _checkFormat($field, $value) {

        switch ($field) {
            case 'useremail':
                /* Check if valid email address */
                $regex = "/^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*"
                        . "@[a-z0-9-]+(\.[a-z0-9-]{1,})*"
                        . "\.([a-z]{2,}){1}$/i";
                $msg = "Email address invalid";
                break;
            case 'password':
            case 'newpassword':    
                /* Check if password is not alphanumeric */
                $regex = "/^([0-9a-z])+$/i";
                $msg = "Password not alphanumeric";
                break;
            case 'name':
                /* Check if name must not numeric */
                $regex = "/^([a-z ])+$/i";
                $msg = "Name must be alphabetic";
                break;
            case 'phone':
                /* Check phone number is not alphabetic */
                $regex = "/^([0-9])+$/";
                $msg = "Phone not numeric";
                break;
            default:;
        }

        /* Tests against regular expression */
        if (!preg_match($regex, ( $value = trim($value)))) {
            $this->_setError($field, $msg);
            return false;
        }

        return true;
    }
    
    /**
     * _checkSize - check the field value is within given size limit
     */
    private function _checkSize($field, $value, $minLength, $maxLength) {
        $value = trim($value);
        
        /* checks if the value within minimum and maximum length */
        if (strlen($value) < $minLength) {
            $this->_setError($field, "The field value is too short");
            return false;
        } else if (strlen($value) > $maxLength) {
            $this->_setError($field, "The field value is too big");
            return false;
        }

        return true;
    }
    
    /**
     * validateCredentials - validates login details 
     * and returns true if valid or false either.
     */
    public function validateCredentials($useremail, $password) {
        $userDao = \Dao\User::getinstance();
        
        /* Checks that useremail is in database and associated password is correct */
        $useremail = stripslashes($useremail);
        $result = $userDao->confirmPassword($useremail, md5($password));

        /* Check error codes */
        if ($result == 1) {
            $this->_setError("useremail", "Email address not found");
            return false;
        } elseif ($result == 2) {
            $this->_setError("password", "Invalid password");
            return false;
        }
        
        return true;
    }
    
    /**
     * emailExists - Checks if given email address is exists in database
     */
    public function emailExists($useremail) {
        $userDao = \Dao\User::getinstance();
        
        /* if useremail alreday taken then set error */
        if ($userDao->useremailTaken($useremail)) {
            $this->_setError('useremail', "Email already in use");
            return true;
        } 
        
        return false;
    }
    
    /**
     * checkPassword - checks the given passowrd belongs to the given email address or not
     */
    public function checkPassword($useremail, $password) {
        $userDao = \Dao\User::getinstance();
        
        /* Checks that useremail is in database and associated password is correct */
        $useremail = stripslashes($useremail);
        $result = $userDao->confirmPassword($useremail, md5($password));

        /* Check error codes */
        if ($result != 0) {
            $this->_setError("password", "Current password incorrect");
            return false;
        }
        
        return true;
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