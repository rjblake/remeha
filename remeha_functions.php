<?php
// Uncomment to report Errors for Debug purposes
// error_reporting(E_ALL);

// Function to lookup Domoticz Values from an array
//
function array_lookup($parsed_json, $DOMOIDX, $DOMOType)
{
	$parsed_json_result = $parsed_json['result'];
	$key = array_search($DOMOIDX, array_column($parsed_json_result, 'idx'));
	$DOMO_array = $parsed_json['result'][$key];
	// echo "DOMOType: $DOMOType :";
	$array_val = $DOMO_array[$DOMOType];
	return $array_val;
}

// Function to update Domoticz Switches using cURL
//
function swdevice($idx, $nvalue, $svalue, $DOMOIPAddress, $DOMOPort, $Username, $Password, $DOMOUpdate)
{
	if ($DOMOUpdate == "1")
	{	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, "http://$Username:$Password@$DOMOIPAddress:$DOMOPort/json.htm?type=command&param=switchlight&idx=$idx&nvalue=$nvalue&switchcmd=$svalue");
		curl_exec($ch);
		curl_close($ch);
		// Sleep for 1/4 sec - in case system stressed/slow
		// usleep(250000);

	}
	else
	{
		$newline ="<br />";
		echo "Debug Info: IDX:$idx Value:$svalue - http://$Username:$Password@$DOMOIPAddress:$DOMOPort/json.htm?type=command&ampparam=switchlight&idx=$idx&nvalue=$nvalue&switchcmd=$svalue$newline";
	}
}

// Function to update Domoticz using cURL
//
function udevice($idx, $nvalue, $svalue, $DOMOIPAddress, $DOMOPort, $Username, $Password, $DOMOUpdate)
{
	if ($DOMOUpdate == "1")
	{	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, "http://$Username:$Password@$DOMOIPAddress:$DOMOPort/json.htm?type=command&param=udevice&idx=$idx&nvalue=$nvalue&svalue=$svalue");	
		curl_exec($ch);
		curl_close($ch);
		// Sleep for 1/4 sec - in case system stressed/slow
		// usleep(250000);

	}
	else
	{
		$newline ="<br />";
		echo "Debug Info: IDX:$idx Value:$svalue - http://$Username:$Password@$DOMOIPAddress:$DOMOPort/json.htm?type=command&ampparam=udevice&idx=$idx&nvalue=$nvalue&svalue=$svalue$newline";
	}
}

// Function to work out BIT flags
// 
function nbit($number, $n) 
{
	return ($number >> $n) & 1;	#BITS Numbered 0-7
}

// Function to debug BITs
//
function showbits($val, $byte)
{
// Show workings of BITS
	$valDEC = hexdec($val);
	$valBIN = base_convert($val, 16, 2);
	$valBIN = sprintf( "%08d", $valBIN);

	$valBITx0 = ($valDEC & (1<<0));
	$valBITx1 = ($valDEC & (1<<1));
	$valBITx2 = ($valDEC & (1<<2));
	$valBITx3 = ($valDEC & (1<<3));
	$valBITx4 = ($valDEC & (1<<4));
	$valBITx5 = ($valDEC & (1<<5));
	$valBITx6 = ($valDEC & (1<<6));
	$valBITx7 = ($valDEC & (1<<7));
	$nbitX0 = nbit($valDEC, 0);
	$nbitX1 = nbit($valDEC, 1);
	$nbitX2 = nbit($valDEC, 2);
	$nbitX3 = nbit($valDEC, 3);
	$nbitX4 = nbit($valDEC, 4);
	$nbitX5 = nbit($valDEC, 5);
	$nbitX6 = nbit($valDEC, 6);
	$nbitX7 = nbit($valDEC, 7);

	echo str_repeat("=", 40) . "$newline";
	echo "Content & Value of BITS: $byte$newline";
	echo "Value (HEX, Dec, Bin): $val, $valDEC, $valBIN$newline";
	echo str_repeat("=", 40) . "$newline";
	echo "nBit0: 	$nbitX0" .' '."| Bit0: 	$valBITx0$newline";
	echo "nBit1: 	$nbitX1" .' '."| Bit1: 	$valBITx1$newline";
	echo "nBit2: 	$nbitX2" .' '."| Bit2: 	$valBITx2$newline";
	echo "nBit3: 	$nbitX3" .' '."| Bit3: 	$valBITx3$newline";
	echo "nBit4: 	$nbitX4" .' '."| Bit4: 	$valBITx4$newline";
	echo "nBit5: 	$nbitX5" .' '."| Bit5: 	$valBITx5$newline";
	echo "nBit6: 	$nbitX6" .' '."| Bit6: 	$valBITx6$newline";
	echo "nBit7: 	$nbitX7" .' '."| Bit7: 	$valBITx7$newline";
	echo str_repeat("=", 40) . "$newline";
// END workings of BITS
}

// ...and the CRC16 Modbus Check to check data integrity
//
function crc16_modbus($msg)
{
	$data = pack('H*',$msg);
	$crc = 0xFFFF;
	for ($i = 0; $i < strlen($data); $i++)
	{
		$crc ^=ord($data[$i]);
		for ($j = 8; $j !=0; $j--)
		{
			if (($crc & 0x0001) !=0)
			{
				$crc >>= 1;
				$crc ^= 0xA001;
			}
			else $crc >>= 1;
			}
		}

	$crc_semi_inverted = sprintf('%04x', $crc);
	$crc_modbus = substr($crc_semi_inverted, 2, 2).substr($crc_semi_inverted, 0, 2);
	$crc_modbus = hexdec($crc_modbus);
	return sprintf('%04x', $crc_modbus);
}

// Function to convert HEX to ASCII for Device ID, Serial, etc.
// usage (e.g. $hexstr = "43616c656e7461202020202020202020";) whic is "Calenta"
function hex2str($hex)
{
	$str = "";
	for($i=0;$i<strlen($hex);$i+=2) $str .= chr(hexdec(substr($hex, $i, 2)));
	return $str;
}

// Function to show statements or not based on a flag
// If variable $echo_flag = 1, show the output of "echo", otherwise hide it
function conditional_echo($string,$echo_flag)
{
	if ($echo_flag == 1)
	{
		echo $string;
	}
}

// Function to connect to ESP8266
//
function connect_to_esp($ESPIPAddress, $ESPPort, $retries, $newline)
{
	$connected = false;
	$retry = 0;
	
	// Keep looping until connected or met no. of retries if $retries is not zero
	while (!$connected && ($retries==0 || ($retries>0 && $retry<$retries)))
	{
		// try connecting to the ESP8266
		$fp = fsockopen($ESPIPAddress, $ESPPort, $errno, $errstr, 5);
		
		if ($fp)
		{
			return $fp;
			// connection was successful
		}
		else
		{
			echo "Unable to establish connection to ".$ESPIPAddress.":".$ESPPort." - Error:$errno:".$errstr."$newline";
			echo "Trying to reset ESP8266 @ $ESPIPAddress $newline";
			file_get_contents("http://$ESPIPAddress/log/reset");
		}
		sleep(10); // sleep for 10 seconds before trying again
		$retry++;
	}
	return $fp;
}

// Function to convert 'Signed HEX Values' to Decimal
//
function hexdecs($hex)
{
    $hex = preg_replace('/[^0-9A-Fa-f]/', '', $hex);
    $dec = hexdec($hex);
    $max = pow(2, 4 * (strlen($hex) + (strlen($hex) % 2)));
    $_dec = $max - $dec;
    return $dec > $_dec ? -$_dec : $dec;
}

function cls()
{
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
	{
    		echo '\r\n';
	} 
	elseif (strtoupper(substr(PHP_OS, 0, 3)) === 'LIN')
	{
		array_map(create_function('$a', 'print chr($a);'), array(27, 91, 72, 27, 91, 50, 74));
	}
	elseif (strtoupper(substr(PHP_OS, 0, 3)) === 'DAR')
	{
		array_map(create_function('$a', 'print chr($a);'), array(27, 91, 72, 27, 91, 50, 74));
	}

}
?>
