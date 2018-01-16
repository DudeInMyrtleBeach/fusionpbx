-- data is provided by this funstions
-- timestamp

function vtiger_api_call_start(credentials, data)

    local api_data = data

    api_data['timestamp'] = os.time()
    local api_string = credentials['url'] .. "call_start.php content-type application/json post '"..json_encode(api_data).."'"
    if (api_data['debug']) then
        freeswitch.consoleLog("NOTICE", "[vtiger_connector][call_start] "..api_string)
    else
        api:executeString("bgapi culr "..api_string)
    end

end

function vtiger_api_call_ringing(credentials, data)

    local api_data = data

    api_data['timestamp'] = os.time()
    local api_string = credentials['url'] .. "call_ringing.php content-type application/json post '"..json_encode(api_data).."'"
    if (api_data['debug']) then
        freeswitch.consoleLog("NOTICE", "[vtiger_connector][call_ringing] "..api_string)
    else
        api:executeString("bgapi culr "..api_string)
    end
end

function vtiger_api_call_answer(credentials, data)

    local api_data = data

    api_data['timestamp'] = os.time()
    local api_string = credentials['url'] .. "call_answered.php content-type application/json post '"..json_encode(api_data).."'"
    if (api_data['debug']) then
        freeswitch.consoleLog("NOTICE", "[vtiger_connector][call_answered] "..api_string)
    else
        api:executeString("bgapi culr "..api_string)
    end
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