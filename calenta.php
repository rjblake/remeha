<?php
// Uncomment to report Errors for Debug purposes
// error_reporting(E_ALL);

/* 
****************************************
** Codes are for a Remeha Calenta 40C **
****************************************
*/

function hex_dump($data, $newline="<br />")
{
	static $from = '';
	static $to = '';
	static $width = 16; # number of bytes per line
	static $pad = '.'; # padding for non-visible characters

	if ($from==='')
	{
	for ($i=0; $i<=0xFF; $i++)
		{
    		$from .= chr($i);
    		$to .= ($i >= 0x20 && $i <= 0x7E) ? chr($i) : $pad;
		}
	}

// Start Do a CRC Check	
	$decode = str_split(bin2hex($data), 2);
	$hexstr = str_split(bin2hex($data), 148);
	$hexstr2 = bin2hex($data);
	$hexstrPayload = substr($hexstr2, 2, 140);
	$hexstrCRC = substr($hexstr2, 142, 4);
	echo "Full Hex string response: $hexstr2<br />";
	echo "Hex string Payload: $hexstrPayload<br />";
	echo "Hex string CRC Value: $hexstrCRC<br />";		
	
	$crcCalc = crc16_modbus($hexstrPayload);
	
	if ($hexstrCRC == $crcCalc)
		{
		echo "CRC Computes OK: $hexstrCRC = $crcCalc <br />";
		}
	else
		{
		echo "CRC ERROR!!!  $hexstrCRC != $crcCalc <br />";
		exit("CRC does not compute");
		}
	
// Uncomment to add full response to an Array 
	// print_r($hexstr);
	// echo "<br />"; 
	
// Uncomment to show array elements
	// print_r($decode);
	// echo "<br />";

	$flowtemperature ="";
	$flowtemperature .= $decode["8"];
	$flowtemperature .= $decode["7"];
	   
	$returntemperature = "";
	$returntemperature .= $decode["10"];
	$returntemperature .= $decode["9"];

	$outsidetemperature = "";
	$outsidetemperature .= $decode["14"];
	$outsidetemperature .= $decode["13"];
	if ($outsidetemperature == 8000) {$outsidetemperature = 0.00;}
	else {$outsidetemperature == $outsidetemperature;} 	  
	   
	$roomtemperature = "";
	$roomtemperature .= $decode["22"];
	$roomtemperature .= $decode["21"];
  
	$controltemperature = "";
	$controltemperature .= $decode["59"];
	$controltemperature .= $decode["58"];

	$caloriftemperature = "";
	$caloriftemperature .= $decode["12"];
	$caloriftemperature .= $decode["11"];
	if ($caloriftemperature == 8000) {$caloriftemperature = 0.00;}
	else {$caloriftemperature == $caloriftemperature;} 	  
      
	$thermostat = "";
	$thermostat .= $decode["28"];
	$thermostat .= $decode["27"];
  
	$chsetpoint = "";
	$chsetpoint .= $decode ["24"];
	$chsetpoint .= $decode ["23"];
  
	$dhwsetpoint = "";
	$dhwsetpoint .= $decode ["26"];
	$dhwsetpoint .= $decode ["25"];
  
	$internalsetpoint = "";
	$internalsetpoint .= $decode ["35"];
	$internalsetpoint .= $decode ["34"];
  
	$boilerctrltemperature = "";
	$boilerctrltemperature .= $decode ["20"];
	$boilerctrltemperature .= $decode ["19"];

  	$fanspeed = "";
	$fanspeed .= $decode ["32"];
	$fanspeed .= $decode ["31"];
  
	$fanspeedsetpoint = "";
	$fanspeedsetpoint .= $decode ["30"];
	$fanspeedsetpoint .= $decode ["29"];
  
	$ionisationcurrent = "";
	$ionisationcurrent .= $decode ["33"];
  
	$pumppower = "";
	$pumppower .= $decode ["37"];
  
	$pressure = "";
	$pressure .= $decode ["56"];
  
	$dhwflowrate = "";
	$dhwflowrate .= $decode ["61"];
	$dhwflowrate .= $decode ["60"];	
  
	$actualpower = "";
	$actualpower .= $decode ["40"];
  
	$requiredoutput = "";
	$requiredoutput .= $decode ["39"];
    
	$availablepower = "";
	$availablepower .= $decode ["36"];
    
	$modrequest = "";
	$modrequest .= $decode ["43"];
  
	$ionisation = "";
	$ionisation .= $decode ["44"];
	
	$valves = "";
	$valves .= $decode ["45"];
  
	$pump = "";
	$pump .= $decode ["46"];
  
	$state = "";
	$state .= $decode ["47"];

	$substate = "";
	$substate .= $decode ["50"];  

//Convert Hex2Dec 
  	$flowtemperature = number_format(hexdec($flowtemperature)/100, 2);
	$returntemperature = number_format(hexdec($returntemperature)/100, 2);
	$outsidetemperature = number_format(hexdec($outsidetemperature)/100, 2);	
  	$roomtemperature = number_format(hexdec($roomtemperature)/100, 2);
  	$controltemperature = number_format(hexdec($controltemperature)/100, 2);
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
// end Convert Hex2Dec  	
  	
// Translate 'bits' to messages  	
	$modrequestBIT1 = hexdec($modrequest);
	if (nbit($modrequestBIT1,1) == 0) {$modrequestTXT1 = "No";}
	elseif (nbit($modrequestBIT1,1) == 1) {$modrequestTXT1 = "Yes";}
	else {$modrequestTXT1 = "UNKNOWN";}
	$modrequest1 = "$modrequestBIT1:$modrequestTXT1";

	$modrequestBIT2 = hexdec($modrequest);
	if (nbit($modrequestBIT2,2) == 0) {$modrequestTXT2 = "No";}
	elseif (nbit($modrequestBIT2,2) == 1) {$modrequestTXT2 = "Yes";}
	else {$modrequestTXT2 = "UNKNOWN";}
	$modrequest2 = "$modrequestBIT2:$modrequestTXT2";

	$modrequestBIT4 = hexdec($modrequest);
	if (nbit($modrequestBIT4,4) == 0) {$modrequestTXT4 = "Yes";}
	elseif (nbit($modrequestBIT4,4) == 1) {$modrequestTXT4 = "No";}
	else {$modrequestTXT4 = "UNKNOWN";}
	$modrequest4 = "$modrequestBIT4:$modrequestTXT4";

	$modrequestBIT7 = hexdec($modrequest);
	if (nbit($modrequestBIT7,7) == 0) {$modrequestTXT7 = "No";}
	elseif (nbit($modrequestBIT7,7) == 1) {$modrequestTXT7 = "Yes";}
	else {$modrequestTXT7 = "UNKNOWN";}
	$modrequest7 = "$modrequestBIT7:$modrequestTXT7";

	$ionisationBIT2 = hexdec($ionisation);
	if (nbit($ionisationBIT2,2) == 0) {$ionisationTXT2 = "No";}
	elseif (nbit($ionisationBIT2,2) == 1) {$ionisationTXT2 = "Yes";}
	else {$ionisationTXT2 = "UNKNOWN";}
	$ionisation2 = "$ionisationBIT2:$ionisationTXT2";

	$gasvalveBIT0 = hexdec($valves);
	if (nbit($gasvalveBIT0,0) == 0) {$gasvalveTXT0 = "Open";}
	elseif (nbit($gasvalveBIT0,0) == 1) {$gasvalveTXT0 = "Closed";}
	else {$gasvalveTXT0 = "UNKNOWN";}
	$gasvalve0 = "$gasvalveBIT0:$gasvalveTXT0";

	$ignitionBIT2 = hexdec($valves);
	if (nbit($ignitionBIT2,2) == 0) {$ignitionTXT2 = "Off";}
	elseif (nbit($ignitionBIT2,2) == 1) {$ignitionTXT2 = "On";}
	else {$ignitionTXT2 = "UNKNOWN";}
	$ignition2 = "$ignitionBIT2:$ignitionTXT2";

	$threewayvalveBIT3 = hexdec($valves);
	if (nbit($threewayvalveBIT3,3) == 0) {$threewayvalveTXT3 = "CH";}
	elseif (nbit($threewayvalveBIT3,3) == 1) {$threewayvalveTXT3 = "DHW";}
	else {$threewayvalveTXT3 = "UNKNOWN";}
	$threewayvalve3 = "$threewayvalveBIT3:$threewayvalveTXT3";

	$pumpBIT0 = hexdec($pump);
	if (nbit($pumpBIT0,0) == 0) {$pumpTXT0 = "Off";}
	elseif (nbit($pumpBIT0,0) == 1) {$pumpTXT0 = "On";}
	else {$pumpTXT0 = "UNKNOWN";}
	$pump0 = "$pumpBIT0:$pumpTXT0";
// end translate 'bits' to messages

// Debug BITS to check Flags 
	// showbits($modrequest);
	// showbits($ionisation);
	// showbits($valves);  
	// showbits($pump);  
// end Debug BITS to check Flags

// Start mapping of Status & Sub-Status values  	
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
	else {$substate = "Unknown Sub-State";}
	
	$state = $state . ' / ' . $substate;
// End mapping of Status & Sub-Status values
  
// Start Display Parameters & Values
	echo "=============================================<br />";	
	echo "Parameters & Values Received:<br />";
	echo "=============================================<br />";
	echo "Flow Temperature:			$flowtemperature &degC<br />";
	echo "Return Temperature:		$returntemperature &degC<br />";
	echo "Calorifier Temperature:	$caloriftemperature &degC<br />";  	
	echo "Outside Temperature:		$outsidetemperature &degC<br />";  	
	echo "Control Temperature:		$controltemperature &degC<br />";
	echo "Internal Setpoint:		$internalsetpoint &degC<br />";
	echo "CH Setpoint:				$chsetpoint &degC<br />";
	echo "DHW Setpoint:				$dhwsetpoint &degC<br />";
	echo "Room Temperature:			$roomtemperature &degC<br />";
	echo "Room Temp. Setpoint:		$thermostat &degC<br />";
	echo "Boiler Control Temp.:		$boilerctrltemperature &degC<br />";
	echo "<br />";
	echo "Fan Speed setpoint:		$fanspeedsetpoint RPM<br />";
	echo "Fan Speed:				$fanspeed RPM<br />";
	echo "Ionisation Current:		$ionisationcurrent μA<br />";
	echo "Pump Speed:				$pumppower %<br />";
	echo "Hydro Pressure:			$pressure bar<br />";
	echo "DHW Flow rate:			$dhwflowrate litres/minute<br />";
	echo "Required Output:			$requiredoutput %<br />";
	echo "Available Power:			$availablepower %<br />";
	echo "Actual Power:				$actualpower %<br />";
	echo "<br />";

	echo "Ignition:					$ignition2<br />";
	echo "Gas Valve:				$gasvalve0<br />";
	echo "Ionisation:				$ionisation2<br />";
	echo "Pump:						$pump0<br />";
	echo "3-Way Valve:				$threewayvalve3<br />";
	echo "Mod. Heat Demand:			$modrequest1<br />";
	echo "On/Off Heat Demand:		$modrequest2<br />";	
	echo "DHW Eco:					$modrequest4<br />";
	echo "DHW Demand:				$modrequest7<br />";
	echo "Combined State/Sub-State: $state<br />";
	echo "=============================================<br />";
// End Display Parameters & Values


// Update Domoticz Devices with collected values
// DomoticZ Device ID's
  	$flowtemperatureIDX = 93;			#Temperature
	$returntemperatureIDX = 94;			#Temperature
  	$caloriftemperatureIDX = 95;		#Temperature
  	$outsidetemperatureIDX = 96;		#Temperature
  	$controltemperatureIDX = 97;		#Temperature
  	$internalsetpointIDX = 98;			#Temperature
	$chsetpointIDX = 99;				#Temperature
	$dhwsetpointIDX = 100; 				#Temperature
	$roomtemperatureIDX = 101;			#Temperature
	$thermostatIDX = 102; 				#Temperature
	$boilerctrltemperatureIDX = 103;	#Temperature
	$fanspeedsetpointIDX = 104;			#Custom Sensor - Axis Label "RPM"
  	$fanspeedIDX = 105;					#Custom Sensor - Axis Label "RPM"
  	$ionisationcurrentIDX = 106;		#Custom Sensor - Axis Label "microamps"
  	$pumppowerIDX = 107;				#Percentage 
  	$pressureIDX = 108;					#Pressure (Bar)
  	$dhwflowrateIDX = 109;				#Waterflow
  	$requiredoutputIDX = 110;			#Percentage
  	$availablepowerIDX = 111;			#Percentage
  	$actualpowerIDX = 112;				#Percentage
	$modulationdemandIDX = 113;			#Text
	$ignitionIDX = 114;					#Text
	$gasIDX = 115;						#Text
	$ionisationIDX = 116;				#Text
	$pumpIDX = 117;						#Text
	$threewayvalveIDX = 118;			#Text
	$DHWRequestIDX = 119;				#Text
	$DHWEcoIDX = 120;					#Text
	$stateIDX = 121;					#Text
// end Device ID's

// Set variables for cURL updates & call udevice function to update
	$DOMOflowtemperature = udevice($flowtemperatureIDX,0,$flowtemperature);
	$DOMOreturntemperature = udevice($returntemperatureIDX,0,$returntemperature);
 	$DOMOcaloriftemperature = udevice($caloriftemperatureIDX,0,$caloriftemperature);
	$DOMOoutsidetemperature = udevice($outsidetemperatureIDX,0,$outsidetemperature);
	$DOMOcontroltemperature = udevice($controltemperatureIDX,0,$controltemperature);
	$DOMOinternalsetpoint = udevice($internalsetpointIDX,0,$internalsetpoint);
	$DOMOchsetpoint = udevice($chsetpointIDX,0,$chsetpoint);
	$DOMOdhwsetpoint = udevice($dhwsetpointIDX,0,$dhwsetpoint);
	$DOMOroomtemperature = udevice($roomtemperatureIDX,0,$roomtemperature);
	$DOMOthermostat = udevice($thermostatIDX,0,$thermostat);
	$DOMOboilerctrltemp = udevice($boilerctrltemperatureIDX,0,$boilerctrltemperature);
	$DOMOfanspeedsetpoint = udevice($fanspeedsetpointIDX,0,$fanspeedsetpoint);
	$DOMOfanSpeed = udevice($fanspeedIDX,0,$fanspeed);
	$DOMOionisationCurent = udevice($ionisationcurrentIDX,0,$ionisationcurrent);
	$DOMOpumppower = udevice($pumppowerIDX,0,$pumppower);
	$DOMOpressure = udevice($pressureIDX,0,$pressure);
	$DOMOdhwflowrate = udevice($dhwflowrateIDX,0,$dhwflowrate);
	$DOMOrequiredoutput = udevice($requiredoutputIDX,0,$requiredoutput);
	$DOMOavailablepower = udevice($availablepowerIDX,0,$availablepower);
	$DOMOactualpower = udevice($actualpowerIDX,0,$actualpower);
	$DOMOmodulationdemand = udevice($modulationdemandIDX,0,$modrequest1);
	$DOMOignition = udevice($ignitionIDX,0,$ignition2);
	$DOMOgas = udevice($gasIDX,0,$gasvalve0);
	$DOMOionisation = udevice($ionisationIDX,0,$ionisation2);
	$DOMOpump = udevice($pumpIDX,0,$pump0);
	$DOMOthreewayvalve = udevice($threewayvalveIDX,0,$threewayvalve3);
	$DOMODHWRequest = udevice($DHWRequestIDX,0,$modrequest7);
	$DOMODHWEco = udevice($DHWEcoIDX,0,$modrequest4);
	$DOMOstatus = udevice($stateIDX,0,str_replace(' ', '%20', $state));
// end set variables for cURL updates

}

// Function to update Domoticz using cURL
//
function udevice($idx,$nvalue,$svalue) 
{
	//IP Address & Port of Domoticz Server - NOT ESP8266 IP & Port	
	$DOMOIPAddress = "192.168.178.10";
	$DOMOPort = 8084;

// Comment if you don't want to update Domoticz	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://$DOMOIPAddress:$DOMOPort/json.htm?type=command&param=udevice&idx=$idx&nvalue=$nvalue&svalue=$svalue");
	curl_exec($ch);
	curl_close($ch);
// End Comment if you don't want to update Domoticz

// Sleep for 1/4 sec - in case system stressed/slow
	// usleep(250000);
	
// Debug cURL string
	// echo "Debug Only - No db update - http://$DOMOIPAddress:$DOMOPort/json.htm?type=command&param=udevice&idx=$idx&nvalue=$nvalue&svalue=$svalue<br />";

}

// Hex string to get data values from Calenta - 02 FE 01 05 08 02 01 69 AB 03 - does not include counters
// Change as required for specific model (Calenta, avanta, etc...)
//
$remeha1=chr(0x02).chr(0xFE).chr(0x01).chr(0x05).chr(0x08).chr(0x02).chr(0x01).chr(0x69).chr(0xAB).chr(0x03);

// Open connection to ESP connected to Calenta
// Change to your own IP Address and Port
//
$ESPIPAddress = "192.168.178.91";
$ESPPort = 23;
$fp=fsockopen($ESPIPAddress,$ESPPort, $errno, $errstr, 30);
if (!$fp) 
	{
	echo "ERROR opening port<br />";
	echo $errno;
	echo $errstr;
	} 
else 
	{
	echo "=============================================<br />";
	echo "Connected to port<br />";
   	echo "Sending request...<br />";
   	// Send the Hex String to the Remeha
   	fputs($fp,$remeha1);
	echo "Request sent, reading answer...<br />";
   	
   	$data="";
   	$data=fread($fp, 180);
   
	echo "Answer read<br />";
	echo "=============================================<br />";	
	$output = hex_dump($data);
	echo "<br />=============================================<br />";
	
  	echo "<br />";
  	fclose($fp);
	echo "*********************<br />";	
	echo " ** Connection closed **<br />";
	echo "*********************<br />";	
	} 

// Function to work out BIT flags
// 
function nbit($number, $n) 
{
	return ($number >> $n) & 1;	#BITS Numbered 0-7
}

// Function to debug BITs
//
function showbits($val)
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
	$nbitX0 = nbit($valDEC,0);
	$nbitX1 = nbit($valDEC,1);
	$nbitX2 = nbit($valDEC,2);
	$nbitX3 = nbit($valDEC,3);
	$nbitX4 = nbit($valDEC,4);
	$nbitX5 = nbit($valDEC,5);
	$nbitX6 = nbit($valDEC,6);
	$nbitX7 = nbit($valDEC,7);

	echo "========================================<br />";
	echo "Content & Value of BITS:<br />";
	echo "Value (HEX, Dec, Bin): $val, $valDEC, $valBIN<br />";	
	echo "========================================<br />";
	echo "nBit0: 	$nbitX0" .' '."Bit0: 	$valBITx0<br />";
	echo "nBit1: 	$nbitX1" .' '."Bit1: 	$valBITx1<br />";
	echo "nBit2: 	$nbitX2" .' '."Bit2: 	$valBITx2<br />";
	echo "nBit3: 	$nbitX3" .' '."Bit3: 	$valBITx3<br />";
	echo "nBit4: 	$nbitX4" .' '."Bit4: 	$valBITx4<br />";
	echo "nBit5: 	$nbitX5" .' '."Bit5: 	$valBITx5<br />";
	echo "nBit6: 	$nbitX6" .' '."Bit6: 	$valBITx6<br />";
	echo "nBit7: 	$nbitX7" .' '."Bit7: 	$valBITx7<br />";
	echo "========================================<br /><br />";
// end workings of BITS
}

// ...and the CRC16 Modbus Check before we do anything with the data...
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
    $crc_modbus = substr($crc_semi_inverted,2,2).substr($crc_semi_inverted,0,2);
    $crc_modbus = hexdec($crc_modbus);
    return sprintf('%04x', $crc_modbus);
}

?>
