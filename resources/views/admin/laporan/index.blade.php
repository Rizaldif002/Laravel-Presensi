<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Laporan Presensi</h2>
    </x-slot>

    <div class="px-5 pt-5" x-data="{ modalFilter: false }">

        {{-- Toolbar --}}
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-3">
                @include('admin.components.per-page-selector')
                @if(request()->hasAny(['tahun_ajaran_id','dosen_id','mata_kuliah_id']))
                    <a href="{{ route('admin.laporan.presensi', array_filter(['per_page' => request('per_page')])) }}"
                       class="inline-flex items-center gap-1 px-3 py-2 bg-red-50 border border-red-200 text-red-600 hover:bg-red-100 rounded-lg text-sm font-medium transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reset
                    </a>
                @endif
            </div>
            <button @click="modalFilter = true"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white border text-gray-700 hover:bg-gray-50 rounded-lg text-sm font-medium shadow-sm transition-all whitespace-nowrap
                {{ request()->hasAny(['tahun_ajaran_id','dosen_id','mata_kuliah_id']) ? 'ring-2 ring-blue-400 border-blue-400' : 'border-gray-300' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                Filter Laporan
                @php $filterCount = collect(['tahun_ajaran_id','dosen_id','mata_kuliah_id'])->filter(fn($k) => request()->filled($k))->count(); @endphp
                @if($filterCount > 0)
                    <span class="inline-flex items-center justify-center w-4 h-4 bg-blue-500 text-white rounded-full text-xs font-bold">{{ $filterCount }}</span>
                @endif
            </button>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto bg-white rounded-xl border border-gray-200 shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Mata Kuliah</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Kelas</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Dosen</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Tahun Ajaran</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Pertemuan</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Peserta</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Export PDF</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($kelasList as $i => $k)
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-4 py-4 text-sm font-medium text-gray-500">{{ $kelasList->firstItem() + $i }}</td>
                        <td class="px-4 py-4">
                            <p class="text-sm font-bold text-gray-800">{{ $k->mataKuliah->nama_mk ?? '-' }}</p>
                            <p class="text-xs text-gray-400">{{ $k->mataKuliah->kode_mk ?? '' }}</p>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 font-bold rounded-md text-xs">{{ $k->nama_kelas }}</span>
                        </td>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-700">{{ $k->dosen->nama_dosen ?? '-' }}</td>
                        <td class="px-4 py-4 text-center">
                            <p class="text-sm font-bold text-gray-700">{{ $k->tahunAjaran->tahun_ajaran ?? '-' }}</p>
                            <p class="text-xs text-blue-500 font-semibold">{{ $k->tahunAjaran->semester ?? '' }}</p>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100 text-sm font-bold shadow-sm">
                                {{ $k->total_pertemuan }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 text-sm font-bold shadow-sm">
                                {{ $k->total_peserta ?? 0 }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <a href="{{ route('admin.laporan.pdf', $k->id) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-md shadow-sm transition-colors"
                               title="Download PDF Laporan Kehadiran">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                PDF
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-16 text-center text-sm font-medium text-gray-400 italic">
                            Belum ada kelas dengan sesi presensi selesai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 pb-4 flex justify-end">
            {{ $kelasList->links() }}
        </div>

        {{-- Modal Filter --}}
        <div x-show="modalFilter" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" style="display:none">
            <div @click.outside="modalFilter = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-base font-bold text-gray-800">Filter Laporan</h3>
                    <button @click="modalFilter = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form action="{{ route('admin.laporan.presensi') }}" method="GET" class="space-y-4">
                    @foreach(request()->except(['tahun_ajaran_id','dosen_id','mata_kuliah_id','page']) as $key => $val)
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endforeach

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tahun Ajaran</label>
                        <select name="tahun_ajaran_id" class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm text-gray-700 bg-gray-50 focus:bg-white focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Tahun Ajaran</option>
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->tahun_ajaran }} — {{ $ta->semester }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Dosen</label>
                        <select name="dosen_id" class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm text-gray-700 bg-gray-50 focus:bg-white focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Dosen</option>
                            @foreach($dosens as $d)
                                <option value="{{ $d->id }}" {{ request('dosen_id') == $d->id ? 'selected' : '' }}>{{ $d->nama_dosen }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Mata Kuliah</label>
                        <select name="mata_kuliah_id" class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm text-gray-700 bg-gray-50 focus:bg-white focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Mata Kuliah</option>
                            @foreach($mataKuliahs as $mk)
                                <option value="{{ $mk->id }}" {{ request('mata_kuliah_id') == $mk->id ? 'selected' : '' }}>{{ $mk->nama_mk }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('admin.laporan.presensi') }}" class="flex-1 text-center py-2 px-4 border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-100 transition-all">Reset</a>
                        <button type="submit" class="flex-1 py-2 px-4 bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/20 text-white rounded-lg text-sm font-bold transition-all">Terapkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
