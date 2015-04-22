<?php
	$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	$rootpath = "/portal";
	
	include("$serverrootpath/includes/db.inc.php");
	include("$includepath/common.php");
	
	user_protect();
	
	$queryares = "select g.cGuildName, g.iGuildID from GUILD_T g where cLocation = 'aresden' AND (SELECT COUNT(c.cCharName) FROM CHARACTER_T c INNER JOIN mainlogin.dbo.ACCOUNT_T a ON c.cAccountID = a.cAccountID WHERE (c.iGuildID = g.iGuildID) AND (a.BlockDate < GETDATE()) AND (c.FileSaveDate > GETDATE()-30)) > 5 order by cGuildName";
	$resultares = QueryWS1($queryares);
	
	$queryelv = "select g.cGuildName, g.iGuildID from GUILD_T g where cLocation = 'elvine' and (SELECT COUNT(c.cCharName) FROM CHARACTER_T c INNER JOIN mainlogin.dbo.ACCOUNT_T a ON c.cAccountID = a.cAccountID WHERE (c.iGuildID = g.iGuildID) AND (a.BlockDate < GETDATE()) AND (c.FileSaveDate > GETDATE()-30)) > 5 order by cGuildName";
	$resultelv = QueryWS1($queryelv);
	
	//keep at bottom
	include "$includepath/base.php";
?>

<?php startblock('title') ?>
	Guilds
<?php endblock() ?>

<?php startblock('header') ?>
	Guilds
<?php endblock() ?>

<?php startblock('additionalScripts') ?>

<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li class="active">Guilds</a>
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
								<th>Guild</th>
								<th>Guildmasters</th>
								<th>Active Characters</th>
								<th>EKs</th>
								<th>Level Range/Avg</th>
							</tr>
						</thead>
						<tbody>
						<?php
							for ( $i = 0; $i < Num_Rows($resultares); $i++ ) {
								$guildname = Result($resultares, $i, "cGuildName");
								$masters = "";
								$eks = 0;
								$guildID = Result($resultares, $i, "iGuildID");
								$q = 1;
								
								$resultares2 = QueryWS1("SELECT c.sLevel, c.sGuildRank, c.iEK, c.cCharName FROM CHARACTER_T c INNER JOIN mainlogin.dbo.ACCOUNT_T a ON c.cAccountID = a.cAccountID WHERE (c.iGuildID = ".$guildID.") AND (a.BlockDate < GETDATE()) AND (c.FileSaveDate > GETDATE()-30)");
								
								for ( $k = 0; $k < Num_Rows($resultares2); $k++ ) {
									if (Result($resultares2, $k, "sGuildRank") == 0) {
										if ($q == 1) {
											$masters .= Result($resultares2, $k, "cCharName");
										} else {
											$masters .= "<br />" . Result($resultares2, $k, "cCharName");
										}
										$q++;
									}
									$eks += Result($resultares2, $k, "iEK");
								}
								
								$members = Num_Rows($resultares2);

								if ( $members > 5 )
								{
									$levelMin = 255;
									$levelMax = 0;
									$levelTotal = 0;

									for ( $j = 0; $j < $members; $j++ )
									{
										$level = Result($resultares2, $j, "sLevel");
										if ( $level > $levelMax )
											$levelMax = $level;
										if ( $level < $levelMin )
											$levelMin = $level;
										$levelTotal += $level;
									}

									if ( $j )
									{
										$levelTotal /= $j;

										if ( $levelMax != 0 )
										{
											echo "<tr><td>$guildname</td><td>$masters</td><td>$members</td><td>$eks</td><td>$levelMin - $levelMax (".(int)($levelTotal + 0.5).")</td></tr>";
										}
									}
								}
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
								<th>Guild</th>
								<th>Guildmasters</th>
								<th>Active Characters</th>
								<th>EKs</th>
								<th>Level Range/Avg</th>
							</tr>
						</thead>
						<tbody>
						<?php
							for ( $i = 0; $i < Num_Rows($resultelv); $i++ )
							{
								$guildname = Result($resultelv, $i, "cGuildName");
								$guildID = Result($resultelv, $i, "iGuildID");
								$masters = "";
								$q = 1;		
								$eks = 0;
								
								$resultelv2 = QueryWS1("SELECT c.sLevel, c.sGuildRank, c.iEK, c.cCharName FROM CHARACTER_T c INNER JOIN mainlogin.dbo.ACCOUNT_T a ON c.cAccountID = a.cAccountID WHERE (c.iGuildID = ".$guildID.") AND (a.BlockDate < GETDATE()) AND (c.FileSaveDate > GETDATE()-30)");

								for ( $k = 0; $k < Num_Rows($resultelv2); $k++ ) {
									if (Result($resultelv2, $k, "sGuildRank") == 0) {
										if ($q == 1) {
											$masters .= Result($resultelv2, $k, "cCharName");
										} else {
											$masters .= "<br />" . Result($resultelv2, $k, "cCharName");
										}
										$q++;
									}	
									$eks += Result($resultelv2, $k, "iEK");
								}
								
								$members = Num_Rows($resultelv2);

								if ( $members > 5 )
								{
									$levelMin = 255;
									$levelMax = 0;
									$levelTotal = 0;

									for ( $j = 0; $j < $members; $j++ )
									{
										$level = Result($resultelv2, $j, "sLevel");
										if ( $level > $levelMax )
											$levelMax = $level;
										if ( $level < $levelMin )
											$levelMin = $level;
										$levelTotal += $level;
									}

									if ( $j )
									{
										$levelTotal /= $j;

										if ( $levelMax != 0 )
										{
											echo "<tr><td>$guildname</td><td>$masters</td><td>$members</td><td>$eks</td><td>$levelMin - $levelMax (".(int)($levelTotal + 0.5).")</td></tr>";
										}
									}
								}
							}
						?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
<?php endblock() ?>