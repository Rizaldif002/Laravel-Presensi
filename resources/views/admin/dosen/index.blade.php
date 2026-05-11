<x-app-layout>
    <x-slot name="header">
        Data Dosen
    </x-slot>

    <div>
        <div class="mb-4 flex flex-col gap-3">
            <div class="flex justify-start">
                <button onclick="openTambahModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center gap-2 text-sm shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Dosen
                </button>
            </div>

            <div class="border-t border-gray-200 pt-3 flex flex-col gap-3">


                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex flex-wrap items-center gap-2">
                        @include('admin.components.per-page-selector')
                        @if(request('search') || request('sort'))
                            <a href="{{ route('admin.dosen') }}" class="bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 font-medium py-2 px-4 rounded-lg flex items-center gap-2 text-sm shadow-sm transition-all">
                                Reset
                            </a>
                        @endif
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" id="btnOpenFilter" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2 px-4 rounded-lg flex items-center gap-2 text-sm shadow-sm transition-all">
                            Filter & Urutkan
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                        </button>
                        <button onclick="openImportDosenModal()" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center gap-2 text-sm shadow-sm transition-all">
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
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">NIP</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Dosen</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">No. HP</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($dosens as $index => $dosen)
                        <tr class="odd:bg-white even:bg-gray-50 hover:bg-blue-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $dosens->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-600">{{ $dosen->nip ?? $dosen->nidn }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700">{{ $dosen->nama_dosen }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ $dosen->no_hp ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" onclick="document.getElementById('modalEdit{{ $dosen->id }}').classList.remove('hidden')"
                                            class="inline-flex items-center justify-center w-9 h-9 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-700 hover:text-white transition-colors shadow-sm"
                                            title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form action="{{ route('admin.dosen.destroy', $dosen->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data dosen ini?');" class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-9 h-9 bg-red-100 text-red-600 rounded-md hover:bg-red-600 hover:text-white transition-colors shadow-sm"
                                                title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Edit Dosen -->
                        <div id="modalEdit{{ $dosen->id }}" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm transition-opacity">
                            <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 overflow-hidden text-left">
                                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                                    <h3 class="text-lg font-bold text-gray-800">Edit Data Dosen</h3>
                                    <button type="button" onclick="document.getElementById('modalEdit{{ $dosen->id }}').classList.add('hidden')" class="text-gray-400 hover:text-red-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                                <form action="{{ route('admin.dosen.update', $dosen->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="p-6 space-y-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">NIP</label>
                                            <input type="text" name="nip" value="{{ $dosen->nip ?? $dosen->nidn }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Dosen</label>
                                            <input type="text" name="nama_dosen" value="{{ $dosen->nama_dosen }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">No. HP</label>
                                            <input type="text" name="no_hp" value="{{ $dosen->no_hp }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                                        </div>
                                    </div>
                                    <div class="px-6 py-4 bg-gray-50 flex justify-end gap-2">
                                        <button type="button" onclick="document.getElementById('modalEdit{{ $dosen->id }}').classList.add('hidden')" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium">Batal</button>
                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-sm text-gray-400 text-center italic">Belum ada data dosen.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 flex justify-end">
            {{ $dosens->links() }}
        </div>
    </div>

    <!-- Modal Filter -->
    <div id="modalFilter" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 overflow-hidden text-left">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Filter & Cari Dosen</h3>
                <button type="button" onclick="closeFilterModal()" class="text-gray-400 hover:text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.dosen') }}" method="GET" id="formFilterDosen">
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Cari NIP / Nama</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci..." class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Urutan NIP</label>
                        <select name="sort" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                            <option value="nip_asc" {{ request('sort') == 'nip_asc' ? 'selected' : '' }}>Terkecil ke Terbesar (Default)</option>
                            <option value="nip_desc" {{ request('sort') == 'nip_desc' ? 'selected' : '' }}>Terbesar ke Terkecil</option>
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

    <!-- Modal Tambah Dosen -->
    <div id="modalTambah" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 overflow-hidden text-left">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Tambah Dosen</h3>
                <button type="button" onclick="closeTambahModal()" class="text-gray-400 hover:text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.dosen.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">NIP</label>
                        <input type="text" name="nip" required placeholder="Nomor Induk Pegawai (NIP) Dosen" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Dosen</label>
                        <input type="text" name="nama_dosen" required placeholder="Nama lengkap dosen" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">No. HP (Opsional)</label>
                        <input type="text" name="no_hp" placeholder="08123456789" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-2">
                    <button type="button" onclick="closeTambahModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Import Dosen -->
    <div id="modalImportDosen" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 overflow-hidden text-left">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Import Data Dosen</h3>
                <button type="button" onclick="closeImportDosenModal()" class="text-gray-400 hover:text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.dosen.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6 space-y-4">
                    <div class="bg-blue-50 p-3 rounded-lg text-sm text-blue-800 border border-blue-100">
                        <strong>Format Excel:</strong><br>
                        - Kolom A: NIP<br>
                        - Kolom B: Nama Dosen<br>
                        - Kolom C: No HP
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
                    <button type="button" onclick="closeImportDosenModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium">Import</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openFilterModal() { document.getElementById('modalFilter').classList.remove('hidden'); }
        function closeFilterModal() { document.getElementById('modalFilter').classList.add('hidden'); }
        function openTambahModal() { document.getElementById('modalTambah').classList.remove('hidden'); }
        function closeTambahModal() { document.getElementById('modalTambah').classList.add('hidden'); }
        function openImportDosenModal() { document.getElementById('modalImportDosen').classList.remove('hidden'); }
        function closeImportDosenModal() { document.getElementById('modalImportDosen').classList.add('hidden'); }

        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('btnOpenFilter').addEventListener('click', function() {
                openFilterModal();
            });

            // Otomatis fokus ke input search pada modal filter ketika terbuka
            document.getElementById('modalFilter').addEventListener('transitionend', function(e) {
                if (!this.classList.contains('hidden')) {
                    let input = this.querySelector('input[name="search"]');
                    if(input) input.focus();
                }
            });

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