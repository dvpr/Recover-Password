<?php
function asMain(){
	if(isset($_POST['action']) && $_POST['action'] == 'update'){
		asInit();
		if(asUserExist($_POST['username'])){
			if(asUpdate($_POST['username'], $_POST['password'])){
				wp_redirect('wp-login.php');
			}else{
				echo "Reset password failed";
			}
		}else{
			echo "User not exists";
		}
	}
}

function asInit(){
	// Display error messages.
	ini_set('display_errors',1);            //
	ini_set('display_startup_errors',1);    //
	error_reporting(-1);                    //

	// Load Wordpress system stuff.
	include("./wp-load.php");
}

function asUpdate($username, $password){
	global $wpdb;
	$sql = "UPDATE " . $wpdb->users . " SET user_pass = '"
    . md5($password) . "' WHERE user_login = '".$username."'";
	if ($link = $wpdb->query($sql)) {
	    //@unlink($_SERVER['SCRIPT_FILENAME']);
	    return true;
	} else {
	    return false;
	}
}

function asUserExist($username){
	global $wpdb;
	$sql = "SELECT * FROM " . $wpdb->users . " WHERE user_login = '".$username."'";
	$link = $wpdb->query($sql);
	if ($link) {
	    return true;
	} else {
	    return false;
	}
}

asMain();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Recover password by Dvpr</title>
    <style type="text/css">
    </style>
</head>
<body>
	<h1>Reset your Super Administrator password.</h1>
	<ol>
		<li>
			<form method="post">
				username:<input type="type" name="username">
				password:<input type="type" name="password">
				<input type="hidden" name="action" value="update">
				<input type="submit" value="Submit">
			</form>
		</li>
	</ol>
</body>
</html>
