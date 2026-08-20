@echo off
setlocal EnableExtensions

set "ROOT=%~dp0"
set "DB=%ROOT%database\database.sqlite"
set "BACKUP_DIR=%ROOT%backups"

if not exist "%DB%" (
    echo No se encontro la base de datos:
    echo   %DB%
    exit /b 1
)

if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

for /f %%i in ('powershell -NoProfile -Command "Get-Date -Format yyyy-MM-dd_HHmmss"') do set "STAMP=%%i"
set "DEST=%BACKUP_DIR%\database_%STAMP%.sqlite"

copy /Y "%DB%" "%DEST%" >nul
if errorlevel 1 (
    echo Error al crear la copia de seguridad.
    exit /b 1
)

echo Copia creada:
echo   %DEST%
echo.
echo Usa restore-db.bat si necesitas recuperar estos datos.
exit /b 0
