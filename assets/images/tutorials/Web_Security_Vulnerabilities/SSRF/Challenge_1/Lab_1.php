<?php 
	if (isset($_GET['url'])) {
	    $target_url = $_GET['url'];
    	$response = file_get_contents($target_url);
        echo $response;
	}
?>