<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Monitor Sesi Presensi</h2>
    </x-slot>

    <div class="px-5 pt-5">

        {{-- Stat cards --}}
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-emerald-200 shadow-sm p-5 flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Sesi Aktif</p>
                    <p class="text-2xl font-extrabold text-emerald-600">{{ $statAktif }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Sesi Selesai</p>
                    <p class="text-2xl font-extrabold text-gray-700">{{ $statSelesai }}</p>
                </div>
            </div>
        </div>

        {{-- Tabel monitor --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex flex-wrap items-center justify-between gap-3">
                <h3 class="text-base font-bold text-gray-700">Sesi Presensi</h3>
                <form action="{{ route('admin.sesi.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                    <select name="status" onchange="this.form.submit()" class="border border-gray-300 rounded-lg py-1.5 pl-3 pr-7 text-xs text-gray-700 bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Status</option>
                        <option value="aktif"   {{ request('status') === 'aktif'   ? 'selected' : '' }}>Aktif</option>
                        <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                    <select name="dosen_id" onchange="this.form.submit()" class="border border-gray-300 rounded-lg py-1.5 pl-3 pr-7 text-xs text-gray-700 bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Dosen</option>
                        @foreach($dosens as $d)
                            <option value="{{ $d->id }}" {{ request('dosen_id') == $d->id ? 'selected' : '' }}>{{ $d->nama_dosen }}</option>
                        @endforeach
                    </select>
                    @if(request()->hasAny(['status','dosen_id']))
                        <a href="{{ route('admin.sesi.index') }}" class="text-xs text-red-500 hover:text-red-700 font-medium">Reset</a>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Mata Kuliah</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Dosen</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Pertemuan</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Waktu Buka</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Hadir</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($sesis as $i => $sesi)
                        <tr class="hover:bg-gray-50 transition-colors {{ $sesi->status === 'aktif' ? 'bg-emerald-50/30' : '' }}">
                            <td class="px-4 py-4 text-sm text-gray-500">{{ $sesis->firstItem() + $i }}</td>
                            <td class="px-4 py-4">
                                <p class="text-sm font-bold text-gray-800">{{ $sesi->jadwalPerkuliahan->kelasPerkuliahan->mataKuliah->nama_mk ?? '-' }}</p>
                                <p class="text-xs text-gray-500">Kelas {{ $sesi->jadwalPerkuliahan->kelasPerkuliahan->nama_kelas ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $sesi->jadwalPerkuliahan->kelasPerkuliahan->dosen->nama_dosen ?? '-' }}</td>
                            <td class="px-4 py-4 text-sm text-gray-600">{{ $sesi->nama_pertemuan ?? '-' }}</td>
                            <td class="px-4 py-4 text-center whitespace-nowrap text-xs text-gray-600">
                                {{ \Carbon\Carbon::parse($sesi->waktu_buka)->format('d M Y, H:i') }}
                            </td>
                            <td class="px-4 py-4 text-center text-sm font-bold text-gray-700">{{ $sesi->presensis->count() }}</td>
                            <td class="px-4 py-4 text-center">
                                @if($sesi->status === 'aktif')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>Aktif
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Selesai</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                <a href="{{ route('admin.sesi.live', $sesi->id) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-medium rounded-full shadow-sm transition-all">
                                    Monitor
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-sm text-gray-400">Belum ada sesi presensi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 px-4 pb-4 flex justify-end">
                {{ $sesis->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
