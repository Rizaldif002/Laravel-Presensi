<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.kelas.index') }}"
               class="text-gray-400 hover:text-blue-600 transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span class="text-sm font-medium">Kembali</span>
            </a>
            <span class="text-gray-300">/</span>
            <h2 class="text-xl font-bold text-gray-800">Kelola Peserta Kelas</h2>
        </div>
    </x-slot>

    <div class="px-5 pt-5 pb-10"
         x-data="{ showTambah: false, showImport: false, showFilter: false }">

        {{-- Card Info Kelas --}}
        <div class="bg-white rounded-xl border-l-4 border-l-blue-600 border border-gray-200 shadow-sm p-5 mb-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Mata Kuliah</p>
                    <p class="font-bold text-gray-800">{{ $kelas->mataKuliah->nama_mk ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Kelas</p>
                    <p class="font-bold text-gray-800">{{ $kelas->nama_kelas }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Dosen Pengampu</p>
                    <p class="font-bold text-gray-800">{{ $kelas->dosen->nama_dosen ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Tahun Ajaran</p>
                    <p class="font-bold text-gray-800">
                        {{ $kelas->tahunAjaran->tahun_ajaran ?? '-' }}
                        <span class="text-blue-500 font-normal">{{ $kelas->tahunAjaran->semester ?? '' }}</span>
                    </p>
                </div>
            </div>
        </div>

        {{-- Toolbar --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mb-4">

            {{-- Kiri: Per Page + Reset --}}
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <form method="GET" action="{{ route('admin.kelas.peserta.index', $kelas->id) }}" id="formPerPage">
                    @foreach(request()->except(['per_page', 'page']) as $key => $val)
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endforeach
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-500">Tampilkan</span>
                        <select name="per_page" onchange="document.getElementById('formPerPage').submit()"
                                class="w-20 py-1.5 pl-3 pr-8 text-sm border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-white">
                            @foreach([10, 15, 25, 50] as $n)
                                <option value="{{ $n }}" {{ request('per_page', 15) == $n ? 'selected' : '' }}>{{ $n }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
                @if(request()->filled('search') || request()->filled('sort'))
                    <a href="{{ route('admin.kelas.peserta.index', array_filter(['per_page' => request('per_page'), 'kelas' => $kelas->id])) }}"
                       class="inline-flex items-center gap-1 px-3 py-2 bg-red-50 border border-red-200 text-red-600 hover:bg-red-100 rounded-lg text-sm font-medium transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reset
                    </a>
                @endif
            </div>

            {{-- Kanan: Filter + Tambah + Import --}}
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto justify-end">
                <button @click="showFilter = true"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white border text-gray-700 hover:bg-gray-50 rounded-lg text-sm font-medium shadow-sm transition-all whitespace-nowrap
                               {{ request()->hasAny(['search','sort']) ? 'ring-2 ring-blue-400 border-blue-400' : 'border-gray-300' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                    Filter & Urutkan
                    @if(request()->filled('search') || (request()->filled('sort') && request('sort') !== 'nim_asc'))
                        <span class="inline-flex items-center justify-center w-4 h-4 bg-blue-500 text-white rounded-full text-xs font-bold">
                            {{ collect(['search', 'sort'])->filter(fn($k) => request()->filled($k) && ($k !== 'sort' || request('sort') !== 'nim_asc'))->count() }}
                        </span>
                    @endif
                </button>

                <button @click="showTambah = true"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-md shadow-sm transition-colors whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Peserta
                </button>

                <button @click="showImport = true"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-md shadow-sm transition-colors whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                    Import Excel
                </button>
            </div>
        </div>

        {{-- Tabel Peserta --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase w-16">No.</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase w-40">NIM</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase">Nama Mahasiswa</th>
                            <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($peserta as $no => $p)
                            @php
                                $nim  = $p->mahasiswa->nim ?? '';
                                $nama = $p->mahasiswa->nama_lengkap ?? '-';
                            @endphp
                            <tr class="hover:bg-blue-50/50 transition-colors">
                                <td class="px-5 py-4 text-sm text-gray-500 font-medium">{{ $peserta->firstItem() + $no }}</td>
                                <td class="px-5 py-4 text-sm font-semibold text-gray-700">{{ $nim ?: '-' }}</td>
                                <td class="px-5 py-4 text-sm font-semibold text-gray-800 uppercase">{{ $nama }}</td>
                                <td class="px-5 py-4 text-center">
                                    <form action="{{ route('admin.kelas.peserta.destroy', [$kelas->id, $p->id]) }}"
                                          method="POST"
                                          onsubmit="return confirm('Keluarkan {{ addslashes($nama) }} dari kelas ini?')"
                                          class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-100 rounded-md hover:bg-red-600 hover:text-white transition-colors shadow-sm"
                                                title="Hapus Peserta">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-16 text-center text-gray-400 italic">
                                    {{ request()->filled('search') ? 'Tidak ada peserta yang cocok dengan pencarian.' : 'Belum ada mahasiswa di kelas ini.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                Total: <span class="font-bold text-gray-700">{{ $peserta->total() }}</span> peserta
                @if(request()->filled('search'))
                    <span class="text-gray-400">(difilter)</span>
                @endif
            </p>
            {{ $peserta->links() }}
        </div>

        {{-- Modal Filter & Urutkan --}}
        <div x-show="showFilter"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div @click.away="showFilter = false"
                 class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-base font-bold text-gray-800">Filter & Urutkan</h3>
                    <button @click="showFilter = false" class="text-gray-400 hover:text-red-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form action="{{ route('admin.kelas.peserta.index', $kelas->id) }}" method="GET" class="p-6 space-y-4">
                    @if(request()->filled('per_page'))
                        <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                    @endif

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Cari NIM / Nama</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Ketik kata kunci..."
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Urutan</label>
                        <select name="sort" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="nim_asc"   {{ request('sort', 'nim_asc') === 'nim_asc'   ? 'selected' : '' }}>NIM Terkecil ke Terbesar (Default)</option>
                            <option value="nim_desc"  {{ request('sort') === 'nim_desc'  ? 'selected' : '' }}>NIM Terbesar ke Terkecil</option>
                            <option value="nama_asc"  {{ request('sort') === 'nama_asc'  ? 'selected' : '' }}>Nama A → Z</option>
                            <option value="nama_desc" {{ request('sort') === 'nama_desc' ? 'selected' : '' }}>Nama Z → A</option>
                        </select>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <a href="{{ route('admin.kelas.peserta.index', array_filter(['per_page' => request('per_page'), 'kelas' => $kelas->id])) }}"
                           class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-100">
                            Reset
                        </a>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                            Terapkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Tambah Manual --}}
        <div x-show="showTambah"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div @click.away="showTambah = false"
                 class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/80">
                    <h3 class="text-base font-bold text-gray-800">Tambah Mahasiswa Manual</h3>
                    <button @click="showTambah = false" class="text-gray-400 hover:text-red-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form action="{{ route('admin.kelas.peserta.store', $kelas->id) }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Mahasiswa</label>
                        <select name="mahasiswa_id" required
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">-- Pilih Mahasiswa --</option>
                            @foreach($mahasiswas as $m)
                                <option value="{{ $m->id }}">{{ $m->nim }} — {{ $m->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="showTambah = false"
                                class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 shadow-md shadow-blue-600/20 transition-all">
                            Tambahkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Import Excel --}}
        <div x-show="showImport"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div @click.away="showImport = false"
                 class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/80">
                    <h3 class="text-base font-bold text-gray-800">Import Peserta dari Excel</h3>
                    <button @click="showImport = false" class="text-gray-400 hover:text-red-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form action="{{ route('admin.kelas.peserta.import', $kelas->id) }}" method="POST"
                      enctype="multipart/form-data" class="p-6 space-y-5">
                    @csrf
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-800 space-y-1.5 shadow-sm">
                        <p class="font-bold flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Format Template Excel:
                        </p>
                        <p class="ml-6">Kolom A (baris 2 dst): <span class="font-mono font-bold bg-white px-1 py-0.5 rounded border border-blue-200">NIM</span> mahasiswa.</p>
                        <p class="ml-6 text-blue-600 text-xs">Baris pertama adalah header, akan diabaikan otomatis.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih File (.xlsx / .csv)</label>
                        <input type="file" name="file" required accept=".xlsx,.xls,.csv"
                               class="w-full text-sm text-gray-600 border border-gray-200 rounded-lg shadow-sm file:mr-4 file:py-2.5 file:px-4 file:border-0 file:text-sm file:font-bold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer transition-colors focus:outline-none">
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="showImport = false"
                                class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 bg-green-600 text-white rounded-lg text-sm font-bold hover:bg-green-700 shadow-md shadow-green-600/20 transition-all">
                            Mulai Import
                        </button>
                    </div>
                </form>
            </div>
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
                    customClass: { popup: 'rounded-2xl shadow-xl border border-gray-100' }
                });
            @endif
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Perhatian!',
                    html: `<ul class="text-left text-sm text-red-600 list-disc pl-5 font-medium">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>`,
                    confirmButtonColor: '#2563EB',
                    customClass: { popup: 'rounded-2xl shadow-xl border border-gray-100', confirmButton: 'rounded-lg font-bold' }
                });
            @endif
        });
    </script>
</x-app-layout>
