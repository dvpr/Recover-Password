<?php
function asMain(){
	if(isset($_POST['action']) && in_array($_POST['action'], array('update', 'create', 'file'))){
		switch ($_POST['action']) {
			case 'update':
				asInit();
				if(asUserExist($_POST['username'])){
					asUpdate($_POST['username'], $_POST['password']);
					Header("Location: " . JURI::root() . 'administrator');
				}else{
					echo "User not exists";
				}

				break;

			case 'create':
				asInit();
				if(!asUserExist($_POST['username'])){
					asCreate($_POST['username'], $_POST['password']);
					echo "Created";Header("Location: " . JURI::root() . 'administrator');
				}else{
					echo "User already exists";
				}

				break;

			case 'file':
				asInit();
				asFile($_POST['username']);
				# code...
				break;

			default:
				# code...
				break;
		}
	}
}

function asInit(){
	// Display error messages.
	ini_set('display_errors',1);            //
	ini_set('display_startup_errors',1);    //
	error_reporting(-1);                    //

	// Load Joomla system stuff.
	define('_JEXEC', 1);

	if (file_exists(__DIR__ . '/defines.php'))
	{
		include_once __DIR__ . '/defines.php';
	}

	if (!defined('_JDEFINES'))
	{
		define('JPATH_BASE', __DIR__);
		require_once JPATH_BASE . '/includes/defines.php';
	}
}

function asUpdate($username, $password){
	// Load Joomla system class for database execute.
	require_once JPATH_BASE . '/includes/framework.php';
	jimport( 'joomla.user.helper' );

	// Generate password
	$salt = JUserHelper::genRandomPassword(32);
	$crypt = JUserHelper::getCryptedPassword($password, $salt);
	$password = $crypt . ':' . $salt;

	$object = new stdClass();
	$object->username = $username;
	$object->password = $password;

	// Update in database
	try {
		$result = JFactory::getDbo()->updateObject('#__users', $object, 'username');
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}

function asCreate($username, $password){
	// Load Joomla system class for database execute.
	require_once JPATH_BASE . '/includes/framework.php';
	jimport( 'joomla.user.helper' );

	// Generate password
	$salt = JUserHelper::genRandomPassword(32);
	$crypt = JUserHelper::getCryptedPassword($password, $salt);
	$password = $crypt . ':' . $salt;

	try {
		$user = new stdClass();
		$user->name = $username;
		$user->username = $username;
		$user->password = $password;
		$user->params = '';

		// Update table users
		$resultUser = JFactory::getDbo()->insertObject('#__users', $user, 'id');

		if(isset($user->id) && $user->id > 0){
			$userGroupMap = new stdClass();
			$userGroupMap->user_id = $user->id;
			$userGroupMap->group_id = 8;
			try {
				// Update table user_usergroup_map
				$resultUserGroupMap = JFactory::getDbo()->insertObject('#__user_usergroup_map', $userGroupMap);
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		}
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}

function asFile($username){
	$configurationFilePath = JPATH_BASE.DIRECTORY_SEPARATOR."configuration.php";
	if(is_writable($configurationFilePath)){
		$arrayOldContent = file($configurationFilePath);
		array_splice($arrayOldContent, 6, 0, array('public $root_user="'.$username.'";'."\n\r"));
		file_put_contents($configurationFilePath, $arrayOldContent);
		echo "You can use ".$username." as Super Administrator now.";
	}else{
		echo "configuration.php now writeable.";
	}
}

function asUserExist($username){
	require_once JPATH_BASE . '/includes/framework.php';

	// Get a db connection.
	$db = JFactory::getDbo();

	// Create a new query object.
	$query = $db->getQuery(true);

	$query->select($db->quoteName(array('id', 'username', 'password')));
	$query->from($db->quoteName('#__users'));
	$query->where($db->quoteName('username')." = ".$db->quote($username));

	// Reset the query using our newly populated query object.
	$db->setQuery($query);

	// Load the results as a list of stdClass objects (see later for more options on retrieving data).
	$results = $db->loadObject();

	if(isset($results->id) && $results->id > 0){
		return true;
	}else{
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
	<h1>There are 3 ways to re-control your site.</h1>
	<ol>
		<li>
			<h5>Reset your Super Administrator password.</h5>
			<form method="post">
				username:<input type="type" name="username">
				password:<input type="type" name="password">
				<input type="hidden" name="action" value="update">
				<input type="submit" value="Submit">
			</form>
		</li>
		<li>
			<h5>Create a new Super Administrator.</h5>
			<form method="post">
				username:<input type="type" name="username">
				password:<input type="type" name="password">
				<input type="hidden" name="action" value="create">
				<input type="submit" value="Submit">
			</form>
		</li>
		<li>
			<h5>Let one of user as Super Administrtor.</h5>
			<form method="post">
				username:<input type="type" name="username">
				<input type="hidden" name="action" value="file">
				<input type="submit" value="Submit">
			</form>
		</li>
	</ol>
</body>
</html>

