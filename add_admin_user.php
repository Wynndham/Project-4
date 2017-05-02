<?

if($_GET['key']!="XXX") {
	die("Access denied");
}

$password = $_GET['password'];
$username = $_GET['username'];

if ($username != "" && $password != "") {
	require_once("functions.php");
	$hash = password_hash($password, PASSWORD_BCRYPT);
	$mysqli = db_connect();
	if ($mysqli->query("INSERT INTO Users VALUES ('".$username."', '".$hash."', 'admin', '0', '0')")) 
		echo 'The following admin user was created: '.$username;
	else
		echo $mysqli->error; 
	$mysqli->close();	
}
else {
	echo 'Query parameters required';
}

?>