<?php
	if($_POST) {
		$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
		$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
		$rootpath = "/portal";
		
		include("$serverrootpath/includes/db.inc.php");
		include("$includepath/common.php");
		
		page_protect();
		$user_id = $_SESSION['user_id'];
	
		$u = str_replace( "'", "''", stripslashes(trim($_POST["username"])) );
		$p = str_replace( "'", "''", stripslashes(trim($_POST["password"])) );
		//$l = str_replace( "'", "''", stripslashes($_POST["lastname"]) );
		$d = str_replace( "'", "''", stripslashes(trim($_POST["displayname"])) );
		
		if($u && $p && $d) {
			$p = (strlen($p) > 10) ? substr($p,0,10) : $p;
			
			$result = Query("Select TOP 1 iAccountID, cRealName from ACCOUNT_T where cAccountID = '".$u."' and cPasswd = '".$p."' and BlockDate < GetDate()");	
			if ( $result == false || Num_Rows($result) == 0 ) {
				// Login failed 
				echo json_encode(array('success' => false, 'message' => 'Error: Invalid account/password.'));
			} else {
				$iAccountID = Result( $result, 0, "iAccountID" );
				
				$result2 = QueryWS1("SELECT * FROM PlayerPortalLinkedAccount WHERE AccountName = '$u' AND PPLoginID = '$user_id'");
				$num = mssql_num_rows($result2);
		
				if ( $num > 0 ) { 
					echo json_encode(array('success' => false, 'message' => 'Error: This account is already linked.'));
				} else {
					$result3 = QueryWS1("SELECT * FROM PlayerPortalLinkedAccount WHERE DisplayAlias = '$d'");
					$num3 = mssql_num_rows($result3);
					
					if ( $num3 > 0 ) { 
						echo json_encode(array('success' => false, 'message' => 'Error: Display Name already taken. Please choose another.'));
					} else {
						$sql_insert = "INSERT into PlayerPortalLinkedAccount
						(PPLoginID, AccountName, DisplayAlias)
						VALUES
						('$user_id','$u','$d')";
						
						QueryWS1($sql_insert) or die("Insertion Failed:" . mssql_get_last_message());
						
						$q = QueryWS1("SELECT TOP 1 PPLinkedAccountID FROM PlayerPortalLinkedAccount ORDER BY PPLinkedAccountID DESC") or die(mssql_get_last_message());
						$r = mssql_fetch_assoc($q);
					
						$account_id = $r['PPLinkedAccountID'];
						
						echo json_encode(array('success' => true, 'message' => 'Success!', 'accountid' => "$account_id"));
					}
				}
			}			
		} else { 
			echo json_encode(array('success' => false, 'message' => 'Error: Empty fields.'));
		}
	}
?>