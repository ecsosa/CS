<html>
  <head>
    <title>CS 4339/5339 Assignment 3a: signin</title>
  </head>
  <body>
	
	<h1>Sign In Form</h1>
	<p>Please enter your credentials.</P>
	<form action="signin.php" method="post">
		User Name:<br>
		<input type="text" name="username" value="">
		<br/>
		Password:<br/>
		<input type="password" name="password" value="">
		<br/><br/>
		<input type="submit" value="Submit">
	</form>

	<?php // signin.php
	
		require_once 'login.php';
		$connection = new mysqli($hn, $un, $pw, $db);
		
		if ($connection->connect_error) die("Fatal Error");

		if (isset($_POST['username']) &&
			isset($_POST['password']))
		{
			$un_temp = mysql_entities_fix_string($connection, $_POST['username']);
			$pw_temp = mysql_entities_fix_string($connection, $_POST['password']);
			$query   = "SELECT * FROM users WHERE username='$un_temp'";
			$result  = $connection->query($query);

		if (!$result) die("User not found");
		
		elseif ($result->num_rows)
		{
			$row = $result->fetch_array(MYSQLI_NUM);

			$result->close();

			// The real password is checked against the constant salt (in login.php), the username, and the entered password
			if (password_verify($constant_salt.$un_temp.$pw_temp, $row[4]))
			{
				session_start();
				$_SESSION['forename'] = $row[0];
				$_SESSION['surname']  = $row[1];
				$_SESSION['username']  = $row[2];
				$_SESSION['recordID']  = $row[3];
				$_SESSION['admin']  = $row[5];
				
				// Debug: Used for testing signing in behavior on same page
				//echo htmlspecialchars("$row[0] $row[1] : Hi $row[0], you are now logged in as '$row[2]'");
				
				// Since user and passwd verification succeeded, we return to mainpage
				header("Location:mainpage.php");
				exit();
			}
			else die("Invalid username/password combination");
		}
		else die("Invalid username/password combination");
		}

		$connection->close();

		function mysql_entities_fix_string($connection, $string)
		{
		return htmlentities(mysql_fix_string($connection, $string));
		}	

		function mysql_fix_string($connection, $string)
		{
		if (get_magic_quotes_gpc()) $string = stripslashes($string);
		return $connection->real_escape_string($string);
		}
	?>
	
  </body>
</html>