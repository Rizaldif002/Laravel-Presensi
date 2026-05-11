<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('dosen.riwayat.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <span class="text-xs text-gray-400">Riwayat Presensi</span>
                </div>
                <h2 class="text-lg font-bold text-gray-800 leading-tight">
                    {{ $kelas->mataKuliah->nama_mk ?? '-' }} — Kelas {{ $kelas->nama_kelas }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $kelas->tahunAjaran->tahun_ajaran ?? '-' }} {{ $kelas->tahunAjaran->semester ?? '' }}
                </p>
            </div>
            <button disabled class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-400 text-sm font-semibold rounded-lg cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export PDF
            </button>
        </div>
    </x-slot>

    <div class="px-5 pt-5 pb-8" x-data="{ search: '' }">

        {{-- Summary cards --}}
        <div class="grid grid-cols-3 gap-4 mb-5">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Total Pertemuan</p>
                <p class="text-2xl font-extrabold text-indigo-600">{{ $sesiList->count() }}</p>
            </div>
            <div class="bg-white rounded-xl border border-emerald-200 shadow-sm p-4 text-center">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Total Mahasiswa</p>
                <p class="text-2xl font-extrabold text-emerald-600">{{ $mahasiswas->count() }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Total Hadir</p>
                <p class="text-2xl font-extrabold text-gray-700">
                    {{ collect($matrix)->sum('hadir') }}
                </p>
            </div>
        </div>

        @if($sesiList->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center text-sm text-gray-400">
            Belum ada sesi selesai untuk kelas ini.
        </div>
        @else

        {{-- Search bar --}}
        <div class="mb-3">
            <div class="relative max-w-sm">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input x-model="search" type="text" placeholder="Cari nama atau NIM..."
                    class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        {{-- Matrix table --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase sticky left-0 bg-gray-50 z-10 min-w-[40px]">No</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase sticky left-10 bg-gray-50 z-10 min-w-[180px]">Nama Mahasiswa</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase min-w-[110px]">NIM</th>
                            @foreach($sesiList as $i => $sesi)
                            <th class="px-2 py-3 text-center font-bold text-gray-500 uppercase min-w-[72px]">
                                <span class="block text-gray-700">P{{ $i + 1 }}</span>
                                <span class="block font-normal text-gray-400 normal-case">
                                    {{ \Carbon\Carbon::parse($sesi->waktu_buka)->format('d/m') }}
                                </span>
                            </th>
                            @endforeach
                            <th class="px-3 py-3 text-center font-bold text-emerald-600 uppercase min-w-[56px]">H</th>
                            <th class="px-3 py-3 text-center font-bold text-red-500 uppercase min-w-[56px]">A</th>
                            <th class="px-3 py-3 text-center font-bold text-gray-500 uppercase min-w-[64px]">%</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($mahasiswas as $no => $m)
                        @php
                            $row = $matrix[$m->id] ?? ['hadir' => 0, 'alpa' => 0, 'sesi' => []];
                            $pct = $sesiList->count() > 0 ? round($row['hadir'] / $sesiList->count() * 100) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors"
                            x-show="search === '' || '{{ strtolower($m->nama_lengkap) }}'.includes(search.toLowerCase()) || '{{ $m->nim }}'.includes(search)">
                            <td class="px-4 py-3 text-gray-500 sticky left-0 bg-white z-10">{{ $no + 1 }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-800 sticky left-10 bg-white z-10">{{ $m->nama_lengkap }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $m->nim }}</td>
                            @foreach($sesiList as $sesi)
                            @php $status = $row['sesi'][$sesi->id] ?? 'A'; @endphp
                            <td class="px-2 py-3 text-center">
                                @if($status === 'H')
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 font-bold">H</span>
                                @else
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-red-100 text-red-600 font-bold">A</span>
                                @endif
                            </td>
                            @endforeach
                            <td class="px-3 py-3 text-center font-bold text-emerald-600">{{ $row['hadir'] }}</td>
                            <td class="px-3 py-3 text-center font-bold text-red-500">{{ $row['alpa'] }}</td>
                            <td class="px-3 py-3 text-center">
                                <span class="font-semibold {{ $pct >= 75 ? 'text-emerald-600' : 'text-red-500' }}">{{ $pct }}%</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ 6 + $sesiList->count() }}" class="px-4 py-12 text-center text-gray-400">
                                Belum ada mahasiswa yang absen di kelas ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Legend --}}
        <div class="mt-3 flex flex-wrap items-center gap-4 text-xs text-gray-500">
            <span class="flex items-center gap-1.5">
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs">H</span>
                Hadir
            </span>
            <span class="flex items-center gap-1.5">
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-600 font-bold text-xs">A</span>
                Alpa (tidak hadir)
            </span>
            <span class="flex items-center gap-1.5 ml-auto">
                <span class="text-emerald-600 font-semibold">%</span>
                Persentase kehadiran &mdash; merah jika &lt;75%
            </span>
        </div>

        @endif
    </div>
</x-app-layout>
