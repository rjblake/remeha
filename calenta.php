<?php
// Uncomment to report Errors for Debug purposes
// error_reporting(E_ALL);

/* 
****************************************
** Codes are for a Remeha Calenta 40C **
****************************************
*/

// Define Variables that will be used
// Hex string to get data values from Calenta - 02 FE 01 05 08 02 01 69 AB 03 - does not include counters
// Change as required for specific model (Calenta, avanta, etc...)
$remeha_sample=chr(0x02).chr(0xFE).chr(0x01).chr(0x05).chr(0x08).chr(0x02).chr(0x01).chr(0x69).chr(0xAB).chr(0x03);
$remeha_counter1=chr(0x02).chr(0xFE).chr(0x00).chr(0x05).chr(0x08).chr(0x10).chr(0x1C).chr(0x98).chr(0xC2).chr(0x03);
$remeha_counter2=chr(0x02).chr(0xFE).chr(0x00).chr(0x05).chr(0x08).chr(0x10).chr(0x1D).chr(0x59).chr(0x02).chr(0x03);
$remeha_counter3=chr(0x02).chr(0xFE).chr(0x00).chr(0x05).chr(0x08).chr(0x10).chr(0x1E).chr(0x19).chr(0x03).chr(0x03);
$remeha_counter4=chr(0x02).chr(0xFE).chr(0x00).chr(0x05).chr(0x08).chr(0x10).chr(0x1F).chr(0xD8).chr(0xC3).chr(0x03);

$ini_array = parse_ini_file("remeha.ini");
// print_r($ini_array);
$ESPIPAddress = ($ini_array['ESPIPAddress']);
$ESPPort = ($ini_array['ESPPort']);	

// Connect to Remeha & get info
// Open connection to ESP connected to Calenta
$fp=fsockopen($ESPIPAddress, $ESPPort, $errno, $errstr, 5);
stream_set_timeout($fp, 5);
$sleeptime = "500000";		# Adjust as required to allow Remeha to respond 500000 = 0.5secs

if (!$fp) 
	{
	echo "ERROR opening $ESPIPAddress:$ESPPort<br />";
	echo $errno;
	echo $errstr;
	} 
else
	{	
	echo str_repeat("=", 148) . "<br />";
	echo "Connected to $ESPIPAddress:$ESPPort<br />";
	echo "Sending request...<br />";
   	fwrite($fp,$remeha_sample, 10);
	$data_sample="";	
	$data_sample=bin2hex(fread($fp, 148));
	echo "Sample Data read:$data_sample<br />";
	usleep($sleeptime);
	
	fwrite($fp,$remeha_counter1, 10);
	$data_counter1="";
	$data_counter1=bin2hex(fread($fp, 52));
	echo "Counter Data-1 read:$data_counter1<br />";	
	usleep($sleeptime);
	
	fwrite($fp,$remeha_counter2, 10);
	$data_counter2="";
	$data_counter2=bin2hex(fread($fp, 52));
	echo "Counter Data-2 read:$data_counter2<br />";
	usleep($sleeptime);
 		
	fwrite($fp,$remeha_counter3, 10);
	$data_counter3="";
	$data_counter3=bin2hex(fread($fp, 52));
	echo "Counter Data-3 read:$data_counter3<br />";
	usleep($sleeptime);
	
	fwrite($fp,$remeha_counter4, 10);
	$data_counter4="";
	$data_counter4=bin2hex(fread($fp, 52));
	echo "Counter Data-4 read:$data_counter4<br />";
	
	$output = hex_dump($data_sample, $data_counter1, $data_counter2, $data_counter3, $data_counter4);
  	fclose($fp);
	echo "<br />";
	echo "**********************<br />";	
	echo "*** Connection closed ***<br />";
	echo "**********************<br />";
	echo "$i<br />";
	} 

// END of connect to Remeha & get info

// Data conversion
//
function hex_dump($data_sample, $data_counter1, $data_counter2,  $data_counter3, $data_counter4, $newline="<br />")
{

// Manipulate data & Do a CRC Check	
	$decode = str_split($data_sample, 2);
	$hexstr = str_split($data_sample, 148);
	$hexstrPayload = substr($data_sample, 2, 140);
	$hexstrCRC = substr($data_sample, 142, 4);
	$crcCalc = crc16_modbus($hexstrPayload);	

	$decode_cnt1 = str_split($data_counter1, 2);
	$hexstr_cnt1 = str_split($data_counter1, 52);
	$hexstrPayload_cnt1 = substr($data_counter1, 2, 44);
	$hexstrCRC_cnt1 = substr($data_counter1, 46, 4);
	$crcCalc_cnt1 = crc16_modbus($hexstrPayload_cnt1);	

	$decode_cnt2 = str_split($data_counter2, 2);
	$hexstr_cnt2 = str_split($data_counter2, 52);
	$hexstrPayload_cnt2 = substr($data_counter2, 2, 44);
	$hexstrCRC_cnt2 = substr($data_counter2, 46, 4);
	$crcCalc_cnt2 = crc16_modbus($hexstrPayload_cnt2);

	$decode_cnt3 = str_split($data_counter3, 2);
	$hexstr_cnt3 = str_split($data_counter3, 52);
	$hexstrPayload_cnt3 = substr($data_counter3, 2, 44);
	$hexstrCRC_cnt3 = substr($data_counter3, 46, 4);
	$crcCalc_cnt3 = crc16_modbus($hexstrPayload_cnt3);	
	
	$decode_cnt4 = str_split($data_counter4, 2);
	$hexstr_cnt4 = str_split($data_counter4, 52);
	$hexstrPayload_cnt4 = substr($data_counter4, 2, 44);
	$hexstrCRC_cnt4 = substr($data_counter4, 46, 4);
	$crcCalc_cnt4 = crc16_modbus($hexstrPayload_cnt4);	

/*
	echo "Full data_sample response: $data_sample<br />";
	echo "Hex string data_sample Payload: $hexstrPayload<br />";
	echo "Hex string data_sample CRC Value: $hexstrCRC<br />";		
	echo "Calculated data_sample CRC Value: $crcCalc<br />";

	echo "Full data_counter1 response: $data_counter1<br />";
	echo "Hex string data_counter1 Payload: $hexstrPayload_cnt1<br />";
	echo "Hex string data_counter1 CRC Value: $hexstrCRC_cnt1<br />";	
	echo "Calculated data_counter1 CRC Value: $crcCalc_cnt1<br />";

	echo "Full data_counter2 response: $data_counter2<br />";
	echo "Hex string data_counter2 Payload: $hexstrPayload_cnt2<br />";
	echo "Hex string data_counter2 CRC Value: $hexstrCRC_cnt2<br />";	
	echo "Calculated data_counter2 CRC Value: $crcCalc_cnt2<br />";
	
	echo "Full data_counter3 response: $data_counter3<br />";
	echo "Hex string data_counter3 Payload: $hexstrPayload_cnt3<br />";
	echo "Hex string data_counter3 CRC Value: $hexstrCRC_cnt3<br />";	
	echo "Calculated data_counter3 CRC Value: $crcCalc_cnt3<br />";

	echo "Full data_counter4 response: $data_counter4<br />";
	echo "Hex string data_counter4 Payload: $hexstrPayload_cnt4<br />";
	echo "Hex string data_counter4 CRC Value: $hexstrCRC_cnt4<br />";	
	echo "Calculated data_counter4 CRC Value: $crcCalc_cnt4<br />";
*/
	// Concatenate Counter data to work with
	$concat_counter = substr($hexstrPayload_cnt1, 12, 32).substr($hexstrPayload_cnt2, 12, 32).substr($hexstrPayload_cnt3, 12, 32).substr($hexstrPayload_cnt4, 12, 32);
	$decode_counter = str_split($concat_counter, 2);		

	// Write the contents to the file
	$ini_array = parse_ini_file("remeha.ini");
	$file = ($ini_array['file']);
	date_default_timezone_set('Europe/Amsterdam');
	$date = date_create();
	$datatowrite = date_format($date, 'Y-m-d H:i:s') . ' | 02 ' . $hexstrPayload . ' ' . $hexstrCRC . ' ' .'03 | ' . ' ' . $concat_counter . "\n";
	// echo "$datatowrite<br />";
	file_put_contents($file, $datatowrite, FILE_APPEND);
	echo "Data written to log: $file<br />";

	if (($hexstrCRC == $crcCalc) && ($hexstrCRC_cnt1 == $crcCalc_cnt1) && ($hexstrCRC_cnt2 == $crcCalc_cnt2) && ($hexstrCRC_cnt3 == $crcCalc_cnt3) && ($hexstrCRC_cnt4 == $crcCalc_cnt4))
		{
		echo "Data Integrity Good - CRCs Compute OK<br />";
		echo str_repeat("=", 148) . "<br /><br />";
		}
	else
		{
		echo "<br />";
		echo "************** CRC ERROR!!!! ***********<br />";
		exit("****** CRC does not compute - Exiting *******");			#exit as CRC not correct and data likely corrupted
		}

// Uncomment to show the full Array 
/*	print_r($hexstr);
	echo "<br />"; 
	print_r($hexstr_cnt1);
	echo "<br />"; 
	print_r($hexstr_cnt2);
	echo "<br />"; 
	print_r($hexstr_cnt3);
	echo "<br />"; 
	print_r($hexstr_cnt4);
	echo "<br />"; 
*/	
// Uncomment to show array elements
/*	print_r($decode);
	echo "<br />"; 
	print_r($decode_cnt1);
	echo "<br />"; 
	print_r($decode_cnt2);
	echo "<br />"; 
	print_r($decode_cnt3);
	echo "<br />"; 
	print_r($decode_cnt4);
	echo "<br />";
	print_r($decode_counter);
	echo "<br />";
*/

// Counter Info
	$pumphours_ch_dhw = $decode_counter["0"];
	$pumphours_ch_dhw .= $decode_counter["1"];
	$threewayvalvehours = $decode_counter["2"];
	$threewayvalvehours .= $decode_counter["3"];
	$hours_ch_dhw = $decode_counter["4"];
	$hours_ch_dhw .= $decode_counter["5"];
	$hours_dhw = $decode_counter["6"];
	$hours_dhw .= $decode_counter["7"];
	$powerhours_ch_dhw = $decode_counter["8"];
	$powerhours_ch_dhw .= $decode_counter["9"];
	$pumpstarts_ch_dhw = $decode_counter["10"];
	$pumpstarts_ch_dhw .= $decode_counter["11"];
	$nr_threewayvalvecycles = $decode_counter["12"];
	$nr_threewayvalvecycles .= $decode_counter["13"];
	$burnerstarts_dhw = $decode_counter["14"];
	$burnerstarts_dhw .= $decode_counter["15"];
	$tot_burnerstarts_ch_dhw = $decode_counter["16"];
	$tot_burnerstarts_ch_dhw .= $decode_counter["17"];
	$failed_burnerstarts = $decode_counter["18"];
	$failed_burnerstarts .= $decode_counter["19"];
	$nr_flame_loss = $decode_counter["20"];
	$nr_flame_loss .= $decode_counter["21"];
// END Counter Info

// Sample Data Info	
	$flowtemperature = $decode["8"];
	$flowtemperature .= $decode["7"];   
	$returntemperature = $decode["10"];
	$returntemperature .= $decode["9"];
	$dhwintemperature = $decode["12"];
	$dhwintemperature .= $decode["11"];
	if ($dhwintemperature == 8000) {$dhwintemperature = 0.00;}
	else {$dhwintemperature == $dhwintemperature;}
	$outsidetemperature = $decode["14"];
	$outsidetemperature .= $decode["13"];
	if ($outsidetemperature == 8000) {$outsidetemperature = 0.00;}
	else {$outsidetemperature == $outsidetemperature;} 	  
	$caloriftemperature = $decode["18"]; # Documented as Byte 15, but doesn't seem to make sense
	$caloriftemperature .= $decode["17"];
	if ($caloriftemperature == 8000) {$caloriftemperature = 0.00;}
	else {$caloriftemperature == $caloriftemperature;}
	$boilerctrltemperature = $decode["20"];
	$boilerctrltemperature .= $decode["19"];
	$roomtemperature = $decode["22"];
	$roomtemperature .= $decode["21"];
	$chsetpoint = $decode["24"];
	$chsetpoint .= $decode["23"];
	$dhwsetpoint = $decode["26"];
	$dhwsetpoint .= $decode["25"];
	$thermostat = $decode["28"];
	$thermostat .= $decode["27"];
	$fanspeedsetpoint = $decode["30"];
	$fanspeedsetpoint .= $decode["29"];
	$fanspeed = $decode["32"];
	$fanspeed .= $decode["31"];
	$ionisationcurrent = "";
	$ionisationcurrent .= $decode["33"];
	$internalsetpoint = $decode["35"];
	$internalsetpoint .= $decode["34"];
	$availablepower = $decode["36"];
	$pumppower = $decode["37"];
	$requiredoutput = $decode["39"];
	$actualpower = $decode["40"];
	$modrequest = $decode["43"];
	$ionisation = $decode["44"];
	$valves = $decode["45"];
	$pump .= $decode["46"];
	$state .= $decode["47"];
	$substate = "";
	$substate .= $decode["50"];  
	$pressure = "";
	$pressure .= $decode["56"];
	$controltemperature = "";
	$controltemperature .= $decode["59"];
	$controltemperature .= $decode["58"];
	$dhwflowrate = "";
	$dhwflowrate .= $decode["61"];
	$dhwflowrate .= $decode["60"];
// END Sample Data Info		

//Convert Hex2Dec
// Counters
	$pumphours_ch_dhw = hexdec($pumphours_ch_dhw)*2;	
	$threewayvalvehours = hexdec($threewayvalvehours)*2;
	$hours_ch_dhw = hexdec($hours_ch_dhw)*2;
	$hours_dhw = hexdec($hours_dhw);
	$powerhours_ch_dhw = hexdec($powerhours_ch_dhw)*2;
	$pumpstarts_ch_dhw = hexdec($pumpstarts_ch_dhw)*8;
	$nr_threewayvalvecycles = hexdec($nr_threewayvalvecycles)*8;
	$burnerstarts_dhw = hexdec($burnerstarts_dhw)*8;
	$tot_burnerstarts_ch_dhw = hexdec($tot_burnerstarts_ch_dhw)*8;
	$failed_burnerstarts = hexdec($failed_burnerstarts);
	$nr_flame_loss = hexdec($nr_flame_loss);
 // END Counters
 
 // Sample Data
  	$flowtemperature = number_format(hexdec($flowtemperature)/100, 2);
	$returntemperature = number_format(hexdec($returntemperature)/100, 2);
	$outsidetemperature = number_format(hexdec($outsidetemperature)/100, 2);	
  	$roomtemperature = number_format(hexdec($roomtemperature)/100, 2);
  	$controltemperature = number_format(hexdec($controltemperature)/100, 2);
	$dhwintemperature = number_format(hexdec($dhwintemperature)/100, 2);
  	$caloriftemperature = number_format(hexdec($caloriftemperature)/100, 2);  
  	$boilerctrltemperature = number_format(hexdec($boilerctrltemperature)/100, 2);  		
  	$thermostat = number_format(hexdec($thermostat)/100, 2);
  	$chsetpoint = number_format(hexdec($chsetpoint)/100, 2);
  	$dhwsetpoint = number_format(hexdec($dhwsetpoint)/100, 2);
  	$internalsetpoint = number_format(hexdec($internalsetpoint)/100, 2);
  	$fanspeed = hexdec($fanspeed);
  	$fanspeedsetpoint = hexdec($fanspeedsetpoint);
  	$ionisationcurrent = number_format(hexdec($ionisationcurrent)/10, 1);
  	$pumppower = hexdec($pumppower);
  	$pressure = number_format(hexdec($pressure)/10, 1);
  	$dhwflowrate = number_format(hexdec($dhwflowrate)/100, 2);
  	$actualpower = hexdec($actualpower);
  	$requiredoutput = hexdec($requiredoutput);
  	$availablepower = hexdec($availablepower);
// END Sample Data  	
// END Convert Hex2Dec  	
  	
// Translate 'bits' to useful stuff
	// Modulating Controller Connected
	$modrequestBIT0 = hexdec($modrequest);
	if (nbit($modrequestBIT0,0) == 0) {$modrequestTXT0 = "No";}
	elseif (nbit($modrequestBIT0,0) == 1) {$modrequestTXT0 = "Yes";}
	else {$modrequestTXT0 = "UNKNOWN";}
	$modrequest0 = "$modrequestBIT0:$modrequestTXT0";
	
	// Heat demand from mod. controller
	$modrequestBIT1 = hexdec($modrequest);
	if (nbit($modrequestBIT1,1) == 0) {$modrequestTXT1 = "No";}
	elseif (nbit($modrequestBIT1,1) == 1) {$modrequestTXT1 = "Yes";}
	else {$modrequestTXT1 = "UNKNOWN";}
	$modrequest1 = "$modrequestBIT1:$modrequestTXT1";

	// Heat demand from on/off controller
	$modrequestBIT2 = hexdec($modrequest);
	if (nbit($modrequestBIT2,2) == 0) {$modrequestTXT2 = "No";}
	elseif (nbit($modrequestBIT2,2) == 1) {$modrequestTXT2 = "Yes";}
	else {$modrequestTXT2 = "UNKNOWN";}
	$modrequest2 = "$modrequestBIT2:$modrequestTXT2";

	// Frost protection
	$modrequestBIT3 = hexdec($modrequest);
	if (nbit($modrequestBIT3,3) == 0) {$modrequestTXT3 = "No";}
	elseif (nbit($modrequestBIT3,3) == 1) {$modrequestTXT3 = "Yes";}
	else {$modrequestTXT3 = "UNKNOWN";}
	$modrequest3 = "$modrequestBIT3:$modrequestTXT3";

	// DHW Eco - XML seems to say otherwise...
	$modrequestBIT4 = hexdec($modrequest);
	if (nbit($modrequestBIT4,4) == 0) {$modrequestTXT4 = "Yes";}
	elseif (nbit($modrequestBIT4,4) == 1) {$modrequestTXT4 = "No";}
	else {$modrequestTXT4 = "UNKNOWN";}
	$modrequest4 = "$modrequestBIT4:$modrequestTXT4";

	// DHW Blocking
	$modrequestBIT5 = hexdec($modrequest);
	if (nbit($modrequestBIT5,5) == 0) {$modrequestTXT5 = "No";}
	elseif (nbit($modrequestBIT5,5) == 1) {$modrequestTXT5 = "Yes";}
	else {$modrequestTXT5 = "UNKNOWN";}
	$modrequest5 = "$modrequestBIT5:$modrequestTXT5";

	// Anti-Legionella
	$modrequestBIT6 = hexdec($modrequest);
	if (nbit($modrequestBIT6,6) == 0) {$modrequestTXT6 = "No";}
	elseif (nbit($modrequestBIT6,6) == 1) {$modrequestTXT6 = "Yes";}
	else {$modrequestTXT6 = "UNKNOWN";}
	$modrequest6 = "$modrequestBIT6:$modrequestTXT6";

	// DHW heat demand	
	$modrequestBIT7 = hexdec($modrequest);
	if (nbit($modrequestBIT7,7) == 0) {$modrequestTXT7 = "No";}
	elseif (nbit($modrequestBIT7,7) == 1) {$modrequestTXT7 = "Yes";}
	else {$modrequestTXT7 = "UNKNOWN";}
	$modrequest7 = "$modrequestBIT7:$modrequestTXT7";

	// Shutdown Input
	$ionisationBIT0 = hexdec($ionisation);
	if (nbit($ionisationBIT0,0) == 0) {$ionisationTXT2 = "Closed";}
	elseif (nbit($ionisationBIT0,0) == 1) {$ionisationTXT0 = "Open";}
	else {$ionisationTXT0 = "UNKNOWN";}
	$ionisation0 = "$ionisationBIT0:$ionisationTXT0";

	// Release Input
	$ionisationBIT1 = hexdec($ionisation);
	if (nbit($ionisationBIT1,1) == 0) {$ionisationTXT1 = "Closed";}
	elseif (nbit($ionisationBIT1,1) == 1) {$ionisationTXT1 = "Open";}
	else {$ionisationTXT1 = "UNKNOWN";}
	$ionisation1 = "$ionisationBIT1:$ionisationTXT1";

	// Ionisation
	$ionisationBIT2 = hexdec($ionisation);
	if (nbit($ionisationBIT2,2) == 0) {$ionisationTXT2 = "No";}
	elseif (nbit($ionisationBIT2,2) == 1) {$ionisationTXT2 = "Yes";}
	else {$ionisationTXT2 = "UNKNOWN";}
	$ionisation2 = "$ionisationBIT2:$ionisationTXT2";

	// Flow Switch for detecting DHW
	$ionisationBIT3 = hexdec($ionisation);
	if (nbit($ionisationBIT3,3) == 0) {$ionisationTXT3 = "Open";}
	elseif (nbit($ionisationBIT3,3) == 1) {$ionisationTXT3 = "Closed";}
	else {$ionisationTXT3 = "UNKNOWN";}
	$ionisation3 = "$ionisationBIT3:$ionisationTXT3";

	// Min. Gas Pressure
	$ionisationBIT5 = hexdec($ionisation);
	if (nbit($ionisationBIT5,5) == 0) {$ionisationTXT5 = "Open";}
	elseif (nbit($ionisationBIT5,5) == 1) {$ionisationTXT5 = "Closed";}
	else {$ionisationTXT5 = "UNKNOWN";}
	$ionisation5 = "$ionisationBIT5:$ionisationTXT5";

	// CH Enable
	$ionisationBIT6 = hexdec($ionisation);
	if (nbit($ionisationBIT6,6) == 0) {$ionisationTXT6 = "No";}
	elseif (nbit($ionisationBIT6,6) == 1) {$ionisationTXT6 = "Yes";}
	else {$ionisationTXT6 = "UNKNOWN";}
	$ionisation6 = "$ionisationBIT6:$ionisationTXT6";

	// DHW Enable
	$ionisationBIT7 = hexdec($ionisation);
	if (nbit($ionisationBIT7,7) == 0) {$ionisationTXT2 = "No";}
	elseif (nbit($ionisationBIT7,7) == 1) {$ionisationTXT7 = "Yes";}
	else {$ionisationTXT7 = "UNKNOWN";}
	$ionisation7 = "$ionisationBIT7:$ionisationTXT7";

	// Gas valve - XML seems to say otherwise... 
	$gasvalveBIT0 = hexdec($valves);
	if (nbit($gasvalveBIT0,0) == 0) {$gasvalveTXT0 = "Open";}
	elseif (nbit($gasvalveBIT0,0) == 1) {$gasvalveTXT0 = "Closed";}
	else {$gasvalveTXT0 = "UNKNOWN";}
	$gasvalve0 = "$gasvalveBIT0:$gasvalveTXT0";

	// Ignition
	$ignitionBIT2 = hexdec($valves);
	if (nbit($ignitionBIT2,2) == 0) {$ignitionTXT2 = "Off";}
	elseif (nbit($ignitionBIT2,2) == 1) {$ignitionTXT2 = "On";}
	else {$ignitionTXT2 = "UNKNOWN";}
	$ignition2 = "$ignitionBIT2:$ignitionTXT2";

	// 3-way valve
	$threewayvalveBIT3 = hexdec($valves);
	if (nbit($threewayvalveBIT3,3) == 0) {$threewayvalveTXT3 = "CH";}
	elseif (nbit($threewayvalveBIT3,3) == 1) {$threewayvalveTXT3 = "DHW";}
	else {$threewayvalveTXT3 = "UNKNOWN";}
	$threewayvalve3 = "$threewayvalveBIT3:$threewayvalveTXT3";

	// External 3-way valve
	$threewayvalveBIT4 = hexdec($valves);
	if (nbit($threewayvalveBIT4,4) == 0) {$threewayvalveTXT4 = "Open";}
	elseif (nbit($threewayvalveBIT4,4) == 1) {$threewayvalveTXT4 = "Closed";}
	else {$threewayvalveTXT4 = "UNKNOWN";}
	$threewayvalve4 = "$threewayvalveBIT4:$threewayvalveTXT4";

	// External Gas valve
	$gasvalveBIT6 = hexdec($valves);
	if (nbit($gasvalveBIT6,6) == 0) {$gasvalveTXT6 = "Closed";}
	elseif (nbit($gasvalveBIT6,6) == 1) {$gasvalveTXT6 = "Open";}
	else {$gasvalveTXT6 = "UNKNOWN";}
	$gasvalve6 = "$gasvalveBIT6:$gasvalveTXT6";

	// Pump
	$pumpBIT0 = hexdec($pump);
	if (nbit($pumpBIT0,0) == 0) {$pumpTXT0 = "Off";}
	elseif (nbit($pumpBIT0,0) == 1) {$pumpTXT0 = "On";}
	else {$pumpTXT0 = "UNKNOWN";}
	$pump0 = "$pumpBIT0:$pumpTXT0";

	// calorifier Pump
	$pumpBIT1 = hexdec($pump);
	if (nbit($pumpBIT1,1) == 0) {$pumpTXT1 = "Open";}
	elseif (nbit($pumpBIT1,1) == 1) {$pumpTXT1 = "Closed";}
	else {$pumpTXT1 = "UNKNOWN";}
	$pump1 = "$pumpBIT1:$pumpTXT1";

	// External CH Pump
	$pumpBIT2 = hexdec($pump);
	if (nbit($pumpBIT2,2) == 0) {$pumpTXT2 = "Off";}
	elseif (nbit($pumpBIT2,2) == 1) {$pumpTXT2 = "On";}
	else {$pumpTXT2 = "UNKNOWN";}
	$pump2 = "$pumpBIT2:$pumpTXT2";

	// Status report
	$pumpBIT4 = hexdec($pump);
	if (nbit($pumpBIT4,4) == 0) {$pumpTXT4 = "Open";}
	elseif (nbit($pumpBIT4,4) == 1) {$pumpTXT4 = "Closed";}
	else {$pumpTXT4 = "UNKNOWN";}
	$pump4 = "$pumpBIT4:$pumpTXT4";

	// Opentherm Smart Power
	$pumpBIT7 = hexdec($pump);
	if (nbit($pumpBIT7,7) == 0) {$pumpTXT7 = "Off";}
	elseif (nbit($pumpBIT7,7) == 1) {$pumpTXT7 = "On";}
	else {$pumpTXT7 = "UNKNOWN";}
	$pump7 = "$pumpBIT7:$pumpTXT7";
// END translate 'bits' to useful stuff

// Debug BITS to check Flags
	// showbits($modrequest,"36:modrequest");
	// showbits($ionisation,"37:ionisation");
	// showbits($valves,"38:valves");  
	// showbits($pump,"39:pump");  
// END Debug BITS to check Flags

// Mapping of Status & Sub-Status values  	
  	$state = hexdec($state);
  	if ($state == 0) {$state = "0:Standby";}
  	elseif ($state == 1) {$state = "1:Boiler start";}
  	elseif ($state == 2) {$state = "2:Burner start";}
  	elseif ($state == 3) {$state = "3:Burning CH";}
  	elseif ($state == 4) {$state = "4:Burning DHW";}
  	elseif ($state == 5) {$state = "5:Burner stop";}
  	elseif ($state == 6) {$state = "6:Boiler stop";}
  	elseif ($state == 7) {$state = "7:-";}
  	elseif ($state == 8) {$state = "8:Controlled stop";}
  	elseif ($state == 9) {$state = "9:Blocking mode";}
  	elseif ($state == 10) {$state = "10:Locking mode";}
  	elseif ($state == 11) {$state = "11:Chimney mode L";}
  	elseif ($state == 12) {$state = "12:Chimney mode h";}
  	elseif ($state == 13) {$state = "13:Chimney mode H";}
  	elseif ($state == 14) {$state = "14:-";}
  	elseif ($state == 15) {$state = "15:Manual Heat demand";}
  	elseif ($state == 16) {$state = "16:Boiler-frost-protection";}
  	elseif ($state == 17) {$state = "17:De-airation";}
  	elseif ($state == 18) {$state = "18:Controller temp protection";}
  	elseif ($state == 19) {$state = "19:-";}
  	elseif ($state == 20) {$state = "20:-";}
  	elseif ($state == 999) {$state = "Unkown State";}
  	else {$state = "Unknown State";}
  	
  	$substate = hexdec($substate);
  	if ($substate == 0) {$substate = "0:Standby";}
  	elseif ($substate == 1) {$substate = "1:Anti-cycling";}
  	elseif ($substate == 2) {$substate = "2:Open hydraulic valve";}
	elseif ($substate == 3) {$substate = "3:Pump start";}
	elseif ($substate == 4) {$substate = "4:Wait for burner start";}
	elseif ($substate == 5) {$substate = "5:-";}
	elseif ($substate == 6) {$substate = "6:-";}
	elseif ($substate == 7) {$substate = "7:-";}
	elseif ($substate == 8) {$substate = "8:-";}
	elseif ($substate == 9) {$substate = "9:-";}
	elseif ($substate == 10) {$substate = "10:Open external gas valve";}
	elseif ($substate == 11) {$substate = "11:Fan to fluegasvalve speed";}
	elseif ($substate == 12) {$substate = "12:Open fluegasvalve";}
	elseif ($substate == 13) {$substate = "13:Pre-purge";}
	elseif ($substate == 14) {$substate = "14:Wait for release";}
	elseif ($substate == 15) {$substate = "15:Burner start";}
	elseif ($substate == 16) {$substate = "16:VPS test";}
	elseif ($substate == 17) {$substate = "17:Pre-ignition";}
	elseif ($substate == 18) {$substate = "18:Ignition";}
	elseif ($substate == 19) {$substate = "19:Flame check";}
	elseif ($substate == 20) {$substate = "20:Interpurge";}
	elseif ($substate == 30) {$substate = "30:Normal internal setpoint";}
	elseif ($substate == 31) {$substate = "31:Limited internal setpoint";}
	elseif ($substate == 32) {$substate = "32:Normal power control";}
	elseif ($substate == 33) {$substate = "33:Gradient control level 1";}
	elseif ($substate == 34) {$substate = "34:Gradient control level 2";}
	elseif ($substate == 35) {$substate = "35:Gradient control level 3";}
	elseif ($substate == 36) {$substate = "36:Flame protection";}
	elseif ($substate == 37) {$substate = "37:Stabilization time";}
	elseif ($substate == 38) {$substate = "38:Cold start";}
	elseif ($substate == 39) {$substate = "39:Limited power Tfg";}
	elseif ($substate == 40) {$substate = "40:Burner stop";}
	elseif ($substate == 41) {$substate = "41:Post purge";}
	elseif ($substate == 42) {$substate = "42:Fan to flue gas valve speed";}
	elseif ($substate == 43) {$substate = "43:Close flue gas valve";}
	elseif ($substate == 44) {$substate = "44:Stop fan";}
	elseif ($substate == 45) {$substate = "45:Close external gas valve";}
	elseif ($substate == 46) {$substate = "46:-";}
	elseif ($substate == 47) {$substate = "47:-";}
	elseif ($substate == 48) {$substate = "48:-";}
	elseif ($substate == 49) {$substate = "49:-";}
	elseif ($substate == 39) {$substate = "39:Heat exchanger protection";}
	elseif ($substate == 60) {$substate = "60:Pump post running";}
	elseif ($substate == 61) {$substate = "61:Pump stop";}
	elseif ($substate == 62) {$substate = "62:Close hydraulic valve";}
	elseif ($substate == 63) {$substate = "63:Start anti-cycle timer";}
	elseif ($substate == 999) {$substate = "999:Unkown Sub-State";}
	elseif ($substate == 255) {$substate = "255:Reset wait time";}
	else {$substate = "Unknown Sub-State";}
	
	// Combine State & Sub-State to a single variable
	$state = $state . ' / ' . $substate;

// END mapping of Status & Sub-Status values
  
// START Display Parameters & Values

	echo str_repeat("=", 40) . "<br />";
	echo "Counters Received:<br />";
	echo str_repeat("=", 40) . "<br />";
	echo "Hours run pump CH+DHW:		$pumphours_ch_dhw<br />";
	echo "Hours run 3-way valve DHW:	$threewayvalvehours<br />";
	echo "Hours run CH+DHW:				$hours_ch_dhw<br />";
	echo "Hours run DHW:				$hours_dhw<br />";
	echo "Power Supply available hours:	$powerhours_ch_dhw<br />";
	echo "Pump starts CH+DHW:			$pumpstarts_ch_dhw<br />";
	echo "Number of 3-way valve cycles:	$nr_threewayvalvecycles<br />";
	echo "Burner Starts DHW:			$burnerstarts_dhw<br />";
	echo "Total Burner Starts CH+DHW:	$tot_burnerstarts_ch_dhw<br />";
	echo "Failed burner starts:			$failed_burnerstarts<br />";
	echo "Number of flame loss:			$nr_flame_loss<br />";
	echo str_repeat("=", 40) . "<br />";
	echo "Parameters & Values Received:<br />";
	echo str_repeat("=", 40) . "<br />";
	echo "Flow Temperature:				$flowtemperature &degC<br />";
	echo "Return Temperature:			$returntemperature &degC<br />";
	echo "DHW-in Temperature:			$dhwintemperature &degC<br />";  	
	echo "Calorifier Temperature:		$caloriftemperature &degC<br />";  	
	echo "Outside Temperature:			$outsidetemperature &degC<br />";  	
	echo "Control Temperature:			$controltemperature &degC<br />";
	echo "Internal Setpoint:			$internalsetpoint &degC<br />";
	echo "CH Setpoint:					$chsetpoint &degC<br />";
	echo "DHW Setpoint:					$dhwsetpoint &degC<br />";
	echo "Room Temperature:				$roomtemperature &degC<br />";
	echo "Room Temp. Setpoint:			$thermostat &degC<br />";
	echo "Boiler Control Temp.:			$boilerctrltemperature &degC<br />";
	echo "<br />";

	echo "Fan Speed setpoint:			$fanspeedsetpoint RPM<br />";
	echo "Fan Speed:					$fanspeed RPM<br />";
	echo "Ionisation Current:			$ionisationcurrent μA<br />";
	echo "Pump Speed:					$pumppower %<br />";
	echo "Hydro Pressure:				$pressure bar<br />";
	echo "DHW Flow rate:				$dhwflowrate litres/minute<br />";
	echo "Desired Max.Power from controller:	$requiredoutput %<br />";
	echo "Output:						$availablepower %<br />";
	echo "Actual Power from boiler:		$actualpower %<br />";
	echo "<br />";

	echo "Ignition:						$ignition2<br />";
	echo "Gas Valve:					$gasvalve0<br />";
	echo "Ionisation:					$ionisation2<br />";
	echo "Pump:							$pump0<br />";
	echo "3-Way Valve:					$threewayvalve3<br />";
	echo "Heat Demand from mod.controller:		$modrequest1<br />";
	echo "Heat Demand from on/off controller:	$modrequest2<br />";	
	echo "DHW Eco:						$modrequest4<br />";
	echo "DHW Demand:					$modrequest7<br />";
	echo "Combined State/Sub-State: 	$state<br />";
	echo str_repeat("=", 40) . "<br />";
// END Display Parameters & Values

// Update Domoticz Devices with collected values
// DomoticZ Device ID's
	$pumphours_ch_dhwIDX = ($ini_array['pumphours_ch_dhwIDX']);
	$threewayvalvehoursIDX = ($ini_array['threewayvalvehoursIDX']);
	$hours_ch_dhwIDX = ($ini_array['hours_ch_dhwIDX']);
	$hours_dhwIDX = ($ini_array['hours_dhwIDX']);
	$powerhours_ch_dhwIDX = ($ini_array['powerhours_ch_dhwIDX']);
	$pumpstarts_ch_dhwIDX = ($ini_array['pumpstarts_ch_dhwIDX']);
	$nr_threewayvalvecyclesIDX = ($ini_array['nr_threewayvalvecyclesIDX']);
	$burnerstarts_dhwIDX = ($ini_array['burnerstarts_dhwIDX']);
	$tot_burnerstarts_ch_dhwIDX = ($ini_array['tot_burnerstarts_ch_dhwIDX']);
	$failed_burnerstartsIDX = ($ini_array['failed_burnerstartsIDX']);
	$nr_flame_lossIDX = ($ini_array['nr_flame_lossIDX']);
	$flowtemperatureIDX = ($ini_array['flowtemperatureIDX']);
	$returntemperatureIDX = ($ini_array['returntemperatureIDX']);
  	$dhwintemperatureIDX = ($ini_array['dhwintemperatureIDX']);
  	$caloriftemperatureIDX = ($ini_array['calorifiertemperatureIDX']);
  	$outsidetemperatureIDX = ($ini_array['outsidetemperatureIDX']);
  	$controltemperatureIDX = ($ini_array['controltemperatureIDX']);
  	$internalsetpointIDX = ($ini_array['internalsetpointIDX']);
	$chsetpointIDX = ($ini_array['chsetpointIDX']);
	$dhwsetpointIDX = ($ini_array['dhwsetpointIDX']);
	$roomtemperatureIDX = ($ini_array['roomtemperatureIDX']);
	$thermostatIDX = ($ini_array['thermostatIDX']);
	$boilerctrltemperatureIDX = ($ini_array['boilerctrltemperatureIDX']);
	$fanspeedsetpointIDX = ($ini_array['fanspeedsetpointIDX']);
  	$fanspeedIDX = ($ini_array['fanspeedIDX']);
  	$ionisationcurrentIDX = ($ini_array['ionisationcurrentIDX']);
  	$pumppowerIDX = ($ini_array['pumppowerIDX']);
  	$pressureIDX = ($ini_array['pressureIDX']);
  	$dhwflowrateIDX = ($ini_array['dhwflowrateIDX']);
  	$requiredoutputIDX = ($ini_array['requiredoutputIDX']);
  	$availablepowerIDX = ($ini_array['availablepowerIDX']);
  	$actualpowerIDX = ($ini_array['actualpowerIDX']);
	$modulationdemandIDX = ($ini_array['modulationdemandIDX']);
	$ignitionIDX = ($ini_array['ignitionIDX']);
	$gasIDX = ($ini_array['gasIDX']);
	$ionisationIDX = ($ini_array['ionisationIDX']);
	$pumpIDX = ($ini_array['pumpIDX']);
	$threewayvalveIDX = ($ini_array['threewayvalveIDX']);
	$dhwrequestIDX = ($ini_array['dhwrequestIDX']);
	$dhwecoIDX = ($ini_array['dhwecoIDX']);
	$stateIDX = ($ini_array['stateIDX']);
// END Device ID's

// Set variables for cURL updates & call udevice function to update
	$DOMOIPAddress = ($ini_array['DOMOIPAddress']);
	$DOMOPort = ($ini_array['DOMOPort']);
	$Username = ($ini_array['Username']);
	$Password = ($ini_array['Password']);	

	$DOMOpumphours_ch_dhw = udevice($pumphours_ch_dhwIDX, 0, $pumphours_ch_dhw, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOthreewayvalvehours = udevice($threewayvalvehoursIDX, 0, $threewayvalvehours, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOhours_ch_dhw = udevice($hours_ch_dhwIDX, 0, $hours_ch_dhw, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOhours_dhw = udevice($hours_dhwIDX, 0, $hours_dhw, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOpowerhours_ch_dhw = udevice($powerhours_ch_dhwIDX, 0, $powerhours_ch_dhw, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOpumpstarts_ch_dhw = udevice($pumpstarts_ch_dhwIDX, 0, $pumpstarts_ch_dhw, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOnr_threewayvalvecycles = udevice($nr_threewayvalvecyclesIDX, 0, $nr_threewayvalvecycles, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOburnerstarts_dhw = udevice($burnerstarts_dhwIDX, 0, $burnerstarts_dhw, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOtot_burnerstarts_ch_dhw = udevice($tot_burnerstarts_ch_dhwIDX, 0, $tot_burnerstarts_ch_dhw, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOfailed_burnerstarts = udevice($failed_burnerstartsIDX, 0, $failed_burnerstarts, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOnr_flame_loss = udevice($nr_flame_lossIDX, 0, $nr_flame_loss, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOflowtemperature = udevice($flowtemperatureIDX, 0, $flowtemperature, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOreturntemperature = udevice($returntemperatureIDX, 0, $returntemperature, $DOMOIPAddress, $DOMOPort, $Username, $Password);
 	$DOMOdhwintemperature = udevice($dhwintemperatureIDX, 0, $dhwintemperature, $DOMOIPAddress, $DOMOPort, $Username, $Password);
 	$DOMOcaloriftemperature = udevice($caloriftemperatureIDX, 0, $caloriftemperature, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOoutsidetemperature = udevice($outsidetemperatureIDX, 0, $outsidetemperature, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOcontroltemperature = udevice($controltemperatureIDX, 0, $controltemperature, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOinternalsetpoint = udevice($internalsetpointIDX, 0, $internalsetpoint, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOchsetpoint = udevice($chsetpointIDX, 0, $chsetpoint, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOdhwsetpoint = udevice($dhwsetpointIDX, 0, $dhwsetpoint, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOroomtemperature = udevice($roomtemperatureIDX, 0, $roomtemperature, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOthermostat = udevice($thermostatIDX, 0, $thermostat, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOboilerctrltemp = udevice($boilerctrltemperatureIDX, 0, $boilerctrltemperature, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOfanspeedsetpoint = udevice($fanspeedsetpointIDX, 0, $fanspeedsetpoint, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOfanSpeed = udevice($fanspeedIDX, 0, $fanspeed, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOionisationCurent = udevice($ionisationcurrentIDX, 0, $ionisationcurrent, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOpumppower = udevice($pumppowerIDX, 0, $pumppower, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOpressure = udevice($pressureIDX, 0, $pressure, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOdhwflowrate = udevice($dhwflowrateIDX, 0, $dhwflowrate, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOrequiredoutput = udevice($requiredoutputIDX, 0, $requiredoutput, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOavailablepower = udevice($availablepowerIDX, 0, $availablepower, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOactualpower = udevice($actualpowerIDX, 0, $actualpower, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOmodulationdemand = udevice($modulationdemandIDX, 0, $modrequest1, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOignition = udevice($ignitionIDX, 0, $ignition2, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOgas = udevice($gasIDX, 0, $gasvalve0, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOionisation = udevice($ionisationIDX, 0, $ionisation2, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOpump = udevice($pumpIDX, 0, $pump0, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOthreewayvalve = udevice($threewayvalveIDX, 0, $threewayvalve3, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOdhwrequest = udevice($dhwrequestIDX, 0, $modrequest7, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOdhweco = udevice($dhwecoIDX, 0, $modrequest4, $DOMOIPAddress, $DOMOPort, $Username, $Password);
	$DOMOstatus = udevice($stateIDX, 0, str_replace(' ', '%20', $state), $DOMOIPAddress, $DOMOPort, $Username, $Password);
// END set variables for cURL updates

}

// Function to update Domoticz using cURL
//
function udevice($idx, $nvalue, $svalue, $DOMOIPAddress, $DOMOPort, $Username, $Password) 
{
	
// Comment if you don't want to update Domoticz	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, "http://$Username:$Password@$DOMOIPAddress:$DOMOPort/json.htm?type=command&param=udevice&idx=$idx&nvalue=$nvalue&svalue=$svalue");	
	curl_exec($ch);
	curl_close($ch);
// End Comment if you don't want to update Domoticz

// Sleep for 1/4 sec - in case system stressed/slow
	// usleep(250000);
	
// Debug cURL string
	// echo "Debug Info - http://$Username:$Password@$DOMOIPAddress:$DOMOPort/json.htm?type=command&param=udevice&idx=$idx&nvalue=$nvalue&svalue=$svalue<br />";
	// echo "IDX: $idx Value: $svalue<br />";
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

	echo str_repeat("=", 40) . "<br />";
	echo "Content & Value of BITS: $byte<br />";
	echo "Value (HEX, Dec, Bin): $val, $valDEC, $valBIN<br />";	
	echo str_repeat("=", 40) . "<br />";
	echo "nBit0: 	$nbitX0" .' '."  Bit0: 	$valBITx0<br />";
	echo "nBit1: 	$nbitX1" .' '."  Bit1: 	$valBITx1<br />";
	echo "nBit2: 	$nbitX2" .' '."  Bit2: 	$valBITx2<br />";
	echo "nBit3: 	$nbitX3" .' '."  Bit3: 	$valBITx3<br />";
	echo "nBit4: 	$nbitX4" .' '."  Bit4: 	$valBITx4<br />";
	echo "nBit5: 	$nbitX5" .' '."  Bit5: 	$valBITx5<br />";
	echo "nBit6: 	$nbitX6" .' '."  Bit6: 	$valBITx6<br />";
	echo "nBit7: 	$nbitX7" .' '."  Bit7: 	$valBITx7<br />";
	echo str_repeat("=", 40) . "<br />";
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
// usage (e.g.):
// $hexstr = "43616c656e7461202020202020202020"; # Calenta
function hex2str($hex) {
    $str = "";
    for($i=0;$i<strlen($hex);$i+=2) $str .= chr(hexdec(substr($hex, $i, 2)));
    return $str;
}

?>
