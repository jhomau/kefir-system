@echo off
echo ============================================================
echo  Reparar localhost en Windows (requiere Administrador)
echo ============================================================
echo.
echo Tu archivo hosts tiene "localhost" comentado.
echo Este script lo descomenta para que localhost funcione.
echo.
pause

powershell -NoProfile -ExecutionPolicy Bypass -Command ^
  "$hosts = 'C:\Windows\System32\drivers\etc\hosts';" ^
  "$content = Get-Content $hosts -Raw;" ^
  "$content = $content -replace '#\s*127\.0\.0\.1\s+localhost', '127.0.0.1       localhost';" ^
  "$content = $content -replace '#\s*::1\s+localhost', '::1             localhost';" ^
  "if ($content -notmatch '127\.0\.0\.1\s+localhost') { $content += \"`r`n127.0.0.1       localhost`r`n::1             localhost`r`n\" };" ^
  "Set-Content -Path $hosts -Value $content -Encoding ASCII;" ^
  "Write-Host 'Listo. Reinicia el navegador y prueba http://localhost:8000' -ForegroundColor Green"

pause
