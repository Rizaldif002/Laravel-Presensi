@extends("dashboard.layouts.main")

@section("container")
    <div>
        <div class="-mx-3 flex flex-wrap lg:gap-y-3">
            
            <div class="mb-6 w-full max-w-full px-3 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                <a href="{{ route('admin.dosen.index') ?? '#' }}" class="dark:bg-slate-850 dark:shadow-dark-xl relative flex min-w-0 flex-col break-words rounded-2xl bg-white bg-clip-border shadow-xl hover:shadow-2xl transition-shadow cursor-pointer">
                    <div class="flex-auto p-4">
                        <div class="-mx-3 flex flex-row">
                            <div class="w-2/3 max-w-full flex-none px-3">
                                <div>
                                    <p class="mb-0 font-sans text-sm font-semibold uppercase leading-normal dark:text-white dark:opacity-60">Total Dosen</p>
                                    <h5 class="mb-2 font-bold dark:text-white">{{ $totalDosen ?? 0 }}</h5>
                                </div>
                            </div>
                            <div class="basis-1/3 px-3 text-right">
                                <div class="rounded-circle inline-block h-12 w-12 bg-gradient-to-tl from-blue-500 to-violet-500 text-center shadow-sm">
                                    <i class="ri-user-star-line relative top-3 text-2xl leading-none text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="mb-6 w-full max-w-full px-3 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                <a href="{{ route('admin.matakuliah.index') ?? '#' }}" class="dark:bg-slate-850 dark:shadow-dark-xl relative flex min-w-0 flex-col break-words rounded-2xl bg-white bg-clip-border shadow-xl hover:shadow-2xl transition-shadow cursor-pointer">
                    <div class="flex-auto p-4">
                        <div class="-mx-3 flex flex-row">
                            <div class="w-2/3 max-w-full flex-none px-3">
                                <div>
                                    <p class="mb-0 font-sans text-sm font-semibold uppercase leading-normal dark:text-white dark:opacity-60">Mata Kuliah</p>
                                    <h5 class="mb-2 font-bold dark:text-white">{{ $totalMatkul ?? 0 }}</h5>
                                </div>
                            </div>
                            <div class="basis-1/3 px-3 text-right">
                                <div class="rounded-circle inline-block h-12 w-12 bg-gradient-to-tl from-red-600 to-orange-600 text-center shadow-sm">
                                    <i class="ri-book-read-line relative top-3 text-2xl leading-none text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="mb-6 w-full max-w-full px-3 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                <a href="{{ route('admin.ruangan.index') ?? '#' }}" class="dark:bg-slate-850 dark:shadow-dark-xl relative flex min-w-0 flex-col break-words rounded-2xl bg-white bg-clip-border shadow-xl hover:shadow-2xl transition-shadow cursor-pointer">
                    <div class="flex-auto p-4">
                        <div class="-mx-3 flex flex-row">
                            <div class="w-2/3 max-w-full flex-none px-3">
                                <div>
                                    <p class="mb-0 font-sans text-sm font-semibold uppercase leading-normal dark:text-white dark:opacity-60">Titik Ruangan</p>
                                    <h5 class="mb-2 font-bold dark:text-white">{{ $totalRuangan ?? 0 }}</h5>
                                </div>
                            </div>
                            <div class="basis-1/3 px-3 text-right">
                                <div class="rounded-circle inline-block h-12 w-12 bg-gradient-to-tl from-emerald-500 to-teal-400 text-center shadow-sm">
                                    <i class="ri-map-pin-line relative top-3 text-2xl leading-none text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="w-full max-w-full px-3 sm:w-1/2 sm:flex-none xl:w-1/4">
                <a href="{{ route('admin.kelas.index') ?? '#' }}" class="dark:bg-slate-850 dark:shadow-dark-xl relative flex min-w-0 flex-col break-words rounded-2xl bg-white bg-clip-border shadow-xl hover:shadow-2xl transition-shadow cursor-pointer">
                    <div class="flex-auto p-4">
                        <div class="-mx-3 flex flex-row">
                            <div class="w-2/3 max-w-full flex-none px-3">
                                <div>
                                    <p class="mb-0 font-sans text-sm font-semibold uppercase leading-normal dark:text-white dark:opacity-60">Jadwal Aktif</p>
                                    <h5 class="mb-2 font-bold dark:text-white">{{ $totalKelas ?? 0 }}</h5>
                                </div>
                            </div>
                            <div class="basis-1/3 px-3 text-right">
                                <div class="rounded-circle inline-block h-12 w-12 bg-gradient-to-tl from-orange-500 to-yellow-500 text-center shadow-sm">
                                    <i class="ri-calendar-event-line relative top-3 text-2xl leading-none text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="-mx-3 mt-6 flex flex-wrap">
            <div class="mb-6 mt-0 w-full max-w-full px-3">
                <div class="dark:bg-slate-850 dark:shadow-dark-xl relative flex min-w-0 flex-col break-words rounded-2xl border-0 border-solid bg-white bg-clip-border shadow-xl">
                    
                    <div class="rounded-t-2xl mb-0 p-4 pb-0 flex justify-between items-center border-b border-gray-100 dark:border-white/10 pb-4">
                        <h6 class="mb-0 dark:text-white text-lg font-bold">Pemantauan Kelas Hari Ini: <span class="text-blue-500">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span></h6>
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                            Sistem Aktif
                        </span>
                    </div>

                    <div class="overflow-x-auto p-4">
                        <table class="table mb-0 w-full border-collapse items-center align-top dark:border-white/40">
                            <thead class="bg-gray-50 dark:bg-slate-900 rounded-lg">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider dark:text-gray-300">Waktu</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider dark:text-gray-300">Mata Kuliah & Kelas</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider dark:text-gray-300">Dosen Pengampu</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider dark:text-gray-300">Titik Ruangan</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider dark:text-gray-300">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-white/10">
                                @forelse ($kelasHariIni ?? [] as $kelas)
                                    @php
                                        $now = \Carbon\Carbon::now();
                                        $jamMulai = \Carbon\Carbon::parse($kelas->jam_mulai);
                                        $jamSelesai = \Carbon\Carbon::parse($kelas->jam_selesai);
                                        
                                        if ($now->between($jamMulai, $jamSelesai)) {
                                            $status = 'Sedang Berjalan';
                                            $badge = 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300';
                                        } elseif ($now->gt($jamSelesai)) {
                                            $status = 'Selesai';
                                            $badge = 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400';
                                        } else {
                                            $status = 'Menunggu';
                                            $badge = 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300';
                                        }
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-800 dark:text-white">
                                            {{ \Carbon\Carbon::parse($kelas->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($kelas->jam_selesai)->format('H:i') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                            <div class="font-bold text-gray-800 dark:text-white">{{ $kelas->mataKuliah->nama_mk ?? '-' }}</div>
                                            <div class="text-xs text-blue-600 dark:text-blue-400 font-semibold mt-1">Kelas: {{ $kelas->nama_kelas }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $kelas->dosen->nama_dosen ?? '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-center font-medium text-gray-600 dark:text-gray-400">
                                            {{ $kelas->ruangan->nama_ruangan ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-md {{ $badge }}">
                                                {{ $status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-400 dark:text-gray-500 italic">
                                            <div class="flex flex-col items-center justify-center">
                                                <i class="ri-calendar-close-line text-4xl mb-2 opacity-50"></i>
                                                <p>Tidak ada jadwal kelas perkuliahan yang aktif untuk hari ini.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection