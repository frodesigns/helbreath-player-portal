<?php
	if($_POST) {
		$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
		$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
		$rootpath = "/portal";
		
		include("$serverrootpath/includes/db.inc.php");
		include("$includepath/common.php");
		
		page_protect();
		$user_id = $_SESSION['user_id'];
	
		$a = str_replace( "'", "''", stripslashes($_POST["accountid"]) );
		$n = str_replace( "'", "''", stripslashes($_POST["newname"]) );
		
		if($a) {
			$result3 = QueryWS1("SELECT * FROM PlayerPortalLinkedAccount WHERE DisplayAlias = '$n'");
			$num3 = mssql_num_rows($result3);
			
			if ( $num3 > 0 ) { 
				echo json_encode(array('success' => false, 'message' => 'Error: Display Name already taken. Please choose another.'));
			} else {
				$sql_update = "UPDATE PlayerPortalLinkedAccount SET DisplayAlias = '$n' WHERE PPLinkedAccountID = '$a' AND PPLoginID = '$user_id'";
				
				QueryWS1($sql_update) or die("Update Failed:" . mssql_get_last_message());
				
				echo json_encode(array('success' => true, 'message' => 'Success!'));
			}
		}  else { 
			echo json_encode(array('success' => false, 'message' => 'Error: Missing fields.'));
		}
	}
?>