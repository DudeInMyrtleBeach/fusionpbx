-- This function is called like app_custom.lua vtiger_connector
-- You MUST specify VTiger URL in Default (or Domain) settings.
-- Also it uses freeswitch curl command, so it also need to be loaded

-- Vars to specify
-- url
-- api_key

require "resources.functions.database_handle"
require "app.custom.vtiger_connector.resources.functions.get_vtiger_settings"

local dbh = database_handle('system');

if (session:ready()) then
    local vtiger_settings = get_vtiger_settings(dbh)

    if (vtiger_settings == nil) then
        do return end
    end
    freeswitch.consoleLog("NOTICE", "[vtiger_connector] Got Vtiger URL("..vtiger_settings['url']..") and key("..vtiger_settings['key']..") ")
end
