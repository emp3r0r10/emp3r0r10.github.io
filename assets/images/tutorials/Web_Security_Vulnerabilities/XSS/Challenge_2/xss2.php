<?php
$illegal = "#$%^&*+-[];,/{}|:<>?~";
$xss = $_GET['img'];
$x = strpbrk($xss, $illegal);

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
            padding: 20px;
            border-width: thick;
        }
    </style>
</head>
<body>

    <?php if ($x !== false) { ?>
            <h1>XSS Detected!</h1>
    <?php } ?>

    <?php if ($x == false){ ?>
        <?php if (!isset($xss)) { ?>
        </br></br>You can inject your payload in the GET parameter 'img' (Example: http://127.0.0.1/<font color=lightgreen>xss2.php?img=</font>Your_XSS_Payload)
        <?php } ?>    
        <div>
            <?php if (isset($xss)) { ?>
                <h1>Your photo looks beautiful</br></br><img src='<?php echo $xss; ?>'></h1>
            <?php } ?>
        </div>
    <?php } ?>

    
</body>
</html>
