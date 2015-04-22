<?php
	if($_POST) {
		$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
		$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
		$rootpath = "/portal";
		
		include("$serverrootpath/includes/db.inc.php");
		include("$includepath/common.php");
		include("$includepath/itemdecode.php");
		include("$includepath/items.inc.php");
		
		user_protect();
		if (isset($_SESSION['user_id'])) {
			$user_id = $_SESSION['user_id'];
		}
	
		$sort = str_replace( "'", "''", stripslashes(trim($_POST["sort"])) );
		$sort2 = str_replace( "'", "''", stripslashes(trim($_POST["sort2"])) );
		$count = str_replace( "'", "''", stripslashes(trim($_POST["count"])) );
		
		if ($count == 0) {
			$count = "";
		} else {
			$count = " TOP $count";
		}
		
		if ($sort != "") {
			$where = "WHERE (";
			
			$i = 0;
			foreach($itemposarray as $itemid=>$itempos) {
				if ($itempos == $sort) {
					if ($i == 0) {
						$where .= "sItemID = $itemid ";
					} else {
						$where .= "OR sItemID = $itemid ";
					}
					
					$i++;
				}
			}
			
			$where .= ") ";
		} else {
			$where = "";
		}
		
		if ($sort2 == "all") {
			$and = "";
		} else if ($sort2 == "month") {
			if ($sort == "") {
				$and = "WHERE t.UpdateDT > GETDATE() - 30";
			} else {
				$and = "AND t.UpdateDT > GETDATE() - 30";
			}
		} else if ($sort2 == "week") {
			if ($sort == "") {
				$and = "WHERE t.UpdateDT > GETDATE() - 7";
			} else {
				$and = "AND t.UpdateDT > GETDATE() - 7";
			}
		} else if ($sort2 == "day") {
			if ($sort == "") {
				$and = "WHERE t.UpdateDT > GETDATE() - 1";
			} else {
				$and = "AND t.UpdateDT > GETDATE() - 1";
			}
		} else {
			$and = "";
		}
		
		$query8 = "SELECT$count t.*, p.DisplayAlias
			FROM PPTradeItems t				
			INNER JOIN PlayerPortalLogin p ON p.PPLoginID = t.PPLoginID
			$where
			$and
			ORDER BY t.UpdateDT DESC";
		
		$result8 = QueryWS1($query8);
			
		if (Num_Rows($result8) > 0 ) {
			echo "<table id='marketItemTable' class='table table-bordered table-striped table-condensed sortable'>";
			echo "<thead>
				<tr>
					<th>Item</th>
					<th>Main Stat</th>
					<th>Sub Stat</th>
					<th>Owner</th>
					<th>Added</th>
				</tr>
			</thead>
			<tbody>";
			
			for ( $i = 0; $i < Num_Rows($result8); $i++ ) {
				$sItemID = Result($result8, $i, "sItemID");
				$sID1 = Result($result8, $i, "sID1");
				$sID2 = Result($result8, $i, "sID2");
				$sID3 = Result($result8, $i, "sID3");
				$owner = Result($result8, $i, "DisplayAlias");
				$timestamp = getRelativeTime(Result($result8, $i, "UpdateDT"));
				
				$result9 = QueryWS1("SELECT iCount, sEffect2, iAttribute, sItemType FROM ITEM_T WHERE sItemID = $sItemID AND sID1 = $sID1 AND sID2 = $sID2 AND sID3 = $sID3");
				
				if (Num_Rows($result9) > 0 ) {
				
					for ( $j = 0; $j < Num_Rows($result9); $j++ ) {		
						$sEffect2 = Result($result9, $j, "sEffect2");
						$iCount = Result($result9, $j, "iCount");
						$iAttribute = Result($result9, $j, "iAttribute");
						$sItemType = Result($result9, $j, "sItemType");
					
						if ($iCount > 1) {
							$qty = " (" . number_format($iCount) . ")";
						} else {
							$qty = "";
						}
						
						if ($sItemID >= 881 && $sItemID <= 884) {
							$completion = " " . $sEffect2 . "%";
						} else {
							$completion = "";
						}
						
						$tablettype = "";
						if ($sItemID == 867) {
							if ($sEffect2 == 1) {
								$tablettype = "Health";
							} else if ($sEffect2 == 2) {
								$tablettype = "Berserk";
							} else if ($sEffect2 == 3) {
								$tablettype = "Mana";
							} else if ($sEffect2 == 4) {
								$tablettype = "Experience";
							}
						}

						$itemname = $itemarray[$sItemID];
						if (array_key_exists($itemname, $itemnamearray)) {
							$realitemname = trim($itemnamearray[$itemname]);
						} else {
							$realitemname = $itemname;
						}						
						
						$stats = getItemStats($iAttribute, $sItemType, $sEffect2, $iCount);
						
						$plusvalue = $stats['plusvalue'];
						if ($sItemID >= 908 && $sItemID <= 911) {
							$plusvalue = "+" . $sEffect2;
						}
						
						echo "<tr data-sItemID='$sItemID' data-sID1='$sID1' data-sID2='$sID2' data-sID3='$sID3' data-attribute='$iAttribute' class='$stats[class]'><td>$realitemname$plusvalue$completion$qty</td><td>$stats[mainstat]$tablettype$stats[mainstatpercent]</td><td>$stats[substat] $stats[substatpercent]</td><td><a href='$rootpath/userprofile.php?username=$owner'>$owner</a></td><td>$timestamp</td></tr>";						
					}
					
				} else {
				
					$result10 = QueryWS1("SELECT iCount, sEffect2, iAttribute, sItemType FROM BANKITEM_T WHERE sItemID = $sItemID AND sID1 = $sID1 AND sID2 = $sID2 AND sID3 = $sID3");
					
					if (Num_Rows($result10) > 0 ) {
						
						for ( $j = 0; $j < Num_Rows($result10); $j++ ) {		
							$sEffect2 = Result($result10, $j, "sEffect2");
							$iCount = Result($result10, $j, "iCount");
							$iAttribute = Result($result10, $j, "iAttribute");
							$sItemType = Result($result10, $j, "sItemType");
						
							if ($iCount > 1) {
								$qty = " (" . number_format($iCount) . ")";
							} else {
								$qty = "";
							}
							
							if ($sItemID >= 881 && $sItemID <= 884) {
								$completion = " " . $sEffect2 . "%";
							} else {
								$completion = "";
							}
							
							$tablettype = "";
							if ($sItemID == 867) {
								if ($sEffect2 == 1) {
									$tablettype = "Health";
								} else if ($sEffect2 == 2) {
									$tablettype = "Berserk";
								} else if ($sEffect2 == 3) {
									$tablettype = "Mana";
								} else if ($sEffect2 == 4) {
									$tablettype = "Experience";
								}
							}

							$itemname = $itemarray[$sItemID];
							if (array_key_exists($itemname, $itemnamearray)) {
								$realitemname = trim($itemnamearray[$itemname]);
							} else {
								$realitemname = $itemname;
							}						
							
							$stats = getItemStats($iAttribute, $sItemType, $sEffect2, $iCount);
							
							$plusvalue = $stats['plusvalue'];
							if ($sItemID >= 908 && $sItemID <= 911) {
								$plusvalue = "+" . $sEffect2;
							}
							
							echo "<tr data-sItemID='$sItemID' data-sID1='$sID1' data-sID2='$sID2' data-sID3='$sID3' data-attribute='$iAttribute' class='$stats[class]'><td>$realitemname$plusvalue$completion$qty</td><td>$stats[mainstat]$tablettype$stats[mainstatpercent]</td><td>$stats[substat] $stats[substatpercent]</td><td><a href='$rootpath/userprofile.php?username=$owner'>$owner</a></td><td>$timestamp</td></tr>";						
						}
					
					}
				
				}
			}
			
			echo "</tbody></table>";
		} else {
			echo "No items found.";
		}
	}
?>