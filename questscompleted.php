<?php
	$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	$rootpath = "/portal";
	
	include("$serverrootpath/includes/db.inc.php");
	include("$includepath/common.php");
	
	page_protect();
	
	$charname = $_GET['charname'];
	$user_id = $_SESSION['user_id'];
	
	$yourchar = isYourCharacter($charname);
	
	if ($yourchar) {
		$result = QueryWS1("SELECT cAccountID, sGuildRank, CharID FROM CHARACTER_T WHERE cCharName = '$charname'");
		list($accountname, $guildrank, $charid) = mssql_fetch_row($result);
		
		$result3 = QueryWS1("SELECT 'Map' = CASE WHEN RangeFrom = 0 THEN 'GRAND TOTAL' ELSE Map END
			, 'Quests_Completed' = TotalContribs
			, 'Contribs_CashedIn' = CASE WHEN RangeFrom = 0 THEN TotalContribs - UnusedContribs ELSE NULL END
			, 'Available_Contribs' = CASE WHEN RangeFrom = 0 THEN UnusedContribs ELSE NULL END
		FROM CharacterContribs_vw
		WHERE CharID = $charid
		ORDER BY RangeTo");	
	}
	
	//keep at bottom
	include "$includepath/base.php";
?>

<?php startblock('title') ?>
	Completed Quests
<?php endblock() ?>

<?php startblock('header') ?>
	<?php echo $charname; ?>'s Completed Quests
<?php endblock() ?>

<?php startblock('additionalScripts') ?>

<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li><a href="<?php echo $rootpath; ?>/characters.php">Characters</a></li> <span class="divider">/</span>
	<li><a href="<?php echo $rootpath; ?>/charstats.php?charname=<?php echo $charname; ?>"><?php echo $charname; ?></a></li> <span class="divider">/</span>
	<li class="active">Completed Quests</a>
<?php endblock() ?>

<?php startblock('containerContent') ?>	
	<div class="row-fluid">
	<?php if ($yourchar) { ?>
		<div class="span12">
			<div class="tab-content">
				<table id="completedQuestsTable" class="table table-bordered table-striped sortable">
					<thead>
						<tr>
							<th>Map</th>
							<th>Quests Completed</th>
							<th>Contribution Cashed In</th>
							<th>Available Contribution</th>
						</tr>
					</thead>
					<tbody>
					<?php
						if (Num_Rows($result3) > 1) {
							for ( $i = 0; $i < Num_Rows($result3); $i++ ) {
								$map = Result($result3, $i, "Map");
								$completed = Result($result3, $i, "Quests_Completed");
								$cashed = Result($result3, $i, "Contribs_CashedIn");
								$available = Result($result3, $i, "Available_Contribs");
								$trclass = "";
								if ($map == "GRAND TOTAL") {
									$trclass = "grandtotal";
								}
								echo "<tr class='$trclass'><td>$map</td><td>$completed</td><td>$cashed</td><td>$available</td></tr>";
							}
						} else {
							echo "<td colspan='4'>No quests found.</td>";
						}
					?>
					</tbody>
				</table>
			</div>
		</div>		
	<?php			
		} else {
			echo "Character not found on any of your linked accounts.";
		}
	?>
	</div>
<?php endblock() ?>