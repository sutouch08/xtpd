Set WinScriptHost = CreateObject("WScript.Shell")
WinScriptHost.Run Chr(34) & "C:\Apache24\htdocs\tpd\sync_script\auto_cancel_order.bat" & Chr(34), 0
Set WinScriptHost = Nothing