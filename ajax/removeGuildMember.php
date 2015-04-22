<?php
	$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	$rootpath = "/portal";
	
	include("$serverrootpath/includes/db.inc.php");
	include("$includepath/common.php");
	
	page_protect();
	$user_id = $_SESSION['user_id'];
	
	if (empty($_POST['CharName']) || empty($_POST['GuildName'])) {
		$return['error'] = true;
		$return['msg'] = 'Error!';
	} else {
		$sql = "UPDATE CHARACTER_T SET cGuildName = 'NONE', iGuildID = -1, sGuildRank = -1 WHERE cCharName = '" . $_POST['CharName'] . "'";
		$result = QueryWS1($sql);
		
		if ($result) {
			$sql = "DELETE FROM GUILDMEMBER_T WHERE cMemberName = '" . $_POST['CharName'] . "' AND cGuildName = '" . $_POST['GuildName'] . "'";
			$result = QueryWS1($sql);
			if ($result) {
				$return['error'] = false;
				$return['msg'] = $_POST['CharName'] . ' was successfully removed from the guild.';
			} else {
				$return['error'] = true;
				$return['msg'] = 'Error!';
			}
		} else {
			$return['error'] = true;
			$return['msg'] = 'Error!';
		}			
	}

	echo json_encode($return);
?>