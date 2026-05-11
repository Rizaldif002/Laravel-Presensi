<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Data Mata Kuliah</h2>
    </x-slot>

    <div>
        <div class="mb-4 flex flex-col gap-3">
            <div class="flex justify-start">
                <button onclick="openTambahModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center gap-2 text-sm shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Mata Kuliah
                </button>
            </div>

            <div class="border-t border-gray-200 pt-3 flex flex-col gap-3">


                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex flex-wrap items-center gap-2">
                        @include('admin.components.per-page-selector')
                        @if(request('search') || request('semester'))
                            <a href="{{ route('admin.mata-kuliah') }}" class="bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 font-medium py-2 px-4 rounded-lg flex items-center gap-2 text-sm shadow-sm transition-all">
                                Reset
                            </a>
                        @endif
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button onclick="openFilterModal()" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2 px-4 rounded-lg flex items-center gap-2 text-sm shadow-sm transition-all">
                            Filter Matkul
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                        </button>
                        <button onclick="openImportModal()" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center gap-2 text-sm shadow-sm transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Import Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto bg-white rounded-xl border border-gray-200 shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kode MK</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Mata Kuliah</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">SKS</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Semester</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($mataKuliahs as $index => $mk)
                        <tr class="odd:bg-white even:bg-gray-50 hover:bg-blue-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $mataKuliahs->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-600">{{ $mk->kode_mk }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700">{{ $mk->nama_mk }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ $mk->sks }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ $mk->semester }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" onclick="openEditModal({{ $mk->id }})"
                                            class="inline-flex items-center justify-center w-9 h-9 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-700 hover:text-white transition-colors shadow-sm"
                                            title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form action="{{ route('admin.mata-kuliah.destroy', $mk->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus mata kuliah ini?');" class="m-0">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-9 h-9 bg-red-100 text-red-600 rounded-md hover:bg-red-600 hover:text-white transition-colors shadow-sm"
                                                title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Edit Mata Kuliah -->
                        <div id="modalEdit{{ $mk->id }}" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm transition-opacity">
                            <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 overflow-hidden text-left">
                                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                                    <h3 class="text-lg font-bold text-gray-800">Edit Mata Kuliah</h3>
                                    <button type="button" onclick="closeEditModal({{ $mk->id }})" class="text-gray-400 hover:text-red-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                                <form action="{{ route('admin.mata-kuliah.update', $mk->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="p-6 space-y-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Kode MK</label>
                                            <input type="text" name="kode_mk" value="{{ $mk->kode_mk }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Mata Kuliah</label>
                                            <input type="text" name="nama_mk" value="{{ $mk->nama_mk }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-1">SKS</label>
                                                <input type="number" name="sks" value="{{ $mk->sks }}" required min="1" max="6" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-1">Semester</label>
                                                <input type="number" name="semester" value="{{ $mk->semester }}" required min="1" max="8" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="px-6 py-4 bg-gray-50 flex justify-end gap-2">
                                        <button type="button" onclick="closeEditModal({{ $mk->id }})" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium">Batal</button>
                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-sm text-gray-400 text-center italic">Belum ada data mata kuliah yang ditambahkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 flex justify-end">
            {{ $mataKuliahs->links() }}
        </div>
    </div>

    <!-- Modal Filter Mata Kuliah -->
    <div id="modalFilter" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 overflow-hidden text-left">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Filter & Cari Matkul</h3>
                <button type="button" onclick="closeFilterModal()" class="text-gray-400 hover:text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.mata-kuliah') }}" method="GET">
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Cari Kode / Nama MK</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci..." class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Filter Semester</label>
                        <select name="semester" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                            <option value="">-- Tampilkan Semua --</option>
                            @for($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}" {{ request('semester') == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-2">
                    <button type="button" onclick="closeFilterModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Mata Kuliah -->
    <div id="modalTambah" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 overflow-hidden text-left">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Tambah Mata Kuliah</h3>
                <button type="button" onclick="closeTambahModal()" class="text-gray-400 hover:text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.mata-kuliah.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kode MK</label>
                        <input type="text" name="kode_mk" required placeholder="Contoh: TEE123" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Mata Kuliah</label>
                        <input type="text" name="nama_mk" required placeholder="Contoh: Algoritma dan Pemrograman" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">SKS</label>
                            <input type="number" name="sks" required min="1" max="6" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Semester</label>
                            <input type="number" name="semester" required min="1" max="8" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-2">
                    <button type="button" onclick="closeTambahModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Import Mata Kuliah -->
    <div id="modalImportMK" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 overflow-hidden text-left">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Import Mata Kuliah</h3>
                <button type="button" onclick="closeImportModal()" class="text-gray-400 hover:text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.mata-kuliah.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6 space-y-4">
                    <div class="bg-blue-50 p-3 rounded-lg text-sm text-blue-800 border border-blue-100">
                        <strong>Format Excel:</strong><br>
                        - Kolom A: Kode MK<br>
                        - Kolom B: Nama MK<br>
                        - Kolom C: SKS<br>
                        - Kolom D: Semester
                    </div>
                    <input type="file" name="file" required accept=".xlsx, .csv" 
    class="w-full text-sm text-gray-600 
           file:mr-4 file:py-2 file:px-4 
           file:rounded-full file:border-0 
           file:text-sm file:font-semibold 
           file:bg-green-50 file:text-green-700 
           hover:file:bg-green-100 cursor-pointer transition-colors">
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-2">
                    <button type="button" onclick="closeImportModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium">Import</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openFilterModal() {
            document.getElementById('modalFilter').classList.remove('hidden');
        }
        function closeFilterModal() {
            document.getElementById('modalFilter').classList.add('hidden');
        }

        function openTambahModal() {
            document.getElementById('modalTambah').classList.remove('hidden');
        }
        function closeTambahModal() {
            document.getElementById('modalTambah').classList.add('hidden');
        }

        function openImportModal() {
            document.getElementById('modalImportMK').classList.remove('hidden');
        }
        function closeImportModal() {
            document.getElementById('modalImportMK').classList.add('hidden');
        }

        function openEditModal(id) {
            document.getElementById('modalEdit' + id).classList.remove('hidden');
        }
        function closeEditModal(id) {
            document.getElementById('modalEdit' + id).classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function () {
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

            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan!',
                    html: '<ul class="text-left text-sm text-red-600 list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                    confirmButtonColor: '#2563EB',
                    customClass: { popup: 'rounded-xl' }
                });
            @endif
        });
    </script>
</x-app-layout>