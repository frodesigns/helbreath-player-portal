<?php
	if(isset($_GET['userid'])) {
		$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
		$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
		$rootpath = "/portal";
		
		include("$serverrootpath/includes/db.inc.php");
		include("$includepath/common.php");
		
		page_protect();
		$user_id = $_SESSION['user_id'];
	
		$userid = $_GET['userid'];
		
		if (empty($userid)) {
			echo "Error!";
		} else {
			$query = "SELECT AccountName, DisplayAlias FROM PlayerPortalLinkedAccount WHERE PPLoginID = '$userid' ORDER BY DisplayAlias ASC";
			$result = QueryWS1($query);
			
			echo "<table class='table table-bordered table-striped table-condensed'><thead><tr>";
			echo "<th>Display Name</th>";
			echo "<th>Account Name</th>";
			echo "</tr></thead>";
			echo "<tbody>";
			
			for ( $i = 0; $i < Num_Rows($result); $i++ ) {		
				$AccountName = Result($result, $i, "AccountName");
				$DisplayAlias = Result($result, $i, "DisplayAlias");
				
				echo "<tr>";
				echo "<td>$DisplayAlias</td>";
				echo "<td>$AccountName</td>";
				echo "</tr>";
			}
			
			echo "</tbody></table>";
		}
	}
?>