<?php
namespace Service;
/**
 * User Class - Performs user services like login, registration,
 * account update, logout etc. It also deals with initiating session, cookies etc.
 */
class User {
    
    var $useremail;     //useremail given on sign-up
    var $userid;        //userid created on sign-up
    var $username;      //username given on sign-up
    var $userphone;     //userphone given on sign-up
    var $userhash;      //Random value generated to track on each login 
    var $userlevel;     //a number assigend to denote user level i.e. admin, normal or guest
    var $logged_in;     //true if user is logged in, false otherwise
    //holds own instance
    private static $_instance = null;
    
    /**
     * Class contructor, performs session startup 
     * and checks if the user is loggedin
     */
    private function __construct() {
        session_start();   //start the php session

        /* check if user is logged in */
        $this->logged_in = $this->_isLogin();

        /**
         * not loggedin users are guest users
         */
        if (!$this->logged_in) {
            $this->useremail = $_SESSION['useremail'] = GUEST_NAME;
            $this->userlevel = GUEST_LEVEL;
        }
    }

    /**
     * isLogin - Checks if the user has already previously
     * logged in, and a session with the user has already been
     * established. Also checks to see if user has been remembered.
     * If so, the database is queried to make sure of the user's
     * authenticity. Returns true if the user has logged in.
     */
    private function _isLogin() {
        $userDao = \Dao\User::getinstance();

        /* Check if user has been remembered */
        if (isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])) {
            $this->useremail = $_SESSION['useremail'] = $_COOKIE['cookname'];
            $this->userhash = $_SESSION['userhash'] = $_COOKIE['cookid'];
        }

        /* useremail and userhash have been set and not guest */
        if (isset($_SESSION['useremail']) && isset($_SESSION['userhash']) &&
                $_SESSION['useremail'] != GUEST_NAME) {
            
            /* Confirm that useremail and userhash are valid */
            if ($userDao->confirmUserHash($_SESSION['useremail'], $_SESSION['userhash']) != 0) {
                /* Variables are incorrect, user not logged in */
                unset($_SESSION['useremail']);
                unset($_SESSION['userhash']);
                unset($_SESSION['userid']);
                return false;
            }

            /* User is logged in, set class variables */
            $userinfo = $userDao->get($_SESSION['useremail']);//get user details
            $this->useremail = $userinfo['useremail'];
            $this->userid = $userinfo['id'];
            $this->userhash = $userinfo['userhash'];
            $this->userlevel = $userinfo['userlevel'];
            $this->username = $userinfo['username'];
            $this->userphone = $userinfo['phone'];
            return true;
            
        } else {
            /* User not logged in */ 
            return false;
        }
    }

    /**
     * login - validated user login details, performs user login, sets class 
     * variables with user details
     * and if user checked 'remember me' then set cookies
     */
    public function login($useremail, $password, $rememberme) {
        $userDao = \Dao\User::getinstance();
        $validator = \Service\Validator::getinstance();

        /* Useremail validation */
        $validator->validate("useremail", $useremail);
        /* Password validation */
        $validator->validate("password", $password);

        /* Return if validation errors exist */
        if ($validator->num_errors > 0) {
            return false;
        }
        
        /* validates the user access details */
        if (!$validator->validateCredentials($useremail, $password)) {
            return false;
        }


        /* useremail and password correct, register session variables */
        $userinfo = $userDao->get($useremail);//get user details
        $this->useremail = $_SESSION['useremail'] = $userinfo['useremail'];
        $this->userid = $_SESSION['userid'] = $userinfo['id'];
        $this->userhash = $_SESSION['userhash'] = $this->_generateRandID();
        $this->userlevel = $userinfo['userlevel'];
        $this->username = $userinfo['username'];
        $this->userphone = $userinfo['phone'];

        /* Insert userhash into users table */
        $userDao->updateField($this->userid, "userhash", $this->userhash);

        
        if ($rememberme == 'true') {
            setcookie("cookname", $this->useremail, time() + COOKIE_EXPIRE, COOKIE_PATH);
            setcookie("cookid", $this->userhash, time() + COOKIE_EXPIRE, COOKIE_PATH);
        }

        /* Login completed successfully */
        return true;
    }

    /**
     * register - validates registration info and performs user registration tasks
     */
    public function register($username, $useremail, $password, $phone) {
        $userDao = \Dao\User::getinstance();
        $validator = \Service\Validator::getinstance();

        /* Name validation */
        $validator->validate("name", $username);
        /* Email validation */
        $validator->validate("useremail", $useremail);
        /* Password validation */
        $validator->validate("password", $password);
        /* Phone number validation */
        $validator->validate("phone", $phone);
        
        /* Check if any validation error remains */
        if ($validator->num_errors > 0) {
            return false;
        }
        
        /* Check for email address availability */
        if($validator->emailExists($useremail)) {
            return false;
        }       

        /* Finally, insert the new user to database and return the boolean status */
        return $userDao->insert(array('useremail' => $useremail, 'password' => md5($password), 'username' => $username, 'phone' => $phone));
         
    }
    
    /**
     * getUser - validated given useremail and
     * returns the user informations in an array
     */
    public function getUser($useremail){
        $userDao = \Dao\User::getinstance();
        $validator = \Service\Validator::getinstance();
        
        $validator->validate("useremail", $useremail);
        
        /* Check if any validation error remains */
        if ($validator->num_errors > 0) {
            return false;
        }
        /* Check for email address availability */
        if (!$validator->emailExists($useremail)) {
            return false;
        }
        
        $userinfo = $userDao->get($useremail);//get user details
        
        if(!empty($userinfo)){
            return $userinfo;
        } 
        
        return false;
    }
    
    /**
     * update - validates and updates user informations
     * We will not allow to change useremail 
     * because this the unique indentifier of the user.
     */
    public function update($username, $phone, $password, $newPassword) {
        $userDao = \Dao\User::getinstance();
        $validator = \Service\Validator::getinstance();

        /* Name validation */
        if($username)
            $validator->validate("name", $username);
        
        /* Phone number validation */
        if($phone)   
            $validator->validate("phone", $phone);
        
        /* Check if any validation error remains */
        if ($validator->num_errors > 0) {
            return false;
        }
        
        /* Change Name */
        if ($username) {
            $userDao->updateField($this->userid, "username", $username);
        }
        
        /* Change Phone */
        if ($phone) {
            $userDao->updateField($this->userid, "phone", $phone);
        }
        
        
        
        /* Password validate & update */
        if($password && $newPassword){
            
            if($this->updatePassword($password, $newPassword)===false){
                return false;
            }
        }

        /* Success! */
        return true;
    }

    /**
     * updatePassword - validates and updates user password
     */
    public function updatePassword($password, $newPassword) {
        $userDao = \Dao\User::getinstance();
        $validator = \Service\Validator::getinstance();

        /* Password validation */
        if($password && $newPassword){
            $validator->validate("password", $password);
            $validator->validate("newpassword", $newPassword);
            
            /* Check if any validation error remains */
            if ($validator->num_errors > 0) {
                return false;
            }
            
            /* Check whether the passowrd belongs to logged in user */
            if ($validator->checkPassword($this->useremail, $password)===false) {
                return false;
            }
            
            /* Update password since there were no errors */
            $userDao->updateField($this->userid, "password", md5($newPassword));
        }

        return true;
    }

    /**
     * isAdmin - Returns true if currently logged in user is
     * an administrator, false otherwise.
     */
    public function isAdmin() {
        return ($this->userlevel == ADMIN_LEVEL ||
                $this->useremail == ADMIN_EMAIL);
    }

    /**
     * _generateRandID - Generates a string made up of randomized
     * letters (lower and upper case) and digits and returns
     * the md5 hash of it to be used as a userhash.
     */
    private function _generateRandID() {
        return md5($this->_generateRandStr(16));
    }

    /**
     * generateRandStr - Generates a string made up of randomized
     * letters (lower and upper case) and digits, the length
     * is a specified parameter.
     */
    private function _generateRandStr($length) {
        $randstr = "";
        for ($i = 0; $i < $length; $i++) {
            $randnum = mt_rand(0, 61);
            if ($randnum < 10) {
                $randstr .= chr($randnum + 48);
            } else if ($randnum < 36) {
                $randstr .= chr($randnum + 55);
            } else {
                $randstr .= chr($randnum + 61);
            }
        }
        return $randstr;
    }

    /**
     * logout - makes the user logout, unsets cookies or sessions
     * and demotes the user as guest
     */
    public function logout() {
        /**
         * Delete cookies - the time must be in the past,
         * so just negate what you added when creating the
         * cookie.
         */
        if (isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])) {
            setcookie("cookname", "", time() - COOKIE_EXPIRE, COOKIE_PATH);
            setcookie("cookid", "", time() - COOKIE_EXPIRE, COOKIE_PATH);
        }

        /* Unset PHP session variables */
        unset($_SESSION['useremail']);
        unset($_SESSION['userhash']);

        /* Reflect fact that user has logged out */
        $this->logged_in = false;

        /* Set user level to guest */
        $this->useremail = GUEST_NAME;
        $this->userlevel = GUEST_LEVEL;
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
