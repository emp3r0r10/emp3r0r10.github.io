<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style type="text/css">
        body {
            margin: 0;
            font-family: Roboto, sans-serif;
            background-color: #09B5E9;
/*            color: white;*/
        }

        .btn {
            text-decoration: none;
            background-color: lightblue;
            border: none;
            border-radius: 10px;
            font-size: 30px;
            padding: 5px;
            float:right;
            color: white;
            margin: -3.7rem 1rem 0 1rem;
        }
        .container {
            display: flex;
            justify-content: space-between;
            background-color: #fff;
            padding: 20px;
            border: none;
            border-radius: 20px;
            margin: 2rem 20rem 0 20rem;
        }

        .column {
            flex: 1;
            padding: 20px;
            border-right: 1px solid #ccc;
        }

        .column:last-child {
            border-right: none;
        }
        .nav {
            list-style-type: none;
            margin: 1rem 10rem 0 10rem;
            padding: 0;
            overflow: hidden;
        }
        .footer {
            margin-top: 5rem;
            left: 0;
            bottom: 0;
            text-align: center;
        }
        .border {
            border-top-width: 500px;
            border-top: 2px solid gray;
            width: 600px;
            margin-left: 36rem;
        }
    </style>
</head>
<body>
    <div class="nav">
            <h1>Welcome, <?php echo $username; ?></h1>
            <a href="logout.php" class="btn">Logout</a>
    </div>        
    <div class="container">
        <div class="column">
            <h3>Users</h3>
            <p>angel</p>
            <p>bubbles</p>
            <p>shimmer</p>
            <p>angelic</p>
            <p>bubbly</p>
            <p>glimmer</p>
            <p>baby</p>
            <p>pink</p>
            <p>little</p>
            <p>butterfly</p>
            <p>sparkly</p>
            <p>doll</p>
            <p>sweet</p>
            <p>sparkles</p>
            <p>dolly</p>
            <p>sweetie</p>
            <p>sprinkles</p>
            <p>lolly</p>
            <p>princess</p>
            <p>fairy</p>
            <p>honey</p>
            <p>snowflake</p>
            <p>pretty</p>
            <p>sugar</p>
            <p>cherub</p>
            <p>lovely</p>
            <p>blossom</p>
        </div>
        <div class="column">
            <h3>Permissions</h3>
            <p>user</p>
            <p>manager</p>
            <p>engineer</p>
            <p>doctor</p>
            <p>teacher</p>
            <p>artist</p>
            <p>designer</p>
            <p>administrator</p>
            <p>user</p>
            <p>manager</p>
            <p>engineer</p>
            <p>doctor</p>
            <p>teacher</p>
            <p>artist</p>
            <p>designer</p>
            <p>administrator</p>
            <p>user</p>
            <p>manager</p>
            <p>engineer</p>
            <p>doctor</p>
            <p>teacher</p>
            <p>artist</p>
            <p>designer</p>
            <p>administrator</p>
            <p>user</p>
            <p>manager</p>
            <p>engineer</p>
            <p>doctor</p>
        </div>
        <div class="column">
            <h3>Status</h3>
            <p>online</p>
            <p>offline</p>
            <p>online</p>
            <p>offline</p>
            <p>online</p>
            <p>offline</p>
            <p>online</p>
            <p>offline</p>   
            <p>online</p>
            <p>offline</p>
            <p>online</p>
            <p>offline</p>
            <p>online</p>
            <p>offline</p>
            <p>online</p>
            <p>offline</p>   
            <p>online</p>
            <p>offline</p>
            <p>online</p>
            <p>offline</p>
            <p>online</p>
            <p>offline</p>
            <p>online</p>
            <p>offline</p>   
            <p>online</p>
            <p>offline</p>
            <p>online</p>
            <p>offline</p>
        </div>
    </div>
    <div class="footer">
        <div class="border"></div>
        <p>Copyright © 2024 Bootstrap</p>
    </div>
</body>
</html>
