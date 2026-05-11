<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Data Tahun Ajaran</h2>
    </x-slot>

    @php
        $currentYear = date('Y');
        $opsiTahun = [];
        // Membuat opsi dari 2 tahun lalu sampai 3 tahun ke depan
        for ($i = -2; $i <= 3; $i++) {
            $year = ($currentYear + $i);
            $opsiTahun[] = $year . '/' . ($year + 1);
        }
    @endphp

    <div class="px-5 pt-5">
        <div class="mb-4 flex flex-col gap-3">
            <div class="flex justify-start">
                <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center gap-2 text-sm shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Tahun Ajaran
                </button>
            </div>

            <div class="border-t border-gray-200 pt-3 flex flex-col gap-3">
                <div class="w-full overflow-x-auto">
                    {{ $tahunAjarans->links() }}
                </div>

                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex flex-wrap items-center gap-2">
                        @include('admin.components.per-page-selector')
                        @if(request('tahun_ajaran') || request('semester'))
                            <a href="{{ route('admin.tahun-ajaran', array_filter(['per_page' => request('per_page')])) }}" class="bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 font-medium py-2 px-4 rounded-lg flex items-center gap-2 text-sm shadow-sm transition-all">
                                Reset Filter
                            </a>
                        @endif
                    </div>
                    <button onclick="document.getElementById('modalFilter').classList.remove('hidden')" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2 px-4 rounded-lg flex items-center gap-2 text-sm shadow-sm transition-all">
                        Filter Data
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto bg-white rounded-xl border border-gray-200 shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Tahun Ajaran</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase">Semester</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($tahunAjarans ?? [] as $index => $ta)
                    <tr class="odd:bg-white even:bg-gray-50 hover:bg-blue-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tahunAjarans->firstItem() + $index }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800">{{ $ta->tahun_ajaran }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold text-blue-600">{{ $ta->semester }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($ta->is_active)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> Aktif
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">Tidak Aktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Tombol Jadikan Aktif (atau placeholder) --}}
                                @if(!$ta->is_active)
                                <form action="{{ route('admin.tahun-ajaran.aktif', $ta->id) }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center justify-center w-9 h-9 bg-green-100 text-green-600 rounded-md hover:bg-green-600 hover:text-white transition-colors shadow-sm"
                                            title="Jadikan Aktif">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                </form>
                                @else
                                <div class="w-9 h-9"></div>
                                @endif

                                {{-- Tombol Edit --}}
                                <button type="button" onclick="document.getElementById('modalEdit{{ $ta->id }}').classList.remove('hidden')"
                                        class="inline-flex items-center justify-center w-9 h-9 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-700 hover:text-white transition-colors shadow-sm"
                                        title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>

                                {{-- Tombol Hapus (atau placeholder) --}}
                                @if(!$ta->is_active)
                                <form action="{{ route('admin.tahun-ajaran.destroy', $ta->id) }}" method="POST" class="m-0" onsubmit="return confirm('Yakin ingin menghapus?');">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center justify-center w-9 h-9 bg-red-100 text-red-600 rounded-md hover:bg-red-600 hover:text-white transition-colors shadow-sm"
                                            title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                                @else
                                <div class="w-9 h-9"></div>
                                @endif
                            </div>
                        </td>
                    </tr>

                    <div id="modalEdit{{ $ta->id }}" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm transition-opacity">
                        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 overflow-hidden text-left">
                            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                                <h3 class="text-lg font-bold text-gray-800">Edit Tahun Ajaran</h3>
                                <button type="button" onclick="document.getElementById('modalEdit{{ $ta->id }}').classList.add('hidden')" class="text-gray-400 hover:text-red-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                            </div>
                            <form action="{{ route('admin.tahun-ajaran.update', $ta->id) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="p-6 space-y-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun Ajaran</label>
                                        <select name="tahun_ajaran" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                                            @foreach($opsiTahun as $th)
                                                <option value="{{ $th }}" {{ $ta->tahun_ajaran == $th ? 'selected' : '' }}>{{ $th }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Semester</label>
                                        <select name="semester" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                                            <option value="Ganjil" {{ $ta->semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                            <option value="Genap" {{ $ta->semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-2">
                                    <button type="button" onclick="document.getElementById('modalEdit{{ $ta->id }}').classList.add('hidden')" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium">Batal</button>
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @empty
                    <tr><td colspan="5" class="px-6 py-8 text-sm text-gray-400 text-center italic">Belum ada data Tahun Ajaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="modalTambah" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 overflow-hidden text-left">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Tambah Tahun Ajaran</h3>
                <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="text-gray-400 hover:text-red-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form action="{{ route('admin.tahun-ajaran.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun Ajaran</label>
                        <select name="tahun_ajaran" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            @foreach($opsiTahun as $th)
                                <option value="{{ $th }}">{{ $th }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Semester</label>
                        <select name="semester" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                            <option value="">-- Pilih Semester --</option>
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalFilter" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 overflow-hidden text-left">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Filter Data</h3>
                <button type="button" onclick="document.getElementById('modalFilter').classList.add('hidden')" class="text-gray-400 hover:text-red-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form action="{{ route('admin.tahun-ajaran') }}" method="GET">
                @if(request()->filled('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Filter Tahun Ajaran</label>
                        <select name="tahun_ajaran" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                            <option value="">-- Semua Tahun --</option>
                            @foreach($opsiTahun as $th)
                                <option value="{{ $th }}" {{ request('tahun_ajaran') == $th ? 'selected' : '' }}>{{ $th }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Filter Semester</label>
                        <select name="semester" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                            <option value="">-- Semua Semester --</option>
                            <option value="Ganjil" {{ request('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ request('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modalFilter').classList.add('hidden')" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session("success") }}', showConfirmButton: false, timer: 3000, customClass: { popup: 'rounded-xl' } });
            @endif
            @if($errors->any())
                Swal.fire({ icon: 'error', title: 'Perhatian!', html: `<ul class="text-left text-sm text-red-600 list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>`, confirmButtonColor: '#2563EB', customClass: { popup: 'rounded-xl' } });
            @endif
        });
    </script>
</x-app-layout>