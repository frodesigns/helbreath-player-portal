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
		
		if($a) {
			$sql_delete = "DELETE FROM PlayerPortalLinkedAccount WHERE PPLinkedAccountID = '$a' AND PPLoginID = '$user_id'";
			
			QueryWS1($sql_delete) or die("Delete Failed:" . mssql_get_last_message());
			
			echo json_encode(array('success' => true, 'message' => 'Success!'));
		}  else { 
			echo json_encode(array('success' => false, 'message' => 'Error: Missing account ID.'));
		}
	}
?>