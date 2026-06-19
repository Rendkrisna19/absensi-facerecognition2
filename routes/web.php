<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QrCodeController;

//import controller admin
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\FaceRecordingController;
use App\Http\Controllers\Admin\PengaturanLanController;
use App\Http\Controllers\Admin\PengaturanAbsensiController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LiburSemesterController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RiwayatAbsensiController;

//import role kepala-yayasan
use App\Http\Controllers\KepalaYayasan\DashboardYayasanController;
use App\Http\Controllers\KepalaYayasan\LaporanAbsensiController;
use App\Http\Controllers\KepalaYayasan\PotonganController;

//impoert role guru
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Guru\ScanAbsensiController;
use App\Http\Controllers\Guru\RiwayatController;
use App\Http\Controllers\Guru\DendaController;
use App\Http\Controllers\Guru\PengaturanController;
use App\Http\Controllers\Guru\PengajuanIzinController;
use App\Http\Controllers\Guru\RekamWajahController;

Route::get('/setup-storage', function () {
    Artisan::call('storage:link');
    return 'Storage berhasil dilink!';
});



Route::redirect('/', '/login');

// QR Code Absensi (Tanpa Login — ditampilkan di layar monitor PC sekolah)
Route::get('/qr-absensi', [QrCodeController::class, 'index'])->name('qr-absensi');



// 2. Route Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/profil-saya', [ProfileController::class, 'index'])->name('profile.index');
Route::put('/profil-saya', [ProfileController::class, 'update'])->name('profile.update');
Route::get('/notifications/read-all', function() {
    \Illuminate\Support\Facades\DB::table('notifications')->update(['is_read' => 1]);
    return back();
})->name('notifications.readAll');

// 3. Group Routes: ADMIN
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard/semua-absen', [DashboardController::class, 'semuaAbsen'])->name('admin.dashboard.semua-absen');
    Route::get('/guru/download-template', [GuruController::class, 'downloadTemplate'])->name('admin.guru.downloadTemplate');
    Route::post('/guru/import-excel', [GuruController::class, 'importExcel'])->name('admin.guru.importExcel');
    Route::resource('guru', GuruController::class)->names('admin.guru');
    Route::get('/face-recording', [FaceRecordingController::class, 'index'])->name('admin.face.index');
    Route::get('/face-recording/{guru}/record', [FaceRecordingController::class, 'record'])->name('admin.face.record');
    Route::post('/face-recording/{guru}/store', [FaceRecordingController::class, 'store'])->name('admin.face.store');
    Route::resource('pengaturan-lan', PengaturanLanController::class)->names('admin.pengaturan-lan');
    Route::post('/pengaturan-lan/toggle/{id}', [PengaturanLanController::class, 'toggleStatus'])->name('admin.pengaturan-lan.toggle');
    Route::get('/pengaturan-absensi', [PengaturanAbsensiController::class, 'index'])->name('admin.pengaturan-absensi.index');
    Route::post('/pengaturan-absensi', [PengaturanAbsensiController::class, 'store'])->name('admin.pengaturan-absensi.store');
    Route::resource('user', UserController::class, ['as' => 'admin']);
    Route::get('/admin/guru/export/excel', [GuruController::class, 'exportExcel'])->name('admin.guru.export.excel');
    Route::get('/admin/guru/export/pdf', [GuruController::class, 'exportPdf'])->name('admin.guru.export.pdf');
    Route::get('/admin/guru/{guru}/print', [GuruController::class, 'print'])->name('admin.guru.print');
    Route::get('/user', [UserController::class, 'index'])->name('admin.user.index');
    Route::get('/user/create', [UserController::class, 'create'])->name('admin.user.create');
    Route::post('/user', [UserController::class, 'store'])->name('admin.user.store');
    Route::get('/user/{user}/edit', [UserController::class, 'edit'])->name('admin.user.edit');
    Route::put('/user/{user}', [UserController::class, 'update'])->name('admin.user.update');
    Route::delete('/user/{user}', [UserController::class, 'destroy'])->name('admin.user.destroy');
    Route::resource('libur-semester', LiburSemesterController::class, ['as' => 'admin']);
    Route::get('/libur-semester', [LiburSemesterController::class, 'index'])->name('admin.libur-semester.index');
    Route::post('/libur-semester', [LiburSemesterController::class, 'store'])->name('admin.libur.store');
    Route::put('/libur-semester/{libur}', [LiburSemesterController::class, 'update'])->name('admin.libur.update');
    Route::delete('/libur-semester/{libur}', [LiburSemesterController::class, 'destroy'])->name('admin.libur.destroy');
    Route::get('/pengajuan-izin', [App\Http\Controllers\Admin\PengajuanIzinController::class, 'index'])->name('admin.pengajuan-izin.index');
    Route::post('/pengajuan-izin/{id}/status', [App\Http\Controllers\Admin\PengajuanIzinController::class, 'updateStatus'])->name('admin.pengajuan-izin.status');
    Route::post('/pengaturan-lan/toggle/{id}', [\App\Http\Controllers\Admin\PengaturanLanController::class, 'toggleStatus'])->name('admin.pengaturan-lan.toggle');
    Route::get('/riwayat-absensi', [RiwayatAbsensiController::class, 'index'])->name('admin.riwayat-absensi.index');
    Route::get('/riwayat-absensi/pdf', [RiwayatAbsensiController::class, 'exportPdf'])->name('admin.riwayat-absensi.pdf');
    Route::get('/riwayat-absensi/excel', [RiwayatAbsensiController::class, 'exportExcel'])->name('admin.riwayat-absensi.excel');
    Route::post('/riwayat-absensi/cleanup', [RiwayatAbsensiController::class, 'cleanup'])->name('admin.riwayat-absensi.cleanup');
    Route::post('/pengajuan-izin/cleanup', [App\Http\Controllers\Admin\PengajuanIzinController::class, 'cleanup'])->name('admin.pengajuan-izin.cleanup');
});

// 4. Group Routes: KEPALA YAYASAN
Route::middleware(['auth', 'role:kepala_yayasan'])->prefix('yayasan')->group(function () {
    Route::get('/dashboard', [DashboardYayasanController::class, 'index'])->name('yayasan.dashboard');
    Route::get('/laporan-kehadiran', [LaporanAbsensiController::class, 'index'])->name('yayasan.laporan.index');
    Route::get('/yayasan/potongan', [PotonganController::class, 'index'])->name('yayasan.potongan.index');
    Route::get('/yayasan/potongan/pdf', [PotonganController::class, 'exportPdf'])->name('yayasan.potongan.pdf');
    Route::get('/yayasan/potongan/excel', [PotonganController::class, 'exportExcel'])->name('yayasan.potongan.excel');
    Route::get('/yayasan/laporan/pdf', [LaporanAbsensiController::class, 'exportPdf'])->name('yayasan.laporan.pdf');
    Route::get('/yayasan/laporan/excel', [LaporanAbsensiController::class, 'exportExcel'])->name('yayasan.laporan.excel');
});

// 5. Group Routes: GURU
// Rute untuk menerima lemparan dari Lokal (Tidak butuh auth karena divalidasi via Token Enkripsi)
Route::get('/guru/scan-online', [ScanAbsensiController::class, 'onlineScan'])->name('guru.scan.online');
Route::post('/guru/scan-online/store', [ScanAbsensiController::class, 'onlineStore'])->name('guru.scan.online.store');

Route::middleware(['auth', 'role:guru'])->prefix('guru')->group(function () {
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('guru.dashboard');
    Route::get('/scan-absensi', [ScanAbsensiController::class, 'index'])->name('guru.scan');
    Route::post('/scan-absensi/store', [ScanAbsensiController::class, 'store'])->name('guru.scan.store');
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('guru.riwayat');
    Route::get('/denda', [DendaController::class, 'index'])->name('guru.denda');
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('guru.pengaturan');
    Route::post('/pengaturan/update-profil', [PengaturanController::class, 'updateProfil'])->name('guru.pengaturan.update');
    Route::get('/pengajuan-izin', [PengajuanIzinController::class, 'index'])->name('guru.pengajuan-izin.index');
    Route::get('/pengajuan-izin/create', [PengajuanIzinController::class, 'create'])->name('guru.pengajuan-izin.create');
    Route::post('/pengajuan-izin', [PengajuanIzinController::class, 'store'])->name('guru.pengajuan-izin.store');
    Route::get('/pengajuan-izin/{pengajuanIzin}', [PengajuanIzinController::class, 'show'])->name('guru.pengajuan-izin.show');

    // Rekam Wajah Mandiri (hanya 1x, tidak bisa edit)
    Route::get('/rekam-wajah', [RekamWajahController::class, 'index'])->name('guru.rekam-wajah');
    Route::post('/rekam-wajah/store', [RekamWajahController::class, 'store'])->name('guru.rekam-wajah.store');
});
