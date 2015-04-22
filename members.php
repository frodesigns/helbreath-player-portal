<?php
	$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	$rootpath = "/portal";
	
	include("$serverrootpath/includes/db.inc.php");
	include("$includepath/common.php");
	
	user_protect();
	if (isset($_SESSION['user_id'])) {
		$user_id = $_SESSION['user_id'];
	}
	
	$result = QueryWS1("SELECT PPLoginID, DisplayAlias, RegDate FROM PlayerPortalLogin WHERE Approved = 1 ORDER BY RegDate DESC");
	
	//keep at bottom
	include "$includepath/base.php";
?>

<?php startblock('title') ?>
	Members
<?php endblock() ?>

<?php startblock('header') ?>
	Members
<?php endblock() ?>

<?php startblock('additionalScripts') ?>

<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li class="active">Members</a>
<?php endblock() ?>

<?php startblock('containerContent') ?>
	<div class="row-fluid">
		<div class="span12">
			<h3>Members - <?php echo Num_Rows($result); ?></h3>
			<table id="membersTable" class="table table-bordered table-striped sortable">
				<thead>
					<tr>
						<th>Display Name</th>
						<!--<th>Accounts</th>-->
						<th>Registration Date</th>
					</tr>
				</thead>
				<tbody>
				<?php
					for ( $i = 0; $i < Num_Rows($result); $i++ ) {
						$userid = Result($result, $i, "PPLoginID");
						$displayalias = Result($result, $i, "DisplayAlias");
						$timestamp = getRelativeTime(Result($result, $i, "RegDate"));
						
						echo "<tr>";
							echo "<td><a href='$rootpath/userprofile.php?username=$displayalias'>$displayalias</a></td>";
							
							// $accounts = "";
							// $result2 = QueryWS1("SELECT AccountName, DisplayAlias FROM PlayerPortalLinkedAccount WHERE PPLoginID = '$userid' ORDER BY DisplayAlias ASC");
							// for ( $j = 0; $j < Num_Rows($result2); $j++ ) {
								// if ($j == 0) {
									// $accounts .= Result($result2, $j, "DisplayAlias");
								// } else {
									// $accounts .= ", " . Result($result2, $j, "DisplayAlias");
								// }
							// }							
							// echo "<td>$accounts</td>";
							
							echo "<td>$timestamp</td>";
						echo "</tr>";
					}
				?>
				</tbody>
			</table>
		</div>
	</div>
<?php endblock() ?>