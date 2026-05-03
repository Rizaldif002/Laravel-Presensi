<x-app-layout>
    @section('title', 'Dashboard Dosen')

    <div class="bg-white border-b border-gray-200 px-8 py-4 mb-6 shadow-sm">
        <h2 class="text-xl font-bold text-gray-800">
            Selamat Datang, {{ $dosen?->nama_dosen ?? auth()->user()->name }}
        </h2>
        <p class="text-sm text-gray-500">
            Ringkasan aktivitas perkuliahan Anda hari ini —
            <strong class="text-blue-600">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</strong>
        </p>
    </div>

    @if(!$dosen)
        <div class="px-8">
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 text-center">
                <svg class="w-12 h-12 text-amber-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <p class="text-amber-800 font-semibold">Profil dosen belum ditautkan ke akun ini.</p>
                <p class="text-sm text-amber-600 mt-1">Hubungi Administrator untuk menautkan akun Anda ke data dosen.</p>
            </div>
        </div>
    @else

    <div class="px-8 space-y-6">

        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            {{-- Jadwal hari ini --}}
            <div class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4 shadow-sm">
                <div class="p-4 rounded-lg bg-blue-50 text-blue-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Jadwal Hari Ini</p>
                    <h4 class="text-2xl font-extrabold text-gray-800">{{ $jadwalHariIni->count() }}</h4>
                </div>
            </div>

            {{-- Sesi aktif --}}
            <div class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4 shadow-sm">
                <div class="p-4 rounded-lg bg-emerald-50 text-emerald-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Sesi Aktif</p>
                    <h4 class="text-2xl font-extrabold text-gray-800">{{ $sesiAktif->count() }}</h4>
                </div>
            </div>

            {{-- Total hadir --}}
            <div class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4 shadow-sm">
                <div class="p-4 rounded-lg bg-indigo-50 text-indigo-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Mahasiswa Hadir</p>
                    <h4 class="text-2xl font-extrabold text-gray-800">{{ $totalHadir }}</h4>
                </div>
            </div>
        </div>

        {{-- Sesi yang sedang aktif --}}
        @if($sesiAktif->count() > 0)
        <div class="bg-white rounded-xl border border-emerald-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-emerald-100 bg-emerald-50 flex items-center gap-2">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                </span>
                <h3 class="font-bold text-emerald-800 text-sm">Sesi Presensi Sedang Aktif</h3>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($sesiAktif as $sesi)
                <div class="px-6 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <div>
                        <p class="font-bold text-gray-800">
                            {{ $sesi->jadwalPerkuliahan->kelasPerkuliahan->mataKuliah->nama_mk ?? 'N/A' }}
                            <span class="ml-1 text-xs font-normal text-gray-500">
                                — Kelas {{ $sesi->jadwalPerkuliahan->kelasPerkuliahan->nama_kelas ?? '' }}
                            </span>
                        </p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $sesi->jadwalPerkuliahan->hari }}
                            {{ substr($sesi->jadwalPerkuliahan->jam_mulai, 0, 5) }}–{{ substr($sesi->jadwalPerkuliahan->jam_selesai, 0, 5) }}
                            &bull; {{ $sesi->jadwalPerkuliahan->ruangan?->nama_ruangan ?? '-' }}
                            &bull; Dibuka {{ \Carbon\Carbon::parse($sesi->waktu_buka)->format('H:i') }}
                        </p>
                        <p class="text-xs font-semibold text-emerald-600 mt-1">
                            {{ $sesi->presensis->count() }} mahasiswa hadir
                        </p>
                    </div>
                    <a href="{{ route('dosen.sesi.show', $sesi->id) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-3 9a9 9 0 110-18 9 9 0 010 18z"/>
                        </svg>
                        Lihat Live
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Jadwal hari ini --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 text-sm">
                    Jadwal Hari Ini
                    <span class="ml-1 text-blue-600">({{ $hariIni }})</span>
                </h3>
                <a href="{{ route('dosen.sesi.index') }}"
                   class="text-xs text-blue-600 hover:underline font-medium">
                    Buka Panel Sesi →
                </a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($jadwalHariIni as $jadwal)
                    @php
                        $now        = \Carbon\Carbon::now();
                        $jamMulai   = \Carbon\Carbon::parse($jadwal->jam_mulai);
                        $jamSelesai = \Carbon\Carbon::parse($jadwal->jam_selesai);
                        $sesiJ      = $jadwal->sesiPresensis->first();

                        if ($now->between($jamMulai, $jamSelesai)) {
                            $status = 'Sedang Berjalan'; $badge = 'bg-blue-100 text-blue-700';
                        } elseif ($now->gt($jamSelesai)) {
                            $status = 'Selesai'; $badge = 'bg-gray-100 text-gray-500';
                        } else {
                            $status = 'Belum Mulai'; $badge = 'bg-yellow-100 text-yellow-700';
                        }
                    @endphp
                    <div class="px-6 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                        <div>
                            <p class="font-bold text-gray-800 text-sm">
                                {{ $jadwal->kelasPerkuliahan->mataKuliah->nama_mk ?? '-' }}
                                <span class="text-xs font-normal text-gray-500">— Kelas {{ $jadwal->kelasPerkuliahan->nama_kelas ?? '' }}</span>
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $jamMulai->format('H:i') }}–{{ $jamSelesai->format('H:i') }}
                                &bull; {{ $jadwal->ruangan?->nama_ruangan ?? '-' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-md {{ $badge }}">{{ $status }}</span>
                            @if($sesiJ)
                                <a href="{{ route('dosen.sesi.show', $sesiJ->id) }}"
                                   class="text-xs text-emerald-600 hover:underline font-semibold">Live →</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center text-sm text-gray-400 italic">
                        Tidak ada jadwal perkuliahan hari ini.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
    @endif
</x-app-layout>
