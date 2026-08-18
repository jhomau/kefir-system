# Inicia el servidor de desarrollo Laravel (Windows)
$phpDir = "$env:LOCALAPPDATA\Microsoft\WinGet\Packages\PHP.PHP.8.2_Microsoft.Winget.Source_8wekyb3d8bbwe"
$php = Join-Path $phpDir "php.exe"
$extDir = Join-Path $phpDir "ext"
$phpArgs = @(
    "-d", "extension_dir=$extDir",
    "-d", "extension=openssl",
    "-d", "extension=curl",
    "-d", "extension=mbstring",
    "-d", "extension=fileinfo",
    "-d", "extension=pdo_mysql",
    "-d", "extension=pdo_sqlite",
    "-d", "extension=sqlite3",
    "-d", "extension=zip",
    "-d", "extension=gd",
    "-d", "extension=intl"
)

$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location (Join-Path $projectRoot "public")

Write-Host "Servidor: http://localhost:8000" -ForegroundColor Green
& $php @phpArgs -S localhost:8000 "..\vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php"
