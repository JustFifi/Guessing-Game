<?php
	$min = 1;
	$max = 10;
	
	// start the session with a persistent cookie of 1 year
	$lifetime = 60 * 60 * 24 * 365;	// 1 year cookie
	session_set_cookie_params($lifetime, '/');
	session_start();
	
	$msg = new MyMessages;
	
	$msie = strpos($_SERVER["HTTP_USER_AGENT"], 'MSIE') ? true : false;
	$firefox = strpos($_SERVER["HTTP_USER_AGENT"], 'Firefox') ? true : false;
	$safari = strpos($_SERVER["HTTP_USER_AGENT"], 'Safari') ? true : false;
	$chrome = strpos($_SERVER["HTTP_USER_AGENT"], 'Chrome') ? true : false;
	
	class MyMessages {
		public $fail = "<h1>You failed to guess the correct number, please try again.</h1>";
		public $success = "<h1>Congratulations, you guessed the correct number in <span>_guess_</span> guess!</h1>";
		public $direction = "<p>You should pick a number that is <span>_direction_</span>.</p>";
		public $success_obj = "<a href=\"index.php\"><img src=\"gd.php?text=Play%20Again?\" name=\"again\" alt=\"Play Again?\"></a>";
		public $start = "<h1>Pick a number from the list above to begin the guessing game!</h1>";
		public $guesses = "<p>Total Guesses: <span>_guess_</span></p>";
	}

	function build_buttons() {
		$a = "<form method=\"post\" action=\"index.php\">";
		for ($i = 1;$i <= 10;$i++) {
			$a .= "<button name=\"number\" value=\"$i\"><img src=\"gd.php?text=$i\" alt=\"Button $i\"></button>";
		}	
		$a .= "</form>";
		
		return $a;
	}
	
	function build_list() {
		$a = "<form method=\"post\" action=\"index.php\">";
		for ($i = 1;$i <= 10;$i++) {
			$a .= "<button name=\"number\" value=\"$i\"><img src=\"gd.php?text=$i\" alt=\"Button $i\"></button>";
		}	
		$a .= "</form>";
		
		return $a;
	}
	
	function get_message($a, $b) {
		//$a = set_number
		//$b = user_guess
		global $msg, $guess_count, $_SESSION;
		
		if ($b > 0) { $guess_count++; $_SESSION['guess_count']++; }
		
		if ($a > $b AND $b != 0) {
			$m = $msg->fail;
			$m .= preg_replace('/_direction_/', 'Higher', $msg->direction);
			$m .= preg_replace('/_guess_/', $guess_count, $msg->guesses);
		}
		elseif ($a < $b AND $b != 0) {
			$m = $msg->fail;
			$m .= preg_replace('/_direction_/', 'Lower', $msg->direction);
			$m .= preg_replace('/_guess_/', $guess_count, $msg->guesses);
		}
		elseif ($b == 0) {
			$m = $msg->start;
		}
		else {
			$m = preg_replace('/_guess_/', $guess_count, $msg->success);
			if ($guess_count > 1) 
				$m = preg_replace('/!/', 'es!', $m);
			session_destroy();
			session_start();
			session_regenerate_id();
		}
		return $m;
	}
	
	function pick_number($x, $y) {
		$a = mt_rand($x, $y);
		return $a;
	}
	
	
	
	$html = "
	<!DOCTYPE html>
	<html>
		<head>
			<title>Guessing Game - Made by Rick Anderson</title>
			<meta charset=\"utf-8\">
			<link href=\"main.css\" type=\"text/css\" rel=\"stylesheet\">
		</head>
		
		<body>
			<div id=\"wrapper\">
				<div id=\"content\">
					_type_
				</div>
				
				_message_
				
			</div>
		</body>
	</html>";
	
	if (empty($_SESSION['set_number'])) {
		$_SESSION['set_number'] = pick_number($min, $max);
		$set_number = $_SESSION['set_number'];
		$_SESSION['guess_count'] = 0;
		$guess_count = $_SESSION['guess_count'];
		$user_guess = 0;
	}
	else {
		$set_number = $_SESSION['set_number'];
		$guess_count = $_SESSION['guess_count'];
		
		if (empty($_POST['number']) AND !empty($user_guess)) {
			$_POST['number'] = $_SESSION['last_number'];
		}
		else {
			if (empty($user_guess) AND empty($_POST['number'])) {
				$user_guess = 0;
			}
			else {
				$_SESSION['last_number'] = $_POST['number'];
				$user_guess = $_POST['number'];

			}
		}
		
	}
	
	$message = get_message($set_number, $user_guess);
	$chk_msg = strpos($message, 'Congratulations,') ? true : false;
	$success = $msg->success_obj;
	
	if (!$chk_msg)
		$html = preg_replace('/_type_/', build_buttons(), $html);
	elseif ($chk_msg) 
		$html = preg_replace('/_type_/', $success, $html);
	
	$html = preg_replace('/_message_/', $message, $html);
	
	print($html); // TEMP -- REMOVE AFTER
?>