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
		list($accountname, $guildrank, $CharID) = mssql_fetch_row($result);
		
		$result3 = QueryWS1("SELECT * FROM EKParty_vw WHERE CharID = $CharID ORDER BY EKPartyID DESC");
		
		$result4 = QueryWS1("select Assists, Parties from EKAssist_vw WHERE Character = '$charname' ORDER BY Assists Desc");
		list($totalassists, $numparties) = mssql_fetch_row($result4);
	}
	
	//keep at bottom
	include "$includepath/base.php";
?>

<?php startblock('title') ?>
	EK Assists
<?php endblock() ?>

<?php startblock('header') ?>
	<?php echo $charname; ?>'s EK Assists
<?php endblock() ?>

<?php startblock('additionalScripts') ?>
	<script type="text/javascript" src="<?php echo $rootpath; ?>/js/ekassists.js"></script>
<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li><a href="<?php echo $rootpath; ?>/characters.php">Characters</a></li> <span class="divider">/</span>
	<li><a href="<?php echo $rootpath; ?>/charstats.php?charname=<?php echo $charname; ?>"><?php echo $charname; ?></a></li> <span class="divider">/</span>
	<li class="active">EK Assists</a>
<?php endblock() ?>

<?php startblock('containerContent') ?>
	<div class="modal hide fade" id="assistsModal">
		<div class="modal-header">
			<button class="close" data-dismiss="modal">&times;</button>
			<h3></h3>
		</div>
		<div class="modal-body">
		</div>		
	</div>
	
	<div class="row-fluid">
	<?php if ($yourchar) { ?>
		<div class="span12">
			<div class="tab-content">
				<h2>Total Assists: <?php echo $totalassists; ?></h2>
				<table id="questsTable" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>Party Name</th>      
							<th>Created</th>
							<th>Duration</th>                                             
							<th>Closed By</th>                                      
							<th>Members</th>                                              
							<th>DQed</th>  
							<th>EKs</th>
							<th>Bonus Rate</th>      
							<th>Assists</th>
						</tr>
					</thead>
					<tbody>
					<?php
						if (Num_Rows($result3) > 0) {
							for ( $i = 0; $i < Num_Rows($result3); $i++ ) {
								$Party_Name = Result($result3, $i, "Party_Name");     
								$Created = Result($result3, $i, "Created");  
								$Duration = Result($result3, $i, "Duration");                                               
								$Closed_By = Result($result3, $i, "Closed_By");                                         
								$Members = Result($result3, $i, "Members");                                                 
								$DQed = Result($result3, $i, "DQed");    
								$EKs = Result($result3, $i, "EKs");    
								$Bonus_Rate = Result($result3, $i, "Bonus_Rate");       
								$Assists = Result($result3, $i, "Assists");  
								$EKPartyID = Result($result3, $i, "EKPartyID");

								echo "<tr>";
								echo "<td>$Party_Name</td>";
								echo "<td>$Created</td>";
								echo "<td>$Duration</td>";
								echo "<td>$Closed_By</td>";
								echo "<td><a class='btnfloat btn btn-info btn-mini getmembers' href='$rootpath/ajax/ekPartyMembers.php?id=$EKPartyID' title='$Party_Name Member List'>View List</a> $Members</td>";
								echo "<td>$DQed</td>";
								echo "<td>$EKs</td>";
								echo "<td>$Bonus_Rate</td>";
								echo "<td>$Assists</td>";
								echo "</tr>";
							}
						} else {
							echo "<td colspan='9'>No EK assist parties found.</td>";
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