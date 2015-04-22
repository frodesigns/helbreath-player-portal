<?php
	$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	$rootpath = "/portal";
	
	include("$serverrootpath/includes/db.inc.php");
	include("$includepath/common.php");
	
	page_protect();
	$user_id = $_SESSION['user_id'];
	
	if (empty($_POST['CharName']) || empty($_POST['Type'])) {
		$return['error'] = true;
		$return['msg'] = 'Error!';
	} else {
		if ($_POST['Type'] == "Promote") {
			$sql = "UPDATE CHARACTER_T SET sGuildRank = 0 WHERE cCharName = '" . $_POST['CharName'] . "'";
			$result = QueryWS1($sql);
			
			if ($result) {
				$return['error'] = false;
				$return['msg'] = $_POST['CharName'] . ' was promoted to guildmaster!';
			} else {
				$return['error'] = true;
				$return['msg'] = 'Error!';
			}	
			
		} else {
			$sql = "UPDATE CHARACTER_T SET sGuildRank = 12 WHERE cCharName = '" . $_POST['CharName'] . "'";
			$result = QueryWS1($sql);
			
			if ($result) {
				$return['error'] = false;
				$return['msg'] = $_POST['CharName'] . ' was demoted to regular member!';
			} else {
				$return['error'] = true;
				$return['msg'] = 'Error!';
			}		
			
		}
	}

	echo json_encode($return);
?>