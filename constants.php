<?php 
/**
 * Database Constants 
 */
define("DB_SERVER", "localhost");
define("DB_USER", "root");
define("DB_PASSWORD", "root");
define("DB_NAME", "user");

/**
 * User Constants
 */
define("ADMIN_EMAIL", "admin@mysite.com");
define("GUEST_NAME", "Guest");
define("ADMIN_LEVEL", 9);
define("USER_LEVEL",  1);
define("GUEST_LEVEL", 0);

/**
 * Field value lengths: char
 */
define("NAME_LENGTH_MIN", 5);
define("NAME_LENGTH_MAX", 100);
define("PASS_LENGTH_MIN", 8);
define("PASS_LENGTH_MAX", 32);

/**
 * Cookie Constants 
 */
define("COOKIE_EXPIRE", 60*60*24*100);  //100 days by default
define("COOKIE_PATH", "/");  //Available in whole domain

?>
