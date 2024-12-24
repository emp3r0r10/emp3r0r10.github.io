<?php
$servername = "localhost";
$username = "root";
$password = "password"; // Change this to your MySQL password
$dbname = "SQL_TO_RCE";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
