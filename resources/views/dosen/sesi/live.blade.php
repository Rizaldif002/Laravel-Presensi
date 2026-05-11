<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div class="flex items-center gap-3">
                <span class="relative flex h-3 w-3 flex-shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                <div>
                    <h2 class="text-lg font-bold text-gray-800 leading-tight">
                        {{ $sesi->jadwalPerkuliahan->kelasPerkuliahan->mataKuliah->nama_mk ?? 'Sesi Live' }}
                        <span class="ml-2 text-xs font-normal bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full">SESI AKTIF</span>
                    </h2>
                    <p class="text-xs text-gray-500">
                        Kelas {{ $sesi->jadwalPerkuliahan->kelasPerkuliahan->nama_kelas ?? '-' }} &bull;
                        {{ $sesi->jadwalPerkuliahan->ruangan->nama_ruangan ?? '-' }} &bull;
                        Dibuka {{ \Carbon\Carbon::parse($sesi->waktu_buka)->format('H:i') }} WITA
                    </p>
                </div>
            </div>
            <button type="button" onclick="konfirmasiTutup()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Tutup Sesi
            </button>
            <form id="form-tutup" action="{{ route('dosen.sesi.tutup', $sesi) }}" method="POST" class="hidden">@csrf</form>
        </div>
    </x-slot>

    <div class="px-5 pt-5">

        {{-- Counter bar --}}
        <div class="flex flex-wrap items-center gap-4 mb-5">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-3 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Mahasiswa Hadir</p>
                    <p id="counter-hadir" class="text-2xl font-extrabold text-emerald-600">{{ $sesi->presensis->count() }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-3 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Ruangan & Radius</p>
                    <p class="text-sm font-bold text-gray-700">
                        {{ $sesi->jadwalPerkuliahan->ruangan->nama_ruangan ?? '-' }}
                        <span class="text-xs font-normal text-gray-400">({{ $sesi->jadwalPerkuliahan->ruangan->radius_meter ?? '-' }} m)</span>
                    </p>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-3 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg {{ $sesi->is_gps_enabled ? 'bg-blue-100' : 'bg-amber-100' }} flex items-center justify-center">
                    <svg class="w-5 h-5 {{ $sesi->is_gps_enabled ? 'text-blue-600' : 'text-amber-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Validasi GPS</p>
                    <p class="text-sm font-bold {{ $sesi->is_gps_enabled ? 'text-blue-700' : 'text-amber-700' }}">
                        {{ $sesi->is_gps_enabled ? 'Aktif' : 'Nonaktif' }}
                    </p>
                </div>
            </div>
            <div class="ml-auto flex items-center gap-2 text-xs text-gray-400">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                </span>
                Auto-refresh tiap 10 detik &bull; <span id="last-update">baru saja</span>
            </div>
        </div>

        {{-- Tabel kehadiran --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="text-sm font-bold text-gray-700">Daftar Kehadiran Real-time</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama Mahasiswa</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">NIM</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Waktu Presensi</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">GPS</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Foto</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody id="hadir-tbody" class="divide-y divide-gray-100 bg-white">
                        @forelse($sesi->presensis as $i => $p)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-800">{{ $p->mahasiswa->nama_lengkap ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $p->mahasiswa->nim ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-sm font-semibold text-gray-700">{{ \Carbon\Carbon::parse($p->waktu_absen)->format('H:i:s') }}</td>
                            <td class="px-4 py-3 text-center text-xs">–</td>
                            <td class="px-4 py-3 text-center">
                                @if($p->foto_wajah)
                                    <img src="{{ asset('storage/' . $p->foto_wajah) }}" class="w-8 h-8 rounded-full object-cover mx-auto border border-gray-200" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($p->mahasiswa->nama_lengkap ?? 'M') }}&size=32&background=e0e7ff&color=4f46e5'">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center mx-auto text-xs font-bold text-indigo-600">{{ substr($p->mahasiswa->nama_lengkap ?? 'M', 0, 1) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $p->status_kehadiran === 'Hadir' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $p->status_kehadiran }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr id="row-kosong">
                            <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-400">
                                <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3A9 9 0 110 12a9 9 0 0118 0z"/></svg>
                                Menunggu mahasiswa absen...
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        const liveDataUrl = '{{ route("dosen.sesi.live-data", $sesi) }}';
        const showUrl     = '{{ route("dosen.sesi.show", $sesi) }}';

        function buildRow(p, no) {
            const fotoHtml = p.foto
                ? `<img src="${p.foto}" class="w-8 h-8 rounded-full object-cover mx-auto border border-gray-200" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(p.nama)}&size=32&background=e0e7ff&color=4f46e5'">`
                : `<div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center mx-auto text-xs font-bold text-indigo-600">${p.nama.charAt(0)}</div>`;

            let gpsHtml = '<span class="text-gray-400">–</span>';
            if (p.jarak_meter !== null) {
                if (p.dalam_radius) {
                    gpsHtml = `<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">✓ ${p.jarak_meter}m</span>`;
                } else {
                    gpsHtml = `<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-600">✗ ${p.jarak_meter}m</span>`;
                }
            }

            const statusColor = p.status === 'Hadir' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700';

            return `<tr class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3 text-sm text-gray-500">${no}</td>
                <td class="px-4 py-3 text-sm font-semibold text-gray-800">${p.nama}</td>
                <td class="px-4 py-3 text-sm text-gray-600">${p.nim}</td>
                <td class="px-4 py-3 text-center text-sm font-semibold text-gray-700">${p.waktu}</td>
                <td class="px-4 py-3 text-center text-xs">${gpsHtml}</td>
                <td class="px-4 py-3 text-center">${fotoHtml}</td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold ${statusColor}">${p.status}</span>
                </td>
            </tr>`;
        }

        function refreshData() {
            fetch(liveDataUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.json())
                .then(data => {
                    if (data.status !== 'aktif') {
                        window.location.href = showUrl;
                        return;
                    }

                    document.getElementById('counter-hadir').textContent = data.count;

                    const tbody = document.getElementById('hadir-tbody');
                    if (data.presensis.length === 0) {
                        tbody.innerHTML = `<tr id="row-kosong"><td colspan="7" class="px-4 py-12 text-center text-sm text-gray-400">Menunggu mahasiswa absen...</td></tr>`;
                    } else {
                        tbody.innerHTML = data.presensis.map((p, i) => buildRow(p, i + 1)).join('');
                    }

                    const now = new Date();
                    document.getElementById('last-update').textContent =
                        now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                })
                .catch(() => {});
        }

        setInterval(refreshData, 10000);

        function konfirmasiTutup() {
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
                    document.getElementById('form-tutup').submit();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session("success") }}', showConfirmButton: false, timer: 3000, customClass: { popup: 'rounded-xl' } });
            @endif
        });
    </script>

</x-app-layout>
