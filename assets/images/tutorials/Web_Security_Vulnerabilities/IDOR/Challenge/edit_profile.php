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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($user) {
        $newEmail = $_POST['email'];
        $sql = "UPDATE users SET email = '$newEmail' WHERE id = $user_id";
        $conn->query($sql);
        header('Location: profile.php?id=' . $user_id);
        exit;        
    }

}

?>





<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
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
    <h1>Edit Profile</h1>
    <form method="post">
        New Email: <input type="email" name="email" value="<?php echo $user['email']; ?>"><br>
        <button type="submit" class="btn">Save</button>
    </form>
</body>
</html>
