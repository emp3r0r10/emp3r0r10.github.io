<?php
	function is_safe_url($url) {
	    return !preg_match('/^(http:\/\/)?(localhost|127\.0\.0\.1)/', $url);
	}
	if (isset($_GET['url'])) {
	    $target_url = $_GET['url'];
	    if (!is_safe_url($target_url)) {
	        header('Location: Detected.html');
	    }
    	$response = file_get_contents($target_url);
        echo $response;
	}
?>