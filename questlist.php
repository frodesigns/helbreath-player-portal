<?php
	$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	$rootpath = "/portal";
	
	include("$serverrootpath/includes/db.inc.php");
	include("$includepath/common.php");
	
	user_protect();
	
	$result = QueryWS1( "select * from QuestList_webvw order by Sort, MinLevel, MaxLevel" );
	
	//keep at bottom
	include "$includepath/base.php";
?>

<?php startblock('title') ?>
	Quest List
<?php endblock() ?>

<?php startblock('header') ?>
	Quest List
<?php endblock() ?>

<?php startblock('additionalScripts') ?>

<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li class="active">Quest List</a>
<?php endblock() ?>

<?php startblock('containerContent') ?>
	<div class="row-fluid">
		<div class="span12">
			<table id="questsTable" class="table table-striped table-bordered table-condensed sortable">
				<thead>
					<tr>
						<th>Objective</th>
						<th>Map</th>			
						<th>Reward</th>
						<th>For Levels</th>
						<th>Expires</th>
						<th>"Set Contribs"</th>
					</tr>
				</thead>
				<tbody>
				<?php	
					for ( $i = 0; $i < Num_Rows($result); $i++ ) {
						$set_contribs = Result($result, $i, "Set_Contribs");
						$map = Result($result, $i, "Map");
						$objective = Result($result, $i, "Objective");
						$reward = Result($result, $i, "Reward");
						$minlevel = Result($result, $i, "MinLevel");
						$maxlevel = Result($result, $i, "MaxLevel");
						$expires = Result($result, $i, "Expires");
						
						$lvlrange = $minlevel . "-" . $maxlevel;
						
						if ($minlevel == $maxlevel) {
							$lvlrange = $maxlevel;
						}
						
						echo "<tr><td>$objective</td><td>$map</td><td>$reward</td><td>$lvlrange</td><td>$expires</td><td>$set_contribs</td></tr>";
					}
				?>
				</tbody>
			</table>
		</div>
	</div>
<?php endblock() ?>