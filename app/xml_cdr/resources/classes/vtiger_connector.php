<?php

if (!class_exists('vtiger_connector')) {
	class vtiger_connector {

        private $url;
        private $key;
        private $fields;

        public function __construct($url, $key, $database_fields, $record_path) {
            if (stlen($url) == 0 or strlen($key) == 0) {
                return false;
            }

            $this->url = $url;
            $this->key = $key;

            $this->fields = array();
            $this->fields['timestamp'] = $database_fields['end_epoch'];
            $this->fields['direction'] = $database_fields['direction'];
            // Get correct hangup
            switch ($database_fields['hangup_cause']) {
                case 'NORMAL_CLEARING':
                    $this->fields['status'] = 'answered';
                    break;
                case 'CALL_REJECTED':
                case 'SUBSCRIBER_ABSENT':
                case 'USER_BUSY':
                    $this->fields['status'] = 'busy';
                    break;
                case 'NO_ANSWER':
                case 'NO_USER_RESPONSE':
                case 'ORIGINATOR_CANCEL':
                case 'LOSE_RACE': // This cause usually in ring groups, so this call is not ended.
                    $this->fields['status'] = 'no answer';
                    break;
                default:
                    $this->fields['status'] = 'failed';
                    break;
            }
            $src = array();
            $src['name'] = $database_fields['caller_id_name'];
            $src['number'] = $database_fields['caller_id_number'];
            $this->fields['src'] = $src;

            $last_seen = array();
            $last_seen['name'] = $database_fields['destination_number'];
            $last_seen['number'] = $database_fields['destination_number'];
            $this->fields['last_seen'] = $last_seen;

            $time = array();
            $time['duration'] = $database_fields['duration'];
            $time['answered'] = $database_fields['billsec'];
            $this->fields['time'] = $time;

            $this->fields['uuid'] = $database_fields['uuid'];

            if ($record_path) {
                $this->fields['recording'] = $record_path;
            }
            
            return true;
        }

        public function __destruct() {
			if (isset($this)) foreach ($this as $key => $value) {
				unset($this->$key);
			}
		}


        public function send() {
            
            $data_string = json_encode($this->fields);

            $ch = curl_init($this->url.'/call_end.php');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURL_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json',
                                                    'Content-Length: ' . strlen($data_string)
                                                ));

            $resp = curl_exec($ch);
            curl_close($ch);

            file_put_contents('/tmp/api_vtiger.log', " -> ".$this->url.'/call_end.php'. " Req:".$data_string." Resp:".$resp."\n", FILE_APPEND);

        }
    }
}
?>