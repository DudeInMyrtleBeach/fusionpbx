-- data is provided by this funstions
-- timestamp

function vtiger_api_call(method, credentials, data, is_return)

    local api_data = data

    api_data['timestamp'] = os.time()
    api_data['uuid'] = session:getVariable('call_uuid') or ""

    local api_string = credentials['url'] .. "call_"..method..".php content-type application/json post '"..json_encode(api_data).."'"
    if (api_data['debug']) then
        freeswitch.consoleLog("NOTICE", "[vtiger_connector][call_"..method.."] "..api_string)
    else
        local api_response = api:execute("curl ", api_string)
        freeswitch.consoleLog("NOTICE", "[vtiger_connector][call_"..method.."] Response: "..api_response)
    end

end

function ringing_answered_call(type, argv)
	local credentials = {}
	if (argv[3] == nil or argv[4] == nil) then
		freeswitch.consoleLog("WARNING", "[vtiger_connector]["..type.."] Can't get URL or key")
		do return end
	end
	credentials['url'], credentials['key'] = argv[3], argv[4]
	local dialed_user = session:getVariable("dialed_user")
	if (dialed_user == nil) then
		freeswitch.consoleLog("WARNING", "[vtiger_connector]["..type.."] Can't get dialed user")
		do return end
	end
	local call_data = {}
    call_data['number'] = dialed_user
    --call_data['debug'] = true

	vtiger_api_call(type, credentials, call_data)
end

-- Prepare JSON strings
function json_encode(data)
    local function string(o)
        return '"' .. tostring(o) .. '"'
    end
    local function recurse(o, indent)
        if indent == nil then
            indent = ''
        end
        indent = indent.."{"
        for k,v in pairs(o) do
            indent = indent .. string(k) .. ":"
            if type(v) == 'table' then
                indent = indent .. recurse(v)
            else 
                indent = indent .. string(v) .. ","
            end
        end
        return indent:sub(0, -2) .. "},"
    end
    if type(data) ~= 'table' then
        return nil
    end
    return recurse(data):sub(0, -2)
end

-- Get call direction
function get_call_direction(src, dst)

    -- Emergency routes
    local emergency_table = {}
    emergency_table['911'] = 1

    local src_len = src:len()
    local dst_len = dst:len()

    if emergency_table[dst] ~= nil and dst_len >= 7 then
        return "outbound"
    end

    if (src_len > 7) then
        return "inbound"
    end

    return "local"


end