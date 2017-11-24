if (session:ready()) then
    freeswitch.consoleLog("NOTICE", "[vtiger_connector] Answer")
    session:execute("info", "()")
end