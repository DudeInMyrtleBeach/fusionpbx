if (session:ready()) then
	local credentials = {}
	local _, _, url, key = argv
	if (url == nil or url == nil) then
		freeswitch.consoleLog("WARNING", "[vtiger_connector][ringing] Can't get URL or key")
		do return end
	end
	credentials['url'], credentials['key'] = url, key
	local dialed_user = session:getVariable("dialed_user")
	if (dialed_user == nil) then
		freeswitch.consoleLog("WARNING", "[vtiger_connector][ringing] Can't get dialed user")
		do return end
	end
	local call_data = {}
	call_data['uuid'] = session:getVariable('call_uuid') or ""
	call_data['number'] = dialed_user

	vtiger_api_call_ringing(credentials, call_data)

end