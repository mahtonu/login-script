<?php
require_once 'UserApplication.php';
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
        
        if (!$user->logged_in) {
        ?>

            <h2>User Login</h2>
            <br />
            <?php
            /**
             * User not logged in, display the login form.
             */
            if ($validator->num_errors > 0) {
                echo "<font size='2' color='#ff0000'>" . $validator->num_errors . " error(s) found</font>";
            }
            ?>
            
            <form action="UserApplication.php" method="POST">
                Useremail: <br />
                <input type="text" name="useremail" value="<?= $validator->getValue("useremail") ?>"> <? echo "<font size='2' color='#ff0000'>".$validator->getError("useremail")."</font>"; ?>
                <br />
                Password:<br />
                <input type="password" name="password" value=""> <? echo "<font size='2' color='#ff0000'>".$validator->getError("password")."</font>"; ?>
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
