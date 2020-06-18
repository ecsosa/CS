  	<!DOCTYPE html>
<html>
<head>
	
<link rel ="stylesheet" href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script>
		$(document).ready( function () {
    		$('#myTable').DataTable();
		} );
	</script>


</head>
 <body>

<h1>CS 4339 Assignment 4</h1>

<?php
	require_once 'login.php';
	session_start();
	
	echo '<table id="myTable"  >
			<thead>
				<tr>
					<th>Year</th>
					<th>Certificate<br>year</th>
					<th>Last</th>
					<th>First</th>
					<th>Department</th>
					<th>Classification</th>
					<th>Program</th>';
	// If a user is signed in, they can view more details
	if(isset($_SESSION['forename']) && isset($_SESSION['surname']))
	{
		echo '<th>Email</th>
				<th>ID</th>
				<th>NickName</th>
				<th>Phone Number</th>
				<th>Record ID</th>';
	}
	
	echo "</tr></thead>";
			
	// connecting to the list of CS graduates
	$connection = new mysqli($hn, $un, $pw, $db);
	
	if ($connection->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$sql = "SELECT * FROM records";
	$result = $connection->query($sql);
	echo "<tbody>";
	if ($result->num_rows > 0) {
	// output data of each row
		while($row = $result->fetch_assoc()) {
			$rowdata = "<tr>";
			$rowdata .= "<td>" . $row["Year"]. "</td><td>" . $row["Certificate Year"] . "</td>"; 
			$rowdata .= "<td>". $row["Last"]. "</td><td>". $row["First"]. "</td>";
			$rowdata .= "<td>". $row["Department"]. "</td><td>". $row["Classification"] . "</td>";
			$rowdata .= "<td>". $row["Degree"]. "</td>";
			
			// If a user is signed in, they can view more details
			if(isset($_SESSION['forename']) && isset($_SESSION['surname']))
			{
				$rowdata .= "<td>" . $row["Email"]. "</td><td>". $row["IDNumber"] . "</td>";
				$rowdata .= "<td>". $row["NickName"]."</td><td>". $row["PhoneNumber"]."</td>";
				$rowdata .= "<td>" . '<a href="user.php?user=' . $row["RecordID"] . '">' . $row["RecordID"] . '</a></td>';
			}
			
			$rowdata .= "</tr>";
			echo $rowdata;
		}
		
		echo "</tbody>";
		echo "</table>";
	} 
	
	else { 
		echo "0 results"; 
	}
	$connection->close();
	
	// Sign in and user related elements
	$introMessage = "<p><b>Please proceed to sign in.</b></P>";
	
	// If the user cliecked the "log out" button
	if(isset($_GET['signout']))
	{
		session_unset(); 
		session_destroy();
		$introMessage = "<p><b>Signed Out</b></P>";
	}
	
	// If a session exists for this user
	if(isset($_SESSION['forename']) && isset($_SESSION['surname']))
	{
		echo "<b>Welcome back, " . $_SESSION["forename"] . " " . $_SESSION["surname"] . ".</b><br/><br/><tr>";
		echo "<table>";
		
		// If admin, give access to admin page
		if(isset($_SESSION['admin']) && $_SESSION['admin'] > 0)
		{
			$adminPage_form =	'<td><form action="admin.php">
									<input type="submit" value="Admin Page">
								</form></td>';
			echo $adminPage_form;
		}
		
		$userPage_form =	'<td><form action="user.php">
								<input type="submit" value="User Page">
							</form></td>';
		echo $userPage_form;
		
		$signOut_form =	'<td><form action="mainpage.php" method="get">
							<input type="hidden" name="signout" value="true">
							<input type="submit" value="Sign Out">
						</form></td></tr></table>';
		echo $signOut_form;
	}
	
	// User visits without having signed in previously (no existing session)
	else
	{
		echo $introMessage;
		
		$signIn_form =	'<form action="signin.php">
							<input type="submit" value="Sign In">
						</form></br>';
		echo $signIn_form;
	}
	
?>

</body>
</html>