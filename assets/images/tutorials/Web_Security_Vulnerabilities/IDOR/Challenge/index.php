<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

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
        .btn {
            background-color: lightblue;
            border: none;
            border-radius: 10px;
            font-size: 40px;
            padding: 5px;
            margin-top: 1rem;
            margin-left: 2rem;
        }
        a {
            text-decoration: none;
            color: black;
        }
    </style>
</head>
<body>
    <h1>Welcome ' . $_SESSION['username'] .'</h1>

</body>
</html>
';
echo '<button type="submit" class="btn"><a href="profile.php">View Profile</a></button>';
echo '<button type="submit" class="btn"><a href="edit_profile.php">Edit Profile</a></button>';
echo '<button type="submit" class="btn"><a href="logout.php">Logout</a></button>';
?>
