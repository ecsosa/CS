<html>
  <head>
    <title>CS 4339/5339 Assignment 3a: admin</title>
	</head>
	<body>
		<h2>Administrator Command Center</h2>
		
		<?php // query.php
			require_once 'login.php';
			session_start();
			
			if(!(isset($_SESSION['admin']) && $_SESSION['admin'] > 0)) 
				die ("You are not authorized to access this page; You need to be logged in as an administrator to access this page.");
				
			$connection = new mysqli($hn, $un, $pw, $db);
			if ($connection->connect_error) die("Fatal Error");
			
			echo '<form action="admin.php" method="post">
					<b>Add-User Form</b><br/>
					Record ID:<br/><input type="text" name="recordID"><br/>
					Username:<br/><input type="text" name="username"><br/>
					Password:<br/><input type="password" name="password"><br/>
					Re-Type Password:<br/><input type="password" name="passwordCheck"><br/>
					Is Administrator:	<input type="checkbox" name="isAdmin"><br/><br/>
					<input type="hidden" name="adduser" value="true">
					<input type="submit" value="Add User">
				 </form>
				 <form action="admin.php" method="get">
					<input type="hidden" name="showusers" value="true">
					<input type="submit" value="Show Users">
				 </form>';
			
			if(isset($_POST['adduser']))
			{	
				$username = htmlspecialchars($_POST['username']);
				$recordID = htmlspecialchars($_POST['recordID']);
				$password = htmlspecialchars($_POST['password']);
				$passwordCheck = htmlspecialchars($_POST['passwordCheck']);
				
				$query   = "SELECT * FROM users WHERE recordID='$recordID'";
				$result  = $connection->query($query);
				$row = $result->fetch_array(MYSQLI_NUM);
				
				$query   = "SELECT * FROM records WHERE recordID='$recordID'";
				$result  = $connection->query($query);
				$row2 = $result->fetch_array(MYSQLI_NUM);
				
				// Used for debugging
				//echo "existing username=" . $row[2] . " existing recordID=" . $row[3] . " requested record id=" . $row2[11];
				
				// Make sure all required information is filled in
				if($recordID == '' ||$username == ''||$password == ''||$passwordCheck == '')
				{
					echo "<script type='text/javascript'>alert('Please enter all required information.');</script>";
				}
				
				// Check if the username already exists
				else if ($row[2] == $username)
				{
					echo "<script type='text/javascript'>alert('Username already exist.');</script>";
				}
				
				// Check if the student exists in the list of CS graduates
				// a) student doesnt exists
				else if ($row2[11] != $recordID)
				{
					echo "<script type='text/javascript'>alert('Student Record Not Found.');</script>";
				}
				
				// b) student exists, already has profile
				else if ($row2[11] = $row[3])
				{
					echo "<script type='text/javascript'>alert('Student already has a profile.');</script>";
				}
				
				// Make sure the user entered the password correctly
				else if($password != $passwordCheck){
					echo "<script type='text/javascript'>alert('The passwords do not match.');</script>";
				}
				
				// Conditions met, ready to hash the password and add the user
				else
				{
					$forename = htmlspecialchars( $row2[3] );
					$surname = htmlspecialchars( $row2[2] );
					
					// the hash is being salted by concatinating the the constant salt (in login.php), username, and the password.
					$hash = password_hash($constant_salt.$username.$password, PASSWORD_DEFAULT);
					
					$isAdmin = isset($_POST['isAdmin']);
					add_user($connection, $forename, $surname, $username, $recordID, $hash, $isAdmin);
				}
				
				$result->close();
			}

			if(isset($_GET['showusers']))
			{
				$query  = "SELECT * FROM users";
				$result = $connection->query($query);
				if (!$result) die("Fatal Error");

				$rows = $result->num_rows;
				
				if($rows < 1) echo 'No users in the database.<br/>';
				else echo '<b>All User</b><br/>';

				for ($j = 0 ; $j < $rows ; ++$j)
				{
					$result->data_seek($j);
					echo 'Forename: '	. htmlspecialchars($result->fetch_assoc()['forename'])	. '<br/>';
					$result->data_seek($j);
					echo 'Surname: '	. htmlspecialchars($result->fetch_assoc()['surname'])	. '<br/>';
					$result->data_seek($j);
					echo 'Username: '	. htmlspecialchars($result->fetch_assoc()['username'])	. '<br/>';
					$result->data_seek($j);
					$isAdmin = (htmlspecialchars($result->fetch_assoc()['admin']) > 0 ? "Yes" : "no");
					echo 'Admin: '		. $isAdmin . '<br/><br/>';
				}

				$result->close();
			}
			
			function add_user($connection, $fn, $sn, $un, $sr, $pw, $ia)
			{
				$stmt = $connection->prepare('INSERT INTO users VALUES(?,?,?,?,?,?)');
				$stmt->bind_param('ssssss', $fn, $sn, $un, $sr, $pw, $ia);
				$stmt->execute();
				$stmt->close();
				echo "<script type='text/javascript'>alert('New user added.');</script>";
			}
			
			$connection->close();
		?>
		
		<form action="mainpage.php">
			<input type="submit" value="Main Page">
		</form>
		
		<form action="mainpage.php" method="get">
			<input type="hidden" name="signout" value="true">
			<input type="submit" value="Sign Out">
		</form>
	</body>
</html>