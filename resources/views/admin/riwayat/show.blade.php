<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('admin.riwayat.index') }}"
                       class="text-gray-400 hover:text-blue-600 transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        <span class="text-sm font-medium">Kembali</span>
                    </a>
                </div>
                <h2 class="text-xl font-bold text-gray-800 leading-tight">
                    Presensi Mahasiswa
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="px-5 pt-5 pb-8" 
         x-data="{ 
            search: '', 
            showOverrideModal: false, 
            mhsName: '', 
            sesiId: '', 
            mhsId: '',
            currentStatus: '',
            openModal(sId, mId, name, status) {
                this.sesiId = sId;
                this.mhsId = mId;
                this.mhsName = name;
                this.currentStatus = status;
                this.showOverrideModal = true;
            }
         }">

        {{-- Info Kelas --}}
        <div class="bg-white rounded-xl border-l-4 border-l-blue-600 border border-gray-200 shadow-sm p-5 mb-8">
            <table class="text-sm text-gray-800">
                <tr>
                    <td class="pr-6 pb-2 font-semibold text-gray-500">Mata Kuliah</td>
                    <td class="pb-2 font-bold uppercase">: {{ $kelas->mataKuliah->nama_mk ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="pr-6 pb-2 font-semibold text-gray-500">Kelas</td>
                    <td class="pb-2 font-bold">: {{ $kelas->nama_kelas }}</td>
                </tr>
                <tr>
                    <td class="pr-6 pb-2 font-semibold text-gray-500">Dosen Pengampu</td>
                    <td class="pb-2 font-bold">: {{ $kelas->dosen->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="pr-6 font-semibold text-gray-500">Tahun Ajaran</td>
                    <td class="font-bold">: {{ $kelas->tahunAjaran->tahun_ajaran ?? '-' }} - {{ $kelas->tahunAjaran->semester ?? '' }}</td>
                </tr>
            </table>
        </div>

        {{-- Toolbar --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-4">
            <div class="flex items-center gap-3">
                <button class="px-6 py-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold rounded-md shadow-sm transition-colors">
                    Tampilkan
                </button>
                <button class="px-6 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-green-600 text-sm font-semibold rounded-md shadow-sm transition-colors">
                    Print
                </button>
            </div>

            <div class="relative max-w-xs w-full">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input x-model="search" type="text" placeholder="Cari nama atau NIM..."
                       class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 bg-white focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        {{-- Tabel Matriks --}}
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto border border-dashed border-gray-300 p-2 rounded-lg">
                <table class="min-w-full text-xs border-collapse">
                    <thead>
                        <tr>
                            <th rowspan="2" class="px-3 py-4 text-center font-bold text-gray-700 border-b border-gray-200 align-middle w-10">
                                No.
                            </th>
                            <th rowspan="2" class="px-4 py-4 text-left font-bold text-gray-700 border-b border-gray-200 align-middle min-w-[220px]">
                                Mahasiswa
                            </th>
                            <th colspan="16" class="px-1 pt-4 pb-2 text-center font-bold text-gray-700 border-b border-gray-100">
                                Pertemuan
                            </th>
                        </tr>
                        <tr>
                            @for($i = 1; $i <= 16; $i++)
                                <th class="px-1 pb-4 pt-2 text-center font-bold text-gray-800 border-b border-gray-200 w-8">
                                    {{ $i }}
                                </th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($mahasiswas as $no => $m)
                            @php
                                $row = $matrix[$m->id] ?? [];
                                $nameLower = strtolower($m->nama_lengkap);
                                $nim = $m->nim ?? '';
                            @endphp
                            <tr class="border-b border-dashed border-gray-200 hover:bg-gray-50 transition-colors"
                                x-show="search === '' || '{{ $nameLower }}'.includes(search.toLowerCase()) || '{{ $nim }}'.includes(search)">

                                <td class="px-3 py-5 text-center font-medium text-gray-500">{{ $no + 1 }}</td>

                                <td class="px-4 py-4">
                                    <div class="text-gray-500 text-[11px] mb-0.5">{{ $m->nim ?? '-' }}</div>
                                    <div class="font-bold text-gray-800 uppercase text-[12px]">{{ $m->nama_lengkap }}</div>
                                </td>

                                @for($i = 0; $i < 16; $i++)
                                    <td class="px-1 py-4 text-center">
                                        @php $sesi = $sesiList[$i] ?? null; @endphp
                                        @if($sesi)
                                            @php $status = $row['sesi'][$sesi->id] ?? 'A'; @endphp
                                            <button type="button"
                                                    @click="openModal('{{ $sesi->id }}', '{{ $m->id }}', '{{ $m->nama_lengkap }}', '{{ $status }}')"
                                                    class="focus:outline-none transition-transform hover:scale-125">
                                                @if($status === 'H')
                                                    <svg class="w-5 h-5 text-green-500 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                @elseif($status === 'S')
                                                    <span class="font-bold text-blue-500 text-sm">S</span>
                                                @elseif($status === 'I')
                                                    <span class="font-bold text-yellow-500 text-sm">I</span>
                                                @else
                                                    {{-- Logo X Merah untuk Alpa --}}
                                                    <svg class="w-4 h-4 text-red-500 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                @endif
                                            </button>
                                        @else
                                            <span class="text-gray-200">-</span>
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        @empty
                            <tr>
                                <td colspan="18" class="px-4 py-16 text-center text-gray-400 italic font-medium">
                                    Belum ada mahasiswa yang terdaftar di kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- MODAL OVERRIDE (Pop-up Respon) --}}
        <div x-show="showOverrideModal" 
             x-cloak 
             class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            
            <div @click.away="showOverrideModal = false" 
                 class="bg-white rounded-[1rem] shadow-2xl w-full max-w-md p-8 text-center transform transition-all border border-gray-100">
                
                <div class="w-20 h-20 rounded-full border-4 border-orange-100 text-orange-400 flex items-center justify-center mx-auto mb-5 bg-orange-50">
                    <span class="text-4xl font-bold italic">!</span>
                </div>

                <h3 class="text-lg font-bold text-gray-800 mb-2 leading-tight">
                    Apakah anda ingin mengabsenkan mahasiswa ini?
                </h3>
                <p class="text-sm text-gray-500 mb-8 px-4">
                    Anda akan melakukan perubahan status absen <br>
                    <span class="font-bold text-gray-900 mt-1 block" x-text="mhsName"></span>
                </p>

                <form action="{{ route('admin.riwayat.override') }}" method="POST">
                    @csrf
                    <input type="hidden" name="sesi_id" x-model="sesiId">
                    <input type="hidden" name="mahasiswa_id" x-model="mhsId">
                    
                    {{-- Tombol Sejajar Satu Baris --}}
                    <div class="flex items-center justify-center gap-2 overflow-x-auto pb-2">
                        <button type="button" @click="showOverrideModal = false" 
                                class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold rounded-lg transition-all min-w-[70px]">
                            Batal
                        </button>
                        <button type="submit" name="status" value="S" 
                                class="px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-bold rounded-lg shadow-lg shadow-blue-500/20 min-w-[70px]">
                            Sakit
                        </button>
                        <button type="submit" name="status" value="I" 
                                class="px-4 py-2.5 bg-yellow-400 hover:bg-yellow-500 text-white text-xs font-bold rounded-lg shadow-lg shadow-yellow-400/20 min-w-[70px]">
                            Izin
                        </button>
                        <button type="submit" name="status" value="H" 
                                class="px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white text-xs font-bold rounded-lg shadow-lg shadow-green-500/20 min-w-[70px]">
                            Hadir
                        </button>
                        <button type="submit" name="status" value="A" 
                                class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded-lg shadow-lg shadow-red-500/20 min-w-[70px]">
                            Alpa
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>