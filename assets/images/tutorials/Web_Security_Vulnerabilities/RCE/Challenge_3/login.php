<?php
include 'db.php';

// Get username and password from input
$user = isset($_GET['username']) ? $_GET['username'] : '';
$pass = isset($_GET['password']) ? $_GET['password'] : '';

// Vulnerable query
$sql = "SELECT * FROM users WHERE username = '$user' AND password = '$pass'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "Login successful!<br>";
    echo "Welcome, " . htmlspecialchars($user) . "!";
} else {
    echo "Invalid credentials.";
}

$conn->close();
?>
