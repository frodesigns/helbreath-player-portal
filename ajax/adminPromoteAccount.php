<?php
	if($_POST) {
		$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
		$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
		$rootpath = "/portal";
		
		include("$serverrootpath/includes/db.inc.php");
		include("$includepath/common.php");
		
		page_protect();
		$user_id = $_SESSION['user_id'];
		
		$accountid = str_replace( "'", "''", stripslashes($_POST["accountid"]) );
		$admin = str_replace( "'", "''", stripslashes($_POST["admin"]) );
		
		if (checkAdmin() && $accountid != "" && $admin != "") {
			$sql_update = "UPDATE PlayerPortalLogin SET UserLevel = '$admin' WHERE PPLoginID = '$accountid'";
			
			QueryWS1($sql_update) or die("Update Failed:" . mssql_get_last_message());
			
			echo json_encode(array('success' => true, 'message' => 'Success!'));
		} else {
			echo json_encode(array('success' => false, 'message' => 'Error: Missing parameters.'));
		}

	}
?>