<x-app-layout>
    @section('title', 'Panel Dosen — Sesi Presensi')

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl text-sm font-medium">
            {{ session('info') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="mb-6 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h2 class="text-xl font-extrabold text-gray-800">Panel Sesi Presensi</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Selamat datang, <span class="font-semibold text-blue-600">{{ $dosen->nama_dosen }}</span> — pilih jadwal untuk membuka sesi absensi.
                </p>
            </div>
            <span class="text-xs font-medium px-3 py-1.5 bg-blue-100 text-blue-700 rounded-full">
                {{ now()->isoFormat('dddd, D MMMM Y') }}
            </span>
        </div>
    </div>

    {{-- Kelas list --}}
    @forelse ($kelasList as $kelas)
        <div class="mb-6 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

            {{-- Kelas header --}}
            <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                <div>
                    <h3 class="text-base font-bold text-white">
                        {{ $kelas->mataKuliah->nama_mk ?? 'N/A' }}
                        <span class="ml-2 text-xs font-normal bg-blue-500 px-2 py-0.5 rounded-full">{{ $kelas->mataKuliah->kode_mk ?? '' }}</span>
                    </h3>
                    <p class="text-blue-200 text-xs mt-0.5">
                        Kelas {{ $kelas->nama_kelas }} &bull;
                        {{ $kelas->mataKuliah->sks ?? '-' }} SKS &bull;
                        {{ $kelas->tahunAjaran->tahun_ajaran ?? '-' }} {{ $kelas->tahunAjaran->semester ?? '' }}
                    </p>
                </div>
                <span class="text-xs font-medium px-3 py-1 bg-blue-800/60 text-blue-100 rounded-full whitespace-nowrap">
                    {{ $kelas->jadwalPerkuliahans->count() }} jadwal
                </span>
            </div>

            {{-- Jadwal list --}}
            @forelse ($kelas->jadwalPerkuliahans as $jadwal)
                @php
                    $sesiAktif = $jadwal->sesiPresensis->first();
                @endphp
                <div class="px-6 py-4 border-b border-gray-50 last:border-b-0 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">

                    {{-- Jadwal info --}}
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center
                            {{ $sesiAktif ? 'bg-emerald-100' : 'bg-gray-100' }}">
                            <svg class="w-6 h-6 {{ $sesiAktif ? 'text-emerald-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-bold text-gray-800">{{ $jadwal->hari }}</span>
                                <span class="text-xs text-gray-500">{{ substr($jadwal->jam_mulai, 0, 5) }} – {{ substr($jadwal->jam_selesai, 0, 5) }} WITA</span>
                                @if($sesiAktif)
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded-full">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                        SESI AKTIF
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5">
                                <svg class="w-3 h-3 inline mr-0.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                                {{ $jadwal->ruangan->nama_ruangan ?? 'Ruangan belum diatur' }}
                                @if($jadwal->ruangan?->gedung)
                                    &bull; {{ $jadwal->ruangan->gedung }}
                                @endif
                                &bull; Radius {{ $jadwal->ruangan->radius_meter ?? '-' }} m
                            </p>
                        </div>
                    </div>

                    {{-- Action button --}}
                    @if($sesiAktif)
                        <a href="{{ route('dosen.sesi.show', $sesiAktif->id) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-3 9a9 9 0 110-18 9 9 0 010 18z"/>
                            </svg>
                            Lihat Sesi Live
                        </a>
                    @else
                        <form action="{{ route('dosen.sesi.buka') }}" method="POST">
                            @csrf
                            <input type="hidden" name="jadwal_perkuliahan_id" value="{{ $jadwal->id }}">
                            <button type="submit"
                                onclick="return confirm('Buka sesi presensi untuk jadwal {{ $jadwal->hari }} {{ substr($jadwal->jam_mulai,0,5) }}?')"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all whitespace-nowrap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Buka Sesi
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="px-6 py-8 text-center text-sm text-gray-400">
                    Belum ada jadwal perkuliahan untuk kelas ini.
                </div>
            @endforelse
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-gray-500 font-medium">Anda belum memiliki kelas yang ditugaskan.</p>
            <p class="text-sm text-gray-400 mt-1">Hubungi administrator untuk mendapatkan kelas perkuliahan.</p>
        </div>
    @endforelse
</x-app-layout>
