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
	Characters
<?php endblock() ?>

<?php startblock('header') ?>
	Characters
<?php endblock() ?>

<?php startblock('additionalScripts') ?>

<?php endblock() ?>

<?php startblock('breadcrumbs') ?>
	<li><a href="<?php echo $rootpath; ?>">Home</a></li> <span class="divider">/</span>
	<li class="active">Characters</a>
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
		
		$result2 = QueryWS1("SELECT TOP 4 cCharName, sLevel, bGender, iExp, cNation, cMapLoc, cGuildName, sGuildRank, sStr, sDex, sVit, sMag, sInt, sChar FROM CHARACTER_T WHERE cAccountID = '$AccountName' ORDER BY FileSaveDate DESC");			
	?>
		<h2><?php echo $DisplayAlias . " ($AccountName)"; ?></h2>
		<div class="row-fluid">			
			<?php
				if (Num_Rows($result2) > 0 ) {
					for ( $j = 0; $j < Num_Rows($result2); $j++ ) {
						$charname = Result($result2, $j, "cCharName");
						$level = Result($result2, $j, "sLevel");
						$gender = Result($result2, $j, "bGender");
						$exp = Result($result2, $j, "iExp");
						$nation = Result($result2, $j, "cNation");
						$location = Result($result2, $j, "cMapLoc");
						$guild = Result($result2, $j, "cGuildName");
						$guildrank = Result($result2, $j, "sGuildRank");
						$str = Result($result2, $j, "sStr");
						$dex = Result($result2, $j, "sDex");
						$vit = Result($result2, $j, "sVit");
						$int = Result($result2, $j, "sInt");
						$mag = Result($result2, $j, "sMag");
						$cha = Result($result2, $j, "sChar");
						
						$chartype = charType($str, $dex, $int, $mag, $vit, $cha);
						$nation = getNation($nation);
						$guild = getGuild($guild, $guildrank);
						
						echo "<div class='span3 character'>";
						echo "<div class='well'>";
						echo "<h3>$charname</h3>";
						// if ($level == 180) {
							// echo "$chartype<br />";
						// }
						echo "$nation<br />";
						echo "$guild<br />";
						echo "Level: $level<br />";
						//echo "Exp: $exp<br />";
						echo "<ul class='nav nav-list'>";
						echo "<li class='nav-header'>Character Info</li>";
						echo "<li><a href='$rootpath/charstats.php?charname=$charname'><i class='icon-signal'></i> Character Stats</a></li>";
						echo "<li><a href='$rootpath/inventory.php?charname=$charname'><i class='icon-th'></i> Inventory</a></li>";
						echo "<li><a href='$rootpath/quests.php?charname=$charname'><i class='icon-book'></i> Saved Quests</a></li>";
						echo "<li><a href='$rootpath/questscompleted.php?charname=$charname'><i class='icon-ok'></i> Completed Quests</a></li>";
						echo "<li><a href='$rootpath/ekassists.php?charname=$charname'><i class='icon-fire'></i> EK Assists</a></li>";
						echo "</ul>";
						echo "</div>";
						echo "</div>";
					}
				} else {
					echo "<div class='span12'><div class='well'>No characters found on this account.</div></div>";
					
				}
			?>
		</div>
	<?php
	}
	?>
<?php endblock() ?>