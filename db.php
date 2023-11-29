<?php
$sName = "localhost";
$uName = "root";
$pass = "";
$db_name = "db_ba3101";


$conn = new mysqli($sName, $uName, $pass, $db_name);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>