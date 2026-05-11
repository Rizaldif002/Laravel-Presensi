<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800"> Data Kelas</h2>
    </x-slot>

    <div class="px-5 pt-5">
        <div class="mb-4 flex flex-col gap-3">
            <div class="flex justify-start">
                <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center justify-center gap-2 text-sm shadow-sm transition-all whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Kelas
                </button>
            </div>

            <div class="border-t border-gray-200 pt-3 flex flex-col gap-3">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-3">
                        @include('admin.components.per-page-selector')
                        @if(request()->filled('tahun_ajaran_id') || request()->filled('dosen_id') || request()->filled('mata_kuliah_id'))
                            <a href="{{ route('admin.kelas.index', array_filter(['per_page' => request('per_page')])) }}" class="inline-flex items-center gap-1 px-3 py-2 bg-red-50 border border-red-200 text-red-600 hover:bg-red-100 rounded-lg text-sm font-medium transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Reset
                            </a>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <button type="button" onclick="document.getElementById('modalFilter').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-sm font-medium shadow-sm transition-all whitespace-nowrap {{ request()->hasAny(['tahun_ajaran_id','dosen_id','mata_kuliah_id']) ? 'ring-2 ring-blue-400 border-blue-400' : '' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                            Filter Kelas
                            @if(request()->hasAny(['tahun_ajaran_id','dosen_id','mata_kuliah_id']))
                                <span class="inline-flex items-center justify-center w-4 h-4 bg-blue-500 text-white rounded-full text-xs font-bold">
                                    {{ collect(['tahun_ajaran_id','dosen_id','mata_kuliah_id'])->filter(fn($k) => request()->filled($k))->count() }}
                                </span>
                            @endif
                        </button>
                        <button type="button" onclick="document.getElementById('modalImport').classList.remove('hidden')" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center gap-2 text-sm shadow-sm transition-all whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
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
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Mata Kuliah</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Nama Kelas</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Dosen Pengampu</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Jumlah Peserta</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Tahun Ajaran</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($kelas as $index => $k)
                        <tr class="hover:bg-blue-50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $kelas->firstItem() + $index }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-800">{{ $k->mataKuliah->nama_mk ?? 'N/A' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 font-bold rounded-md text-xs">{{ $k->nama_kelas }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $k->dosen->nama_dosen ?? 'N/A' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-50 text-blue-700 text-sm font-bold">{{ $k->peserta_count ?? 0 }}</span>
                            </td>
                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                <span class="block text-sm font-semibold text-gray-700">{{ $k->tahunAjaran->tahun_ajaran ?? '-' }}</span>
                                <span class="text-xs text-blue-500">{{ $k->tahunAjaran->semester ?? '' }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Tombol Peserta --}}
                                    <a href="{{ route('admin.kelas.peserta.index', $k->id) }}" 
                                       class="inline-flex items-center justify-center w-9 h-9 bg-blue-100 text-blue-600 rounded-md hover:bg-blue-600 hover:text-white transition-colors shadow-sm" 
                                       title="Lihat Peserta Kelas">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    </a>
                                    {{-- Tombol Edit --}}
                                    <button type="button" 
                                            onclick="document.getElementById('modalEdit{{ $k->id }}').classList.remove('hidden')" 
                                            class="inline-flex items-center justify-center w-9 h-9 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-700 hover:text-white transition-colors shadow-sm" 
                                            title="Edit Wadah Kelas">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('admin.kelas.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kelas ini beserta seluruh jadwal di dalamnya?');" class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center justify-center w-9 h-9 bg-red-100 text-red-600 rounded-md hover:bg-red-600 hover:text-white transition-colors shadow-sm" 
                                                title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <div id="modalEdit{{ $k->id }}" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm overflow-y-auto pt-10 pb-10">
                            <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 overflow-hidden text-left my-auto">
                                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                                    <h3 class="text-lg font-bold text-gray-800">Edit</h3>
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
                            <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                                Belum ada data Kelas Perkuliahan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 flex justify-end">
            {{ $kelas->links() }}
        </div>
    </div>

    {{-- Modal Filter --}}
    <div id="modalFilter" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-sm mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Filter Kelas</h3>
                <button type="button" onclick="document.getElementById('modalFilter').classList.add('hidden')" class="text-gray-400 hover:text-red-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('admin.kelas.index') }}" method="GET" class="p-6 space-y-4">
                @foreach(request()->except(['tahun_ajaran_id', 'dosen_id', 'mata_kuliah_id', 'page']) as $key => $val)
                    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                @endforeach

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun Ajaran</label>
                    <select name="tahun_ajaran_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm bg-gray-50">
                        <option value="">Semua Tahun Ajaran</option>
                        @foreach($tahunAjarans as $ta)
                            <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>
                                {{ $ta->tahun_ajaran }} – {{ $ta->semester }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Dosen Pengampu</label>
                    <select name="dosen_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm bg-gray-50">
                        <option value="">Semua Dosen</option>
                        @foreach($dosens as $dsn)
                            <option value="{{ $dsn->id }}" {{ request('dosen_id') == $dsn->id ? 'selected' : '' }}>
                                {{ $dsn->nama_dosen }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Mata Kuliah</label>
                    <select name="mata_kuliah_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm bg-gray-50">
                        <option value="">Semua Mata Kuliah</option>
                        @foreach($mataKuliahs as $mk)
                            <option value="{{ $mk->id }}" {{ request('mata_kuliah_id') == $mk->id ? 'selected' : '' }}>
                                {{ $mk->kode_mk }} – {{ $mk->nama_mk }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <a href="{{ route('admin.kelas.index', array_filter(['per_page' => request('per_page')])) }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-100">Reset</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Terapkan</button>
                </div>
            </form>
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