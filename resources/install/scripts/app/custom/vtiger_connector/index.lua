-- This function is called like app_custom.lua vtiger_connector
-- You MUST specify VTiger URL in Default (or Domain) settings.
-- Also it uses freeswitch curl command, so it also need to be loaded

-- Vars to specify
-- url
-- api_key

require "resources.functions.database_handle"
require "app.custom.vtiger_connector.resources.functions.get_vtiger_settings"

local app_name = argv[2]

if (app_name and app_name ~= 'main') then
    loadfile(scripts_dir .. "/app/custom/vtiger_connector/" .. app_name .. ".lua")(argv)
    do return end
end

local dbh = database_handle('system');

local license_key = argv[3] or '';
local execute_on_ring_suffix = argv[4] or '3';
local execute_on_answer_suffix = argv[5] or '3';

if (session:ready()) then
    local vtiger_settings = get_vtiger_settings(dbh)

    if (vtiger_settings == nil) then
        do return end
    end
    freeswitch.consoleLog("NOTICE", "[vtiger_connector] Got Vtiger URL("..vtiger_settings['url']..") and key("..vtiger_settings['key']..") ")
    
    freeswitch.setVariable("is_vtiger_connector", "true")
    session:execute("export","execute_on_ring_"..execute_on_ring_suffix.."=lua app_custom.lua vtiger_connector ringing "..vtiger_settings['url'].." "..vtiger_settings['key'])
    session:execute("export","execute_on_answer_"..execute_on_answer_suffix.."=lua app_custom.lua vtiger_connector answer "..vtiger_settings['url'].." "..vtiger_settings['key'])
end
