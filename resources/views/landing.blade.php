<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antropometri — Aplikasi Pemantau Gizi</title>
    
    <meta name="description" content="Aplikasi pencatat dan pemantau gizi untuk balita, remaja, hingga dewasa. Solusi praktis, offline, dan gratis.">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('landing-assets/css/style.css?v=' . time()) }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; }
        .hero-gradient { background: linear-gradient(135deg, #f0f9ff 0%, #e1fcf2 100%); }
    </style>
</head>
<body class="bg-slate-50">

    <nav id="navbar" class="nav-glass fixed top-0 left-0 w-full z-50 transition-all duration-300">
        <div class="container mx-auto px-6 py-3 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div>
                    <img src="{{ asset('landing-assets/images/icon.png') }}" class="w-10 h-10 rounded-lg" alt="Logo">
                </div>
                <div class="flex flex-col">
                    <span class="text-xl font-extrabold text-slate-800 tracking-tight leading-none">ANTRO<span class="text-emerald-500">POMETRI</span></span>
                    <span class="text-[9px] uppercase tracking-[0.3em] font-bold text-slate-400 mt-1">Pemantau Gizi</span>
                </div>
            </div>
            <div class="hidden md:flex items-center space-x-8 text-sm font-semibold">
                <a href="#fitur" class="text-slate-600 hover:text-emerald-600 transition">Fitur App</a>
                <a href="#workflow" class="text-slate-600 hover:text-emerald-600 transition">Cara Kerja</a>
                <a href="#keamanan" class="text-slate-600 hover:text-emerald-600 transition">Keamanan</a>
                <a href="#harga" class="text-slate-600 hover:text-emerald-600 transition">Harga</a>
                <a href="#faq" class="text-slate-600 hover:text-emerald-600 transition">FAQ</a>
            </div>
            <button class="md:hidden text-slate-800 mobile-toggle p-2"><i class="fa fa-bars text-2xl"></i></button>
        </div>
        
        <div class="nav-links-mobile hidden md:hidden bg-white/90 backdrop-blur-2xl border-b border-white/20 absolute w-full left-0 px-8 py-12 flex flex-col gap-8 shadow-2xl ring-1 ring-black/5">
            <a href="#fitur" class="text-2xl font-extrabold text-slate-800 flex items-center justify-between group">
                <span>Fitur App</span>
                <i class="fa fa-chevron-right text-emerald-500 text-sm group-hover:translate-x-1 transition-transform"></i>
            </a>
            <a href="#workflow" class="text-2xl font-extrabold text-slate-800 flex items-center justify-between group">
                <span>Cara Kerja</span>
                <i class="fa fa-chevron-right text-emerald-500 text-sm group-hover:translate-x-1 transition-transform"></i>
            </a>
            <a href="#keamanan" class="text-2xl font-extrabold text-slate-800 flex items-center justify-between group">
                <span>Keamanan</span>
                <i class="fa fa-chevron-right text-emerald-500 text-sm group-hover:translate-x-1 transition-transform"></i>
            </a>
            <a href="#harga" class="text-2xl font-extrabold text-slate-800 flex items-center justify-between group">
                <span>Harga</span>
                <i class="fa fa-chevron-right text-emerald-500 text-sm group-hover:translate-x-1 transition-transform"></i>
            </a>
            <a href="#faq" class="text-2xl font-extrabold text-slate-800 flex items-center justify-between group">
                <span>FAQ</span>
                <i class="fa fa-chevron-right text-emerald-500 text-sm group-hover:translate-x-1 transition-transform"></i>
            </a>
            <div class="mt-4 pt-8 border-t border-slate-100">
                <a href="#" class="btn-primary-glass w-full text-center py-4 rounded-[10px] shadow-xl shadow-emerald-200/50">Unduh Sekarang</a>
            </div>
        </div>
    </nav>

    <section class="hero-gradient min-h-screen flex items-center pt-28 md:pt-32 pb-16 md:pb-20 relative overflow-hidden">
        <div class="hero-blob -top-20 -right-20"></div>
        <div class="hero-blob bottom-10 left-10"></div>
        
        <div class="container mx-auto px-4 md:px-6 relative z-10">
            <div class="flex flex-col lg:flex-row items-center gap-12 md:gap-16">
                <div class="w-full lg:w-1/2 text-center lg:text-left" data-aos="fade-up">
                    <div class="inline-flex items-center gap-2 px-4 py-2 mb-8 bg-emerald-100/50 rounded-full border border-emerald-200/50 backdrop-blur-sm">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-ping"></span>
                        <span class="text-[10px] font-black tracking-[0.2em] text-emerald-700 uppercase">100% Gratis & Bebas Iklan</span>
                    </div>
                    <h1 class="text-3xl sm:text-5xl md:text-7xl font-extrabold text-slate-900 leading-[1.1] md:leading-[1.05] mb-6 md:mb-8 lg:max-w-xl break-words">
                        Pantau Gizi <br>
                        <span class="text-emerald-500">Makin Mudah.</span>
                    </h1>
                    <p class="text-lg md:text-xl text-slate-600 mb-12 max-w-xl mx-auto lg:mx-0 leading-relaxed font-medium">
                        Aplikasi pencatatan praktis untuk memantau status gizi anak, remaja, hingga dewasa. <b class="text-emerald-600">Tetap bisa dipakai meski tidak ada sinyal internet</b> (offline mode), dan sepenuhnya gratis!
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center lg:justify-start gap-5">
                        <a href="#" class="btn-primary-glass text-lg px-12 py-5 shadow-2xl shadow-emerald-200/60 transition-all hover:scale-105 flex items-center justify-center gap-3">
                            Unduh Sekarang <i class="fab fa-android text-2xl"></i>
                        </a>
                        <a href="#workflow" class="px-10 py-5 rounded-[10px] font-bold border-2 border-slate-200 text-slate-700 bg-white/40 hover:bg-white hover:border-emerald-300 transition-all duration-300 flex items-center justify-center group">
                            Pelajari Caranya <i class="fa fa-arrow-right ml-3 text-sm group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
                <div class="w-full lg:w-1/2" data-aos="zoom-out" data-aos-delay="200">
                    <div class="relative max-w-sm md:max-w-lg mx-auto lg:mr-0">
                        <div class="absolute inset-0 bg-emerald-400/20 blur-[120px] rounded-full"></div>
                        <img src="{{ asset('landing-assets/images/hero.png') }}" class="relative z-10 w-full h-auto rounded-[30px] md:rounded-[40px] shadow-[0_50px_100px_-20px_rgba(0,0,0,0.25)] border-[8px] md:border-[12px] border-white/60" alt="Android App Mockup">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Workflow Section -->
    <section id="workflow" class="py-24 md:py-32 bg-white relative overflow-hidden">
        <div class="container mx-auto px-4 md:px-6">
            <div class="text-center max-w-3xl mx-auto mb-16 md:mb-24">
                <h2 class="text-[10px] md:text-sm font-black text-emerald-600 uppercase tracking-[0.3em] mb-4" data-aos="fade-up">Bagaimana Caranya?</h2>
                <h3 class="text-2xl sm:text-3xl md:text-5xl font-extrabold text-slate-900 mb-6 break-words px-2" data-aos="fade-up" data-aos-delay="100">Alur Penggunaan Aplikasi</h3>
                <p class="text-slate-500 text-sm md:text-lg px-2 sm:px-4" data-aos="fade-up" data-aos-delay="200">Simpel dan mudah dipahami. Kami merancangnya agar kamu bisa mencatat data gizi subjek secara cepat di lapangan.</p>
            </div>

            <div class="grid md:grid-cols-4 gap-12 relative">
                <!-- Step 1 -->
                <div class="step-item text-center group" data-aos="fade-up" data-aos-delay="100">
                    <div class="step-connector hidden md:block"></div>
                    <div class="step-number bg-slate-100 group-hover:bg-emerald-500 group-hover:text-white group-hover:border-emerald-500 transition-all duration-500 rounded-[10px]">01</div>
                    <h5 class="text-xl font-extrabold text-slate-800 mb-4 px-2">Instalasi Mudah</h5>
                    <p class="text-sm text-slate-500 leading-relaxed px-4">Tinggal unduh dan pasang di HP Android-mu, lalu daftar akun dalam beberapa langkah.</p>
                </div>
                <!-- Step 2 -->
                <div class="step-item text-center group" data-aos="fade-up" data-aos-delay="200">
                    <div class="step-connector hidden md:block"></div>
                    <div class="step-number bg-slate-100 group-hover:bg-emerald-500 group-hover:text-white group-hover:border-emerald-500 transition-all duration-500 rounded-[10px]">02</div>
                    <h5 class="text-xl font-extrabold text-slate-800 mb-4 px-2">Catat Saat Offline</h5>
                    <p class="text-sm text-slate-500 leading-relaxed px-4">Sedang tidak ada sinyal? Tenang, kamu tetap bisa melengkapi data ukur seperti biasa.</p>
                </div>
                <!-- Step 3 -->
                <div class="step-item text-center group" data-aos="fade-up" data-aos-delay="300">
                    <div class="step-connector hidden md:block"></div>
                    <div class="step-number bg-slate-100 group-hover:bg-emerald-500 group-hover:text-white group-hover:border-emerald-500 transition-all duration-500 rounded-[10px]">03</div>
                    <h5 class="text-xl font-extrabold text-slate-800 mb-4 px-2">Lihat Hasil Instan</h5>
                    <p class="text-sm text-slate-500 leading-relaxed px-4">Status gizinya langsung ketahuan otomatis, nggak perlu pusing menghitung manual lagi.</p>
                </div>
                <!-- Step 4 -->
                <div class="step-item text-center group" data-aos="fade-up" data-aos-delay="400">
                    <div class="step-number bg-slate-100 group-hover:bg-emerald-500 group-hover:text-white group-hover:border-emerald-500 transition-all duration-500 rounded-[10px]">04</div>
                    <h5 class="text-xl font-extrabold text-slate-800 mb-4 px-2">Sinkronisasi Datamu</h5>
                    <p class="text-sm text-slate-500 leading-relaxed px-4">Saat HP kamu kembali terhubung ke internet, server otomatis menyimpan datanya secara aman.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="fitur" class="py-24 md:py-32 bg-slate-50 relative">
        <div class="container mx-auto px-4 md:px-6">
            <div class="grid lg:grid-cols-2 gap-12 md:gap-20 items-center">
                <div data-aos="fade-right">
                    <h2 class="text-[10px] md:text-sm font-black text-emerald-600 uppercase tracking-[0.3em] mb-6">Fitur Keren</h2>
                    <h3 class="text-2xl sm:text-3xl md:text-5xl font-extrabold text-slate-900 mb-8 md:mb-10 leading-[1.2] md:leading-[1.1] break-words">Kenapa Pakai <br> Aplikasi Kami?</h3>
                    
                    <div class="grid sm:grid-cols-2 gap-8">
                        <div class="card-premium group" data-aos="fade-up" data-aos-delay="100">
                            <div class="w-14 h-14 bg-emerald-50 rounded-[10px] flex items-center justify-center text-emerald-500 mb-6 shadow-inner group-hover:bg-emerald-500 group-hover:text-white transition-all duration-500">
                                <i class="fa fa-mobile-screen text-2xl"></i>
                            </div>
                            <h6 class="font-extrabold text-slate-800 text-xl mb-4">Ringan & Mulus</h6>
                            <p class="text-sm text-slate-500 leading-relaxed font-medium">Buka aplikasi dengan cepat. Antarmukanya sudah disesuaikan agar berjalan lancar di berbagai tipe HP.</p>
                        </div>
                        <div class="card-premium group" data-aos="fade-up" data-aos-delay="200">
                            <div class="w-14 h-14 bg-blue-50 rounded-[10px] flex items-center justify-center text-blue-500 mb-6 shadow-inner group-hover:bg-blue-500 group-hover:text-white transition-all duration-500">
                                <i class="fa fa-wifi text-2xl"></i>
                            </div>
                            <h6 class="font-extrabold text-slate-800 text-xl mb-4">Cocok di Pelosok</h6>
                            <p class="text-sm text-slate-500 leading-relaxed font-medium">Sistem penyimpanan lokal membantu kamu memasukkan data ukur bahkan saat jaringan internet putus.</p>
                        </div>
                        <div class="card-premium group" data-aos="fade-up" data-aos-delay="300">
                            <div class="w-14 h-14 bg-purple-50 rounded-[10px] flex items-center justify-center text-purple-500 mb-6 shadow-inner group-hover:bg-purple-500 group-hover:text-white transition-all duration-500">
                                <i class="fa fa-users text-2xl"></i>
                            </div>
                            <h6 class="font-extrabold text-slate-800 text-xl mb-4">Balita s/d Dewasa</h6>
                            <p class="text-sm text-slate-500 leading-relaxed font-medium">Kamu nggak hanya bisa mengukur balita, tetapi juga ada fitur untuk remaja dan para orang dewasa.</p>
                        </div>
                        <div class="card-premium group" data-aos="fade-up" data-aos-delay="400">
                            <div class="w-14 h-14 bg-orange-50 rounded-[10px] flex items-center justify-center text-orange-500 mb-6 shadow-inner group-hover:bg-orange-500 group-hover:text-white transition-all duration-500">
                                <i class="fa fa-file-pdf text-2xl"></i>
                            </div>
                            <h6 class="font-extrabold text-slate-800 text-xl mb-4">Laporan PDF & Excel</h6>
                            <p class="text-sm text-slate-500 leading-relaxed font-medium">Hasil rekap ukur gampang banget diexport. Format datanya siap pakai dan dicetak buat dokumentasi.</p>
                        </div>
                    </div>
                </div>
                <div class="relative w-full" data-aos="fade-up">
                    <div class="bg-slate-900 rounded-[10px] p-8 md:p-12 lg:p-16 relative overflow-hidden shadow-xl">
                        <div class="absolute -bottom-20 -right-20 w-80 h-80 bg-emerald-500/20 blur-[80px] rounded-full"></div>
                        <div class="absolute top-10 left-10 w-20 h-20 bg-emerald-500/10 blur-[40px] rounded-full"></div>
                        
                        <div class="text-white relative z-10">
                            <h4 class="text-2xl md:text-4xl font-extrabold mb-8 leading-tight">Tak Perlu Cemas <br> Sinyal Jelek.</h4>
                            <p class="mb-10 text-slate-400 leading-relaxed text-sm md:text-lg font-medium">
                                Kalau kegiatan puskesmas atau pendataan ada di area minim sinyal, aplikasi ini bakal tetap aktif karena ada fitur sinkronisasi cerdas dari perangkatmu.
                            </p>
                            
                            <div class="space-y-4">
                                <div class="flex items-center gap-5 py-4 px-6 bg-white/5 rounded-[10px] backdrop-blur-md border border-white/10 hover:bg-white/10 transition-colors">
                                    <div class="w-12 h-12 bg-emerald-500 rounded-[10px] flex items-center justify-center text-white shadow-lg shadow-emerald-500/20">
                                        <i class="fa fa-database text-xl"></i>
                                    </div>
                                    <div>
                                        <span class="block text-sm font-bold">Simpan secara Lokal</span>
                                        <span class="text-[10px] text-slate-500 uppercase tracking-widest font-bold">Data tidak langsung hilang</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-5 py-4 px-6 bg-white/5 rounded-[10px] backdrop-blur-md border border-white/10 hover:bg-white/10 transition-colors">
                                    <div class="w-12 h-12 bg-blue-500 rounded-[10px] flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
                                        <i class="fa fa-cloud-arrow-up text-xl"></i>
                                    </div>
                                    <div>
                                        <span class="block text-sm font-bold">Backup Sinkron Online</span>
                                        <span class="text-[10px] text-slate-500 uppercase tracking-widest font-bold">Kirim ke server saat ada internet</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Security & Compliance -->
    <section id="keamanan" class="py-32 bg-slate-50 border-t border-slate-100">
        <div class="container mx-auto px-6">
            <div class="flex flex-col lg:flex-row items-center gap-20">
                <div class="w-full lg:w-1/2 order-2 lg:order-1" data-aos="fade-up">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative">
                        <div class="absolute inset-0 bg-blue-500/5 blur-[120px] pointer-events-none"></div>
                        <div class="bg-gradient-to-br from-slate-900 to-slate-800 p-6 md:p-8 rounded-[10px] text-white shadow-xl shadow-slate-900/10 border border-slate-700 hover:-translate-y-1 transition-transform duration-300">
                            <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center mb-6">
                                <i class="fa fa-shield-halved text-emerald-400 text-xl"></i>
                            </div>
                            <h4 class="text-3xl md:text-4xl font-extrabold text-emerald-400 mb-2 tracking-tight">100%</h4>
                            <p class="text-[10px] uppercase tracking-widest font-black text-slate-400">Privasi Terjaga</p>
                        </div>
                        <div class="bg-white p-6 md:p-8 rounded-[10px] shadow-sm border border-slate-200 hover:-translate-y-1 transition-transform duration-300">
                            <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center mb-6">
                                <i class="fa fa-user-lock text-emerald-600 text-xl"></i>
                            </div>
                            <h4 class="text-xl md:text-2xl font-extrabold text-slate-800 mb-2 tracking-tight">Akses Ketat</h4>
                            <p class="text-[10px] uppercase tracking-widest font-black text-slate-500">Otentikasi Berlapis</p>
                        </div>
                        <div class="sm:col-span-2 relative h-[250px] md:h-[300px] rounded-[10px] overflow-hidden shadow-lg border border-slate-200 bg-slate-900 flex items-center justify-center">
                            <img src="{{ asset('landing-assets/images/security.png') }}" class="w-full h-auto min-h-full object-cover group-hover:scale-105 transition-transform duration-700 opacity-50 mix-blend-overlay" alt="Security Illustration" onerror="this.style.display='none'">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent flex flex-col justify-end p-6 md:p-8">
                                <div class="flex items-center gap-4 mb-2">
                                    <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center text-white shadow-lg shadow-emerald-500/30"><i class="fa fa-server text-sm"></i></div>
                                    <span class="text-white font-extrabold text-lg tracking-tight">Infrastruktur Adil</span>
                                </div>
                                <p class="text-slate-300 text-xs md:text-sm font-medium ml-14">Data tersimpan aman dengan protokol enkripsi canggih.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full lg:w-1/2 order-1 lg:order-2" data-aos="fade-up">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 mb-6 bg-blue-50/50 rounded-full border border-blue-100/50 backdrop-blur-sm">
                        <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                        <span class="text-[9px] md:text-[10px] font-black tracking-[0.2em] text-blue-700 uppercase">Enterprise Grade Security</span>
                    </div>
                    <h3 class="text-2xl sm:text-3xl md:text-5xl font-extrabold text-slate-900 mb-6 leading-[1.2] md:leading-[1.15] break-words">Lindungi Data <br> Medis Sensitif.</h3>
                    <p class="text-slate-600 text-sm md:text-lg mb-8 leading-relaxed font-medium px-2">
                        Catatan status gizi adalah privasi yang harus dijaga ketat. Sistem kami dibangun dengan arsitektur keamanan berlapis yang menjamin eksklusivitas akses data hanya untuk tenaga medis berwenang.
                    </p>
                    <div class="space-y-4 mb-10">
                        <div class="flex items-center gap-5 p-4 rounded-[10px] hover:bg-white hover:shadow-md border border-transparent hover:border-slate-100 transition-all duration-300">
                            <div class="w-12 h-12 rounded-[10px] bg-blue-50 text-blue-600 flex items-center justify-center shadow-inner"><i class="fa fa-key text-lg"></i></div>
                            <div>
                                <span class="block font-extrabold text-slate-800 text-base">Token-Based Authentication</span>
                                <span class="text-sm text-slate-500 font-medium">Akses login divalidasi dengan token terenkripsi.</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-5 p-4 rounded-[10px] hover:bg-white hover:shadow-md border border-transparent hover:border-slate-100 transition-all duration-300">
                            <div class="w-12 h-12 rounded-[10px] bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-inner"><i class="fa fa-users-viewfinder text-lg"></i></div>
                            <div>
                                <span class="block font-extrabold text-slate-800 text-base">Isolasi Data Per-Pengguna</span>
                                <span class="text-sm text-slate-500 font-medium">Petugas hanya dapat melihat hasil ukurnya sendiri.</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-5 p-4 rounded-[10px] hover:bg-white hover:shadow-md border border-transparent hover:border-slate-100 transition-all duration-300">
                            <div class="w-12 h-12 rounded-[10px] bg-purple-50 text-purple-600 flex items-center justify-center shadow-inner"><i class="fa fa-book-medical text-lg"></i></div>
                            <div>
                                <span class="block font-extrabold text-slate-800 text-base">Sesuai Standar Kesehatan</span>
                                <span class="text-sm text-slate-500 font-medium">Metode penyimpanan sesuai pedoman privasi klinis.</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        <a href="#harga" class="w-full sm:w-auto text-center px-8 py-4 bg-slate-900 text-white rounded-[10px] font-bold shadow-xl shadow-slate-900/20 hover:bg-emerald-600 hover:shadow-emerald-600/30 transition-all duration-300 hover:-translate-y-1">Mulai Gunakan Gratis</a>
                        <a href="#workflow" class="w-full sm:w-auto text-center px-8 py-4 text-slate-600 font-bold rounded-[10px] hover:bg-slate-200/50 transition-colors">Pelajari Sistemnya</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Impact Section -->
    <section class="py-24 bg-slate-900 relative overflow-hidden text-white">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-5"></div>
        <div class="container mx-auto px-6 relative z-10">
            <div class="grid md:grid-cols-3 gap-12 text-center divide-y md:divide-y-0 md:divide-x divide-slate-800">
                <div class="p-4" data-aos="fade-up">
                    <h3 class="text-2xl font-black text-emerald-400 mb-2">Sangat Simpel</h3>
                    <p class="text-slate-400 font-medium uppercase tracking-widest text-sm">Tanpa Belajar Bikin Pusing</p>
                </div>
                <div class="p-4" data-aos="fade-up" data-aos-delay="100">
                    <h3 class="text-2xl font-black text-emerald-400 mb-2">Pemakaian Offline</h3>
                    <p class="text-slate-400 font-medium uppercase tracking-widest text-sm">Masuk Desa dan Area Susah Sinyal Tetap Bisa</p>
                </div>
                <div class="p-4" data-aos="fade-up" data-aos-delay="200">
                    <h3 class="text-2xl font-black text-emerald-400 mb-2">Selalu Gratis</h3>
                    <p class="text-slate-400 font-medium uppercase tracking-widest text-sm">Memang Buat Kepentingan Bersama</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing / Free Tier -->
    <section id="harga" class="py-24 md:py-32 bg-emerald-50 text-center relative">
        <div class="absolute inset-0 top-0 bg-gradient-to-b from-white to-emerald-50"></div>
        <div class="container mx-auto px-4 md:px-6 relative z-10">
            <h2 class="text-[10px] md:text-sm font-black text-emerald-600 uppercase tracking-[0.3em] mb-4" data-aos="fade-up">Gak Pake Bayar</h2>
            <h3 class="text-2xl sm:text-3xl md:text-5xl font-extrabold text-slate-900 mb-6 break-words" data-aos="fade-up" data-aos-delay="100">Berapa Sih Biayanya?</h3>
            <p class="text-slate-500 text-sm md:text-lg mb-12 md:mb-16 max-w-2xl mx-auto font-medium px-4" data-aos="fade-up" data-aos-delay="200">Aplikasi Antropometri ini murni <span class="font-bold text-emerald-600">terbuka buat kamu pakai</span>. Cuma butuh di-download, dan fasilitas penuhnya bebas kamu nikmati tanpa pungutan iuran bulanan.</p>
            
            <div class="max-w-md mx-auto card-premium relative group" data-aos="zoom-in" data-aos-delay="300">
                <div class="absolute -top-6 left-1/2 -translate-x-1/2 bg-gradient-to-r from-emerald-400 to-emerald-600 text-white px-8 py-2.5 rounded-full font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-emerald-500/30 group-hover:scale-110 transition-transform duration-500">Benar-Benar Gratis</div>
                <h4 class="text-3xl font-extrabold text-slate-800 mb-2 mt-4">Free Akses</h4>
                <p class="text-slate-500 mb-8 font-medium">Bisa buka ke semua menu pengukuran bawaan aplikasi ini tanpa dibatasi.</p>
                <div class="text-7xl font-black text-slate-900 mb-10 tracking-tighter">Rp0<span class="text-xl text-slate-400 font-medium tracking-normal">/selamanya</span></div>
                
                <ul class="space-y-5 text-left mb-12">
                    <li class="flex items-center gap-4"><i class="fa fa-circle-check text-emerald-500 text-2xl"></i> <span class="font-bold text-slate-700">Mode Offline Full</span></li>
                    <li class="flex items-center gap-4"><i class="fa fa-circle-check text-emerald-500 text-2xl"></i> <span class="font-bold text-slate-700">Analisis Gizi Otomatis</span></li>
                    <li class="flex items-center gap-4"><i class="fa fa-circle-check text-emerald-500 text-2xl"></i> <span class="font-bold text-slate-700">Daftar Profil Lengkap</span></li>
                    <li class="flex items-center gap-4"><i class="fa fa-circle-check text-emerald-500 text-2xl"></i> <span class="font-bold text-slate-700">Sinkronisasi Cloud Cerah</span></li>
                    <li class="flex items-center gap-4"><i class="fa fa-circle-check text-emerald-500 text-2xl"></i> <span class="font-bold text-slate-700">Laporan PDF & Excel</span></li>
                </ul>
                
                <a href="#" class="btn-primary-glass w-full text-center py-5 shadow-emerald-500/20 text-lg rounded-[10px]">Download Sekarang</a>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-32 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-20">
                <h3 class="text-4xl font-extrabold text-slate-900 mb-4">Hal Yang Sering Ditanya</h3>
                <div class="w-24 h-1.5 bg-emerald-500 mx-auto rounded-full"></div>
            </div>
            
            <div class="max-w-3xl mx-auto space-y-6">
                <!-- FAQ 1 -->
                <div class="card-premium group cursor-pointer" onclick="toggleFaq(1)">
                    <div class="flex justify-between items-center">
                        <h5 class="font-extrabold text-slate-800 text-lg group-hover:text-emerald-600 transition-colors">Aplikasi ini bisa dipakai di versi Android yang lawas gak?</h5>
                        <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center group-hover:bg-emerald-500 group-hover:text-white transition-all duration-500">
                            <i id="faq-icon-1" class="fa fa-plus text-sm transition-transform"></i>
                        </div>
                    </div>
                    <div id="faq-answer-1" class="hidden mt-6 text-slate-500 font-medium leading-relaxed border-t pt-6 border-slate-100 italic">
                        Tenang, aplikasinya didesain ringan supaya tetap suport sama sebagian besar HP Android, termasuk versi-versi yang lama.
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="card-premium group cursor-pointer" onclick="toggleFaq(2)">
                    <div class="flex justify-between items-center">
                        <h5 class="font-extrabold text-slate-800 text-lg group-hover:text-emerald-600 transition-colors">Gimana kalau pas mengukur, lagi di lokasi gak ada jaringan sama sekali?</h5>
                        <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center group-hover:bg-emerald-500 group-hover:text-white transition-all duration-500">
                            <i id="faq-icon-2" class="fa fa-plus text-sm transition-transform"></i>
                        </div>
                    </div>
                    <div id="faq-answer-2" class="hidden mt-6 text-slate-500 font-medium leading-relaxed border-t pt-6 border-slate-100 italic">
                        Aplikasi ini punya mode offline yang tetep bisa menghitung dan menyimpan data. Nanti sewaktu kamu balik ke daerah ada jaringan internet, file di HP kamu akan dikirim rapi ke server.
                    </div>
                </div>
                
                <!-- FAQ 3 -->
                <div class="card-premium group cursor-pointer" onclick="toggleFaq(3)">
                    <div class="flex justify-between items-center">
                        <h5 class="font-extrabold text-slate-800 text-lg group-hover:text-emerald-600 transition-colors">Ukurannya berat gak kalau diinstal?</h5>
                        <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center group-hover:bg-emerald-500 group-hover:text-white transition-all duration-500">
                            <i id="faq-icon-3" class="fa fa-plus text-sm transition-transform"></i>
                        </div>
                    </div>
                    <div id="faq-answer-3" class="hidden mt-6 text-slate-500 font-medium leading-relaxed border-t pt-6 border-slate-100 italic">
                        Enggak kok. Ukurannya lumayan kecil jadi penyimpanan memori HP kamu nggak bakal penuh dan gak bikin lemot.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Floating Action Button (FAB) -->
    <div class="fab-container lg:hidden">
        <a href="#" class="fab-btn fab-chat" title="Tanya Kami">
            <i class="fab fa-whatsapp text-2xl"></i>
        </a>
        <a href="#" class="fab-btn fab-download shadow-emerald-500/50" title="Download Disini">
            <i class="fa fa-download text-xl"></i>
        </a>
    </div>

    <!-- Footer -->
    <footer class="bg-slate-900 border-t border-slate-800 pt-24 pb-12 text-slate-500">
        <div class="container mx-auto px-6">
            <div class="grid lg:grid-cols-4 gap-16 mb-20 pb-16 border-b border-slate-800">
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-3 mb-8">
                        <div>
                            <img src="{{ asset('landing-assets/images/icon.png') }}" class="w-10 h-10 rounded-md border border-white" alt="Logo">
                        </div>
                        <span class="text-xl font-black text-white tracking-widest uppercase">Antropometri</span>
                    </div>
                    <p class="max-w-md mb-8 text-lg font-medium leading-relaxed">Mencatat, memantau, dan menjaga kondisi gizi agar tetap terpantau dengan mudah hanya dari HP kamu.</p>
                </div>
                <div>
                    <h6 class="text-white font-black uppercase text-xs mb-8 tracking-[0.2em]">Fitur</h6>
                    <ul class="space-y-5 text-sm font-bold">
                        <li><a href="#" class="hover:text-emerald-400 transition-colors">Aplikasi Android</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition-colors">Dukungan Offline</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition-colors">Cetak PDF & Excel Laporan</a></li>
                    </ul>
                </div>
                <div>
                    <h6 class="text-white font-black uppercase text-xs mb-8 tracking-[0.2em]">Bantuan</h6>
                    <ul class="space-y-5 text-sm font-bold">
                        <li><a href="#faq" class="hover:text-emerald-400 transition-colors">FAQ</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition-colors">Panduan Aplikasi</a></li>
                        <li><a href="/privacy-policy" class="hover:text-emerald-400 transition-colors">Kebijakan Privasi</a></li>
                    </ul>
                </div>
                <div>
                    <h6 class="text-white font-black uppercase text-xs mb-8 tracking-[0.2em]">Samrifa Studio</h6>
                    <ul class="space-y-5 text-sm font-bold">
                        <li><a href="https://samrifa.com" target="_blank" class="hover:text-emerald-400 transition-colors flex items-center gap-2">Website Utama <i class="fa fa-external-link text-[10px]"></i></a></li>
                        <li><a href="https://products.samrifa.com" target="_blank" class="hover:text-emerald-400 transition-colors flex items-center gap-2">Daftar Produk <i class="fa fa-external-link text-[10px]"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="flex flex-col md:flex-row justify-between items-center gap-8 text-center md:text-left">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] mb-2 text-slate-600">Dikembangkan Oleh</p>
                    <a href="https://samrifa.com" target="_blank" class="text-xl font-black text-white hover:text-emerald-400 transition-colors tracking-tighter">
                        SAMRIFA <span class="text-emerald-500">STUDIO</span> TEKNOLOGI
                    </a>
                </div>
                <div class="flex flex-col md:items-end gap-3">
                    <p class="text-xs font-bold uppercase tracking-widest">© 2026 Antropometri. Aplikasi Pemantauan Gizi.</p>
                    <div class="flex items-center justify-center md:justify-end gap-4 text-xs font-black">
                        <span class="px-4 py-1.5 bg-emerald-500/10 text-emerald-400 rounded-full border border-emerald-500/20 backdrop-blur-sm">VERSION 1.0.0 — STABLE</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="{{ asset('landing-assets/js/main.js') }}"></script>
    <script>
        AOS.init({ 
            duration: 800, 
            once: true,
            offset: 50,
            disable: window.innerWidth < 768
        });
        
        function toggleFaq(id) {
            const answer = document.getElementById(`faq-answer-${id}`);
            const icon = document.getElementById(`faq-icon-${id}`);
            const isActive = !answer.classList.contains('hidden');
            
            if (isActive) {
                answer.classList.add('hidden');
                icon.classList.replace('fa-minus', 'fa-plus');
                icon.style.transform = 'rotate(0deg)';
            } else {
                answer.classList.remove('hidden');
                icon.classList.replace('fa-plus', 'fa-minus');
                icon.style.transform = 'rotate(180deg)';
            }
        }
    </script>
</body>
</html>
