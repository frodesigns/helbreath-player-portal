<?php
	$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	$rootpath = "/portal";
	
	include("$serverrootpath/includes/db.inc.php");	
	include("$includepath/common.php");
	include("$includepath/itemdecode.php");
	include("$includepath/items.inc.php");
	
	page_protect();
	
	$charname = $_GET['charname'];
	$user_id = $_SESSION['user_id'];
	
	$yourchar = isYourCharacter($charname);
	$isgm = isPPAccountGuildmaster($user_id);
	
	if ($yourchar) {
		$result = QueryWS1("SELECT CharID, cAccountID, sID1, sID2, sID3 FROM CHARACTER_T WHERE cCharName = '$charname'");
		list($charid, $accountname, $charsID1, $charsID2, $charsID3) = mssql_fetch_row($result);
		
		$result2 = QueryWS1("SELECT * FROM ITEM_T WHERE CharID = $charid AND bItemEquip = 1 AND sItemID > 0");
		
		$result3 = QueryWS1("SELECT * FROM ITEM_T WHERE CharID = $charid AND bItemEquip = 0 AND sItemID > 0");
		
		$result4 = QueryWS1("SELECT * FROM BANKITEM_T WHERE CharID = $charid AND sItemID > 0");		
	}
	
	//keep at bottom
	include "$includepath/base.php";
?>

<?php startblock('title') ?>
	Inventory
<?php endblock() ?>

<?php startblock('header') ?>
	<?php echo $charname; ?>'s Inventory
<?php endblock() ?>

<?php startblock('additionalScripts') ?>

<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li><a href="<?php echo $rootpath; ?>/characters.php">Characters</a></li> <span class="divider">/</span>
	<li><a href="<?php echo $rootpath; ?>/charstats.php?charname=<?php echo $charname; ?>"><?php echo $charname; ?></a></li> <span class="divider">/</span>
	<li class="active">Inventory</li>
<?php endblock() ?>

<?php startblock('containerContent') ?>
	<div class="modal hide fade" id="itemModal">
		<div class="modal-header">
			<button class="close" data-dismiss="modal">&times;</button>
			<h3></h3>
		</div>
		<div class="modal-body">
			<form id="itemForm">
				<div class="control-group">
				<?php if ($isgm) { ?>
					<label class="control-label" for="acctUsername">Select Guild for Guild Item:</label>
					<div class="controls">						
						<select class="guilds">
							<?php 
								$guilds = getGuildmasterGuilds($user_id);

								foreach ($guilds as $guildid => $guildname) {
									echo "<option value='$guildid'>$guildname</option>";
								}
							?>
						</select>
						<p class="help-block">Not needed for Trade List.</p>						
					</div>
				<?php } ?>
				</div>
				<div class="form-actions">
					<input type="hidden" id="sitemid" value="" />
					<input type="hidden" id="sid1" value="" />
					<input type="hidden" id="sid2" value="" />
					<input type="hidden" id="sid3" value="" />
						
					<button id="addTradeItem" class="btn btn-primary" type="submit"><i class='icon-plus icon-white'></i> Personal Trade List</button>
					<?php if ($isgm) { ?>
					<button id="addGuildItem" class="btn btn-success" type="submit"><i class='icon-plus icon-white'></i> Guild Item</button>
					<?php } ?>
					<a data-dismiss="modal" class="btn" href="#">Cancel</a>
					<span id="acctStatus"></span>
				</div>
			</form>
		</div>		
	</div>
	
	<div class="row-fluid">
		<div class="span12">
			<p>Bound items are in <strong>Bold</strong>. Click on a row to add an item to your trade list.</p>
		</div>
	</div>
	<div class="row-fluid">
	<?php if ($yourchar) { ?>
		<div class="span6 tab-content">
			<div class="widget">
				<h2>
					<div class="pull-right guildlistdiv">
						<a href="#" class="collapse"><i class="icon-chevron-up icon-white"></i></a>
					</div>
					Equipped
				</h2>
				<div class="widget-inner">
					<table id="equippedTable" class="table table-bordered table-striped table-condensed sortable">
						<thead>
							<tr>
								<th>Item</th>
								<th>Main Stat</th>
								<th>Sub Stat</th>
								<th>Endurance</th>
							</tr>
						</thead>
						<tbody>
						<?php
							for ( $i = 0; $i < Num_Rows($result2); $i++ ) {
								$sItemID = Result($result2, $i, "sItemID");
								$iCount = Result($result2, $i, "iCount");
								$sItemType = Result($result2, $i, "sItemType");
								$sID1 = Result($result2, $i, "sID1");
								$sID2 = Result($result2, $i, "sID2");
								$sID3= Result($result2, $i, "sID3");
								$ids = $sID1 . ", " . $sID2 . ", " . $sID3;
								$sColor = Result($result2, $i, "sColor");
								$sEffect1 = Result($result2, $i, "sEffect1");
								$sEffect2 = Result($result2, $i, "sEffect2");
								$sEffect3 = Result($result2, $i, "sEffect3");
								$effects = $sEffect1 . ", " . $sEffect2 . ", " . $sEffect3;
								$iLifeSpan = Result($result2, $i, "iLifeSpan");
								$iAttribute = Result($result2, $i, "iAttribute");
								$sItemPosX = Result($result2, $i, "sItemPosX");
								$sItemPosY = Result($result2, $i, "sItemPosY"); 
								$position = $sItemPosX . ", " . $sItemPosY;
								
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
								
								if ($charsID1 == $sID1 && $charsID2 == $sID2 && $charsID3 == $sID3) {
									$bound = "bound";
								} else {
									$bound = "";
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
								
								echo "<tr data-sItemID='$sItemID' data-sID1='$sID1' data-sID2='$sID2' data-sID3='$sID3' data-attribute='$iAttribute' data-seffect1='$sEffect1' data-seffect2='$sEffect2' data-seffect3='$sEffect3' class='$stats[class]'><td class='$bound'>$realitemname$plusvalue$completion$qty</td><td>$stats[mainstat]$tablettype$stats[mainstatpercent]</td><td>$stats[substat] $stats[substatpercent]</td><td>$iLifeSpan</td></tr>";
							}
						?>
						</tbody>
					</table>
				</div>
			</div>
			
			<div class="widget">
				<h2>
					<div class="pull-right guildlistdiv">
						<a href="#" class="collapse"><i class="icon-chevron-up icon-white"></i></a>
					</div>
					Bag
				</h2>
				<div class="widget-inner">
					<table id="bagTable" class="table table-bordered table-striped table-condensed sortable">
						<thead>
							<tr>
								<th>Item</th>
								<th>Main Stat</th>
								<th>Sub Stat</th>
								<th>Endurance</th>
							</tr>
						</thead>
						<tbody>
						<?php
							for ( $i = 0; $i < Num_Rows($result3); $i++ ) {
								$sItemID = Result($result3, $i, "sItemID");
								$iCount = Result($result3, $i, "iCount");
								$sItemType = Result($result3, $i, "sItemType");
								$sID1 = Result($result3, $i, "sID1");
								$sID2 = Result($result3, $i, "sID2");
								$sID3= Result($result3, $i, "sID3");
								$ids = $sID1 . ", " . $sID2 . ", " . $sID3;
								$sColor = Result($result3, $i, "sColor");
								$sEffect1 = Result($result3, $i, "sEffect1");
								$sEffect2 = Result($result3, $i, "sEffect2");
								$sEffect3 = Result($result3, $i, "sEffect3");
								$effects = $sEffect1 . ", " . $sEffect2 . ", " . $sEffect3;
								$iLifeSpan = Result($result3, $i, "iLifeSpan");
								$iAttribute = Result($result3, $i, "iAttribute");
								$sItemPosX = Result($result3, $i, "sItemPosX");
								$sItemPosY = Result($result3, $i, "sItemPosY"); 
								$position = $sItemPosX . ", " . $sItemPosY;
								
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
								
								if ($charsID1 == $sID1 && $charsID2 == $sID2 && $charsID3 == $sID3) {
									$bound = "bound";
								} else {
									$bound = "";
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
								
								echo "<tr data-sItemID='$sItemID' data-sID1='$sID1' data-sID2='$sID2' data-sID3='$sID3' data-attribute='$iAttribute' data-seffect1='$sEffect1' data-seffect2='$sEffect2' data-seffect3='$sEffect3' class='$stats[class]'><td class='$bound'>$realitemname$plusvalue$completion$qty</td><td>$stats[mainstat]$tablettype$stats[mainstatpercent]</td><td>$stats[substat] $stats[substatpercent]</td><td>$iLifeSpan</td></tr>";
							}
						?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="span6 tab-content">
			<div class="widget">
				<h2>
					<div class="pull-right guildlistdiv">
						<a href="#" class="collapse"><i class="icon-chevron-up icon-white"></i></a>
					</div>
					Warehouse
				</h2>
				<div class="widget-inner">
					<table id="warehouseTable" class="table table-bordered table-striped table-condensed sortable">
						<thead>
							<tr>
								<th>Item</th>
								<th>Main Stat</th>
								<th>Sub Stat</th>
								<th>Endurance</th>
							</tr>
						</thead>
						<tbody>
						<?php
							for ( $i = 0; $i < Num_Rows($result4); $i++ ) {
								$sItemID = Result($result4, $i, "sItemID");
								$iCount = Result($result4, $i, "iCount");
								$sItemType = Result($result4, $i, "sItemType");
								$sID1 = Result($result4, $i, "sID1");
								$sID2 = Result($result4, $i, "sID2");
								$sID3= Result($result4, $i, "sID3");
								$ids = $sID1 . ", " . $sID2 . ", " . $sID3;
								$sColor = Result($result4, $i, "sColor");
								$sEffect1 = Result($result4, $i, "sEffect1");
								$sEffect2 = Result($result4, $i, "sEffect2");
								$sEffect3 = Result($result4, $i, "sEffect3");
								$effects = $sEffect1 . ", " . $sEffect2 . ", " . $sEffect3;
								$iLifeSpan = Result($result4, $i, "iLifeSpan");
								$iAttribute = Result($result4, $i, "iAttribute");
								
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
								
								if ($charsID1 == $sID1 && $charsID1 == $sID1 && $charsID1 == $sID1) {
									$bound = "bound";
								} else {
									$bound = "";
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
								
								echo "<tr data-sItemID='$sItemID' data-sID1='$sID1' data-sID2='$sID2' data-sID3='$sID3' data-attribute='$iAttribute' data-seffect1='$sEffect1' data-seffect2='$sEffect2' data-seffect3='$sEffect3' class='$stats[class]'><td class='$bound'>$realitemname$plusvalue$completion$qty</td><td>$stats[mainstat]$tablettype$stats[mainstatpercent]</td><td>$stats[substat] $stats[substatpercent]</td><td>$iLifeSpan</td></tr>";
							}
						?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	<?php			
		} else {
			echo "Character not found on any of your linked accounts.";
		}
	?>
	</div>
<?php endblock() ?>