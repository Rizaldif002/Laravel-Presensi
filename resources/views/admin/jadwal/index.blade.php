<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Manajemen Kelas Perkuliahan</h2>
    </x-slot>

    <div class="px-5 pt-5">
        <div class="mb-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h3 class="text-lg font-semibold text-gray-700">Kelas Perkuliahan</h3>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <button onclick="document.getElementById('modalTambahJadwal').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Jadwal
                </button>
            </div>
        </div>

        <div class="overflow-x-auto bg-white rounded-xl border border-gray-200 shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">Hari & Waktu</th>
                        <th class="px-4 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">Mata Kuliah</th>
                        <th class="px-4 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">Dosen</th>
                        <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-400 uppercase tracking-wider">Ruangan</th>
                        <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-400 uppercase tracking-wider">Presensi</th>
                        <th class="px-4 py-3 text-center text-[11px] font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($jadwals as $j)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="block text-sm font-bold text-gray-800">{{ $j->hari }}</span>
                            <span class="text-xs text-gray-500">{{ substr($j->jam_mulai,0,5) }} - {{ substr($j->jam_selesai,0,5) }} WITA</span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-800">{{ $j->kelasPerkuliahan->mataKuliah->nama_mk }}</div>
                            <div class="text-[10px] inline-flex items-center px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 font-bold mt-1 border border-blue-100 uppercase">
                                Kelas {{ $j->kelasPerkuliahan->nama_kelas }}
                            </div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600">
                            {{ $j->kelasPerkuliahan->dosen->nama_dosen }}
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="text-xs font-bold px-2 py-1 bg-gray-100 rounded text-gray-600 border border-gray-200">{{ $j->ruangan->nama_ruangan }}</span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <form action="{{ route('admin.sesi.buka') }}" method="POST">
                                @csrf
                                <input type="hidden" name="jadwal_perkuliahan_id" value="{{ $j->id }}">
                                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1 mx-auto transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 001.555-.832l-3-2z"/></svg>
                                    Mulai Absen
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" onclick="document.getElementById('modalEditJadwal{{ $j->id }}').classList.remove('hidden')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 rounded-full text-xs font-medium text-gray-700 hover:bg-gray-100 hover:text-blue-600 shadow-sm transition-all">
                                    Edit
                                </button>
                                
                                <form action="{{ route('admin.jadwal.destroy', $j->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?');">
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
                    <tr><td colspan="6" class="p-10 text-center text-gray-400 text-sm">Belum ada jadwal yang diatur.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="modalTambahJadwal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 mx-4">
            <div class="flex justify-between items-center mb-4 text-gray-800">
                <h3 class="text-xl font-bold">Tambah Jadwal</h3>
                <button onclick="document.getElementById('modalTambahJadwal').classList.add('hidden')" class="text-gray-400 hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('admin.jadwal.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pilih Kelas</label>
                    <select name="kelas_perkuliahan_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                        @foreach($kelases as $kls)
                            <option value="{{ $kls->id }}">{{ $kls->mataKuliah->nama_mk }} - Kelas {{ $kls->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Ruangan</label>
                        <select name="ruangan_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                            @foreach($ruangans as $rng)
                                <option value="{{ $rng->id }}">{{ $rng->nama_ruangan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Hari</label>
                        <select name="hari" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $h)
                                <option value="{{ $h }}">{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jam Mulai</label>
                        <input type="time" name="jam_mulai" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jam Selesai</label>
                        <input type="time" name="jam_selesai" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" onclick="document.getElementById('modalTambahJadwal').classList.add('hidden')" class="px-4 py-2 text-sm text-gray-500 font-bold uppercase tracking-wider">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-bold text-sm shadow-md hover:bg-blue-700 transition-all uppercase tracking-wider">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>

    @foreach($jadwals as $j)
    <div id="modalEditJadwal{{ $j->id }}" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 mx-4">
            <div class="flex justify-between items-center mb-4 text-gray-800">
                <h3 class="text-xl font-bold">Edit Jadwal</h3>
                <button type="button" onclick="document.getElementById('modalEditJadwal{{ $j->id }}').classList.add('hidden')" class="text-gray-400 hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('admin.jadwal.update', $j->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pilih Kelas</label>
                    <select name="kelas_perkuliahan_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                        @foreach($kelases as $kls)
                            <option value="{{ $kls->id }}" {{ $j->kelas_perkuliahan_id == $kls->id ? 'selected' : '' }}>
                                {{ $kls->mataKuliah->nama_mk }} - Kelas {{ $kls->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Ruangan</label>
                        <select name="ruangan_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                            @foreach($ruangans as $rng)
                                <option value="{{ $rng->id }}" {{ $j->ruangan_id == $rng->id ? 'selected' : '' }}>
                                    {{ $rng->nama_ruangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Hari</label>
                        <select name="hari" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $h)
                                <option value="{{ $h }}" {{ $j->hari == $h ? 'selected' : '' }}>{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jam Mulai</label>
                        <input type="time" name="jam_mulai" value="{{ substr($j->jam_mulai, 0, 5) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jam Selesai</label>
                        <input type="time" name="jam_selesai" value="{{ substr($j->jam_selesai, 0, 5) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm">
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" onclick="document.getElementById('modalEditJadwal{{ $j->id }}').classList.add('hidden')" class="px-4 py-2 text-sm text-gray-500 font-bold uppercase tracking-wider">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-yellow-500 text-white rounded-lg font-bold text-sm shadow-md hover:bg-yellow-600 transition-all uppercase tracking-wider">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

</x-app-layout>