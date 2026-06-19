# ============================================================
# SCRIPT: Download semua library CDN ke lokal
# Jalankan script ini SEKALI SAJA saat ada internet
# Setelah itu, aplikasi bisa jalan 100% OFFLINE
# ============================================================
# Cara pakai:
#   1. Buka PowerShell
#   2. cd ke folder project ini
#   3. Ketik: powershell -ExecutionPolicy Bypass -File .\download-offline-libs.ps1
# ============================================================

Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  DOWNLOAD LIBRARY OFFLINE" -ForegroundColor Cyan
Write-Host "  Sistem Absensi Yayasan Tri Jaya" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

$publicDir = Join-Path $PSScriptRoot "public"

# Buat folder-folder yang dibutuhkan
$folders = @(
    "$publicDir\vendor\sweetalert2",
    "$publicDir\vendor\alpinejs",
    "$publicDir\vendor\qrcodejs",
    "$publicDir\vendor\apexcharts",
    "$publicDir\vendor\lottie-player",
    "$publicDir\vendor\tailwindcss",
    "$publicDir\vendor\fontawesome\css",
    "$publicDir\vendor\fontawesome\webfonts",
    "$publicDir\vendor\fonts"
)

foreach ($folder in $folders) {
    if (!(Test-Path $folder)) {
        New-Item -ItemType Directory -Path $folder -Force | Out-Null
    }
}
Write-Host "[OK] Semua folder vendor dibuat" -ForegroundColor Green

# --- Fungsi download ---
function Download-File {
    param([string]$Url, [string]$OutFile, [string]$Label)
    try {
        if ($Label) { Write-Host "  > $Label" -ForegroundColor Gray }
        [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
        Invoke-WebRequest -Uri $Url -OutFile $OutFile -UseBasicParsing -ErrorAction Stop
        $size = [math]::Round((Get-Item $OutFile).Length / 1024, 1)
        Write-Host "    [OK] ${size}KB" -ForegroundColor Green
        return $true
    } catch {
        Write-Host "    [GAGAL] $_" -ForegroundColor Red
        return $false
    }
}

# ===== 1. TailwindCSS Play CDN =====
Write-Host ""
Write-Host "[1/8] TailwindCSS Play CDN..." -ForegroundColor Yellow
Download-File "https://cdn.tailwindcss.com/3.4.17" "$publicDir\vendor\tailwindcss\tailwind.min.js" "tailwind.min.js"

# ===== 2. SweetAlert2 =====
Write-Host ""
Write-Host "[2/8] SweetAlert2..." -ForegroundColor Yellow
Download-File "https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js" "$publicDir\vendor\sweetalert2\sweetalert2.all.min.js" "sweetalert2.all.min.js"

# ===== 3. Alpine.js =====
Write-Host ""
Write-Host "[3/8] Alpine.js..." -ForegroundColor Yellow
Download-File "https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js" "$publicDir\vendor\alpinejs\alpinejs.min.js" "alpinejs.min.js"

# ===== 4. QRCode.js =====
Write-Host ""
Write-Host "[4/8] QRCode.js..." -ForegroundColor Yellow
Download-File "https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js" "$publicDir\vendor\qrcodejs\qrcode.min.js" "qrcode.min.js"

# ===== 5. ApexCharts =====
Write-Host ""
Write-Host "[5/8] ApexCharts..." -ForegroundColor Yellow
Download-File "https://cdn.jsdelivr.net/npm/apexcharts@latest/dist/apexcharts.min.js" "$publicDir\vendor\apexcharts\apexcharts.min.js" "apexcharts.min.js"

# ===== 6. Lottie Player =====
Write-Host ""
Write-Host "[6/8] Lottie Player..." -ForegroundColor Yellow
Download-File "https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js" "$publicDir\vendor\lottie-player\lottie-player.js" "lottie-player.js"

# ===== 7. Font Awesome 6.4.0 =====
Write-Host ""
Write-Host "[7/8] Font Awesome 6.4.0..." -ForegroundColor Yellow
Download-File "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" "$publicDir\vendor\fontawesome\css\all.min.css" "all.min.css"

$faWebfonts = @(
    "fa-solid-900.woff2",
    "fa-solid-900.ttf",
    "fa-regular-400.woff2",
    "fa-regular-400.ttf",
    "fa-brands-400.woff2",
    "fa-brands-400.ttf"
)
foreach ($font in $faWebfonts) {
    Download-File "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/$font" "$publicDir\vendor\fontawesome\webfonts\$font" $font
}

# ===== 8. Google Fonts (Montserrat & Poppins) =====
Write-Host ""
Write-Host "[8/8] Google Fonts (Montserrat & Poppins)..." -ForegroundColor Yellow

# Montserrat - setiap weight punya URL yang beda
$montserratFiles = @(
    @{w="300"; file="JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCtr6Hw0aXpsog.woff2"},
    @{w="400"; file="JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCuM73w5aXpsog.woff2"},
    @{w="500"; file="JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCtZ6Hw0aXpsog.woff2"},
    @{w="600"; file="JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCu173w5aXpsog.woff2"},
    @{w="700"; file="JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCuM73w5aXpsog.woff2"}
)
foreach ($m in $montserratFiles) {
    Download-File "https://fonts.gstatic.com/s/montserrat/v29/$($m.file)" "$publicDir\vendor\fonts\montserrat-$($m.w).woff2" "Montserrat $($m.w)"
}

# Poppins
$poppinsFiles = @(
    @{w="300"; file="pxiByp8kv8JHgFVrLDz8Z1xlFQ.woff2"},
    @{w="400"; file="pxiEyp8kv8JHgFVrJJfecg.woff2"},
    @{w="500"; file="pxiByp8kv8JHgFVrLGT9Z1xlFQ.woff2"},
    @{w="600"; file="pxiByp8kv8JHgFVrLEj6Z1xlFQ.woff2"},
    @{w="700"; file="pxiByp8kv8JHgFVrLCz7Z1xlFQ.woff2"},
    @{w="800"; file="pxiByp8kv8JHgFVrLDD4Z1xlFQ.woff2"}
)
foreach ($p in $poppinsFiles) {
    Download-File "https://fonts.gstatic.com/s/poppins/v22/$($p.file)" "$publicDir\vendor\fonts\poppins-$($p.w).woff2" "Poppins $($p.w)"
}

# --- Fix Font Awesome CSS agar pakai path lokal ---
Write-Host ""
Write-Host "[PATCH] Memperbaiki path di Font Awesome CSS..." -ForegroundColor Yellow
$faCss = Get-Content "$publicDir\vendor\fontawesome\css\all.min.css" -Raw -ErrorAction SilentlyContinue
if ($faCss) {
    $faCss = $faCss -replace '\.\./webfonts/', '/vendor/fontawesome/webfonts/'
    Set-Content "$publicDir\vendor\fontawesome\css\all.min.css" $faCss -NoNewline
    Write-Host "  [OK] Path webfonts diperbaiki ke /vendor/fontawesome/webfonts/" -ForegroundColor Green
}

Write-Host ""
Write-Host "============================================" -ForegroundColor Green
Write-Host "  SELESAI! Semua library sudah didownload" -ForegroundColor Green
Write-Host "  Aplikasi sekarang bisa jalan 100% OFFLINE" -ForegroundColor Green
Write-Host "============================================" -ForegroundColor Green
Write-Host ""
Write-Host "Selanjutnya jalankan:" -ForegroundColor Cyan
Write-Host "  .\start-server.bat" -ForegroundColor White
Write-Host ""
pause
