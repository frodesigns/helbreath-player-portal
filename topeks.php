<?php
	$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	$rootpath = "/portal";
	
	include("$serverrootpath/includes/db.inc.php");
	include("$includepath/common.php");
	
	user_protect();
	
	$queryares = "select top 50 ch.CharID, ch.cCharName, ch.sLevel, ch.iEK, 'active' =  CASE WHEN ch.FileSaveDate > GETDATE()-30 THEN 'Yes' ELSE 'No' END
		from ws1.dbo.character_t as ch, mainlogin.dbo.account_t as ml
		where (ch.cNation='aresden') and ml.cAccountID = ch.cAccountID and
		ml.BlockDate < getdate() and sAdminLevel = 0 and ch.cCharName <> 'Dev1n' and ch.cCharName <> 'Aztec' order by ch.iEK desc";
	$resultares = QueryWS1($queryares);
	
	$queryelv = "select top 50 ch.CharID, ch.cCharName, ch.sLevel, ch.iEK, 'active' =  CASE WHEN ch.FileSaveDate > GETDATE()-30 THEN 'Yes' ELSE 'No' END
		from ws1.dbo.character_t as ch, mainlogin.dbo.account_t as ml
		where (ch.cNation='elvine') and ml.cAccountID = ch.cAccountID and
		ml.BlockDate < getdate() and sAdminLevel = 0 and ch.cCharName <> 'Darmok' and ch.cCharName <> 'DeVi11e' and ch.cCharName <> 'Brodoax' order by ch.iEK desc";
	$resultelv = QueryWS1($queryelv);
	
	//keep at bottom
	include "$includepath/base.php";
?>

<?php startblock('title') ?>
	Top Enemy Kills
<?php endblock() ?>

<?php startblock('header') ?>
	Top Enemy Kills
<?php endblock() ?>

<?php startblock('additionalScripts') ?>

<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li class="active">Top Enemy Kills</a>
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
								<th>Character</th>
								<th>EKs</th>
								<th>Active</th>
							</tr>
						</thead>
						<tbody>
						<?php
							for ( $i = 0; $i < Num_Rows($resultares); $i++ ) {
								$character = Result($resultares, $i, "cCharName");
								$charid = Result($resultares, $i, "CharID");
								$eks = Result($resultares, $i, "iEK");		
								$active = Result($resultares, $i, "active");	  
								$rank = $i + 1;
								
								echo "<tr>";
								echo "<td>$rank</td>";
								echo "<td>$character</td>";
								echo "<td>$eks</td>";
								echo "<td>$active</td>";		
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
								<th>Character</th>
								<th>EKs</th>
								<th>Active</th>
							</tr>
						</thead>
						<tbody>
						<?php
							for ( $i = 0; $i < Num_Rows($resultelv); $i++ ) {
								$character = Result($resultelv, $i, "cCharName");
								$charid = Result($resultelv, $i, "CharID");
								$eks = Result($resultelv, $i, "iEK");		
								$active = Result($resultelv, $i, "active");	  
								$rank = $i + 1;
								
								echo "<tr>";
								echo "<td>$rank</td>";
								echo "<td>$character</td>";
								echo "<td>$eks</td>";
								echo "<td>$active</td>";		
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