@extends('layouts.app')

@section('title', 'Perekaman Wajah')
@section('page_title', 'Registrasi Wajah Guru')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    .font-poppins { font-family: 'Poppins', sans-serif !important; }
</style>
@endpush

@section('content')
<div class="max-w-3xl mx-auto font-poppins">
    
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        <div class="p-5 md:p-6 border-b border-gray-100 flex justify-between items-center bg-white">
            <div>
                <h3 class="text-lg md:text-xl font-bold text-gray-800">Perekaman Wajah</h3>
                <p class="text-sm font-medium text-[#1e3b8b] mt-0.5">{{ $guru->name }} <span class="text-gray-400 font-normal">| NIK: {{ $guru->nik ?? $guru->username }}</span></p>
            </div>
            <a href="{{ route('admin.face.index') }}" class="flex items-center justify-center w-10 h-10 bg-gray-50 text-gray-500 hover:bg-gray-100 hover:text-gray-800 rounded-xl transition-colors shadow-sm" title="Kembali">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>

        <div class="p-6 md:p-8 flex flex-col items-center bg-gray-50/30">
            
            <div class="relative w-full max-w-[400px] mx-auto bg-[#0f172a] rounded-2xl overflow-hidden aspect-[3/4] shadow-md mb-6 border-[5px] border-white flex items-center justify-center ring-1 ring-gray-200">
                
                <div id="loading" class="absolute inset-0 flex flex-col items-center justify-center bg-[#0f172a]/90 text-white z-20 backdrop-blur-sm">
                    <i class="fa-solid fa-circle-notch fa-spin text-4xl mb-4 text-[#3b82f6]"></i>
                    <p class="text-sm font-medium animate-pulse tracking-wide">Memuat Model AI...</p>
                    <p class="text-[10px] text-gray-400 mt-2">Mohon tunggu sebentar</p>
                </div>

                <video id="video" autoplay muted playsinline class="absolute inset-0 w-full h-full object-cover hidden"></video>
                
                <canvas id="overlay" class="absolute inset-0 w-full h-full object-cover z-10"></canvas>
            </div>

            <div class="w-full max-w-[400px] text-center space-y-5 mx-auto">
                <div class="bg-white px-4 py-3 rounded-xl border border-gray-100 shadow-sm">
                    <p id="status-text" class="text-sm text-gray-500 font-medium flex items-center justify-center gap-2">
                        <i class="fa-solid fa-camera text-gray-400"></i> Pastikan wajah berada di tengah kamera.
                    </p>
                </div>
                
                <button id="capture-btn" disabled class="w-full bg-[#1e3b8b] hover:bg-[#152b69] text-white font-bold py-3.5 px-6 rounded-xl transition-all shadow-sm disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed flex items-center justify-center text-sm uppercase tracking-wider">
                    <i class="fa-solid fa-expand mr-2 text-lg"></i> Rekam & Simpan Wajah
                </button>
            </div>

        </div>
    </div>
</div>

<script src="{{ asset('js/face-api.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // --- LOGIC JAVASCRIPT TIDAK DIRUBAH SAMA SEKALI ---
    const video = document.getElementById('video');
    const overlay = document.getElementById('overlay');
    const loading = document.getElementById('loading');
    const captureBtn = document.getElementById('capture-btn');
    const statusText = document.getElementById('status-text');

    let stream = null;

    // 1. Load semua model secara lokal
    const modelUrl = '{{ asset("models") }}';
    Promise.all([
        faceapi.nets.ssdMobilenetv1.loadFromUri(modelUrl),
        faceapi.nets.faceLandmark68Net.loadFromUri(modelUrl),
        faceapi.nets.faceRecognitionNet.loadFromUri(modelUrl)
    ]).then(startVideo).catch(err => {
        console.error(err);
        Swal.fire({
            title: '<span class="font-poppins">Error</span>',
            html: '<span class="font-poppins">Gagal memuat model face-api. Pastikan folder /models sudah ada.</span>',
            icon: 'error'
        });
    });

    // 2. Hidupkan Kamera
    function startVideo() {
        navigator.mediaDevices.getUserMedia({ video: { width: 720, height: 960 } })
            .then(mediaStream => {
                stream = mediaStream;
                video.srcObject = mediaStream;
                video.classList.remove('hidden');
                loading.classList.add('hidden');
            })
            .catch(err => {
                console.error(err);
                loading.innerHTML = '<div class="flex flex-col items-center"><i class="fa-solid fa-triangle-exclamation text-red-500 text-4xl mb-3"></i><p class="text-sm text-red-400 text-center px-4 font-medium">Kamera tidak dapat diakses.<br>Pastikan izin kamera diberikan.</p></div>';
            });
    }

    // 3. Proses deteksi saat video jalan (Real-time tracking)
    video.addEventListener('play', () => {
        const displaySize = { width: video.videoWidth, height: video.videoHeight };
        faceapi.matchDimensions(overlay, displaySize);

        setInterval(async () => {
            const detection = await faceapi.detectSingleFace(video).withFaceLandmarks().withFaceDescriptor();
            
            const ctx = overlay.getContext('2d');
            ctx.clearRect(0, 0, overlay.width, overlay.height);

            if (detection) {
                const resizedDetections = faceapi.resizeResults(detection, displaySize);
                // Gambar kerangka wajah
                faceapi.draw.drawDetections(overlay, resizedDetections);
                faceapi.draw.drawFaceLandmarks(overlay, resizedDetections);
                
                captureBtn.disabled = false;
                captureBtn.classList.replace('bg-gray-200', 'bg-[#1e3b8b]');
                captureBtn.classList.remove('disabled:text-gray-400');
                statusText.innerHTML = '<span class="text-green-600 font-bold flex items-center justify-center gap-2"><i class="fa-solid fa-face-smile text-lg"></i> Wajah terdeteksi! Silakan klik rekam.</span>';
            } else {
                captureBtn.disabled = true;
                captureBtn.classList.replace('bg-[#1e3b8b]', 'bg-gray-200');
                captureBtn.classList.add('disabled:text-gray-400');
                statusText.innerHTML = '<span class="text-orange-500 flex items-center justify-center gap-2"><i class="fa-solid fa-spinner fa-spin"></i> Mencari wajah...</span>';
            }
        }, 100); // refresh tiap 100ms
    });

    // 4. Tombol Rekam Diklik
    captureBtn.addEventListener('click', async () => {
        captureBtn.disabled = true;
        captureBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Memproses...';

        const detection = await faceapi.detectSingleFace(video).withFaceLandmarks().withFaceDescriptor();
        
        if (!detection) {
            Swal.fire({
                title: '<span class="font-poppins">Gagal</span>',
                html: '<span class="font-poppins text-sm">Wajah tidak terdeteksi dengan jelas. Ulangi kembali.</span>',
                icon: 'error',
                confirmButtonColor: '#1e3b8b'
            });
            captureBtn.innerHTML = '<i class="fa-solid fa-expand mr-2 text-lg"></i> Rekam & Simpan Wajah';
            return;
        }

        // Ekstrak array titik wajah jadi text/JSON
        const descriptorArray = Array.from(detection.descriptor);
        const faceData = JSON.stringify(descriptorArray);

        // Kirim ke backend Laravel
        fetch("{{ route('admin.face.store', $guru->id) }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                face_descriptor: faceData
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Matikan kamera
                if(stream) stream.getTracks().forEach(track => track.stop());
                
                Swal.fire({
                    title: '<span class="font-poppins font-bold">Berhasil!</span>',
                    html: `<span class="font-poppins text-sm">${data.message}</span>`,
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 2000,
                    customClass: { popup: 'rounded-2xl' }
                }).then(() => {
                    window.location.href = "{{ route('admin.face.index') }}";
                });
            } else {
                Swal.fire({
                    title: '<span class="font-poppins">Error</span>',
                    html: `<span class="font-poppins text-sm">${data.message}</span>`,
                    icon: 'error',
                    confirmButtonColor: '#1e3b8b'
                });
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                title: '<span pclass="font-poppins">Error</span>',
                html: '<span class="font-poppins text-sm">Terjadi kesalahan jaringan.</span>',
                icon: 'error',
                confirmButtonColor: '#1e3b8b'
            });
        })
        .finally(() => {
            captureBtn.innerHTML = '<i class="fa-solid fa-expand mr-2 text-lg"></i> Rekam & Simpan Wajah';
            captureBtn.disabled = false;
        });
    });
</script>
@endsection