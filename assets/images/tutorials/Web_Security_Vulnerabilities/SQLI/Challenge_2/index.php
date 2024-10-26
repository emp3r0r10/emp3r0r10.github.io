<?php
// Database connection configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sqlinjection";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process search
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];

    // Vulnerable SQL query - no input sanitization
    $sql = "SELECT * FROM products WHERE name LIKE '%$searchTerm%'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h2>Search Results:</h2>";
        while ($row = $result->fetch_assoc()) {
            echo "<p><b style='color:red'>Product: </b>" . $row['name'] . "</p>";
            echo "<p><b style='color:red'>Price:</b> " . $row['price'] . "</p>";
            echo "<p style='padding: 0 20rem 0 20rem'><b style='color:red'>Description:</b> " . $row['description'] . "</p>";
        }
    } else {
        echo "<h3>No results found.</h3>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Lab</title>
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
    <h1>Product Search</h1>
    <form method="GET" action="">
        <label>Search:</label>
        <input type="text" name="search">
        <br>
        <button type="submit" class="btn">Search</button>
    </form>
</body>
</html>
<!-- we have some products like adidas and nike -->