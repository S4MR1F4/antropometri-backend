# Petunjuk Utama Proyek Antropometri (GEMINI.md)

Dokumen ini adalah acuan kerja mengikat bagi seluruh eksekusi agen AI di repositori **Antropometri**.

---

## 1. Aturan Aset Visual: DILARANG MENGGUNAKAN EMOJI
- Dilarang keras menyematkan emoji mentah (seperti 👶, 🤰, ⚖️, 📱, dll.) pada kode UI (Flutter / Blade) maupun diagram arsitektur (Mermaid).
- Wajib menggunakan icon resmi developer:
  - Mobile: SDK Material Icons (`Icons.child_care_rounded`, `Icons.pregnant_woman_rounded`, `Icons.calculate_rounded`, dll.) atau Cupertino.
  - Web: Icon SVG / Web Font developer resmi (Material Symbols, Lucide, Heroicons).
  - Mermaid: Gunakan teks teknis profesional tanpa karakter emoji atau simbol non-standar.

---

## 2. Navigasi & Intelijen Kode (CodeGraph Mandatory)
- Gunakan tool `codegraph` (`codegraph_explore` atau CLI `codegraph`) sebagai sarana eksplorasi kode utama untuk menelusuri simbol, relasi pemanggil/pemanggilan (*call path*), dan analisis dampak perubahan (*blast radius*).
- Indeks codegraph berada di root direktori `.codegraph/`.

---

## 3. Kualitas Kode & Kebijakan Zero-Error
1. **Flutter Mobile (`/mobile`)**:
   - `flutter analyze` wajib menghasilkan **0 issues** (0 errors, 0 warnings).
   - Seluruh unit test pada `flutter test` wajib lulus 100%.
   - Gunakan `AppLogService` untuk logging; hindari `print()` mentah.
   - Hindari duplikasi logika visualisasi dan perhitungan di halaman UI. Gunakan reusable components.
   - Tampilan wajib simpel, cepat, tidak bertele-tele, fokus pada Input -> Proses -> Output.
2. **Laravel Backend (`/backend`)**:
   - `php artisan test` wajib lulus 100%.
   - Pastikan isolasi kepemilikan data (`BelongsToUser`) dan Sanctum auth selalu terjaga.
3. **Standar Round-Trip Excel (100% Kompatibel Dua Arah)**:
   - Format ekspor sistem dan format impor Excel wajib identik 100%. Data yang diekspor harus dapat diedit dan diimpor ulang tanpa merusak kolom atau formula.

---

## 4. Git Workflow, Commit, & Tagging Rilis
1. **Conventional Commits**: Format commit wajib menggunakan `feat:`, `fix:`, `perf:`, `docs:`, `chore:`, `refactor:`.
2. **Remote Origin**:
   - Backend: `git@samrifa.com:S4MR1F4/antropometri-backend.git`
   - Mobile: `git@samrifa.com:S4MR1F4/antropometri-mobile.git`
3. **Release & Tagging**:
   - Format tag: `vX.Y.Z` (Semantic Versioning).
   - Wajib annotated tag: `git tag -a vX.Y.Z -m "Release vX.Y.Z - <keterangan>"`.
   - Push tags: `git push origin --tags`.

---

## 5. SOP Dokumentasi Outline & Preservasi Konteks AI
- **Outline URL**: `https://outline.samrifa.com/api`
- **Collection**: `Antropometri`
- **Task Planning (Anti Context Loss)**:
  - Sebelum memulai pengerjaan perubahan besar / fitur, buat dokumen tugas sementara di Outline dengan format `[DEV-TASK] <Judul Task>`.
  - Catat sasaran, rencana perubahan berkas, dan checklist pengujian.
- **Pembersihan Pasca Rilis**:
  - Setelah kode lolos verifikasi, dikomit, dibuat tag rilis, dan di-push ke remote, update catatan rilis pada dokumen master di Outline.
  - Hapus dokumen `[DEV-TASK]` sementara tersebut agar Outline tetap bersih dan terawat.
