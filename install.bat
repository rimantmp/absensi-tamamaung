@echo off
setlocal
cd /d "%~dp0"

echo ============================================
echo   SISTEM ABSENSI SISWA - AUTO INSTALLER
echo   SD Inpres Tamamaung IV
echo ============================================
echo.

where powershell >nul 2>nul
if errorlevel 1 (
    echo PowerShell tidak ditemukan di sistem ini.
    pause
    exit /b 1
)

powershell -NoLogo -NoProfile -ExecutionPolicy Bypass -File "%~dp0install.ps1"
set EXITCODE=%ERRORLEVEL%

echo.
if "%EXITCODE%"=="0" (
    echo ============================================
    echo   Instalasi selesai. Selamat menggunakan!
    echo ============================================
) else (
    echo ============================================
    echo   Instalasi gagal. Periksa pesan error di atas.
    echo ============================================
)
echo.
pause
exit /b %EXITCODE%
