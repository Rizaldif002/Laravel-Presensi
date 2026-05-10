<x-app-layout>
    @section('title', 'Sesi Presensi')

    {{-- Header --}}
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <div class="flex items-center gap-3 mb-1">
                @if($sesi->status === 'aktif')
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                    <h2 class="text-xl font-extrabold text-gray-800">Sesi Presensi</h2>
                @else
                    <span class="w-3 h-3 rounded-full bg-gray-400 flex-shrink-0"></span>
                    <h2 class="text-xl font-extrabold text-gray-800">Sesi Presensi Selesai</h2>
                @endif
            </div>
            <p class="text-sm text-gray-500">
                <strong class="text-blue-600">{{ $sesi->jadwalPerkuliahan->kelasPerkuliahan->mataKuliah->nama_mk }}</strong>
                &bull; Kelas <strong>{{ $sesi->jadwalPerkuliahan->kelasPerkuliahan->nama_kelas }}</strong>
                &bull; {{ $sesi->jadwalPerkuliahan->hari }},
                  {{ substr($sesi->jadwalPerkuliahan->jam_mulai, 0, 5) }}–{{ substr($sesi->jadwalPerkuliahan->jam_selesai, 0, 5) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">
                Dibuka: {{ \Carbon\Carbon::parse($sesi->waktu_buka)->format('d M Y, H:i') }}
                @if($sesi->waktu_tutup)
                    &bull; Ditutup: {{ \Carbon\Carbon::parse($sesi->waktu_tutup)->format('d M Y, H:i') }}
                @endif
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('dosen.sesi.index') }}"
               class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all">
                ← Kembali
            </a>
            @if($sesi->status === 'aktif')
                <form action="{{ route('dosen.sesi.tutup', $sesi->id) }}" method="POST"
                      onsubmit="return confirm('Tutup sesi presensi? Mahasiswa tidak akan bisa absen lagi setelah ini.');">
                    @csrf
                    <button type="submit"
                            class="px-4 py-2 rounded-lg font-bold flex items-center gap-2 shadow-md text-sm bg-red-600 hover:bg-red-700 text-white transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Tutup Sesi
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Peta Geofencing --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-700 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    Radar Geofencing
                </h3>
                <span class="text-xs font-semibold px-3 py-1 bg-blue-100 text-blue-700 rounded-full">
                    {{ $sesi->jadwalPerkuliahan->ruangan->nama_ruangan ?? 'Ruangan' }}
                    &bull; Radius {{ $sesi->jadwalPerkuliahan->ruangan->radius_meter ?? 50 }} m
                </span>
            </div>
            <div id="map" class="w-full h-[420px] z-10 relative"></div>
        </div>

        {{-- Daftar hadir --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col">
            <div class="p-4 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-700 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Mahasiswa Hadir
                </h3>
                <span class="text-xs font-bold px-2 py-1 bg-emerald-100 text-emerald-700 rounded-lg">
                    {{ $sesi->presensis->count() }} orang
                </span>
            </div>

            <div class="flex-1 overflow-y-auto max-h-[420px] p-4 space-y-3 bg-gray-50/30">
                @forelse ($sesi->presensis as $absen)
                    <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm flex items-center gap-3">
                        <div class="w-11 h-11 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0 border border-gray-200">
                            @if($absen->foto_wajah)
                                <img src="{{ asset('storage/' . $absen->foto_wajah) }}"
                                     alt="Foto"
                                     class="w-full h-full object-cover"
                                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($absen->mahasiswa->nama_lengkap ?? 'M') }}&background=e0e7ff&color=4f46e5&size=64'">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-indigo-100 text-indigo-600 font-bold text-sm">
                                    {{ substr($absen->mahasiswa->nama_lengkap ?? 'M', 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-800 truncate">{{ $absen->mahasiswa->nama_lengkap ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $absen->mahasiswa->nim ?? '-' }}
                                &bull; {{ \Carbon\Carbon::parse($absen->waktu_absen)->format('H:i:s') }}
                            </p>
                        </div>
                        <span class="flex-shrink-0 text-xs font-semibold px-2 py-0.5 rounded-full
                            {{ $absen->status_kehadiran === 'Hadir' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $absen->status_kehadiran }}
                        </span>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center text-center opacity-50 py-16">
                        <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3A9 9 0 11.015 12.005 9 9 0 0112 21z"/>
                        </svg>
                        <p class="text-sm text-gray-500 font-medium">Menunggu mahasiswa absen...</p>
                        <p class="text-xs text-gray-400 mt-1">Data akan muncul secara real-time.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <style>
        #map { height: 420px; border-radius: 0; }
        .radar-pulse {
            border-radius: 50%;
            background: rgba(37, 99, 235, 0.15);
            border: 2px solid rgba(37, 99, 235, 0.4);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%   { transform: scale(0.1); opacity: 1; }
            100% { transform: scale(1.2); opacity: 0; }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var lat    = parseFloat("{{ $sesi->jadwalPerkuliahan->ruangan->latitude ?? -0.4948 }}") || -0.4948;
            var lng    = parseFloat("{{ $sesi->jadwalPerkuliahan->ruangan->longitude ?? 117.1436 }}") || 117.1436;
            var radius = parseInt("{{ $sesi->jadwalPerkuliahan->ruangan->radius_meter ?? 50 }}") || 50;

            var map = L.map('map').setView([lat, lng], 18);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            var geofenceCircle = L.circle([lat, lng], {
                color: '#1e40af', weight: 3, opacity: 1,
                fillColor: '#3b82f6', fillOpacity: 0.15,
                radius: radius
            }).addTo(map);

            var pulseIcon = L.divIcon({ className: 'radar-pulse', iconSize: [20, 20], iconAnchor: [10, 10] });
            L.marker([lat, lng], { icon: pulseIcon, interactive: false }).addTo(map);

            L.marker([lat, lng]).addTo(map)
                .bindPopup("<b>{{ $sesi->jadwalPerkuliahan->ruangan->nama_ruangan ?? 'Ruangan' }}</b><br>Radius: " + radius + " meter")
                .openPopup();

            map.fitBounds(geofenceCircle.getBounds());
            setTimeout(function () { map.invalidateSize(); }, 400);

            @foreach($sesi->presensis as $absen)
                @if($absen->latitude && $absen->longitude)
                    L.circleMarker(
                        [{{ $absen->latitude }}, {{ $absen->longitude }}],
                        { radius: 7, color: '#059669', fillColor: '#6ee7b7', fillOpacity: 0.8 }
                    ).addTo(map).bindTooltip(
                        "{{ $absen->mahasiswa->nama_lengkap ?? 'Mahasiswa' }} ({{ $absen->mahasiswa->nim ?? '-' }})"
                    );
                @endif
            @endforeach
        });
    </script>
</x-app-layout>
