<?php
require_once('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
    $query = $_GET['query'];

    $sql = "SELECT * FROM products WHERE name LIKE '%$query%' OR description LIKE '%$query%'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        echo '<h1>Search Results</h1>';
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<p>Name: ' . $row['name'] . '</p>';
            echo '<p>Description: ' . $row['description'] . '</p>';
            echo '<p>Price: ' . $row['price'] . '</p>';
            echo '<hr>';
        }
    } else {
        echo 'No results found.';
    }
}
?>
