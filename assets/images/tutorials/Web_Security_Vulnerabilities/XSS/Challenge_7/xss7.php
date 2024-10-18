<?php
$xss = $_GET['name'] ?? '';

if (!empty($xss)) {
    // Filter out alert, prompt, and confirm
    $xss = preg_replace('/\b(alert|prompt|confirm)\b/i', '', $xss);

    // Display the sanitized input
    echo "<h1>Welcome, <br>" . $xss . "</h1>";
} else {
    echo "</br>Hi!</br></br>I want you to execute: " . htmlspecialchars("alert(10)");
    echo "</br></br>You can inject your payload in the GET parameter 'name' (Example: http://localhost/1.php?name=Your_XSS_Payload)";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>XSS</title>
    <style>
        body {
            background-color: #148692;
            text-align:center;
            justify-content: center;
            margin: 20rem;
            color:white;
        }
        div {
            margin: 20rem;
            border: #6A7B7D solid 4px;
            padding: 25px;
            border-width: thick;
        }
    </style>
</head>
<body>
    <div>
        <?php if (isset($replacer)) { ?>
            <h1>Welcome, <br><?php echo $replacer; ?></h1>
        <?php } ?>
    </div>
</body>
</html>
