<?php
	function getItemStats($iAttribute, $intType, $lngE2, $lngCount) {
		$pstrMainStat = "";
		$pstrItemName = "";
		$pstrMainStatPercent = "";
		$pstrSubStat = "";
		$pstrSubStatPercent = "";
		$class = "";
		
		$strTemp1 = fmt_binary($iAttribute, 32);
		// $strTemp2 = fmt_binary("&H1", 32);
    
		// If (substr($strTemp1, 28, 4) == substr($strTemp2, 28, 4)) {
			// If ($intType = 12) {
				// $pstrMainStat = "Purity: " . $lngE2 . "%";
			// } Else {
				// If ($lngEquipPos = 11) {
					// $pstrMainStat = "Completion: " . $lngE2 . "%";
				// } Else {
					// $pstrMainStat = "Completion: " . $lngE2 + 100 . "%";
				// }
			// }
		// } Else {
			// If ($lngCount > 1) {
				// $pstrMainStat = "" . "&HF0000000";
			// }
		// }
		
		$mainstat = ConvertToLong(substr($strTemp1, 8, 4));
		switch ($mainstat) {
			Case 1:  
				$pstrMainStat = "Critical ";
				$pstrMainStatPercent = "+" . ConvertToLong(substr($strTemp1, 12, 4));
				$class .= "special" . " critical";
				break;
			Case 2:  
				$pstrMainStat = "Poisoning ";
				$pstrMainStatPercent = "+" . ((ConvertToLong(substr($strTemp1, 12, 4))) * 5);
				$class .= "special" . " poison";
				break;
			Case 3:  
				$pstrMainStat = "Righteous ";
				$pstrMainStatPercent = "";
				$class .= "special" . " righteous";
				break;
			Case 4:  
				$pstrMainStat = "";
				break;
			Case 5:  
				$pstrMainStat = "Agile ";
				$pstrMainStatPercent = "";
				$class .= "special" . " agile";
				break;
			Case 6:  
				$pstrMainStat = "Light ";
				$pstrMainStatPercent = "-" . ((ConvertToLong(substr($strTemp1, 12, 4))) * 4) . "%";
				$class .= "special" . " light";
				break;
			Case 7:  
				$pstrMainStat = "Sharp ";
				$pstrMainStatPercent = "";
				$class .= "special" . " sharp";
				break;
			Case 8:  
				$pstrMainStat = "Strong ";
				$pstrMainStatPercent = "+" . ((ConvertToLong(substr($strTemp1, 12, 4))) * 7) . "%";
				$class .= "special" . " strong";
				break;
			Case 9:  
				$pstrMainStat = "Ancient ";
				$pstrMainStatPercent = "";
				$class .= "special" . " ancient";
				break;
			Case 10:  
				$pstrMainStat = "Casting Probability ";
				$pstrMainStatPercent = "+" . ((ConvertToLong(substr($strTemp1, 12, 4))) * 3) . "%";
				$class .= "special" . " castingprob";
				break;
			Case 11:  
				$pstrMainStat = "Mana Converting ";
				$pstrMainStatPercent = "+" . ConvertToLong(substr($strTemp1, 12, 4)) . "%";
				$class .= "special" . " manaconvert";
				break;
			Case 12:  
				$pstrMainStat = "Crit Increase ";
				$pstrMainStatPercent = "+" . ConvertToLong(substr($strTemp1, 12, 4)) . "%";
				$class .= "special" . " critinc";
				break;
		}
		
		$substat = ConvertToLong(substr($strTemp1, 16, 4));
		switch ($substat) {
			Case 1:  
				$pstrSubStat = "PR";
				$pstrSubStatPercent = "+" . ((ConvertToLong(substr($strTemp1, 20, 4))) * 7) . "%";
				$class .= " poisonresist";
				break;
			Case 2:  
				$pstrSubStat = "HP";
				$pstrSubStatPercent = "+" . ((ConvertToLong(substr($strTemp1, 20, 4))) * 7);
				$class .= " hittingprob";
				break;
			Case 3:  
				$pstrSubStat = "DR";
				$pstrSubStatPercent = "+" . ((ConvertToLong(substr($strTemp1, 20, 4))) * 7);
				$class .= " defenseratio";
				break;
			Case 4:  
				$pstrSubStat = "HP Recovery";
				$pstrSubStatPercent = "+" . ((ConvertToLong(substr($strTemp1, 20, 4))) * 7) . "%";
				$class .= " hprec";
				break;
			Case 5:  
				$pstrSubStat = "SP Recovery";
				$pstrSubStatPercent = "+" . ((ConvertToLong(substr($strTemp1, 20, 4))) * 7) . "%";
				$class .= " sprec";
				break;
			Case 6:  
				$pstrSubStat = "MP Recovery";
				$pstrSubStatPercent = "+" . ((ConvertToLong(substr($strTemp1, 20, 4))) * 7) . "%";
				$class .= " mprec";
				break;
			Case 7:  
				$pstrSubStat = "MR";
				$pstrSubStatPercent = "+" . ((ConvertToLong(substr($strTemp1, 20, 4))) * 7) . "%";
				$class .= " magresist";
				break;
			Case 8:  
				$pstrSubStat = "PA";
				$pstrSubStatPercent = "+" . ((ConvertToLong(substr($strTemp1, 20, 4))) * 3) . "%";
				$class .= " physabsorb";
				break;
			Case 9:  
				$pstrSubStat = "MA";
				$pstrSubStatPercent = "+" . ((ConvertToLong(substr($strTemp1, 20, 4))) * 3) . "%";
				$class .= " magabsorb";
				break;
			Case 10:  
				$pstrSubStat = "Rep";
				$pstrSubStatPercent = "+" . ConvertToLong(substr($strTemp1, 20, 4));
				$class .= " rep";
				break;
			Case 11:  
				$pstrSubStat = "Exp";
				$pstrSubStatPercent = "+" . ((ConvertToLong(substr($strTemp1, 20, 4))) * 10) . "%";
				$class .= " exp";
				break;
			Case 12:  
				$pstrSubStat = "Gold";
				$pstrSubStatPercent = "+" . ((ConvertToLong(substr($strTemp1, 20, 4))) * 10) . "%";
				$class .= " gold";
				break;
		}
		
		$plusvalue = (ConvertToLong(substr($strTemp1, 0, 4)));
		
		if ($plusvalue != 0) {
			$plusvalue = "+" . $plusvalue;
		} else {
			$plusvalue = "";
		}
		
		return array( "mainstat" => "$pstrMainStat", "mainstatpercent" => "$pstrMainStatPercent", "substat" => "$pstrSubStat", "substatpercent" => "$pstrSubStatPercent", "class" => "$class", "plusvalue" => "$plusvalue" );
	}
	
	function ConvertToLong($binary_value) {	 
		$hex_result = "";

		// Remove any leading &B if present.
		// (Note: &B is not a standard prefix, it just
		// makes some sense.)
		$binary_value = strtoupper(trim($binary_value));
		
		if (substr($binary_value, 0, 2) == "&B") {
			$binary_value = substr($binary_value, 2, 1);
		}
	 
		// Strip out spaces in case the bytes are separated
		// by spaces.
		$binary_value = str_replace(" ", "", $binary_value);
	 
		// Left pad with zeros so we have a full 32 bits.
		$binary_value = str_pad($binary_value, 32, "0", STR_PAD_LEFT);

		// Read the bits in nibbles from right to left.
		// (A nibble is half a byte. No kidding!)
		for ($nibble_num = 7; $nibble_num >= 0; $nibble_num--) {
			// Convert this nibble into a hexadecimal string.
			$factor = 1;
			$nibble_value = 0;
	 
			//Read the nibble's bits from right to left.
			for ($bit = 3; $bit >= 0; $bit--) {
				$start = $nibble_num * 4 + $bit;

				if (substr($binary_value, $start, 1) == "1") {
					$nibble_value = $nibble_value + $factor;
				}
				$factor = $factor * 2;
			}
	 
			// Add the nibble's value to the left of the
			// result hex string.
			$hex_result = dechex($nibble_value) . $hex_result;	
		}

		// Convert the result string into a long.
		
		return hexdec("&H" . $hex_result);	
	}
	
	function ConvertToBinary($long_value) {
		$result_string = "";
		
		// Convert into hex.
		$hex_string = dechex($long_value);
		
		// Zero-pad to a full 8 characters.
		$hex_string = str_pad($hex_string, 8, "0", STR_PAD_LEFT);

		// Read the hexadecimal digits
		// one at a time from right to left.
		for ($digit_num = 7; $digit_num >= 0; $digit_num--) {
			// Convert this hexadecimal digit into a
			// binary nibble.
			$digit_value = hexdec("&H" . substr($hex_string, $digit_num, 1));

			// Convert the value into bits.
			$factor = 1;
			$nibble_string = "";
			
			for ($bit = 3; $bit >= 0; $bit--) {
				if ($digit_value && $factor) {
					$nibble_string = "1" . $nibble_string;
				} else {
					$nibble_string = "0" . $nibble_string;
				}
				$factor = $factor * 2;
			}
	 
			// Add the nibble's string to the left of the
			// result string.
			$result_string = $nibble_string . $result_string;
		}
	 
		// Return the result.
		return $result_string;
	}
	
	function right($value, $count){
		return substr($value, ($count*-1));
	}

	function left($string, $count){
		return substr($string, 0, $count);
	}
	
	function fmt_binary($x, $numbits = 8) {
        // Convert to binary
        $bin = decbin($x);
        $bin = substr(str_repeat(0,$numbits),0,$numbits - strlen($bin)) . $bin;
		
        // Split into x 4-bits long
        $rtnval = '';
        for ($x = 0; $x < $numbits/4; $x++) {
            $rtnval .= ' ' . substr($bin,$x*4,4);
        }
		
		// Get rid of first space.
		return str_replace(" ", "", $rtnval);
	} 
?>