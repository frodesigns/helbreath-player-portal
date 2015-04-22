<?php
	$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	$rootpath = "/portal";
	
	include("$serverrootpath/includes/db.inc.php");
	include("$includepath/common.php");
	
	user_protect();
	
	$query = "select * from TownContribs_Rankings_vw ";
	$result = QueryWS1($query);
	
	//keep at bottom
	include "$includepath/base.php";
?>

<?php startblock('title') ?>
	Town Questing
<?php endblock() ?>

<?php startblock('header') ?>
	Town Questing
<?php endblock() ?>

<?php startblock('additionalScripts') ?>

<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li class="active">Town Questing</a>
<?php endblock() ?>

<?php startblock('containerContent') ?>
	<div class="row-fluid">
		<div class="span12 tab-content">
			<table class="table table-striped table-bordered table-condensed">
				<thead>
					<tr>
						<th>Town</th>
						<th>Total Quests Completed</th>
						<th>Quests Needed For Next Abaddon</th>
					</tr>
				</thead>
				<tbody>
				<?php
					for ( $i = 0; $i < Num_Rows($result); $i++ ) {
						$town = Result($result, $i, "Town");
						$quests = Result($result, $i, "Quests");	
						$nextabby = Result($result, $i, "NextAbby");
						
						echo "<tr>";
						echo "<td>$town</td>";
						echo "<td>$quests</td>";	
						echo "<td>$nextabby</td>";
						echo "</tr>";
					}
				?>
				</tbody>
			</table>
		</div>
	</div>
<?php endblock() ?>