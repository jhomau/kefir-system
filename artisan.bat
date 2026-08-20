@echo off
setlocal EnableExtensions

set "PHP_DIR=%LOCALAPPDATA%\Microsoft\WinGet\Packages\PHP.PHP.8.2_Microsoft.Winget.Source_8wekyb3d8bbwe"
set "PHP=%PHP_DIR%\php.exe"
set "EXT_DIR=%PHP_DIR%\ext"
set "ROOT=%~dp0"

if not exist "%PHP%" (
    echo No se encontro PHP 8.2. Verifica la instalacion de WinGet.
    exit /b 1
)

cd /d "%ROOT%"

"%PHP%" -d "extension_dir=%EXT_DIR%" -d extension=openssl -d extension=curl -d extension=mbstring -d extension=fileinfo -d extension=pdo_mysql -d extension=pdo_sqlite -d extension=sqlite3 -d extension=zip -d extension=gd -d extension=intl artisan %*
