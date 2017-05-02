<? 
require_once("functions.php");

session_start();
if ($_SESSION['authenticated'] != true) {
  die("Access denied");	
}

print_html_header("Home");
print_home();
print_html_footer();
?>