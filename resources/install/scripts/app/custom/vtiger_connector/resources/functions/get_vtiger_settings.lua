-- https://github.com/kennyledet/Algorithm-Implementations/blob/master/ROT13_Cipher/Lua/Yonaba/rot13.lua

function vtiger_connector_get_domain_name()
    return session:getVariable("domain_uuid")
end


-- Prepare SQL string for request
function vtiger_connector_form_sql_request(prefix)

    local domain_uuid = get_domain_name()
    if (domain_uuid == nil) then
        return nil
    end

    local sql = "SELECT "..prefix.."_setting_subcategory AS subcategory, "..prefix.."_setting_value AS value FROM v_"..prefix.."_settings"
    sql = sql .. " WHERE "..prefix.."_setting_category = 'vtiger'"
    sql = sql .. " AND "..prefix.."_setting_name = 'text'"
    sql = sql .. " AND "..prefix.."_setting_enabled = 'true'"
    if (prefix == "domain") then
        sql = sql .. " AND domain_uuid = '"..domain_uuid.."'"
    end
    return sql
end


-- Ask database and return results if any
function vtiger_connector_process_getting_settings(dbh, sql, settings)

    local result = settings

    if (sql == nil) then
        return false, false
    end

    dbh:query(sql, function(row)
        if (row['subcategory'] and row['subcategory'] == 'url' and result['url'] == nil) then
            result['url'] = row['value'] or nil
        end
        if (row['subcategory'] and row['subcategory'] == 'api_key' and result['key'] == nil) then
            result['key'] = row['value'] or nil
        end
    end);
    
    if (result['url'] and result['key']) then
        return result, true
    end

    return result, false

end

-- Return actual settings for VTiger as table (url, key) or nil
function get_vtiger_settings(dbh) 
    
    local is_full_settings
    local settings = {}


    -- Loop through domain and global settings. Not really efficient, but works
    local sql = vtiger_connector_form_sql_request("domain")
    settings, is_full_settings = vtiger_connector_process_getting_settings(dbh, sql, settings)
    if (is_full_settings) then
        return settings
    end

    sql = vtiger_connector_form_sql_request("default")
    settings, is_full_settings = vtiger_connector_process_getting_settings(dbh, sql, settings)
    if (is_full_settings) then
        return settings
    end

    return nil

end