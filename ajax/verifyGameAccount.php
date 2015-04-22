<?php
	if($_POST) {
		$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
		$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
		$rootpath = "/portal";
		
		include("$serverrootpath/includes/db.inc.php");
		include("$includepath/common.php");
	
		$u = str_replace( "'", "''", stripslashes(trim($_POST["username"])) );
		$p = str_replace( "'", "''", stripslashes(trim($_POST["password"])) );
		//$l = str_replace( "'", "''", stripslashes(trim($_POST["lastname"])) );
		$d = str_replace( "'", "''", stripslashes(trim($_POST["displayname"])) );
		
		if($u && $p && $d) {
			$p = (strlen($p) > 10) ? substr($p,0,10) : $p;
			
			$result = Query("Select TOP 1 iAccountID, cRealName from ACCOUNT_T where cAccountID = '".$u."' and cPasswd = '".$p."' and BlockDate < GetDate()");	
			if ( $result == false || Num_Rows($result) == 0 ) {
				// Login failed 
				echo json_encode(array('success' => false, 'message' => 'Error: Invalid account/password.'));
			} else {
				$result3 = QueryWS1("SELECT * FROM PlayerPortalLinkedAccount WHERE DisplayAlias = '$d'");
				$num3 = mssql_num_rows($result3);
				
				if ( $num3 > 0 ) { 
					echo json_encode(array('success' => false, 'message' => 'Error: Display Name already taken. Please choose another.'));
				} else {
					$iAccountID = Result( $result, 0, "iAccountID" );
					
					echo json_encode(array('success' => true, 'message' => 'Success!'));
				}
			}			
		} else { 
			echo json_encode(array('success' => false, 'message' => 'Error: Empty fields.'));
		}
	}
?>