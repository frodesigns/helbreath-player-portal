<?php
	$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	$rootpath = "/portal";
	
	include("$serverrootpath/includes/db.inc.php");
	include("$includepath/common.php");
	
	page_protect();
	
	$user_id = $_SESSION['user_id'];
	
	$result = QueryWS1("SELECT AccountName, DisplayAlias FROM PlayerPortalLinkedAccount WHERE PPLoginID = '$user_id' ORDER BY DisplayAlias ASC");
	
	//keep at bottom
	include "$includepath/base.php";
?>

<?php startblock('title') ?>
	Account Quest Summary
<?php endblock() ?>

<?php startblock('header') ?>
	Account Quest Summary
<?php endblock() ?>

<?php startblock('additionalScripts') ?>

<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li class="active">Account Quest Summary</a>
<?php endblock() ?>

<?php startblock('containerContent') ?>
	<?php		
	if (Num_Rows($result) == 0) {
		echo "<h3>There seems to be nothing here.</h3>";
		echo "<a href='$rootpath/profile.php'>Add some game accounts!</a>";
	}
	for ( $i = 0; $i < Num_Rows($result); $i++ ) {
		$AccountName = Result($result, $i, "AccountName");
		$DisplayAlias = Result($result, $i, "DisplayAlias");
		
		$result2 = QueryWS1("SELECT AccountID
			, 'Zone' = case when DefaultContribs >= 900 then Zone + '*' 
			  when DefaultContribs = 0 then Zone + ' (excl. *)'
			  else Zone 
			  end
			, TotalContribs
			, UnusedContribs
			, DefaultContribs
			FROM AccountContribs_vw2 
			WHERE AccountID = '$AccountName' 
			ORDER BY CASE WHEN DefaultContribs = 0 THEN DefaultContribs + 9999 ELSE DefaultContribs END");	
	?>
		<h2><?php echo $DisplayAlias . " ($AccountName)"; ?></h2>
		<div class="row-fluid">	
			<table id="<?php echo $AccountName; ?>_quests" class="table table-condensed table-striped table-bordered">
				<thead>
					<tr>
						<th>Zone</th>
						<th>Total Contribs</th>
						<th>Unused Contribs</th>
						<th>Default Contribs</th>
					</tr>
				</thead>
				<tbody>
				<?php
					if (Num_Rows($result2) > 0 ) {
						for ( $j = 0; $j < Num_Rows($result2); $j++ ) {
							$zone = Result($result2, $j, "Zone");
							$total = Result($result2, $j, "TotalContribs");
							$unused = Result($result2, $j, "UnusedContribs");
							$default = Result($result2, $j, "DefaultContribs");
							
							echo "<tr><td>$zone</td><td>$total</td><td>";
							
							if ( Num_Rows($result2) - 1 == $j ) {
								echo "$unused";
							}
							
							echo "</td><td>$default</td></tr>";
						}
					} else {
						echo "<tr><td colspan='4'>No records found.</td></tr>";
					}
				?>
				</tbody>
			</table>
		</div>
	<?php
	}
	?>
<?php endblock() ?>