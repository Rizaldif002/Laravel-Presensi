<x-app-layout>
    <div class="bg-white border-b border-gray-200 px-8 py-4 mb-6 shadow-sm">
        <h2 class="text-xl font-bold text-gray-800">
            Halaman Utama 
        </h2>
        <p class="text-sm text-gray-500">Pantau statistik data master dan jadwal kelas hari ini secara real-time.</p>
    </div>

    <div class="px-8 w-full mx-auto space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <a href="{{ route('admin.dosen') }}" class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4 shadow-sm hover:shadow-md hover:border-blue-300 transition-all group">
                <div class="p-4 rounded-lg bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Dosen</p>
                    <h4 class="text-2xl font-extrabold text-gray-800">{{ $totalDosen ?? 0 }}</h4>
                </div>
            </a>

            <a href="{{ route('admin.mata-kuliah') }}" class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4 shadow-sm hover:shadow-md hover:border-purple-300 transition-all group">
                <div class="p-4 rounded-lg bg-purple-50 text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Mata Kuliah</p>
                    <h4 class="text-2xl font-extrabold text-gray-800">{{ $totalMatkul ?? 0 }}</h4>
                </div>
            </a>

            <a href="{{ route('admin.ruangan') }}" class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4 shadow-sm hover:shadow-md hover:border-green-300 transition-all group">
                <div class="p-4 rounded-lg bg-green-50 text-green-600 group-hover:bg-green-600 group-hover:text-white transition-colors">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Titik Ruangan</p>
                    <h4 class="text-2xl font-extrabold text-gray-800">{{ $totalRuangan ?? 0 }}</h4>
                </div>
            </a>

            <a href="{{ route('admin.kelas.index') ?? '#' }}" class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4 shadow-sm hover:shadow-md hover:border-orange-300 transition-all group">
                <div class="p-4 rounded-lg bg-orange-50 text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-colors">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Jadwal Aktif</p>
                    <h4 class="text-2xl font-extrabold text-gray-800">{{ $totalKelas ?? 0 }}</h4>
                </div>
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-10">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800"> Kelas Hari Ini <span class="text-blue-600">({{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }})</span></h3>
                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    Sistem Aktif
                </span>
            </div>
            
            <div class="p-0 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-white">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Waktu</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Mata Kuliah & Kelas</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Dosen Pengampu</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Ruangan</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($jadwalHariIni ?? [] as $jadwal)
                            @php
                                $now = \Carbon\Carbon::now();
                                $jamMulai = \Carbon\Carbon::parse($jadwal->jam_mulai);
                                $jamSelesai = \Carbon\Carbon::parse($jadwal->jam_selesai);
                                
                                if ($now->between($jamMulai, $jamSelesai)) {
                                    $status = 'Sedang Berjalan';
                                    $badge = 'bg-blue-100 text-blue-700';
                                } elseif ($now->gt($jamSelesai)) {
                                    $status = 'Selesai';
                                    $badge = 'bg-gray-100 text-gray-600';
                                } else {
                                    $status = 'Menunggu';
                                    $badge = 'bg-yellow-100 text-yellow-700';
                                }
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">
                                    {{ $jamMulai->format('H:i') }} - {{ $jamSelesai->format('H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{-- Akses data lewat relasi kelasPerkuliahan --}}
                                    <div class="font-bold text-gray-800">{{ $jadwal->kelasPerkuliahan->mataKuliah->nama_mk ?? '-' }}</div>
                                    <div class="text-xs text-blue-600 font-semibold mt-1">Kelas: {{ $jadwal->kelasPerkuliahan->nama_kelas }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $jadwal->kelasPerkuliahan->dosen->nama_dosen ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-medium text-gray-600">
                                    {{ $jadwal->ruangan->nama_ruangan ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-md {{ $badge }}">
                                        {{ $status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-400 italic">
                                    Tidak ada jadwal kelas perkuliahan yang aktif untuk hari ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>