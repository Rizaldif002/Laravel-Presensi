<x-app-layout>
    @section('title', 'Riwayat Presensi')

    {{-- Header --}}
    <div class="mb-6 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h2 class="text-xl font-extrabold text-gray-800">Riwayat Presensi</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Rekap seluruh catatan kehadiran mahasiswa
                    @if(auth()->user()->isDosen())
                        pada kelas yang Anda ampu.
                    @else
                        di semua kelas.
                    @endif
                </p>
            </div>
            <span class="text-xs font-medium px-3 py-1.5 bg-blue-100 text-blue-700 rounded-full whitespace-nowrap">
                {{ now()->isoFormat('dddd, D MMMM Y') }}
            </span>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total</p>
                <p class="text-xl font-bold text-gray-800">{{ number_format($stats['total']) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">Hadir</p>
                <p class="text-xl font-bold text-emerald-600">{{ number_format($stats['hadir']) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-yellow-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">Terlambat</p>
                <p class="text-xl font-bold text-yellow-600">{{ number_format($stats['terlambat']) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">Izin / Alfa</p>
                <p class="text-xl font-bold text-red-500">{{ number_format($stats['izin'] + $stats['alfa']) }}</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Cari NIM, nama mahasiswa, atau mata kuliah…"
                       class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <select name="status"
                    class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                <option value="">Semua Status</option>
                @foreach(['Hadir', 'Terlambat', 'Izin', 'Sakit', 'Alfa'] as $s)
                    <option value="{{ $s }}" {{ ($filters['status'] ?? '') === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>

            <input type="date" name="tanggal" value="{{ $filters['tanggal'] ?? '' }}"
                   class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">

            <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors whitespace-nowrap">
                Cari
            </button>

            @if(array_filter($filters ?? []))
                <a href="{{ request()->url() }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors whitespace-nowrap">
                    Reset
                </a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    @if($presensiList->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-gray-500 font-medium">Belum ada data presensi.</p>
            <p class="text-sm text-gray-400 mt-1">
                @if(array_filter($filters ?? []))
                    Tidak ada hasil yang cocok dengan filter yang dipilih.
                @else
                    Data presensi akan muncul setelah mahasiswa melakukan absensi.
                @endif
            </p>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Mahasiswa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Mata Kuliah</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ruangan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu Absen</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($presensiList as $presensi)
                            @php
                                $jadwal  = $presensi->sesiPresensi?->jadwalPerkuliahan;
                                $kelas   = $jadwal?->kelasPerkuliahan;
                                $mk      = $kelas?->mataKuliah;
                                $ruangan = $jadwal?->ruangan;
                                $statusMap = [
                                    'Hadir'     => 'bg-emerald-100 text-emerald-700',
                                    'Terlambat' => 'bg-yellow-100 text-yellow-700',
                                    'Izin'      => 'bg-blue-100 text-blue-700',
                                    'Sakit'     => 'bg-purple-100 text-purple-700',
                                    'Alfa'      => 'bg-red-100 text-red-700',
                                ];
                                $statusClass = $statusMap[$presensi->status_kehadiran] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <tr class="odd:bg-white even:bg-gray-50 hover:bg-blue-50 transition-colors">
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-gray-800">{{ $presensi->mahasiswa?->nama_lengkap ?? '-' }}</p>
                                    <p class="text-xs text-gray-400">{{ $presensi->mahasiswa?->nim ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-700">{{ $mk?->nama_mk ?? '-' }}</p>
                                    <p class="text-xs text-gray-400">{{ $kelas?->nama_kelas ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $ruangan?->nama_ruangan ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                    {{ $presensi->waktu_absen ? \Carbon\Carbon::parse($presensi->waktu_absen)->isoFormat('D MMM Y, HH:mm') : '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusClass }}">
                                        {{ $presensi->status_kehadiran ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($presensiList->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">
                    {{ $presensiList->links() }}
                </div>
            @endif
        </div>

        <p class="text-xs text-gray-400 text-right mt-2">
            Menampilkan {{ $presensiList->firstItem() }}–{{ $presensiList->lastItem() }} dari {{ $presensiList->total() }} data
        </p>
    @endif
</x-app-layout>
