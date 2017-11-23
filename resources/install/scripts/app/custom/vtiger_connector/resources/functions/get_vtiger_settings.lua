
-- Prepare SQL string for request
function form_sql_request(prefix)
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
function process_getting_settings(sql, dbh)
    
    local settings = {}

    local results_count = 0
    dbh:query(sql, function(row)
        if (row['subcategory'] and row['subcategory'] == 'url') then
            settings['url'] = row['value'] or nil
        end
        if (row['subcategory'] and row['subcategory'] == 'api') then
            settings['api'] = row['value'] or nil
        end
        results_count = results_count + 1
    end);
    
    if (results_count == 2 and settings['url'] and settings['api']) then
        return settings
    end

    return nil

end

-- Return actual settings for VTiger as table (url, key) or nil
function get_vtiger_settings(domain_uuid, dbh) 
    
    local sql = form_sql_request("domain")
    local settings =  process_getting_settings(sql, dbh)

    if (settings) then
        return settings
    end

    sql = form_sql_request("default")

    return process_getting_settings(sql, dbh)

end