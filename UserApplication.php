<?php
include("constants.php");
/* include the APIs */
include("Dao/Database.php");
include("Dao/User.php");
include("Service/User.php");
include("Service/Validator.php");

$user = \Service\User::getinstance();
$validator = \Service\Validator::getinstance();

/**
 * UserApplication - This application class serves as end-user application.
 * User interacts with this application via user interface and 
 * this class corresponds with service classes. 
 * This application will serve & process user login, registration, profile update etc.
 */
class UserApplication {
    
    /* Class constructor */
    public function __construct() {
        global $user;
        
        /* User submitted login request */
        if (isset($_POST['login'])) {

            $this->_login();
        }
        /* User submitted registration request */ 
        else if (isset($_POST['register'])) {

            $this->_register();
        }
        /* User submitted profile update request */ 
        else if (isset($_POST['update'])) {

            $this->_update();
        }
        /* User submitted logout request */ 
        else if ( $user->logged_in && isset($_GET['logout']) ) {
            
            $this->_logout();
            
        } 
    }
    
    /**
     * _login - redirects to homepage on successful login
     */
    private function _login() {
        global $user, $validator;

        /* Login attempt */
        $success = $user->login($_POST['useremail'], $_POST['password'], isset($_POST['rememberme']));


        if ($success) {
            /* Login successful */
            $_SESSION['statusMsg'] = "Successful login!";
        } else {
            /* Login failed */
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $validator->getErrorArray();
        }

        header("Location: index.php");
    }
    
    /**
     * _register - registers a user information into data base 
     */
    private function _register() {
        global $user, $validator;

        /* Registration attempt */
        $success = $user->register($_POST['name'], $_POST['useremail'], $_POST['password'], $_POST['phone']);


        if ($success) {
            /* Registration successful */
            $_SESSION['statusMsg'] = "Registration was successful!";
            header("Location: index.php");
        } else {
            /* Registration failed */
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $validator->getErrorArray();
            header("Location: register.php");
        }
    }
    
    /**
     * _update - updates a user information 
     */
    private function _update() {
        global $user, $validator;

        /* User update attempt */
        $success = $user->update($_POST['name'], $_POST['phone'], $_POST['password'], $_POST['newpassword']);


        if ($success) {
            /* Update successful */
            $_SESSION['statusMsg'] = "Successfully Updated!";
            header("Location: profile.php");
        } else {
            /* Update failed */
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $validator->getErrorArray();
            header("Location: profileedit.php");
        }
    }
    
    /**
     * _logout - logout a user and return to index page
     */
    private function _logout(){
        global $user;

        /* logout attempt */
        $success = $user->logout(); 
        header("Location: index.php");
    }
}

/* init the user app */
$userApp = new UserApplication();
?>
