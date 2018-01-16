if (session:ready()) then

	if (argv[3] == nil or argv[4] == nil) then
		freeswitch.consoleLog("WARNING", "[vtiger_connector][ringing] Can't get URL or key")
		do return end
	end
	local url, key = argv[3], argv[4]
	local dialed_user = session:getVariable("dialed_user")
	if (dialed_user == nil) then
		freeswitch.consoleLog("WARNING", "[vtiger_connector][ringing] Can't get dialed user")
		do return end
	end
	local call_data = {}
	call_data['uuid'] = session:getVariable('call_uuid') or ""
	call_data['number'] = dialed_user
	call_data['timestamp'] = os.time()

	local api_string = url .. "/call_ringing.php content-type application/json post '"..json_encode(call_data).."'"
	
	freeswitch.consoleLog("NOTICE", "[vtiger_connector][call_ringing] "..api_string)
	
	--api:executeString("bgapi culr "..api_string)

end