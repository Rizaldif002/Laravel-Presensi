<x-app-layout>
    <x-slot name="header">
        Dashboard
    </x-slot>
    @section('title', 'Dashboard Admin')
        
        {{-- SECTION 1: TOP STATS GRID (7 Cards) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-10">
            
            <a href="{{ route('admin.mahasiswa') ?? '#' }}" class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4 shadow-sm hover:shadow-md hover:border-blue-300 transition-all group">
                <div class="p-4 rounded-lg bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Data Mahasiswa</p>
                    <h4 class="text-2xl font-extrabold text-gray-800">{{ $totalMahasiswa ?? 0 }}</h4>
                </div>
            </a>

            <a href="{{ route('admin.dosen') }}" class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4 shadow-sm hover:shadow-md hover:border-indigo-300 transition-all group">
                <div class="p-4 rounded-lg bg-indigo-50 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Dosen</p>
                    <h4 class="text-2xl font-extrabold text-gray-800">{{ $totalDosen ?? 0 }}</h4>
                </div>
            </a>

            <a href="{{ route('admin.ruangan') }}" class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4 shadow-sm hover:shadow-md hover:border-green-300 transition-all group">
                <div class="p-4 rounded-lg bg-green-50 text-green-600 group-hover:bg-green-600 group-hover:text-white transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Titik Ruangan</p>
                    <h4 class="text-2xl font-extrabold text-gray-800">{{ $totalRuangan ?? 0 }}</h4>
                </div>
            </a>

            <a href="{{ route('admin.kelas.index') ?? '#' }}" class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4 shadow-sm hover:shadow-md hover:border-purple-300 transition-all group">
                <div class="p-4 rounded-lg bg-purple-50 text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Data Kelas</p>
                    <h4 class="text-2xl font-extrabold text-gray-800">{{ $totalKelas ?? 0 }}</h4>
                </div>
            </a>

            <a href="{{ route('admin.tahun-ajaran') ?? '#' }}" class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4 shadow-sm hover:shadow-md hover:border-teal-300 transition-all group">
                <div class="p-4 rounded-lg bg-teal-50 text-teal-600 group-hover:bg-teal-600 group-hover:text-white transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tahun Ajaran</p>
                    <h4 class="text-2xl font-extrabold text-gray-800">{{ $totalTahunAjaran ?? 0 }}</h4>
                </div>
            </a>

            <a href="{{ route('admin.mata-kuliah') }}" class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4 shadow-sm hover:shadow-md hover:border-pink-300 transition-all group">
                <div class="p-4 rounded-lg bg-pink-50 text-pink-600 group-hover:bg-pink-600 group-hover:text-white transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Mata Kuliah</p>
                    <h4 class="text-2xl font-extrabold text-gray-800">{{ $totalMatkul ?? 0 }}</h4>
                </div>
            </a>

            <a href="{{ route('admin.jadwal.index') ?? '#' }}" class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4 shadow-sm hover:shadow-md hover:border-orange-300 transition-all group sm:col-span-2 xl:col-span-1">
                <div class="p-4 rounded-lg bg-orange-50 text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Jadwal Aktif</p>
                    <h4 class="text-2xl font-extrabold text-gray-800">{{ $jadwalAktif ?? 0 }}</h4>
                </div>
            </a>
        </div>

        {{-- SECTION 2: BOTTOM SPLIT (Kalender & Tabel) --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-10">
            
            {{-- ===== Left Column: Kalender Interaktif ===== --}}
            <div class="xl:col-span-1 bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col">

                {{-- Header bulan + navigasi --}}
                <div class="flex items-center justify-between mb-4">
                    <span id="cal-month-label" class="text-base font-bold text-gray-800"></span>
                    <div class="flex items-center gap-1">
                        <button id="cal-prev"
                                class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 hover:text-gray-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button id="cal-next"
                                class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 hover:text-gray-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Grid hari --}}
                <div class="grid grid-cols-7 mb-2">
                    @foreach (['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $hari)
                        <div class="text-center text-xs font-semibold text-gray-400 py-1">{{ $hari }}</div>
                    @endforeach
                </div>

                {{-- Grid tanggal --}}
                <div id="cal-grid" class="grid grid-cols-7 gap-y-1 flex-1"></div>
            </div>
            {{-- ===== End Kalender ===== --}}

            {{-- Right Column: Tabel Kelas Hari Ini --}}
            <div class="xl:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">
                        Kelas Hari Ini
                        <span class="text-blue-600">({{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }})</span>
                    </h3>
                    <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full flex items-center gap-1.5 shrink-0">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        Sistem Aktif
                    </span>
                </div>
                
                <div class="p-0 overflow-x-auto flex-1">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Waktu</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kelas Perkuliahan</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Dosen Pengampu</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Ruangan</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($jadwalHariIni ?? [] as $jadwal)
                                @php
                                    $now        = \Carbon\Carbon::now();
                                    $jamMulai   = \Carbon\Carbon::parse($jadwal->jam_mulai);
                                    $jamSelesai = \Carbon\Carbon::parse($jadwal->jam_selesai);
                                    
                                    if ($now->between($jamMulai, $jamSelesai)) {
                                        $status = 'Sedang Berjalan';
                                        $badge  = 'bg-blue-100 text-blue-700';
                                    } elseif ($now->gt($jamSelesai)) {
                                        $status = 'Selesai';
                                        $badge  = 'bg-gray-100 text-gray-600';
                                    } else {
                                        $status = 'Menunggu';
                                        $badge  = 'bg-yellow-100 text-yellow-700';
                                    }
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">
                                        {{ $jamMulai->format('H:i') }} - {{ $jamSelesai->format('H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
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

    {{-- ===== Script Kalender ===== --}}
    <script>
    (function () {
        // Nama bulan dalam Bahasa Indonesia
        const BULAN = [
            'Januari','Februari','Maret','April','Mei','Juni',
            'Juli','Agustus','September','Oktober','November','Desember'
        ];

        // State kalender — mulai dari bulan & tahun sekarang
        const today  = new Date();
        let curYear  = today.getFullYear();
        let curMonth = today.getMonth(); // 0-based

        const labelEl = document.getElementById('cal-month-label');
        const gridEl  = document.getElementById('cal-grid');
        const prevBtn = document.getElementById('cal-prev');
        const nextBtn = document.getElementById('cal-next');

        /**
         * Render kalender untuk bulan & tahun yang aktif
         */
        function renderCalendar() {
            // Update label bulan & tahun
            labelEl.textContent = BULAN[curMonth] + ', ' + curYear;

            // Kosongkan grid sebelum render ulang
            gridEl.innerHTML = '';

            // Hari pertama bulan ini (0 = Minggu)
            const firstDay   = new Date(curYear, curMonth, 1).getDay();
            // Jumlah hari di bulan ini
            const totalDays  = new Date(curYear, curMonth + 1, 0).getDate();
            // Jumlah hari di bulan sebelumnya
            const prevTotal  = new Date(curYear, curMonth, 0).getDate();

            // ── Isi tanggal bulan sebelumnya (abu-abu) ──
            for (let i = firstDay - 1; i >= 0; i--) {
                gridEl.appendChild(createCell(prevTotal - i, false, false, false, true));
            }

            // ── Isi tanggal bulan ini ──
            for (let d = 1; d <= totalDays; d++) {
                const isToday   = (d === today.getDate() && curMonth === today.getMonth() && curYear === today.getFullYear());
                const isSunday  = (new Date(curYear, curMonth, d).getDay() === 0);
                gridEl.appendChild(createCell(d, true, isToday, isSunday, false));
            }

            // ── Isi tanggal bulan berikutnya (abu-abu) ──
            const filled    = firstDay + totalDays;
            const remaining = filled % 7 === 0 ? 0 : 7 - (filled % 7);
            for (let d = 1; d <= remaining; d++) {
                gridEl.appendChild(createCell(d, false, false, false, true));
            }
        }

        /**
         * Buat satu sel tanggal
         * @param {number}  num        - angka tanggal
         * @param {boolean} current    - apakah bulan ini
         * @param {boolean} isToday    - apakah hari ini
         * @param {boolean} isSunday   - apakah hari Minggu
         * @param {boolean} dimmed     - bulan lain (abu-abu)
         */
        function createCell(num, current, isToday, isSunday, dimmed) {
            const cell = document.createElement('div');
            cell.textContent = num;

            // Base style — semua sel
            cell.className = 'flex items-center justify-center text-sm h-9 w-full rounded-lg font-medium select-none';

            if (isToday) {
                // Hari ini: background biru, teks putih
                cell.classList.add('bg-blue-600', 'text-white', 'font-bold', 'shadow-sm');
            } else if (dimmed) {
                // Tanggal bulan lain: abu-abu muda
                cell.classList.add('text-gray-300', 'cursor-default');
            } else if (isSunday) {
                // Hari Minggu: teks merah
                cell.classList.add('text-red-500', 'hover:bg-red-50', 'cursor-pointer', 'transition-colors');
            } else {
                // Tanggal biasa
                cell.classList.add('text-gray-700', 'hover:bg-blue-50', 'hover:text-blue-700', 'cursor-pointer', 'transition-colors');
            }

            return cell;
        }

        // ── Navigasi prev/next ──
        prevBtn.addEventListener('click', function () {
            curMonth--;
            if (curMonth < 0) { curMonth = 11; curYear--; }
            renderCalendar();
        });

        nextBtn.addEventListener('click', function () {
            curMonth++;
            if (curMonth > 11) { curMonth = 0; curYear++; }
            renderCalendar();
        });

        // Render pertama kali saat halaman dimuat
        renderCalendar();
    })();
    </script>

</x-app-layout>