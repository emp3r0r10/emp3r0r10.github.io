<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'idor_lab');

$user_id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['user_id'];

$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

if ($user) {
    echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style type="text/css">
        body {
            background-color: #09B5E9;
            align-items: center;
            margin-top: 10rem;
            font-family: Roboto, sans-serif;
            text-align: center;
        }
        h1 {
            color:white;
            font-size:50px;
        }
                 
    </style>
</head>
<body>
    <h2>Username: '. $user['username'] . '</h2>
    <h2>Email: ' . $user['email'] . ' </h2>
    <h2>Card: ' .$user['Card_Number'] . 
    '</h2>
</body>
</html>';
} else {
    echo "User not found.";
}
if ($user['username'] == "john") {
    echo '<h2 style="color:red">I need to hide my password in more secure way than this</h2>


<!-- VDBQX1MzQ3IzdF8xMjMh -->';
}
?>
