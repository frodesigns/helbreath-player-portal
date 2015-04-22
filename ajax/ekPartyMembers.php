<?php
	if(isset($_GET['id'])) {
		$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
		$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
		$rootpath = "/portal";
		
		include("$serverrootpath/includes/db.inc.php");
		include("$includepath/common.php");
		
		page_protect();
		$user_id = $_SESSION['user_id'];
	
		$partyid = $_GET['id'];
		
		if (empty($partyid)) {
			echo "Error!";
		} else {
			$query = "select * from EKPartyMember_vw where EKPartyID = $partyid order by Character asc";
			$result = QueryWS1($query);
			
			echo "<table class='table table-bordered table-striped table-condensed'><thead><tr>";
			echo "<th>Character</th>";
			echo "<th>Joined</th>";
			echo "<th>In Party</th>";
			echo "<th>DQed</th>";
			echo "<th>DQ Reason</th>";
			echo "<th>EKs</th>";
			echo "<th>Assists</th>";
			echo "</tr></thead>";
			echo "<tbody>";
			
			for ( $i = 0; $i < Num_Rows($result); $i++ ) {		
				$Character = Result($result, $i, "Character");
				$Joined = Result($result, $i, "Joined");
				$In_Party = Result($result, $i, "In_Party");
				$DQed = Result($result, $i, "DQed");
				$DQ_Reason = Result($result, $i, "DQ_Reason");
				$EKs = Result($result, $i, "EKs");
				$Assists = Result($result, $i, "Assists");
				
				echo "<tr>";
				echo "<td>$Character</td>";
				echo "<td>$Joined</td>";
				echo "<td>$In_Party</td>";
				echo "<td>$DQed</td>";
				echo "<td>$DQ_Reason</td>";
				echo "<td>$EKs</td>";
				echo "<td>$Assists</td>";
				echo "</tr>";
			}
			
			echo "</tbody></table>";
		}
	}
?>