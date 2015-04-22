<?php
	if($_POST) {
		$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
		$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
		$rootpath = "/portal";
		
		include("$serverrootpath/includes/db.inc.php");
		include("$includepath/common.php");
		
		page_protect();
		$user_id = $_SESSION['user_id'];
	
		$guildid = str_replace( "'", "''", stripslashes(trim($_POST["guildid"])) );
		
		if($guildid) {			
			$result = QueryWS1("SELECT c.CharID, c.sLevel, c.sGuildRank, c.iEK, c.iPK, c.iGizoneItemUpgradeLeft, c.cCharName FROM CHARACTER_T c INNER JOIN mainlogin.dbo.ACCOUNT_T a ON c.cAccountID = a.cAccountID WHERE (c.iGuildID = '$guildid') AND (a.BlockDate < GETDATE())");	
			
			$masters = "";
			$eks = 0;
			$pks = 0;
			$maj = 0;
			$members = Num_Rows($result);
			$q = 1;
			
			for ( $k = 0; $k < Num_Rows($result); $k++ ) {
				$charid = Result($result, $k, "CharID");
				
				if (Result($result, $k, "sGuildRank") == 0) {
					if ($q == 1) {
						$masters .= Result($result, $k, "cCharName");
					} else {
						$masters .= ", " . Result($result, $k, "cCharName");
					}
					$q++;
				}
									
				$eks += Result($result, $k, "iEK");
				$pks += Result($result, $k, "iPK");
				$maj += Result($result, $k, "iGizoneItemUpgradeLeft");
			}
			
			$levelMin = 255;
			$levelMax = 0;
			$levelTotal = 0;

			for ( $j = 0; $j < $members; $j++ )
			{
				$level = Result($result, $j, "sLevel");
				if ( $level > $levelMax )
					$levelMax = $level;
				if ( $level < $levelMin )
					$levelMin = $level;
				$levelTotal += $level;
			}
			
			$result2 = QueryWS1("select cGuildName FROM GUILD_T WHERE iGuildID = $guildid");
			list($guildname) = mssql_fetch_row($result2);
			
			$result3 = QueryWS1("select Grand_Aggregate from GuildContribs_Rankings_vw WHERE Guild_Name = '$guildname' order by Grand_Aggregate desc");
			list($totalquests) = mssql_fetch_row($result3);

			if ( $j )
			{
				$levelTotal /= $j;

				if ( $levelMax != 0 )
				{
					echo "<div class='row-fluid'>";
					echo "<div class='span6'>";
					echo "<h3>Overall Guild</h3>";
					echo "<strong>Total Members:</strong> $members<br />";
					echo "<strong>Guildmasters:</strong> $masters<br />";
					echo "<strong>Level Range/Avg:</strong> $levelMin - $levelMax (".(int)($levelTotal + 0.5).")<br /><br />";
					echo "<a class='btn btn-info' href='$rootpath/guildmembers.php?guildid=$guildid'>View Guild Page</a>";
					echo "</div>";
					echo "<div class='span6'>";
					echo "<h3>PvP</h3>";
					echo "<strong>Total EKs:</strong> $eks<br />";
					echo "<strong>Total Criminal Count:</strong> $pks<br />";
					echo "<h3>PvM</h3>";
					echo "<strong>Total Quests Completed:</strong> $totalquests<br />";
					echo "<strong>Total Majestic Points:</strong> $maj";
					echo "</div>";
					echo "</div>";
				}
			}
			
		} else { 
			echo "Error";
		}
	}
?>