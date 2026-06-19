@extends('layouts.mobile')

@section('title', 'Rekam Wajah')
@section('subtitle', 'Registrasi Biometrik')
@section('page_title', 'Rekam Wajah')

@section('content')
<div class="flex flex-col items-center w-full h-full">

    {{-- Info Banner --}}
    <div class="w-full bg-blue-50 border border-blue-200 text-blue-800 p-4 rounded-2xl mb-5 flex items-start gap-3">
        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center shrink-0 mt-0.5">
            <i class="fa-solid fa-info text-blue-500"></i>
        </div>
        <div>
            <p class="text-sm font-bold">Penting!</p>
            <p class="text-xs mt-0.5 leading-relaxed">Perekaman wajah hanya dapat dilakukan <strong>1 kali</strong> dan tidak dapat diubah. Pastikan wajah terlihat jelas dan pencahayaan cukup.</p>
        </div>
    </div>

    {{-- Camera Container --}}
    <div class="relative w-full max-w-[300px] mx-auto bg-[#0f172a] rounded-3xl overflow-hidden aspect-[3/4] shadow-lg mb-5 border-4 border-white ring-1 ring-gray-200 flex items-center justify-center">
        
        <div id="loading" class="absolute inset-0 flex flex-col items-center justify-center bg-[#0f172a]/90 text-white z-20">
            <i class="fa-solid fa-circle-notch fa-spin text-4xl mb-3 text-blue-400"></i>
            <p class="text-sm font-medium">Memuat Model AI...</p>
            <p class="text-[10px] text-gray-400 mt-1 text-center px-6">Pertama kali butuh ~10 detik untuk download model.<br>Harap tunggu dan jangan tutup halaman.</p>
        </div>

        <video id="video" autoplay muted playsinline class="absolute inset-0 w-full h-full object-cover hidden transform scale-x-[-1]"></video>
        <canvas id="overlay" class="absolute inset-0 w-full h-full object-cover z-10 transform scale-x-[-1]"></canvas>
        
        {{-- Face Guide --}}
        <div class="absolute inset-0 z-5 pointer-events-none flex items-center justify-center opacity-20">
            <div class="w-36 h-44 border-2 border-dashed border-white rounded-full"></div>
        </div>
    </div>

    {{-- Status --}}
    <div class="w-full max-w-[300px] bg-white px-4 py-3 rounded-xl border border-gray-100 shadow-sm mb-4 text-center">
        <p id="status-text" class="text-sm text-gray-500 font-medium flex items-center justify-center gap-2">
            <i class="fa-solid fa-camera text-gray-400"></i> Posisikan wajah di tengah kamera
        </p>
    </div>

    {{-- Capture Button --}}
    <button id="capture-btn" disabled class="w-full max-w-[300px] bg-[#002D8B] hover:bg-[#001f63] text-white font-bold py-4 px-6 rounded-2xl transition-all shadow-md disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed flex items-center justify-center text-sm uppercase tracking-wider active:scale-95">
        <i class="fa-solid fa-camera-retro mr-2 text-lg"></i> Rekam & Simpan Wajah
    </button>

    {{-- Back Link --}}
    <a href="{{ route('guru.dashboard') }}" class="mt-4 text-sm text-gray-400 hover:text-gray-600 font-medium flex items-center gap-1">
        <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Beranda
    </a>

</div>
@endsection

@push('scripts')
<script src="{{ asset('js/face-api.min.js') }}"></script>

<script>
    const video = document.getElementById('video');
    const overlay = document.getElementById('overlay');
    const loading = document.getElementById('loading');
    const captureBtn = document.getElementById('capture-btn');
    const statusText = document.getElementById('status-text');

    let stream = null;
    let detectionInterval = null;

    // 1. Load model AI — Sequential loading (satu per satu, tidak barengan)
    const modelServerUrl = window.location.protocol + '//' + window.location.hostname + ':8001';
    const localModelUrl = '{{ asset("models") }}';

    async function loadModelsSequential(url) {
        await faceapi.nets.tinyFaceDetector.loadFromUri(url);
        await faceapi.nets.faceLandmark68Net.loadFromUri(url);
        await faceapi.nets.faceRecognitionNet.loadFromUri(url);
    }

    (async () => {
        // 1. Coba Model Server port 8001
        try {
            loading.querySelector('p:last-child').textContent = 'Memuat AI (server cepat)...';
            await Promise.race([
                loadModelsSequential(modelServerUrl),
                new Promise((_, reject) => setTimeout(() => reject(new Error('timeout')), 15000))
            ]);
            console.log('Models from port 8001');
            startVideo();
            return;
        } catch(e) { console.warn('Port 8001 failed:', e.message); }

        // 2. Fallback: Server lokal port 8000 (tanpa timeout, biarkan selesai)
        try {
            loading.querySelector('p:last-child').textContent = 'Memuat AI (server lokal)... harap tunggu';
            await loadModelsSequential(localModelUrl);
            console.log('Models from port 8000');
            startVideo();
            return;
        } catch(e) { console.error('Port 8000 also failed:', e.message); }

        loading.innerHTML = '<div class="flex flex-col items-center px-4"><i class="fa-solid fa-triangle-exclamation text-red-500 text-3xl mb-2"></i><p class="text-xs text-red-400 text-center">Gagal memuat model AI.</p><button onclick="location.reload()" class="mt-3 bg-blue-600 text-white text-xs font-bold px-5 py-2 rounded-lg">Coba Lagi</button></div>';
    })();

    // 2. Nyalakan kamera depan
    function startVideo() {
        navigator.mediaDevices.getUserMedia({ 
            video: { facingMode: 'user', width: { ideal: 480 }, height: { ideal: 640 } } 
        })
        .then(mediaStream => {
            stream = mediaStream;
            video.srcObject = mediaStream;
            video.classList.remove('hidden');
            loading.classList.add('hidden');
        })
        .catch(err => {
            console.error(err);
            loading.innerHTML = '<div class="flex flex-col items-center"><i class="fa-solid fa-camera-slash text-red-500 text-3xl mb-2"></i><p class="text-xs text-red-400 text-center px-4">Kamera tidak dapat diakses.<br>Mohon izinkan akses kamera.</p></div>';
        });
    }

    // 3. Deteksi wajah real-time
    video.addEventListener('play', () => {
        const displaySize = { width: video.videoWidth, height: video.videoHeight };
        faceapi.matchDimensions(overlay, displaySize);

        detectionInterval = setInterval(async () => {
            const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 224 }))
                .withFaceLandmarks()
                .withFaceDescriptor();
            
            const ctx = overlay.getContext('2d');
            ctx.clearRect(0, 0, overlay.width, overlay.height);

            if (detection) {
                const resizedDetections = faceapi.resizeResults(detection, displaySize);
                faceapi.draw.drawDetections(overlay, resizedDetections);
                faceapi.draw.drawFaceLandmarks(overlay, resizedDetections);
                
                captureBtn.disabled = false;
                captureBtn.classList.remove('bg-gray-200', 'disabled:text-gray-400');
                captureBtn.classList.add('bg-[#002D8B]');
                statusText.innerHTML = '<span class="text-green-600 font-bold flex items-center justify-center gap-2"><i class="fa-solid fa-face-smile"></i> Wajah terdeteksi! Klik rekam.</span>';
            } else {
                captureBtn.disabled = true;
                captureBtn.classList.add('bg-gray-200', 'disabled:text-gray-400');
                captureBtn.classList.remove('bg-[#002D8B]');
                statusText.innerHTML = '<span class="text-orange-500 flex items-center justify-center gap-2"><i class="fa-solid fa-spinner fa-spin"></i> Mencari wajah...</span>';
            }
        }, 150);
    });

    // 4. Tombol rekam diklik
    captureBtn.addEventListener('click', async () => {
        // Konfirmasi dulu karena tidak bisa diulang
        const confirm = await Swal.fire({
            title: '<span class="font-bold">Konfirmasi Perekaman</span>',
            html: '<span class="text-sm">Wajah yang direkam <strong>tidak dapat diubah</strong> selamanya. Apakah Anda yakin wajah sudah terlihat jelas?</span>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#002D8B',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Rekam Sekarang',
            cancelButtonText: 'Belum, Cek Lagi',
            customClass: { popup: 'rounded-2xl' }
        });

        if (!confirm.isConfirmed) return;

        captureBtn.disabled = true;
        captureBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Memproses...';

        // Ambil descriptor wajah terbaru
        const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 320 }))
            .withFaceLandmarks()
            .withFaceDescriptor();
        
        if (!detection) {
            Swal.fire({
                title: 'Gagal',
                html: '<span class="text-sm">Wajah tidak terdeteksi. Coba lagi.</span>',
                icon: 'error',
                confirmButtonColor: '#002D8B'
            });
            captureBtn.innerHTML = '<i class="fa-solid fa-camera-retro mr-2 text-lg"></i> Rekam & Simpan Wajah';
            captureBtn.disabled = false;
            return;
        }

        const descriptorArray = Array.from(detection.descriptor);
        const faceData = JSON.stringify(descriptorArray);

        // Kirim ke server
        fetch("{{ route('guru.rekam-wajah.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ face_descriptor: faceData })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Matikan kamera
                if (stream) stream.getTracks().forEach(track => track.stop());
                if (detectionInterval) clearInterval(detectionInterval);

                Swal.fire({
                    title: 'Berhasil!',
                    html: '<span class="text-sm">' + data.message + '</span>',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 2500,
                    customClass: { popup: 'rounded-2xl' }
                }).then(() => {
                    window.location.href = "{{ route('guru.dashboard') }}";
                });
            } else {
                Swal.fire({
                    title: 'Gagal',
                    html: '<span class="text-sm">' + data.message + '</span>',
                    icon: 'error',
                    confirmButtonColor: '#002D8B'
                });
                captureBtn.innerHTML = '<i class="fa-solid fa-camera-retro mr-2 text-lg"></i> Rekam & Simpan Wajah';
                captureBtn.disabled = false;
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                title: 'Error',
                html: '<span class="text-sm">Terjadi kesalahan jaringan.</span>',
                icon: 'error',
                confirmButtonColor: '#002D8B'
            });
            captureBtn.innerHTML = '<i class="fa-solid fa-camera-retro mr-2 text-lg"></i> Rekam & Simpan Wajah';
            captureBtn.disabled = false;
        });
    });
</script>
@endpush
