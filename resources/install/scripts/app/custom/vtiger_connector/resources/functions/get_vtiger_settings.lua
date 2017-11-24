
-- Prepare SQL string for request
function form_sql_request(prefix)

    local domain_uuid = session:getVariable("domain_uuid")

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
function process_getting_settings(dbh, sql, settings)

    local result = settings

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
    
    local is_full
    local settings = {}

    local sql = form_sql_request("domain")
    
    settings, is_full_settings =  process_getting_settings(dbh, sql, settings)
    if (is_full_settings) then
        return settings
    end

    sql = form_sql_request("default")
    settings, is_full_settings =  process_getting_settings(dbh, sql, settings)
    if (is_full_settings) then
        return settings
    end

    return nil

end