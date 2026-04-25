<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Manajemen Data Kelas</h2>
    </x-slot>

    <div class="px-5 pt-5">
        <div class="mb-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h3 class="text-lg font-semibold text-gray-700 hidden sm:block">
                Daftar Kelas Perkuliahan
            </h3>
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                <form action="{{ route('admin.kelas.index') }}" method="GET" class="flex items-center w-full sm:w-auto relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Matkul, Dosen, Kelas..." class="w-full sm:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <div class="absolute left-3 top-2.5 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    @if(request('search'))
                        <a href="{{ route('admin.kelas.index') }}" class="ml-2 text-red-500 hover:text-red-700 text-sm font-medium whitespace-nowrap">Reset</a>
                    @endif
                </form>
                <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center justify-center gap-2 text-sm shadow-sm transition-all whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Kelas
                </button>
                <button type="button" onclick="document.getElementById('modalImport').classList.remove('hidden')" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center gap-2 text-sm shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Import Excel
                </button>
            </div>
        </div>

        <div class="overflow-x-auto bg-white rounded-xl border border-gray-200 shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Mata Kuliah</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Nama Kelas</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Dosen Pengampu</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Tahun Ajaran</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($kelas as $index => $k)
                        <tr class="hover:bg-blue-50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-800">{{ $k->mataKuliah->nama_mk ?? 'N/A' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 font-bold rounded-md text-xs">{{ $k->nama_kelas }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $k->dosen->nama_dosen ?? 'N/A' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center text-gray-500 font-medium">{{ $k->tahunAjaran->tahun_ajaran ?? 'N/A' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" onclick="document.getElementById('modalEdit{{ $k->id }}').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 rounded-full text-xs font-medium text-gray-700 hover:bg-gray-100 hover:text-blue-600 shadow-sm transition-all">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.kelas.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kelas ini beserta seluruh jadwal di dalamnya?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-red-200 rounded-full text-xs font-medium text-red-600 hover:bg-red-50 shadow-sm transition-all">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                             </td>
                        </tr>
                        <div id="modalEdit{{ $k->id }}" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm overflow-y-auto pt-10 pb-10">
                            <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 overflow-hidden text-left my-auto">
                                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                                    <h3 class="text-lg font-bold text-gray-800">Edit Wadah Kelas</h3>
                                    <button type="button" onclick="document.getElementById('modalEdit{{ $k->id }}').classList.add('hidden')" class="text-gray-400 hover:text-red-500">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                                
                                <form action="{{ route('admin.kelas.update', $k->id) }}" method="POST">
                                    @csrf 
                                    @method('PUT')
                                    <div class="p-6 space-y-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun Ajaran</label>
                                            <select name="tahun_ajaran_id" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm bg-gray-50">
                                                @foreach($tahunAjarans as $ta)
                                                    <option value="{{ $ta->id }}" {{ $k->tahun_ajaran_id == $ta->id ? 'selected' : '' }}>{{ $ta->tahun_ajaran }} - {{ $ta->semester }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Mata Kuliah</label>
                                            <select name="mata_kuliah_id" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                                                @foreach($mataKuliahs as $mk)
                                                    <option value="{{ $mk->id }}" {{ $k->mata_kuliah_id == $mk->id ? 'selected' : '' }}>{{ $mk->kode_mk }} - {{ $mk->nama_mk }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Dosen Pengampu</label>
                                            <select name="dosen_id" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                                                @foreach($dosens as $dsn)
                                                    <option value="{{ $dsn->id }}" {{ $k->dosen_id == $dsn->id ? 'selected' : '' }}>{{ $dsn->nama_dosen }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Kelas</label>
                                            <input type="text" name="nama_kelas" value="{{ $k->nama_kelas }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                                        </div>
                                    </div>
                                    <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-2">
                                        <button type="button" onclick="document.getElementById('modalEdit{{ $k->id }}').classList.add('hidden')" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-100">Batal</button>
                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-500">
                                Belum ada data Kelas Perkuliahan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="modalTambah" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm overflow-y-auto pt-10 pb-10">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 overflow-hidden text-left my-auto">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Buat Wadah Kelas Baru</h3>
                <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="text-gray-400 hover:text-red-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('admin.kelas.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun Ajaran</label>
                        <select name="tahun_ajaran_id" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm bg-gray-50">
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}">{{ $ta->tahun_ajaran }} - {{ $ta->semester }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Mata Kuliah</label>
                        <select name="mata_kuliah_id" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                            <option value="">-- Pilih Mata Kuliah --</option>
                            @foreach($mataKuliahs as $mk)
                                <option value="{{ $mk->id }}">{{ $mk->kode_mk }} - {{ $mk->nama_mk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Dosen Pengampu</label>
                        <select name="dosen_id" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                            <option value="">-- Pilih Dosen --</option>
                            @foreach($dosens as $dsn)
                                <option value="{{ $dsn->id }}">{{ $dsn->nama_dosen }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Kelas</label>
                        <input type="text" name="nama_kelas" required placeholder="Contoh: A, B, atau TI-1" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-100">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Simpan Kelas</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalImport" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Import Data Kelas</h3>
                <button type="button" onclick="document.getElementById('modalImport').classList.add('hidden')" class="text-gray-400 hover:text-red-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('admin.kelas.import') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel (.xlsx / .csv)</label>
                    <input type="file" name="file" required accept=".xlsx, .csv" class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer transition-colors">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modalImport').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 shadow-md">Mulai Import</button>
                </div>
            </form>
        </div>
    </div>

    <script>
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
                    title: 'Perhatian!',
                    html: `<ul class="text-left text-sm text-red-600 list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>`,
                    confirmButtonColor: '#2563EB',
                    customClass: { popup: 'rounded-xl' }
                });
            @endif
        });
    </script>
</x-app-layout>