<?php

if (isset($validator->statusMsg)) {
    echo "<font size='2' color='#207b00'>" . $validator->statusMsg . "</font>";
}

if ($user->logged_in) {
    echo "<h2>Welcome $user->username!</h2>";
    echo "<a href='profile.php?user=" . $user->useremail . "'>My Profile</a> | "
    . "<a href='profileedit.php'>Edit Profile</a> | "
    . "<a href='UserApplication.php?logout=1'>Logout</a> ";
}
?>
