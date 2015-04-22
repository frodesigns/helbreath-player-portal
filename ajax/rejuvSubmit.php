<?php
	if($_POST) {
		$serverrootpath = $_SERVER['DOCUMENT_ROOT'];
		$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
		$rootpath = "/portal";
		
		include("$serverrootpath/includes/db.inc.php");
		include("$includepath/common.php");
		
		page_protect();
		$user_id = $_SESSION['user_id'];
		
		$result10 = QueryWS1("SELECT convert(varchar(10), getdate(), 111)");
		list($thedate) = mssql_fetch_row($result10);
		
		$charid = str_replace( "'", "''", stripslashes($_POST["charid"]) );
		$str = str_replace( "'", "''", stripslashes($_POST["str"]) );
		$dex = str_replace( "'", "''", stripslashes($_POST["dex"]) );
		$int = str_replace( "'", "''", stripslashes($_POST["int"]) );
		$mag = str_replace( "'", "''", stripslashes($_POST["mag"]) );
		$vit = str_replace( "'", "''", stripslashes($_POST["vit"]) );
		$chr = str_replace( "'", "''", stripslashes($_POST["chr"]) );
		
		$rejuvskills = Array();
		
		foreach ($_POST as $k=>$v) {
			$k = str_replace( "'", "''", stripslashes($k));
			
			if ( $k != "charid" && $k != "charname" && $k != "str" && $k != "dex" && $k != "int" && $k != "mag" && $k != "vit" && $k != "chr" ) {			
				$rejuvskills[$k] = str_replace( "'", "''", stripslashes($v)); 
			}
		}
		
		$totalStats = $str + $dex + $int + $mag + $vit + $chr;
		
		$result = QueryWS1("SELECT cCharName, cAccountID, sLevel, bGender, iExp, cNation, cMapLoc, cGuildName, iGuildID, sGuildRank, cProfile, iContribution, iHP, iMP, iSP, iEK, iPK, iGizoneItemUpgradeLeft, iPopular, cMagicMastery, sID1, sID2, sID3 FROM CHARACTER_T WHERE CharID = $charid");
		list($charname, $accountname, $level, $gender, $exp, $nation, $location, $guild, $guildid, $guildrank, $profile, $contrib, $hp, $mp, $sp, $eks, $pks, $maj, $rep, $magicmastery, $charsID1, $charsID2, $charsID3) = mssql_fetch_row($result);
		
		$isp2p = isP2P($accountname);
		
		if ($level == 180 && $totalStats == 367 && $charid > 0 && $str >= 10 && $dex >= 10 && $int >= 10 && $mag >= 10 && $vit >= 10 && $chr >= 10 && $str <= 200 && $dex <= 200 && $int <= 200 && $mag <= 200 && $vit <= 200 && $chr <= 200) {			
			
			//update stats, exp, level
			$result2 = QueryWS1("Update CHARACTER_T set iExp = 5929982, sLevel = 100, sMag = $mag, sVit = $vit, sStr = $str, sDex = $dex, sChar = $chr, sInt = $int where CharID = $charid");
			
			//update skills percents
			foreach ($rejuvskills as $k=>$v) {
				$result3 = QueryWS1("UPDATE SKILL_T SET sSkillMastery = $v WHERE sSkillID = $k AND CharID = $charid");
			}
			
			//delete phantom items
			$delete1 = QueryWS1("DELETE FROM ITEM_T WHERE CharID = $charid AND sItemID = 0");
			$delete2 = QueryWS1("DELETE FROM BANKITEM_T WHERE CharID = $charid AND sItemID = 0");
			
			//get next item id
			$result5 = QueryWS1("select sID1, sID2, sID3 from dbo.udfGetNextItemID('$thedate')");
			list($sid1, $sid2, $sid3) = mssql_fetch_row($result5);
			
			//insert rej emblem
			$result6 = QueryWS1("INSERT INTO BANKITEM_T (sItemID, sID1, sID2, sID3, CharID, sEffect1, sEffect2, sEffect3, iAttribute, iCount, iLifeSpan, sColor, sItemType) VALUES (964, $sid1, $sid2, $sid3, $charid, 0, 0, 0, 0, 1, 1, 0, 1)");
			
			if ( $isp2p ) {				
				//insert 3 exp slates
				for ($i = 1; $i <= 3; $i++) {
					//increment serial (sID3) for next item
					$sid3++;
					
					$result7 = QueryWS1("INSERT INTO BANKITEM_T (sItemID, sID1, sID2, sID3, CharID, sEffect1, sEffect2, sEffect3, iAttribute, iCount, iLifeSpan, sColor, sItemType) VALUES (867, $sid1, $sid2, $sid3, $charid, 0, 4, 0, 0, 1, 1, 7, 3)");
				}
				
				//insert 3 boot leathers
				$result8 = QueryWS1("INSERT INTO BANKITEM_T (sItemID, sID1, sID2, sID3, CharID, sEffect1, sEffect2, sEffect3, iAttribute, iCount, iLifeSpan, sColor, sItemType) VALUES (720, 0, 0, 0, $charid, 0, 0, 0, 0, 3, 1, 6, 0)");
			}
			
			//write to log file
			$formatted_newtime = date("d/m/y g:i a", time());
			
			$data = "$formatted_newtime: " . $_SERVER['REMOTE_ADDR']. ", $accountname, INT($int), MAG($mag), VIT($vit), STR($str), DEX($dex), CHR($chr)";
			
			foreach ($rejuvskills as $k=>$v) {
				$data .= " " . getSkillName($k) . "($v)";
			}			
			$data .= " SET for $charname\n";
			
			$fp = fopen("c:\logs\Rejuv-Portal.log", "a");
			fwrite($fp, $data);
			fclose($fp);
			
			echo json_encode(array('success' => true, 'message' => 'Success!'));
		} else {
			echo json_encode(array('success' => false, 'message' => 'Error: Form filled out incorrectly.'));
		}

	}
?>