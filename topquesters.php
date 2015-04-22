<?php
	$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	$rootpath = "/portal";
	
	include("$serverrootpath/includes/db.inc.php");
	include("$includepath/common.php");
	
	user_protect();
	
	$query = "select * from CharacterContribs_Rankings_vw order by Grand_Aggregate desc, Rising_Tide desc";
	$result = QueryWS1($query);

	//keep at bottom
	include "$includepath/base.php";
?>

<?php startblock('title') ?>
	Top Questers
<?php endblock() ?>

<?php startblock('header') ?>
	Top Questers
<?php endblock() ?>

<?php startblock('additionalScripts') ?>

<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li class="active">Top Questers</a>
<?php endblock() ?>

<?php startblock('containerContent') ?>
	<div class="row-fluid">
		<div class="span12">
			<table class="table table-striped table-bordered table-condensed">
				<thead>
					<tr>
						<th>Rank</th>
						<th>Account Name</th>
						<th>Grand Aggregate (Total Quests)</th>
						<th>High Water Mark (Best Map/Zone)</th>
						<th>Rising Tide ("Worst" Map/Zone) </th>
					</tr>
				</thead>
				<tbody>
				<?php
					for ( $i = 0; $i < Num_Rows($result); $i++ ) {
						$accountid = Result($result, $i, "AccountID");
						$accountname = Result($result, $i, "AccountName");
						$grandaggregate = Result($result, $i, "Grand_Aggregate");	
						$highwatermark = Result($result, $i, "High_Water_Mark");
						$risingtide = Result($result, $i, "Rising_Tide");
						$rank = $i + 1;
						
						echo "<tr>";
						echo "<td>$rank</td>";
						echo "<td>$accountname</td>";
						echo "<td>$grandaggregate</td>";	
						echo "<td>$highwatermark</td>";
						echo "<td>$risingtide</td>";
						echo "</tr>";
					}
				?>
				</tbody>
			</table>
		</div>
	</div>
<?php endblock() ?>