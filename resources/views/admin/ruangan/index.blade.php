<x-app-layout>
    <x-slot name="header">
        Data Ruangan (Geofencing)
    </x-slot>

    <div>
        <div class="mb-4 flex flex-col sm:flex-row justify-between items-center gap-4">
            <h3 class="text-lg font-semibold text-gray-700">Titik Koordinat Ruang Kelas</h3>
            <button onclick="openMapModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center gap-2 text-sm shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Tambah Ruangan & Titik
            </button>
        </div>

        <div class="overflow-x-auto bg-white rounded-xl border border-gray-200 shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Ruangan</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Latitude</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Longitude</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Radius Absen</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($ruangans as $index => $ruang)
                        <tr class="odd:bg-white even:bg-gray-50 hover:bg-blue-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $ruang->nama_ruangan }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center font-mono">{{ $ruang->latitude }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center font-mono">{{ $ruang->longitude }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                <span class="px-2 py-1 bg-green-100 text-green-700 font-semibold rounded-md text-xs">
                                    {{ $ruang->radius_meter }} M
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button"
                                        onclick="openEditModal('{{ $ruang->id }}', '{{ addslashes($ruang->nama_ruangan) }}', '{{ $ruang->latitude }}', '{{ $ruang->longitude }}', '{{ $ruang->radius_meter }}')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 rounded-full text-xs font-medium text-gray-700 hover:bg-gray-100 shadow-sm transition-all">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.ruangan.destroy', $ruang->id) }}" method="POST" onsubmit="return confirm('Hapus titik ruangan ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-red-200 rounded-full text-xs font-medium text-red-600 hover:bg-red-50 shadow-sm transition-all">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-400">Tidak ada data ruangan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="modalTambahMap" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-4xl mx-4 overflow-hidden flex flex-col md:flex-row">
            <div class="w-full md:w-3/5 h-64 md:h-auto bg-gray-200 relative">
                <div id="map" class="w-full h-full absolute inset-0 z-0"></div>
                <div class="absolute top-2 left-1/2 transform -translate-x-1/2 z-10 bg-white/90 px-3 py-1 rounded-full shadow-md text-xs font-bold text-blue-600">
                    📍 Klik di peta untuk menandai lokasi
                </div>
            </div>
            <div class="w-full md:w-2/5 p-6 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Detail Ruangan</h3>
                        <button type="button" onclick="closeMapModal()" class="text-gray-400 hover:text-red-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <form action="{{ route('admin.ruangan.store') }}" method="POST" id="formRuangan">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Ruangan</label>
                                <input type="text" name="nama_ruangan" required placeholder="Cth: Gedung GKT Lt.2" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Latitude</label>
                                    <input type="text" id="latInput" name="latitude" required readonly class="w-full bg-gray-100 border-gray-300 rounded-lg text-sm text-gray-500 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Longitude</label>
                                    <input type="text" id="lngInput" name="longitude" required readonly class="w-full bg-gray-100 border-gray-300 rounded-lg text-sm text-gray-500 cursor-not-allowed">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Radius Toleransi (Meter)</label>
                                <input type="number" name="radius_meter" required min="5" step="1" value="50" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                                <p class="text-[10px] text-gray-500 mt-1">Jarak maksimal mahasiswa bisa absen dari titik pusat.</p>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="flex justify-end gap-2 mt-6 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeMapModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200">Batal</button>
                    <button type="submit" form="formRuangan" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Simpan Titik</button>
                </div>
            </div>
        </div>
    </div>

    <div id="modalEditMap" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-4xl mx-4 overflow-hidden flex flex-col md:flex-row">
            <div class="w-full md:w-3/5 h-64 md:h-auto bg-gray-200 relative">
                <div id="edit_map" class="w-full h-full absolute inset-0 z-0"></div>
                <div class="absolute top-2 left-1/2 transform -translate-x-1/2 z-10 bg-white/90 px-3 py-1 rounded-full shadow-md text-xs font-bold text-blue-600">
                    📍 Klik di peta untuk ubah lokasi
                </div>
            </div>
            <div class="w-full md:w-2/5 p-6 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Edit Ruangan</h3>
                        <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-red-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <form method="POST" id="formEditRuangan">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Ruangan</label>
                                <input type="text" name="nama_ruangan" id="edit_nama_ruangan" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Latitude</label>
                                    <input type="text" id="edit_latInput" name="latitude" required readonly class="w-full bg-gray-100 border-gray-300 rounded-lg text-sm text-gray-500 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Longitude</label>
                                    <input type="text" id="edit_lngInput" name="longitude" required readonly class="w-full bg-gray-100 border-gray-300 rounded-lg text-sm text-gray-500 cursor-not-allowed">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Radius Toleransi (Meter)</label>
                                <input type="number" name="radius_meter" id="edit_radius" required min="5" step="1" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                                <p class="text-[10px] text-gray-500 mt-1">Jarak maksimal mahasiswa bisa absen dari titik pusat.</p>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="flex justify-end gap-2 mt-6 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200">Batal</button>
                    <button type="submit" form="formEditRuangan" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Map Tambah Ruangan
        let map = null;
        let marker = null;
        function initMap() {
            const unmulLat = -0.4662, unmulLng = 117.1556;
            if (!map) {
                map = L.map('map').setView([unmulLat, unmulLng], 16);
                L.tileLayer('http://{s}.google.com/vt?lyrs=m&x={x}&y={y}&z={z}', {
                    maxZoom: 20, subdomains:['mt0','mt1','mt2','mt3']
                }).addTo(map);

                marker = L.marker([unmulLat, unmulLng], { draggable: false }).addTo(map);

                map.on('click', function(e) {
                    let lat = parseFloat(e.latlng.lat).toFixed(6);
                    let lng = parseFloat(e.latlng.lng).toFixed(6);
                    marker.setLatLng([lat, lng]);
                    document.getElementById('latInput').value = lat;
                    document.getElementById('lngInput').value = lng;
                });
            } else {
                // reset default position for form baru
                const latInput = document.getElementById('latInput');
                const lngInput = document.getElementById('lngInput');
                map.setView([unmulLat, unmulLng], 16);
                marker.setLatLng([unmulLat, unmulLng]);
                if(latInput && lngInput) {
                    latInput.value = '';
                    lngInput.value = '';
                }
            }
        }

        function openMapModal() {
            document.getElementById('modalTambahMap').classList.remove('hidden');
            setTimeout(() => {
                initMap();
                setTimeout(() => { map.invalidateSize() }, 100);
            }, 200);
        }
        function closeMapModal() {
            document.getElementById('modalTambahMap').classList.add('hidden');
        }

        // Map Edit Ruangan
        let editMap = null;
        let editMarker = null;
        function openEditModal(id, nama, lat, lng, radius) {
            const modalEdit = document.getElementById('modalEditMap');
            modalEdit.classList.remove('hidden');

            // Isi data form
            document.getElementById('edit_nama_ruangan').value = nama;
            document.getElementById('edit_latInput').value = lat;
            document.getElementById('edit_lngInput').value = lng;
            document.getElementById('edit_radius').value = radius;
            document.getElementById('formEditRuangan').action = `/admin/ruangan/${id}`;

            setTimeout(() => {
                lat = parseFloat(lat);
                lng = parseFloat(lng);
                if (!editMap) {
                    editMap = L.map('edit_map').setView([lat, lng], 18);
                    L.tileLayer('http://{s}.google.com/vt?lyrs=m&x={x}&y={y}&z={z}', {
                        maxZoom: 20, subdomains:['mt0','mt1','mt2','mt3']
                    }).addTo(editMap);

                    editMarker = L.marker([lat, lng], { draggable: false }).addTo(editMap);

                    editMap.on('click', function(e) {
                        let newLat = parseFloat(e.latlng.lat).toFixed(6);
                        let newLng = parseFloat(e.latlng.lng).toFixed(6);
                        editMarker.setLatLng([newLat, newLng]);
                        document.getElementById('edit_latInput').value = newLat;
                        document.getElementById('edit_lngInput').value = newLng;
                    });
                } else {
                    editMap.setView([lat, lng], 18);
                    editMarker.setLatLng([lat, lng]);
                    document.getElementById('edit_latInput').value = lat;
                    document.getElementById('edit_lngInput').value = lng;
                }
                editMap.invalidateSize();
            }, 200);
        }

        function closeEditModal() {
            document.getElementById('modalEditMap').classList.add('hidden');
        }

        // Notifikasi SweetAlert
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session("success") }}',
                    showConfirmButton: false,
                    timer: 3000
                });
            @endif
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan!',
                    text: 'Pastikan titik peta sudah diklik dan form terisi.'
                });
            @endif
        });
    </script>
</x-app-layout>