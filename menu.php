<?php

if (isset($validator->statusMsg)) {
    echo "<span style=\"color:#207b00;\">" . $validator->statusMsg . "</span>";
}

if ($user->logged_in) {
    echo "<h2>Welcome $user->username!</h2>";
    echo "<a href='profile.php'>My Profile</a> | "
    . "<a href='profile-edit.php'>Edit Profile</a> | "
    . "<a href='UserApplication.php?logout=1'>Logout</a> ";
}
?>
