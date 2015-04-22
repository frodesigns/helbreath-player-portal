<?php
	if($_POST) {
		$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
		$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
		$rootpath = "/portal";
		
		include("$serverrootpath/includes/db.inc.php");
		include("$includepath/common.php");
		
		page_protect();
		$user_id = $_SESSION['user_id'];
	
		$sitemid = str_replace( "'", "''", stripslashes($_POST["sitemid"]) );
		$sid1 = str_replace( "'", "''", stripslashes($_POST["sid1"]) );
		$sid2 = str_replace( "'", "''", stripslashes($_POST["sid2"]) );
		$sid3 = str_replace( "'", "''", stripslashes($_POST["sid3"]) );
		$guildid = str_replace( "'", "''", stripslashes($_POST["guildid"]) );
		
		if($sitemid && $sid1 && $sid2 && $sid3 && $guildid) {
			$result = QueryWS1("SELECT * FROM PPGuildItems WHERE sItemID = $sitemid AND sID1 = $sid1 AND sID2 = $sid2 AND sID3 = $sid3 AND iGuildID = $guildid");
			$num = mssql_num_rows($result);
			
			if ( $num > 0 ) { 
				echo json_encode(array('success' => false, 'message' => 'Error: This item is already a guild item.'));
			} else {
				$sql_insert = "INSERT INTO PPGuildItems (iGuildID, sItemID, sID1, sID2, sID3) VALUES ($guildid, $sitemid, $sid1, $sid2, $sid3)";
				
				QueryWS1($sql_insert) or die("Insert Failed:" . mssql_get_last_message());
				
				echo json_encode(array('success' => true, 'message' => 'Item successfully added to the guild item list!'));
			}
		}  else { 
			echo json_encode(array('success' => false, 'message' => 'Error: Missing fields.'));
		}
	}
?>