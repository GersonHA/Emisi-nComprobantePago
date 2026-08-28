@echo off
title API Comprobantes
echo Buscando PHP...

for /f "delims=" %%i in ('where /r "%LOCALAPPDATA%\Microsoft\WinGet\Packages" php.exe 2^>nul') do (
    set "PHP_EXE=%%i"
    goto :start
)

echo.
echo No encontre php.exe. Abri PowerShell y corré:
echo   winget install --id=PHP.PHP.8.2 -e
echo.
pause
exit /b 1

:start
echo Usando: %PHP_EXE%
echo.
echo Server en http://127.0.0.1:8000  (Ctrl+C para detener)
echo.

"%PHP_EXE%" -S 127.0.0.1:8000 index.php

pause