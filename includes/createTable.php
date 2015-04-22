<?php
	//start debugging
	error_reporting(E_ALL);
	ini_set('display_errors', True);
	//end debugging
	
	$path = $_SERVER['DOCUMENT_ROOT'];	
	//include("$path/includes/db.inc.php");
	
	// define("COOKIE_TIME_OUT", 60); //specify cookie timeout in days (default is 10 days)
	// define('SALT_LENGTH', 9); // salt for password

	// // Password and salt generation
	// function PwdHash($pwd, $salt = null)
	// {
		// if ($salt === null)     {
			// $salt = substr(md5(uniqid(rand(), true)), 0, SALT_LENGTH);
		// }
		// else     {
			// $salt = substr($salt, 0, SALT_LENGTH);
		// }
		// return $salt . sha1($pwd . $salt);
	// }

	// $query = "
		// CREATE TABLE users
		// (
		   // userid BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
		   // md5userid VARCHAR(200),
		   // displayname VARCHAR(200),
		   // email VARCHAR(220) NOT NULL UNIQUE,
		   // password VARCHAR(220) NOT NULL,
		   // userlevel TINYINT NOT NULL DEFAULT 0,
		   // ip VARCHAR(200) NOT NULL,
		   // activationcode INT NOT NULL DEFAULT 0,
		   // regdate DATETIME NOT NULL DEFAULT GETDATE(),
		   // approved TINYINT NOT NULL DEFAULT 0,
		   // ctime VARCHAR(220),
		   // ckey VARCHAR(220)
		// );
	// ";
	
	// $query = "
		// CREATE TABLE accounts
		// (
		   // accountid BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
		   // userid BIGINT NOT NULL,
		   // accountname VARCHAR(220) NOT NULL
		// );
	// ";
	
	//$user_ip = $_SERVER['REMOTE_ADDR'];
	//$sha1pass = PwdHash('test123');
	//$activ_code = rand(1000,9999);
	
	// $query = "
		// INSERT into `users`
		// (`email`,`password`,`userlevel`,`ip`,`activationcode`,`approved`)
		// VALUES
		// ('brianthefro@gmail.com','$sha1pass',1,'$user_ip','$activ_code',1)
	// ";
	
	// $query = "
		// INSERT into `accounts`
		// (`userid`,`accountname`)
		// VALUES
		// (1,'blah')
	// ";
	
	// $query = "SELECT * FROM users";
	
	// $query = "SELECT * FROM accounts";
	
	// $query = "DROP TABLE users";
	
	// $result = QueryWebsite($query);
	
	// for ( $i = 0; $i < Num_Rows($result); $i++ ) {
		// $email = Result($result, $i, "email");
		// $password = Result($result, $i, "password");
		
		// echo "$email - $password<br />";
	// }
	
	// for ( $i = 0; $i < Num_Rows($result); $i++ ) {
		// $accountid = Result($result, $i, "accountid");
		// $accountname = Result($result, $i, "accountname");
		
		// echo "$accountid - $accountname<br />";
	// }
	
	// $query = "SELECT * FROM accounts";
	// $database=ConnectToWebsite();
	// $result = mssql_query($query, $database); 
	// if (!$result) { 
		// $message = 'ERROR: ' . mssql_get_last_message(); 
		// return $message; 
	// } else { 
		// $i = 0; echo '<html><body><table><tr>'; 
		// while ($i < mssql_num_fields($result)) { 
			// $meta = mssql_fetch_field($result, $i);
			// echo '<td>' . $meta->name . '</td>'; 
			// $i = $i + 1; 
		// } 
		// echo '</tr>'; 
		// while ( ($row = mssql_fetch_row($result))) { 
			// $count = count($row); 
			// $y = 0; 
			// echo '<tr>'; 
			// while ($y < $count) { 
				// $c_row = current($row); 
				// echo '<td>' . $c_row . '</td>'; 
				// next($row); $y = $y + 1; 
			// } 
			// echo '</tr>'; 
		// } 
		// mssql_free_result($result); 
		// echo '</table></body></html>'; 
	// }
	
	$database = @mssql_connect( "69.147.250.178", "phpAccess1", "?Acc1Access*");
	mssql_select_db("WS1",$database);
	
	//$query = "UPDATE PlayerPortalLogin SET UserLevel = 2 WHERE UserLevel = 1";
	
	//mssql_query($query, $database) or die (mssql_get_last_message());
	
	$query = "SELECT convert(varchar(10), getdate(), 111)";
			$result = mssql_query($query, $database) or die (mssql_get_last_message());
			list($date) = mssql_fetch_row($result);
			
			$query = "select sID1, sID2, sID3 from dbo.udfGetNextItemID('$date')";
			$result = mssql_query($query, $database) or die (mssql_get_last_message());
			
			if (!$result) { 
				$message = 'ERROR: ' . mssql_get_last_message(); 
				return $message; 
			} else { 
				$i = 0; 
				echo '<html><body><table><tr>'; 
				while ($i < mssql_num_fields($result)) { 
					$meta = mssql_fetch_field($result, $i); 
					echo '<td>' . $meta->name . '</td>'; 
					$i = $i + 1; 
				} 
				echo '</tr>'; 
				while ( ($row = mssql_fetch_row($result))) { 
					$count = count($row); 
					$y = 0; 
					echo '<tr>'; 
					while ($y < $count) { 
						$c_row = current($row); echo '<td>' . $c_row . '</td>'; 
						next($row); $y = $y + 1; 
					} 
					echo '</tr>'; 
				} 
				mssql_free_result($result); echo '</table></body></html>'; 
			}
	
	// $database = @mssql_connect( "174.34.166.234", "HBWeb02", "?MonthVagas");
	// mssql_select_db("HelbreathWebsite",$database);
	
	// echo $database . "<br /><br />";
  
	// if($database == false)
	// {
		// echo("A connection to the database could not be established!<br /><br />");
	// } else {
		// echo("Connected!<br /><br />");
	// }
	
	// define ("DB_HOST", "174.34.166.234"); // set database host
	// define ("DB_USER", "HBWeb02"); // set database user
	// define ("DB_PASS", "?MonthVagas"); // set database password
	// define ("DB_NAME", "HelbreathWebsite"); // set database name
	
	// $link = mysql_connect(DB_HOST, DB_USER, DB_PASS) or die("Couldn't make connection.");
	// $db = mysql_select_db(DB_NAME, $link) or die("Couldn't select database");
	
	// $serverinfo["server"] = "69.147.250.178";
	// $serverinfo["username"] = "phpAccess1";
	// $serverinfo["password"] = "?Acc1Access*";

	// echo("Executing query: $query<br /><br />");

	// $result = mssql_query($query, $database);
	
	// if($result == false)
	// {
		// //echo("No result set specified!");
		// $message = 'ERROR: ' . mssql_get_last_message(); 
		// echo($message); 
	// } else {
		// echo("Result OK!");
	// }
?>