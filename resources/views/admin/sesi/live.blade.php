<x-app-layout>
    @section('title', 'Pemantauan Radar Presensi')

    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="relative flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                <h2 class="text-xl font-extrabold text-gray-800">Sesi Presensi Aktif</h2>
            </div>
            <p class="text-sm text-gray-500">Mata Kuliah: <strong class="text-blue-600">{{ $sesi->jadwalPerkuliahan->kelasPerkuliahan->mataKuliah->nama_mk }}</strong> | Kelas: <strong>{{ $sesi->jadwalPerkuliahan->kelasPerkuliahan->nama_kelas }}</strong></p>
        </div>
        
        <form action="{{ route('admin.sesi.tutup', $sesi->id) }}" method="POST" onsubmit="return confirm('Tutup sesi presensi? Mahasiswa tidak akan bisa absen lagi setelah ini.');">
            @csrf
            <button type="submit" style="background-color: #ef4444; color: #ffffff;" class="px-4 py-2 rounded-lg font-bold flex items-center gap-2 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all border border-red-600 text-sm">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                TUTUP SESI PRESENSI   
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-700 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        Radar Geofencing Ruangan
                    </h3>
                    <span class="text-xs font-semibold px-3 py-1 bg-blue-100 text-blue-700 rounded-full">
                        Radius: {{ $sesi->jadwalPerkuliahan->ruangan->radius_meter ?? 50 }} Meter
                    </span>
                </div>
                
                <div id="map" class="w-full h-[400px] z-10 relative"></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col h-full">
            <div class="p-4 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-700 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Mahasiswa Hadir
                </h3>
                <span class="text-xs font-bold px-2 py-1 bg-emerald-100 text-emerald-700 rounded-lg" id="totalHadir">
                    {{ isset($sesi->presensis) && $sesi->presensis ? $sesi->presensis->count() : 0 }} Orang
                </span>
            </div>
            
            <div class="p-4 flex-1 overflow-y-auto max-h-[400px] bg-gray-50/30" id="liveFeedContainer">
                @php
                    $presensis = $sesi->presensis ?? collect();
                @endphp

                @forelse ($presensis as $absen)
                    <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm flex items-center gap-3 mb-3 animate-fade-in-up">
                        <div class="w-12 h-12 rounded-lg bg-gray-200 overflow-hidden flex-shrink-0 border border-gray-200">
                            <img src="{{ asset('storage/' . $absen->foto_wajah) }}" alt="Foto" class="w-full h-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($absen->mahasiswa->nama) }}&background=random'">
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-800">{{ $absen->mahasiswa->nama ?? 'Unknown' }}</h4>
                            <p class="text-[10px] text-gray-500">NIM: {{ $absen->mahasiswa->nim ?? '-' }} | Jam: {{ \Carbon\Carbon::parse($absen->waktu_absen)->format('H:i:s') }}</p>
                        </div>
                        <div class="ml-auto">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center text-center opacity-50 py-10">
                        <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3A9 9 0 11.015 12.005 9 9 0 0112 21z"/></svg>
                        <p class="text-sm text-gray-500 font-medium">Menunggu mahasiswa absensi...</p>
                        <p class="text-xs text-gray-400 mt-1">Foto wajah akan muncul di sini.</p>
                    </div>
                @endforelse

            </div>
        </div>
    </div>

    <style>
        /* Mengatur ukuran container peta agar pasti muncul */
        #map { height: 500px; width: 100%; border-radius: 15px; }

        /* Efek Animasi Radar Pulse */
        .radar-pulse {
            background: rgba(37, 99, 235, 0.4);
            border-radius: 50%;
            box-shadow: 0 0 0 rgba(37, 99, 235, 0.4);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 20px rgba(37, 99, 235, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(37, 99, 235, 0); }
        }
    </style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil lokasi dan radius ruangan (lokasi absensi/center radar)
        var lat = parseFloat("{{ $sesi->jadwalPerkuliahan->ruangan->latitude }}") || -0.4948;
        var lng = parseFloat("{{ $sesi->jadwalPerkuliahan->ruangan->longitude }}") || 117.1436;
        var radius = parseInt("{{ $sesi->jadwalPerkuliahan->ruangan->radius_meter }}") || 50;

        // Inisialisasi peta dan fit ke bounds lokasi absensi
        var map = L.map('map').setView([lat, lng], 18);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Lingkaran sesuai lokasi absensi
        var geofenceCircle = L.circle([lat, lng], {
            color: '#1e40af',      // Biru Gelap (Border)
            weight: 4,
            opacity: 1,
            fillColor: '#3b82f6',
            fillOpacity: 0.2,
            radius: radius
        }).addTo(map);

        // Radar Pulse hanya di lokasi absensi
        var pulseIcon = L.divIcon({
            className: 'radar-pulse',
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });
        L.marker([lat, lng], { icon: pulseIcon, interactive: false }).addTo(map);

        // Marker lokasi absensi utama
        var mainMarker = L.marker([lat, lng]).addTo(map)
            .bindPopup("<b>Lokasi Presensi:</b><br>{{ $sesi->jadwalPerkuliahan->ruangan->nama_ruangan }}<br>Radius: " + radius + " meter")
            .openPopup();

        // Batas peta langsung fit ke area radar absensi
        map.fitBounds(geofenceCircle.getBounds());

        // Perbaikan jika peta muncul abu-abu sebagian
        setTimeout(function(){ map.invalidateSize()}, 400);


        @foreach($sesi->presensis ?? [] as $presensi)
           @if($presensi->latitude && $presensi->longitude)
                L.circleMarker([{{ $presensi->latitude }}, {{ $presensi->longitude }}], {radius: 8, color:'#22c55e', fillColor:'#6ee7b7', fillOpacity:0.7})
                    .addTo(map).bindTooltip("{{ $presensi->mahasiswa->nama }} (NIM: {{ $presensi->mahasiswa->nim }})");
           @endif
        @endforeach
    });
</script>
</x-app-layout>