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
require('remeha_functions.php');

// remeha.ini file Variables
//
$ini_array = parse_ini_file("remeha.ini");
$ESPIPAddress = $ini_array['ESPIPAddress'];
$ESPPort = $ini_array['ESPPort'];
$retries = $ini_array['retries'];
$nanosleeptime =  $ini_array['nanosleeptime'];
$echo_flag = "1";
$newline = "<br />";
$phpver = phpversion();

$remeha_id1 = hex2bin($ini_array['remeha_id1']);
$remeha_id2 = hex2bin($ini_array['remeha_id2']);
$remeha_id3 = hex2bin($ini_array['remeha_id3']);

$fp = connect_to_esp($ESPIPAddress, $ESPPort, $retries, $newline);
if (!$fp) 
	{
	exit("Unable to establish connection to $ESPIPAddress:$ESPPort$newline");
	} 
else
	{
	stream_set_timeout($fp, 5);
	conditional_echo(str_repeat("=", 166) . "$newline", $echo_flag);
	conditional_echo("PHP version: $phpver$newline", $echo_flag);	
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
	fclose($fp);

	$output = identification_data_dump($data_id1, $data_id2, $data_id3, $echo_flag, $newline);
	}

function  identification_data_dump($data_id1, $data_id2, $data_id3, $echo_flag, $newline)
{
	// Manipulate data & Do a CRC Check	
	$decode_id1 = str_split($data_id1, 2);
	$hexstr_id1 = str_split($data_id1, 148);
	$hexstrPayload_id1 = substr($data_id1, 2, 140);
	$hexstrCRC_id1 = substr($data_id1, 142, 4);
	$crcCalc_id1 = crc16_modbus($hexstrPayload_id1);

	$decode_id2 = str_split($data_id2, 2);
	$hexstr_id2 = str_split($data_id2, 52);
	$hexstrPayload_id2 = substr($data_id2, 2, 44);
	$hexstrCRC_id2 = substr($data_id2, 46, 4);
	$crcCalc_id2 = crc16_modbus($hexstrPayload_id2);

	$decode_id3 = str_split($data_id3, 2);
	$hexstr_id3 = str_split($data_id3, 52);
	$hexstrPayload_id3 = substr($data_id3, 2, 44);
	$hexstrCRC_id3 = substr($data_id3, 46, 4);
	$crcCalc_id3 = crc16_modbus($hexstrPayload_id3);

	// Concatenate identification data to work with
	$concat_identification = substr($hexstrPayload_id1, 12, 128).substr($hexstrPayload_id2, 12, 32).substr($hexstrPayload_id3, 12, 32);
	echo "Identification used: 02".strtoupper($hexstrPayload_id1.substr($hexstrPayload_id2, 12, 32).substr($hexstrPayload_id3, 12, 32)).$newline;
	$decode_identification = str_split($concat_identification, 2);		

	// Write the contents to the file
	$ini_array = parse_ini_file("remeha.ini");
	$log_data = $ini_array['log_data'];
	$path = $ini_array['path_to_logs'];
	$filename = $ini_array['identification_data_log'];
	$file = "$path$filename";
	date_default_timezone_set('Europe/Amsterdam');
	$date = date_create();
	$deg_symbol = "&degC";

	if (($hexstrCRC_id1 == $crcCalc_id1) && ($hexstrCRC_id2 == $crcCalc_id2) && ($hexstrCRC_id3 == $crcCalc_id3))
		{
		conditional_echo("Data Integrity Good - CRCs Compute OK$newline", $echo_flag);
		if ($log_data == 2)
			{
			$datatowrite = date_format($date, 'Y-m-d H:i:s') . ' | 02 ' . $hexstrPayload_id1 . ' ' . $hexstrCRC_id1 . ' ' .'03 | ' . '02 ' . $hexstrPayload_id2 . ' ' . $hexstrCRC_id2 . ' ' .'03 | ' . '02 ' . $hexstrPayload_id3 . ' ' . $hexstrCRC_id3 . ' ' .'03 | ' . "\n";
			file_put_contents($file, $datatowrite, FILE_APPEND);
			conditional_echo("Data written to log: $file$newline", $echo_flag);
			}
		conditional_echo(str_repeat("=", 166) . "$newline", $echo_flag);
		}
	else
		{
		if (($log_data == 1) || ($log_data == 2))
			{
			$datatowrite = '**** CRC Error **** | ' . date_format($date, 'Y-m-d H:i:s') . ' | 02 ' . $hexstrPayload_id1 . ' ' . $hexstrCRC_id1 . ' ' .'03 | ' . '02 ' . $hexstrPayload_id2 . ' ' . $hexstrCRC_id2 . ' ' .'03 | ' . '02 ' . $hexstrPayload_id3 . ' ' . $hexstrCRC_id3 . ' ' .'03 | ' . "\n";
			file_put_contents($file, $datatowrite, FILE_APPEND);
			conditional_echo("Data written to log: $file$newline", $echo_flag);
			}
		conditional_echo("$newline", $echo_flag);
		conditional_echo("************** CRC ERROR!!!! ***********$newline", $echo_flag);
		return;		# Don't continue with updating identification data
		}

	// identification Info

	$df_code = hexdec($decode_identification["1"]);
	$du_code = hexdec($decode_identification["2"]);
	$sw_version = $decode_identification["5"]/10;
	$param_version = $decode_identification["6"]/10;
	$param_type = hexdec($decode_identification["7"]);
	$next_service_type = hexdec($decode_identification["10"]);
	if ($next_service_type == 0) {$next_service_type = "A";}
	elseif ($next_service_type == 1) {$next_service_type = "b";}
	elseif ($next_service_type == 2) {$next_service_type = 'A';}
	elseif ($next_service_type == 3) {$next_service_type = 'C';}
	$boiler_serial = hex2str($decode_identification["32"].$decode_identification["33"].$decode_identification["34"].$decode_identification["35"].$decode_identification["36"].$decode_identification["37"].$decode_identification["38"].$decode_identification["39"].$decode_identification["40"].$decode_identification["41"].$decode_identification["42"].$decode_identification["43"].$decode_identification["44"].$decode_identification["45"].$decode_identification["46"].$decode_identification["47"]);
	$boiler_name = hex2str($decode_identification["48"].$decode_identification["49"].$decode_identification["50"].$decode_identification["51"].$decode_identification["52"].$decode_identification["53"].$decode_identification["54"].$decode_identification["55"].$decode_identification["56"].$decode_identification["57"].$decode_identification["58"].$decode_identification["59"].$decode_identification["60"].$decode_identification["61"].$decode_identification["62"].$decode_identification["63"]);

	$pcu_device_type = hexdec($decode_identification["64"]);
	$pcu_sw_version = $decode_identification["65"]/10;
	$pcu_param_version = $decode_identification["66"]/10;
	$pcu_param_type = hexdec($decode_identification["67"]);
	$pcu_operating_hours = hexdec($decode_identification["68"].$decode_identification["69"])*2;
	$pcu_su_type = hexdec($decode_identification["70"]);
	$pcu_psu_type = hexdec($decode_identification["71"]);
	$pcu_last_blocking_code = hexdec($decode_identification["72"]);
	$pcu_last_locking_code = hexdec($decode_identification["73"]);
	$pcu_operating_voltage = hexdec($decode_identification["74"]);
	$pcu_serial = $decode_identification["75"].$decode_identification["76"].hex2str($decode_identification["77"]).$decode_identification["78"].$decode_identification["79"];

	$su_device_type = hexdec($decode_identification["80"]);
	$su_sw_version = $decode_identification["81"]/10;
	$su_param_version = $decode_identification["82"]/10;
	$su_param_type = hexdec($decode_identification["83"]);
	$su_operating_hours = hexdec($decode_identification["84"].$decode_identification["85"])*8;
	$su_pcu_type = hexdec($decode_identification["86"]);
	$su_psu_type = hexdec($decode_identification["87"]);
	$su_last_blocking_code = hexdec($decode_identification["88"]);
	$su_last_locking_code = hexdec($decode_identification["89"]);
	$su_last_internal_error = hexdec($decode_identification["90"]);
	$su_serial = $decode_identification["91"].$decode_identification["92"].hex2str($decode_identification["93"]).$decode_identification["94"].$decode_identification["95"];
	
	// END identifications Info

	// START Display identifications
	echo "Identifications Received: " . date_format($date, 'Y-m-d H:i:s') . "$newline";
	echo str_repeat("=", 80) . "$newline";
	echo "Boiler Name: $boiler_name$newline";
	echo "dF-Code: $df_code$newline";
	echo "dU-Code: $du_code$newline";
	echo "Boiler Serial Number: $boiler_serial$newline";
	echo "Software Version: $sw_version$newline";
	echo "Parameter Version: $param_version$newline";
	echo "Parameter Type: $param_type$newline";
	echo "Next Service Type: $next_service_type$newline";

	echo $newline;
	echo "PCU Device Type: $pcu_device_type$newline";
	echo "PCU Serial Number: $pcu_serial$newline";
	echo "PCU Software Version: $pcu_sw_version$newline";
	echo "PCU Parameter Version: $pcu_param_version$newline";
	echo "PCU Parameter Type: $pcu_param_type$newline";
	echo "PCU Operating Hours: $pcu_operating_hours$newline";
	echo "PCU Connected SU Type: $pcu_su_type$newline";
	echo "PCU Connected PSU Type: $pcu_psu_type$newline";
	echo "PCU Last Blocking Code: $pcu_last_blocking_code$newline";
	echo "PCU Last Locking Code: $pcu_last_locking_code$newline";
	echo "Operating Voltage: $pcu_operating_voltage"." volts".$newline;

	echo $newline;
	echo "SU Device Type: $su_device_type$newline";
	echo "SU Serial Number: $su_serial$newline";
	echo "SU Software Version: $su_sw_version$newline";
	echo "SU Parameter Version: $su_param_version$newline";
	echo "SU Parameter Type: $su_param_type$newline";
	echo "SU Operating Hours: $su_operating_hours$newline";
	echo "SU Connected PCU Type: $su_pcu_type$newline";
	echo "SU Connected PSU Type: $su_psu_type$newline";
	echo "SU Last Blocking Code: $su_last_blocking_code$newline";
	echo "SU Last Locking Code: $su_last_locking_code$newline";
	echo "SU Last Internal Error: $su_last_internal_error$newline";
	echo str_repeat("=", 80) . "$newline";
	// END Display identifications
}

?>
