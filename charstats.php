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
	
	if ($yourchar) {
		$result = QueryWS1("SELECT CharID, cAccountID, sLevel, bGender, iExp, cNation, cMapLoc, cGuildName, iGuildID, sGuildRank, sStr, sDex, sVit, sMag, sInt, sChar, cProfile, iContribution, iHP, iMP, iSP, iEK, iPK, iGizoneItemUpgradeLeft, iPopular, cMagicMastery, sID1, sID2, sID3 FROM CHARACTER_T WHERE cCharName = '$charname'");
		list($charid, $accountname, $level, $gender, $exp, $nation, $location, $guild, $guildid, $guildrank, $str, $dex, $vit, $mag, $int, $cha, $profile, $contrib, $hp, $mp, $sp, $eks, $pks, $maj, $rep, $magicmastery, $charsID1, $charsID2, $charsID3) = mssql_fetch_row($result);
		
		$result3 = QueryWS1("SELECT sSkillID, sSkillMastery, iSkillSSN FROM SKILL_T WHERE CharID = '$charid' AND (iSkillSSN <> '' OR sSkillID = 13)");
		
		$result4 = QueryWS1("SELECT * FROM ITEM_T WHERE CharID = $charid AND sID1 = $charsID1 AND sID2 = $charsID2 AND sID3 = $charsID3 AND (sItemID >= 400 AND sItemID <= 428 AND sItemID <> 402 OR sItemID >= 745 AND sItemID <= 746)");
		
		$result5 = QueryWS1("SELECT * FROM BANKITEM_T WHERE CharID = $charid AND sID1 = $charsID1 AND sID2 = $charsID2 AND sID3 = $charsID3 AND (sItemID >= 400 AND sItemID <= 428 AND sItemID <> 402 OR sItemID >= 745 AND sItemID <= 746)");
		
		$result6 = QueryWS1("SELECT sSkillID, sSkillMastery, iSkillSSN FROM SKILL_T WHERE CharID = '$charid' AND sSkillMastery = 100 AND (iSkillSSN <> '' OR sSkillID = 13) AND (sSkillID = 0 OR sSkillID = 1 OR sSkillID = 2 OR sSkillID = 12 OR sSkillID = 13)");
		
		$chartype = charType($str, $dex, $int, $mag, $vit, $cha);			
		$town = getNation($nation);
		$guild = getGuild($guild, $guildrank);
		$profile = str_replace("_", " ", $profile);
		$totaleks = 0;
		$totaleks += $eks;
	}
	
	//keep at bottom
	include "$includepath/base.php";
?>

<?php startblock('title') ?>
	Character Stats
<?php endblock() ?>

<?php startblock('header') ?>
	<?php echo $charname; ?>'s Stats
<?php endblock() ?>

<?php startblock('additionalScripts') ?>
	<script type="text/javascript" src="<?php echo $rootpath; ?>/js/charstats.js"></script>
<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li><a href="<?php echo $rootpath; ?>/characters.php">Characters</a></li> <span class="divider">/</span>
	<li><a href="<?php echo $rootpath; ?>/charstats.php?charname=<?php echo $charname; ?>"><?php echo $charname; ?></a></li> <span class="divider">/</span>
	<li class="active">Character Stats</li>
<?php endblock() ?>

<?php startblock('containerContent') ?>
	<div class="row-fluid">
	<?php if ($yourchar) { ?>
		<div class="span6">
			<div class="widget">
				<h2>
					<div class="pull-right guildlistdiv">
						<a href="#" class="collapse"><i class="icon-chevron-up icon-white"></i></a>
					</div>
					Character Info
				</h2>
				<div class="widget-inner">
					<div class="well">
						<div class="row-fluid charinfo">
							<div class="span7">					
								<strong><?php echo $town; ?></strong><br />
								<strong><?php echo $guild; ?></strong><br /><br />
								<strong>Level:</strong> <?php echo $level; ?><br />
								<?php
									if ($level == 180) {
										echo "<strong>Character Type:</strong> $chartype<br />";
									}
								?>
								<strong>Exp:</strong> <?php echo number_format($exp); ?><br />
								<strong>Current Map:</strong> <?php echo getMapName($location); ?><br /><br />
								<?php
									if ($level == 180) {
										echo "<a href='$rootpath/rejuvenate.php?char=$charname' class='btn btn-danger'>Rejuvenate</a>";
									}
								?>
							</div>
							<div class="span5">
								<strong>Health:</strong> <?php echo $hp; ?><br />
								<strong>Mana:</strong> <?php echo $mp; ?><br />
								<strong>Stamina:</strong> <?php echo $sp; ?><br /><br />
								<strong>Enemy Kills:</strong> <?php echo $eks; ?><br />
								<strong>Majestic Points:</strong> <?php echo $maj; ?><br />
								<strong>Reputation:</strong> <?php echo $rep; ?><br />
								<strong>Contribution:</strong> <?php echo $contrib; ?><br />
								<?php if ($pks > 0) { ?>
								<strong>Criminal Count:</strong> <?php echo $pks; ?>
								<?php } ?>
							</div>
						</div>
						<div class="row-fluid stats">
							<div class="span4">
								<strong>STR:</strong> <?php echo $str; ?><br />
								<strong>DEX:</strong> <?php echo $dex; ?>
							</div>
							<div class="span4">
								<strong>INT:</strong> <?php echo $int; ?><br />
								<strong>MAG:</strong> <?php echo $mag; ?>
							</div>
							<div class="span4">
								<strong>VIT:</strong> <?php echo $vit; ?><br />
								<strong>CHA:</strong> <?php echo $cha; ?>
							</div>
						</div>
						<div class="row-fluid profile">
							<div class="span12">						
								<strong>Trophies:</strong>
								<?php if (Num_Rows($result4) == 0 && Num_Rows($result5) == 0  && Num_Rows($result6) == 0) { ?>
									None<br />
								<?php } else { ?>
									<div class="hero">
									<?php
										for ( $i = 0; $i < Num_Rows($result4); $i++ ) {
											$sItemID = Result($result4, $i, "sItemID");
											$iAttribute = Result($result4, $i, "iAttribute");
											$sItemType = Result($result4, $i, "sItemType");
											$sEffect2 = Result($result4, $i, "sEffect2");
											$iCount = Result($result4, $i, "iCount");
											
											$itemname = $itemarray[$sItemID];
											$realitemname = trim($itemnamearray[$itemname]);
											
											$stats = getItemStats($iAttribute, $sItemType, $sEffect2, $iCount);
											
											if ($sItemID >= 745 && $sItemID <= 746) {
												$plusvalue = $stats['plusvalue'];
												$realitemname .= $plusvalue;
												
												if ($plusvalue == 2) {
													$maj = 2;
												} else if ($plusvalue == 4) {
													$maj = 6;
												} else if ($plusvalue == 6) {
													$maj = 13;
												} else if ($plusvalue == 8) {
													$maj = 24;
												} else if ($plusvalue == 10) {
													$maj = 40;
												} else if ($plusvalue == 12) {
													$maj = 62;
												} else if ($plusvalue == 14) {
													$maj = 91;
												} else if ($plusvalue == 15) {
													$maj = 128;
													$sItemID .= ".15";
												}

												$cost = "$maj Maj";
											} else {
												$ekcost = getHeroEKCost($sItemID);
												$totaleks += $ekcost;
												$cost = $ekcost . " EKs";
											}
											
											echo "<div class='heropiece'><img rel='tooltip' title='$realitemname - $cost' src='$rootpath/img/item-bag-images/$sItemID.png' alt='$realitemname' /></div>";
										}
										
										for ( $i = 0; $i < Num_Rows($result5); $i++ ) {
											$sItemID = Result($result5, $i, "sItemID");
											$iAttribute = Result($result5, $i, "iAttribute");
											$sItemType = Result($result5, $i, "sItemType");
											$sEffect2 = Result($result5, $i, "sEffect2");
											$iCount = Result($result5, $i, "iCount");
											
											$itemname = $itemarray[$sItemID];
											$realitemname = trim($itemnamearray[$itemname]);
											
											$stats = getItemStats($iAttribute, $sItemType, $sEffect2, $iCount);
											
											if ($sItemID >= 745 && $sItemID <= 746) {
												$plusvalue = $stats['plusvalue'];
												$realitemname .= $plusvalue;
												
												if ($plusvalue == 2) {
													$maj = 2;
												} else if ($plusvalue == 4) {
													$maj = 6;
												} else if ($plusvalue == 6) {
													$maj = 13;
												} else if ($plusvalue == 8) {
													$maj = 24;
												} else if ($plusvalue == 10) {
													$maj = 40;
												} else if ($plusvalue == 12) {
													$maj = 62;
												} else if ($plusvalue == 14) {
													$maj = 91;
												} else if ($plusvalue == 15) {
													$maj = 128;
													$sItemID .= ".15";
												}

												$cost = "$maj Maj";
											} else {
												$ekcost = getHeroEKCost($sItemID);
												$totaleks += $ekcost;
												$cost = $ekcost . " EKs";
											}
											
											echo "<div class='heropiece'><img rel='tooltip' title='$realitemname - $cost' src='$rootpath/img/item-bag-images/$sItemID.png' alt='$realitemname' /></div>";
										}
										
										for ( $i = 0; $i < Num_Rows($result6); $i++ ) {
											$skillid = Result($result6, $i, "sSkillID");
											$skillmastery = Result($result6, $i, "sSkillMastery");
											$skill = getSkillName($skillid);
											
											//mining 0, fishing 1, farming 2, alch 12, manu 13
											if ($skillid == 0) {
												$itemid = 231;
											} else if ($skillid == 1) {
												$itemid = 105;
											} else if ($skillid == 2) {
												$itemid = 232;
											} else if ($skillid == 12) {
												$itemid = 227;
											} else if ($skillid == 13) {
												$itemid = 236;
											}
											
											echo "<div class='heropiece'><img rel='tooltip' title='100% $skill' src='$rootpath/img/item-bag-images/$itemid.png' alt='100% $skill' /></div>";
										}
									?>
									</div>
								<?php } ?>
								<br />
								<strong>Total EKs Including Hero:</strong> <?php echo $totaleks; ?><br /><br />
								<strong>Profile:</strong> <?php echo $profile; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="span6">
			<div class="widget">
				<h2>
					<div class="pull-right guildlistdiv">
						<a href="#" class="collapse"><i class="icon-chevron-up icon-white"></i></a>
					</div>
					Spells
				</h2>
				<div class="widget-inner">
					<strong>Equipment:</strong>
					<select id="msWand">
						<option value="0">No Wand</option>
						<option value="10">MS10 Wand</option>
						<option value="20">MS20 Wand</option>
						<option value="30">MS30 LLF Wand</option>
						<option value="28">DK Wand</option>
						<option value="32">Templar Wand</option>
					</select>
					<select id="msNeck">
						<option value="0">No Necklace</option>
						<option value="10">MS10 Necklace</option>
						<option value="12">MS12 Necklace</option>
						<option value="14">MS14 Necklace</option>
						<option value="16">MS16 Necklace</option>
						<option value="18">MS18 Necklace</option>
						<option value="20">Liche Necklace</option>
					</select>
					<ul class="nav nav-tabs">
						<li class="active"><a href="#spells1" data-toggle="tab">I</a></li>
						<li><a href="#spells2" data-toggle="tab">II</a></li>
						<li><a href="#spells3" data-toggle="tab">III</a></li>
						<li><a href="#spells4" data-toggle="tab">IV</a></li>
						<li><a href="#spells5" data-toggle="tab">V</a></li>
						<li><a href="#spells6" data-toggle="tab">VI</a></li>
						<li><a href="#spells7" data-toggle="tab">VII</a></li>
						<li><a href="#spells8" data-toggle="tab">VIII</a></li>
						<li><a href="#spells9" data-toggle="tab">IX</a></li>
						<li><a href="#spells10" data-toggle="tab">X</a></li>
					</ul>
					<div class="tab-content spellList">
					<?php 
						$spellarray = getSpells($magicmastery); 
						$count = sizeof($spellarray);
						$lastpage = 0;
						$i = 0;
						
						foreach ($spellarray as $spell) {
							$name = $spell['name'];
							$mpcost = $spell['mpcost'];
							$intreq = $spell['intreq'];
							$inwiz = $spell['inwiz'];
							$page = $spell['page'];
							
							if ($inwiz == 0) {
								$spellclass = "rare";
							} else {
								$spellclass = "";
							}
							
							if ($page != 1 && $page != $lastpage) {
								echo "</tbody></table>";
								echo "</div>";
							}
							
							if ($page > $lastpage + 1) {
								for ( $j = $lastpage + 1; $j < $page; $j++ ) {
									echo "<div class='tab-pane' id='spells$j'>No spells in this circle.<br /><br /></div>";
								}
							}
							
							if ($page != $lastpage) {
								echo "<div class='tab-pane' id='spells$page'>";
								echo "<table class='table table-condensed table-bordered table-striped'><thead><th>Spell</th><th>MP Cost</th><th>INT Required</th></thead><tbody>";
							}
							
							echo "<tr class='$spellclass'><td>$name</td><td class='mpcost'><span>$mpcost</span><input type='hidden' class='origmpcost' value='$mpcost' /></td><td>$intreq</td></tr>";

							$lastpage = $page;
							$i++;
							
							if ($i == $count) {
								echo "</tbody></table>";
								echo "</div>";
							}
						}
						
						if ($lastpage < 10) {
							for ( $j = $lastpage + 1; $j <= 10; $j++ ) {
								echo "<div class='tab-pane' id='spells$j'>No spells in this circle.<br /><br /></div>";
							}
						}
					?>
					</div>
				</div>
			</div>
			
			<div class="widget">
				<h2>
					<div class="pull-right guildlistdiv">
						<a href="#" class="collapse"><i class="icon-chevron-up icon-white"></i></a>
					</div>
					Skills
				</h2>
				<div class="widget-inner">
					<table id="skillsTable" class="table table-bordered table-striped table-condensed">
						<thead>
							<tr>
								<th>Skill</th>
								<th>Mastery</th>
							</tr>
						</thead>
						<tbody>
						<?php
							for ( $i = 0; $i < Num_Rows($result3); $i++ ) {
								$skillid = Result($result3, $i, "sSkillID");
								$skillmastery = Result($result3, $i, "sSkillMastery") . "%";
								$skill = getSkillName($skillid);
								
								echo "<tr rel='$skillid'><td>$skill</td><td>$skillmastery</td></tr>";
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