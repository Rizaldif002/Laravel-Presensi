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
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
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
        #main-sidebar .sidebar-scroll {
            overflow-y: auto !important;
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
        }
        #main-sidebar .sidebar-scroll::-webkit-scrollbar { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-100" x-data="{ sidebarExpanded: true }">

@php
    $user     = auth()->user();
    $initials = collect(explode(' ', $user->name))
        ->filter()
        ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
        ->take(2)
        ->implode('');
@endphp

{{-- ═══════════════════════════ SIDEBAR ═══════════════════════════ --}}
<aside id="main-sidebar"
       class="fixed top-0 left-0 z-50 h-screen transition-all duration-300 -translate-x-full sm:translate-x-0 bg-blue-900 shadow-xl flex flex-col"
       :class="sidebarExpanded ? 'w-64' : 'w-20'">

    {{-- Sidebar Header --}}
    <div class="shrink-0 h-16 flex items-center justify-center border-b border-blue-800/50 overflow-hidden px-3">
        <p x-show="sidebarExpanded"
           x-transition:enter="transition-opacity ease-out duration-200"
           x-transition:enter-start="opacity-0"
           x-transition:enter-end="opacity-100"
           x-transition:leave="transition-opacity ease-in duration-100"
           x-transition:leave-start="opacity-100"
           x-transition:leave-end="opacity-0"
           class="text-sm font-bold text-blue-400/80 uppercase tracking-[0.25em] select-none text-center whitespace-nowrap">
            Presensi FT Unmul
        </p>
        <p x-show="!sidebarExpanded" x-cloak
           x-transition:enter="transition-opacity ease-out duration-200"
           x-transition:enter-start="opacity-0"
           x-transition:enter-end="opacity-100"
           x-transition:leave="transition-opacity ease-in duration-100"
           x-transition:leave-start="opacity-100"
           x-transition:leave-end="opacity-0"
           class="text-sm font-bold text-blue-400/80 uppercase tracking-wider select-none">
            FT
        </p>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-3 py-4 sidebar-scroll overflow-y-auto">

        {{-- Dashboard --}}
        <div class="mb-5">
            <a href="{{ route('dashboard') }}"
               title="Dashboard"
               :class="sidebarExpanded ? 'px-4 gap-3 justify-start' : 'justify-center px-0 gap-0'"
               class="flex items-center py-2.5 rounded-xl text-white transition-all duration-300
                      {{ request()->routeIs('dashboard') ? 'bg-blue-600 shadow-md font-semibold' : 'hover:bg-blue-800/70 font-medium' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span x-show="sidebarExpanded"
                      x-transition:enter="transition-opacity ease-out duration-200"
                      x-transition:enter-start="opacity-0"
                      x-transition:enter-end="opacity-100"
                      x-transition:leave="transition-opacity ease-in duration-100"
                      x-transition:leave-start="opacity-100"
                      x-transition:leave-end="opacity-0"
                      class="text-base whitespace-nowrap">Dashboard</span>
            </a>
        </div>

        {{-- ======================== --}}
        {{-- MENU ADMIN               --}}
        {{-- ======================== --}}
        @if($user->isAdmin())

        @php
            $penggunaOpen      = request()->routeIs('admin.dosen', 'admin.mahasiswa');
            $akademikOpen      = request()->routeIs('admin.ruangan', 'admin.mata-kuliah', 'admin.tahun-ajaran', 'admin.kelas.*', 'admin.jadwal.*');
            $presensiAdminOpen = request()->routeIs('admin.sesi.*', 'admin.riwayat.*');
        @endphp

        {{-- ── MASTER DATA Section ── --}}
        <p x-show="sidebarExpanded"
           x-transition:enter="transition-opacity ease-out duration-200"
           x-transition:enter-start="opacity-0"
           x-transition:enter-end="opacity-100"
           x-transition:leave="transition-opacity ease-in duration-100"
           x-transition:leave-start="opacity-100"
           x-transition:leave-end="opacity-0"
           class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-400/60 px-4 mb-2 mt-1">Master Data</p>

        {{-- Pengguna --}}
        <div class="mb-4" x-data="{ open: {{ $penggunaOpen ? 'true' : 'false' }} }">
            <button @click="if (sidebarExpanded) open = !open"
                    type="button"
                    title="Pengguna"
                    :class="sidebarExpanded ? 'px-4 gap-3 justify-start' : 'justify-center px-0 gap-0'"
                    class="w-full flex items-center py-2.5 rounded-xl text-white font-medium transition-all duration-300
                           {{ $penggunaOpen ? 'bg-blue-800/50' : 'hover:bg-blue-800/70' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="flex-shrink-0">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span x-show="sidebarExpanded"
                      x-transition:enter="transition-opacity ease-out duration-200"
                      x-transition:enter-start="opacity-0"
                      x-transition:enter-end="opacity-100"
                      x-transition:leave="transition-opacity ease-in duration-100"
                      x-transition:leave-start="opacity-100"
                      x-transition:leave-end="opacity-0"
                      class="flex-1 text-left text-base font-semibold whitespace-nowrap">Pengguna</span>
                <svg x-show="sidebarExpanded"
                     x-transition:enter="transition-opacity ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity ease-in duration-100"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     :class="open ? 'rotate-180' : ''"
                     class="transition-transform duration-300 flex-shrink-0"
                     style="width:16px;height:16px;"
                     fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open && sidebarExpanded" x-cloak
                 x-transition:enter="transition-all ease-out duration-300"
                 x-transition:enter-start="opacity-0 max-h-0 overflow-hidden"
                 x-transition:enter-end="opacity-100 max-h-96 overflow-hidden"
                 x-transition:leave="transition-all ease-in duration-200"
                 x-transition:leave-start="opacity-100 max-h-96 overflow-hidden"
                 x-transition:leave-end="opacity-0 max-h-0 overflow-hidden"
                 class="mt-1 ml-3 pl-3 border-l-2 border-blue-700/40 space-y-0.5">

                <a href="{{ route('admin.dosen') }}"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-base transition-all duration-300
                          {{ request()->routeIs('admin.dosen') ? 'bg-blue-700/50 text-white font-semibold' : 'text-blue-200/80 hover:text-white hover:bg-blue-800/50' }}">
                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ request()->routeIs('admin.dosen') ? 'bg-white' : 'bg-blue-400/50' }}"></span>
                    Data Dosen
                </a>

                <a href="{{ route('admin.mahasiswa') }}"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-base transition-all duration-300
                          {{ request()->routeIs('admin.mahasiswa') ? 'bg-blue-700/50 text-white font-semibold' : 'text-blue-200/80 hover:text-white hover:bg-blue-800/50' }}">
                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ request()->routeIs('admin.mahasiswa') ? 'bg-white' : 'bg-blue-400/50' }}"></span>
                    Data Mahasiswa
                </a>
            </div>
        </div>

        {{-- Akademik --}}
        <div class="mb-4" x-data="{ open: {{ $akademikOpen ? 'true' : 'false' }} }">
            <button @click="if (sidebarExpanded) open = !open"
                    type="button"
                    title="Akademik"
                    :class="sidebarExpanded ? 'px-4 gap-3 justify-start' : 'justify-center px-0 gap-0'"
                    class="w-full flex items-center py-2.5 rounded-xl text-white font-medium transition-all duration-300
                           {{ $akademikOpen ? 'bg-blue-800/50' : 'hover:bg-blue-800/70' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="flex-shrink-0">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span x-show="sidebarExpanded"
                      x-transition:enter="transition-opacity ease-out duration-200"
                      x-transition:enter-start="opacity-0"
                      x-transition:enter-end="opacity-100"
                      x-transition:leave="transition-opacity ease-in duration-100"
                      x-transition:leave-start="opacity-100"
                      x-transition:leave-end="opacity-0"
                      class="flex-1 text-left text-base font-semibold whitespace-nowrap">Akademik</span>
                <svg x-show="sidebarExpanded"
                     x-transition:enter="transition-opacity ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity ease-in duration-100"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     :class="open ? 'rotate-180' : ''"
                     class="transition-transform duration-300 flex-shrink-0"
                     style="width:16px;height:16px;"
                     fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open && sidebarExpanded" x-cloak
                 x-transition:enter="transition-all ease-out duration-300"
                 x-transition:enter-start="opacity-0 max-h-0 overflow-hidden"
                 x-transition:enter-end="opacity-100 max-h-96 overflow-hidden"
                 x-transition:leave="transition-all ease-in duration-200"
                 x-transition:leave-start="opacity-100 max-h-96 overflow-hidden"
                 x-transition:leave-end="opacity-0 max-h-0 overflow-hidden"
                 class="mt-1 ml-3 pl-3 border-l-2 border-blue-700/40 space-y-0.5">

                <a href="{{ route('admin.tahun-ajaran') }}"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-base transition-all duration-300
                          {{ request()->routeIs('admin.tahun-ajaran') ? 'bg-blue-700/50 text-white font-semibold' : 'text-blue-200/80 hover:text-white hover:bg-blue-800/50' }}">
                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ request()->routeIs('admin.tahun-ajaran') ? 'bg-white' : 'bg-blue-400/50' }}"></span>
                    Data Tahun Ajaran
                </a>

                <a href="{{ route('admin.mata-kuliah') }}"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-base transition-all duration-300
                          {{ request()->routeIs('admin.mata-kuliah') ? 'bg-blue-700/50 text-white font-semibold' : 'text-blue-200/80 hover:text-white hover:bg-blue-800/50' }}">
                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ request()->routeIs('admin.mata-kuliah') ? 'bg-white' : 'bg-blue-400/50' }}"></span>
                    Data Mata Kuliah
                </a>

                <a href="{{ route('admin.ruangan') }}"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-base transition-all duration-300
                          {{ request()->routeIs('admin.ruangan') ? 'bg-blue-700/50 text-white font-semibold' : 'text-blue-200/80 hover:text-white hover:bg-blue-800/50' }}">
                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ request()->routeIs('admin.ruangan') ? 'bg-white' : 'bg-blue-400/50' }}"></span>
                    Data Ruangan
                </a>

                <a href="{{ route('admin.kelas.index') }}"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-base transition-all duration-300
                          {{ request()->routeIs('admin.kelas.*') ? 'bg-blue-700/50 text-white font-semibold' : 'text-blue-200/80 hover:text-white hover:bg-blue-800/50' }}">
                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ request()->routeIs('admin.kelas.*') ? 'bg-white' : 'bg-blue-400/50' }}"></span>
                    Data Kelas
                </a>

                <a href="{{ route('admin.jadwal.index') }}"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-base transition-all duration-300
                          {{ request()->routeIs('admin.jadwal.*') ? 'bg-blue-700/50 text-white font-semibold' : 'text-blue-200/80 hover:text-white hover:bg-blue-800/50' }}">
                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ request()->routeIs('admin.jadwal.*') ? 'bg-white' : 'bg-blue-400/50' }}"></span>
                    Data Perkuliahan
                </a>
            </div>
        </div>

        {{-- ── PRESENSI Section ── --}}
        <p x-show="sidebarExpanded"
           x-transition:enter="transition-opacity ease-out duration-200"
           x-transition:enter-start="opacity-0"
           x-transition:enter-end="opacity-100"
           x-transition:leave="transition-opacity ease-in duration-100"
           x-transition:leave-start="opacity-100"
           x-transition:leave-end="opacity-0"
           class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-400/60 px-4 mb-2 mt-2">Presensi</p>

        <div class="mb-4" x-data="{ open: {{ $presensiAdminOpen ? 'true' : 'false' }} }">
            <button @click="if (sidebarExpanded) open = !open"
                    type="button"
                    title="Presensi"
                    :class="sidebarExpanded ? 'px-4 gap-3 justify-start' : 'justify-center px-0 gap-0'"
                    class="w-full flex items-center py-2.5 rounded-xl text-white font-medium transition-all duration-300
                           {{ $presensiAdminOpen ? 'bg-blue-800/50' : 'hover:bg-blue-800/70' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="flex-shrink-0">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <span x-show="sidebarExpanded"
                      x-transition:enter="transition-opacity ease-out duration-200"
                      x-transition:enter-start="opacity-0"
                      x-transition:enter-end="opacity-100"
                      x-transition:leave="transition-opacity ease-in duration-100"
                      x-transition:leave-start="opacity-100"
                      x-transition:leave-end="opacity-0"
                      class="flex-1 text-left text-base font-semibold whitespace-nowrap">Presensi</span>
                <svg x-show="sidebarExpanded"
                     x-transition:enter="transition-opacity ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity ease-in duration-100"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     :class="open ? 'rotate-180' : ''"
                     class="transition-transform duration-300 flex-shrink-0"
                     style="width:16px;height:16px;"
                     fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open && sidebarExpanded" x-cloak
                 x-transition:enter="transition-all ease-out duration-300"
                 x-transition:enter-start="opacity-0 max-h-0 overflow-hidden"
                 x-transition:enter-end="opacity-100 max-h-96 overflow-hidden"
                 x-transition:leave="transition-all ease-in duration-200"
                 x-transition:leave-start="opacity-100 max-h-96 overflow-hidden"
                 x-transition:leave-end="opacity-0 max-h-0 overflow-hidden"
                 class="mt-1 ml-3 pl-3 border-l-2 border-blue-700/40 space-y-0.5">

                <a href="{{ route('admin.sesi.index') }}"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-base transition-all duration-300
                          {{ request()->routeIs('admin.sesi.*') ? 'bg-blue-700/50 text-white font-semibold' : 'text-blue-200/80 hover:text-white hover:bg-blue-800/50' }}">
                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ request()->routeIs('admin.sesi.*') ? 'bg-white' : 'bg-blue-400/50' }}"></span>
                    Kelola Sesi
                </a>

                <a href="{{ route('admin.riwayat.index') }}"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-base transition-all duration-300
                          {{ request()->routeIs('admin.riwayat.*') ? 'bg-blue-700/50 text-white font-semibold' : 'text-blue-200/80 hover:text-white hover:bg-blue-800/50' }}">
                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ request()->routeIs('admin.riwayat.*') ? 'bg-white' : 'bg-blue-400/50' }}"></span>
                    Riwayat Presensi
                </a>
            </div>
        </div>

        {{-- ── LAPORAN Section ── --}}
        <p x-show="sidebarExpanded"
           x-transition:enter="transition-opacity ease-out duration-200"
           x-transition:enter-start="opacity-0"
           x-transition:enter-end="opacity-100"
           x-transition:leave="transition-opacity ease-in duration-100"
           x-transition:leave-start="opacity-100"
           x-transition:leave-end="opacity-0"
           class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-400/60 px-4 mb-2 mt-2">Laporan</p>

        <div class="mb-5">
            <a href="{{ route('admin.laporan.presensi') }}"
               title="Laporan Presensi"
               :class="sidebarExpanded ? 'px-4 gap-3 justify-start' : 'justify-center px-0 gap-0'"
               class="flex items-center py-2.5 rounded-xl text-white transition-all duration-300
                      {{ request()->routeIs('admin.laporan.presensi') ? 'bg-blue-600 shadow-md font-semibold' : 'hover:bg-blue-800/70 font-medium' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="flex-shrink-0">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-6 0h8m-7 4h8a2 2 0 002-2V5a2 2 0 00-2-2h-8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <span x-show="sidebarExpanded"
                      x-transition:enter="transition-opacity ease-out duration-200"
                      x-transition:enter-start="opacity-0"
                      x-transition:enter-end="opacity-100"
                      x-transition:leave="transition-opacity ease-in duration-100"
                      x-transition:leave-start="opacity-100"
                      x-transition:leave-end="opacity-0"
                      class="text-base whitespace-nowrap">Laporan Presensi</span>
            </a>
        </div>

        @endif
        {{-- END MENU ADMIN --}}

        {{-- ======================== --}}
        {{-- MENU DOSEN               --}}
        {{-- ======================== --}}
        @if($user->isDosen())

            @php
                $dosenAkademikOpen = request()->routeIs('dosen.sesi.index');
                $dosenPresensiOpen = request()->routeIs('dosen.sesi.*', 'dosen.riwayat.*');
            @endphp

            {{-- ── AKADEMIK Section ── --}}
            <p x-show="sidebarExpanded"
               x-transition:enter="transition-opacity ease-out duration-200"
               x-transition:enter-start="opacity-0"
               x-transition:enter-end="opacity-100"
               x-transition:leave="transition-opacity ease-in duration-100"
               x-transition:leave-start="opacity-100"
               x-transition:leave-end="opacity-0"
               class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-400/60 px-4 mb-2 mt-1">Akademik</p>

            <div class="mb-2" x-data="{ open: {{ $dosenAkademikOpen ? 'true' : 'false' }} }">
                <button @click="if (sidebarExpanded) open = !open"
                        type="button"
                        title="Akademik"
                        :class="sidebarExpanded ? 'px-4 gap-3 justify-start' : 'justify-center px-0 gap-0'"
                        class="w-full flex items-center py-2.5 rounded-xl text-white font-medium transition-all duration-300
                               {{ $dosenAkademikOpen ? 'bg-blue-800/50' : 'hover:bg-blue-800/70' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="flex-shrink-0">
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span x-show="sidebarExpanded"
                          x-transition:enter="transition-opacity ease-out duration-200"
                          x-transition:enter-start="opacity-0"
                          x-transition:enter-end="opacity-100"
                          x-transition:leave="transition-opacity ease-in duration-100"
                          x-transition:leave-start="opacity-100"
                          x-transition:leave-end="opacity-0"
                          class="flex-1 text-left text-base font-semibold whitespace-nowrap">Akademik</span>
                    <svg x-show="sidebarExpanded"
                         x-transition:enter="transition-opacity ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition-opacity ease-in duration-100"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         :class="open ? 'rotate-180' : ''"
                         class="transition-transform duration-300 flex-shrink-0"
                         style="width:16px;height:16px;"
                         fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open && sidebarExpanded" x-cloak
                     x-transition:enter="transition-all ease-out duration-300"
                     x-transition:enter-start="opacity-0 max-h-0 overflow-hidden"
                     x-transition:enter-end="opacity-100 max-h-96 overflow-hidden"
                     x-transition:leave="transition-all ease-in duration-200"
                     x-transition:leave-start="opacity-100 max-h-96 overflow-hidden"
                     x-transition:leave-end="opacity-0 max-h-0 overflow-hidden"
                     class="mt-1 ml-3 pl-3 border-l-2 border-blue-700/40 space-y-0.5">

                    <a href="{{ route('dosen.sesi.index') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-base transition-all duration-300
                              {{ request()->routeIs('dosen.sesi.index') ? 'bg-blue-700/50 text-white font-semibold' : 'text-blue-200/80 hover:text-white hover:bg-blue-800/50' }}">
                        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ request()->routeIs('dosen.sesi.index') ? 'bg-white' : 'bg-blue-400/50' }}"></span>
                        Kelas Perkuliahan
                    </a>
                </div>
            </div>

            {{-- ── PRESENSI Section ── --}}
            <p x-show="sidebarExpanded"
               x-transition:enter="transition-opacity ease-out duration-200"
               x-transition:enter-start="opacity-0"
               x-transition:enter-end="opacity-100"
               x-transition:leave="transition-opacity ease-in duration-100"
               x-transition:leave-start="opacity-100"
               x-transition:leave-end="opacity-0"
               class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-400/60 px-4 mb-2 mt-2">Presensi</p>

            <div class="mb-2" x-data="{ open: {{ $dosenPresensiOpen ? 'true' : 'false' }} }">
                <button @click="if (sidebarExpanded) open = !open"
                        type="button"
                        title="Presensi"
                        :class="sidebarExpanded ? 'px-4 gap-3 justify-start' : 'justify-center px-0 gap-0'"
                        class="w-full flex items-center py-2.5 rounded-xl text-white font-medium transition-all duration-300
                               {{ $dosenPresensiOpen ? 'bg-blue-800/50' : 'hover:bg-blue-800/70' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="flex-shrink-0">
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span x-show="sidebarExpanded"
                          x-transition:enter="transition-opacity ease-out duration-200"
                          x-transition:enter-start="opacity-0"
                          x-transition:enter-end="opacity-100"
                          x-transition:leave="transition-opacity ease-in duration-100"
                          x-transition:leave-start="opacity-100"
                          x-transition:leave-end="opacity-0"
                          class="flex-1 text-left text-base font-semibold whitespace-nowrap">Presensi</span>
                    <svg x-show="sidebarExpanded"
                         x-transition:enter="transition-opacity ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition-opacity ease-in duration-100"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         :class="open ? 'rotate-180' : ''"
                         class="transition-transform duration-300 flex-shrink-0"
                         style="width:16px;height:16px;"
                         fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open && sidebarExpanded" x-cloak
                     x-transition:enter="transition-all ease-out duration-300"
                     x-transition:enter-start="opacity-0 max-h-0 overflow-hidden"
                     x-transition:enter-end="opacity-100 max-h-96 overflow-hidden"
                     x-transition:leave="transition-all ease-in duration-200"
                     x-transition:leave-start="opacity-100 max-h-96 overflow-hidden"
                     x-transition:leave-end="opacity-0 max-h-0 overflow-hidden"
                     class="mt-1 ml-3 pl-3 border-l-2 border-blue-700/40 space-y-0.5">

                    <a href="{{ route('dosen.sesi.index') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-base transition-all duration-300
                              {{ request()->routeIs('dosen.sesi.*') ? 'bg-blue-700/50 text-white font-semibold' : 'text-blue-200/80 hover:text-white hover:bg-blue-800/50' }}">
                        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ request()->routeIs('dosen.sesi.*') ? 'bg-white' : 'bg-blue-400/50' }}"></span>
                        Kelola Sesi
                    </a>

                    <a href="{{ route('dosen.riwayat.index') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-base transition-all duration-300
                              {{ request()->routeIs('dosen.riwayat.*') ? 'bg-blue-700/50 text-white font-semibold' : 'text-blue-200/80 hover:text-white hover:bg-blue-800/50' }}">
                        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ request()->routeIs('dosen.riwayat.*') ? 'bg-white' : 'bg-blue-400/50' }}"></span>
                        Riwayat Presensi
                    </a>
                </div>
            </div>

            {{-- ── LAPORAN Section ── --}}
            <p x-show="sidebarExpanded"
               x-transition:enter="transition-opacity ease-out duration-200"
               x-transition:enter-start="opacity-0"
               x-transition:enter-end="opacity-100"
               x-transition:leave="transition-opacity ease-in duration-100"
               x-transition:leave-start="opacity-100"
               x-transition:leave-end="opacity-0"
               class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-400/60 px-4 mb-2 mt-2">Laporan</p>

            <div class="mb-5">
                <a href="#"
                   title="Laporan Presensi (Segera)"
                   :class="sidebarExpanded ? 'px-4 gap-3 justify-start' : 'justify-center px-0 gap-0'"
                   class="flex items-center py-2.5 rounded-xl text-white/40 font-medium cursor-not-allowed transition-all duration-300">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="flex-shrink-0">
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-6 0h8m-7 4h8a2 2 0 002-2V5a2 2 0 00-2-2h-8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <span x-show="sidebarExpanded"
                          x-transition:enter="transition-opacity ease-out duration-200"
                          x-transition:enter-start="opacity-0"
                          x-transition:enter-end="opacity-100"
                          x-transition:leave="transition-opacity ease-in duration-100"
                          x-transition:leave-start="opacity-100"
                          x-transition:leave-end="opacity-0"
                          class="text-base whitespace-nowrap">Laporan Presensi</span>
                    <span x-show="sidebarExpanded"
                          x-transition:enter="transition-opacity ease-out duration-200"
                          x-transition:enter-start="opacity-0"
                          x-transition:enter-end="opacity-100"
                          x-transition:leave="transition-opacity ease-in duration-100"
                          x-transition:leave-start="opacity-100"
                          x-transition:leave-end="opacity-0"
                          class="ml-auto text-[10px] bg-blue-700/60 px-1.5 py-0.5 rounded text-blue-300">Soon</span>
                </a>
            </div>

        @endif
        {{-- END MENU DOSEN --}}

    </nav>

    {{-- Sidebar Footer: Profil & Logout --}}
    <div class="shrink-0 border-t border-blue-800/50 p-3 space-y-1">

        {{-- Profil --}}
        <a href="{{ route('profile.edit') }}"
           title="Profil Saya"
           :class="sidebarExpanded ? 'px-2 gap-3 justify-start' : 'justify-center px-0 gap-0'"
           class="flex items-center py-1.5 rounded-xl transition-all duration-300
                  {{ request()->routeIs('profile.edit') ? 'bg-blue-600 shadow-md' : 'hover:bg-blue-800/70' }}">
            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs select-none flex-shrink-0
                {{ $user->isAdmin() ? 'bg-blue-400/30 text-blue-100' : 'bg-emerald-400/30 text-emerald-100' }}">
                {{ $initials }}
            </div>
            <div x-show="sidebarExpanded"
                 x-transition:enter="transition-opacity ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-white truncate">{{ $user->name }}</p>
                <p class="text-[10px] text-blue-300/70 uppercase tracking-wider truncate">
                    @if($user->isAdmin()) Administrator
                    @elseif($user->isDosen()) Dosen
                    @else {{ $user->role }}
                    @endif
                </p>
            </div>
            <svg x-show="sidebarExpanded"
                 x-transition:enter="transition-opacity ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="flex-shrink-0 text-blue-400/60"
                 style="width:16px;height:16px;"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </a>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    title="Keluar"
                    :class="sidebarExpanded ? 'px-2 gap-3 justify-start' : 'justify-center px-0 gap-0'"
                    class="w-full flex items-center py-1.5 rounded-xl text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-all duration-300">
                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <span x-show="sidebarExpanded"
                      x-transition:enter="transition-opacity ease-out duration-200"
                      x-transition:enter-start="opacity-0"
                      x-transition:enter-end="opacity-100"
                      x-transition:leave="transition-opacity ease-in duration-100"
                      x-transition:leave-start="opacity-100"
                      x-transition:leave-end="opacity-0"
                      class="text-sm font-medium whitespace-nowrap">Keluar</span>
            </button>
        </form>

    </div>
</aside>

{{-- ═══════════════════════════ MAIN CONTENT ═══════════════════════════ --}}
<div class="transition-all duration-300" :class="sidebarExpanded ? 'sm:ml-64' : 'sm:ml-20'">

    {{-- TOP NAVBAR --}}
    <nav class="sticky top-0 z-40 w-full bg-white border-b border-gray-200 shadow-sm h-16 flex items-center justify-between px-1 lg:px-2">

        <div class="flex items-center gap-1 sm:gap-2">
            {{-- Mobile toggle --}}
            <button id="sidebarToggle" class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 sm:hidden transition-all duration-200">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"/>
                </svg>
            </button>

            {{-- Desktop collapse toggle --}}
            <button @click="sidebarExpanded = !sidebarExpanded"
                    :title="sidebarExpanded ? 'Tutup sidebar' : 'Buka sidebar'"
                    class="hidden sm:flex items-center justify-center p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-blue-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Teks Judul Statis (Tetap, tidak berubah-ubah) --}}
            <h1 class="text-lg sm:text-xl font-bold text-gray-800 tracking-tight select-none">
                Presensi Teknik Elektro
            </h1>
        </div>

        <div class="flex items-center gap-4 relative">
            
            {{-- Flash error dari middleware --}}
            @if(session('error'))
                <div class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-red-50 border border-red-200 text-red-700 text-xs font-medium rounded-lg">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <button id="profileDropdownButton" class="flex items-center gap-3 focus:outline-none group" type="button">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-gray-800 group-hover:text-blue-600 transition-all duration-300">
                        {{ $user->name }}
                    </p>
                    <p class="text-[10px] text-gray-400 uppercase tracking-tighter">
                        @if($user->isAdmin()) Administrator
                        @elseif($user->isDosen()) Dosen
                        @else {{ $user->role }}
                        @endif
                    </p>
                </div>
                <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 font-bold text-sm select-none transition-all duration-300
                    {{ $user->isAdmin() ? 'bg-blue-100 border-blue-200 text-blue-700' : 'bg-emerald-100 border-emerald-200 text-emerald-700' }}">
                    {{ $initials }}
                </div>
            </button>

            <div id="profileDropdownMenu"
                 class="absolute right-0 top-12 mt-2 w-52 bg-white border border-gray-200 rounded-xl shadow-xl py-2 z-50 transition-all duration-300 transform opacity-0 scale-95 pointer-events-none">

                <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm select-none shrink-0
                        {{ $user->isAdmin() ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                        {{ $initials }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-gray-800 truncate">{{ $user->name }}</p>
                        <p class="text-[10px] text-gray-400 truncate">
                            @if($user->isAdmin()) Administrator
                            @elseif($user->isDosen()) Dosen Pengampu
                            @else {{ $user->role }}
                            @endif
                        </p>
                        <p class="text-[10px] text-gray-400 truncate">{{ $user->email }}</p>
                    </div>
                </div>

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-all duration-300">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Edit Profil
                </a>

                <hr class="my-1 border-gray-100">

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="p-4 md:py-6 md:px-5">
        @if(isset($header))
            <div class="mb-6">
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