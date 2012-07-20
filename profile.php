<?php
require_once 'UserApplication.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        include 'menu.php';

        if ($user->logged_in) {
            echo '<h2>User Profile</h2>';

            echo "Name : " . $user->username . "<br />";
            echo "Email: " . $user->useremail . "<br />";
            echo "Phone: " . $user->userphone . "<br />";
        }
        ?>
    </body>
</html>
