<?php
	$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	$rootpath = "/portal";
	
	include("$serverrootpath/includes/db.inc.php");
	include("$includepath/common.php");
	
	page_protect();
	
	$char = $_GET['char'];
	$user_id = $_SESSION['user_id'];
	
	$yourchar = isYourCharacter($char);
	
	if ($yourchar) {
		$result = QueryWS1("SELECT CharID, cAccountID, sLevel, bGender, iExp, cNation, cMapLoc, cGuildName, iGuildID, sGuildRank, sStr, sDex, sVit, sMag, sInt, sChar, cProfile, iContribution, iHP, iMP, iSP, iEK, iPK, iGizoneItemUpgradeLeft, iPopular, cMagicMastery, sID1, sID2, sID3 FROM CHARACTER_T WHERE cCharName = '$char'");
		list($charid, $accountname, $level, $gender, $exp, $nation, $location, $guild, $guildid, $guildrank, $str, $dex, $vit, $mag, $int, $cha, $profile, $contrib, $hp, $mp, $sp, $eks, $pks, $maj, $rep, $magicmastery, $charsID1, $charsID2, $charsID3) = mssql_fetch_row($result);
		
		$isp2p = isP2P($accountname);
		
		if ( $isp2p ) {
			$slots = 5;
		} else {
			$slots = 1;									
		}
		
		$result3 = QueryWS1("SELECT sSkillID, sSkillMastery, iSkillSSN FROM SKILL_T WHERE CharID = '$charid' AND (iSkillSSN <> '' OR sSkillID = 13)");
		
		$result5 = QueryWS1("SELECT COUNT(*) FROM BANKITEM_T WHERE CharID = $charid AND sItemID > 0");
		list($whcount) = mssql_fetch_row($result5);
	}
	
	//keep at bottom
	include "$includepath/base.php";
?>

<?php startblock('title') ?>
	Rejuvenate
<?php endblock() ?>

<?php startblock('header') ?>
	Rejuvenate <?php echo $char; ?>
<?php endblock() ?>

<?php startblock('additionalScripts') ?>
	<script type="text/javascript" src="<?php echo $rootpath; ?>/js/rejuvenate.js"></script>
<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li><a href="<?php echo $rootpath; ?>/characters.php">Characters</a></li> <span class="divider">/</span>
	<li><a href="<?php echo $rootpath; ?>/charstats.php?charname=<?php echo $char; ?>"><?php echo $char; ?></a></li> <span class="divider">/</span>
	<li class="active">Rejuvenate</a>
<?php endblock() ?>

<?php startblock('containerContent') ?>
	<div class="row-fluid">
	<?php if ($yourchar) { ?>
		<div class="span12">
			<h2>ONLY USE IF <?php echo $char; ?> IS <em>OFFLINE</em>! Refresh this page after logging out.</h2><br />
			<h3>Things to be aware of before filling out the form:</h3>
			<ul>
				<li>That the character is <strong>offline</strong>. Changes will not be saved if your character is online.</li>
				<li>Make sure your character is level <strong>180</strong>.</li>
				<li>There is <strong>no limit</strong> to how many times you can adjust your characters stats once your character obtains level 180 again.</li>
				<li><strong>You must spend all 367 stat points</strong> with a minimum of 10 stat points and a maximum of 200 stat points.</li>
				<li><strong>Your character's level will be reset to level 100</strong>.</li>
				<li><strong><u>All</u> entries are logged</strong>, any attempt to abuse or take advantage of this page in ways that it wasn't designed for will result in your character being blocked indefinitely.</li>
				<li><strong>F2P</strong> characters will receive <strong>1 x Rejuventation Emblem</strong>.</li>
				<li><strong>P2P</strong> characters will receive <strong>1 x Rejuventation Emblem, 3 x EXP Slates, and 3 x Boot Leathers</strong>.</li>
			</ul>
			<br />
		</div>
	</div>
	<div class="row-fluid">
		<div class="span6">
			<div class="widget">
				<h2>New Stats</h2>
				<div class="widget-inner">
					<form method="post" action="<?php echo $rootpath; ?>/ajax/rejuvSubmit.php">
						<div class="control-group">
							<label class="control-label" for="str">STR:</label>
							<div class="controls">						
								<input type="number" id="str" name="str" />					
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="dex">DEX:</label>
							<div class="controls">						
								<input type="number" id="dex" name="dex" />					
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="int">INT:</label>
							<div class="controls">						
								<input type="number" id="int" name="int" />					
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="mag">MAG:</label>
							<div class="controls">						
								<input type="number" id="mag" name="mag" />					
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="vit">VIT:</label>
							<div class="controls">						
								<input type="number" id="vit" name="vit" />					
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="chr">CHR:</label>
							<div class="controls">						
								<input type="number" id="chr" name="chr" />
								<p class="help-block">Remaining Stat Points: <span id="remainingstats"></span></p>
							</div>
						</div>
						<div>
							You have <?php echo 120 - $whcount; ?> free slots in your Warehouse.
							<?php
								echo "$slots free slots required.";
							?>
						</div>
						<div class="form-actions">
							<input type="hidden" id="charid" name="charid" value="<?php echo $charid; ?>" />
							<input type="hidden" id="charname" name="charname" value="<?php echo $char; ?>" />
							<?php if ( (120 - $whcount) > $slots ) { ?>
								<button id="rejuvSubmit" class="btn btn-primary" type="submit">Submit</button>
							<?php } else {
								$slotsneeded = $slots - (120 - $whcount);
								echo "<button id='rejuvSubmit' class='btn btn-primary' type='submit' disabled>Submit</button><br />";
								echo "<strong>Free up $slotsneeded slots in your warehouse, log out, and refresh this page to continue.</strong>";
							} ?>
							<br /><br />
							<p class="help-block">All requests are logged. I am an active Helbreath subscriber and completely understand the conditions. I want to reduce the contribution of the character above. I accept that this will wipe my current quest information including my current kill count and any possible reward that I may have gotten from the quest.</p>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="span6">
			<div class="widget">
				<h2>Skills</h2>
				<div class="widget-inner">
					<strong>Red indicates skills that will be changed.</strong><br /><br />
					<table id="skillsTable" class="table table-bordered table-striped table-condensed">
						<thead>
							<tr>
								<th>Skill</th>
								<th>Mastery After Rejuv</th>
								<th>Current Mastery</th>
							</tr>
						</thead>
						<tbody>
						<?php
							for ( $i = 0; $i < Num_Rows($result3); $i++ ) {
								$skillid = Result($result3, $i, "sSkillID");
								$skillmastery = Result($result3, $i, "sSkillMastery") . "%";
								$skill = getSkillName($skillid);
								
								echo "<tr rel='$skillid'><td>$skill</td><td>$skillmastery</td><td>$skillmastery</td></tr>";
							}
						?>
						</tbody>
					</table>
				</div>
			</div>
			
			<div class="widget">
				<h2>Spells</h2>
				<div class="widget-inner">
					<strong>Red indicates spells that will be lost.</strong> Be sure to use a spell removal scroll on your rare/green spells first!<br /><br />
					<table id="spellsTable" class='table table-condensed table-bordered table-striped'>
						<thead>
							<th>Spell</th>
							<th>INT Required</th>
							<th>Circle</th>
						</thead>
						<tbody>
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
								
								echo "<tr class='$spellclass'><td>$name</td><td>$intreq</td><td>$page</td></tr>";
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