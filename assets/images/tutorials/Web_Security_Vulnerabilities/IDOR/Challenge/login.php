<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = new mysqli('localhost', 'root', '', 'idor_lab');

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: index.php');
        exit;
    } else {
        echo '<div style="color:red;background-color:#FF9999;width:18%;height:6%;margin-left:45rem;padding-top:15px;font-size:20px;">Invalid username or password.</div>    ';
    }
}
?>
<!DOCTYPE html>
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

        }   
        label {
            font-size: 20px;
        }
        input {
            height: 25px;
        }                     
    </style>
</head>
<body>
    <h1>Login</h1>
    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <button type="submit" class="btn" name="login">Login</button>
    </form>
</body>
</html>
