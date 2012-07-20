<?php
namespace My\Application;
use My\Service\UserService;
use My\Service\ValidatorService;
session_start();   

/* include the APIs */
require_once "Dao/BaseDao.php";
require_once "Dao/UserDao.php";
require_once "Service/ValidatorService.php";
require_once "Service/UserService.php";


/**
 * UserApplication - This application class serves as end-user application.
 * User interacts with this application via user interface and 
 * this class corresponds with service classes. 
 * This application will serve & process user login, registration, profile update etc.
 */
class UserApplication {    
    
    public function __construct(UserService $user, ValidatorService $validator) {
        
        $this->userService = $user;
        $this->validator = $validator;
        
        if (isset($_POST['login'])) {

            $this->login();
        }
        else if (isset($_POST['register'])) {

            $this->register();
        }
        else if (isset($_POST['update'])) {

            $this->update();
        }
        else if ( isset($_GET['logout']) ) {

            $this->logout();

        } 
    }
    
    public function login() {

        $success = $this->userService->login($_POST);

        if ($success) {
            $_SESSION['statusMsg'] = "Successful login!";
        } else {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $this->validator->getErrorArray();
        }
        
        header("Location: index.php");
    }
    
    public function register() {

        $success = $this->userService->register($_POST);


        if ($success) {
            $_SESSION['statusMsg'] = "Registration was successful!";
            header("Location: index.php");
        } else {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $this->validator->getErrorArray();
            header("Location: register.php");
        }
    }
    
    public function update() {

        $success = $this->userService->update($_POST);


        if ($success) {
            $_SESSION['statusMsg'] = "Successfully Updated!";
            header("Location: profile.php");
        } else {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $this->validator->getErrorArray();
            header("Location: profileedit.php");
        }
    }
    
    public function logout(){

        $success = $this->userService->logout(); 
        header("Location: index.php");
    }
}

$userApp = new \My\Application\UserApplication($user, $validator);
?>
