<?php	
	//start debugging
	error_reporting(E_ALL);
	ini_set('display_errors', True);
	//end debugging
	
	date_default_timezone_set('America/Detroit');
	
	define("COOKIE_TIME_OUT", 60); //specify cookie timeout in days (default is 10 days)
	define('SALT_LENGTH', 9); // salt for password
	define ("ADMIN_LEVEL", 2);
	define ("MODERATOR_LEVEL", 1);
	define ("USER_LEVEL", 0);
	
	function mssql_escape($str)
	{
		if(get_magic_quotes_gpc())
		{
			$str = stripslashes($str);
		}
		return str_replace("'", "''", $str);
	}
		
	function filter($data) {
		$data = trim(htmlentities(strip_tags($data)));
		
		if (get_magic_quotes_gpc())
			$data = stripslashes($data);
		
		$data = mssql_escape($data);
		
		return $data;
	}
	
	function plural($num) {
		if ($num != 1)
			return "s";
	}

	function getRelativeTime($date) {
		//add 1 hour to date for correct timezone
		$diff = time() - (strtotime($date)+(60*60*1));
		if ($diff<60)
			return $diff . " second" . plural($diff) . " ago";
		$diff = round($diff/60);
		if ($diff<60)
			return $diff . " minute" . plural($diff) . " ago";
		$diff = round($diff/60);
		if ($diff<24)
			return $diff . " hour" . plural($diff) . " ago";
		$diff = round($diff/24);
		if ($diff<7)
			return $diff . " day" . plural($diff) . " ago";
		$diff = round($diff/7);
		if ($diff<4)
			return $diff . " week" . plural($diff) . " ago";
		return "on " . date("F j, Y", strtotime($date));
	}
	
	function getRelativeCharTime($date) {
		//subtract 4 hours from date for correct timezone
		$diff = time() - (strtotime($date)+(60*60*-4));
		if ($diff<60)
			return $diff . " second" . plural($diff) . " ago";
		$diff = round($diff/60);
		if ($diff<60)
			return $diff . " minute" . plural($diff) . " ago";
		$diff = round($diff/60);
		if ($diff<24)
			return $diff . " hour" . plural($diff) . " ago";
		$diff = round($diff/24);
		if ($diff<7)
			return $diff . " day" . plural($diff) . " ago";
		$diff = round($diff/7);
		if ($diff<4)
			return $diff . " week" . plural($diff) . " ago";
		return "on " . date("F j, Y", strtotime($date));
	}
	
	function isP2P($account_name) {
		//In MainLogin..Account_T: IF BlockDate is less than today's date, then ValidDate = '1985-01-01' indicates F2P; ValidDate = '1900-01-01' indicates P2P. 
		$result = Query("SELECT BlockDate, ValidDate FROM ACCOUNT_T WHERE cAccountID = '$account_name'");
		list($blockdate, $validdate) = mssql_fetch_row($result);
		
		if ($validdate == 'Jan 1 1900 12:00AM') {
			return true;
		} else {
			return false;
		}
	}
	
	function isYourCharacter($character_name) {
		$user_id = $_SESSION['user_id'];
		
		$result = QueryWS1("SELECT cAccountID FROM CHARACTER_T WHERE cCharName = '$character_name'");
		list($accountname) = mssql_fetch_row($result);
		
		$result2 = QueryWS1("SELECT * FROM PlayerPortalLinkedAccount WHERE PPLoginID = '$user_id' AND AccountName = '$accountname'");
		$num = mssql_num_rows($result2);
		
		if ( $num > 0 || $user_id == 1 ) { 
			return true;
		} else {
			return false;
		}
	}
	
	function isYourGuild($guildid) {
		$user_id = $_SESSION['user_id'];
		
		$result = QueryWS1("SELECT AccountName FROM PlayerPortalLinkedAccount WHERE PPLoginID = '$user_id' ORDER BY DisplayAlias ASC");
		$yours = false;
		
		for ( $i = 0; $i < Num_Rows($result); $i++ ) {
			$AccountName = Result($result, $i, "AccountName");

			$result2 = QueryWS1("SELECT * FROM CHARACTER_T WHERE cAccountID = '$AccountName' AND iGuildID = $guildid");
			
			$num = mssql_num_rows($result2);	
			
			if ( $num > 0 ) { 
				$yours = true;
				break;
			}			
		}
		
		return $yours;
	}
	
	function isGuildmasterOfGuild($user_id, $guildid) {
		$result = QueryWS1("SELECT AccountName FROM PlayerPortalLinkedAccount WHERE PPLoginID = '$user_id' ORDER BY DisplayAlias ASC");
		
		for ( $i = 0; $i < Num_Rows($result); $i++ ) {
			$AccountName = Result($result, $i, "AccountName");

			$result2 = QueryWS1("SELECT * FROM CHARACTER_T WHERE cAccountID = '$AccountName' AND iGuildID = $guildid AND sGuildRank = 0 ORDER BY cCharName ASC");
			
			$num = mssql_num_rows($result2);
		
			if ( $num > 0 ) { 
				return true;
			}			
		}
		
		return false;
	}
	
	function isPPAccountGuildmaster($user_id) {
		$result = QueryWS1("SELECT AccountName FROM PlayerPortalLinkedAccount WHERE PPLoginID = '$user_id' ORDER BY DisplayAlias ASC");
		
		for ( $i = 0; $i < Num_Rows($result); $i++ ) {
			$AccountName = Result($result, $i, "AccountName");

			$result2 = QueryWS1("SELECT iGuildID FROM CHARACTER_T WHERE cAccountID = '$AccountName' AND sGuildRank = 0 ORDER BY cCharName ASC");
			
			$num = mssql_num_rows($result2);
		
			if ( $num > 0 ) { 
				return true;
			}
			
		}
		
		return false;
	}
	
	function getAccountsAndCharacters($user_id) {
		$result = QueryWS1("SELECT AccountName, DisplayAlias FROM PlayerPortalLinkedAccount WHERE PPLoginID = '$user_id' ORDER BY DisplayAlias ASC");
		
		$accounts = array();
		
		for ( $i = 0; $i < Num_Rows($result); $i++ ) {
			$AccountName = Result($result, $i, "AccountName");
			$DisplayAlias = Result($result, $i, "DisplayAlias");

			$result2 = QueryWS1("SELECT TOP 4 cCharName, CharID FROM CHARACTER_T WHERE cAccountID = '$AccountName' ORDER BY cCharName ASC, FileSaveDate DESC");
			
			$characters = array();
			
			for ( $j = 0; $j < Num_Rows($result2); $j++ ) {
				$charname = Result($result2, $j, "cCharName");
				$characters[] = $charname;
			}
			
			$accounts[] = array("accountname" => "$DisplayAlias ($AccountName)", "characters" => $characters);
		}
		
		return $accounts;
		
		//sample usage
		// $last = count($characters) - 1;

		// foreach ($characters as $i => $row) {
			// $isFirst = ($i == 0);
			// $isLast = ($i == $last);
			// $accountname = $row['accountname'];
			// $chars = $row['characters'];
			
			// echo "<li class='nav-header'>$accountname</li>";
			
			// foreach ($chars as $j => $row2) {
				// $charactername = $row2;
				
				// echo "<li><a href='#'>$charactername</a></li>";
			// }
			
			// if (!$isLast) {
				// echo "<li class='divider'></li>";
			// }

		// }
	}
	
	function getCharacters($user_id) {
		$result = QueryWS1("SELECT AccountName, DisplayAlias FROM PlayerPortalLinkedAccount WHERE PPLoginID = '$user_id' ORDER BY DisplayAlias ASC");
		
		$characters = array();
		
		for ( $i = 0; $i < Num_Rows($result); $i++ ) {
			$AccountName = Result($result, $i, "AccountName");
			$DisplayAlias = Result($result, $i, "DisplayAlias");

			$result2 = QueryWS1("SELECT TOP 4 cCharName, CharID FROM CHARACTER_T WHERE cAccountID = '$AccountName' ORDER BY cCharName ASC, FileSaveDate DESC");
			
			for ( $j = 0; $j < Num_Rows($result2); $j++ ) {
				$charname = Result($result2, $j, "cCharName");
				$characters[] = $charname;
			}
			
		}
		natcasesort($characters);
		return $characters;
	}
	
	function getGuilds($user_id) {
		$result = QueryWS1("SELECT AccountName, DisplayAlias FROM PlayerPortalLinkedAccount WHERE PPLoginID = '$user_id' ORDER BY DisplayAlias ASC");
		
		$guilds = array();
		
		for ( $i = 0; $i < Num_Rows($result); $i++ ) {
			$AccountName = Result($result, $i, "AccountName");
			$DisplayAlias = Result($result, $i, "DisplayAlias");

			$result2 = QueryWS1("SELECT cGuildName, iGuildID FROM CHARACTER_T WHERE cAccountID = '$AccountName' ORDER BY cCharName ASC");
			
			for ( $j = 0; $j < Num_Rows($result2); $j++ ) {
				$guildname = trim(Result($result2, $j, "cGuildName"));
				$guildname = str_replace("_", " ", $guildname);
				$guildid = Result($result2, $j, "iGuildID");
				
				if (!array_key_exists("$guildid", $guilds) && $guildname != "NONE") {
					$guilds[$guildid] = $guildname;
				}				
			}
			
		}
		natcasesort($guilds);
		return $guilds;
	}
	
	function getGuildmasterGuilds($user_id) {
		$result = QueryWS1("SELECT AccountName, DisplayAlias FROM PlayerPortalLinkedAccount WHERE PPLoginID = '$user_id' ORDER BY DisplayAlias ASC");
		
		$guilds = array();
		
		for ( $i = 0; $i < Num_Rows($result); $i++ ) {
			$AccountName = Result($result, $i, "AccountName");
			$DisplayAlias = Result($result, $i, "DisplayAlias");

			$result2 = QueryWS1("SELECT cGuildName, iGuildID FROM CHARACTER_T WHERE cAccountID = '$AccountName' AND sGuildRank = 0 ORDER BY cCharName ASC");
			
			for ( $j = 0; $j < Num_Rows($result2); $j++ ) {
				$guildname = trim(Result($result2, $j, "cGuildName"));
				$guildname = str_replace("_", " ", $guildname);
				$guildid = Result($result2, $j, "iGuildID");
				
				if (!array_key_exists("$guildid", $guilds) && $guildname != "NONE") {
					$guilds[$guildid] = $guildname;
				}				
			}
			
		}
		natcasesort($guilds);
		return $guilds;
	}
	
	function getCharIDs($user_id) {
		$result = QueryWS1("SELECT AccountName, DisplayAlias FROM PlayerPortalLinkedAccount WHERE PPLoginID = '$user_id' ORDER BY DisplayAlias ASC");
		
		$characters = array();
		
		for ( $i = 0; $i < Num_Rows($result); $i++ ) {
			$AccountName = Result($result, $i, "AccountName");
			$DisplayAlias = Result($result, $i, "DisplayAlias");

			$result2 = QueryWS1("SELECT cCharName, CharID FROM CHARACTER_T WHERE cAccountID = '$AccountName' ORDER BY cCharName ASC");
			
			for ( $j = 0; $j < Num_Rows($result2); $j++ ) {
				$charid = Result($result2, $j, "CharID");
				$characters[] = $charid;
			}
			
		}
		natcasesort($characters);
		return $characters;
	}
	
	function charType($str, $dex, $int, $mag, $vit, $cha) {
		if ($str >= 130 && $dex >= 100 && $mag <= 70) {
			$chartype = "Warrior";
		} else if ($mag >= 50 && $int >= 112 && $str >= 130) {
			$chartype = "AMP Warrior";
		} else if ($mag >= 130 && $int >= 112 && $dex <= 70 && $str <= 52) {
			$chartype = "Mage";
		} else if ($mag >= 130 && $str >= 100 && $dex <= 70) {
			$chartype = "Plate Mage";
		} else if ($mag >= 130 && $str >= 84 && $dex >= 70) {
			$chartype = "Battle Mage";
		} else {
			$chartype = "Mystery";
		}
		
		return $chartype;
	}
	
	function getHeroEKCost($sItemID) {
		if ($sItemID >= 400 && $sItemID <= 401) {
			$ekcost = 300;
		} else if ($sItemID >= 403 && $sItemID <= 406) {
			$ekcost = 150;
		} else if ($sItemID >= 407 && $sItemID <= 410) {
			$ekcost = 100;
		} else if ($sItemID >= 411 && $sItemID <= 414) {
			$ekcost = 300;
		} else if ($sItemID >= 415 && $sItemID <= 418) {
			$ekcost = 200;
		} else if ($sItemID >= 419 && $sItemID <= 422) {
			$ekcost = 100;
		} else if ($sItemID >= 423 && $sItemID <= 426) {
			$ekcost = 150;
		} else if ($sItemID >= 427 && $sItemID <= 428) {
			$ekcost = 330;
		} else {
			$ekcost = 0;
		}
		
		return $ekcost;
	}
	
	function getMapName($map) {
		$map = trim($map);
		
		if ($map == "default") {
			$map = "Beginner Zone";
		} else if ($map == "elvine") {
			$map = "Elvine";
		} else if ($map == "aresden") {
			$map = "Aresden";
		} else if ($map == "middleland") {
			$map = "Middle Land";
		} else if ($map == "elvuni") {
			$map = "Elvine Garden";
		} else if ($map == "areuni") {
			$map = "Aresden Garden";
		} else if ($map == "huntzone1") {
			$map = "Rocky Highland";
		} else if ($map == "huntzone2") {
			$map = "Eternal Field";
		} else if ($map == "huntzone3") {
			$map = "Death Valley";
		} else if ($map == "huntzone4") {
			$map = "Silent Wood";
		} else if ($map == "elvfarm") {
			$map = "Elvine Farm";
		} else if ($map == "arefarm") {
			$map = "Aresden Farm";
		} else if ($map == "elvined1") {
			$map = "Elvine Dungeon";
		} else if ($map == "aresdend1") {
			$map = "Aresden Dungeon";
		} else if ($map == "bisle") {
			$map = "Bleeding Isle";
		} else if ($map == "resurr1" || $map == "resurr2") {
			$map = "Resurrection Zone";
		} else if ($map == "toh1") {
			$map = "Tower of Hell Level 1";
		} else if ($map == "toh2") {
			$map = "Tower of Hell Level 2";
		} else if ($map == "toh3") {
			$map = "Tower of Hell Level 3";
		} else if ($map == "iceland") {
			$map = "Ice Land";
		} else if ($map == "middled1x") {
			$map = "Middle Land Mine";
		} else if ($map == "middled1n") {
			$map = "Beginner Dungeon";
		} else if ($map == "2ndmiddle") {
			$map = "Promiseland";
		} else if ($map == "dglv2") {
			$map = "Dungeon Level 2";
		} else if ($map == "dglv3") {
			$map = "Dungeon Level 3";
		} else if ($map == "dglv4") {
			$map = "Dungeon Level 4";
		} else if ($map == "icebound") {
			$map = "Ice Bound";
		} else if ($map == "druncncity") {
			$map = "Druncian City";
		} else if ($map == "inferniaA") {
			$map = "Infernia A";
		} else if ($map == "inferniaB") {
			$map = "Infernia B";
		} else if ($map == "procella") {
			$map = "Procella";
		} else if ($map == "maze") {
			$map = "Maze";
		} else if ($map == "abaddon") {
			$map = "Abaddon";
		} else if ($map == "GodH") {
			$map = "Heldenien Castle";
		} else if ($map == "HRampart") {
			$map = "Heldenien Rampart";
		} else if ($map == "BtField") {
			$map = "Battle Field";
		} else if ($map == "wrhus_1") {
			$map = "Aresden West Warehouse";
		} else if ($map == "arewrhus") {
			$map = "Aresden East Warehouse";
		} else if ($map == "wrhus_1f") {
			$map = "Aresden Farm Warehouse";
		} else if ($map == "wrhus_2") {
			$map = "Elvine North Warehouse";
		} else if ($map == "elvwrhus") {
			$map = "Elvine South Warehouse";
		} else if ($map == "wrhus_2f") {
			$map = "Elvine Farm Warehouse";
		} else if ($map == "bsmith_1") {
			$map = "Aresden Blacksmith";
		} else if ($map == "bsmith_1f") {
			$map = "Aresden Farm Blacksmith";
		} else if ($map == "bsmith_2") {
			$map = "Elvine Blacksmith";
		} else if ($map == "bsmith_2f") {
			$map = "Elvine Farm Blacksmith";
		} else if ($map == "gshop_1") {
			$map = "Aresden Shop";
		} else if ($map == "gshop_1f") {
			$map = "Aresden Farm Shop";
		} else if ($map == "gshop_2") {
			$map = "Elvine Shop";
		} else if ($map == "gshop_2f") {
			$map = "Elvine Farm Shop";
		} else if ($map == "wzdtwr_1") {
			$map = "Aresden Wizard Tower";
		} else if ($map == "wzdtwr_2") {
			$map = "Elvine Wizard Tower";
		} else if ($map == "cmdhall_1") {
			$map = "Aresden Command Hall";
		} else if ($map == "cmdhall_2") {
			$map = "Elvine Command Hall";
		} else if ($map == "cityhall_1") {
			$map = "Aresden City Hall";
		} else if ($map == "cityhall_2") {
			$map = "Elvine City Hall";
		} else if ($map == "cath_1") {
			$map = "Aresden Church";
		} else if ($map == "cath_2") {
			$map = "Elvine Church";
		}
		
		return $map;
	}
	
	function getNation($nation) {
		$nation = trim($nation);
		if ($nation == "NONE") {
			$town = "Traveller";
		} else if ($nation == "elvine") {
			$town = "Elvine Combatant";
		} else if ($nation == "elvhunter") {
			$town= "Elvine Civilian";
		} else if ($nation == "aresden") {
			$town = "Aresden Combatant";
		} else if ($nation == "arehunter") {
			$town = "Aresden Civilian";
		} else {
			$town = "Mystery Town";
		}
		
		return $town;
	}
	
	function getSkillName($skillid) {
		switch ($skillid) {
			case 0:
				$skill = "Mining";
				break;
			case 1:
				$skill = "Fishing";
				break;
			case 2:
				$skill = "Farming";
				break;
			case 3:
				$skill = "Magic Resistance";
				break;
			case 4:
				$skill = "Magic";
				break;
			case 5:
				$skill = "Hand Attack";
				break;
			case 6:
				$skill = "Archery";
				break;
			case 7:
				$skill = "Short Sword";
				break;
			case 8:
				$skill = "Long Sword";
				break;
			case 9:
				$skill = "Fencing";
				break;
			case 10:
				$skill = "Axe Attack";
				break;
			case 11:
				$skill = "Shield";
				break;
			case 12:
				$skill = "Alchemy";
				break;
			case 13:
				$skill = "Manufacturing";
				break;
			case 14:
				$skill = "Hammer";
				break;
			case 19:
				$skill = "Pretend Corpse";
				break;
			case 21:
				$skill = "Staff Attack";
				break;
			case 23:
				$skill = "Poison Resistence";
				break;
			default:
				$skill = "?";
				break;
		}
		
		return $skill;
	}
	
	function startsWith($haystack, $needle) {
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}
	
	function getSpells($magicmastery) {
		$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
		
		$spellsarray = split("\n", file_get_contents("$includepath/MAGICCFG.TXT"));
		
		$spellarray = array();
		
		foreach ($spellsarray as $spells) {			
			$spellinfo = preg_split('/\s+/', $spells);
			
			if ($spellinfo[0] == "magic") {			
				$index = $spellinfo[2];
				
				if ($magicmastery[$index] == 1) {
					$hasspell = true;
				} else {
					$hasspell = false;
				}
		
				if ($spellinfo[2] < 10) {
					$page = 1;
				} else if ($spellinfo[2] < 20) {
					$page = 2;
				} else if ($spellinfo[2] < 30) {
					$page = 3;
				} else if ($spellinfo[2] < 40) {
					$page = 4;
				} else if ($spellinfo[2] < 50) {
					$page = 5;
				} else if ($spellinfo[2] < 60) {
					$page = 6;
				} else if ($spellinfo[2] < 70) {
					$page = 7;
				} else if ($spellinfo[2] < 80) {
					$page = 8;
				} else if ($spellinfo[2] < 90) {
					$page = 9;
				} else if ($spellinfo[2] < 100) {
					$page = 10;
				}
				
				if ($hasspell) {	
					$spellarray[] =  array('num' => "$spellinfo[2]", 'name' => "$spellinfo[3]", 'mpcost' => "$spellinfo[4]", 'intreq' => "$spellinfo[5]", 'page' => "$page", 'inwiz' => "$spellinfo[10]" );
				}
			}		
		}
		
		return $spellarray;
	}
	
	function getGuild($guild, $guildrank) {
		if ($guildrank == 0) {
			$guildrank = "Guildmaster";
		} else {
			$guildrank = "Guildmember";
		}
		$guild = trim($guild);
		if ($guild == "NONE") {
			$guild = "Guildless";
		} else {
			$guild = str_replace("_", " ", $guild);
			$guild .= " $guildrank";
		}
		
		return $guild;
	}

	function EncodeURL($url) {
		$new = strtolower(ereg_replace(' ','_',$url));
		return($new);
	}

	function DecodeURL($url) {
		$new = ucwords(ereg_replace('_',' ',$url));
		return($new);
	}

	function ChopStr($str, $len) {
		if (strlen($str) < $len)
			return $str;

		$str = substr($str,0,$len);
		if ($spc_pos = strrpos($str," "))
				$str = substr($str,0,$spc_pos);

		return $str . "...";
	}	

	function isEmail($email) {
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return true;
		} else {
			return false;
		}
	}
	 
	function isURL($url) {
		if (preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $url)) {
			return true;
		} else {
			return false;
		}
	}
	
	function PwdHash($pwd, $salt = null) {
		if ($salt === null)     {
			$salt = substr(md5(uniqid(rand(), true)), 0, SALT_LENGTH);
		}
		else     {
			$salt = substr($salt, 0, SALT_LENGTH);
		}
		return $salt . sha1($pwd . $salt);
	}

	function checkAdmin() {
		if($_SESSION['user_level'] == ADMIN_LEVEL) {
			return true;
		} else { 
			return false;
		}
	}
	
	function checkMod() {
		if($_SESSION['user_level'] == MODERATOR_LEVEL) {
			return true;
		} else { 
			return false;
		}
	}
	
	function checkPwd($x,$y) {
		if(empty($x) || empty($y) ) { return false; }
		if (strlen($x) < 6 || strlen($y) < 6) { return false; }
		if (strcmp($x,$y) != 0) { return false; } 		
		return true;
	}
	
	function GenPwd($length = 7) {
		$password = "";
		$possible = "0123456789bcdfghjkmnpqrstvwxyz"; //no vowels

		$i = 0; 

		while ($i < $length) { 
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
			
			if (!strstr($password, $char)) { 
				$password .= $char;
				$i++;
			}
		}
		return $password;
	}
	
	function GenKey($length = 7) {
		$password = "";
		$possible = "0123456789abcdefghijkmnopqrstuvwxyz"; 

		$i = 0; 

		while ($i < $length) { 
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
			
			if (!strstr($password, $char)) { 
				$password .= $char;
				$i++;
			}
		}

		return $password;
	}
	
	function logout() {
		global $db;
		session_save_path($_SERVER['DOCUMENT_ROOT'] . "/portal/sessions");
		ini_set('session.gc_probability', 1);
		session_start();

		if(isset($_SESSION['user_id']) || isset($_COOKIE['user_id'])) {
			QueryWS1("UPDATE PlayerPortalLogin SET ckey = '', ctime = '' WHERE PPLoginID = '$_SESSION[user_id]' OR PPLoginID = '$_COOKIE[user_id]'") or die(mssql_get_last_message());
		}			

		unset($_SESSION['user_id']);
		unset($_SESSION['user_email']);
		unset($_SESSION['user_level']);
		unset($_SESSION['HTTP_USER_AGENT']);
		session_unset();
		session_destroy(); 

		/* Delete the cookies*******************/
		setcookie("user_id", '', time()-60*60*24*COOKIE_TIME_OUT, "/");
		setcookie("user_email", '', time()-60*60*24*COOKIE_TIME_OUT, "/");
		setcookie("user_key", '', time()-60*60*24*COOKIE_TIME_OUT, "/");

		header("Location: loginregister.php");
	}
	
	function page_protect() {
		session_save_path($_SERVER['DOCUMENT_ROOT'] . "/portal/sessions");
		ini_set('session.gc_probability', 1);
		session_start();

		global $db; 

		/* Secure against Session Hijacking by checking user agent */
		if (isset($_SESSION['HTTP_USER_AGENT']))
		{
			if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT']))
			{
				logout();
				exit;
			}
		}

		// before we allow sessions, we need to check authentication key - ckey and ctime stored in database

		/* If session not set, check for cookies set by Remember me */
		if (!isset($_SESSION['user_id']) && !isset($_SESSION['user_email']) ) {
			if(isset($_COOKIE['user_id']) && isset($_COOKIE['user_key'])) {
				/* we double check cookie expiry time against stored in database */
				$cookie_user_id  = filter($_COOKIE['user_id']);
				$rs_ctime = QueryWS1("SELECT ckey, ctime, Approved FROM PlayerPortalLogin WHERE PPLoginID = '$cookie_user_id'") or die(mssql_get_last_message());
				list($ckey,$ctime,$approved) = mssql_fetch_row($rs_ctime);
				// coookie expiry
				if( (time() - $ctime) > 60*60*24*COOKIE_TIME_OUT ) {
					logout();
				}
				if ( $approved != 1 ) {
					logout();
				}
				/* Security check with untrusted cookies - dont trust value stored in cookie. 		
				/* We also do authentication check of the `ckey` stored in cookie matches that stored in database during login*/
				if( !empty($ckey) && is_numeric($_COOKIE['user_id']) && isEmail($_COOKIE['user_email']) /* && $_COOKIE['user_key'] == sha1($ckey) */  ) {
					session_regenerate_id(); //against session fixation attacks.
			
					$_SESSION['user_id'] = $_COOKIE['user_id'];
					$_SESSION['user_email'] = $_COOKIE['user_email'];
					/* query user level from database instead of storing in cookies */	
					list($user_level, $displayname) = mssql_fetch_row(QueryWS1("SELECT UserLevel, DisplayAlias FROM PlayerPortalLogin WHERE PPLoginID = '$_SESSION[user_id]'"));

					$_SESSION['user_level'] = $user_level;
					$_SESSION['display_name'] = $displayname;
					$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
				  
				} else {
					logout();
				}

			} else {
				header("Location: loginregister.php");
				exit();
			}
		}
	}
	
	function user_protect() {
		session_save_path($_SERVER['DOCUMENT_ROOT'] . "/portal/sessions");
		ini_set('session.gc_probability', 1);
		session_start();

		global $db; 

		/* Secure against Session Hijacking by checking user agent */
		if (isset($_SESSION['HTTP_USER_AGENT']))
		{
			if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT']))
			{
				logout();
				exit;
			}
		}

		// before we allow sessions, we need to check authentication key - ckey and ctime stored in database

		/* If session not set, check for cookies set by Remember me */
		if (!isset($_SESSION['user_id']) && !isset($_SESSION['user_email']) ) {
			if(isset($_COOKIE['user_id']) && isset($_COOKIE['user_key'])) {
				/* we double check cookie expiry time against stored in database */			
				$cookie_user_id  = filter($_COOKIE['user_id']);
				$rs_ctime = QueryWS1("SELECT ckey, ctime, Approved FROM PlayerPortalLogin WHERE PPLoginID = '$cookie_user_id'") or die(mssql_get_last_message());
				list($ckey,$ctime,$approved) = mssql_fetch_row($rs_ctime);
				
				// coookie expiry
				if( (time() - $ctime) > 60*60*24*COOKIE_TIME_OUT ) {
					logout();
				}
				if ( $approved != 1 ) {
					logout();
				}
				/* Security check with untrusted cookies - dont trust value stored in cookie. 		
				/* We also do authentication check of the `ckey` stored in cookie matches that stored in database during login*/

				if( !empty($ckey) && is_numeric($_COOKIE['user_id']) && isEmail($_COOKIE['user_email']) /* && $_COOKIE['user_key'] == sha1($ckey) */  ) {
					session_regenerate_id(); //against session fixation attacks.
				
					$_SESSION['user_id'] = $_COOKIE['user_id'];
					$_SESSION['user_email'] = $_COOKIE['user_email'];
					/* query user level from database instead of storing in cookies */	
					list($user_level, $displayname) = mssql_fetch_row(QueryWS1("SELECT UserLevel, DisplayAlias FROM PlayerPortalLogin WHERE PPLoginID = '$_SESSION[user_id]'"));

					$_SESSION['user_level'] = $user_level;
					$_SESSION['display_name'] = $displayname;
					$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
					  
				} else {
					logout();
				}
			} else {
			
			}
		}
	}
?>