require "app.custom.vtiger_connector.resources.functions.api_functions"

if (session:ready()) then
	local credentials = {}
	local _, _, credentials['url'], credentials['key'] = argv
	if (credentials['url'] == nil or credentials['key'] == nil) then
		freeswitch.consoleLog("WARNING", "[vtiger_connector][ringing] Can't get URL or key")
		do return end
	end
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