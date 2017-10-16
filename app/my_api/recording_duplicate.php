<?php
/*
    FusionPBX
    Version: MPL 1.1

    The contents of this file are subject to the Mozilla Public License Version
    1.1 (the "License"); you may not use this file except in compliance with
    the License. You may obtain a copy of the License at
    http://www.mozilla.org/MPL/

    Software distributed under the License is distributed on an "AS IS" basis,
    WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
    for the specific language governing rights and limitations under the
    License.

    The Original Code is FusionPBX

    The Initial Developer of the Original Code is
    Mark J Crane <markjcrane@fusionpbx.com>
    Portions created by the Initial Developer are Copyright (C) 2008-2016
    the Initial Developer. All Rights Reserved.

    Contributor(s):
    Mark J Crane <markjcrane@fusionpbx.com>
    Igor Olhovskiy <igorolhovskiy@gmail.com>

*/
require_once "root.php";
require_once "resources/require.php";
require_once "resources/check_auth.php";

include "resources/classes/functions.php";

// my_api_data - table
// @uuid - just random uuid
// @domain_uuid - data specific for this domain
// @api_name - name of api
// @json - json data

// Moving path allows following templates
// [DOMAIN] - Actual domain name
// [YEAR] - Year of recording
// [MONTH] - Month of recording
// [DAY] - Day of recording (number)
// [TIME] - Time of recording
// [CLID_NAME] - CallerID name of source
// [CLID_NUMBER] - CallerID number of source
// [DURATION] - Duration of recording
// [DOMAIN] - Domain name
// [DAY_N] - Day Name, ex Mon, Tue...
// [DIRECTION] - Direction of the call (local, inbound, outbound)
// [DESTINATION] - Destination of the call
// [EXT] - Extension. Actually, mandatory

function prepare_filepath($timestamp, $path, $callerid_name, $callerid_number, $duration, $domain, $direction, $destination) {
    $tmp_year = date("Y", $timestamp);
    $tmp_month = date("M", $timestamp);
    $tmp_day = date("d", $timestamp);
    $tmp_time = date("H-i-s", $timestamp);
    $tmp_day_n = date("D", $timestamp);
    $result = $path;
    $result = str_replace('[YEAR]', $tmp_year, $result);
    $result = str_replace('[MONTH]', $tmp_month, $result);
    $result = str_replace('[DAY]', $tmp_day, $result);
    $result = str_replace('[TIME]', $tmp_time, $result);
    $result = str_replace('[CLID_NAME]', $$callerid_name, $result);
    $result = str_replace('[CLID_NUMBER]', $callerid_number, $result);
    $result = str_replace('[DURATION]', $duration, $result);
    $result = str_replace('[DOMAIN]', $domain, $result);
    $result = str_replace('[DAY_N]', $tmp_day_n, $result);
    $result = str_replace('[DIRECTION]', $direction, $result);
    $result = str_replace('[DESTINATION]', $destination, $result);
    // Cleanup string if any
    $result = preg_replace("/[^0-9a-zA-Z-]/","_",$result);
    return $result;
}


$domain_uuid = $_SESSION['domain_uuid'];

if ($domain_uuid == "") {
    send_api_answer("404", "Domain UUID not found");
    exit;
}

$moving_path = isset($_SESSION['external storage']['record_path']['text'])?$_SESSION['external storage']['record_path']['text']:False;

if (!$moving_path or $moving_path == "") {
    send_api_answer("503", "Storage path not found");
    exit;
}

// Get last data here
$sql = "SELECT uuid, json";
$sql .= " FROM v_my_api_data WHERE";
$sql .= " domain_uuid = '".$domain_uuid."' AND api_name = 'recording_duplicate'";

$prep_statement = $db->prepare(check_sql($sql));
$prep_statement->execute();
$db_result = $prep_statement->fetch(PDO::FETCH_ASSOC);
unset ($prep_statement, $sql);

$last_timestamp = (string)time();

if (count($db_result) > 0 && $db_result) { // Assume no data is received
    $current_api_data = $db_result['json'];
    $current_api_data = json_decode($current_api_data, true);
    $last_timestamp = $current_api_data['last_timestamp'];
    $last_timestamp_uuid = $db_result['uuid'];
}

unset($db_result);

// Get ALL CDR's here
// Get CDR's here
$sql = "SELECT caller_id_name, caller_id_number, duration, json, uuid, bridge_uuid, hangup_cause, billmsec, start_epoch, destination_number, direction";
$sql .= " FROM v_xml_cdr WHERE";
$sql .= " (start_epoch > '".$last_timestamp."') AND";
$sql .= " (domain_uuid = '".$domain_uuid."')";

$prep_statement = $db->prepare(check_sql($sql));
$prep_statement->execute();
$db_result = $prep_statement->fetchAll();
unset ($prep_statement, $sql);

if (count($db_result) == 0) {
    send_api_answer("404", "No records found starting from stamp ".$last_timestamp);
}

// Reset last_timestamp
$new_last_timestamp = $last_timestamp;

foreach ($db_result as $cdr_line) {

    if ($cdr_line['start_epoch'] > $new_last_timestamp) {
        $new_last_timestamp = $cdr_line['start_epoch'];
    }
    $json_cdr_line = json_decode($cdr_line['json'], true);

    $tmp_year = date("Y", $cdr_line['start_epoch']);
    $tmp_month = date("M", $cdr_line['start_epoch']);
    $tmp_day = date("d", $cdr_line['start_epoch']);

    $seconds = ($cdr_line['hangup_cause']=="ORIGINATOR_CANCEL") ? $cdr_line['duration'] : round(($cdr_line['billmsec'] / 1000), 0, PHP_ROUND_HALF_UP);

    $tmp_rel_path = '/archive/'.$tmp_year.'/'.$tmp_month.'/'.$tmp_day;
    $tmp_dir = $_SESSION['switch']['recordings']['dir'].'/'.$_SESSION["domain_name"].$tmp_rel_path;
    $tmp_name = '';
    if (!empty($cdr_line['recording_file']) && file_exists($cdr_line['recording_file'])) { 
        $tmp_name = $cdr_line['recording_file']; 
    } elseif (file_exists($tmp_dir.'/'.$cdr_line['uuid'].'.wav')) { 
        $tmp_name = $cdr_line['uuid'].".wav"; 
    } elseif (file_exists($tmp_dir.'/'.$cdr_line['uuid'].'_1.wav')) { 
        $tmp_name = $cdr_line['uuid']."_1.wav"; 
    } elseif (file_exists($tmp_dir.'/'.$cdr_line['uuid'].'.mp3')) { 
        $tmp_name = $cdr_line['uuid'].".mp3"; 
    } elseif (file_exists($tmp_dir.'/'.$cdr_line['uuid'].'_1.mp3')) { 
        $tmp_name = $cdr_line['uuid']."_1.mp3"; 
    } elseif (file_exists($tmp_dir.'/'.$cdr_line['bridge_uuid'].'.wav')) { 
        $tmp_name = $cdr_line['bridge_uuid'].".wav"; 
    } elseif (file_exists($tmp_dir.'/'.$cdr_line['bridge_uuid'].'_1.wav')) { 
        $tmp_name = $cdr_line['bridge_uuid']."_1.wav"; 
    } elseif (file_exists($tmp_dir.'/'.$cdr_line['bridge_uuid'].'.mp3')) { 
        $tmp_name = $cdr_line['bridge_uuid'].".mp3"; 
    } elseif (file_exists($tmp_dir.'/'.$cdr_line['bridge_uuid'].'_1.mp3')) { 
        $tmp_name = $cdr_line['bridge_uuid']."_1.mp3"; 
    }
    if (strlen($tmp_name) > 0 && file_exists($tmp_dir.'/'.$tmp_name) && $seconds > 0) { // Recording file found
        $recording_file_path = $tmp_dir.'/'.$tmp_name;
        $recording_file_name = strtolower(pathinfo($tmp_name, PATHINFO_BASENAME));
        $recording_file_ext = pathinfo($recording_file_name, PATHINFO_EXTENSION);

        $new_file_path_full = prepare_filepath($cdr_line['start_epoch'], $moving_path, $cdr_line['caller_id_name'], $cdr_line['caller_id_number'], $cdr_line['duration'], $_SESSION['domain_name'], $cdr_line['direction'], $cdr_line['destination_number']);
        $new_file_path_full = str_replace("[EXT]",$recording_file_ext, $new_file_path_full);

        // Just printing here
        $new_file_path = pathinfo($new_file_path_full, PATHINFO_DIRNAME);

        mkdir($new_file_path, 0777, true);
        copy($recording_file_path, $new_file_path_full);
    }
}

if ($new_last_timestamp > $last_timestamp) {
    $last_timestamp = $new_last_timestamp;
}

if (isset($last_timestamp_uuid)) { // Here we know about last timestamp
    $current_api_data['last_timestamp'] = $last_timestamp;
    $current_api_data = json_encode($current_api_data);
    $sql = "UPDATE v_my_api_data SET";
    $sql .= " json = '".$current_api_data."'";
    $sql .= " WHERE uuid = '".$last_timestamp_uuid."'";
} else {
    $current_api_data = array('last_timestamp' => $last_timestamp);
    $current_api_data = json_encode($current_api_data);
    $sql = "INSERT INTO v_my_api_data";
    $sql .= "(uuid, domain_uuid, api_name, json)";
    $sql .= " VALUES (";
    $sql .= " '".uuid()."',";
    $sql .= " '".$domain_uuid."',";
    $sql .= " 'recording_duplicate',";
    $sql .= "'".$current_api_data."')";
}
$prep_statement = $db->prepare(check_sql($sql));
$prep_statement->execute();
unset ($prep_statement, $sql);

?>