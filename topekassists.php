<?php
	$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	$rootpath = "/portal";
	
	include("$serverrootpath/includes/db.inc.php");
	include("$includepath/common.php");
	
	user_protect();
	
	$resultares = QueryWS1("select TOP 50 * from EKAssist_vw WHERE Assists > 0 AND Character <> 'TestChar1' AND Nation = 'A' ORDER BY Assists Desc");
	
	$resultelv = QueryWS1("select TOP 50 * from EKAssist_vw WHERE Assists > 0 AND Character <> 'TestChar1' AND Nation = 'E' ORDER BY Assists Desc");
	
	//keep at bottom
	include "$includepath/base.php";
?>

<?php startblock('title') ?>
	Top EK Assists
<?php endblock() ?>

<?php startblock('header') ?>
	Top EK Assists
<?php endblock() ?>

<?php startblock('additionalScripts') ?>

<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li class="active">Top EK Assists</a>
<?php endblock() ?>

<?php startblock('containerContent') ?>
	<div class="row-fluid">
		<div class="span6">
			<div class="widget">
				<h2>
					<div class="pull-right guildlistdiv">
						<a href="#" class="collapse"><i class="icon-chevron-up icon-white"></i></a>
					</div>
					Aresden
				</h2>
				<div class="widget-inner">
					<table class="table table-striped table-bordered table-condensed">
						<thead>
							<tr>
								<th>Rank</th>
								<th>Character Name</th>
								<th>Assists</th>
								<th>Parties</th>
							</tr>
						</thead>
						<tbody>
						<?php
							for ( $i = 0; $i < Num_Rows($resultares); $i++ ) {
								$Character = Result($resultares, $i, "Character");   
								$Assists = Result($resultares, $i, "Assists");      
								$Parties = Result($resultares, $i, "Parties");  
								$rank = $i + 1;
								
								echo "<tr>";
								echo "<td>$rank</td>";
								echo "<td>$Character</td>";
								echo "<td>$Assists</td>";
								echo "<td>$Parties</td>";		
								echo "</tr>";
							}
						?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="span6">
			<div class="widget">
				<h2>
					<div class="pull-right guildlistdiv">
						<a href="#" class="collapse"><i class="icon-chevron-up icon-white"></i></a>
					</div>
					Elvine
				</h2>
				<div class="widget-inner">
					<table class="table table-striped table-bordered table-condensed">
						<thead>
							<tr>
								<th>Rank</th>
								<th>Character Name</th>
								<th>Assists</th>
								<th>Parties</th>
							</tr>
						</thead>
						<tbody>
						<?php
							for ( $i = 0; $i < Num_Rows($resultelv); $i++ ) {
								$Character = Result($resultelv, $i, "Character");   
								$Assists = Result($resultelv, $i, "Assists");      
								$Parties = Result($resultelv, $i, "Parties");  
								$rank = $i + 1;
								
								echo "<tr>";
								echo "<td>$rank</td>";
								echo "<td>$Character</td>";
								echo "<td>$Assists</td>";
								echo "<td>$Parties</td>";		
								echo "</tr>";
							}
						?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
<?php endblock() ?>