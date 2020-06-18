<?php //init users
  require_once 'login.php';
  $connection = new mysqli($hn, $un, $pw, $db);
  

  $query  = "DROP TABLE users";
  $result = $connection->query($query);
  if (!$result) die ("Database access failed: could not drop table 'users'.");
  else echo "succesfully droped 'users' table.<br/>";
  
  if ($connection->connect_error) die("Fatal Error: cannot connect to database.");
  
  $query = "CREATE TABLE users (
		forename VARCHAR(32) NOT NULL,
		surname  VARCHAR(32) NOT NULL,
		username VARCHAR(32) NOT NULL UNIQUE,
		recordID VARCHAR(32) NOT NULL UNIQUE,
		password VARCHAR(255) NOT NULL,
		admin tinyint(1) NOT NULL DEFAULT '0'
	)";

  $result = $connection->query($query);
  if (!$result) die("Fatal Error: query was not accepted.");
  else echo "succesfully Created 'users' table.<br/>";

  $forename = 'admin';
  $surname  = 'admin';
  $username = 'administrator';
  $recordID = '0';
  $password = 'mysecret';
  $hash     = password_hash($constant_salt.$username.$password, PASSWORD_DEFAULT);
  
  add_user($connection, $forename, $surname, $username, $recordID, $hash, '1');

  $forename = 'Gerardo';
  $surname  = 'Carbajal';
  $username = 'ccarbajal';
  $recordID = '501';
  $password = 'acrobat';
  $hash     = password_hash($constant_salt.$username.$password, PASSWORD_DEFAULT);
  
  add_user($connection, $forename, $surname, $username, $recordID, $hash, '0');


    
  $forename = 'Carlos';
  $surname  = 'Gameros';
  $username = 'cgameros';
  $recordID = '503';
  $password = 'acrobat';
  $hash     = password_hash($constant_salt.$username.$password, PASSWORD_DEFAULT);
  
  add_user($connection, $forename, $surname, $username, $recordID, $hash, '0');


  function add_user($connection, $fn, $sn, $un, $sr, $pw, $ia)
  {
    $stmt = $connection->prepare('INSERT INTO users VALUES(?,?,?,?,?,?)');
	$stmt->bind_param('ssssss', $fn, $sn, $un, $sr, $pw, $ia);
	$stmt->execute();
	$stmt->close();
	echo "Succesfully added user '$un'!<br/>";
  }
?>