<?php
namespace My\Service;
use My\Dao\UserDao;
use My\Service\ValidatorService;
/**
 * User Service Class - Performs user services like login, registration,
 * account update, logout etc. It also deals with session, cookies etc.
 */
class UserService {
    
    public $useremail;     
    private $userid;        
    public $username;      
    public $userphone;     
    private $userhash;     
    private $userlevel;    
    public $logged_in;     
    
    const ADMIN_EMAIL = "admin@mysite.com";
    const GUEST_NAME  =  "Guest";
    const ADMIN_LEVEL = 9;
    const USER_LEVEL  =  1;
    const GUEST_LEVEL = 0;

    const COOKIE_EXPIRE =  8640000;  //60*60*24*100 seconds = 100 days by default
    const COOKIE_PATH = "/";  //Available in whole domain

    public function __construct(UserDao $userDao, ValidatorService $validator) {
        
        $this->userDao = $userDao;
        $this->validator = $validator;
        
        $this->logged_in = $this->isLogin();

        if (!$this->logged_in) {
            $this->useremail = $_SESSION['useremail'] = self::GUEST_NAME;
            $this->userlevel = self::GUEST_LEVEL;
        }
    }

    private function isLogin() {

        if (isset($_SESSION['useremail']) && isset($_SESSION['userhash']) &&
                $_SESSION['useremail'] != self::GUEST_NAME) {
            
            if ($this->userDao->checkHashConfirmation($_SESSION['useremail'], $_SESSION['userhash']) === false) {
                unset($_SESSION['useremail']);
                unset($_SESSION['userhash']);
                unset($_SESSION['userid']);
                return false;
            }

            $userinfo = $this->userDao->get($_SESSION['useremail']);
            if(!$userinfo){
                return false;
            }
            
            $this->useremail = $userinfo['useremail'];
            $this->userid = $userinfo['id'];
            $this->userhash = $userinfo['userhash'];
            $this->userlevel = $userinfo['userlevel'];
            $this->username = $userinfo['username'];
            $this->userphone = $userinfo['phone'];
            return true;
            
        }
        
        if (isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])) {
            $this->useremail = $_SESSION['useremail'] = $_COOKIE['cookname'];
            $this->userhash = $_SESSION['userhash'] = $_COOKIE['cookid'];
            return true;
        }
        
        return false;
    }

    public function login($values) {
        
        $useremail = $values['useremail']; 
        $password = $values['password']; 
        $rememberme = isset($values['rememberme']);
                
        $this->validator->validate("useremail", $useremail);
        $this->validator->validate("password", $password);

        if ($this->validator->num_errors > 0) {
            return false;
        }
        
        if (!$this->validator->validateCredentials($useremail, $password)) {
            return false;
        }


        $userinfo = $this->userDao->get($useremail);
        if(!$userinfo){
            return false;
        }
        
        $this->useremail = $_SESSION['useremail'] = $userinfo['useremail'];
        $this->userid = $_SESSION['userid'] = $userinfo['id'];
        $this->userhash = $_SESSION['userhash'] = md5(microtime());
        $this->userlevel = $userinfo['userlevel'];
        $this->username = $userinfo['username'];
        $this->userphone = $userinfo['phone'];
        
        $this->userDao->update($this->userid, array("userhash" => $this->userhash));

        
        if ($rememberme == 'true') {
            setcookie("cookname", $this->useremail, time() + self::COOKIE_EXPIRE, self::COOKIE_PATH);
            setcookie("cookid", $this->userhash, time() + self::COOKIE_EXPIRE, self::COOKIE_PATH);
        }

        return true;
    }

    public function register($values) {
        $username = $values['name']; 
        $useremail = $values['useremail']; 
        $password = $values['password']; 
        $phone = $values['phone'];
                
        $this->validator->validate("name", $username);
        $this->validator->validate("useremail", $useremail);
        $this->validator->validate("password", $password);
        $this->validator->validate("phone", $phone);
        
        if ($this->validator->num_errors > 0) {
            return false;
        }
        
        if($this->validator->emailExists($useremail)) {
            return false;
        }       
        
        $ulevel = (strcasecmp($useremail, self::ADMIN_EMAIL) == 0) ? self::ADMIN_LEVEL : self::USER_LEVEL;
        
        return $this->userDao->insert(array(
            'useremail' => $useremail, 'password' => md5($password), 
            'userlevel' => $ulevel, 'username' => $username, 
            'phone' => $phone, 'timestamp' => time()
            ));
         
    }
    
    public function getUser($useremail){
        
        $this->validator->validate("useremail", $useremail);
        
        if ($this->validator->num_errors > 0) {
            return false;
        }
        if (!$this->validator->emailExists($useremail)) {
            return false;
        }
        
        $userinfo = $this->userDao->get($useremail);
        
        if($userinfo){
            return $userinfo;
        } 
        
        return false;
    }
    
    public function update($values) {
        $username = $values['name'];
        $phone = $values['phone'];
        $password = $values['password'];
        $newPassword = $values['newpassword'];
        
        $updates = array();
        
        if($username) {
            $this->validator->validate("name", $username);
            $updates['username'] = $username;
        }
        
        if($phone) {  
            $this->validator->validate("phone", $phone);
            $updates['phone'] = $phone;
        }
        
        if($password && $newPassword){
            $this->validator->validate("password", $password);
            $this->validator->validate("newpassword", $newPassword);
        }
        
        if ($this->validator->num_errors > 0) {
            return false;
        }
                
        if($password && $newPassword){
            if ($this->validator->checkPassword($this->useremail, $password)===false) {
                return false;
            }
            
            $updates['password'] = md5($newPassword);
        }
        
        $this->userDao->update($this->userid, $updates);
        
        return true;
    }

    public function isAdmin() {
        return ($this->userlevel == self::ADMIN_LEVEL ||
                $this->useremail == self::ADMIN_EMAIL);
    }

    public function logout() {

        if (isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])) {
            setcookie("cookname", "", time() - self::COOKIE_EXPIRE, self::COOKIE_PATH);
            setcookie("cookid", "", time() - self::COOKIE_EXPIRE, self::COOKIE_PATH);
        }

        unset($_SESSION['useremail']);
        unset($_SESSION['userhash']);

        $this->logged_in = false;

        $this->useremail = self::GUEST_NAME;
        $this->userlevel = self::GUEST_LEVEL;
    }

}

$userService = new \My\Service\UserService($userDao, $validator);
?>
