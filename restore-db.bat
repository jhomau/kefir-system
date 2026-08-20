@echo off
setlocal EnableExtensions EnableDelayedExpansion

set "ROOT=%~dp0"
set "DB=%ROOT%database\database.sqlite"
set "BACKUP_DIR=%ROOT%backups"

if not exist "%BACKUP_DIR%" (
    echo No existe la carpeta de respaldos:
    echo   %BACKUP_DIR%
    echo Ejecuta primero backup-db.bat
    exit /b 1
)

set "COUNT=0"
echo Respaldos disponibles:
echo.

for /f "delims=" %%F in ('dir /b /a:-d /o:-d "%BACKUP_DIR%\database_*.sqlite" 2^>nul') do (
    set /a COUNT+=1
    set "FILE_!COUNT!=%%F"
    echo   !COUNT!. %%F
)

if "%COUNT%"=="0" (
    echo No hay archivos database_*.sqlite en backups\
    echo Ejecuta primero backup-db.bat
    exit /b 1
)

echo.
set /p "CHOICE=Elige el numero del respaldo a restaurar (o Enter para cancelar): "
if "%CHOICE%"=="" (
    echo Cancelado.
    exit /b 0
)

set "SELECTED="
for /L %%N in (1,1,%COUNT%) do (
    if "%CHOICE%"=="%%N" set "SELECTED=!FILE_%%N!"
)

if not defined SELECTED (
    echo Numero invalido.
    exit /b 1
)

set "SRC=%BACKUP_DIR%\%SELECTED%"

echo.
echo Vas a restaurar:
echo   %SRC%
echo Sobre:
echo   %DB%
echo.
set /p "CONFIRM=Escribe SI para continuar: "
if /I not "%CONFIRM%"=="SI" (
    echo Cancelado.
    exit /b 0
)

if exist "%DB%" (
    if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"
    for /f %%i in ('powershell -NoProfile -Command "Get-Date -Format yyyy-MM-dd_HHmmss"') do set "STAMP=%%i"
    set "SAFETY=%BACKUP_DIR%\before_restore_%STAMP%.sqlite"
    copy /Y "%DB%" "!SAFETY!" >nul
    if errorlevel 1 (
        echo No se pudo crear el respaldo de seguridad previo. Abortado.
        exit /b 1
    )
    echo Respaldo previo guardado en:
    echo   !SAFETY!
)

copy /Y "%SRC%" "%DB%" >nul
if errorlevel 1 (
    echo Error al restaurar la base de datos.
    exit /b 1
)

echo.
echo Base de datos restaurada correctamente.
echo Si el servidor esta corriendo, reinicialo (cierra y vuelve a abrir serve.bat).
exit /b 0
