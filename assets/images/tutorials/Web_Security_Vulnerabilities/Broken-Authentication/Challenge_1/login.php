<?php
session_start();
if (isset($_POST['username']) && isset($_POST['password'])) {
    if ($_POST['username'] === 'James' && $_POST['password'] === '*T0P_S3CR3T') {
        $_SESSION['username'] = 'James';
        header('Location: dashboard.php');
        exit;
    } else {
        $error = '<div style="color:red;background-color:#FF9999;width:15%;margin-left:46rem;padding-top:4px;"><p style="padding-bottom:18px;">Invalid username or password</p></div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
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
    <?php if (isset($error)) { ?>
        <p><?php echo $error; ?></p>
    <?php } ?>
    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <button type="submit" class="btn" name="login">Login</button>
    </form>
</body>
</html>
