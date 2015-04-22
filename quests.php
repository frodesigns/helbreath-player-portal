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
		$result = QueryWS1("SELECT cAccountID, sGuildRank FROM CHARACTER_T WHERE cCharName = '$charname'");
		list($accountname, $guildrank) = mssql_fetch_row($result);
		
		$result3 = QueryWS1("SELECT QuestID, AccountID, Character, Mob, Map, Status, SaveDT, Expires FROM SavedQuest_vw WHERE Character = '$charname' AND ( Expires = '' OR convert(datetime,Expires) > getdate()) ORDER BY AccountID, Character, SaveDT");
	}
	
	//keep at bottom
	include "$includepath/base.php";
?>

<?php startblock('title') ?>
	Saved Quests
<?php endblock() ?>

<?php startblock('header') ?>
	<?php echo $charname; ?>'s Saved Quests
<?php endblock() ?>

<?php startblock('additionalScripts') ?>

<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li><a href="<?php echo $rootpath; ?>/characters.php">Characters</a></li> <span class="divider">/</span>
	<li><a href="<?php echo $rootpath; ?>/charstats.php?charname=<?php echo $charname; ?>"><?php echo $charname; ?></a></li> <span class="divider">/</span>
	<li class="active">Saved Quests</a>
<?php endblock() ?>

<?php startblock('containerContent') ?>
	<div class="row-fluid">
	<?php if ($yourchar) { ?>
		<div class="span12">
			<div class="tab-content">
				<table id="questsTable" class="table table-bordered table-striped sortable">
					<thead>
						<tr>
							<th>Quest ID</th>
							<th>Mob</th>
							<th>Map</th>
							<th>Status</th>
							<th>SaveDT<i class="sort asc icon-chevron-up"></i></th>
							<th>Expires</th>
						</tr>
					</thead>
					<tbody>
					<?php
						if (Num_Rows($result3) > 0) {
							for ( $i = 0; $i < Num_Rows($result3); $i++ ) {
								//$accountid = Result($result3, $i, "AccountID");
								//$character = Result($result3, $i, "Character");
								$questid = Result($result3, $i, "QuestID");
								$mob = Result($result3, $i, "Mob");
								$map = Result($result3, $i, "Map");
								$status = Result($result3, $i, "Status");
								$savedt = Result($result3, $i, "SaveDT");
								$expires = Result($result3, $i, "Expires");
								$saved = date("F j, Y - g:i a", strtotime($savedt));
								
								echo "<tr id='$questid'><td>$questid</td><td>$mob</td><td>$map</td><td>$status</td><td>$saved</td><td>$expires</td></tr>";
							}
						} else {
							echo "<td colspan='6'>No quests found.</td>";
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