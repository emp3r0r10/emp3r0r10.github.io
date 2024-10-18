<?php
// $xss = $_GET['name'];
if (isset($xss)) {
    $replacer = preg_replace("<script>", "", $xss);
} else {
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>XSS Lab_1</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    
        <style>
            body {
                font-family: "Roboto", sans-serif;
                background-color: #148692;
                text-align:center;
                justify-content: center;
                margin: 10rem;
                color:white;
            }
            h1 {
                font-size: 40px;
                color: black;
            }
            label {
                font-size: 25px;
            }
            input {
                height: 25px;
            }
            div {
                border: #6A7B7D solid 4px;
                margin: 0 30rem 0 30rem;
                padding: 50px;
                /* border-width: thick; */
                box-shadow: 1px 1px 1px 1px #6A7B7D;
            }
            .btn {
                background-color: #008CBA; /* Green */
                border: none;
                color: white;
                font-size: 30px;
                color: black; 
                border: 2px solid #008CBA;
            }
            #searchResults {
                font-size: 20px;
            }
        </style>
    </head>
    <body>
        </br>Hi!</br></br>I want you to execute: ' . htmlspecialchars('alert("XSS_Challenge_1")') . '</body>';
    echo "</br></br>You can inject your payload in the GET parameter 'name' (Example: http://localhost/<font color=gree>xss1.php?xss=</font>Your_XSS_Payload)";
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
