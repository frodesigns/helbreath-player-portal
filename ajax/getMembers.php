<?php
	$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	$rootpath = "/portal";
	
	include("$serverrootpath/includes/db.inc.php");
	include("$includepath/common.php");
	
	user_protect();
	if (isset($_SESSION['user_id'])) {
		$user_id = $_SESSION['user_id'];
	}

	$result = QueryWS1("SELECT DisplayAlias FROM PlayerPortalLogin WHERE Approved = 1 ORDER BY DisplayAlias ASC");
	
	$membersarray = array();
	
	for ( $i = 0; $i < Num_Rows($result); $i++ ) {
		$displayalias = Result($result, $i, "DisplayAlias");
		
		array_push($membersarray, "$displayalias");
	}
	
	echo json_encode($membersarray);
?>