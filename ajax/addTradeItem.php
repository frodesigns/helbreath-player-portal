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
		
		if($sitemid && $sid1 && $sid2 && $sid3) {
			$result = QueryWS1("SELECT * FROM PPTradeItems WHERE sItemID = $sitemid AND sID1 = $sid1 AND sID2 = $sid2 AND sID3 = $sid3 AND PPLoginID = $user_id");
			$num = mssql_num_rows($result);
			
			if ( $num > 0 ) { 
				echo json_encode(array('success' => false, 'message' => 'Error: This item is already on your trade list.'));
			} else {
				$sql_insert = "INSERT INTO PPTradeItems (PPLoginID, sItemID, sID1, sID2, sID3) VALUES ($user_id, $sitemid, $sid1, $sid2, $sid3)";
				
				QueryWS1($sql_insert) or die("Insert Failed:" . mssql_get_last_message());
				
				echo json_encode(array('success' => true, 'message' => 'Item successfully added to your trade list!'));
			}
		}  else { 
			echo json_encode(array('success' => false, 'message' => 'Error: Missing fields.'));
		}
	}
?>