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
            ?>

            <h2>Edit Profile</h2><br />

            <?php

            if ($validator->num_errors > 0) {
                echo "<span style=\"color:#ff0000;\">" . $validator->num_errors . " error(s) found</span>";
            }
            ?>

            <form action="UserApplication.php" method="POST">
                Name: <br />
                <input type="text" name="name" value="<?= ($validator->getValue("name") != "") ? $validator->getValue("name") : $userService->username ?>"> <? echo "<span style=\"color:#ff0000;\">" . $validator->getError("name") . "</span>"; ?>
                <br />
                Password:<br />
                <input type="password" name="password" value=""> <? echo "<span style=\"color:#ff0000;\">" . $validator->getError("password") . "</span>"; ?>
                <br />
                New Password: <font size="2">(Leave blank to remain password unchanged)</font><br />
                <input type="password" name="newpassword" value=""> <? echo "<span style=\"color:#ff0000;\">" . $validator->getError("newpassword") . "</span>"; ?>
                <br />
                Phone: <br />
                <input type="text" name="phone" value="<?= ($validator->getValue("phone") != "") ? $validator->getValue("phone") : $userService->userphone ?>"> <? echo "<span style=\"color:#ff0000;\">" . $validator->getError("phone") . "</span>"; ?>
                <br /><br />
                <input type="hidden" name="update" value="1">
                <input type="submit" value="Save">
            </form>
            <?php
        }
        ?>
    </body>
</html>
