<?
	$includepath = $_SERVER['DOCUMENT_ROOT'] . "/portal/includes";
	
	$itemnamesarray = split("\n", file_get_contents("$includepath/ItemName.cfg"));
	
	$itemnamearray = array();
	
	foreach ($itemnamesarray as $items) {
		$iteminfo = explode("=", $items);

		if ($iteminfo[0] == "Item") {
			$itemnamearray[$iteminfo[1]] = "$iteminfo[2]";
		}
	}
		
	$itemsarray1 = split("\n", file_get_contents("$includepath/Item.cfg"));
	$itemsarray2 = split("\n", file_get_contents("$includepath/Item2.cfg"));
	$itemsarray3 = split("\n", file_get_contents("$includepath/Item3.cfg"));
	$itemsarray4 = split("\n", file_get_contents("$includepath/Item4.cfg"));
	
	$itemarray = array();
	$itemposarray = array();
	
	foreach ($itemsarray1 as $items1) {
		$iteminfo1 = preg_split('/\s+/', $items1);

		if ($iteminfo1[0] == "Item") {
			$itemarray[$iteminfo1[2]] = "$iteminfo1[3]";
			$itemposarray[$iteminfo1[2]] = "$iteminfo1[5]";
		}
	}
	foreach ($itemsarray2 as $items2) {
		$iteminfo2 = preg_split('/\s+/', $items2);

		if ($iteminfo2[0] == "Item") {
			$itemarray[$iteminfo2[2]] = "$iteminfo2[3]";
			$itemposarray[$iteminfo2[2]] = "$iteminfo2[5]";
		}
	}
	foreach ($itemsarray3 as $items3) {
		$iteminfo3 = preg_split('/\s+/', $items3);

		if ($iteminfo3[0] == "Item") {
			$itemarray[$iteminfo3[2]] = "$iteminfo3[3]";
			$itemposarray[$iteminfo3[2]] = "$iteminfo3[5]";
		}
	}
	foreach ($itemsarray4 as $items4) {
		$iteminfo4 = preg_split('/\s+/', $items4);

		if ($iteminfo4[0] == "Item") {
			$itemarray[$iteminfo4[2]] = "$iteminfo4[3]";
			$itemposarray[$iteminfo4[2]] = "$iteminfo4[5]";
		}
	}
?>