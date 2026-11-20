@echo off
chcp 65001 >nul
title Smart Gram - Server Launcher
echo ========================================================
echo   Smart Gram Server Launcher
echo ========================================================
echo.

:: 1. Check if MariaDB/MySQL is running on port 3306
echo [1/3] Checking Database (MariaDB/MySQL)...
netstat -ano | findstr :3306 >nul
if %errorlevel% neq 0 (
    echo MariaDB is not running. Starting MariaDB...
    start "" /b "C:\Program Files\MariaDB 13.0\bin\mysqld.exe" --defaults-file="C:\Program Files\MariaDB 13.0\data\my.ini"
    timeout /t 3 /nobreak >nul
    echo MariaDB started successfully.
) else (
    echo MariaDB is already running on port 3306.
)

:: 2. Check if PHP server is running on port 8000
echo.
echo [2/3] Checking PHP Web Server on port 8000...
netstat -ano | findstr :8000 >nul
if %errorlevel% neq 0 (
    echo Starting PHP development server...
    start "" php -S 127.0.0.1:8000 -t public public/index.php
    timeout /t 2 /nobreak >nul
    echo PHP development server started on http://127.0.0.1:8000.
) else (
    echo PHP server is already listening on http://127.0.0.1:8000.
)

:: 3. Open Application in default browser
echo.
echo [3/3] Opening Smart Gram in browser...
start http://127.0.0.1:8000

echo.
echo ========================================================
echo Smart Gram is live at http://127.0.0.1:8000
echo ========================================================
