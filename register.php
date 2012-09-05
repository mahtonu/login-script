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

            <h2>User Registration</h2><br />
            <?php

            if ($validator->num_errors > 0) {
                echo "<span style=\"color:#ff0000;\">" . $validator->num_errors . " error(s) found</span>";
            }
            ?>
            
            <form action="UserApplication.php" method="POST">
                Name: <br />
                <input type="text" name="name" value="<?= $validator->getValue("name") ?>"> <? echo "<span style=\"color:#ff0000;\">".$validator->getError("name")."</span>"; ?>
                <br />
                Email: <br />
                <input type="text" name="useremail" value="<?= $validator->getValue("useremail") ?>"> <? echo "<span style=\"color:#ff0000;\">".$validator->getError("useremail")."</span>"; ?>
                <br />
                Password:<br />
                <input type="password" name="password" value=""> <? echo "<span style=\"color:#ff0000;\">".$validator->getError("password")."</span>"; ?>
                <br />
                Phone: <br />
                <input type="text" name="phone" value="<?= $validator->getValue("phone") ?>"> <? echo "<span style=\"color:#ff0000;\">".$validator->getError("phone")."</span>"; ?>
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
