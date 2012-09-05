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

        if ($userService->logged_in) {
            echo '<h2>User Profile</h2>';

            echo "Name : " . $userService->username . "<br />";
            echo "Email: " . $userService->useremail . "<br />";
            echo "Phone: " . $userService->userphone . "<br />";
        }
        ?>
    </body>
</html>
