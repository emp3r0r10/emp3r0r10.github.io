<!DOCTYPE html>
<html>
<head>
    <title>Stored XSS Test Page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style>
        body {
            background-color: #148692;
            font-family: "Roboto", sans-serif;
            margin: 10rem;
        }
        h1 {
            font-size: 40px;
            color: black;
        }
        label {
            font-size: 30px;
        }
        input {
            height: 40px;
        }
        textarea {
            width: 30%;
            height: 150px;
        }
        div {
            margin: 20rem;
            border: #6A7B7D solid 4px;
            padding: 20px;
            border-width: thick;
        }
        .btn {
            background-color: #008CBA; /* Green */
            border: none;
            color: white;
            font-size: 20px;
            color: black; 
            border: 2px solid #008CBA;
        }
    </style>
</head>
<body>
    <h1><b>Guest Book</b></h1>
    <form method="POST" action="">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name"><br><br>
        <label for="message">Message:</label><br>
        <textarea id="message" name="message"></textarea><br><br>
        <input type="submit" value="Submit" class="btn">

    </form>
    <hr>
    <h2>Recent Messages:</h2>
    <ul>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $name = $_POST["name"];
            $message = $_POST["message"];
            
            // Check the length of the name parameter
            if (strlen($name) > 10) {
                echo "Name is too long.";
                exit;
            }
            
            $entry = "<li>$name: $message</li>";
            $file = fopen("guestbook.txt", "a");
            fwrite($file, $entry . PHP_EOL);
            fclose($file);
        }

        // Display recent messages
        $file = file("guestbook.txt");
        foreach ($file as $line) {
            echo $line;
        }
        ?>
    </ul>
</body>
</html>
