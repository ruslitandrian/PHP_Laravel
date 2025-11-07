@echo off
setlocal ENABLEDELAYEDEXPANSION

REM ==========================================
REM Restart Laravel dev server after clearing caches
REM Usage: restart-serve.bat [port]
REM Default port: 8000
REM ==========================================

set PROJECT_DIR=%~dp0
cd /d "%PROJECT_DIR%"

set PORT=%1
if "%PORT%"=="" set PORT=8000
set HOST=127.0.0.1

echo ==========================================
echo =  Clear caches, stop server, start new  =
echo ==========================================

echo [STEP] Clearing Laravel caches...
php -v >NUL 2>&1 || ( echo [ERROR] PHP not found in PATH & exit /b 1 )

REM Ensure compiled views directory exists to avoid RuntimeException
set VIEW_COMPILED_DIR=%PROJECT_DIR%storage\framework\views
if not exist "%VIEW_COMPILED_DIR%" (
  echo [INFO] Creating compiled views directory: %VIEW_COMPILED_DIR%
  mkdir "%VIEW_COMPILED_DIR%" >NUL 2>&1
)

REM Clear caches first (even if the server is still running)
php artisan cache:clear || goto :err
php artisan config:clear || goto :err
php artisan route:clear || goto :err
php artisan view:clear || (
  echo [WARN] view:clear failed once, ensuring compiled dir again and retrying...
  if not exist "%VIEW_COMPILED_DIR%" mkdir "%VIEW_COMPILED_DIR%" >NUL 2>&1
  php artisan view:clear || goto :err
)
php artisan optimize:clear || goto :err

echo [OK  ] Caches cleared.

echo [STEP] Stopping existing php artisan serve on port %PORT% (if any)...
set KILLED=0
for /f "tokens=1,2,3,4,5*" %%a in ('netstat -ano ^| findstr /R /C:":%PORT% .*LISTENING"') do (
  set PID=%%e
  echo [INFO] Killing PID !PID! on port %PORT%
  taskkill /PID !PID! /F >NUL 2>&1
  set KILLED=1
)
if "%KILLED%"=="0" echo [INFO] No existing php artisan serve found on port %PORT% (or already stopped)

REM Give Windows a moment to release the port
ping -n 2 127.0.0.1 >NUL

echo [STEP] Starting php artisan serve on http://%HOST%:%PORT% ...
if "%PORT%"=="8000" (
  set SERVE_CMD=php artisan serve
) else (
  set SERVE_CMD=php artisan serve --host=%HOST% --port=%PORT%
)

echo [RUN ] !SERVE_CMD!
start "Laravel Dev Server" cmd /c "!SERVE_CMD!"

echo [DONE] Restart completed. A new server window has been launched.
exit /b 0

:err
echo [ERROR] A command failed. Aborting. See messages above.
exit /b 1
