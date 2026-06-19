# Analisa Lengkap Project — Sistem Absensi Tri Jaya (Face Recognition)

## 1. Ringkasan Project

Sistem Absensi Berbasis Pengenalan Wajah (Face Recognition) untuk Yayasan Pendidikan Tri Jaya. Sistem ini dirancang sebagai **Progressive Web App (PWA)** yang berjalan di browser mobile dan desktop, memungkinkan guru melakukan absensi harian menggunakan teknologi biometrik wajah secara real-time, dengan validasi jaringan WiFi sekolah, serta perhitungan denda keterlambatan dan ketidakhadiran secara otomatis.

---

## 2. Teknologi yang Digunakan

### 2.1 Backend
| Teknologi | Versi | Kegunaan |
|---|---|---|
| **Laravel** | 11.x | Framework utama backend (PHP) |
| **PHP** | ^8.2 | Bahasa pemrograman server-side |
| **MySQL** | - | Database relasional |
| **Laravel Excel (Maatwebsite)** | ^3.1 | Import/Export data Excel & CSV |
| **Laravel DomPDF (Barryvdh)** | ^3.1 | Generate laporan dalam format PDF |
| **Carbon** | (built-in) | Manipulasi tanggal & waktu (timezone Asia/Jakarta) |

### 2.2 Frontend
| Teknologi | Versi | Kegunaan |
|---|---|---|
| **Tailwind CSS** | 3.4 (CDN) | Utility-first CSS framework |
| **Alpine.js** | 3.x (CDN) | Framework JavaScript ringan untuk interaktivitas |
| **Font Awesome** | 6.4 (CDN) | Ikon UI |
| **SweetAlert2** | 11 (CDN) | Notifikasi & dialog interaktif |
| **Vite** | 6.x | Build tool & asset bundler |
| **Axios** | ^1.7 | HTTP client untuk request AJAX |
| **Google Fonts (Montserrat & Poppins)** | - | Tipografi UI |

### 2.3 Face Recognition (AI/ML)
| Teknologi | Keterangan |
|---|---|
| **face-api.js** | Library JavaScript untuk deteksi dan pengenalan wajah berbasis TensorFlow.js |
| **TinyFaceDetector** | Model AI ringan untuk deteksi wajah cepat (saat scan absensi) |
| **SSDMobilenetv1** | Model AI lebih akurat untuk deteksi wajah (saat perekaman/enrollment) |
| **FaceLandmark68Net** | Model 68 titik landmark wajah |
| **FaceRecognitionNet** | Model ekstraksi descriptor wajah (128-dimension vector) |
| **Euclidean Distance** | Algoritma perbandingan vektor wajah (threshold < 0.45 = match) |

### 2.4 PWA (Progressive Web App)
| Komponen | Keterangan |
|---|---|
| **manifest.json** | Konfigurasi PWA (nama app, ikon, theme color `#002D8B`) |
| **Service Worker (sw.js)** | Network-first caching strategy |
| **Standalone Display** | Aplikasi bisa di-install seperti native app |

### 2.5 Infrastruktur & Konfigurasi
| Komponen | Keterangan |
|---|---|
| **Timezone** | Asia/Jakarta (WIB) |
| **Session Driver** | Database |
| **Queue Driver** | Database |
| **Cache Store** | Database |
| **Storage** | Local (public disk) untuk foto profil & bukti izin |

---

## 3. API Eksternal yang Digunakan

### 3.1 API Hari Libur Nasional Indonesia
- **Endpoint:** `https://libur.deno.dev/api`
- **Parameter:** `year`, `month`, `day`
- **Method:** GET
- **Response:** JSON berisi `is_holiday` (boolean) dan `holiday_list` (array)
- **Kegunaan:** Memvalidasi apakah hari ini adalah hari libur nasional. Jika ya, sistem absensi otomatis ditutup.
- **Dipanggil di:**
  - `ScanAbsensiController@index` & `store` — Validasi saat guru membuka halaman scan dan saat menyimpan absensi
  - `Guru\DashboardController@index` — Menampilkan status libur di dashboard guru
  - `AlpaService@isWorkingDay` — Menentukan hari kerja untuk auto-generate record Alpa

---

## 4. Fitur-Fitur Berdasarkan Role

### 4.1 Role: ADMIN (Administrator)

#### A. Dashboard Admin
- **Statistik kehadiran real-time:** Total guru, hadir, terlambat, dan alpa hari ini
- **Aktivitas absensi terbaru:** 5 record absensi terakhir hari ini
- **Lihat semua absen harian:** Endpoint JSON dengan filter tanggal & pagination

#### B. Manajemen Data Guru (CRUD Lengkap)
- **Tambah guru** — data akun (users) + biodata lengkap (gurus) dalam satu transaksi database
- **Edit & Hapus guru** — termasuk manajemen foto profil (upload, ganti, hapus dari storage)
- **Import Excel/CSV** — bulk upload data guru menggunakan file spreadsheet
- **Download Template** — template Excel standar untuk import
- **Export Excel** — export seluruh data guru ke file `.xlsx`
- **Export PDF** — export data guru ke dokumen PDF (landscape A4)
- **Print Profil Individu** — cetak profil PDF per guru (portrait A4)
- **Data guru mencakup:** NIK, nama, password, jabatan (guru/kepala_sekolah), unit sekolah (SD/SMP), tempat & tanggal lahir, jenis kelamin, agama, alamat, no HP, pendidikan terakhir, tanggal bergabung, foto profil

#### C. Face Recording (Enrollment Wajah)
- **Daftar guru** dengan status rekam wajah (sudah/belum)
- **Filter** berdasarkan status wajah dan pencarian nama/NIK
- **Statistik card:** total guru, sudah rekam, belum rekam
- **Halaman rekam wajah** — live camera feed dengan deteksi wajah real-time menggunakan SSDMobilenetv1 + FaceLandmark68 + FaceRecognition
- **Simpan face descriptor** — 128-dimension vector disimpan sebagai JSON di kolom `face_descriptor` tabel users
- **Proses:** Kamera aktif → deteksi wajah → landmark terdeteksi → tombol aktif → klik rekam → descriptor dikirim via AJAX POST → disimpan ke database

#### D. Pengaturan Jaringan LAN/WiFi
- **CRUD IP Address** yang diizinkan untuk absensi
- **Format IP:** Exact match, wildcard (192.168.1.%), auto-subnet IPv4 (2 blok pertama), auto-subnet IPv6 (4 blok awal)
- **Toggle aktif/nonaktif** per IP via AJAX
- **Master Bypass Switch** — fitur khusus (IP `*`) untuk menonaktifkan seluruh validasi WiFi (semua guru bebas absen dari mana saja)
- **Auto-detect IP admin** saat membuat jaringan baru

#### E. Pengaturan Absensi
- **Jam buka absen** — waktu mulai absensi masuk (default 06:00)
- **Batas jam masuk** — melewati jam ini dianggap terlambat (default 07:15)
- **Jam pulang** — waktu mulai absensi pulang (default 14:00)
- **Denda terlambat** — nominal denda flat per pelanggaran (default Rp 10.000)

#### F. Manajemen User/Akun
- **CRUD akun pengguna** dengan role: admin, guru, kepala_yayasan
- **Username 16 karakter** (unik)
- **Proteksi:** Admin tidak bisa menghapus akun sendiri saat login
- **Filter & pencarian** berdasarkan role dan nama/username

#### G. Manajemen Libur Semester
- **CRUD periode libur semester** (nama, tanggal mulai, tanggal selesai, keterangan, status aktif)
- **Scope `isLiburSekarang()`** — pengecekan cepat apakah saat ini masa libur
- **Dampak:** Sistem absensi otomatis ditutup selama periode libur semester aktif

#### H. Persetujuan Pengajuan Izin
- **Daftar pengajuan izin** (Sakit, Izin, Cuti) dari seluruh guru
- **Statistik:** total pending, disetujui, ditolak
- **Approve/Reject** dengan catatan penolakan
- **Auto-generate absensi:** Jika disetujui, otomatis membuat record absensi (Sakit/Izin/Cuti) untuk setiap tanggal dalam rentang izin menggunakan `updateOrCreate`
- **Cleanup data:** Hapus massal berdasarkan hari, minggu, atau semua data (termasuk hapus file bukti dari storage)

#### I. Riwayat Absensi
- **Tabel lengkap** dengan filter: pencarian nama/NIK, status, bulan, tahun, unit sekolah
- **Export PDF** (landscape A4)
- **Export Excel**
- **Cleanup data:** Hapus massal per hari, minggu, atau semua
- **Auto-backfill Alpa** sebelum query berdasarkan filter bulan/tahun

---

### 4.2 Role: GURU (Teacher)

#### A. Dashboard Guru (Mobile-First)
- **Salam dinamis** berdasarkan jam WIB (Pagi/Siang/Sore/Malam)
- **Status absensi hari ini:** sudah/belum absen masuk, sudah/belum pulang
- **Status izin hari ini:** jika ada pengajuan aktif
- **Deteksi hari libur:** libur semester, hari Minggu, libur nasional (via API)
- **Statistik bulanan:** total hadir, total denda, total alpa
- **Riwayat pengajuan izin** (3 terakhir)
- **Backfill Alpa otomatis** untuk hari kerja yang terlewat

#### B. Scan Absensi Wajah (Core Feature)
**Alur Validasi Bertingkat (Multi-Layer Security):**

1. **Validasi Jaringan WiFi** — Cek IP terhadap whitelist (exact, wildcard, subnet IPv4/IPv6)
2. **Validasi Libur Semester** — Cek apakah sedang masa libur aktif
3. **Validasi Hari Minggu** — Otomatis libur
4. **Validasi Libur Nasional** — Via API `libur.deno.dev`
5. **Validasi Pengajuan Izin** — Cek apakah guru memiliki izin aktif (Pending/Disetujui)
6. **Validasi Pengaturan** — Cek apakah pengaturan absensi sudah dikonfigurasi
7. **Validasi Waktu Absen Masuk** — Tidak sebelum jam buka absen
8. **Validasi Waktu Absen Pulang** — Tidak sebelum jam pulang
9. **Validasi Wajah Terdaftar** — Cek apakah `face_descriptor` sudah ada
10. **Verifikasi Biometrik** — Euclidean distance < 0.45 antara scan wajah dan descriptor tersimpan

**Proses Scan:**
- Kamera depan aktif (facingMode: user, 480x640)
- TinyFaceDetector (inputSize 224) untuk deteksi cepat
- Real-time loop setiap 400ms
- Jika match: kirim POST request ke backend → simpan record absensi → redirect ke dashboard

**Status Absensi:**
- **Hadir** — scan masuk sebelum batas jam masuk
- **Terlambat** — scan masuk setelah batas jam masuk (dengan perhitungan menit terlambat)
- **Absen Pulang** — update `jam_pulang` pada record yang sama

#### C. Riwayat Absensi
- **Filter bulan & tahun**
- **Tabel riwayat** dengan status dan jam masuk/pulang
- **Total kehadiran** (Hadir + Terlambat)
- **Auto-backfill Alpa** untuk hari kerja yang belum tercatat

#### D. Informasi Denda
- **Filter bulan & tahun**
- **Rekap:** total hari terlambat, total hari alpa, total denda
- **Nominal denda flat** × (terlambat + alpa)
- **Detail riwayat** per pelanggaran (tanggal, status, menit terlambat)

#### E. Pengajuan Izin
- **Jenis izin:** Sakit, Izin, Cuti
- **Form pengajuan:** tanggal mulai, tanggal selesai, alasan, file bukti (wajib untuk Sakit)
- **Upload bukti** — PDF/JPG/JPEG/PNG max 2MB, disimpan di `storage/app/public/bukti_izin`
- **Notifikasi otomatis** ke admin saat pengajuan dibuat
- **Riwayat pengajuan** dengan status (Pending/Disetujui/Ditolak) dan catatan penolakan
- **Detail pengajuan** individual

#### F. Pengaturan Akun
- **Update foto profil** (upload/ganti)
- **Update nomor HP** (disimpan di tabel gurus)
- **Validasi:** foto max 2MB (jpeg/png/jpg), no HP 10-15 digit

---

### 4.3 Role: KEPALA YAYASAN (Foundation Head)

#### A. Dashboard Eksekutif
- **Metrik kehadiran hari ini:** total guru, hadir tepat waktu, terlambat, alpa
- **Total denda bulan ini** — (terlambat + alpa) × nominal denda
- **Rata-rata kehadiran** dalam persentase
- **Filter unit sekolah:** SD, SMP, atau semua
- **Grafik tren mingguan** (7 hari terakhir) — Chart.js: garis hadir, terlambat, alpa
- **5 absensi terbaru** hari ini
- **Auto-backfill Alpa** setiap kali dashboard dibuka

#### B. Laporan Kehadiran
- **Filter:** rentang tanggal (start/end), guru spesifik, unit sekolah
- **Rekap summary:** hadir, terlambat, alpa, total
- **Export PDF** (portrait A4)
- **Export Excel**
- **Auto-backfill** sebelum query dan export

#### C. Laporan Potongan Gaji
- **Filter:** bulan, tahun, unit sekolah
- **Per-guru:** jumlah terlambat, jumlah alpa, total potongan, riwayat detail
- **Sorting otomatis** berdasarkan total potongan (tertinggi di atas)
- **Rekap keseluruhan:** total potongan semua guru, jumlah guru yang dipotong
- **Export PDF** (portrait A4)
- **Export Excel**
- **Auto-backfill** sebelum query dan export

---

### 4.4 Fitur Lintas Role (Semua User)

#### A. Autentikasi
- **Login** menggunakan NIK + password (bukan email)
- **Remember Me** permanen (selalu aktif)
- **Role-based redirect** — otomatis diarahkan ke dashboard sesuai role
- **Notifikasi login** — dicatat di tabel notifications

#### B. Profil Pengguna
- **Edit nama, username, password**
- **Upload/ganti foto profil**
- **Akses:** `/profil-saya`

#### C. Sistem Notifikasi
- **Notifikasi login** — setiap user login tercatat
- **Notifikasi pengajuan izin** — saat guru mengajukan izin
- **Tandai semua dibaca** — endpoint `/notifications/read-all`

#### D. Progressive Web App (PWA)
- **Installable** — bisa ditambahkan ke home screen
- **Standalone mode** — berjalan seperti aplikasi native
- **Service Worker** — network-first strategy (tidak cache konten dinamis)
- **Theme color** — `#002D8B` (brand color Yayasan)

---

## 5. Arsitektur Database (Skema)

### 5.1 Tabel `users`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint (PK) | Auto increment |
| name | varchar | Nama lengkap |
| username | varchar (unique) | Username login (16 char) |
| nik | varchar (unique, nullable) | NIK untuk login |
| password | varchar | Hashed password (bcrypt) |
| role | enum | admin, kepala_yayasan, guru |
| unit_sekolah | varchar | SD, SMP, atau kombinasi (comma-separated) |
| jabatan | varchar | guru, kepala_sekolah |
| foto_profil | varchar (nullable) | Path file di storage |
| face_descriptor | text (nullable) | JSON array 128-dimension float |
| remember_token | varchar | Laravel remember token |
| timestamps | - | created_at, updated_at |

### 5.2 Tabel `gurus` (Biodata)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint (PK) | Auto increment |
| user_id | bigint (FK → users) | Cascade on delete |
| tempat_lahir | varchar (nullable) | |
| tanggal_lahir | date (nullable) | |
| jenis_kelamin | enum L/P (nullable) | |
| agama | varchar (nullable) | |
| alamat | text (nullable) | |
| no_hp | varchar (nullable) | |
| pendidikan_terakhir | varchar (nullable) | |
| tanggal_bergabung | date (nullable) | |
| timestamps | - | |

### 5.3 Tabel `absensis`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint (PK) | Auto increment |
| user_id | bigint (FK → users) | Cascade on delete |
| tanggal | date | Tanggal absensi |
| jam_masuk | time (nullable) | Jam scan masuk |
| jam_pulang | time (nullable) | Jam scan pulang |
| status | enum | Hadir, Terlambat, Alpa, Sakit, Izin, Cuti |
| menit_terlambat | int | Selisih menit dari batas jam masuk |
| timestamps | - | |

### 5.4 Tabel `ip_lokals`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint (PK) | |
| nama_jaringan | varchar | Nama WiFi/jaringan |
| ip_address | varchar (unique) | IP address / wildcard / `*` (master) |
| is_active | boolean | Status aktif |
| keterangan | text (nullable) | Catatan |
| timestamps | - | |

### 5.5 Tabel `pengaturan_absensis`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint (PK) | |
| jam_buka_absen | time | Default 06:00 |
| batas_jam_masuk | time | Default 07:15 |
| jam_pulang | time | Default 14:00 |
| denda_terlambat | int | Nominal denda (default 10000) |
| timestamps | - | |

### 5.6 Tabel `libur_semesters`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint (PK) | |
| nama_semester | varchar | |
| tanggal_mulai | date | |
| tanggal_selesai | date | |
| keterangan | text (nullable) | |
| is_active | boolean | |
| timestamps | - | |

### 5.7 Tabel `pengajuan_izins`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint (PK) | |
| user_id | bigint (FK → users) | |
| jenis | enum | Sakit, Izin, Cuti |
| tanggal_mulai | date | |
| tanggal_selesai | date | |
| alasan | text | |
| file_bukti | varchar (nullable) | Path file di storage |
| status | varchar | Pending, Disetujui, Ditolak |
| catatan_penolakan | text (nullable) | |
| disetujui_oleh | bigint (FK → users, nullable) | Admin yang merespon |
| direspon_pada | datetime (nullable) | |
| timestamps | - | |

### 5.8 Tabel `notifications`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | auto increment | |
| user_id | int | |
| title | varchar | |
| message | text | HTML content |
| icon | varchar | Font Awesome icon class |
| is_read | boolean | 0/1 |
| timestamps | - | |

---

## 6. Analisa Kompleksitas

### 6.1 Kompleksitas Tinggi
1. **Face Recognition Pipeline** — Integrasi face-api.js dengan 3 model AI (detector + landmark + recognition), proses enrollment dan verifikasi real-time di browser, threshold matching dengan Euclidean distance
2. **Multi-Layer Validation pada Scan Absensi** — 10 lapis validasi berurutan (WiFi → libur semester → Minggu → libur nasional API → izin → pengaturan → waktu masuk → waktu pulang → wajah terdaftar → biometrik)
3. **IP Validation Engine** — 4 metode pencocokan IP: exact match, wildcard pattern, auto-subnet IPv4 (2 blok), auto-subnet IPv6 (4 blok prefix)
4. **Alpa Auto-Creation Service** — Backfill otomatis record Alpa untuk hari kerja yang belum tercatat, dengan logika penentuan hari kerja (bukan Minggu, bukan libur semester, bukan libur nasional API), hanya untuk tanggal lampau (never today/future)
5. **Multi-tenant Unit Sekolah** — Filtering data berdasarkan unit sekolah (SD/SMP) di hampir semua laporan dan dashboard

### 6.2 Kompleksitas Sedang
1. **Dual Attendance System** — Absen Masuk dan Pulang dalam satu record, dengan jam buka dan jam tutup terpisah
2. **Denda Calculation** — Flat-rate penalty system untuk keterlambatan dan alpa, dihitung per bulan
3. **Auto-absensi dari Izin Disetujui** — Generate otomatis record Sakit/Izin/Cuti saat admin menyetujui pengajuan, menggunakan `updateOrCreate` untuk menghindari duplikat
4. **Export Multi-Format** — PDF (DomPDF) dan Excel (Maatwebsite) untuk 4 jenis laporan berbeda (data guru, riwayat admin, laporan yayasan, potongan gaji)
5. **Import Bulk dengan Relasi** — Import Excel data guru yang sekaligus membuat record di 2 tabel (users + gurus) dalam transaksi

### 6.3 Kompleksitas Rendah
1. **CRUD standar** — Data guru, user, libur semester, pengaturan LAN
2. **Role-based access control** — Middleware sederhana dengan pengecekan role di field users
3. **Authentication** — Login NIK + password, remember me, role-based redirect
4. **Profile management** — Update data pribadi dan foto profil

---

## 7. Ringkasan API Endpoints

### 7.1 API Eksternal
| API | Endpoint | Dipakai Untuk |
|---|---|---|
| **Libur Nasional** | `https://libur.deno.dev/api?year=&month=&day=` | Deteksi hari libur nasional Indonesia |

### 7.2 Internal AJAX Endpoints (JSON Response)
| Method | Route | Fungsi |
|---|---|---|
| POST | `/guru/scan-absensi/store` | Simpan absensi masuk/pulang setelah verifikasi wajah |
| POST | `/admin/face-recording/{guru}/store` | Simpan face descriptor guru |
| POST | `/admin/pengaturan-lan/toggle/{id}` | Toggle status IP aktif/nonaktif |
| POST | `/admin/pengaturan-lan/toggle-master` | Toggle master bypass WiFi |
| GET | `/admin/dashboard/semua-absen` | JSON data semua absensi hari ini (pagination) |

### 7.3 Route Groups
| Prefix | Middleware | Role | Jumlah Route |
|---|---|---|---|
| `/admin` | auth + role:admin | Admin | ~30 routes |
| `/yayasan` | auth + role:kepala_yayasan | Kepala Yayasan | ~7 routes |
| `/guru` | auth + role:guru | Guru | ~10 routes |
| `/` (root) | - | Public/Auth | ~6 routes |

---

## 8. Arsitektur Aplikasi

```
┌─────────────────────────────────────────────┐
│              BROWSER (PWA)                   │
│  ┌─────────────┐  ┌──────────────────────┐  │
│  │  Mobile UI   │  │   Desktop UI         │  │
│  │ (layouts/    │  │  (layouts/app.blade) │  │
│  │  mobile)     │  │  + Sidebar + Header  │  │
│  └──────┬───────┘  └──────────┬───────────┘  │
│         │                     │              │
│  ┌──────┴─────────────────────┴───────────┐  │
│  │         face-api.js (AI Models)        │  │
│  │   TinyFaceDetector / SSDMobilenetv1    │  │
│  │   FaceLandmark68 + FaceRecognition     │  │
│  └──────────────────┬─────────────────────┘  │
└─────────────────────┼────────────────────────┘
                      │ AJAX / HTTP
┌─────────────────────┼────────────────────────┐
│              LARAVEL BACKEND                  │
│  ┌──────────────────┴─────────────────────┐  │
│  │            Routes (web.php)             │  │
│  │   Role Middleware (admin/guru/yayasan)  │  │
│  └──┬──────────┬──────────┬───────────────┘  │
│     │          │          │                  │
│  ┌──┴──┐   ┌──┴──┐   ┌──┴──────┐           │
│  │Admin│   │ Guru│   │Yayasan  │           │
│  │Ctrl │   │Ctrl │   │Ctrl     │           │
│  └──┬──┘   └──┬──┘   └──┬──────┘           │
│     │          │          │                  │
│  ┌──┴──────────┴──────────┴───────────────┐  │
│  │          Services & Models              │  │
│  │  AlpaService │ Absensi │ User │ Guru    │  │
│  └──────────────────┬─────────────────────┘  │
│                     │                        │
│  ┌──────────────────┴─────────────────────┐  │
│  │            MySQL Database               │  │
│  │  users │ gurus │ absensis │ ip_lokals  │  │
│  │  pengaturan_absensis │ libur_semesters  │  │
│  │  pengajuan_izins │ notifications        │  │
│  └────────────────────────────────────────┘  │
│                                              │
│  ┌────────────────────────────────────────┐  │
│  │        External API Call               │  │
│  │  https://libur.deno.dev/api            │  │
│  └────────────────────────────────────────┘  │
└──────────────────────────────────────────────┘
```

---

## 9. Artisan Command Custom

| Command | Deskripsi |
|---|---|
| `php artisan absensi:create-alpa` | Buat record Alpa untuk kemarin |
| `php artisan absensi:create-alpa --date=2026-06-15` | Buat record Alpa untuk tanggal tertentu |
| `php artisan absensi:create-alpa --backfill` | Backfill semua hari kerja dari awal bulan sampai kemarin |
| `php artisan absensi:create-alpa --backfill --start=2026-01-01 --end=2026-06-15` | Backfill rentang tanggal tertentu |
| `php artisan absensi:create-alpa --cleanup-today` | Hapus record Alpa invalid untuk hari ini/masa depan |

---

## 10. Kesimpulan

Project ini adalah **sistem absensi biometrik wajah berbasis web** yang cukup kompleks dengan:
- **3 role pengguna** (Admin, Guru, Kepala Yayasan) dengan hak akses berbeda
- **10 lapis validasi keamanan** pada proses absensi
- **Teknologi AI/ML** (face-api.js) untuk pengenalan wajah real-time di browser
- **Integrasi API eksternal** untuk data libur nasional Indonesia
- **PWA** yang bisa di-install di perangkat mobile
- **Sistem denda otomatis** untuk keterlambatan dan ketidakhadiran
- **Auto-backfill** record Alpa untuk menjaga integritas data kehadiran
- **Multi-format export** (PDF + Excel) untuk pelaporan
- **Multi-tenant** berbasis unit sekolah (SD/SMP)
- **~53+ route endpoints**, **17 controller**, **7 model**, **5 export class**, **1 service class**, **1 artisan command custom**
