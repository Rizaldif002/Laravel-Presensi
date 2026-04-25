<x-app-layout>
    <x-slot name="header">
        Data Mahasiswa
    </x-slot>

    <div>
        <div class="mb-4 flex flex-col sm:flex-row justify-between items-center gap-4">
            <h3 class="text-lg font-semibold text-gray-700">Daftar Mahasiswa</h3>
            <div class="flex items-center gap-2">
                <!-- Filter button -->
                <button id="btnFilter" type="button" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2 px-4 rounded-lg flex items-center gap-2 text-sm shadow-sm transition-all">
                    Filter & Urutkan
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                </button>
                <!-- Reset button (if filter active) -->
                @if(request('search') || request('sort'))
                    <a href="{{ route('admin.mahasiswa') }}" class="bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 font-medium py-2 px-4 rounded-lg flex items-center gap-2 text-sm shadow-sm transition-all">
                        Reset
                    </a>
                @endif
                <!-- Tambah Mahasiswa button -->
                <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center gap-2 text-sm shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Mahasiswa
                </button>
                <!-- Import Excel button -->
                <button onclick="document.getElementById('modalImport').classList.remove('hidden')" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center gap-2 text-sm shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Import Excel
                </button>
            </div>
        </div>

        <!-- Table Data Mahasiswa -->
        <div class="overflow-x-auto bg-white rounded-xl border border-gray-200 shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">NIM</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Lengkap</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Program Studi</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">No. HP</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($mahasiswas as $index => $mhs)
                        <tr class="odd:bg-white even:bg-gray-50 hover:bg-blue-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $mhs->nim }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $mhs->nama_lengkap }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $mhs->program_studi }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ $mhs->no_hp ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Edit Button -->
                                    <button type="button" onclick="document.getElementById('modalEdit{{ $mhs->id }}').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 rounded-full text-xs font-medium text-gray-700 hover:bg-gray-100 hover:text-blue-600 shadow-sm transition-all">
                                        Edit
                                    </button>
                                    <!-- Delete Form -->
                                    <form action="{{ route('admin.mahasiswa.destroy', $mhs->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data mahasiswa ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-red-200 rounded-full text-xs font-medium text-red-600 hover:bg-red-50 shadow-sm transition-all">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Edit Data Mahasiswa -->
                        <div id="modalEdit{{ $mhs->id }}" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm transition-opacity">
                            <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 overflow-hidden text-left">
                                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                                    <h3 class="text-lg font-bold text-gray-800">Edit Data Mahasiswa</h3>
                                    <button type="button" onclick="document.getElementById('modalEdit{{ $mhs->id }}').classList.add('hidden')" class="text-gray-400 hover:text-red-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                                <form action="{{ route('admin.mahasiswa.update', $mhs->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="p-6 space-y-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">NIM</label>
                                            <input type="text" name="nim" value="{{ $mhs->nim }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                                            <input type="text" name="nama_lengkap" value="{{ $mhs->nama_lengkap }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Program Studi</label>
                                            <input type="text" name="program_studi" value="{{ $mhs->program_studi }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">No. HP</label>
                                            <input type="text" name="no_hp" value="{{ $mhs->no_hp }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                                        </div>
                                    </div>
                                    <div class="px-6 py-4 bg-gray-50 flex justify-end gap-2">
                                        <button type="button" onclick="document.getElementById('modalEdit{{ $mhs->id }}').classList.add('hidden')" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium">Batal</button>
                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-sm text-gray-400 text-center italic">
                                Belum ada data mahasiswa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Filter -->
    <div id="modalFilter" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 overflow-hidden text-left">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Filter & Urutkan Data</h3>
                <button type="button" id="btnCloseFilter" class="text-gray-400 hover:text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.mahasiswa') }}" method="GET">
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Cari NIM / Nama</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci..." class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Urutkan Berdasarkan</label>
                        <select name="sort" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                            <option value="">-- Pilihan Urutan --</option>
                            <option value="nim_asc" {{ request('sort') == 'nim_asc' ? 'selected' : '' }}>NIM Terkecil ke Terbesar (A-Z)</option>
                            <option value="nim_desc" {{ request('sort') == 'nim_desc' ? 'selected' : '' }}>NIM Terbesar ke Terkecil (Z-A)</option>
                        </select>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" id="btnBatalFilter" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">Terapkan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Mahasiswa -->
    <div id="modalTambah" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 overflow-hidden text-left">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Tambah Mahasiswa</h3>
                <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="text-gray-400 hover:text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.mahasiswa.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">NIM</label>
                        <input type="text" name="nim" required placeholder="Contoh: 2009106001" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" required placeholder="Nama sesuai KTP/KTM" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Program Studi</label>
                        <input type="text" name="program_studi" required placeholder="Contoh: Teknik Elektro" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">No. HP (Opsional)</label>
                        <input type="text" name="no_hp" placeholder="08123456789" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Import Mahasiswa (Excel) -->
    <div id="modalImport" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 overflow-hidden text-left">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Import Data Mahasiswa</h3>
                <button type="button" onclick="document.getElementById('modalImport').classList.add('hidden')" class="text-gray-400 hover:text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.mahasiswa.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6 space-y-4">
                    <div class="bg-blue-50 p-3 rounded-lg text-sm text-blue-800 border border-blue-100 mb-4">
                        <strong>Aturan Format Excel:</strong><br>
                        - Baris 1: Judul Kolom (NIM, Nama, Prodi, No HP)<br>
                        - Kolom A: NIM<br>
                        - Kolom B: Nama Lengkap<br>
                        - Kolom C: Program Studi<br>
                        - Kolom D: No HP
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Upload File Excel (.xlsx / .csv)</label>
                        <input type="file" name="file_excel" accept=".xlsx, .xls, .csv" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modalImport').classList.add('hidden')" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium">Import</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts: Swal & Modal Toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Alert success
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session("success") }}',
                    showConfirmButton: false,
                    timer: 3000,
                    customClass: { popup: 'rounded-xl' }
                });
            @endif

            // Alert errors
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan!',
                    html: '<ul class="text-left text-sm text-red-600 list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                    confirmButtonColor: '#2563EB',
                    customClass: { popup: 'rounded-xl' }
                });
            @endif

            // Toggle modal filter
            document.getElementById('btnFilter').addEventListener('click', function() {
                document.getElementById('modalFilter').classList.remove('hidden');
            });
            document.getElementById('btnCloseFilter').addEventListener('click', function() {
                document.getElementById('modalFilter').classList.add('hidden');
            });
            document.getElementById('btnBatalFilter').addEventListener('click', function() {
                document.getElementById('modalFilter').classList.add('hidden');
            });
        });
    </script>
</x-app-layout>