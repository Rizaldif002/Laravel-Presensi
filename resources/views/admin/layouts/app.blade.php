<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name', 'Laravel Presensi') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        #map { height: 400px; border-radius: 15px; z-index: 10; }
        .radar-pulse {
            border-radius: 50%;
            background: rgba(37, 99, 235, 0.2);
            border: 2px solid rgba(37, 99, 235, 0.5);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%   { transform: scale(0.1); opacity: 1; }
            100% { transform: scale(1.2); opacity: 0; }
        }
        svg, aside svg { width: 20px; height: 20px; }
        [x-cloak] { display: none !important; }
        .sidebar-brand-active { background-color: #2563eb; font-weight: 700; }
        .sidebar-brand { background-color: transparent !important; box-shadow: none !important; }
        #main-sidebar .sidebar-scroll {
            overflow-y: hidden !important;
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
        }
        #main-sidebar .sidebar-scroll::-webkit-scrollbar { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-100">

@php $user = auth()->user(); @endphp

<!-- Sidebar -->
<aside id="main-sidebar" class="fixed top-0 left-0 z-50 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0 bg-blue-900 shadow-xl flex flex-col">

    {{-- Brand --}}
    <div class="h-16 flex items-center px-6 border-b border-blue-800/50 shrink-0">
        <a href="{{ route('dashboard') }}"
           class="sidebar-brand flex items-center w-full h-full transition select-none cursor-pointer rounded-lg px-4
            {{ request()->routeIs('dashboard') ? 'sidebar-brand-active text-white' : 'text-white hover:bg-blue-800' }}"
           style="{{ request()->routeIs('dashboard') ? 'box-shadow:none;background-color:#2563eb;' : '' }}">
            <span class="font-extrabold text-sm tracking-widest uppercase">Presensi FT Unmul</span>
        </a>
    </div>

    <div class="flex-1 px-3 py-6 sidebar-scroll overflow-y-auto">

        {{-- Menu Bersama: Dashboard --}}
        <div class="mb-6">
            <ul class="flex flex-col gap-1">
                <li>
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg text-white transition hover:bg-blue-800
                          {{ request()->routeIs('dashboard') ? 'bg-blue-600 shadow-lg font-bold' : '' }}">
                        <svg class="flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span class="text-sm font-medium">Halaman Utama</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- ======================== --}}
        {{-- MENU ADMIN               --}}
        {{-- ======================== --}}
        @if($user->isAdmin())

            {{-- Master Data --}}
            <div class="mb-6">
                <span class="text-blue-300 text-[10px] uppercase px-4 mb-2 font-semibold tracking-widest block opacity-70">
                    Master Data
                </span>
                <ul class="flex flex-col gap-1">
                    <li>
                        <a href="{{ route('admin.ruangan') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-white transition hover:bg-blue-800
                            {{ request()->routeIs('admin.ruangan') ? 'bg-blue-600 shadow-lg' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="3" width="7" height="7" rx="1"/>
                                <rect x="14" y="3" width="7" height="7" rx="1"/>
                                <rect x="14" y="14" width="7" height="7" rx="1"/>
                                <rect x="3" y="14" width="7" height="7" rx="1"/>
                            </svg>
                            <span class="text-sm font-medium">Data Ruangan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.mata-kuliah') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-white transition hover:bg-blue-800
                            {{ request()->routeIs('admin.mata-kuliah') ? 'bg-blue-600 shadow-lg' : '' }}">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6.5a2.5 2.5 0 005 0M12 14a2.5 2.5 0 01-5 0V9"/>
                            </svg>
                            <span class="text-sm font-medium">Mata Kuliah</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.tahun-ajaran') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-white transition hover:bg-blue-800
                            {{ request()->routeIs('admin.tahun-ajaran') ? 'bg-blue-600 shadow-lg' : '' }}">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <rect x="3" y="8" width="18" height="13" rx="2"/>
                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/>
                            </svg>
                            <span class="text-sm font-medium">Tahun Ajaran</span>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Manajemen User --}}
            <div class="mb-6">
                <span class="text-blue-300 text-[10px] uppercase px-4 mb-2 font-semibold tracking-widest block opacity-70">
                    Manajemen User
                </span>
                <ul class="flex flex-col gap-1">
                    <li>
                        <a href="{{ route('admin.dosen') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-white transition hover:bg-blue-800
                            {{ request()->routeIs('admin.dosen') ? 'bg-blue-600 shadow-lg' : '' }}">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                      d="M17 20h5v-2a4 4 0 00-3-3.87M9 4a4 4 0 014 4m6 4V5a4 4 0 00-4-4H7a4 4 0 00-4 4v8a4 4 0 004 4h4M6 20v-2a4 4 0 014-4h1.5"/>
                            </svg>
                            <span class="text-sm font-medium">Data Dosen</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.mahasiswa') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-white transition hover:bg-blue-800
                            {{ request()->routeIs('admin.mahasiswa') ? 'bg-blue-600 shadow-lg' : '' }}">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                      d="M17 20h5v-2a4 4 0 00-3-3.87M9 20v-2a4 4 0 014-4h1.5m-9 0A4 4 0 017 16v2m5-10a4 4 0 00-8 0v2a4 4 0 004 4h4"/>
                            </svg>
                            <span class="text-sm font-medium">Data Mahasiswa</span>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Akademik & Laporan --}}
            <div class="mb-6">
                <span class="text-blue-300 text-[10px] uppercase px-4 mb-2 font-semibold tracking-widest block opacity-70">
                    Akademik & Laporan
                </span>
                <ul class="flex flex-col gap-1">
                    <li>
                        <a href="{{ route('admin.kelas.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-white transition hover:bg-blue-800
                                {{ request()->routeIs('admin.kelas.*') ? 'bg-blue-600 shadow-lg' : '' }}">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <span class="text-sm font-medium">Data Kelas</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.jadwal.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-white transition hover:bg-blue-800
                                {{ request()->routeIs('admin.jadwal.*') ? 'bg-blue-600 shadow-lg' : '' }}">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-sm font-medium">Jadwal Perkuliahan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.laporan.presensi') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-white transition hover:bg-blue-800
                              {{ request()->routeIs('admin.laporan.presensi') ? 'bg-blue-600 shadow-lg' : '' }}">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-6 0h8m-7 4h8a2 2 0 002-2V5a2 2 0 00-2-2h-8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-sm font-medium">Laporan Presensi</span>
                        </a>
                    </li>
                </ul>
            </div>

        @endif
        {{-- END MENU ADMIN --}}

        {{-- ======================== --}}
        {{-- MENU DOSEN               --}}
        {{-- ======================== --}}
        @if($user->isDosen())

            {{-- Perkuliahan --}}
            <div class="mb-6">
                <span class="text-blue-300 text-[10px] uppercase px-4 mb-2 font-semibold tracking-widest block opacity-70">
                    Perkuliahan
                </span>
                <ul class="flex flex-col gap-1">
                    <li>
                        <a href="{{ route('dosen.sesi.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-white transition hover:bg-blue-800
                            {{ request()->routeIs('dosen.sesi.*') ? 'bg-blue-600 shadow-lg' : '' }}">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            <span class="text-sm font-medium">Sesi Presensi</span>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Laporan Dosen --}}
            <div class="mb-6">
                <span class="text-blue-300 text-[10px] uppercase px-4 mb-2 font-semibold tracking-widest block opacity-70">
                    Laporan
                </span>
                <ul class="flex flex-col gap-1">
                    <li>
                        <a href="#"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-white transition hover:bg-blue-800 opacity-60 cursor-not-allowed">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 8v4l3 3m6-3A9 9 0 11.015 12.005 9 9 0 0112 21z"/>
                            </svg>
                            <span class="text-sm font-medium">Rekap Kehadiran</span>
                            <span class="ml-auto text-[9px] bg-blue-700 px-1.5 py-0.5 rounded text-blue-200">Soon</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-white transition hover:bg-blue-800 opacity-60 cursor-not-allowed">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-6 0h8m-7 4h8a2 2 0 002-2V5a2 2 0 00-2-2h-8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-sm font-medium">Laporan Kelas</span>
                            <span class="ml-auto text-[9px] bg-blue-700 px-1.5 py-0.5 rounded text-blue-200">Soon</span>
                        </a>
                    </li>
                </ul>
            </div>

        @endif
        {{-- END MENU DOSEN --}}

    </div>
</aside>
<!-- End Sidebar -->

<!-- Main Content Wrapper -->
<div class="sm:ml-64 transition-all duration-300">

    <!-- Top Navbar -->
    <nav class="sticky top-0 z-40 w-full bg-white border-b border-gray-200 shadow-sm h-16 flex items-center justify-between px-4 lg:px-8">
        <button id="sidebarToggle" class="p-2 rounded-md text-gray-600 hover:bg-gray-100 sm:hidden">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
            </svg>
        </button>

        <div class="hidden md:block">
            <h1 class="text-lg font-semibold text-gray-700">
                @yield('title', 'Presensi Teknik Elektro')
            </h1>
        </div>

        {{-- Flash error dari middleware --}}
        @if(session('error'))
            <div class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Profile Dropdown -->
        <div class="relative">
            <button id="profileDropdownButton" class="flex items-center gap-3 focus:outline-none group" type="button">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-gray-800 group-hover:text-blue-600 transition">
                        {{ $user->name }}
                    </p>
                    <p class="text-[10px] text-gray-400 uppercase tracking-tighter">
                        @if($user->isAdmin()) Administrator
                        @elseif($user->isDosen()) Dosen
                        @else {{ $user->role }}
                        @endif
                    </p>
                </div>
                <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 transition overflow-hidden
                    {{ $user->isAdmin() ? 'bg-blue-50 border-blue-100 text-blue-600' : 'bg-emerald-50 border-emerald-100 text-emerald-600' }}">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </div>
            </button>

            <div id="profileDropdownMenu"
                 class="absolute right-0 mt-2 w-52 bg-white border border-gray-200 rounded-xl shadow-xl py-2 z-50 transition transform opacity-0 scale-95 pointer-events-none">

                {{-- Info role --}}
                <div class="px-4 py-2 border-b border-gray-100">
                    <p class="text-xs font-bold text-gray-700 truncate">{{ $user->name }}</p>
                    <p class="text-[10px] text-gray-400">
                        @if($user->isAdmin()) Administrator
                        @elseif($user->isDosen()) Dosen Pengampu
                        @else {{ $user->role }}
                        @endif
                    </p>
                </div>

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Edit Profil
                </a>

                <hr class="my-1 border-gray-100">

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </nav>
    <!-- End Navbar -->

    <!-- Main Content -->
    <main class="p-4 md:p-8">
        @if(isset($header))
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800">{{ $header }}</h2>
            </div>
        @endif

        {{-- Flash messages global --}}
        @if(session('success'))
            <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('info'))
            <div class="mb-4 p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl text-sm font-medium">
                {{ session('info') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm font-medium md:hidden">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm min-h-[calc(100vh-12rem)] p-8">
            {{ $slot }}
        </div>

        <footer class="py-8 text-center text-gray-400 text-[10px] tracking-widest uppercase">
            &copy; 2026 Teknik Elektro - Universitas Mulawarman
        </footer>
    </main>
</div>

<!-- Mobile Sidebar Overlay -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden sm:hidden"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar   = document.getElementById('main-sidebar');
        const overlay   = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggle');

        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
        if (toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
        if (overlay)  overlay.addEventListener('click', toggleSidebar);

        const profileBtn  = document.getElementById('profileDropdownButton');
        const profileMenu = document.getElementById('profileDropdownMenu');

        function closeProfileMenu() {
            profileMenu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
            profileMenu.classList.remove('opacity-100', 'scale-100');
        }
        function toggleProfileMenu(e) {
            e.stopPropagation();
            if (profileMenu.classList.contains('opacity-0')) {
                profileMenu.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
                profileMenu.classList.add('opacity-100', 'scale-100');
            } else {
                closeProfileMenu();
            }
        }
        if (profileBtn && profileMenu) {
            closeProfileMenu();
            profileBtn.addEventListener('click', toggleProfileMenu);
            document.addEventListener('mousedown', function (e) {
                if (!profileMenu.contains(e.target) && !profileBtn.contains(e.target)) {
                    closeProfileMenu();
                }
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeProfileMenu();
            });
        }
    });
</script>
</body>
</html>
