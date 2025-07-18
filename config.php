<?php
define("MYSQL_HOSTNAME", "localhost");
define("MYSQL_USERNAME", "root");
define("MYSQL_PASSWORD", "");
define("MYSQL_DATABASE", "voting_system");

$db = new mysqli(MYSQL_HOSTNAME, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);

/* to check the connection */ 
if($db->connect_error){
    die("Connection failed: " . $db->connect_error);
}
?>
