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