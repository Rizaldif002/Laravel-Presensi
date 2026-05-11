<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Kelola Sesi Presensi</h2>
    </x-slot>

    <div class="px-5 pt-5" x-data="{ modalBuka: false, gpsEnabled: true }">

        {{-- Flash --}}
        @if(session('success'))
            <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm font-medium">{{ session('error') }}</div>
        @endif
        @if(session('info'))
            <div class="mb-4 p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl text-sm font-medium">{{ session('info') }}</div>
        @endif

        {{-- Header Aksi (di luar card) --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
            <div>
                <p class="text-sm text-gray-500">Selamat datang, <span class="font-semibold text-blue-600">{{ $dosen->nama_dosen }}</span></p>
                <p class="text-xs text-gray-400">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
            </div>
            <button @click="modalBuka = true"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg text-sm shadow-sm transition-all whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buka Sesi Baru
            </button>
        </div>

        {{-- Stat Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Sesi Aktif Saat Ini</p>
                <p class="text-3xl font-extrabold text-emerald-600">{{ $statAktif }}</p>
                <p class="text-xs text-gray-400 mt-1">sesi berjalan</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Total Sesi Bulan Ini</p>
                <p class="text-3xl font-extrabold text-blue-600">{{ $statSesiBulanIni }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ now()->isoFormat('MMMM Y') }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Total Hadir Hari Ini</p>
                <p class="text-3xl font-extrabold text-indigo-600">{{ $statHadirHariIni }}</p>
                <p class="text-xs text-gray-400 mt-1">mahasiswa hadir</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Kelas Diampu</p>
                <p class="text-3xl font-extrabold text-gray-700">{{ $statKelasAmpuh }}</p>
                <p class="text-xs text-gray-400 mt-1">kelas aktif</p>
            </div>
        </div>

        {{-- Sesi Sedang Aktif --}}
        @if($sesiAktifs->count())
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-3">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                <h3 class="text-sm font-bold text-emerald-700 uppercase tracking-wide">Sesi Sedang Aktif</h3>
            </div>
            <div class="overflow-x-auto bg-white rounded-xl border border-emerald-200 shadow-sm">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-emerald-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Mata Kuliah & Kelas</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Pertemuan</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Ruangan</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Dibuka Pukul</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">GPS</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Hadir</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($sesiAktifs as $i => $sesi)
                        <tr class="bg-emerald-50/50 hover:bg-emerald-50 transition-colors">
                            <td class="px-4 py-4 text-sm text-gray-500">{{ $i + 1 }}</td>
                            <td class="px-4 py-4">
                                <p class="text-sm font-bold text-gray-800">{{ $sesi->jadwalPerkuliahan->kelasPerkuliahan->mataKuliah->nama_mk ?? '-' }}</p>
                                <p class="text-xs text-gray-500">Kelas {{ $sesi->jadwalPerkuliahan->kelasPerkuliahan->nama_kelas ?? '-' }} &bull; {{ $sesi->jadwalPerkuliahan->hari }}</p>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $sesi->nama_pertemuan ?? '-' }}</td>
                            <td class="px-4 py-4 text-sm text-gray-600">{{ $sesi->jadwalPerkuliahan->ruangan->nama_ruangan ?? '-' }}</td>
                            <td class="px-4 py-4 text-center text-sm font-semibold text-gray-700 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($sesi->waktu_buka)->format('H:i') }} WITA
                            </td>
                            <td class="px-4 py-4 text-center">
                                @if($sesi->is_gps_enabled)
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Aktif</span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700" title="{{ $sesi->gps_reason }}">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center text-sm font-bold text-gray-700">
                                {{ $sesi->presensis->count() }} mhs
                            </td>
                            <td class="px-4 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('dosen.sesi.live', $sesi) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-3-9a9 9 0 110 18A9 9 0 019 3z"/></svg>
                                        Live
                                    </a>
                                    <button type="button"
                                        onclick="konfirmasiTutup({{ $sesi->id }})"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Tutup
                                    </button>
                                    <form id="form-tutup-{{ $sesi->id }}" action="{{ route('dosen.sesi.tutup', $sesi) }}" method="POST" class="hidden">
                                        @csrf
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Riwayat Sesi --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3 bg-gray-50">
                <h3 class="text-sm font-bold text-gray-700">Riwayat Sesi</h3>
                <form action="{{ route('dosen.sesi.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                    <select name="filter_kelas" onchange="this.form.submit()" class="border border-gray-300 rounded-lg py-1.5 pl-3 pr-7 text-xs text-gray-700 bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Kelas</option>
                        @foreach($kelases as $k)
                            <option value="{{ $k->id }}" {{ request('filter_kelas') == $k->id ? 'selected' : '' }}>
                                {{ $k->mataKuliah->nama_mk ?? '-' }} - {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                    <select name="filter_bulan" onchange="this.form.submit()" class="border border-gray-300 rounded-lg py-1.5 pl-3 pr-7 text-xs text-gray-700 bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Bulan</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('filter_bulan') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->isoFormat('MMMM') }}
                            </option>
                        @endforeach
                    </select>
                    @if(request()->hasAny(['filter_kelas','filter_bulan']))
                        <a href="{{ route('dosen.sesi.index') }}" class="text-xs text-red-500 hover:text-red-700 font-medium">Reset</a>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Mata Kuliah & Kelas</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Pertemuan</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Waktu Buka – Tutup</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">GPS</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Hadir</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($riwayat as $i => $sesi)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4 text-sm text-gray-500">{{ $riwayat->firstItem() + $i }}</td>
                            <td class="px-4 py-4">
                                <p class="text-sm font-bold text-gray-800">{{ $sesi->jadwalPerkuliahan->kelasPerkuliahan->mataKuliah->nama_mk ?? '-' }}</p>
                                <p class="text-xs text-gray-500">Kelas {{ $sesi->jadwalPerkuliahan->kelasPerkuliahan->nama_kelas ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $sesi->nama_pertemuan ?? '-' }}</td>
                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                <p class="text-xs font-semibold text-gray-700">{{ \Carbon\Carbon::parse($sesi->waktu_buka)->format('d M Y, H:i') }}</p>
                                @if($sesi->waktu_tutup)
                                    <p class="text-xs text-gray-400">s/d {{ \Carbon\Carbon::parse($sesi->waktu_tutup)->format('H:i') }}</p>
                                @else
                                    <p class="text-xs text-gray-400">–</p>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                @if($sesi->is_gps_enabled)
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Aktif</span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center text-sm font-bold text-gray-700">{{ $sesi->presensis->count() }}</td>
                            <td class="px-4 py-4 text-center">
                                @if($sesi->status === 'aktif')
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Aktif</span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Selesai</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                <a href="{{ route('dosen.sesi.show', $sesi) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-medium rounded-full shadow-sm transition-all">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-sm text-gray-400">Belum ada riwayat sesi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 px-4 pb-4 flex justify-end">
                {{ $riwayat->links() }}
            </div>
        </div>

        {{-- Modal Buka Sesi --}}
        <div x-show="modalBuka" x-cloak
             class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center backdrop-blur-sm overflow-y-auto py-10">
            <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 overflow-hidden" @click.stop>
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-800">Buka Sesi Presensi Baru</h3>
                    <button type="button" @click="modalBuka = false" class="text-gray-400 hover:text-red-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('dosen.sesi.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Jadwal Perkuliahan <span class="text-red-500">*</span></label>
                        <select name="jadwal_perkuliahan_id" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm bg-gray-50">
                            <option value="">-- Pilih Jadwal --</option>
                            @foreach($jadwals as $j)
                                <option value="{{ $j->id }}">
                                    {{ $j->kelasPerkuliahan->mataKuliah->nama_mk ?? '-' }} –
                                    Kelas {{ $j->kelasPerkuliahan->nama_kelas ?? '' }} |
                                    {{ $j->hari }}, {{ substr($j->jam_mulai, 0, 5) }}–{{ substr($j->jam_selesai, 0, 5) }}
                                    ({{ $j->ruangan->nama_ruangan ?? 'Ruangan?' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Pertemuan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_pertemuan" required maxlength="100"
                               placeholder="Contoh: Pertemuan 1 / UTS / Praktikum 3"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-sm font-semibold text-gray-700">Aktifkan Validasi GPS</label>
                            <button type="button"
                                @click="gpsEnabled = !gpsEnabled"
                                :class="gpsEnabled ? 'bg-blue-600' : 'bg-gray-300'"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none">
                                <span :class="gpsEnabled ? 'translate-x-6' : 'translate-x-1'"
                                      class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow"></span>
                            </button>
                        </div>
                        <input type="hidden" name="is_gps_enabled" :value="gpsEnabled ? '1' : '0'">
                        <p class="text-xs text-gray-400" x-text="gpsEnabled ? 'GPS aktif — mahasiswa harus berada dalam radius ruangan.' : 'GPS nonaktif — presensi tanpa validasi lokasi.'"></p>
                    </div>

                    <div x-show="!gpsEnabled" x-transition>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Alasan GPS Dinonaktifkan <span class="text-red-500">*</span></label>
                        <textarea name="gps_reason" rows="2" maxlength="500"
                                  :required="!gpsEnabled"
                                  placeholder="Contoh: Koneksi GPS bermasalah di ruangan ini"
                                  class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm resize-none"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="modalBuka = false" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-100">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 shadow-sm">Buka Sesi</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function konfirmasiTutup(sesiId) {
            Swal.fire({
                title: 'Tutup Sesi?',
                text: 'Mahasiswa tidak dapat absen lagi setelah sesi ditutup.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Tutup Sesi',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-xl' }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-tutup-' + sesiId).submit();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session("success") }}', showConfirmButton: false, timer: 3000, customClass: { popup: 'rounded-xl' } });
            @endif
            @if(session('error'))
                Swal.fire({ icon: 'error', title: 'Gagal!', text: '{{ session("error") }}', confirmButtonColor: '#2563EB', customClass: { popup: 'rounded-xl' } });
            @endif
            @if($errors->any())
                Swal.fire({ icon: 'error', title: 'Perhatian!', html: `<ul class="text-left text-sm text-red-600 list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>`, confirmButtonColor: '#2563EB', customClass: { popup: 'rounded-xl' } });
            @endif
        });
    </script>

</x-app-layout>
