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
        
        if (!$userService->logged_in) {
        ?>

            <h2>User Login</h2>
            <br />
            <?php

            if ($validator->num_errors > 0) {
                echo "<span style=\"color:#ff0000;\">" . $validator->num_errors . " error(s) found</span>";
            }
            ?>
            
            <form action="UserApplication.php" method="POST">
                Email: <br />
                <input type="text" name="useremail" value="<?= $validator->getValue("useremail") ?>"> <? echo "<span style=\"color:#ff0000;\">".$validator->getError("useremail")."</span>"; ?>
                <br />
                Password:<br />
                <input type="password" name="password" value=""> <? echo "<span style=\"color:#ff0000;\">".$validator->getError("password")."</span>"; ?>
                <br />
                <input type="checkbox" name="rememberme" <?=($validator->getValue("rememberme") != "")?"checked":""?>>
                <font size="2">Remember me next time </font>
                <br />
                <input type="hidden" name="login" value="1">
                <input type="submit" value="Login">
            </form>
            <br />
            New User? <a href="register.php">Register here</a>
        <?php
        }
        ?>
    </body>
</html>
