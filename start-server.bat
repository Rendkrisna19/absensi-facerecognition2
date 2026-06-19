@echo off
title Sistem Absensi Tri Jaya - Server Lokal
color 0A

:: Pindah ke folder tempat file .bat ini berada
cd /d "%~dp0"

echo ============================================
echo   SISTEM ABSENSI YAYASAN TRI JAYA
echo   Server Lokal - Bisa Diakses dari HP
echo ============================================
echo.

:: Cek apakah PHP tersedia
php -v >nul 2>&1
if errorlevel 1 (
    echo [ERROR] PHP tidak ditemukan! Pastikan PHP sudah terinstall.
    pause
    exit /b 1
)

:: Cek apakah Node.js tersedia
set NODE_AVAILABLE=0
node -v >nul 2>&1
if not errorlevel 1 (
    set NODE_AVAILABLE=1
    echo [OK] Node.js terdeteksi - Model AI Server akan aktif
) else (
    echo [INFO] Node.js tidak ditemukan - Model AI hanya dari server PHP
)

:: Coba buka firewall (butuh admin, tapi kalau gagal tidak masalah)
echo.
echo [SETUP] Mencoba buka firewall...
netsh advfirewall firewall add rule name="Sistem Absensi Port 8000" dir=in action=allow protocol=TCP localport=8000 >nul 2>&1
if not errorlevel 1 (
    echo   Port 8000 - OK
) else (
    echo   Port 8000 - Lewati (sudah ada atau butuh Admin)
)

netsh advfirewall firewall add rule name="Sistem Absensi Port 8001" dir=in action=allow protocol=TCP localport=8001 >nul 2>&1
if not errorlevel 1 (
    echo   Port 8001 - OK
) else (
    echo   Port 8001 - Lewati (sudah ada atau butuh Admin)
)

:: Jalankan Model AI Server di background (Node.js port 8001)
if "%NODE_AVAILABLE%"=="1" (
    echo.
    echo [INFO] Menjalankan Model AI Server di port 8001...
    start "Model-AI-Server" /MIN node model-server.cjs
    echo [OK] Model AI Server aktif di port 8001
)

echo.
echo ============================================
echo   Server aktif di:
echo.
echo   PC ini    : http://127.0.0.1:8000
echo   Dari HP   : Buka browser, ketik IP PC ini
echo               contoh: http://192.168.x.x:8000
echo.
echo   QR Code   : http://127.0.0.1:8000/qr-absensi
echo ============================================
echo.
echo [INFO] Tekan Ctrl+C untuk menghentikan server.
echo [INFO] Jangan tutup jendela ini!
echo.
echo [TIPS] Jika HP tidak bisa akses:
echo        1. Jalankan file ini sebagai Administrator (klik kanan)
echo        2. Atau matikan Windows Firewall sementara
echo.

php artisan serve --host=0.0.0.0 --port=8000

:: Cleanup
taskkill /F /FI "WINDOWTITLE eq Model-AI-Server" >nul 2>&1
echo.
echo [INFO] Server sudah berhenti.
pause
