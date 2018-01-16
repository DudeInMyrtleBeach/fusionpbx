if (session:ready()) then
	local credentials = {}
	if (argv[3] == nil or argv[4] == nil) then
		freeswitch.consoleLog("WARNING", "[vtiger_connector][ringing] Can't get URL or key")
		do return end
	end
	credentials['url'], credentials['key'] = argv[3], argv[4]
	local dialed_user = session:getVariable("dialed_user")
	if (dialed_user == nil) then
		freeswitch.consoleLog("WARNING", "[vtiger_connector][ringing] Can't get dialed user")
		do return end
	end
	local call_data = {}
    call_data['number'] = dialed_user
    --call_data['debug'] = true

	vtiger_api_call("ringing", credentials, call_data)

end