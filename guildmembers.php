<?php
	$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	$rootpath = "/portal";
	
	include("$serverrootpath/includes/db.inc.php");
	include("$includepath/common.php");
	include("$includepath/itemdecode.php");
	include("$includepath/items.inc.php");
	
	page_protect();
	
	$guildid = $_GET['guildid'];
	$user_id = $_SESSION['user_id'];
	
	$yourguild = isYourGuild($guildid);
	
	$isgm = isGuildmasterOfGuild($user_id, $guildid);
	
	if ($yourguild) {
		$result = QueryWS1("SELECT c.cCharName, c.sLevel, c.sGuildRank, (SELECT COUNT(sLevel) FROM CHARACTER_T WHERE (iGuildID = $guildid) AND (sGuildRank = 0)) AS masters, c.sChar, c.cAccountID, c.FileSaveDate FROM CHARACTER_T c INNER JOIN mainlogin.dbo.ACCOUNT_T a ON c.cAccountID = a.cAccountID WHERE (c.iGuildID = $guildid) AND (a.BlockDate < GETDATE()) ORDER BY c.cAccountID ASC, c.cCharName ASC");
		
		$result8 = QueryWS1("SELECT * 
						FROM PPGuildItems 
						WHERE iGuildID = $guildid");
		
		$result2 = QueryWS1("SELECT cGuildName FROM GUILD_T WHERE iGuildID = $guildid");
		list($guild) =  mssql_fetch_row($result2);
		$origguild = $guild;
		$guild = str_replace("_", " ", trim($guild));
	}
	
	//keep at bottom
	include "$includepath/base.php";
?>

<?php startblock('title') ?>
	<?php echo $guild; ?> Guild
<?php endblock() ?>

<?php startblock('header') ?>
	<?php echo $guild; ?> Guild
<?php endblock() ?>

<?php startblock('additionalScripts') ?>
	<script type="text/javascript" src="<?php echo $rootpath; ?>/js/guildmembers.js"></script>
<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li class="active"><?php echo $guild; ?> Guild</li>
<?php endblock() ?>

<?php startblock('containerContent') ?>
	<div class="row-fluid">
	<?php if ( $yourguild > 0) { ?>
		<div class="span12">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#members" data-toggle="tab">Members</a></li>
				<li><a href="#items" data-toggle="tab">Guild Items</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="members">
					<h2>Total Members: <?php echo Num_Rows($result); ?></h2>
					<table id="guildTable" class="table table-bordered table-striped table-condensed sortable">
						<thead>
							<tr>
								<th>Character</th>
								<th>Level</th>
								<th>Last Logout</th>								
								<?php if ($isgm) { ?>
								<th></th>
								<?php } ?>
							</tr>
						</thead>
						<tbody>
						<?php
							if (Num_Rows($result) > 0) {
								$lastaccount = "";
								for ( $i = 0; $i < Num_Rows($result); $i++ ) {
									$character = Result($result, $i, "cCharName");
									$account = Result($result, $i, "cAccountID");
									$lvl = Result($result, $i, "sLevel");
									$charisma = Result($result, $i, "sChar");
									$masters = Result($result, $i, "masters");
									$masterlevel = Result($result, $i, "sGuildRank");
									$lastlogout = Result($result, $i, "FileSaveDate");
									
									if ($account == $lastaccount) {
										$class = "notopborder";
									} else {
										$class = "";
									}
									
									$promote = "";
									if ($charisma >= 20 && $masterlevel != 0  && $masters < 3) {
										$promote = "<a href='#' class='promoteGuildmember promote' title='$character'>Promote</a>";
									} else if ($masterlevel == 0) {
										$promote = "<a href='#' class='promoteGuildmember demote' title='$character'>Demote</a>";
									}
									
									echo "<tr class='$class'><td>$character</td><td>$lvl</td><td>$lastlogout</td>";
									
									if ($isgm) {
										echo "<td><a href='#' class='removeGuildmember' title='$character' rel='$origguild'>Remove</a> $promote</td>";
									}
									
									echo "</tr>";
									
									$lastaccount = $account;
								}
							} else {
								echo "<td colspan='5'>No characters found.</td>";
							}
						?>
						</tbody>
					</table>
				</div>
				<div class="tab-pane" id="items">
					Note: <strong>Please make sure both characters log out after items are passed to update this list or else you may see duplicate/missing items.</strong>  Ask your Guildmaster to add items to this page.<br /><br />
				<?php					
					if (Num_Rows($result8) > 0 ) {
						echo "<table id='guildItemTable' class='table table-bordered table-striped table-condensed sortable'>";
						echo "<thead>
							<tr>
								<th>Item</th>
								<th>Main Stat</th>
								<th>Sub Stat</th>
								<th></th>";
								if ($isgm) {
									echo "<th></th>";
								}
							echo "</tr>
						</thead>
						<tbody>";
						
						for ( $i = 0; $i < Num_Rows($result8); $i++ ) {
							$sItemID = Result($result8, $i, "sItemID");
							$sID1 = Result($result8, $i, "sID1");
							$sID2 = Result($result8, $i, "sID2");
							$sID3 = Result($result8, $i, "sID3");
							$iGuildID = Result($result8, $i, "iGuildID");
							
							$result9 = QueryWS1("SELECT i.iCount, i.sEffect2, i.iAttribute, i.sItemType, c.cCharName, c.FileSaveDate 
								FROM ITEM_T i
								INNER JOIN CHARACTER_T c ON c.CharID = i.CharID
								WHERE i.sItemID = $sItemID AND i.sID1 = $sID1 AND i.sID2 = $sID2 AND i.sID3 = $sID3");
							
							if (Num_Rows($result9) > 0 ) {		
							
								for ( $j = 0; $j < Num_Rows($result9); $j++ ) {		
									$sEffect2 = Result($result9, $j, "sEffect2");
									$iCount = Result($result9, $j, "iCount");
									$iAttribute = Result($result9, $j, "iAttribute");
									$sItemType = Result($result9, $j, "sItemType");
									$charname = Result($result9, $j, "cCharName");
									$timestamp = getRelativeCharTime(Result($result9, $j, "FileSaveDate"));
								
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
									
									echo "<tr data-sItemID='$sItemID' data-sID1='$sID1' data-sID2='$sID2' data-sID3='$sID3' data-attribute='$iAttribute' class='$stats[class]'><td>$realitemname$plusvalue$completion$qty</td><td>$stats[mainstat]$tablettype$stats[mainstatpercent]</td><td>$stats[substat] $stats[substatpercent]</td><td>$charname had this $timestamp in Bag</td>";
									
									if ($isgm) {
										echo "<td><a href='#' class='removeguilditem'>Remove</a></td>";
									}
									
									echo "</tr>";						
								}
							
							} else {
							
								$result10 = QueryWS1("SELECT i.iCount, i.sEffect2, i.iAttribute, i.sItemType, c.cCharName, c.FileSaveDate
									FROM BANKITEM_T i
									INNER JOIN CHARACTER_T c ON c.CharID = i.CharID
									WHERE i.sItemID = $sItemID AND i.sID1 = $sID1 AND i.sID2 = $sID2 AND i.sID3 = $sID3");
									
								if (Num_Rows($result10) > 0 ) {	
								
									for ( $j = 0; $j < Num_Rows($result10); $j++ ) {		
										$sEffect2 = Result($result10, $j, "sEffect2");
										$iCount = Result($result10, $j, "iCount");
										$iAttribute = Result($result10, $j, "iAttribute");
										$sItemType = Result($result10, $j, "sItemType");
										$charname = Result($result10, $j, "cCharName");
										$timestamp = getRelativeCharTime(Result($result10, $j, "FileSaveDate"));
									
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
										
										echo "<tr data-sItemID='$sItemID' data-sID1='$sID1' data-sID2='$sID2' data-sID3='$sID3' data-attribute='$iAttribute' class='$stats[class]'><td>$realitemname$plusvalue$completion$qty</td><td>$stats[mainstat]$tablettype$stats[mainstatpercent]</td><td>$stats[substat] $stats[substatpercent]</td><td>$charname had this $timestamp in WH</td>";	
										
										if ($isgm) {
											echo "<td><a href='#' class='removeguilditem'>Remove</a></td>";
										}
										
										echo "</tr>";	
									}
								
								} else {
								
									$itemname = $itemarray[$sItemID];
									if (array_key_exists($itemname, $itemnamearray)) {
										$realitemname = trim($itemnamearray[$itemname]);
									} else {
										$realitemname = $itemname;
									}
								
									echo "<tr data-sItemID='$sItemID' data-sID1='$sID1' data-sID2='$sID2' data-sID3='$sID3'><td colspan='4'>$realitemname (Deleted/Missing Item)</td>";
									
									if ($isgm) {
										echo "<td><a href='#' class='removeguilditem'>Remove</a></td>";
									}
										
									echo "</tr>";
									
								}
								
							}
							
						}
						
						echo "</tbody></table>";
					} else {
						echo "No items found.";
					}
				?>
				</div>
			</div>
		</div>
	<?php			
		} else {
			echo "Guild not found on any of the characters on your linked accounts.";
		}
	?>
	</div>
<?php endblock() ?>