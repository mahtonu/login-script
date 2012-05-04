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
        
        if (!$user->logged_in) {
            ?>

            <h2>User Registration</h2><br />
            <?php
            /**
             * User not logged in, display the login form.
             */
            if ($validator->num_errors > 0) {
                echo "<font size='2' color='#ff0000'>" . $validator->num_errors . " error(s) found</font>";
            }
            ?>
            
            <form action="UserApplication.php" method="POST">
                Name: <br />
                <input type="text" name="name" value="<?= $validator->getValue("name") ?>"> <? echo "<font size='2' color='#ff0000'>".$validator->getError("name")."</font>"; ?>
                <br />
                Useremail: <br />
                <input type="text" name="useremail" value="<?= $validator->getValue("useremail") ?>"> <? echo "<font size='2' color='#ff0000'>".$validator->getError("useremail")."</font>"; ?>
                <br />
                Password:<br />
                <input type="password" name="password" value=""> <? echo "<font size='2' color='#ff0000'>".$validator->getError("password")."</font>"; ?>
                <br />
                Phone: <br />
                <input type="text" name="phone" value="<?= $validator->getValue("phone") ?>"> <? echo "<font size='2' color='#ff0000'>".$validator->getError("phone")."</font>"; ?>
                <br /><br />
                <input type="hidden" name="register" value="1">
                <input type="submit" value="Register">
            </form>
            <br />
            Already registered? <a href="index.php">Login here</a>
        <?php
        }
        ?>
    </body>
</html>
