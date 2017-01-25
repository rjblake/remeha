<style>
  body {
  font-family: monaco, monospace;
  font-size: 0.7em;
  // font-family: arial,sans-serif;
  // font-size: small;
  text-align: left;
}
h1 {
  font-size: 20px
}
h2 {
  font-size: 14px;
}
h3 {
  font-size: 10px;
}
</style>

<?php
// Uncomment to report Errors for Debug purposes
// error_reporting(E_ALL);
require('user_functions.php');

// remeha.ini file Variables
//
$ini_array = parse_ini_file("remeha.ini");
$ESPIPAddress = $ini_array['ESPIPAddress'];
$ESPPort = $ini_array['ESPPort'];
$retries = $ini_array['retries'];
$nanosleeptime =  $ini_array['nanosleeptime'];
$echo_flag = "1";
$newline = "<br />";

$remeha_id1 = hex2bin($ini_array['remeha_id1']);
$remeha_id2 = hex2bin($ini_array['remeha_id2']);
$remeha_id3 = hex2bin($ini_array['remeha_id3']);
$remeha_param1 = hex2bin($ini_array['remeha_param1']);
$remeha_param2 = hex2bin($ini_array['remeha_param2']);
$remeha_param3 = hex2bin($ini_array['remeha_param3']);
$remeha_param4 = hex2bin($ini_array['remeha_param4']);
$remeha_param5 = hex2bin($ini_array['remeha_param5']);
$remeha_param6 = hex2bin($ini_array['remeha_param6']);
$remeha_param7 = hex2bin($ini_array['remeha_param7']);
$remeha_param8 = hex2bin($ini_array['remeha_param8']);

$fp = connect_to_esp($ESPIPAddress, $ESPPort, $retries, $newline);
if (!$fp) 
	{
	exit("Unable to establish connection to $ESPIPAddress:$ESPPort$newline");
	} 
else
	{
	// cls();
	stream_set_timeout($fp, 5);
	
	conditional_echo(str_repeat("=", 166) . "$newline", $echo_flag);
	conditional_echo("Connected to $ESPIPAddress:$ESPPort$newline", $echo_flag);
	conditional_echo("Sending request...$newline", $echo_flag);
	
	// ID Data Collection
	fwrite($fp,$remeha_id1, 10);
	$data_id1 = "";
	$data_id1 = bin2hex(fread($fp, 148));
	$data_id1U = strtoupper($data_id1);
	conditional_echo("ID-01 read: $data_id1U$newline", $echo_flag);
	usleep($nanosleeptime);

	fwrite($fp,$remeha_id2, 10);
	$data_id2 = "";
	$data_id2 = bin2hex(fread($fp, 52));
	$data_id2U = strtoupper($data_id2);
	conditional_echo("ID-02 read: $data_id2U$newline", $echo_flag);
	usleep($nanosleeptime);

	fwrite($fp,$remeha_id3, 10);
	$data_id3 = "";
	$data_id3 = bin2hex(fread($fp, 52));
	$data_id3U = strtoupper($data_id3);
	conditional_echo("ID-03 read: $data_id3U$newline", $echo_flag);
	usleep($nanosleeptime);

	// Parameter Data collection
	fwrite($fp,$remeha_param1, 10);
	$data_param1 = "";
	$data_param1 = bin2hex(fread($fp, 52));
	$data_param1U = strtoupper($data_param1);
	conditional_echo("Param-01 read: $data_param1U$newline", $echo_flag);
	usleep($nanosleeptime);

	fwrite($fp,$remeha_param2, 10);
	$data_param2="";
	$data_param2=bin2hex(fread($fp, 52));
	$data_param2U = strtoupper($data_param2);
	conditional_echo("Param-02 read: $data_param2U$newline", $echo_flag);
	usleep($nanosleeptime);

	fwrite($fp,$remeha_param3, 10);
	$data_param3="";
	$data_param3=bin2hex(fread($fp, 52));
	$data_param3U = strtoupper($data_param3);
	conditional_echo("Param-03 read: $data_param3U$newline", $echo_flag);
	usleep($nanosleeptime);

	fwrite($fp,$remeha_param4, 10);
	$data_param4="";
	$data_param4=bin2hex(fread($fp, 52));
	$data_param4U = strtoupper($data_param4);
	conditional_echo("Param-04 read: $data_param4U$newline", $echo_flag);
	usleep($nanosleeptime);

	fwrite($fp,$remeha_param5, 10);
	$data_param5="";
	$data_param5=bin2hex(fread($fp, 52));
	$data_param5U = strtoupper($data_param5);
	conditional_echo("Param-05 read: $data_param5U$newline", $echo_flag);
	usleep($nanosleeptime);

	fwrite($fp,$remeha_param6, 10);
	$data_param6="";
	$data_param6=bin2hex(fread($fp, 52));
	$data_param6U = strtoupper($data_param6);
	conditional_echo("Param-06 read: $data_param6U$newline", $echo_flag);
	usleep($nanosleeptime);
			
	fwrite($fp,$remeha_param7, 10);
	$data_param7="";
	$data_param7=bin2hex(fread($fp, 52));
	$data_param7U = strtoupper($data_param7);
	conditional_echo("Param-07 read: $data_param7U$newline", $echo_flag);
	usleep($nanosleeptime);

	fwrite($fp,$remeha_param8, 10);
	$data_param8="";
	$data_param8=bin2hex(fread($fp, 52));
	$data_param8U = strtoupper($data_param8);
	conditional_echo("Param-08 read: $data_param8U$newline", $echo_flag);
	fclose($fp);

	$output = param_data_dump($data_param1, $data_param2, $data_param3, $data_param4, $data_param5, $data_param6, $data_param7, $data_param8, $echo_flag, $newline);
	}

function param_data_dump($data_param1, $data_param2, $data_param3, $data_param4, $data_param5, $data_param6, $data_param7, $data_param8, $echo_flag, $newline)
{
// Manipulate data & Do a CRC Check	
	$decode_param1 = str_split($data_param1, 2);
	$hexstr_param1 = str_split($data_param1, 52);
	$hexstrPayload_param1 = substr($data_param1, 2, 44);
	$hexstrCRC_param1 = substr($data_param1, 46, 4);
	$crcCalc_param1 = crc16_modbus($hexstrPayload_param1);

	$decode_param2 = str_split($data_param2, 2);
	$hexstr_param2 = str_split($data_param2, 52);
	$hexstrPayload_param2 = substr($data_param2, 2, 44);
	$hexstrCRC_param2 = substr($data_param2, 46, 4);
	$crcCalc_param2 = crc16_modbus($hexstrPayload_param2);

	$decode_param3 = str_split($data_param3, 2);
	$hexstr_param3 = str_split($data_param3, 52);
	$hexstrPayload_param3 = substr($data_param3, 2, 44);
	$hexstrCRC_param3 = substr($data_param3, 46, 4);
	$crcCalc_param3 = crc16_modbus($hexstrPayload_param3);	

	$decode_param4 = str_split($data_param4, 2);
	$hexstr_param4 = str_split($data_param4, 52);
	$hexstrPayload_param4 = substr($data_param4, 2, 44);
	$hexstrCRC_param4 = substr($data_param4, 46, 4);
	$crcCalc_param4 = crc16_modbus($hexstrPayload_param4);	

	$decode_param5 = str_split($data_param5, 2);
	$hexstr_param5 = str_split($data_param5, 52);
	$hexstrPayload_param5 = substr($data_param5, 2, 44);
	$hexstrCRC_param5 = substr($data_param5, 46, 4);
	$crcCalc_param5 = crc16_modbus($hexstrPayload_param5);	

	$decode_param6 = str_split($data_param6, 2);
	$hexstr_param6 = str_split($data_param6, 52);
	$hexstrPayload_param6 = substr($data_param6, 2, 44);
	$hexstrCRC_param6 = substr($data_param6, 46, 4);
	$crcCalc_param6 = crc16_modbus($hexstrPayload_param6);	

	$decode_param7 = str_split($data_param7, 2);
	$hexstr_param7 = str_split($data_param7, 52);
	$hexstrPayload_param7 = substr($data_param7, 2, 44);
	$hexstrCRC_param7 = substr($data_param7, 46, 4);
	$crcCalc_param7 = crc16_modbus($hexstrPayload_param7);	

	$decode_param8 = str_split($data_param8, 2);
	$hexstr_param8 = str_split($data_param8, 52);
	$hexstrPayload_param8 = substr($data_param8, 2, 44);
	$hexstrCRC_param8 = substr($data_param8, 46, 4);
	$crcCalc_param8 = crc16_modbus($hexstrPayload_param8);	

	// Concatenate Parameter data to work with
	$concat_parameter = substr($hexstrPayload_param1, 12, 32).substr($hexstrPayload_param2, 12, 32).substr($hexstrPayload_param3, 12, 32).substr($hexstrPayload_param4, 12, 32).substr($hexstrPayload_param5, 12, 32).substr($hexstrPayload_param6, 12, 32).substr($hexstrPayload_param7, 12, 32).substr($hexstrPayload_param8, 12, 32);
	$decode_parameter = str_split($concat_parameter, 2);		

	// Write the contents to the file
	$ini_array = parse_ini_file("remeha.ini");
	$log_data = $ini_array['log_data'];
	$path = $ini_array['path_to_logs'];
	$filename = $ini_array['parameter_data_log'];
	$file = "$path$filename";
	date_default_timezone_set('Europe/Amsterdam');
	$date = date_create();
	$deg_symbol = "&degC";

	if (($hexstrCRC_param1 == $crcCalc_param1) && ($hexstrCRC_param2 == $crcCalc_param2) && ($hexstrCRC_param3 == $crcCalc_param3) && ($hexstrCRC_param4 == $crcCalc_param4) && ($hexstrCRC_param5 == $crcCalc_param5) && ($hexstrCRC_param6 == $crcCalc_param6) && ($hexstrCRC_param7 == $crcCalc_param7) && ($hexstrCRC_param8 == $crcCalc_param8))
		{
		conditional_echo("Data Integrity Good - CRCs Compute OK$newline", $echo_flag);
		if ($log_data == 2)
			{
			$datatowrite = date_format($date, 'Y-m-d H:i:s') . ' | 02 ' . $hexstrPayload_param1 . ' ' . $hexstrCRC_param1 . ' ' .'03 | ' . '02 ' . $hexstrPayload_param2 . ' ' . $hexstrCRC_param2 . ' ' .'03 | ' . '02 ' . $hexstrPayload_param3 . ' ' . $hexstrCRC_param3 . ' ' .'03 | ' . '02 ' . $hexstrPayload_param4 . ' ' . $hexstrCRC_param4 . ' ' .'03 | ' . '02 ' . $hexstrPayload_param5 . ' ' . $hexstrCRC_param5 . ' ' .'03 | ' . '02 ' . $hexstrPayload_param6 . ' ' . $hexstrCRC_param6 . ' ' .'03 | ' . '02 ' . $hexstrPayload_param7 . ' ' . $hexstrCRC_param7 . ' ' .'03 | ' . '02 ' . $hexstrPayload_param8 . ' ' . $hexstrCRC_param8 . ' ' .'03 | ' . "\n";
			file_put_contents($file, $datatowrite, FILE_APPEND);
			conditional_echo("Data written to log: $file$newline", $echo_flag);
			}
		conditional_echo(str_repeat("=", 166) . "$newline", $echo_flag);
		}
	else
		{
		if ($log_data == 1)
			{
			$datatowrite = '**** CRC Error **** | ' . date_format($date, 'Y-m-d H:i:s') . ' | 02 ' . $hexstrPayload_param1 . ' ' . $hexstrCRC_param1 . ' ' .'03 | ' . '02 ' . $hexstrPayload_param2 . ' ' . $hexstrCRC_param2 . ' ' .'03 | ' . '02 ' . $hexstrPayload_param3 . ' ' . $hexstrCRC_param3 . ' ' .'03 | ' . '02 ' . $hexstrPayload_param4 . ' ' . $hexstrCRC_param4 . ' ' .'03 | ' . '02 ' . $hexstrPayload_param5 . ' ' . $hexstrCRC_param5 . ' ' .'03 | ' . '02 ' . $hexstrPayload_param6 . ' ' . $hexstrCRC_param6 . ' ' .'03 | ' . '02 ' . $hexstrPayload_param7 . ' ' . $hexstrCRC_param7 . ' ' .'03 | ' . '02 ' . $hexstrPayload_param8 . ' ' . $hexstrCRC_param8 . ' ' .'03 | ' . "\n";
			file_put_contents($file, $datatowrite, FILE_APPEND);
			conditional_echo("Data written to log: $file$newline", $echo_flag);
			}
		conditional_echo("$newline", $echo_flag);
		conditional_echo("************** CRC ERROR!!!! ***********$newline", $echo_flag);
		return;		# Don't continue with updating Parameter data
		}

// Parameter Info
	$tflow_setpoint = hexdec($decode_parameter["0"]);
	if (($tflow_setpoint < 20) || ($tflow_setpoint > 90)) { $tflow_setpoint = "ERROR";}
	else { $tflow_setpoint = $tflow_setpoint;}
	
	$dhw_setpoint = hexdec($decode_parameter["1"]);
	if (($dhw_setpoint < 40) || ($dhw_setpoint > 90)) { $dhw_setpoint = "ERROR";}
	else { $dhw_setpoint = $dhw_setpoint;}
	
	$boiler_controls = hexdec($decode_parameter["2"]);
	if ($boiler_controls == 0) { $bolier_controls = "0:CH Off, DHW Off";}
	elseif ($boiler_controls == 1) { $bolier_controls = "1:CH On, DHW On";}
	elseif ($boiler_controls == 2) { $bolier_controls = "2:CH On, DHW Off";}
	elseif ($boiler_controls == 3) { $bolier_controls = "3:CH Off, DHW On";}

	$comfort_dhw = hexdec($decode_parameter["3"]);
	if ($comfort_dhw == 0) { $comfort_dhw = "0:Always On";}
	elseif ($comfort_dhw == 1) { $comfort_dhw = "1:Always Off";}
	elseif ($comfort_dhw == 2) { $comfort_dhw = "2:Controller";}

	$anticipation = hexdec($decode_parameter["4"]);
	if ($anticipation == 0) { $anticipation = "0:no";}
	elseif ($anticipation == 1) { $anticipation = "1:yes";}

	$displaymode = hexdec($decode_parameter["5"]);
	if ($displaymode == 0) { $displaymode = "0:Simple";}
	elseif ($displaymode == 1) { $displaymode = "1:Extended";}
	elseif ($displaymode == 2) { $displaymode = "2:Automatic";}
	elseif ($displaymode == 3) { $displaymode = "3:Automatic+Key Lock";}

	$pumppostrun_CH = hexdec($decode_parameter["6"]);
	if (($pumppostrun_CH < 0) || ($pumppostrun_CH > 99)) { $pumppostrun_CH = "ERROR";}
	else { $pumppostrun_CH = $pumppostrun_CH;}

	$displaybrightness = hexdec($decode_parameter["7"]);
	if ($displaybrightness == 0) { $displaybrightness = "0:Low";}
	elseif ($displaybrightness == 1) { $displaybrightness = "1:High";}

	$fullload_CH = hexdec($decode_parameter["16"])*100;
	if (($fullload_CH < 1000) || ($fullload_CH > 9000)) { $fullload_CH = "ERROR";}
	else { $fullload_CH = $fullload_CH;}
	
	$fullload_DHW = hexdec($decode_parameter["17"])*100;
	if (($fullload_DHW < 1000) || ($fullload_DHW > 9000)) { $fullload_DHW = "ERROR";}
	else { $fullload_DHW = $fullload_DHW;}

	$partload_CHDHW = hexdec($decode_parameter["18"])*100;
	if (($partload_CHDHW < 1000) || ($partload_CHDHW > 5000)) { $partload_CHDHW = "ERROR";}
	else { $partload_CHDHW = $partload_CHDHW;}
	
	$offset_partload = hexdec($decode_parameter["19"]);
	if (($offset_partload < 0) || ($offset_partload > 99)) { $offset_partload = "ERROR";}
	else { $offset_partload = $offset_partload;}

	$startload = hexdec($decode_parameter["20"])*100;
	if (($startload < 1000) || ($startload > 5000)) { $startload = "ERROR";}
	else { $startload = $startload;}

	$minwaterpressure = hexdec($decode_parameter["21"])*0.1;
	if (($minwaterpressure < 0) || ($minwaterpressure > 3)) { $minwaterpressure = "ERROR";}
	else { $minwaterpressure = $minwaterpressure;}

	$maxflowsystem = hexdec($decode_parameter["22"]);
	if (($maxflowsystem < 20) || ($maxflowsystem > 90)) { $maxflowsystem = "ERROR";}
	else { $maxflowsystem = $maxflowsystem ;}
	
	$footpointT_outside = hexdec($decode_parameter["24"]);
	if (($footpointT_outside < 0) || ($footpointT_outside > 30)) { $footpointT_outside = "ERROR";}
	else { $footpointT_outside = $footpointT_outside ;}

	$footpointT_flow = hexdec($decode_parameter["25"]);
	if (($footpointT_flow < 0) || ($footpointT_flow > 90)) { $footpointT_flow = "ERROR";}
	else { $footpointT_flow = $footpointT_flow ;}

	$climap_outsidetemp = hexdecs($decode_parameter["26"]);
	if (($climap_outsidetemp < -30) || ($climap_outsidetemp > 0)) { $climap_outsidetemp = "ERROR";}
	else { $climap_outsidetemp = $climap_outsidetemp ;}

	$pump_CH_min = hexdec($decode_parameter["27"])*10;
	if (($pump_CH_min < 20) || ($pump_CH_min > 100)) { $pump_CH_min = "ERROR";}
	else { $pump_CH_min = $pump_CH_min ;}

	$pump_CH_max = hexdec($decode_parameter["28"])*10;
	if (($pump_CH_max < 20) || ($pump_CH_max > 100)) { $pump_CH_max = "ERROR";}
	else { $pump_CH_max = $pump_CH_max ;}

	$temp_frostprotect = hexdecs($decode_parameter["29"]);
	if (($temp_frostprotect < -30) || ($temp_frostprotect > 0)) { $temp_frostprotect = "ERROR";}
	else { $temp_frostprotect = $temp_frostprotect ;}

	$anti_legionella = hexdec($decode_parameter["30"]);
	if ($anti_legionella == 0) { $anti_legionella = "0:no";}
	elseif ($anti_legionella == 1) { $anti_legionella = "1:yes";}
	elseif ($anti_legionella == 2) { $anti_legionella = "2:Controller";}
		
	$setpointraise_DHW = hexdec($decode_parameter["31"]);
	if (($setpointraise_DHW < 0) || ($setpointraise_DHW > 20)) { $setpointraise_DHW = "ERROR";}
	else { $setpointraise_DHW = $setpointraise_DHW ;}

	$hystereses_calorifier = hexdec($decode_parameter["32"]);
	if (($hystereses_calorifier < 2) || ($hystereses_calorifier > 15)) { $hystereses_calorifier = "ERROR";}
	else { $hystereses_calorifier = $hystereses_calorifier ;}

	$threewayvalve_standby = hexdec($decode_parameter["33"]);
	if ($threewayvalve_standby == 0) { $threewayvalve_standby = "0:CH";}
	elseif ($threewayvalve_standby == 1) { $threewayvalve_standby = "1:DHW";}

	$boiler_type = hexdec($decode_parameter["34"]);
	if ($boiler_type == 0) { $boiler_type = "0:Combi";}
	elseif ($boiler_type == 1) { $boiler_type = "1:Solo (+boiler)";}
	elseif ($boiler_type == 2) { $boiler_type = "2:Comfort Column";}

	$blocking_input = hexdec($decode_parameter["35"]);
	if ($blocking_input == 0) { $blocking_input = "0:CH enable";}
	elseif ($blocking_input == 1) { $blocking_input = "1:Blocking without Frost Protection";}
	elseif ($blocking_input == 2) { $blocking_input = "2:Blocking with Frost protection";}
	elseif ($blocking_input == 3) { $blocking_input = "3:Locking with Frost protection";}

	$release_input = hexdec($decode_parameter["36"]);
	if ($release_input == 0) { $release_input = "0:DHW enable";}
	elseif ($release_input == 1) { $release_input = "1:Burner Release";}
	
	$release_waittime = hexdec($decode_parameter["37"]);
	if (($release_waittime < 0) || ($release_waittime > 255)) { $release_waittime = "ERROR";}
	else { $release_waittime = $release_waittime ;}

	$fluegas_valvetime = hexdec($decode_parameter["38"]);
	if (($fluegas_valvetime < 0) || ($fluegas_valvetime > 255)) { $fluegas_valvetime = "ERROR";}
	else { $fluegas_valvetime = $fluegas_valvetime ;}

	$status_report = hexdec($decode_parameter["39"]);
	if ($status_report == 0) { $status_report = "0:Operation signal";}
	elseif ($status_report == 1) { $status_report = "1:Failure signal";}
	
	$min_gaspressure = hexdec($decode_parameter["40"]);
	if ($min_gaspressure == 0) { $min_gaspressure = "0:no";}
	elseif ($min_gaspressure == 1) { $min_gaspressure = "1:yes";}

	$HRUactive = hexdec($decode_parameter["41"]);
	if ($HRUactive == 0) { $HRUactive = "0:no";}
	elseif ($HRUactive == 1) { $HRUactive = "1:yes";}

	$mains_LN = hexdec($decode_parameter["42"]);
	if ($mains_LN == 0) { $mains_LN = "0:no";}
	elseif ($mains_LN == 1) { $mains_LN = "1:yes";}

	$service_notification = hexdec($decode_parameter["43"]);
	if ($service_notification == 0) { $service_notification = "0:Off";}
	elseif ($service_notification == 1) { $service_notification = "1:ABC";}
	elseif ($service_notification == 2) { $service_notification = "2:Custom";}
	
	$service_hours = hexdec($decode_parameter["44"])*100;
	if (($service_hours < 100) || ($service_hours > 25500)) { $service_hours = "ERROR";}
	else { $service_hours = $service_hours ;}

	$service_burning = hexdec($decode_parameter["45"])*100;
	if (($service_burning < 100) || ($service_burning > 25500)) { $service_burning = "ERROR";}
	else { $service_burning = $service_burning ;}

	$factor_avgflow = hexdec($decode_parameter["46"]);
	if (($factor_avgflow < 1) || ($factor_avgflow > 255)) { $factor_avgflow = "ERROR";}
	else { $factor_avgflow = $factor_avgflow ;}

	$DHW_in_gradient = hexdec($decode_parameter["58"])*0.01;
	if (($DHW_in_gradient < 0.01) || ($DHW_in_gradient > 2)) { $DHW_in_gradient = "ERROR";}
	else { $DHW_in_gradient = $DHW_in_gradient ;}

	$dt_pump_offset = hexdec($decode_parameter["59"]);
	if (($dt_pump_offset < 0) || ($dt_pump_offset > 100)) { $dt_pump_offset = "ERROR";}
	else { $dt_pump_offset = $dt_pump_offset ;}

	$offset_controltemperature = hexdecs($decode_parameter["60"])/10;
	if (($offset_controltemperature < -10) || ($offset_controltemperature > 10)) { $offset_controltemperature = "ERROR";}
	else { $offset_controltemperature = $offset_controltemperature ;}

	$dhw_flowatrpmmin = hexdec($decode_parameter["61"])*0.1;
	if (($dhw_flowatrpmmin < 0) || ($dhw_flowatrpmmin > 5)) { $dhw_flowatrpmmin = "ERROR";}
	else { $dhw_flowatrpmmin = $dhw_flowatrpmmin ;}

	$deairation_cycles = hexdec($decode_parameter["64"]);
	if (($deairation_cycles < 0) || ($deairation_cycles > 10)) { $deairation_cycles = "ERROR";}
	else { $deairation_cycles = $deairation_cycles ;}

	$gradient_dTMax_1 = hexdec($decode_parameter["65"])*0.01;
	if (($gradient_dTMax_1 < 0.01) || ($gradient_dTMax_1 > 2)) { $gradient_dTMax_1 = "ERROR";}
	else { $gradient_dTMax_1 = $gradient_dTMax_1 ;}

	$gradient_dTMax_2 = hexdec($decode_parameter["66"])*0.01;
	if (($gradient_dTMax_2 < 0.01) || ($gradient_dTMax_2 > 2)) { $gradient_dTMax_2 = "ERROR";}
	else { $gradient_dTMax_2 = $gradient_dTMax_2 ;}

	$gradient_dTMax_3 = hexdec($decode_parameter["67"])*0.01;
	if (($gradient_dTMax_3 < 0.01) || ($gradient_dTMax_3 > 2)) { $gradient_dTMax_3 = "ERROR";}
	else { $gradient_dTMax_3 = $gradient_dTMax_3 ;}

	$dT_flow_return = hexdec($decode_parameter["68"]);
	if (($dT_flow_return < 0) || ($dT_flow_return > 60)) { $dT_flow_return = "ERROR";}
	else { $dT_flow_return = $dT_flow_return ;}

	$startpoint_modul = hexdec($decode_parameter["69"]);
	if (($startpoint_modul < 10) || ($startpoint_modul > 40)) { $startpoint_modul = "ERROR";}
	else { $startpoint_modul = $startpoint_modul ;}

	$pump_dTset_CH = hexdec($decode_parameter["70"]);
	if (($pump_dTset_CH < 0) || ($pump_dTset_CH > 40)) { $pump_dTset_CH = "ERROR";}
	else { $pump_dTset_CH = $pump_dTset_CH ;}

	$pump_CH_start = hexdec($decode_parameter["71"]);
	if (($pump_CH_start < 0) || ($pump_CH_start > 100)) { $pump_CH_start = "ERROR";}
	else { $pump_CH_start = $pump_CH_start ;}

	$hysterese_CH = hexdec($decode_parameter["72"]);
	if (($hysterese_CH < 1) || ($hysterese_CH > 10)) { $hysterese_CH = "ERROR";}
	else { $hysterese_CH = $hysterese_CH ;}

	$stabilisation_time = hexdec($decode_parameter["73"]);
	if (($stabilisation_time < 10) || ($stabilisation_time > 180)) { $stabilisation_time = "ERROR";}
	else { $stabilisation_time = $stabilisation_time ;}

	$min_burner_off = hexdec($decode_parameter["74"]);
	if (($min_burner_off < 1) || ($min_burner_off > 15)) { $min_burner_off = "ERROR";}
	else { $min_burner_off = $min_burner_off ;}

	$max_burner_off = hexdec($decode_parameter["75"]);
	if (($max_burner_off < 3) || ($max_burner_off > 15)) { $max_burner_off = "ERROR";}
	else { $max_burner_off = $max_burner_off ;}

	$max_fanspeed_CH = hexdec($decode_parameter["76"])*100;
	if (($max_fanspeed_CH < 1000) || ($max_fanspeed_CH > 9000)) { $max_fanspeed_CH = "ERROR";}
	else { $max_fanspeed_CH = $max_fanspeed_CH ;}

	$max_fanspeed_DHW = hexdec($decode_parameter["77"])*100;
	if (($max_fanspeed_DHW < 1000) || ($max_fanspeed_DHW > 9000)) { $max_fanspeed_DHW = "ERROR";}
	else { $max_fanspeed_DHW = $max_fanspeed_DHW ;}

	$pump_dTset_DHW = hexdec($decode_parameter["78"]);
	if (($pump_dTset_DHW < 5) || ($pump_dTset_DHW > 40)) { $pump_dTset_DHW = "ERROR";}
	else { $pump_dTset_DHW = $pump_dTset_DHW ;}

	$pump_DHW_min = hexdec($decode_parameter["79"]);
	if (($pump_DHW_min < 0) || ($pump_DHW_min > 100)) { $pump_DHW_min = "ERROR";}
	else { $pump_DHW_min = $pump_DHW_min ;}

	$pump_DHW_max = hexdec($decode_parameter["80"]);
	if (($pump_DHW_max < 0) || ($pump_DHW_max > 100)) { $pump_DHW_max = "ERROR";}
	else { $pump_DHW_max = $pump_DHW_max ;}

	$pump_DHW_start = hexdec($decode_parameter["81"]);
	if (($pump_DHW_start < 0) || ($pump_DHW_start > 100)) { $pump_DHW_start = "ERROR";}
	else { $pump_DHW_start = $pump_DHW_start ;}

	$warmup_interval_CH = hexdec($decode_parameter["82"]);
	if (($warmup_interval_CH < 0) || ($warmup_interval_CH > 255)) { $warmup_interval_CH = "ERROR";}
	else { $warmup_interval_CH = $warmup_interval_CH ;}

	$warmup_interval = hexdec($decode_parameter["83"]);
	if (($warmup_interval < 0) || ($warmup_interval > 255)) { $warmup_interval = "ERROR";}
	else { $warmup_interval = $warmup_interval ;}

	$hysterese_warming_up = hexdec($decode_parameter["84"]);
	if (($hysterese_warming_up < 0) || ($hysterese_warming_up > 20)) { $hysterese_warming_up = "ERROR";}
	else { $hysterese_warming_up = $hysterese_warming_up ;}

	$offset_warming_up = hexdecs($decode_parameter["85"]);
	if (($offset_warming_up < -30) || ($offset_warming_up > 20)) { $$offset_warming_up = "ERROR";}
	else { $offset_warming_up = $offset_warming_up ;}

	$DHW_start_raise = hexdec($decode_parameter["86"]);
	if (($DHW_start_raise < 0) || ($DHW_start_raise > 30)) { $DHW_start_raise = "ERROR";}
	else { $DHW_start_raise = $DHW_start_raise ;}

	$hysterese_DHW = hexdec($decode_parameter["87"]);
	if (($hysterese_DHW < 1) || ($hysterese_DHW > 10)) { $hysterese_DHW = "ERROR";}
	else { $hysterese_DHW = $hysterese_DHW ;}

	$offset_DHW = hexdec($decode_parameter["88"]);
	if (($offset_DHW < 0) || ($offset_DHW > 20)) { $offset_DHW = "ERROR";}
	else { $offset_DHW = $offset_DHW ;}

	$offsett_p1_heatexchg = hexdec($decode_parameter["89"]);
	if (($offsett_p1_heatexchg < 0) || ($offsett_p1_heatexchg > 20)) { $offsett_p1_heatexchg = "ERROR";}
	else { $offsett_p1_heatexchg = $offsett_p1_heatexchg ;}

	$Tf_Tr_DHW_pump_20 = hexdec($decode_parameter["90"]);
	if (($Tf_Tr_DHW_pump_20 < 0) || ($Tf_Tr_DHW_pump_20 > 100)) { $Tf_Tr_DHW_pump_20 = "ERROR";}
	else { $Tf_Tr_DHW_pump_20 = $Tf_Tr_DHW_pump_20 ;}

	$Tf_Tr_DHW_pump_100 = hexdec($decode_parameter["91"]);
	if (($Tf_Tr_DHW_pump_100 < 0) || ($Tf_Tr_DHW_pump_100 > 100)) { $Tf_Tr_DHW_pump_100 = "ERROR";}
	else { $Tf_Tr_DHW_pump_100 = $Tf_Tr_DHW_pump_100 ;}

	$delay_pump_DHW = hexdec($decode_parameter["92"])/10;
	if (($delay_pump_DHW < 0) || ($delay_pump_DHW > 10)) { $delay_pump_DHW = "ERROR";}
	else { $delay_pump_DHW = $delay_pump_DHW ;}

	$post_pump_time_DHW = hexdec($decode_parameter["93"]);
	if (($post_pump_time_DHW < 1) || ($post_pump_time_DHW > 99)) { $post_pump_time_DHW = "ERROR";}
	else { $post_pump_time_DHW = $post_pump_time_DHW ;}

	$k_factor_DHW = hexdec($decode_parameter["94"]);
	if (($k_factor_DHW < 1) || ($k_factor_DHW > 255)) { $k_factor_DHW = "ERROR";}
	else { $k_factor_DHW = $k_factor_DHW ;}

	$min_DHW_flow = hexdec($decode_parameter["95"])*0.1;
	if (($min_DHW_flow < 0.1) || ($min_DHW_flow > 1)) { $min_DHW_flow = "ERROR";}
	else { $min_DHW_flow = $min_DHW_flow ;}

	$DHW_sensor = hexdec($decode_parameter["96"]);
	if ($DHW_sensor == 0) { $DHW_sensor = "0:DHW Flow Sensor";}
	elseif ($DHW_sensor == 1) { $DHW_sensor = "1:DHW Flow Switch";}

	$flow_detection_time = hexdec($decode_parameter["97"]);
	if (($flow_detection_time < 0) || ($flow_detection_time > 255)) { $flow_detection_time = "ERROR";}
	else { $flow_detection_time = $flow_detection_time ;}

	$DHW_stabilisation = hexdec($decode_parameter["98"])/10;
	if (($DHW_stabilisation < 0) || ($DHW_stabilisation > 25.5)) { $DHW_stabilisation = "ERROR";}
	else { $DHW_stabilisation = $DHW_stabilisation ;}

	$DHW_gradient = hexdec($decode_parameter["99"])*0.01;
	if (($DHW_gradient < 0.01) || ($DHW_gradient > 2)) { $DHW_gradient = "ERROR";}
	else { $DHW_gradient = $DHW_gradient ;}

	$DHW_booster_off = hexdec($decode_parameter["100"]);
	if (($DHW_booster_off < 0) || ($DHW_booster_off > 99)) { $DHW_booster_off = "ERROR";}
	else { $DHW_booster_off = $DHW_booster_off ;}

	$P_DHW_start = hexdec($decode_parameter["101"]);
	if (($P_DHW_start < 0) || ($P_DHW_start > 200)) { $P_DHW_start = "ERROR";}
	else { $P_DHW_start = $P_DHW_start ;}

	$P_DHW_ffwd = hexdec($decode_parameter["102"]);
	if (($P_DHW_ffwd < 0) || ($P_DHW_ffwd > 200)) { $P_DHW_ffwd = "ERROR";}
	else { $P_DHW_ffwd = $P_DHW_ffwd ;}

	$P_DHW_flowchanges = hexdec($decode_parameter["103"]);
	if (($P_DHW_flowchanges < 0) || ($P_DHW_flowchanges > 200)) { $P_DHW_flowchanges = "ERROR";}
	else { $P_DHW_flowchanges = $P_DHW_flowchanges ;}

	$offset_calorifier = hexdec($decode_parameter["104"]);
	if (($offset_calorifier < 0) || ($offset_calorifier > 10)) { $offset_calorifier = "ERROR";}
	else { $offset_calorifier = $offset_calorifier ;}

	$start_DHW_pump = hexdecs($decode_parameter["105"]);
	if (($start_DHW_pump < -20) || ($start_DHW_pump > 20)) { $start_DHW_pump = "ERROR";}
	else { $start_DHW_pump = $start_DHW_pump ;}

	$post_pump_time_DHW1 = hexdec($decode_parameter["106"]);
	if (($post_pump_time_DHW1 < 1) || ($post_pump_time_DHW1 > 255)) { $post_pump_time_DHW1 = "ERROR";}
	else { $post_pump_time_DHW1 = $post_pump_time_DHW1 ;}

	$prepurge_time = hexdec($decode_parameter["107"]);
	if (($prepurge_time < 0) || ($prepurge_time > 255)) { $prepurge_time = "ERROR";}
	else { $prepurge_time = $prepurge_time ;}

	$postpurge_time = hexdec($decode_parameter["108"]);
	if (($postpurge_time < 0) || ($postpurge_time > 255)) { $postpurge_time = "ERROR";}
	else { $postpurge_time = $postpurge_time ;}

	$max_flowtemp = hexdec($decode_parameter["109"]);
	if (($max_flowtemp < 0) || ($max_flowtemp > 110)) { $max_flowtemp = "ERROR";}
	else { $max_flowtemp = $max_flowtemp ;}

	$pulses_fan = hexdec($decode_parameter["110"]);
	if (($pulses_fan < 1) || ($pulses_fan > 10)) { $pulses_fan = "ERROR";}
	else { $pulses_fan = $pulses_fan ;}

	$p_factor_fan = hexdec($decode_parameter["111"]);
	if (($p_factor_fan < 0) || ($p_factor_fan > 100)) { $p_factor_fan = "ERROR";}
	else { $p_factor_fan = $p_factor_fan ;}

	$i_factor_fan = hexdec($decode_parameter["112"]);
	if (($i_factor_fan < 1) || ($i_factor_fan > 200)) { $i_factor_fan = "ERROR";}
	else { $i_factor_fan = $i_factor_fan ;}

	$p_factor_CH = hexdec($decode_parameter["113"]);
	if (($p_factor_CH < 0) || ($p_factor_CH > 100)) { $p_factor_CH = "ERROR";}
	else { $p_factor_CH = $p_factor_CH ;}

	$i_factor_CH = hexdec($decode_parameter["114"]);
	if (($i_factor_CH < 0) || ($i_factor_CH > 200)) { $i_factor_CH = "ERROR";}
	else { $i_factor_CH = $i_factor_CH ;}

	$p_factor_CH_down = hexdec($decode_parameter["115"]);
	if (($p_factor_CH_down < 0) || ($p_factor_CH_down > 100)) { $p_factor_CH_down = "ERROR";}
	else { $p_factor_CH_down = $p_factor_CH_down ;}

	$i_factor_CH_down = hexdec($decode_parameter["116"]);
	if (($i_factor_CH_down < 0) || ($i_factor_CH_down > 200)) { $i_factor_CH_down = "ERROR";}
	else { $i_factor_CH_down = $i_factor_CH_down ;}

	$p_factor_DHW = hexdec($decode_parameter["117"]);
	if (($p_factor_DHW < 0) || ($p_factor_DHW > 100)) { $p_factor_DHW = "ERROR";}
	else { $p_factor_DHW = $p_factor_DHW ;}

	$i_factor_DHW = hexdec($decode_parameter["118"]);
	if (($i_factor_DHW < 0) || ($i_factor_DHW > 200)) { $i_factor_DHW = "ERROR";}
	else { $i_factor_DHW = $i_factor_DHW ;}

	$i_factor_pump_CH = hexdec($decode_parameter["119"]);
	if (($i_factor_pump_CH < 0) || ($i_factor_pump_CH > 200)) { $i_factor_pump_CH = "ERROR";}
	else { $i_factor_pump_CH = $i_factor_pump_CH ;}

	$i_factor_pump_DHW = hexdec($decode_parameter["120"]);
	if (($i_factor_pump_DHW < 0) || ($i_factor_pump_DHW > 200)) { $i_factor_pump_DHW = "ERROR";}
	else { $i_factor_pump_DHW = $i_factor_pump_DHW ;}

	$rpm_at_0KW = hexdec($decode_parameter["121"]);
	if (($rpm_at_0KW < 0) || ($rpm_at_0KW > 255)) { $rpm_at_0KW = "ERROR";}
	else { $rpm_at_0KW = $rpm_at_0KW ;}

	$KW_at_10000rpm = hexdec($decode_parameter["122"]);
	if (($KW_at_10000rpm < 0) || ($KW_at_10000rpm > 255)) { $KW_at_10000rpm = "ERROR";}
	else { $KW_at_10000rpm = $KW_at_10000rpm ;}

	$display_LED_red = hexdec($decode_parameter["123"]);
	if (($display_LED_red < 0) || ($display_LED_red > 255)) { $display_LED_red = "ERROR";}
	else { $display_LED_red = $display_LED_red ;}

	$display_LED_green = hexdec($decode_parameter["125"]);
	if (($display_LED_green < 0) || ($display_LED_green > 255)) { $display_LED_green = "ERROR";}
	else { $display_LED_green = $display_LED_green ;}

	$display_LED_blue = hexdec($decode_parameter["124"]);
	if (($display_LED_blue < 0) || ($display_LED_blue > 255)) { $display_LED_blue = "ERROR";}
	else { $display_LED_blue = $display_LED_blue ;}
// END Parameters Info

// START Display Parameters
	echo "Parameters Received: " . date_format($date, 'Y-m-d H:i:s') . "$newline";
	echo str_repeat("=", 80) . "$newline";
	echo "Max. Flow Temp. during CH mode: $tflow_setpoint$deg_symbol$newline";
	echo "Desired DHW temperature: $dhw_setpoint$deg_symbol$newline";
	echo "Switch On/Off CH/DHW Functions: $boiler_controls$newline";
	echo "DHW Comfort Setting: $comfort_dhw$newline";
	echo "Anticipation curent On/Off Thermostat: $anticipation$newline";
	echo "Amount of Information to Display: $displaymode$newline";
	echo "Pump post run time: $pumppostrun_CH minutes$newline";
	echo "Display Brightness: $displaybrightness$newline";
	echo "Max Fanspeed during CH mode: $fullload_CH rpm$newline";
	echo "Max Fanspeed during DHW mode: $fullload_DHW rpm$newline";
	echo "Max Fanspeed during CH+DHW mode: $partload_CHDHW rpm$newline";
	echo "Offset on part load fanspeed: $offset_partload rpm$newline";
	echo "Fanspeed at boiler start: $startload rpm$newline";
	echo "Min. water pressure for notification: $minwaterpressure bar$newline";
	echo "Max Flow temperature for CH: $maxflowsystem$deg_symbol$newline"; 
	echo "Footpoint heating curve outside temperature: $footpointT_outside$deg_symbol$newline";
	echo "Footpoint heating curve flow temperature: $footpointT_flow$deg_symbol$newline";
	echo "Clima P outside temperature: $climap_outsidetemp$deg_symbol$newline";
	echo "Pump Control - CH minimum speed: $pump_CH_min %$newline";
	echo "Pump Control - CH maximum speed: $pump_CH_max %$newline"; 
	echo "Minimum outside temperature for Frost Protection: $temp_frostprotect$deg_symbol$newline";
	echo "Anti-Legionella setting: $anti_legionella $newline";
	echo "Setpoint raise at warming up of calorifier: $setpointraise_DHW$deg_symbol$newline";
	echo "Switch on hystereses calorifier sensor: $hystereses_calorifier$deg_symbol$newline";
	echo "Three-way valve standby position: $threewayvalve_standby $newline";
	echo "Boiler Type: $boiler_type $newline";
	echo "Blocking Input: $blocking_input $newline";
	echo "Release Input: $release_input$newline";
	echo "Release Wait time: $release_waittime seconds$newline";
	echo "Fluegas valve Wait time: $fluegas_valvetime seconds$newline";
	echo "Status Report: $status_report$newline";
	echo "Minimum Gas Pressure Detection: $min_gaspressure$newline";
	echo "HRU Connected: $HRUactive$newline";
	echo "Mains Live-Neutral phase Detection: $mains_LN$newline";
	echo "Service Notification for Boiler Dependent Maintenance: $service_notification$newline";
	echo "Service Hours for Boiler connected to mains: $service_hours$newline";
	echo "Service Hours for Boiler Burner: $service_burning$newline";
	echo "Tau Factor for average flow temperature calculation: $factor_avgflow seconds$newline";
	echo "DHW-in gradient for restart stabilisation time: $DHW_in_gradient$deg_symbol/second$newline";
	echo "dT pump offset: $dt_pump_offset$deg_symbol$newline";
	echo "Offset Control temperature: $offset_controltemperature$deg_symbol$newline"; 
	echo "DHW Flow at minimum output power: $dhw_flowatrpmmin litres/minute$newline";
	echo "Number of de-airation cycles on startup: $deairation_cycles $newline";
	echo "Maximum gradient for decreasing modulation: $gradient_dTMax_1 $newline";
	echo "Maximum gradient for forced minimal load: $gradient_dTMax_2 $newline";
	echo "Maximum gradient for blocking: $gradient_dTMax_3 $newline";
	echo "Maximum temp difference between flow and return: $dT_flow_return$deg_symbol$newline";
	echo "Modulate back when dT > this parameter: $startpoint_modul$deg_symbol $newline";
	echo "Pump control, control range dT for CH: $pump_dTset_CH$deg_symbol $newline";
	echo "Pump control, CH on start heatdemand: $pump_CH_start %$newline";
	echo "Start hysteresis for CH: $hysterese_CH$deg_symbol $newline";
	echo "Stabilization time after burner start CH: $stabilisation_time seconds$newline";
	echo "Minimum burner anti-cycle time: $min_burner_off minutes$newline";
	echo "Maximum burner anti-cycle time: $max_burner_off minutes$newline";
	echo "Absolute max fan speed CH: $max_fanspeed_CH rpm$newline";
	echo "Absolute max fan speed DHW: $max_fanspeed_DHW rpm$newline";
	echo "Pump control, control range dT for DHW: $pump_dTset_DHW$deg_symbol $newline";
	echo "Pump control, DHW minimum speed: $pump_DHW_min %$newline";
	echo "Pump control, DHW maximum speed: $pump_DHW_max %$newline";
	echo "Pump control, DHW on start DHW demand: $pump_DHW_start %$newline";
	echo "Warm up interval for DHW after CH: $warmup_interval_CH minutes$newline";
	echo "Time between warming up starts boiler: $warmup_interval minutes$newline";
	echo "Hysterese when warming up for DHW comfort: $hysterese_warming_up$deg_symbol $newline";
	echo "Offset when warming up for DHW comfort: $offset_warming_up$deg_symbol $newline";
	echo "DHW start raise depending op DHW flow: $DHW_start_raise $newline";
	echo "Switch on hystereses DHW operation: $hysterese_DHW$deg_symbol $newline";
	echo "Offset DHW: $offset_DHW$deg_symbol $newline";
	echo "Temperature correction DHW for Tset, ww - Tret, plate heat exchanger: $offsett_p1_heatexchg$deg_symbol $newline";
	echo "%Tf/Tr for DHW control temperature at pumpspeed 20%: $Tf_Tr_DHW_pump_20 %$newline";
	echo "%Tf/Tr for DHW control temperature at pumpspeed 100%: $Tf_Tr_DHW_pump_100 %$newline";
	echo "Waiting time pump for preheat plate heat exchanger: $delay_pump_DHW seconds$newline";
	echo "Postpump time DHW: $post_pump_time_DHW seconds$newline";
	echo "Correction factor DHW pulses to litres/minute: $k_factor_DHW $newline";
	echo "Minimum DHW flow for DHW detection: $min_DHW_flow litres/minute$newline";
	echo "Type of Sensor for DHW detection: $DHW_sensor $newline";
	echo "Factor for dynamic flow detection: $flow_detection_time $newline";
	echo "DHW stabilisation time for pump modulation: $DHW_stabilisation seconds$newline";
	echo "DHW gradient for stabilisation time pump: $DHW_gradient$deg_symbol/second $newline";
	echo "Range in which the DHW booster is diabled: $DHW_booster_off$deg_symbol $newline";
	echo "P factor for booster on start DHW: $P_DHW_start $newline";
	echo "P factor for feedforward on flow DHW: $P_DHW_ffwd $newline";
	echo "P factor for booster on flowchanges DHW: $P_DHW_flowchanges $newline";
	echo "Switch off offset calorifier sensor: $offset_calorifier$deg_symbol $newline";
	echo "Switch on delay DHW pump in comparison with boiler pump: $start_DHW_pump$deg_symbol $newline";
	echo "Post pump time DHW: $post_pump_time_DHW1 seconds$newline";
	echo "Prepurge time for burner start: $prepurge_time seconds$newline";
	echo "Postpurge time for burner stop: $postpurge_time seconds$newline";
	echo "Maximum flow temperature for blocking: $max_flowtemp$deg_symbol $newline";
	echo "Tacho pulses per revolution of the fan: $pulses_fan /second$newline";
	echo "P factor fan speed contro: $p_factor_fan $newline";
	echo "I factor fan speed contro: $i_factor_fan $newline";
	echo "P factor CH control: $p_factor_CH $newline";
	echo "I factor CH control: $i_factor_CH $newline";
	echo "P factor for CH control when T1>setpoint: $p_factor_CH_down $newline";
	echo "I factor for CH control when T1>setpoint: $i_factor_CH_down $newline";
	echo "P factor DHW control: $p_factor_DHW $newline";
	echo "I factor DHW control: $i_factor_DHW $newline";
	echo "I factor for pump control on CH: $i_factor_pump_CH $newline";
	echo "I factor for pump control on DHW: $i_factor_pump_DHW $newline";
	echo "RPM at theoretical 0KW: $rpm_at_0KW $newline";
	echo "Power output (KW) at theoretical 10,000rpm: $KW_at_10000rpm KW$newline";
	echo "PWM value red for normal display backlight: $display_LED_red $newline";
	echo "PWM value green for normal display backlight: $display_LED_green $newline";
	echo "PWM value blue for normal display backlight: $display_LED_blue $newline";
	echo str_repeat("=", 80) . "$newline";
// END Display Parameters
}
?>
