if (session:ready()) then
    freeswitch.consoleLog("NOTICE", "[vtiger_connector] Ringing")
	arguments = "";
	for key,value in pairs(argv) do
	 	if (key > 0) then
	 		arguments = arguments .. " '" .. value .. "'";
	 		freeswitch.consoleLog("notice", "[app_custom.lua ringing] argv["..key.."]: "..argv[key].."\n");
	 	end
	end
end