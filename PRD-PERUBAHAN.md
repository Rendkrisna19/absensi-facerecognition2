# PRD — Perubahan Sistem Absensi: QR Code & Pengaturan LAN

## Ringkasan Perubahan

Mengganti fitur "Pengaturan LAN" menjadi halaman **QR Code Absensi** yang otomatis generate dari IP server. Guru hanya bisa absen jika terhubung ke jaringan WiFi yang sudah didaftarkan oleh admin. QR Code ditampilkan di layar monitor PC sekolah sebagai pintu masuk halaman absensi.

---

## Alur Sistem Baru

```
Admin daftarkan WiFi sekolah → Server deteksi IP sendiri → Generate QR Code
→ QR ditampilkan di layar PC → Guru connect WiFi → Scan QR → Halaman absensi
→ Validasi IP (harus di jaringan terdaftar) → Scan wajah → Absensi tersimpan
```

---

## PHASE 1: Ubah Pengaturan LAN → Halaman QR Code Absensi

### Tujuan
Mengubah halaman "Pengaturan LAN" yang tadinya untuk mengelola daftar IP menjadi halaman yang menampilkan **QR Code absensi** + **pengaturan jaringan WiFi** yang lebih sederhana.

### Requirement

| No | Requirement | Detail |
|---|---|---|
| 1.1 | **Halaman QR Code Absensi** | Buat halaman baru `/qr-absensi` yang bisa diakses **tanpa login** (karena ditampilkan di layar monitor publik sekolah). Halaman ini menampilkan QR Code besar, jam real-time, dan tanggal. |
| 1.2 | **Auto-Detect IP Server** | Sistem otomatis mendeteksi IP address server yang sedang aktif menggunakan PHP (`$_SERVER['SERVER_ADDR']` atau method Laravel). |
| 1.3 | **Generate QR Code dari IP** | QR Code berisi URL: `http://{IP_SERVER}/guru/scan-absensi`. Gunakan library QR Code JavaScript (qrcode.js via CDN). |
| 1.4 | **Auto-Refresh** | Halaman QR Code auto-refresh setiap 30 detik untuk mengantisipasi perubahan IP (DHCP). |
| 1.5 | **Validasi IP vs Jaringan Terdaftar** | QR Code hanya muncul jika IP server cocok dengan salah satu jaringan WiFi yang sudah didaftarkan admin. Jika tidak cocok, tampilkan pesan error. |
| 1.6 | **UI Halaman QR** | Tampilan fullscreen, background gelap, QR Code besar di tengah, jam digital, tanggal, nama sekolah. Cocok untuk ditampilkan di monitor/TV sekolah. |

### File yang Dibuat/Diubah

| File | Aksi |
|---|---|
| `app/Http/Controllers/QrCodeController.php` | **BARU** — Controller untuk halaman QR Code |
| `resources/views/qr-absensi.blade.php` | **BARU** — View halaman QR Code (fullscreen, tanpa layout app) |
| `routes/web.php` | **UBAH** — Tambah route `/qr-absensi` (tanpa auth middleware) |

---

## PHASE 2: Sederhanakan Pengaturan LAN

### Tujuan
Menyederhanakan fitur Pengaturan LAN. Tidak perlu lagi input IP manual satu per satu. Admin cukup daftarkan **nama WiFi dan subnet pattern** saja.

### Requirement

| No | Requirement | Detail |
|---|---|---|
| 2.1 | **Sederhanakan Form Input** | Admin hanya input: Nama WiFi, dan pola IP sederhana (contoh: `192.168.1.%` atau cukup `192.168.1`). Sistem otomatis convert ke pattern matching. |
| 2.2 | **Auto-Detect IP Admin** | Saat admin buat jaringan baru, sistem otomatis suggest IP berdasarkan IP admin yang sedang login. |
| 2.3 | **Tampilkan Status Server** | Di halaman pengaturan, tampilkan IP server saat ini dan apakah sudah cocok dengan jaringan terdaftar. |
| 2.4 | **Hapus Master Bypass** | Hapus fitur master bypass switch (`*`). Tidak diperlukan lagi karena QR Code sudah jadi pintu masuk. |
| 2.5 | **Integrasi dengan Halaman QR** | Tambahkan tombol/link di halaman pengaturan untuk langsung buka halaman QR Code di tab baru. |

### File yang Dibuat/Diubah

| File | Aksi |
|---|---|
| `app/Http/Controllers/Admin/PengaturanLanController.php` | **UBAH** — Sederhanakan, hapus master bypass, tambahkan auto-detect IP server |
| `resources/views/admin/pengaturan_lan/index.blade.php` | **UBAH** — Tambah info IP server, link ke halaman QR, status koneksi |
| `resources/views/admin/pengaturan_lan/create.blade.php` | **UBAH** — Sederhanakan form input |
| `resources/views/admin/pengaturan_lan/edit.blade.php` | **UBAH** — Sederhanakan form input |

---

## PHASE 3: Pertahankan Validasi IP di Scan Absensi

### Tujuan
Validasi IP di `ScanAbsensiController` tetap dipertahankan sebagai **lapis keamanan kedua**. Meskipun QR Code sudah jadi pintu masuk, validasi IP tetap dicek saat guru submit absensi.

### Requirement

| No | Requirement | Detail |
|---|---|---|
| 3.1 | **Pertahankan cekJaringanWifi()** | Method ini tetap ada sebagai secondary validation. Kalau somehow guru buka URL tanpa QR (misal ketik manual), validasi IP tetap jalan. |
| 3.2 | **Pertahankan Semua Validasi Lain** | Validasi libur semester, hari Minggu, libur nasional API, waktu absen, izin — semua tetap jalan normal. |
| 3.3 | **Pertahankan Face Scan** | Proses scan wajah tetap sama, tidak ada perubahan. |

### File yang Dibuat/Diubah

| File | Aksi |
|---|---|
| `app/Http/Controllers/Guru/ScanAbsensiController.php` | **TIDAK BERUBAH** — semua validasi tetap dipertahankan |

---

## PHASE 4: Tambahkan Link QR di Sidebar & Dashboard Admin

### Tujuan
Memudahkan admin mengakses halaman QR Code dari dashboard.

### Requirement

| No | Requirement | Detail |
|---|---|---|
| 4.1 | **Menu Sidebar** | Tambahkan menu "QR Code Absensi" di sidebar admin, dengan ikon QR Code. |
| 4.2 | **Card di Dashboard** | Tambahkan card/widget di dashboard admin yang menampilkan preview kecil QR Code + tombol "Tampilkan Fullscreen". |
| 4.3 | **Rename Menu** | Rename menu "Pengaturan LAN" menjadi "Pengaturan Jaringan" atau "Jaringan WiFi" supaya lebih jelas. |

### File yang Dibuat/Diubah

| File | Aksi |
|---|---|
| `resources/views/components/sidebar.blade.php` | **UBAH** — Tambah menu QR Code, rename Pengaturan LAN |
| `app/Http/Controllers/Admin/DashboardController.php` | **UBAH** — Tambah data QR URL untuk card di dashboard |
| `resources/views/admin/dashboard/index.blade.php` | **UBAH** — Tambah card QR Code preview |

---

## PHASE 5: Guru Tidak Bisa Daftar Sendiri

### Tujuan
Memastikan hanya admin yang bisa mendaftarkan akun guru. Tidak ada fitur registrasi mandiri.

### Requirement

| No | Requirement | Detail |
|---|---|---|
| 5.1 | **Tidak Ada Halaman Register** | Pastikan tidak ada route `/register` atau halaman registrasi publik. |
| 5.2 | **Hanya Admin yang Buat Akun** | Guru dibuat melalui menu "Data Guru" oleh admin (sudah ada saat ini). |
| 5.3 | **Verifikasi** | Cek semua route dan pastikan tidak ada celah registrasi mandiri. |

### File yang Dibuat/Diubah

| File | Aksi |
|---|---|
| `routes/web.php` | **CEK** — Pastikan tidak ada route register |
| `resources/views/auth/` | **CEK** — Pastikan tidak ada form register |

> **Catatan:** Phase ini kemungkinan besar sudah terpenuhi karena project saat ini memang tidak punya fitur register publik. Cukup verifikasi saja.

---

## Urutan Pengerjaan

```
PHASE 1 ──→ PHASE 2 ──→ PHASE 3 ──→ PHASE 4 ──→ PHASE 5
(QR Page)   (Sederhana   (Verifikasi  (Sidebar &   (Verifikasi
 Baru)       LAN)         Scan)        Dashboard)   Register)
```

**Estimasi:**
- Phase 1: Paling besar — buat controller, view fullscreen, QR generation, auto-refresh
- Phase 2: Sedang — modifikasi existing code
- Phase 3: Kecil — hanya verifikasi, tidak ada perubahan kode
- Phase 4: Kecil — tambah menu dan card
- Phase 5: Kecil — hanya verifikasi

---

## Yang TIDAK BERUBAH

Fitur-fitur berikut tetap sama persis, tidak disentuh:

- Face Recording (enrollment wajah guru)
- Face Scan Absensi (proses scan wajah)
- Dashboard Guru, Kepala Yayasan
- Manajemen Data Guru (CRUD + Import/Export)
- Pengaturan Absensi (jam & denda)
- Pengajuan Izin
- Libur Semester
- Riwayat Absensi & Denda
- Laporan & Export (PDF/Excel)
- Manajemen User
- Validasi Libur Nasional (API libur.deno.dev)
- Auto-backfill Alpa
- Autentikasi (Login NIK + Password)
- PWA (Service Worker, manifest)
