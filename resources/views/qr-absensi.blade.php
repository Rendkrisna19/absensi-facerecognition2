<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Absensi - Tri Jaya</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #0a1628 0%, #002D8B 50%, #001a52 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            overflow: hidden;
        }

        /* Animated background particles */
        .bg-particle {
            position: fixed;
            border-radius: 50%;
            background: rgba(255,255,255,0.03);
            animation: float 20s infinite ease-in-out;
        }
        .bg-particle:nth-child(1) { width: 400px; height: 400px; top: -100px; left: -100px; animation-delay: 0s; }
        .bg-particle:nth-child(2) { width: 300px; height: 300px; bottom: -80px; right: -80px; animation-delay: 5s; }
        .bg-particle:nth-child(3) { width: 200px; height: 200px; top: 50%; left: 60%; animation-delay: 10s; }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }

        .container {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 24px;
            padding: 20px;
        }

        /* School Logo & Title */
        .header {
            text-align: center;
            margin-bottom: 8px;
        }
        .header img {
            width: 64px;
            height: 64px;
            object-fit: contain;
            margin-bottom: 12px;
            filter: drop-shadow(0 4px 12px rgba(0,0,0,0.3));
        }
        .header h1 {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            text-shadow: 0 2px 20px rgba(0,0,0,0.3);
        }
        .header p {
            font-size: 14px;
            font-weight: 400;
            opacity: 0.7;
            margin-top: 4px;
            letter-spacing: 4px;
            text-transform: uppercase;
        }

        /* QR Code Card */
        .qr-card {
            background: white;
            border-radius: 32px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4), 0 0 0 1px rgba(255,255,255,0.1);
            position: relative;
            overflow: hidden;
        }
        .qr-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #002D8B, #3b82f6, #002D8B);
        }
        .qr-card .label {
            text-align: center;
            margin-bottom: 20px;
        }
        .qr-card .label span {
            display: inline-block;
            background: #002D8B;
            color: white;
            font-size: 11px;
            font-weight: 700;
            padding: 6px 16px;
            border-radius: 20px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        #qr-canvas {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            border-radius: 12px;
            min-height: 260px;
        }
        #qr-canvas img, #qr-canvas canvas {
            border-radius: 12px;
        }
        .qr-url {
            text-align: center;
            margin-top: 16px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: #64748b;
            background: #f1f5f9;
            padding: 8px 16px;
            border-radius: 8px;
            word-break: break-all;
        }

        /* Info Section */
        .info-section {
            text-align: center;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* Clock */
        .clock {
            font-size: 52px;
            font-weight: 900;
            letter-spacing: 4px;
            text-shadow: 0 4px 20px rgba(0,0,0,0.3);
            line-height: 1;
        }
        .date {
            font-size: 16px;
            font-weight: 500;
            opacity: 0.8;
            letter-spacing: 1px;
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 16px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .status-badge.online {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .status-badge.offline {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        /* IP Info */
        .ip-info {
            font-size: 12px;
            opacity: 0.5;
            font-family: 'Courier New', monospace;
        }

        /* Instruction */
        .instruction {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 20px;
            padding: 20px 28px;
            max-width: 500px;
            text-align: center;
        }
        .instruction h3 {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
            opacity: 0.9;
        }
        .instruction .steps {
            display: flex;
            flex-direction: column;
            gap: 8px;
            text-align: left;
        }
        .instruction .step {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            font-weight: 500;
            opacity: 0.85;
        }
        .instruction .step-num {
            width: 28px;
            height: 28px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 800;
            flex-shrink: 0;
        }

        /* Refresh indicator */
        .refresh-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            height: 3px;
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
            animation: refreshBar 30s linear infinite;
            z-index: 100;
        }
        @keyframes refreshBar {
            0% { width: 0%; }
            100% { width: 100%; }
        }
        .refresh-text {
            position: fixed;
            bottom: 12px;
            right: 16px;
            font-size: 10px;
            opacity: 0.3;
            z-index: 100;
        }

    </style>
</head>
<body>

    <!-- Background particles -->
    <div class="bg-particle"></div>
    <div class="bg-particle"></div>
    <div class="bg-particle"></div>

    {{-- QR Code selalu tampil — IP otomatis dari DHCP server --}}
    <div class="container">

        <div class="header">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
            <h1>Sistem Absensi</h1>
            <p>Yayasan Tri Jaya</p>
        </div>

        <div class="info-section">
            <div class="clock" id="clock">--:--:--</div>
            <div class="date" id="date">Memuat tanggal...</div>
            <div>
                <span class="status-badge online">
                    <i class="fa-solid fa-wifi"></i>
                    Server IP: {{ $serverIp }}
                </span>
            </div>
        </div>

        <div class="qr-card">
            <div class="label">
                <span><i class="fa-solid fa-qrcode" style="margin-right: 6px;"></i>Scan untuk Absensi</span>
            </div>
            <div id="qr-canvas"></div>
            <div class="qr-url">{{ $absensiUrl }}</div>
        </div>

        <div class="instruction">
            <h3><i class="fa-solid fa-list-ol" style="margin-right: 6px;"></i> Cara Absensi</h3>
            <div class="steps">
                <div class="step">
                    <div class="step-num">1</div>
                    <span>Pastikan HP terhubung ke WiFi sekolah</span>
                </div>
                <div class="step">
                    <div class="step-num">2</div>
                    <span>Scan QR Code di atas menggunakan kamera HP</span>
                </div>
                <div class="step">
                    <div class="step-num">3</div>
                    <span>Login dan lakukan scan wajah untuk absensi</span>
                </div>
            </div>
        </div>

        <div class="ip-info">
            Server: {{ $serverIp }}
        </div>
    </div>

    <!-- Refresh bar -->
    <div class="refresh-bar"></div>
    <div class="refresh-text">Auto-refresh setiap 30 detik</div>

    {{-- QR Code Generator Library (Browser-compatible) --}}
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

    <script>
        // 1. Generate QR Code — IP otomatis dari DHCP
        const absensiUrl = @json($absensiUrl);
        const qrContainer = document.getElementById('qr-canvas');

        if (typeof QRCode !== 'undefined') {
            new QRCode(qrContainer, {
                text: absensiUrl,
                width: 260,
                height: 260,
                colorDark: '#002D8B',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
        } else {
            qrContainer.innerHTML = '<p style="color: #ef4444; text-align: center; padding: 40px;">Library QR gagal dimuat. Periksa koneksi internet.</p>';
        }

        // 2. Real-time Clock
        function updateClock() {
            const now = new Date();
            const time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            const date = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            
            document.getElementById('clock').textContent = time + ' WIB';
            document.getElementById('date').textContent = date;
        }
        updateClock();
        setInterval(updateClock, 1000);

        // 3. Auto-refresh setiap 30 detik
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>

</body>
</html>
