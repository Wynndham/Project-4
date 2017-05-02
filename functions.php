<?

// Connects to database
function db_connect() {
	return new mysqli("localhost", "sienasel_sbxusr", "Sandbox@)!&", "sienasel_sandbox");	
}

/* ------------------------- Document Structure Functions ---------------------------- */

// Print html header with bootstrap css and page title
function print_html_header($title) {
	if ($title != "Login")
		$menu = make_menu_bs($title);

	echo '
<!DOCTYPE html>
<html lang="en">
	<head>
	  <meta charset="utf-8">
	  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	  <title>'.$title.'</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css">
		<link rel="stylesheet" href="css/style.css">
	</head>
	<body>
		<div class="container">
			'.$menu.'
			<div class="content">
	';
	if ($title != "Login")
		echo '<h1>'.$title.'</h1>';
}

// Print html footer with bootstrap js
function print_html_footer() {
	echo '
			</div> <!-- /content -->			 
		</div> <!-- /container -->			 
		<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"></script>
		<script src="js/script.js"></script>
	</body>
</html>
	';
}

/* ------------------------- Main Menu Functions ---------------------------- */

// Get main menu items
function get_menu_items($title) {
	$items['home.php'] = "Home";
	$items['play_trivia.php'] = "Play Trivia";
	$items['view_leaders.php'] = "View Leader Board";
	$items['rank_question.php'] = "Rank Questions";
	$items['insert_question.php'] = "Insert Question";
	
	if ($_SESSION['admin']) {	
		$items['insert_user.php'] = "Delete User";
		$items['delete_question.php'] = "Delete Question";
	}		
	$items['logout.php'] = "Logout";
		
	foreach ($items as $key=>$value) {
		$active = '';
		if ($value==$title) $active = "active";
		$menu_items .= '
		  <li class="nav-item">
		    <a class="nav-link '.$active.'" href="'.$key.'">'.$value.'</a>
		  </li>
		';
	}
	return $menu_items;
}

// Make main menu with basic Bootstrap classes
function make_menu($title) {;	
	$menu = '<ul class="nav nav-pills">';
  $menu .= get_menu_items($title);
	$menu .= '</ul>';
	return $menu;
}

// Make main menu with responsive Bootstrap classes
function make_menu_bs($title) {
	$menu = '
	<nav class="navbar navbar-toggleable-md navbar-light bg-faded">
  	<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" 
  	        data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    	<span class="navbar-toggler-icon"></span>
		</button>
		<a class="navbar-brand" href="home.php">Trivia</a>
		<div class="collapse navbar-collapse" id="navbarNav">
    	<ul class="navbar-nav">';
	$menu .= get_menu_items($title);
	$menu .= '
			</ul>
		</div>
	</nav>';
	return $menu;
}

/* ------------------------- Prints the homepage  ---------------------------- */

// Prints the home page content
function print_home() {
	echo '
	<p class="alert alert-info">
		Welcome <b>'.$_SESSION['username']. '</b> you can
		<a href="play_trivia.php">play trivia</a> or <a href="insert_question.php">insert questions</a>.  
		Use the main menu above to select additional options or to logout.
	</p>
	';	
}


/* ------------------------- Login Forms ---------------------------- */

// Print a basic login form
function print_login_form($error) {
	echo '
  <form method="post" action="index.php">
    <label>
    	Username: 
    	<input name="username" type="text">
    </label>
    <label>
    	Password: 
    	<input name="password" type="password">
    </label>
    <input type="submit" name="action" value="Login"> 
    '.$error.'
  </form>
  ';
}

// Print a login form with Bootstrap
function print_login_bs($error) {
	if ($error) {
		$message = '<div class="alert alert-danger">Incorrect username or password</div>';
	}
	echo '
			<form class="form-signin " method="post" action="index.php">
		    <h2 class="form-signin-heading">Login</h2>
		    
		    <label for="username" class="sr-only">Email address</label>
		    
		    <input name="username" type="text" id="username" class="form-control" placeholder="Username" required autofocus>
		    
		    
		    <label for="password" class="sr-only">Password</label>
		    <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
		    '.$message.'
		    <input class="btn btn-lg btn-primary btn-block" type="submit" name="action" value="Login">
		    
		  </form>
		 
	';
}

/* ------------------------- Reusable functions ---------------------------- */

// Print a button with specified url and text
function print_button($url, $text) {
	echo '<a href="'.$url.'" class="btn btn-primary">'.$text.'</a>';	
}


/* Print the question table. Must pass an open database connection
   Thus, you can use this with other script (insert question and delete question)
   verify that they worked */
   
function print_question_table($mysqli) {

	// First, print the columns, i.e, field names
	$result = $mysqli->query("SHOW COLUMNS FROM Questions");
	echo '<table class="table table-striped">';
	echo '<thead class="thead-inverse">';
	echo '<tr>';
	while ($row = $result->fetch_row()) {
		echo '<th>'.$row[0]."</th>";
	}
	echo '</tr>';
	echo '<thead>';
	$result->close();
	
	// Second, print all the rows
	$result = $mysqli->query("SELECT * FROM Questions ORDER BY id DESC");
	echo '<tbody>';
	while ($row = $result->fetch_row()) {
		echo '<tr>';
		foreach ($row as $value) {
			echo '<td>'.$value.'</td>';
		}
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
	$result->close();
}

/* ------------------------- Insert Questions Forms ---------------------------- */

// Print the insert question form 
function print_insert_question_form($action, $error, $p) {
  $c[$p['answer']] = "checked";
	$message = '';
	if ($error) 
		$message = "All fields must be filled out";

	echo '		
  <form method="post" action="'.$action.'">
  	
		<label>Question<br>
		<textarea name="question" rows="3">'.$p['question'].'</textarea>
		</label><br>
		
		<label>Choice 1
		<br>
		<input type="text" name="choice1" size="50" value="'.$p['choice1'].'">
		</label>
		<input type="radio" name="answer" value="1" '.$c[1].'>
		<br>
		
		<label>Choice 2
		<br>
		<input type="text" name="choice2" size="50" value="'.$p['choice2'].'">
		</label>
		<input type="radio" name="answer" value="2" '.$c[2].'>
		<br>
		
		<label>Choice 3
		<br>
		<input type="text" name="choice3" size="50" value="'.$p['choice3'].'">
		</label>
		<input type="radio" name="answer" value="3" '.$c[3].'>
		<br>
		
		<label>Choice 4
		<br>
		<input type="text" name="choice4" size="50" value="'.$p['choice4'].'">
		</label>
		<input type="radio" name="answer" value="4" '.$c[4].'>
		<br>
					
		<input type="submit" name="action" value="Insert">
		'.$message.'
	</form>
	';
}

// Print the insert question form using Bootstrap and a multicolumn responsive layout
function print_insert_question_form_bs($action, $error, $p) {
	$c[$p['answer']] = "checked";
	$message = '';
	if ($error) {
				$message = '
			<div class="alert alert-warning">
				All fields must be filled out
			</div>';
	}
	

	echo '
  <form method="post" action="'.$action.'">
  
		<!-- Question -->
		<div class="form-group row">
		  <label for="question" class="col-sm-2 col-form-label">
		  	Question
		  </label>
		  <div class="col-sm-10">
		    <textarea class="form-control" id="question" rows="2" name="question">'.$p['question'].'</textarea>
		  </div>
		</div>
		
		<!-- Column Header -->
		<div class="row hidden-xs-down">
		  <div class="col-sm-2 offset-sm-10 text-center">
		    Answer
		  </div>
		</div>
		
		<!-- Choice1 -->
		<div class="form-group row">
		  <label for="choice1" class="col-sm-2 col-form-label">
		  	Choice1
		  </label>
		  <div class="col-sm-8">
		    <input type="text" class="form-control" id="choice1" name="choice1" value="'.$p['choice1'].'">
		  </div>
		  <div class="form-check col-sm-2 text-center">
		  	<label class="form-check-label" for="answer1">
		    	<input class="form-check-input" type="radio" name="answer" id="answer1" value="1" '.$c[1].'> 
		    	&nbsp;
		    	<span class="hidden-sm-up">Answer</span>
		    </label>
		  </div>
		</div>
		
		<!-- Choice2 -->
		<div class="form-group row">
		  <label for="choice2" class="col-sm-2 col-form-label">
		  	Choice2
		  </label>
		  <div class="col-sm-8">
		    <input type="text" class="form-control" id="choice2" name="choice2" value="'.$p['choice2'].'">
		  </div>
		  <div class="form-check col-sm-2 text-center">
		  	<label class="form-check-label" for="answer2">
		    	<input class="form-check-input" type="radio" name="answer" id="answer2" value="2" '.$c[2].'>
		    	&nbsp;
		    	<span class="hidden-sm-up">Answer</span>
		    </label>
		  </div>
		</div>
		
		<!-- Choice3 -->
		<div class="form-group row">
		  <label for="choice3" class="col-sm-2 col-form-label">
		  	Choice3
		  </label>
		  <div class="col-sm-8">
		    <input type="text" class="form-control" id="choice3" name="choice3" value="'.$p['choice3'].'">
		  </div>
		  <div class="form-check col-sm-2 text-center">
		  	<label class="form-check-label" for="answer3">
		    	<input class="form-check-input" type="radio" name="answer" id="answer3" value="3" '.$c[3].'>
		    	&nbsp;
		    	<span class="hidden-sm-up">Answer</span>
		    </label>
		  </div>
		</div>
		
		<!-- Choice4 -->
		<div class="form-group row">
		  <label for="choice4" class="col-sm-2 col-form-label">
		  	Choice4
		  </label>
		  <div class="col-sm-8">
		    <input type="text" class="form-control" id="choice4" name="choice4" value="'.$p['choice4'].'">
		  </div>
		  <div class="form-check col-sm-2 text-center">
		  	<label class="form-check-label" for="answer4">
		    	<input class="form-check-input" type="radio" name="answer" id="answer4" value="4" '.$c[4].'>
		    	&nbsp;
		    	<span class="hidden-sm-up">Answer</span>
		    </label>
		  </div>
		</div>    
		
		<!-- Insert Button -->
		<div class="form-group row">	
		  <div class="col-sm-10">'.
		   $message.'
		  </div>
		  <div class="col-sm-2 text-center">
		    <input type="submit" class="btn btn-success" value="Insert">
		  </div>
		</div>
      
  </form>
  ';	
}





	
?>