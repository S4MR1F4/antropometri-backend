# Antropometri Backend

Backend Antropometri adalah REST API Laravel 12 untuk pencatatan subjek, pengukuran antropometri, perhitungan status gizi, sinkronisasi offline mobile, export, import, notifikasi, dan administrasi user.

## Stack

- PHP 8.2+
- Laravel 12
- Laravel Sanctum untuk Bearer token
- MySQL/MariaDB untuk production
- SQLite in-memory untuk test
- Maatwebsite Excel untuk import/export
- DomPDF untuk export PDF

## Setup Lokal

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Jalankan test:

```bash
php artisan test
```

Generate template Excel:

```bash
php artisan app:generate-excel-template
```

Template yang digenerate mengikuti `docs/Template_Perhitungan_Antropometri.xlsx`, tanpa NIK, dan menyiapkan area input sampai 15.000 peserta agar export/import manual tetap konsisten dengan template operasional.

## Konfigurasi Penting

```env
APP_URL=http://127.0.0.1:8000
APP_DEEP_LINK_BASE=antropometri://app
PRIMARY_ADMIN_EMAIL=antropometri@samrifa.com
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=antropometri
DB_USERNAME=root
DB_PASSWORD=
```

Credential production disimpan terpisah di `../docs/server_credentials.md` dan file tersebut wajib tetap masuk `.gitignore`.

## Backup Database Production

Backend menyediakan skrip `scripts/backup_database.sh` untuk backup MySQL/MariaDB production. Skrip membaca konfigurasi database dari `.env`, menyimpan hasil ke `storage/app/backups/database`, mengompres file menjadi `.sql.gz`, dan memakai nama file berbasis tanggal-jam:

```text
{DB_DATABASE}_YYYYMMDD_HHMMSS.sql.gz
```

Jalankan manual di server sebelum deploy atau pull:

```bash
bash scripts/backup_database.sh
# atau lewat Laravel command
php artisan db:backup
```

Laravel scheduler sudah menjadwalkan backup setiap jam `02:00`:

```bash
php artisan schedule:list
```

Untuk shared hosting, opsi paling ringan adalah membuat dua Cron Jobs harian langsung dari panel hosting:

```cron
0 2 * * * cd /home/u863643602/domains/samrifa.com/antropometri_app && php artisan db:backup >> storage/logs/database-backup.log 2>&1
15 2 * * * cd /home/u863643602/domains/samrifa.com/antropometri_app && php artisan data:prune-soft-deleted >> storage/logs/data-retention.log 2>&1
```

Alternatif standar Laravel adalah satu Cron Jobs `schedule:run` setiap menit. Itu tetap ringan karena hanya mengecek task yang sudah due, tetapi opsi dua Cron Jobs harian di atas lebih hemat proses untuk shared hosting.

Dengan jadwal ini database dibackup setiap jam 02:00 waktu server. File `latest.sql.gz` akan menunjuk backup terbaru jika server mendukung symlink. Retensi default adalah 10 file backup terbaru; ketika backup ke-11 berhasil dibuat, file paling lama otomatis dihapus. Jumlah retensi dapat diubah dengan `DB_BACKUP_KEEP_COUNT`.

## Retensi Data Terhapus

Soft delete tetap menjadi tahap aman untuk pasien, pemeriksaan, dan user. Data yang sudah soft-deleted lebih dari 60 hari akan diproses oleh command:

```bash
php artisan data:prune-soft-deleted
```

Command ini:

- Menghitung pasien, pemeriksaan, dan user soft-deleted yang melewati retensi `DATA_RETENTION_SOFT_DELETE_DAYS` (default `60`).
- Membuat backup database terlebih dahulu memakai `scripts/backup_database.sh`.
- Memakai retensi backup yang sama, yaitu 10 backup terbaru via `DB_BACKUP_KEEP_COUNT`.
- Menghapus permanen pasien yang sudah melewati retensi; semua pemeriksaan milik pasien tersebut ikut terhapus permanen oleh relasi database.
- Menghapus permanen pemeriksaan yang soft-deleted sendiri dan sudah melewati retensi.
- Saat user dihapus, data pasien dan pemeriksaan milik user dipindahkan ke admin aktif terlebih dahulu, sehingga penghapusan user tidak menghapus data lapangan.

Scheduler Laravel menjalankan command ini setiap hari jam `02:15` dan menulis log ke `storage/logs/data-retention.log`. Untuk simulasi tanpa backup dan tanpa penghapusan:

```bash
php artisan data:prune-soft-deleted --dry-run
```

Foreign key `subjects.user_id` dan `measurements.user_id` dibuat `restrictOnDelete` supaya force delete user tidak pernah menghapus data pasien/pemeriksaan secara cascade. Relasi pasien ke pemeriksaan tetap `cascadeOnDelete`, sesuai aturan bahwa pemeriksaan ikut hilang permanen hanya ketika pasien dihapus permanen.

## Akun Utama Admin

Akun `PRIMARY_ADMIN_EMAIL` (default `antropometri@samrifa.com`) adalah akun utama pemilik aplikasi. Admin lain tetap bisa melihat akun ini di daftar user, tetapi API akan menolak edit, reset password, atau delete terhadap akun utama dengan HTTP `403`. Response daftar user menyertakan flag:

- `is_current_user`: akun yang sedang login.
- `is_primary`: akun utama pemilik aplikasi.
- `can_manage`: boleh/tidaknya akun login menjalankan aksi manajemen user terhadap target.

Export PDF/Excel mengikuti scope role yang sama dengan API data: admin mengekspor seluruh data sesuai filter, petugas otomatis dibatasi ke `user_id` miliknya. PDF tidak lagi membatasi daftar detail ke 500 baris agar hasil export sesuai penuh dengan filter yang dipilih.

## Struktur Folder

```text
app/Actions/Measurement      Perhitungan balita, remaja, dewasa
app/Exports                  Export Excel
app/Http/Controllers/Api     Controller REST API
app/Http/Requests            Validasi request
app/Http/Resources           Bentuk response API
app/Imports                  Import Excel
app/Models                   Eloquent models
app/Services                 Business logic
database/migrations          Struktur database
database/seeders             Reference data dan akun awal
routes/api.php               Route API utama
```

## Response Envelope

Sukses:

```json
{
  "success": true,
  "message": "Operasi berhasil",
  "data": {}
}
```

Error:

```json
{
  "success": false,
  "message": "Data tidak valid",
  "errors": {
    "field": ["Pesan validasi"]
  }
}
```

Endpoint protected membutuhkan header:

```http
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

## Public API

### GET `/api/health`

Health check server.

Response:

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "status": "healthy"
  }
}
```

### GET `/api/version/check`

Cek versi aplikasi mobile terbaru.

Response berisi metadata versi, link update, dan flag update jika tersedia. Nilai default diarahkan ke versi `2.0.0` dan Play Store `com.samrifa.antropometri`.

Environment yang dapat diatur:

```env
APP_LATEST_VERSION=2.0.0
APP_MIN_SUPPORTED_VERSION=2.0.0
APP_FORCE_UPDATE=false
APP_PLAY_STORE_URL=https://play.google.com/store/apps/details?id=com.samrifa.antropometri
APP_MARKET_URL=market://details?id=com.samrifa.antropometri
APP_RELEASE_NOTES="Pembaruan Antropometri tersedia."
```

Response:

```json
{
  "status": "success",
  "data": {
    "latest_version": "2.0.0",
    "min_version": "2.0.0",
    "force_update": false,
    "play_store_url": "https://play.google.com/store/apps/details?id=com.samrifa.antropometri",
    "market_url": "market://details?id=com.samrifa.antropometri",
    "release_notes": "Pembaruan Antropometri tersedia."
  }
}
```

### App Links Mobile

Email login, reset password, dan notifikasi memakai link HTTPS backend agar Android dapat membuka aplikasi secara langsung. Jika App Link belum terverifikasi atau aplikasi belum terpasang, halaman fallback mencoba custom scheme `antropometri://app/...` lalu mengarahkan ke Play Store.

Environment:

```env
APP_LINK_BASE=https://antropometri.samrifa.com
APP_DEEP_LINK_BASE=antropometri://app
APP_PLAY_STORE_URL=https://play.google.com/store/apps/details?id=com.samrifa.antropometri
ANDROID_PACKAGE_NAME=com.samrifa.antropometri
ANDROID_SHA256_CERT_FINGERPRINTS=AA:BB:CC:...
```

Endpoint `/.well-known/assetlinks.json` membaca `ANDROID_PACKAGE_NAME` dan `ANDROID_SHA256_CERT_FINGERPRINTS`. Isi fingerprint harus memakai SHA-256 dari Play App Signing certificate agar verified App Links aktif di perangkat Android produksi.

### POST `/api/auth/register`

Mendaftarkan petugas baru dan langsung mengembalikan token login.

Request:

```json
{
  "name": "Petugas Puskesmas",
  "email": "petugas@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "device_name": "android-001"
}
```

Response `201`:

```json
{
  "success": true,
  "message": "Registrasi berhasil. Selamat datang!",
  "data": {
    "user": {
      "id": 2,
      "name": "Petugas Puskesmas",
      "email": "petugas@example.com",
      "role": "petugas"
    },
    "token": "plain-text-sanctum-token",
    "token_type": "Bearer"
  }
}
```

### POST `/api/auth/login`

Login user.

Request:

```json
{
  "email": "petugas@example.com",
  "password": "password123",
  "device_name": "android-001"
}
```

Response:

```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "user": {
      "id": 2,
      "name": "Petugas Puskesmas",
      "email": "petugas@example.com",
      "role": "petugas"
    },
    "token": "plain-text-sanctum-token",
    "token_type": "Bearer",
    "expires_at": "2026-05-20T00:00:00+00:00"
  }
}
```

### POST `/api/auth/forgot-password`

Membuat password acak baru dan mencoba mengirimkannya ke email user. Jika SMTP/email gagal, password tetap sudah direset dan API mengembalikan `new_password` agar dapat disalin langsung oleh pengguna.

Email sukses mengirim tombol `Login ke Aplikasi` ke deep link mobile `APP_DEEP_LINK_BASE/login?reset=success` agar tidak membuka root website.

Request:

```json
{
  "email": "petugas@example.com"
}
```

Response sukses email:

```json
{
  "success": true,
  "message": "Kata sandi baru telah dikirim ke email Anda.",
  "data": {
    "email_sent": true
  }
}
```

Response saat email gagal:

```json
{
  "success": true,
  "message": "Password baru berhasil dibuat, tetapi email gagal dikirim. Salin password yang tampil.",
  "data": {
    "email_sent": false,
    "new_password": "randomPassword"
  }
}
```

### POST `/api/auth/reset-password`

Reset password menggunakan token broker Laravel.

Request:

```json
{
  "email": "petugas@example.com",
  "token": "reset-token",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

## Authenticated API

### POST `/api/auth/logout`

Revoke token aktif.

### GET `/api/auth/me`

Mengambil profil user aktif. Query optional: `from_date`, `to_date` untuk statistik user.

### POST `/api/auth/update-profile`

Request:

```json
{
  "name": "Nama Baru",
  "email": "baru@example.com"
}
```

### POST `/api/auth/change-password`

Request:

```json
{
  "current_password": "password123",
  "new_password": "password456",
  "new_password_confirmation": "password456"
}
```

Setiap register, login sukses, login gagal, dan logout dicatat ke activity log beserta IP, user agent, email, dan `device_name` jika dikirim oleh mobile.

Login mendukung multi-device: setiap `device_name` mendapat token terpisah, sehingga satu akun bisa digunakan di lebih dari satu perangkat secara bersamaan. Login dari device yang sama hanya mengganti token lama device tersebut tanpa mengganggu sesi di device lain.

## Subjects

NIK sudah dihapus dari kontrak API. Identifikasi duplikat menggunakan `normalized_name` + `date_of_birth` pada scope user.

### GET `/api/subjects`

Query:

| Parameter | Tipe | Keterangan |
| --- | --- | --- |
| `search` | string | Cari nama subjek |
| `category` | string | `balita`, `remaja`, `dewasa` |
| `gender` | string | `L` atau `P` |
| `sort_by` | string | Kolom sorting |
| `sort_dir` | string | `asc` atau `desc` |
| `trashed` | bool | Sertakan soft-deleted |
| `only_trashed` | bool | Hanya soft-deleted |
| `per_page` | int | Default 15 |

Response:

```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Ayu",
        "normalized_name": "AYU",
        "date_of_birth": "2021-05-01",
        "gender": "P",
        "age_in_months": 60,
        "age_display": "5 tahun",
        "category": "remaja",
        "address": "Makassar",
        "parent_name": "Ibu Ayu",
        "phone": "08123456789",
        "pregnancy_start_date": null,
        "is_pregnant_active": false,
        "measurements_count": 3,
        "latest_measurement": null,
        "created_at": "2026-05-13T00:00:00+00:00",
        "deleted_at": null
      }
    ],
    "meta": {
      "current_page": 1,
      "last_page": 1,
      "per_page": 15,
      "total": 1
    }
  }
}
```

### POST `/api/subjects`

Request:

```json
{
  "name": "Ayu",
  "date_of_birth": "2021-05-01",
  "gender": "P",
  "address": "Makassar",
  "parent_name": "Ibu Ayu",
  "phone": "08123456789"
}
```

Response `201`:

```json
{
  "success": true,
  "message": "Subjek berhasil ditambahkan",
  "data": {
    "subject": {
      "id": 1,
      "name": "Ayu",
      "date_of_birth": "2021-05-01",
      "gender": "P",
      "category": "remaja"
    }
  }
}
```

Jika duplikat:

```json
{
  "success": false,
  "message": "Subjek sudah terdaftar",
  "data": {
    "existing_subject": {
      "id": 1,
      "name": "Ayu",
      "date_of_birth": "2021-05-01",
      "created_at": "2026-05-13T00:00:00+00:00"
    }
  }
}
```

### GET `/api/subjects/{subject}`

Detail subjek beserta `latest_measurement` jika tersedia.

### PUT `/api/subjects/{subject}`

Request sama dengan create, semua field bersifat partial kecuali field yang dikirim tetap divalidasi.

### DELETE `/api/subjects/{subject}`

Soft delete subjek.

### POST `/api/subjects/{subject}/restore`

Restore soft-deleted subject.

### POST `/api/subjects/{subject}/reset-pregnancy`

Mengosongkan `pregnancy_start_date`.

## Measurements

### GET `/api/subjects/{subject}/measurements`

Query: `from_date`, `to_date`, `per_page`.

Response:

```json
{
  "success": true,
  "data": {
    "subject": {
      "id": 1,
      "name": "Ayu",
      "category": "remaja"
    },
    "measurements": [
      {
        "id": 10,
        "subject_id": 1,
        "subject": {
          "id": 1,
          "name": "Ayu",
          "gender": "P",
          "date_of_birth": "2021-05-01"
        },
        "measurement_date": "2026-05-13",
        "category": "remaja",
        "weight": "38.00",
        "height": "145.00",
        "arm_circumference": null,
        "is_pregnant": false,
        "age_in_months": 60,
        "result": {
          "bmi": "18.07",
          "status_imtu": "Gizi Baik"
        },
        "recommendation": "Normal, terapkan hidup sehat & gizi seimbang.",
        "notes": null
      }
    ],
    "pagination": {
      "current_page": 1,
      "last_page": 1,
      "per_page": 10,
      "total": 1
    }
  }
}
```

### POST `/api/subjects/{subject}/measurements`

Request umum:

```json
{
  "measurement_date": "2026-05-13",
  "weight": 38,
  "height": 145,
  "head_circumference": null,
  "waist_circumference": null,
  "arm_circumference": null,
  "measurement_type": "berdiri",
  "is_pregnant": false,
  "pregnancy_start_date": null,
  "notes": "Pemeriksaan rutin"
}
```

Validasi utama:

| Kategori | Berat | Tinggi | Tambahan |
| --- | --- | --- | --- |
| Balita | 1-45 kg | 30-135 cm | `head_circumference`, `measurement_type` |
| Remaja | 8-220 kg | 75-230 cm | - |
| Dewasa | 15-400 kg | 90-260 cm | `waist_circumference` |

Response `201` berisi `measurement` penuh dengan `result`, `references`, `recommendation`, dan `trend_info`.

### PUT `/api/measurements/{measurement}`

Edit pemeriksaan yang sudah tersimpan. Endpoint ini dipakai mobile untuk fitur edit pemeriksaan semua role yang memiliki akses ke data tersebut. Field bersifat partial, tetapi jika field perhitungan dikirim maka hasil antropometri, rekomendasi, trend, dan data kehamilan dihitung ulang.

Request:

```json
{
  "measurement_date": "2026-05-13",
  "weight": 39,
  "height": 145.5,
  "head_circumference": null,
  "waist_circumference": null,
  "arm_circumference": null,
  "measurement_type": "berdiri",
  "is_pregnant": false,
  "pregnancy_start_date": null,
  "notes": "Koreksi hasil pemeriksaan"
}
```

### GET `/api/measurements`

History semua measurement sesuai policy user. Query: `from_date`, `to_date`, `category`, `search`, `only_trashed`, `per_page`.

### GET `/api/measurements/grouped`

History digrup per subject untuk tampilan riwayat mobile. Query: `search`, `only_trashed`, `per_page`.

### GET `/api/measurements/{measurement}`

Detail measurement.

### DELETE `/api/measurements/{measurement}`

Soft delete measurement.

### POST `/api/measurements/{id}/restore`

Restore measurement.

## Offline Sync

### POST `/api/sync/measurements`

Sinkronisasi batch dari mobile.

Request:

```json
{
  "records": [
    {
      "local_id": "uuid-123",
      "subject_id": 1,
      "measurement_date": "2026-05-13",
      "created_at_local": "2026-05-13T09:00:00+08:00",
      "hash": "AYU|2021-05-01|2026-05-13",
      "weight": 38,
      "height": 145
    }
  ]
}
```

Response:

```json
{
  "success": true,
  "message": "Sinkronisasi selesai",
  "data": {
    "synced": 1,
    "skipped": 0,
    "conflicts": []
  }
}
```

Jika validasi batch gagal atau terjadi konflik, backend mencatat `sync_validation_failed`, `sync_record_conflict`, atau `sync_endpoint_failed` ke activity log. Detail yang dicatat mencakup `local_id`, `subject_id`, tanggal pemeriksaan, jumlah record, dan alasan gagal supaya data lapangan dapat ditelusuri tanpa menghapus data lokal secara otomatis.

### POST `/api/activity-logs/client`

Menerima log lokal dari mobile ketika aplikasi kembali online. Dipakai untuk mencatat error offline, aksi tambah/edit/hapus, proses sync, dan konteks perangkat.

Request:

```json
{
  "level": "error",
  "action": "api_error",
  "message": "Koneksi gagal saat simpan pemeriksaan",
  "context": {
    "path": "/subjects/1/measurements",
    "method": "POST"
  },
  "occurred_at": "2026-05-13T08:30:00+08:00"
}
```

### GET `/api/activity-logs`

List log aktivitas. Admin dapat melihat semua log, sedangkan petugas hanya log miliknya sendiri.

Query:

| Parameter | Tipe | Keterangan |
| --- | --- | --- |
| `level` | string | `info`, `warning`, `error` |
| `action` | string | Filter aksi, contoh `login_success`, `client_api_error` |
| `search` | string | Cari action, model, IP, user agent |
| `per_page` | int | Default 20 |

## Export dan Import

### GET `/api/export/excel`

Export Excel. Query: `from_date`, `to_date`, `category`, `user_id`. Struktur output harus tetap selaras dengan template 15.000 peserta di `docs/Template_Perhitungan_Antropometri.xlsx` supaya data hasil export dapat dipindah ke template tanpa mengubah kolom.

### GET `/api/export/pdf`

Export PDF ringkasan. Query mengikuti filter statistik.

### POST `/api/import/measurements`

Import file Excel pengukuran.

Request multipart:

```text
file: Template_Perhitungan_Antropometri.xlsx
```

Response berisi jumlah baris imported dan skipped.

## Notifications

### GET `/api/notifications`

List notifikasi user aktif.

### POST `/api/notifications/{id}/read`

Tandai notifikasi sebagai sudah dibaca.

Email notifikasi sistem memakai `APP_DEEP_LINK_BASE`. Jika notification memiliki `measurement_id`, tombol email diarahkan ke `/measurements/{measurement_id}?subject_id={subject_id}` agar mobile membuka detail pemeriksaan langsung. Jika tidak ada measurement, tombol diarahkan ke `/notifications`.

## Admin API

Semua endpoint admin membutuhkan user dengan role `admin`.

### GET `/api/admin/statistics`

Query: `from_date`, `to_date`, `category`.

### GET `/api/admin/users`

Query: `search`, `per_page`.

### POST `/api/admin/users`

Request:

```json
{
  "name": "Petugas Baru",
  "email": "petugas.baru@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "petugas"
}
```

### PUT `/api/admin/users/{user}`

Partial update user. Field: `name`, `email`, `password`, `password_confirmation`, `role`.

### DELETE `/api/admin/users/{user}`

Soft delete user. Admin tidak dapat menghapus akun sendiri.

### POST `/api/admin/users/{user}/reset-password`

Generate password acak, simpan sebagai password baru, dan kirim ke email user. Response selalu menyertakan `new_password` untuk admin agar bisa disalin dan dikirim manual jika email gagal.

Request normal boleh kosong:

```json
{}
```

Untuk sinkronisasi dari mobile saat admin melakukan reset dalam kondisi offline, endpoint juga menerima password yang sudah dibuat di perangkat:

```json
{
  "password": "RandomPassword123"
}
```

Jika `password` dikirim, backend memakai password tersebut sebagai password baru lalu tetap mencoba mengirim email saat request tersinkron. Log aktivitas tidak menyimpan password.

## Database dan Migration NIK

Migration baru:

```text
database/migrations/2026_05_13_000001_remove_nik_from_subjects_table.php
```

Fungsi:

- `up()`: drop kolom `subjects.nik`
- `down()`: membuat ulang kolom `subjects.nik` sebagai `text nullable`

Sebelum migration ini dijalankan di server, backup NIK dan full database harus sudah tersedia. Catatan compare server ada di `../docs/database_compare.md`.

## Catatan Keamanan

- Token disimpan dengan Sanctum.
- Payload mobile dienkripsi oleh client dan middleware backend.
- Data NIK tidak lagi diterima, disimpan, diexport, atau dikirim oleh API aktif.
- File credential production tidak boleh masuk Git.
- Log aktivitas menyimpan konteks teknis seperlunya untuk audit dan debugging, bukan password atau token.
