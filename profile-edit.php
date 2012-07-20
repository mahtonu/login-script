<?php
require_once __DIR__ . '/UserApplication.php';
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
            ?>

            <h2>Edit Profile</h2><br />

            <?php

            if ($validator->num_errors > 0) {
                echo "<span style=\"color:#ff0000;\">" . $validator->num_errors . " error(s) found</span>";
            }
            ?>

            <form action="UserApplication.php" method="POST">
                Name: <br />
                <input type="text" name="name" value="<?php echo ($validator->getValue("name") != "") ? $validator->getValue("name") : $user->username; ?>"> <?php echo "<span style=\"color:#ff0000;\">" . $validator->getError("name") . "</span>"; ?>
                <br />
                Password:<br />
                <input type="password" name="password" value=""> <?php echo "<span style=\"color:#ff0000;\">" . $validator->getError("password") . "</span>"; ?>
                <br />
                New Password: <font size="2">(Leave blank to remain password unchanged)</font><br />
                <input type="password" name="newpassword" value=""> <?php echo "<span style=\"color:#ff0000;\">" . $validator->getError("newpassword") . "</span>"; ?>
                <br />
                Phone: <br />
                <input type="text" name="phone" value="<?php echo ($validator->getValue("phone") != "") ? $validator->getValue("phone") : $user->userphone; ?>"> <?php echo "<span style=\"color:#ff0000;\">" . $validator->getError("phone") . "</span>"; ?>
                <br /><br />
                <input type="hidden" name="update" value="1">
                <input type="submit" value="Save">
            </form>
            <?php
        }
        ?>
    </body>
</html>
