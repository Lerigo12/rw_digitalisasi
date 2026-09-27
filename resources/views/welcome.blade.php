<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RW 29 Alamanda Regency Blok I – Portal Digital Warga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-emerald-600 selection:text-white">

    <!-- Navbar -->
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-white/80 backdrop-blur-md border-b border-slate-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo & Name -->
                <a href="#" class="flex items-center gap-3 group">
                    <div class="w-11 h-11 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-extrabold text-lg shadow-md shadow-emerald-600/20 group-hover:scale-105 transition-transform">
                        29
                    </div>
                    <div>
                        <span class="block font-extrabold text-slate-900 text-lg leading-tight">RW 29</span>
                        <span class="block text-xs font-semibold text-emerald-700 tracking-wider uppercase">Alamanda Regency Blok I</span>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex items-center gap-8">
                    <a href="#beranda" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Beranda</a>
                    <a href="#tentang" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Tentang RW</a>
                    <a href="#layanan" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Layanan</a>
                    <a href="#alur" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Alur</a>
                    <a href="#pengumuman" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Pengumuman</a>
                    <a href="#kegiatan" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Kegiatan</a>
                    <a href="#transparansi" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Transparansi</a>
                    <a href="#galeri" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Galeri</a>
                    <a href="#kontak" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Kontak</a>
                </div>

                <!-- Action Button -->
                <div class="hidden lg:flex items-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold shadow-lg shadow-emerald-600/20 transition-all hover:-translate-y-0.5">
                            <i class="fa-solid fa-gauge-high mr-2"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold shadow-lg shadow-emerald-600/20 transition-all hover:-translate-y-0.5">
                            <i class="fa-solid fa-right-to-bracket mr-2"></i> Login
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <div class="lg:hidden flex items-center">
                    <button id="mobile-menu-btn" class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700 hover:bg-slate-200 transition-colors">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Modal/Dropdown -->
        <div id="mobile-menu" class="hidden lg:hidden bg-white border-b border-slate-200 px-6 py-6 space-y-4 shadow-xl">
            <a href="#beranda" class="block text-base font-semibold text-slate-700 hover:text-emerald-600">Beranda</a>
            <a href="#tentang" class="block text-base font-semibold text-slate-700 hover:text-emerald-600">Tentang RW</a>
            <a href="#layanan" class="block text-base font-semibold text-slate-700 hover:text-emerald-600">Layanan</a>
            <a href="#alur" class="block text-base font-semibold text-slate-700 hover:text-emerald-600">Alur Pelayanan</a>
            <a href="#pengumuman" class="block text-base font-semibold text-slate-700 hover:text-emerald-600">Pengumuman</a>
            <a href="#kegiatan" class="block text-base font-semibold text-slate-700 hover:text-emerald-600">Agenda Kegiatan</a>
            <a href="#transparansi" class="block text-base font-semibold text-slate-700 hover:text-emerald-600">Transparansi Keuangan</a>
            <a href="#galeri" class="block text-base font-semibold text-slate-700 hover:text-emerald-600">Galeri</a>
            <a href="#kontak" class="block text-base font-semibold text-slate-700 hover:text-emerald-600">Kontak</a>
            <div class="pt-4 border-t border-slate-100 flex flex-col gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="w-full py-3 rounded-xl bg-emerald-600 text-white text-center font-bold shadow-md">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="w-full py-3 rounded-xl bg-emerald-600 text-white text-center font-bold shadow-md">Login Warga</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="beranda" class="relative pt-32 pb-20 md:pt-44 md:pb-32 overflow-hidden bg-gradient-to-b from-emerald-50/60 via-white to-white">
        <!-- Decorative Glow -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-96 bg-gradient-to-r from-emerald-500/10 to-amber-500/10 blur-3xl pointer-events-none -z-10"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                <!-- Left Content -->
                <div class="lg:col-span-7 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-100/80 text-emerald-800 text-xs font-bold uppercase tracking-wider mb-6 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                        DIGITALISASI RW 29
                    </div>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-[1.15]">
                        Selamat Datang di Portal Digital <span class="text-emerald-600">RW 29</span>
                    </h1>
                    <div class="mt-3 text-lg sm:text-xl font-bold text-emerald-800/80 tracking-wide">
                        Alamanda Regency Blok I
                    </div>
                    <p class="mt-6 text-slate-600 text-base sm:text-lg leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        "Platform digital warga RW 29 untuk mendapatkan informasi, mengakses layanan administrasi, mengikuti kegiatan, dan membangun lingkungan yang lebih tertib, transparan, dan terhubung."
                    </p>
                    <div class="mt-8 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        <a href="#layanan" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-center shadow-lg shadow-emerald-600/30 transition-all hover:-translate-y-0.5">
                            Jelajahi Layanan <i class="fa-solid fa-arrow-right ml-2"></i>
                        </a>
                        <a href="#tentang" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-bold text-center transition-all hover:-translate-y-0.5">
                            Tentang RW 29
                        </a>
                    </div>
                </div>

                <!-- Right Visual / Image + Floating Card -->
                <div class="lg:col-span-5 relative">
                    <div class="relative mx-auto max-w-md lg:max-w-none">
                        <!-- Main Image Frame -->
                        <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white bg-white">
                            <img src="{{ asset('storage/images/DSC_0204.JPG') }}" alt="Lingkungan Perumahan Alamanda Regency" class="w-full h-[400px] object-cover hover:scale-105 transition-transform duration-700">
                        </div>
                        <!-- Floating Card -->
                        <div class="absolute -bottom-6 -left-6 sm:-left-10 bg-white/95 backdrop-blur-md p-5 rounded-2xl shadow-xl border border-slate-100 flex items-center gap-4 max-w-xs animate-bounce-slow">
                            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                                <i class="fa-solid fa-house-signal"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">Warga Terhubung</h4>
                                <p class="text-xs text-slate-500 mt-0.5">"Portal informasi dan pelayanan digital RW 29"</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistik RW -->
    <section class="py-16 bg-white border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Stat 1 -->
                <div class="p-6 rounded-2xl bg-emerald-50/50 border border-emerald-100 text-center hover:shadow-lg transition-all">
                    <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center mx-auto mb-4 text-xl shadow-md shadow-emerald-600/20">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <div class="text-3xl lg:text-4xl font-extrabold text-slate-900 count" data-target="29">0</div>
                    <div class="text-sm font-semibold text-emerald-700 mt-1 uppercase tracking-wider">RW</div>
                </div>
                <!-- Stat 2 -->
                <div class="p-6 rounded-2xl bg-emerald-50/50 border border-emerald-100 text-center hover:shadow-lg transition-all">
                    <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center mx-auto mb-4 text-xl shadow-md shadow-emerald-600/20">
                        <i class="fa-solid fa-signs-post"></i>
                    </div>
                    <div class="text-3xl lg:text-4xl font-extrabold text-slate-900 count" data-target="8">0</div>
                    <div class="text-sm font-semibold text-emerald-700 mt-1 uppercase tracking-wider">RT</div>
                </div>
                <!-- Stat 3 -->
                <div class="p-6 rounded-2xl bg-emerald-50/50 border border-emerald-100 text-center hover:shadow-lg transition-all">
                    <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center mx-auto mb-4 text-xl shadow-md shadow-emerald-600/20">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div class="text-3xl lg:text-4xl font-extrabold text-slate-900 count" data-target="450">0</div>
                    <div class="text-sm font-semibold text-emerald-700 mt-1 uppercase tracking-wider">Warga</div>
                </div>
                <!-- Stat 4 -->
                <div class="p-6 rounded-2xl bg-emerald-50/50 border border-emerald-100 text-center hover:shadow-lg transition-all">
                    <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center mx-auto mb-4 text-xl shadow-md shadow-emerald-600/20">
                        <i class="fa-solid fa-laptop-code"></i>
                    </div>
                    <div class="text-3xl lg:text-4xl font-extrabold text-slate-900 count" data-target="12">0</div>
                    <div class="text-sm font-semibold text-emerald-700 mt-1 uppercase tracking-wider">Layanan Digital</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tentang RW 29 -->
    <section id="tentang" class="py-24 bg-slate-50/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Left Image -->
                <div class="lg:col-span-6">
                    <div class="relative">
                        <div class="rounded-3xl overflow-hidden shadow-xl border-4 border-white">
                            <img src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=800&q=80" alt="Tentang RW 29" class="w-full h-[450px] object-cover">
                        </div>
                        <div class="absolute -bottom-6 -right-6 bg-emerald-600 text-white p-6 rounded-2xl shadow-xl hidden sm:block">
                            <span class="block text-3xl font-extrabold">100%</span>
                            <span class="text-xs font-semibold uppercase tracking-wider opacity-90">Transparan & Terpadu</span>
                        </div>
                    </div>
                </div>
                <!-- Right Content -->
                <div class="lg:col-span-6">
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-widest bg-emerald-100 px-3 py-1 rounded-full">PROFIL RW 29</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-4 tracking-tight">Mengenal RW 29</h2>
                    <p class="mt-6 text-slate-600 text-base leading-relaxed">
                        "RW 29 Alamanda Regency Blok I merupakan bagian dari lingkungan masyarakat yang mengutamakan kebersamaan, keamanan, kenyamanan, dan pelayanan warga. Melalui digitalisasi, berbagai informasi dan layanan RW dihadirkan dalam satu platform yang mudah diakses."
                    </p>

                    <!-- Highlights -->
                    <div class="mt-8 space-y-4">
                        <div class="flex gap-4 p-4 rounded-2xl bg-white border border-slate-100 shadow-sm">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-hand-holding-heart"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">Pelayanan</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Pelayanan warga yang mudah dan terorganisir.</p>
                            </div>
                        </div>
                        <div class="flex gap-4 p-4 rounded-2xl bg-white border border-slate-100 shadow-sm">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-scale-balanced"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">Transparansi</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Informasi kegiatan dan keuangan dapat disampaikan secara terbuka.</p>
                            </div>
                        </div>
                        <div class="flex gap-4 p-4 rounded-2xl bg-white border border-slate-100 shadow-sm">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-people-group"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">Kebersamaan</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Membangun komunikasi dan partisipasi warga.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Layanan Digital -->
    <section id="layanan" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-widest bg-emerald-100 px-3 py-1 rounded-full">PORTAL LAYANAN</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-4 tracking-tight">Layanan Warga</h2>
                <p class="mt-4 text-slate-600 text-base">"Berbagai kebutuhan administrasi dan informasi warga dalam satu platform."</p>
            </div>

            <!-- Services Grid -->
            <div class="mt-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card 1 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-emerald-200 hover:shadow-xl transition-all group flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-6">
                            <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xl shadow-md shadow-emerald-600/20 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-file-lines"></i>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-wider">Layanan Online</span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-emerald-600 transition-colors">Surat Pengantar</h3>
                        <p class="mt-2 text-slate-600 text-xs leading-relaxed">Pengajuan surat pengantar RT/RW untuk keperluan administrasi resmi.</p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-200/60">
                        <a href="{{ route('login') }}" class="inline-flex items-center text-xs font-bold text-emerald-600 hover:text-emerald-700">
                            Ajukan Layanan <i class="fa-solid fa-arrow-right ml-1.5"></i>
                        </a>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-emerald-200 hover:shadow-xl transition-all group flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-6">
                            <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xl shadow-md shadow-emerald-600/20 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-house-user"></i>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-wider">Layanan Online</span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-emerald-600 transition-colors">Surat Domisili</h3>
                        <p class="mt-2 text-slate-600 text-xs leading-relaxed">Pembuatan surat keterangan domisili warga dengan verifikasi cepat.</p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-200/60">
                        <a href="{{ route('login') }}" class="inline-flex items-center text-xs font-bold text-emerald-600 hover:text-emerald-700">
                            Ajukan Layanan <i class="fa-solid fa-arrow-right ml-1.5"></i>
                        </a>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-emerald-200 hover:shadow-xl transition-all group flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-6">
                            <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xl shadow-md shadow-emerald-600/20 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-file-shield"></i>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-wider">Layanan Online</span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-emerald-600 transition-colors">Surat Keterangan</h3>
                        <p class="mt-2 text-slate-600 text-xs leading-relaxed">Permohonan surat keterangan untuk berbagai keperluan mendesak.</p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-200/60">
                        <a href="{{ route('login') }}" class="inline-flex items-center text-xs font-bold text-emerald-600 hover:text-emerald-700">
                            Ajukan Layanan <i class="fa-solid fa-arrow-right ml-1.5"></i>
                        </a>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-emerald-200 hover:shadow-xl transition-all group flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-6">
                            <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xl shadow-md shadow-emerald-600/20 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-wider">Layanan Online</span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-emerald-600 transition-colors">Pengaduan Warga</h3>
                        <p class="mt-2 text-slate-600 text-xs leading-relaxed">Sampaikan aspirasi, laporan fasilitas umum, atau pengaduan keamanan.</p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-200/60">
                        <a href="{{ route('login') }}" class="inline-flex items-center text-xs font-bold text-emerald-600 hover:text-emerald-700">
                            Ajukan Layanan <i class="fa-solid fa-arrow-right ml-1.5"></i>
                        </a>
                    </div>
                </div>

                <!-- Card 5 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-emerald-200 hover:shadow-xl transition-all group flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-6">
                            <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xl shadow-md shadow-emerald-600/20 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-folder-open"></i>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-wider">Layanan Online</span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-emerald-600 transition-colors">Permohonan Administrasi</h3>
                        <p class="mt-2 text-slate-600 text-xs leading-relaxed">Layanan pendukung administrasi kependudukan tingkat blok.</p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-200/60">
                        <a href="{{ route('login') }}" class="inline-flex items-center text-xs font-bold text-emerald-600 hover:text-emerald-700">
                            Ajukan Layanan <i class="fa-solid fa-arrow-right ml-1.5"></i>
                        </a>
                    </div>
                </div>

                <!-- Card 6 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-emerald-200 hover:shadow-xl transition-all group flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-6">
                            <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xl shadow-md shadow-emerald-600/20 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-bullhorn"></i>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-wider">Layanan Online</span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-emerald-600 transition-colors">Informasi Kegiatan</h3>
                        <p class="mt-2 text-slate-600 text-xs leading-relaxed">Pusat informasi agenda dan kegiatan warga secara real-time.</p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-200/60">
                        <a href="{{ route('login') }}" class="inline-flex items-center text-xs font-bold text-emerald-600 hover:text-emerald-700">
                            Ajukan Layanan <i class="fa-solid fa-arrow-right ml-1.5"></i>
                        </a>
                    </div>
                </div>

                <!-- Card 7 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-emerald-200 hover:shadow-xl transition-all group flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-6">
                            <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xl shadow-md shadow-emerald-600/20 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-id-card"></i>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-wider">Layanan Online</span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-emerald-600 transition-colors">Data Warga</h3>
                        <p class="mt-2 text-slate-600 text-xs leading-relaxed">Pembaruan mandiri data keluarga dan kependudukan warga.</p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-200/60">
                        <a href="{{ route('login') }}" class="inline-flex items-center text-xs font-bold text-emerald-600 hover:text-emerald-700">
                            Ajukan Layanan <i class="fa-solid fa-arrow-right ml-1.5"></i>
                        </a>
                    </div>
                </div>

                <!-- Card 8 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-emerald-200 hover:shadow-xl transition-all group flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-6">
                            <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xl shadow-md shadow-emerald-600/20 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-sitemap"></i>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-wider">Layanan Online</span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-emerald-600 transition-colors">Informasi RT</h3>
                        <p class="mt-2 text-slate-600 text-xs leading-relaxed">Direktori dan kontak pengurus RT 01 hingga RT 08 di lingkungan RW 29.</p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-200/60">
                        <a href="{{ route('login') }}" class="inline-flex items-center text-xs font-bold text-emerald-600 hover:text-emerald-700">
                            Ajukan Layanan <i class="fa-solid fa-arrow-right ml-1.5"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Alur Pelayanan -->
    <section id="alur" class="py-24 bg-slate-50/50 border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-widest bg-emerald-100 px-3 py-1 rounded-full">PROSES MUDAH</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-4 tracking-tight">Alur Pelayanan Digital</h2>
                <p class="mt-4 text-slate-600 text-base">"Empat langkah praktis mendapatkan layanan administrasi di RW 29."</p>
            </div>

            <div class="mt-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm relative">
                    <div class="text-4xl font-extrabold text-emerald-600/20 mb-4">01</div>
                    <h3 class="text-lg font-bold text-slate-900">Ajukan</h3>
                    <p class="mt-2 text-slate-600 text-xs leading-relaxed">Warga memilih layanan yang dibutuhkan melalui portal digital.</p>
                </div>
                <!-- Step 2 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm relative">
                    <div class="text-4xl font-extrabold text-emerald-600/20 mb-4">02</div>
                    <h3 class="text-lg font-bold text-slate-900">Verifikasi</h3>
                    <p class="mt-2 text-slate-600 text-xs leading-relaxed">Pengurus melakukan pemeriksaan data dan kelengkapan berkas.</p>
                </div>
                <!-- Step 3 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm relative">
                    <div class="text-4xl font-extrabold text-emerald-600/20 mb-4">03</div>
                    <h3 class="text-lg font-bold text-slate-900">Proses</h3>
                    <p class="mt-2 text-slate-600 text-xs leading-relaxed">Permohonan diproses dan ditandatangani secara digital oleh pengurus.</p>
                </div>
                <!-- Step 4 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm relative">
                    <div class="text-4xl font-extrabold text-emerald-600/20 mb-4">04</div>
                    <h3 class="text-lg font-bold text-slate-900">Selesai</h3>
                    <p class="mt-2 text-slate-600 text-xs leading-relaxed">Warga mendapatkan informasi hasil layanan dan dapat mengunduh dokumen.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pengumuman -->
    <section id="pengumuman" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-16">
                <div>
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-widest bg-emerald-100 px-3 py-1 rounded-full">INFORMASI TERBARU</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-4 tracking-tight">Pengumuman Terbaru</h2>
                </div>
                <a href="{{ route('login') }}" class="mt-4 md:mt-0 text-sm font-bold text-emerald-600 hover:text-emerald-700 inline-flex items-center">
                    Lihat Semua Pengumuman <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($announcements as $announcement)
                    <div class="bg-slate-50 rounded-2xl overflow-hidden border border-slate-100 hover:shadow-xl transition-all flex flex-col justify-between">
                        <div class="p-6">
                            <div class="flex items-center justify-between text-xs text-slate-500 mb-4">
                                <span class="font-semibold text-emerald-700 bg-emerald-100 px-2.5 py-1 rounded-md">Pengumuman</span>
                                <span>{{ \Carbon\Carbon::parse($announcement->published_at ?? $announcement->created_at)->translatedFormat('d M Y') }}</span>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 leading-snug">{{ $announcement->title }}</h3>
                            <p class="mt-3 text-slate-600 text-xs leading-relaxed">"{{ \Illuminate\Support\Str::limit(strip_tags($announcement->body), 100) }}"</p>
                        </div>
                        <div class="px-6 pb-6 pt-2">
                            @auth
                                <a href="{{ route('announcements.show', $announcement->id) }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 inline-flex items-center">
                                    Baca Selengkapnya <i class="fa-solid fa-arrow-right ml-1.5"></i>
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 inline-flex items-center">
                                    Baca Selengkapnya <i class="fa-solid fa-arrow-right ml-1.5"></i>
                                </a>
                            @endauth
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 py-12 text-center text-slate-400 text-sm">
                        Belum ada pengumuman terbaru.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Agenda Kegiatan -->
    <section id="kegiatan" class="py-24 bg-slate-50/50 border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-widest bg-emerald-100 px-3 py-1 rounded-full">AGENDA WARGA</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-4 tracking-tight">Agenda RW 29</h2>
                <p class="mt-4 text-slate-600 text-base">"Jadwal kegiatan rutin dan acara khusus di lingkungan Alamanda Regency Blok I."</p>
            </div>

            <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($agendas as $agenda)
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-start gap-5">
                        <div class="bg-emerald-600 text-white rounded-2xl p-4 text-center shrink-0 w-20 shadow-md shadow-emerald-600/20">
                            <span class="block text-2xl font-extrabold leading-tight">{{ \Carbon\Carbon::parse($agenda->starts_at)->format('d') }}</span>
                            <span class="block text-[11px] uppercase tracking-wider font-semibold opacity-95">{{ \Carbon\Carbon::parse($agenda->starts_at)->translatedFormat('F') }}</span>
                            <span class="block text-[10px] opacity-80">{{ \Carbon\Carbon::parse($agenda->starts_at)->format('Y') }}</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">{{ $agenda->title }}</h3>
                            <p class="text-xs font-semibold text-emerald-700 mt-1"><i class="fa-regular fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($agenda->starts_at)->format('H.i') }} WIB - Selesai</p>
                            <p class="text-xs text-slate-500 mt-2"><i class="fa-solid fa-location-dot mr-1"></i> {{ $agenda->location ?? 'Lingkungan RW 29' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 py-12 text-center text-slate-400 text-sm">
                        Belum ada agenda warga terbaru.
                    </div>
                @endforelse
            </div>

            <div class="mt-12 text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center px-8 py-3.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold shadow-lg transition-all">
                    Lihat Semua Agenda <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Transparansi Keuangan -->
    <section id="transparansi" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-widest bg-emerald-100 px-3 py-1 rounded-full">AKUNTABILITAS PUBLIK</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-4 tracking-tight">Transparansi Kas RW</h2>
                <p class="mt-4 text-slate-600 text-base">"Laporan keuangan terbuka untuk seluruh warga RW 29 Alamanda Regency Blok I."</p>
            </div>

            <!-- Stats Cards -->
            <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100">
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Saldo Kas</div>
                    <div class="text-3xl font-extrabold text-emerald-600 mt-2">Rp {{ number_format($totalBalance, 0, ',', '.') }}</div>
                    <div class="text-xs text-emerald-700 mt-1 font-medium"><i class="fa-solid fa-circle-check mr-1"></i> Data real-time database</div>
                </div>
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100">
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pemasukan Bulan Ini</div>
                    <div class="text-3xl font-extrabold text-slate-900 mt-2">Rp {{ number_format($currentMonthIncome, 0, ',', '.') }}</div>
                    <div class="text-xs text-emerald-700 mt-1 font-medium"><i class="fa-solid fa-arrow-trend-up mr-1"></i> Total Pemasukan Bulan Ini</div>
                </div>
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100">
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pengeluaran Bulan Ini</div>
                    <div class="text-3xl font-extrabold text-amber-600 mt-2">Rp {{ number_format($currentMonthExpense, 0, ',', '.') }}</div>
                    <div class="text-xs text-slate-500 mt-1 font-medium"><i class="fa-solid fa-arrow-trend-down mr-1"></i> Total Pengeluaran Bulan Ini</div>
                </div>
            </div>

            <!-- Table -->
            <div class="mt-12 bg-slate-50 rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-slate-200/60 font-bold text-slate-900 text-sm">
                    Riwayat Transaksi Terakhir
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100/70 text-slate-700 uppercase font-semibold">
                            <tr>
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4">Keterangan</th>
                                <th class="px-6 py-4 text-right">Pemasukan</th>
                                <th class="px-6 py-4 text-right">Pengeluaran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/60 text-slate-600">
                            @forelse($transactions as $trx)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-slate-900">{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d M Y') }}</td>
                                    <td class="px-6 py-4">{{ $trx->description }}</td>
                                    @if($trx->transaction_type === 'income')
                                        <td class="px-6 py-4 text-right font-semibold text-emerald-600">+Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 text-right">-</td>
                                    @else
                                        <td class="px-6 py-4 text-right">-</td>
                                        <td class="px-6 py-4 text-right font-semibold text-amber-600">-Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-6 text-center text-slate-500">Belum ada data transaksi yang disetujui.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-10 text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center px-8 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold shadow-lg shadow-emerald-600/20 transition-all">
                    Lihat Laporan Lengkap <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Struktur Pengurus -->
    <section class="py-24 bg-slate-50/50 border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-widest bg-emerald-100 px-3 py-1 rounded-full">PENGURUS RW</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-4 tracking-tight">Pengurus RW 29</h2>
                <p class="mt-4 text-slate-600 text-base">"Struktur kepengurusan RW 29 Alamanda Regency Blok I periode 2025–2028."</p>
            </div>

            <div class="mt-16 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Profile 1 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm text-center hover:shadow-xl transition-all">
                    <div class="w-24 h-24 mx-auto rounded-full overflow-hidden shadow-md mb-4 border-2 border-emerald-600">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=300&q=80" alt="Ketua RW" class="w-full h-full object-cover">
                    </div>
                    <div class="text-xs font-bold text-emerald-600 uppercase tracking-widest">Ketua RW</div>
                    <h3 class="text-lg font-bold text-slate-900 mt-1">Bapak H. Ahmad Subardjo</h3>
                    <p class="text-xs text-slate-500 mt-1">Alamanda Regency Blok I/5</p>
                </div>

                <!-- Profile 2 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm text-center hover:shadow-xl transition-all">
                    <div class="w-24 h-24 mx-auto rounded-full overflow-hidden shadow-md mb-4 border-2 border-emerald-600">
                        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=300&q=80" alt="Sekretaris" class="w-full h-full object-cover">
                    </div>
                    <div class="text-xs font-bold text-emerald-600 uppercase tracking-widest">Sekretaris</div>
                    <h3 class="text-lg font-bold text-slate-900 mt-1">Bapak Budi Santoso</h3>
                    <p class="text-xs text-slate-500 mt-1">Alamanda Regency Blok I/12</p>
                </div>

                <!-- Profile 3 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm text-center hover:shadow-xl transition-all">
                    <div class="w-24 h-24 mx-auto rounded-full overflow-hidden shadow-md mb-4 border-2 border-emerald-600">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=300&q=80" alt="Bendahara" class="w-full h-full object-cover">
                    </div>
                    <div class="text-xs font-bold text-emerald-600 uppercase tracking-widest">Bendahara</div>
                    <h3 class="text-lg font-bold text-slate-900 mt-1">Bapak Joko Widodo</h3>
                    <p class="text-xs text-slate-500 mt-1">Alamanda Regency Blok I/20</p>
                </div>

                <!-- Profile 4 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm text-center hover:shadow-xl transition-all">
                    <div class="w-24 h-24 mx-auto rounded-full overflow-hidden shadow-md mb-4 border-2 border-emerald-600">
                        <img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=300&q=80" alt="Koordinator" class="w-full h-full object-cover">
                    </div>
                    <div class="text-xs font-bold text-emerald-600 uppercase tracking-widest">Koordinator Keamanan</div>
                    <h3 class="text-lg font-bold text-slate-900 mt-1">Bapak Hendra Gunawan</h3>
                    <p class="text-xs text-slate-500 mt-1">Alamanda Regency Blok I/8</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Galeri -->
    <section id="galeri" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-widest bg-emerald-100 px-3 py-1 rounded-full">DOKUMENTASI</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-4 tracking-tight">Dokumentasi Kegiatan</h2>
                <p class="mt-4 text-slate-600 text-base">"Momen kebersamaan dan aktivitas warga RW 29 Alamanda Regency Blok I."</p>
            </div>

            <div class="mt-16 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Gallery Item 1 -->
                <div class="group relative rounded-2xl overflow-hidden shadow-md h-72">
                    <img src="https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&w=600&q=80" alt="Kerja Bakti" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                        <div>
                            <span class="text-xs font-bold text-emerald-400 uppercase">Kerja Bakti</span>
                            <h4 class="text-white font-bold text-base mt-1">Kerja Bakti Pembersihan Lingkungan</h4>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 2 -->
                <div class="group relative rounded-2xl overflow-hidden shadow-md h-72">
                    <img src="https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=600&q=80" alt="Rapat Warga" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                        <div>
                            <span class="text-xs font-bold text-emerald-400 uppercase">Rapat Warga</span>
                            <h4 class="text-white font-bold text-base mt-1">Musyawarah Warga RW 29</h4>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 3 -->
                <div class="group relative rounded-2xl overflow-hidden shadow-md h-72">
                    <img src="https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&w=600&q=80" alt="Kegiatan Sosial" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                        <div>
                            <span class="text-xs font-bold text-emerald-400 uppercase">Kegiatan Sosial</span>
                            <h4 class="text-white font-bold text-base mt-1">Santunan dan Kegiatan Sosial Warga</h4>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 4 -->
                <div class="group relative rounded-2xl overflow-hidden shadow-md h-72">
                    <img src="https://images.unsplash.com/photo-1577495508048-b635879837f1?auto=format&fit=crop&w=600&q=80" alt="Kegiatan Lingkungan" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                        <div>
                            <span class="text-xs font-bold text-emerald-400 uppercase">Kegiatan Lingkungan</span>
                            <h4 class="text-white font-bold text-base mt-1">Penghijauan Taman Blok I</h4>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 5 -->
                <div class="group relative rounded-2xl overflow-hidden shadow-md h-72">
                    <img src="https://images.unsplash.com/photo-1511632765486-a01980e01a18?auto=format&fit=crop&w=600&q=80" alt="Acara Warga" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                        <div>
                            <span class="text-xs font-bold text-emerald-400 uppercase">Acara Warga</span>
                            <h4 class="text-white font-bold text-base mt-1">Perayaan HUT RI Warga RW 29</h4>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 6 -->
                <div class="group relative rounded-2xl overflow-hidden shadow-md h-72">
                    <img src="https://images.unsplash.com/photo-1543269865-cbf427effbad?auto=format&fit=crop&w=600&q=80" alt="Posyandu" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                        <div>
                            <span class="text-xs font-bold text-emerald-400 uppercase">Kesehatan</span>
                            <h4 class="text-white font-bold text-base mt-1">Kegiatan Posyandu Balita & Lansia</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center px-8 py-3.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold shadow-lg transition-all">
                    Lihat Semua Galeri <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Call To Action -->
    <section class="py-20 bg-emerald-600 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.15),transparent_50%)]"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center max-w-3xl">
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Mari Bersama Membangun RW 29 yang Lebih Digital</h2>
            <p class="mt-4 text-emerald-100 text-base sm:text-lg">"Informasi lebih mudah, pelayanan lebih cepat, dan komunikasi warga semakin terhubung."</p>
            <div class="mt-8 flex justify-center">
                <a href="{{ route('login') }}" class="px-8 py-4 rounded-xl bg-white text-emerald-700 hover:bg-slate-100 font-bold text-sm shadow-xl transition-all hover:-translate-y-0.5">
                    Mulai Gunakan Layanan <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Kontak -->
    <section id="kontak" class="py-24 bg-slate-50/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-widest bg-emerald-100 px-3 py-1 rounded-full">HUBUNGI KAMI</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-4 tracking-tight">Hubungi RW 29</h2>
                <p class="mt-4 text-slate-600 text-base">"Saluran komunikasi resmi pengurus RW 29 Alamanda Regency Blok I."</p>
            </div>

            <div class="mt-16 grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
                <!-- Info -->
                <div class="lg:col-span-5 bg-white p-8 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 mb-6">Sekretariat RW 29</h3>
                        <div class="space-y-6">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 text-sm">Alamat</h4>
                                    <p class="text-xs text-slate-600 mt-1">Alamanda Regency Blok I – RW 29, Bekasi, Jawa Barat</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 text-sm">WhatsApp RW</h4>
                                    <p class="text-xs text-slate-600 mt-1">+62 812-3456-7890</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                    <i class="fa-regular fa-envelope"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 text-sm">Email Resmi</h4>
                                    <p class="text-xs text-slate-600 mt-1">sekretariat@rw29alamandaregency.com</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 pt-6 border-t border-slate-100">
                        <a href="https://wa.me/6281234567890" target="_blank" class="w-full py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs text-center flex items-center justify-center gap-2 shadow-md shadow-emerald-600/20">
                            <i class="fa-brands fa-whatsapp text-base"></i> Hubungi via WhatsApp
                        </a>
                    </div>
                </div>

                <!-- Mini Map Placeholder -->
                <div class="lg:col-span-7 bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col">
                    <div class="w-full h-full min-h-[350px] rounded-xl bg-slate-100 overflow-hidden relative flex items-center justify-center border border-slate-200">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.195191834907!2d107.048348!3d-6.226418!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwMTMnMzUuMSJTIDEwN8KwMDInNTQuMSJF!5e0!3m2!1sid!2sid!4v1650000000000!5m2!1sid!2sid" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="absolute inset-0 w-full h-full"></iframe>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 py-16 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-12 pb-12 border-b border-slate-800">
                <!-- Col 1 -->
                <div class="md:col-span-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-extrabold text-base">
                            29
                        </div>
                        <div>
                            <span class="block font-extrabold text-white text-lg leading-tight">RW 29</span>
                            <span class="block text-xs font-semibold text-emerald-400 tracking-wider uppercase">Alamanda Regency Blok I</span>
                        </div>
                    </div>
                    <p class="text-slate-400 text-xs leading-relaxed max-w-sm">
                        "Digitalisasi untuk pelayanan warga yang lebih mudah, transparan, dan terhubung."
                    </p>
                    <div class="flex items-center gap-3 pt-2">
                        <a href="#" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-emerald-600 text-slate-300 hover:text-white flex items-center justify-center transition-colors">
                            <i class="fa-brands fa-whatsapp text-sm"></i>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-emerald-600 text-slate-300 hover:text-white flex items-center justify-center transition-colors">
                            <i class="fa-brands fa-instagram text-sm"></i>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-emerald-600 text-slate-300 hover:text-white flex items-center justify-center transition-colors">
                            <i class="fa-brands fa-facebook-f text-sm"></i>
                        </a>
                    </div>
                </div>

                <!-- Col 2 -->
                <div class="md:col-span-6 grid grid-cols-2 gap-8">
                    <div>
                        <h4 class="font-bold text-white text-sm mb-4">Navigasi</h4>
                        <ul class="space-y-2.5 text-xs">
                            <li><a href="#beranda" class="hover:text-emerald-400 transition-colors">Beranda</a></li>
                            <li><a href="#layanan" class="hover:text-emerald-400 transition-colors">Layanan</a></li>
                            <li><a href="#pengumuman" class="hover:text-emerald-400 transition-colors">Pengumuman</a></li>
                            <li><a href="#kegiatan" class="hover:text-emerald-400 transition-colors">Kegiatan</a></li>
                            <li><a href="#kontak" class="hover:text-emerald-400 transition-colors">Kontak</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-white text-sm mb-4">Layanan Warga</h4>
                        <ul class="space-y-2.5 text-xs">
                            <li><a href="{{ route('login') }}" class="hover:text-emerald-400 transition-colors">Surat Pengantar</a></li>
                            <li><a href="{{ route('login') }}" class="hover:text-emerald-400 transition-colors">Surat Domisili</a></li>
                            <li><a href="{{ route('login') }}" class="hover:text-emerald-400 transition-colors">Pengaduan Warga</a></li>
                            <li><a href="{{ route('login') }}" class="hover:text-emerald-400 transition-colors">Kas RW & Keuangan</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="pt-8 flex flex-col sm:flex-row justify-between items-center text-xs text-slate-500">
                <p>&copy; 2026 RW 29 Alamanda Regency Blok I. All Rights Reserved.</p>
                <p class="mt-2 sm:mt-0">Portal Digital Warga RW 29</p>
            </div>
        </div>
    </footer>

    <!-- Script for Navbar scroll & Counter & Mobile Menu -->
    <script>
        // Navbar scroll effect
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                navbar.classList.add('shadow-md', 'py-1');
                navbar.classList.remove('py-3');
            } else {
                navbar.classList.remove('shadow-md', 'py-1');
            }
        });

        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Close mobile menu on click link
        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
            });
        });

        // Animated counter
        const counters = document.querySelectorAll('.count');
        let animated = false;

        function runCounters() {
            counters.forEach(counter => {
                const target = +counter.getAttribute('data-target');
                let count = 0;
                const speed = target / 50;
                const updateCount = () => {
                    count += speed;
                    if (count < target) {
                        counter.innerText = Math.ceil(count);
                        setTimeout(updateCount, 30);
                    } else {
                        counter.innerText = target;
                    }
                };
                updateCount();
            });
        }

        window.addEventListener('scroll', () => {
            const statsSection = document.querySelector('.count').getBoundingClientRect();
            if (statsSection.top < window.innerHeight && !animated) {
                runCounters();
                animated = true;
            }
        });
    </script>
</body>
</html>
