<html>
  <head>
    <title>CS 4339/5339 Assignment 3a: user</title>
	</head>
	<body>
		<h2>User Information Page</h2>
		
		<?php // query.php
			require_once 'login.php';
			session_start();
			
			$connection = new mysqli($hn, $un, $pw, $db);
			if ($connection->connect_error) die("Fatal Error");
			
			$un_temp;
			$query;
			$result;
			$result2;
			
			// If a signed in user edited a field, save it to the database
			if(isset($_POST['update_profile_request']))
			{
				$un_temp = mysql_entities_fix_string($connection, $_SESSION['recordID']);
				
				if(isset($_POST['change_email']))
				{
					$query  = "UPDATE records SET Email='" . htmlspecialchars($_POST['user_email']) . "' WHERE RecordID='$un_temp'";
					$update_result = $connection->query($query);
					if (!$update_result) die ("Database access failed");
					else echo "<script type='text/javascript'>alert('Saved Email.');</script>";
				}
				if(isset($_POST['change_phone']))
				{
					$query  = "UPDATE records SET PhoneNumber='" . htmlspecialchars($_POST['user_phone']) . "' WHERE RecordID='$un_temp'";
					$update_result = $connection->query($query);
					if (!$update_result) die ("Database access failed");
					else echo "<script type='text/javascript'>alert('Saved Phone Number.');</script>";
				}
				if(isset($_POST['change_nickname']))
				{
					$query  = "UPDATE records SET Nickname='" . htmlspecialchars($_POST['user_nickname']) . "' WHERE RecordID='$un_temp'";
					$update_result = $connection->query($query);
					if (!$update_result) die ("Database access failed");
					else echo "<script type='text/javascript'>alert('Saved Nickname.');</script>";
				}
			}
			
			// If specified in the URL or through GET, you visit a specific user's public profile
			if(isset($_GET['user']))
			{
				// Cannot be admin!
				if($_GET['user'] == 0) die ("You are not authorized to access this page; You need to be a logged in Administrator to access this page.");
				
				// If they are not a signed in user, they cannot view other profiles
				else if(!(isset($_SESSION['username']))) die ("You are not authorized to access this page; You need to be a logged in User to view other profiles.");
				
				else
				{
					$un_temp = mysql_entities_fix_string($connection, $_GET['user']);
					$query  = "SELECT * FROM users WHERE recordID='$un_temp'";
					$result  = $connection->query($query);
					
					$query  = "SELECT * FROM records WHERE recordID='$un_temp'";
					$result2  = $connection->query($query);
				}
			}
			
			// Otherwise, youre visiting your own profile if you have no other valid arguments
			else if(isset($_SESSION['username']))
			{			
				$un_temp = mysql_entities_fix_string($connection, $_SESSION['recordID']);
				$query  = "SELECT * FROM users WHERE recordID='$un_temp'";
				$result  = $connection->query($query);
				
				$query  = "SELECT * FROM records WHERE recordID='$un_temp'";
				$result2  = $connection->query($query);
			}
			
			else die ("You are not authorized to access this page; You need to be a logged in User to access this page.");
			
			if (!$result) die("Fatal Error");

			$row = $result->fetch_array(MYSQLI_NUM);
			$row2 = $result2->fetch_array(MYSQLI_NUM);
			
			if ($row[2] == '') die("User profile does not exist.");
			
			echo '<b>User information: </b><br/>';
			echo '<table>';
			echo '<tr><th>Forename: </th><td>'	. htmlspecialchars($row[0])	. '</td>';
			echo '<tr><th>Surname: </th><td>'		. htmlspecialchars($row[1])	. '</td>';
			echo '<tr><th>Username: </th><td>'	. htmlspecialchars($row[2])	. '</td>';
			echo '<tr><th>UTEP ID: </th><td>' 	. htmlspecialchars($row2[8])	. '</td>';
			
			// if the user is viewing their own profile, they may edit these elements
			if(!(isset($_GET['user'])) || $_SESSION['recordID'] == $_GET['user'] )
			{
				echo '<tr><th>Email: </th>		
						<td>
							<form action="user.php" method="post">
								<input type="hidden" name="update_profile_request" value="true">
								<input type="hidden" name="change_email" value="true">
								<input type="text" name="user_email" value="' . htmlspecialchars($row2[7]) . '">
								<input type="submit" value="save">
							</form>
						</td>';
				 
				echo '<tr><th>Phone #: </th>
						<td>
							<form action="user.php" method="post">
								<input type="hidden" name="update_profile_request" value="true">
								<input type="hidden" name="change_phone" value="true">
								<input type="text" name="user_phone" value="' . htmlspecialchars($row2[10]) . '">
								<input type="submit" value="save">
							</form>
						</td>';
						
				echo '<tr><th>Nickname: </th>
						<td>
							<form action="user.php" method="post">
								<input type="hidden" name="update_profile_request" value="true">
								<input type="hidden" name="change_nickname" value="true">
								<input type="text" name="user_nickname" value="' . htmlspecialchars($row2[9]) . '">
								<input type="submit" value="save">
							</form>
						</td>';
			}
			
			// all other users can only see their values
			else
			{
				echo '<tr><th>Email: </th><td>'		. htmlspecialchars($row2[7])	. '</td>';
				echo '<tr><th>Phone #: </th><td>'	. htmlspecialchars($row2[10])	. '<td/>';
				echo '<tr><th>Nickname: </th><td>'	. htmlspecialchars($row2[9])	. '<td/>';
			}
			
			if($_SESSION['admin']){
				$isAdmin = (htmlspecialchars($row[5]) > 0 ? "Yes" : "no");
				echo '<tr><th>Admin: </th><td>' . $isAdmin . '<td/>';
			}
			
			echo '</table><br/>';

			$result->close();
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
		
		<form action="mainpage.php">
			<input type="submit" value="Main Page">
		</form>
		
		<form action="mainpage.php" method="get">
			<input type="hidden" name="signout" value="true">
			<input type="submit" value="Sign Out">
		</form>
	</body>
</html>