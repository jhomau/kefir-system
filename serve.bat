@echo off
setlocal EnableExtensions

set "PHP_DIR=%LOCALAPPDATA%\Microsoft\WinGet\Packages\PHP.PHP.8.2_Microsoft.Winget.Source_8wekyb3d8bbwe"
set "PHP=%PHP_DIR%\php.exe"
set "EXT_DIR=%PHP_DIR%\ext"
set "ROOT=%~dp0"
set "PUBLIC=%ROOT%public"
set "ROUTER=%ROOT%vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php"

if not exist "%PHP%" (
    echo No se encontro PHP 8.2. Verifica la instalacion de WinGet.
    exit /b 1
)

if not exist "%ROUTER%" (
    echo No se encontro el router de Laravel. Ejecuta composer install.
    exit /b 1
)

cd /d "%PUBLIC%"

echo Servidor: http://127.0.0.1:8000
echo Carpeta: %PUBLIC%
echo (Para ver peticiones como "artisan serve", usa: serve-artisan.bat)
echo.

"%PHP%" -d "extension_dir=%EXT_DIR%" -d extension=openssl -d extension=curl -d extension=mbstring -d extension=fileinfo -d extension=pdo_mysql -d extension=pdo_sqlite -d extension=sqlite3 -d extension=zip -d extension=gd -d extension=intl -S 127.0.0.1:8000 "%ROUTER%"
