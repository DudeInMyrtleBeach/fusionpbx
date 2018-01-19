-- This function is called like app_custom.lua vtiger_connector
-- You MUST specify VTiger URL in Default (or Domain) settings.
-- Also it uses freeswitch curl command, so it also need to be loaded

-- Vars to specify
-- url
-- api_key

require "resources.functions.database_handle"
require "app.custom.vtiger_connector.resources.functions.get_vtiger_settings"
require "app.custom.vtiger_connector.resources.functions.api_functions"

local app_name = argv[2]
api = freeswitch.API()

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
    
    freeswitch.consoleLog("NOTICE", "[vtiger_connector] Got Vtiger URL("..vtiger_settings['url']..") and key("..vtiger_settings['key']..")")
    session:execute("export", "vtiger_url="..enc64(vtiger_settings['url']))
    session:execute("export", "vtiger_api_key="..enc64(vtiger_settings['key']))
    session:execute("export", "vtiger_record_path="..enc64(vtiger_settings['record_path']))    
    session:execute("export", "nolocal:execute_on_ring_"..execute_on_ring_suffix.."=lua app_custom.lua vtiger_connector ringing")
    session:execute("export", "nolocal:execute_on_answer_"..execute_on_answer_suffix.."=lua app_custom.lua vtiger_connector answer")
    local call_start_data = {}
    
    local src = {}
    src['name'] = session:getVariable('caller_id_name') or ""
    src['number'] = session:getVariable('caller_id_number') or ""

    local dst = session:getVariable('destination_number') or ""

    call_start_data['src'] = src
    call_start_data['dst'] = dst
    call_start_data['direction'] = get_call_direction(src['number'], dst)
    --call_start_data['debug'] = true

    vtiger_api_call("start", vtiger_settings, call_start_data)
end