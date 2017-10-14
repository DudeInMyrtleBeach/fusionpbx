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

// Get all domains and user's API keys. Some magic here with join and group by
$sql = "SELECT v_domains.domain_name, MIN(CAST(v_users.api_key AS TEXT)) AS api_key FROM v_users JOIN v_domains ON v_domains.domain_uuid = v_users.domain_uuid WHERE v_users.api_key is not NULL GROUP BY domain_name";

$prep_statement = $db->prepare(check_sql($sql));
$prep_statement->execute();
$db_result = $prep_statement->fetchAll();
unset ($prep_statement, $sql);

foreach ($db_result as $domain_info) {
    $domain_name = $domain_info['domain_name'];
    $api_key = $domain_info['api_key'];
    $curl_path = "https://".$domain_name."/app/my_api/recording_duplicate.php?key=".$api_key;
    $curl_init = curl_init($curl_path);
    curl_setopt($curl_init,CURLOPT_NOBODY,true);
    curl_setopt($curl_init,CURLOPT_CONNECTTIMEOUT,60);
    $curl_response = curl_exec($curl_init);
    print("Processing $domain_name\n$curl_response\n");
    curl_close($curl_init);
}

?>