# Standar Rekayasa Perangkat Lunak & Workflow Antropometri

Dokumen ini adalah aturan paten dan pedoman utama bagi seluruh AI Agent, pair programmer, dan developer dalam memodifikasi, menguji, mendokumentasikan, dan merilis kode pada ekosistem **Antropometri** (Mobile Flutter & Backend Laravel 12).

---

## 1. Aturan Tampilan & Aset Visual: DILARANG MENGGUNAKAN EMOJI

> [!CAUTION]
> **Kebijakan Larangan Emoji (Strict Emoji Ban)**:
> 1. **Dilarang Keras Menggunakan Emoji Unicode Mentah**: Jangan pernah menyematkan emoji (seperti 👶, 🤰, ⚖️, 📱, ☁️, 👨‍⚕️, 🛡️, dll.) baik pada kode antarmuka (UI Flutter / Web HTML / Blade), maupun pada diagram arsitektur (Mermaid).
> 2. **Wajib Menggunakan Icon Resmi Developer**:
>    - **Flutter Mobile**: Gunakan icon resmi dari SDK Material (`Icons.child_care_rounded`, `Icons.pregnant_woman_rounded`, `Icons.calculate_rounded`, `Icons.sync_rounded`, `Icons.history_rounded`, dll.) atau Cupertino (`CupertinoIcons.*`).
>    - **Web Platform**: Gunakan icon font/SVG resmi developer (misal: Lucide Icons, Heroicons, Material Symbols, FontAwesome).
>    - **Diagram Mermaid**: Gunakan teks teknis profesional, bersih, dan ringkas tanpa karakter emoji atau simbol non-standar yang dapat merusak parser lexer.

---

## 2. Arsitektur & Struktur Kode

### A. Mobile Application (Flutter)
1. **Clean & Modular Structure**:
   - `lib/models/`: Entity & serialisasi data (immutable, `copyWith`, `fromJson`/`toJson`).
   - `lib/services/`: Komunikasi API (`Dio`), database lokal (`Sqflite`), sinkronisasi (`SyncService`), ekspor/impor, dan kalkulasi lokal offline (`LocalCalculationService`).
   - `lib/pages/`: Halaman UI berdasarkan domain: `auth/`, `petugas/`, `admin/`, `guest/`, `guide/`, `intro/`, `common/`.
   - `lib/widgets/`: Komponen UI modular dan reusable (tidak menduplikasi UI kompleks antar halaman).
   - `lib/utils/`: Helper formatting, status gizi, konversi, error handler.
   - `lib/config/`: Rute aplikasi (`GoRouter`), palet warna (`AppColors`), tema (`AppTheme`).
2. **Prinsip Anti-Duplikasi (DRY) & Tampilan Cepat**:
   - Dilarang keras menduplikasi logic perhitungan Z-Score atau konfigurasi grafik `fl_chart` di halaman UI. Gunakan service dan widget modular yang sudah teruji.
   - Gunakan antarmuka yang cepat, ringkas, tanpa tombol atau menu membingungkan.
   - Gunakan `AppLogService` untuk pencatatan debug/error; hindari `print()` mentah pada kode produksi.

### B. Backend API & Web Platform (Laravel 12)
1. **Clean MVC & Service Pattern**:
   - `app/Http/Controllers/Api/`: Kontroler ringkas, delegasikan validasi ke Form Requests.
   - `app/Actions/`: Logika perhitungan gizi spesifik (`CalculateBalitaAction`, `CalculateRemajaAction`, `CalculateDewasaAction`).
   - `app/Services/`: Business logic, sinkronisasi batch, export/import service.
   - `app/Models/`: Model Eloquent dengan trait isolasi kepemilikan data (`BelongsToUser`).
   - `app/Http/Resources/`: Standardisasi format JSON response.
2. **Security & Data Ownership**:
   - Autentikasi menggunakan Laravel Sanctum Bearer Token.
   - Role-based authorization (`admin` vs `petugas`).
   - Enkripsi data sensitif (PII) dan perlindungan rate-limiting pada endpoint publik.

---

## 3. Standar Kompatibilitas Dua Arah Excel (100% Round-Trip)

1. **Format Ekspor dan Impor Identik**: File Excel yang diekspor dari sistem harus dapat diisi atau dimodifikasi langsung di Microsoft Excel dan diimpor kembali ke sistem secara mulus tanpa perubahan header atau kolom.
2. **Struktur Kolom Tetap**: Mengikuti template resmi `Template_Perhitungan_Antropometri.xlsx` (Sheet 1: Balita, Sheet 2: Remaja & Dewasa).
3. **Sinkronisasi Rumus**: Rumus formula pada Excel harus menghasilkan status gizi yang identik dengan mesin kalkulasi aplikasi.

---

## 4. Kebijakan Kualitas (Zero-Error & Zero-Warning)

Setiap perubahan kode harus mematuhi standar mutu tanpa kompromi:
1. **Flutter Mobile**:
   - Wajib menjalankan `flutter analyze` dan memastikan **0 errors** dan **0 warnings**.
   - Wajib menjalankan `flutter test` dan memastikan seluruh unit test lulus (100% pass).
2. **Laravel Backend**:
   - Wajib menjalankan `php artisan test` dan memastikan seluruh Feature & Unit test lulus (100% pass).

---

## 5. Standar Git, Commit, & Remote Push

1. **Format Commit (Conventional Commits)**:
   - `feat(...)`: Penambahan fitur baru.
   - `fix(...)`: Perbaikan bug atau penyesuaian logika.
   - `perf(...)`: Optimasi performa dan kecepatan render/query.
   - `refactor(...)`: Perapihan struktur kode tanpa merubah fungsionalitas.
   - `docs(...)`: Pembaruan dokumentasi.
   - `chore(...)`: Penyesuaian build, dependensi, atau konfigurasi.
2. **Remote Repositories**:
   - Mobile: `git@samrifa.com:S4MR1F4/antropometri-mobile.git` (Branch: `main`)
   - Backend: `git@samrifa.com:S4MR1F4/antropometri-backend.git` (Branch: `main`)

---

## 6. Standar Rilis & Git Tagging

1. **Semantic Versioning (`vX.Y.Z`)**:
   - `X` = Major release.
   - `Y` = Minor release.
   - `Z` = Patch release.
2. **Langkah Pembuatan Tag**:
   - Wajib menggunakan annotated tag:
     ```bash
     git tag -a vX.Y.Z -m "Release vX.Y.Z - <Ringkasan Perubahan>"
     ```
   - Push tag ke remote origin:
     ```bash
     git push origin --tags
     ```

---

## 7. SOP Outline: Pengelolaan Tugas & Preservasi Konteks AI

1. **Outline API Endpoint**:
   - URL: `https://outline.samrifa.com/api`
   - Collection: **`Antropometri`**
2. **SOP Sebelum Memulai Pengerjaan (Dev Task Document)**:
   - Saat akan mengerjakan fitur besar, refactoring, atau perbaikan bug krusial, buat dokumen tugas di Outline dengan judul berawalan `[DEV-TASK] <Nama Fitur/Perbaikan>`.
   - Dokumen ini berisi:
     - Latar belakang & sasaran perbaikan.
     - Daftar file yang akan dimodifikasi/dibuat.
     - Checklist langkah kerja dan kriteria pengujian.
3. **SOP Setelah Rilis Selesai (Cleanup Protocol)**:
   - Jalankan seluruh suite pengujian (`flutter analyze`, `flutter test`, `php artisan test`).
   - Lakukan commit dan push dengan annotated tag rilis.
   - Perbarui catatan rilis resmi pada dokumen master di Outline.
   - **HAPUS** dokumen `[DEV-TASK]` sementara tersebut dari Outline agar collection tetap bersih dan rapi.
