<? 
require_once("functions.php");

session_start();

$error = false;

//  Only process if $_POST is not empty and the Login button was pressed 
if (empty($_POST) == false && $_POST['action'] == "Login") {

  $submitted_username = $_POST['username'];
  $submitted_password = $_POST['password'];

	// Connect to database and get stored password and usertype
	$mysqli = db_connect();		
	$result = $mysqli->query("SELECT password, usertype FROM Users WHERE username='$submitted_username'");
	$row = $result->fetch_row();
	$stored_password = $row[0];
	$stored_usertype = $row[1];
	$mysqli->close();
	
	// Set the session variables if the submitted password matches the stored password
	if ($submitted_password != "" && password_verify($submitted_password,$stored_password)) {
		$_SESSION['username'] = $submitted_username;
		$_SESSION['authenticated'] = true;
		// Set admin to true if the user type is admin
		if ($stored_usertype == 'admin')
	  	$_SESSION['admin'] = true;
	  // Redirect to homepage
	  header("Location: home.php");
	  die();
	}
	else {
		$error = true;
	}
}

print_html_header("Login");
print_login_bs($error);
print_html_footer();
?>

