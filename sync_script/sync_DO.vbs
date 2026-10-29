Set WinScriptHost = CreateObject("WScript.Shell")
WinScriptHost.Run Chr(34) & "C:\Apache24\htdocs\tpd\sync_script\sync_DO.bat" & Chr(34), 0
Set WinScriptHost = Nothing
