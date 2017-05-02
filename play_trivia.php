<? 

session_start();

require_once("functions.php");

if ($_SESSION['authenticated'] != true) {
 die("Access denied");	
}

print_html_header("Play Trivia");

// Start State
if (!$_SESSION['question_num'] || !$_POST || $_POST['action']=="Play Again") {
	$_SESSION['question_num'] = 1;
    $pointTrack = 0;
	echo '
		<p>Get 10 question from database</p>
		<form method="post" action="play_trivia.php">
			<input type="submit" name="action" value="Start">
		</form>';
}

// Display State
else if ($_POST['action']=="Start" || $_POST['action']=="Next Question") {
	//echo '
	//	<p>Display question '.$_SESSION['question_num'].'</p>
	//	<form method="post" action="play_trivia.php">
	//		<input type="submit" name="action" value="Submit">
	//	</form>';	
	$question_index = $_SESSION['question_num'] - 1;
	$current_question =  $_SESSION['questions'][ $question_index ];
	$q = $current_question[0];
	$c1 = $current_question[1];
	$c2 = $current_question[2];
	$c3 = $current_question[3];
	$c4 = $current_question[4];
	
	echo '
	<h3>Question '.$_SESSION['question_num'].'</h3>
	<p>'.$q.'</p>
	<form method="post" action="play_trivia.php">		

		<label>
			<input type="radio" name="answer" value="1">
			'.$c1.'
		</label><br>

		<label>
			<input type="radio" name="answer" value="2">
			'.$c2.'
		</label><br>

		<label>
			<input type="radio" name="answer" value="3">
			'.$c3.'
		</label><br>

		<label>
			<input type="radio" name="answer" value="4">
			'.$c4.'
		</label><br>
	
		<input type="submit" name="action" value="Submit">
	</form>';
}

// Add Feedback State
else if ($_POST['action']=="Submit") {
    
    $v = '';
	$k = '';
	foreach ($_POST as $key=>$value) {
	
		// If any value is blank, we have an error, so break out of the loop
		if ($value == "") {
			$error = true;
			break;
		}
		$v .= $value;
        break;
	}
    
	$mysqli = db_connect();
    $question_index = $_SESSION['question_num'] - 1;
	$current_question =  $_SESSION['questions'][ $question_index ];
	$q = $current_question[5];
    
    if ($v == $q) {
        $pointTrack++;
        echo '<p>Correct!</p>';
    }
    else {
        echo '<p>Incorrect...</p>';
    }
    
    echo '<p>Points: '.$pointTrack.'</p>';
	$result = $mysqli->query("SELECT question, choice1, choice2, choice3, choice4, answer FROM Questions ORDER BY RAND() LIMIT 10");
	echo $mysqli->error;
	$questions_array = array();
	while ($row = $result->fetch_row()) {
		array_push($questions_array, $row);
	}
	$result->close();
	$mysqli->close();
	//var_dump($questions_array);
	$_SESSION['questions'] = $questions_array;
	if ($_SESSION['question_num'] < 10) {
		$_SESSION['question_num']++;
		echo '
			<form method="post" action="play_trivia.php">
				<input type="submit" name="action" value="Next Question">
			</form>';		
	}
	else {
        $usr = $_SESSION['username'];
        $mysqli = db_connect();
        $stuff = $mysqli->query("SELECT points, games FROM Users WHERE username='$usr'");
        $row = $stuff->fetch_row();
        $_SESSION['points'] += $pointTrack;
        $_SESSION['games'] ++;
        $row[1]++;
		echo '
            <p> User points: '.$_SESSION['points'].'</p>
            <p> Games played: '.$_SESSION['games'].'</p>
			<form method="post" action="play_trivia.php">
				<input type="submit" name="action" value="Play Again">
			</form>
			<p><a href="home.php">Back to Home</a></p>';				
	}
}

print_html_footer();

?>