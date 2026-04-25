<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name', 'Laravel Presensi') }}</title>

    <!-- Fonts & UI CSS -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom Styles -->
    <style>
        /* Map & Pulse Effect */
        #map {
            height: 400px;
            border-radius: 15px;
            z-index: 10;
        }
        .radar-pulse {
            border-radius: 50%;
            background: rgba(37, 99, 235, 0.2);
            border: 2px solid rgba(37, 99, 235, 0.5);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(0.1); opacity: 1; }
            100% { transform: scale(1.2); opacity: 0; }
        }

        /* UI: Sidebar, SVG, Dropdown, Scrollbars */
        svg,
        aside svg {
            width: 20px;
            height: 20px;
        }
        [x-cloak] { display: none !important; }
        .sidebar-brand-active {
            background-color: #2563eb; /* bg-blue-600 */
            font-weight: 700;
        }
        .profile-menu-dropdown[x-cloak] { display: none !important; }
        .sidebar-brand {
            background-color: transparent !important;
            box-shadow: none !important;
        }
        #main-sidebar .sidebar-scroll {
            overflow-y: hidden !important;
            scrollbar-width: none !important;  /* Firefox */
            -ms-overflow-style: none !important; /* IE and Edge */
        }
        #main-sidebar .sidebar-scroll::-webkit-scrollbar {
            display: none !important; /* Chrome, Safari, Opera */
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-100">

    <!-- Sidebar -->
    <aside id="main-sidebar" class="fixed top-0 left-0 z-50 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0 bg-blue-900 shadow-xl flex flex-col">
        <div class="h-16 flex items-center px-6 border-b border-blue-800/50 shrink-0">
            <a href="{{ route('dashboard') }}"
               class="sidebar-brand flex items-center w-full h-full transition select-none cursor-pointer rounded-lg px-4
                {{ request()->routeIs('dashboard') ? 'sidebar-brand-active text-white' : 'text-white hover:bg-blue-800' }}"
               style="{{ request()->routeIs('dashboard') ? 'box-shadow:none;background-color:#2563eb;' : '' }}"
            >
                <span class="font-extrabold text-sm tracking-widest uppercase">
                    Presensi FT Unmul
                </span>
            </a>
        </div>
        <div class="flex-1 px-3 py-6 sidebar-scroll">
            <!-- Main Navigation -->
            <div class="mb-6">
                <ul class="flex flex-col gap-1">
                    <li>
                        <a href="{{ route('dashboard') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-white transition hover:bg-blue-800 group
                              {{ request()->routeIs('dashboard') ? 'bg-blue-600 shadow-lg font-bold' : '' }}">
                            <svg class="flex-shrink-0 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span class="text-sm font-medium">Halaman Utama</span>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- Master Data -->
            <div class="mb-6">
                <span class="text-blue-300 text-[10px] uppercase px-4 mb-2 font-semibold tracking-widest block opacity-70">
                    Master Data
                </span>
                <ul class="flex flex-col gap-1">
                    <li>
                        <a href="{{ route('admin.ruangan') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-white transition hover:bg-blue-800 group
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
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-white transition hover:bg-blue-800 group
                            {{ request()->routeIs('admin.mata-kuliah') ? 'bg-blue-600 shadow-lg' : '' }}">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6.5a2.5 2.5 0 005 0M12 14a2.5 2.5 0 01-5 0V9"/>
                            </svg>
                            <span class="text-sm font-medium">Mata Kuliah</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.tahun-ajaran') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-white transition hover:bg-blue-800 group
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
            <!-- Manajemen User -->
            <div class="mb-6">
                <span class="text-blue-300 text-[10px] uppercase px-4 mb-2 font-semibold tracking-widest block opacity-70">
                    Manajemen User
                </span>
                <ul class="flex flex-col gap-1">
                    <li>
                        <a href="{{ route('admin.dosen') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-white transition hover:bg-blue-800 group
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
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-white transition hover:bg-blue-800 group
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
            <!-- Akademik & Laporan -->
            <div class="mb-6">
                <span class="text-blue-300 text-[10px] uppercase px-4 mb-2 font-semibold tracking-widest block opacity-70">
                    Akademik & Laporan
                </span>
                <ul class="flex flex-col gap-1">
                    <li>
                        <a href="{{ route('admin.kelas.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-white transition hover:bg-blue-800 group
                                {{ request()->routeIs('admin.kelas.*') ? 'bg-blue-600 shadow-lg' : '' }}">
                            <svg class="flex-shrink-0 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <span class="text-sm font-medium">Data Kelas</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.jadwal.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-white transition hover:bg-blue-800 group
                                {{ request()->routeIs('admin.jadwal.*') ? 'bg-blue-600 shadow-lg' : '' }}">
                            <svg class="flex-shrink-0 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-sm font-medium">Kelas Perkuliahan</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white transition hover:bg-blue-800 group">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 8v4l3 3m6-3A9 9 0 11.015 12.005 9 9 0 0112 21z"/>
                            </svg>
                            <span class="text-sm font-medium">Riwayat Presensi</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.laporan.presensi') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg text-white transition hover:bg-blue-800 group
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
            <!-- Profile Dropdown -->
            <div class="relative">
                <button id="profileDropdownButton"
                        class="flex items-center gap-3 focus:outline-none group"
                        type="button" aria-haspopup="true" aria-expanded="false">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-gray-800 group-hover:text-blue-600 transition">
                            {{ Auth::user()->name ?? 'Admin' }}
                        </p>
                        <p class="text-[10px] text-gray-400 uppercase tracking-tighter">Administrator</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-50 border-2 border-blue-100 flex items-center justify-center text-blue-600 group-hover:border-blue-300 transition overflow-hidden">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    </div>
                </button>
                <div id="profileDropdownMenu"
                     class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-xl shadow-xl py-2 z-50 profile-menu-dropdown transition transform opacity-0 scale-95 pointer-events-none">
                    <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
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
            <!-- End Profile Dropdown -->
        </nav>
        <!-- End Navbar -->

        <!-- Main Content -->
        <main class="p-4 md:p-8">
            @if (isset($header))
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $header }}</h2>
                </div>
            @endif

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm min-h-[calc(100vh-12rem)] p-8">
                {{ $slot }}
            </div>

            <footer class="py-8 text-center text-gray-400 text-[10px] tracking-widest uppercase">
                &copy; 2026 Teknik Elektro - Universitas Mulawarman
            </footer>
        </main>
        <!-- End Main Content -->
    </div>
    <!-- End Main Content Wrapper -->

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden sm:hidden"></div>

    <!-- JS scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Sidebar toggle
            const sidebar    = document.getElementById('main-sidebar');
            const overlay    = document.getElementById('sidebarOverlay');
            const toggleBtn  = document.getElementById('sidebarToggle');

            function toggleSidebar() {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }

            if (toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
            if (overlay)  overlay.addEventListener('click', toggleSidebar);

            // Profile Dropdown
            const profileBtn  = document.getElementById('profileDropdownButton');
            const profileMenu = document.getElementById('profileDropdownMenu');

            function openProfileMenu() {
                profileMenu.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
                profileMenu.classList.add('opacity-100', 'scale-100');
                profileBtn.setAttribute('aria-expanded', 'true');
            }
            function closeProfileMenu() {
                profileMenu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
                profileMenu.classList.remove('opacity-100', 'scale-100');
                profileBtn.setAttribute('aria-expanded', 'false');
            }
            function toggleProfileMenu(e) {
                e.stopPropagation();
                if (profileMenu.classList.contains('opacity-0')) {
                    openProfileMenu();
                } else {
                    closeProfileMenu();
                }
            }
            if(profileBtn && profileMenu) {
                closeProfileMenu();
                profileBtn.addEventListener('click', toggleProfileMenu);
                document.addEventListener('mousedown', function(event) {
                    if (!profileMenu.contains(event.target) && !profileBtn.contains(event.target)) {
                        closeProfileMenu();
                    }
                });
                document.addEventListener('keydown', function(event) {
                    if(event.key === "Escape") {
                        closeProfileMenu();
                    }
                });
            }
        });
    </script>
</body>
</html>