<?php
include("UserApplication.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
        <title></title>
    </head>
    <body>
        <?php
        
        /* menu gues here*/
        include 'menu.php';
        
        if ($user->logged_in) {
            echo '<h2>User Profile</h2>';
            
            /* get the email address from url, else logged in useremail will be used instead */
            $email = (isset($_GET['user'])?$_GET['user']:$user->useremail);
            $userinfo = $user->getUser($email);
            
            /* valid useremail and userinfo */
            if($userinfo){
                echo "Name : ".$userinfo['username']."<br />";
                echo "Email: ".$userinfo['useremail']."<br />";
                echo "Phone: ".$userinfo['phone']."<br />";
            } else {
                echo 'The user details is not available.';
            }
        }
        ?>
    </body>
</html>
