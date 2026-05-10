<x-app-layout>
    <x-slot name="header">Profil Saya</x-slot>
    @section('title', 'Profil Saya')

    @php
        $user = auth()->user();

        $initials = collect(explode(' ', $user->name))
            ->filter()
            ->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))
            ->take(2)
            ->implode('');

        $avatarClass = $user->isAdmin()
            ? 'bg-blue-100 text-blue-700 ring-blue-200'
            : ($user->isDosen()
                ? 'bg-emerald-100 text-emerald-700 ring-emerald-200'
                : 'bg-purple-100 text-purple-700 ring-purple-200');

        $badgeClass = $user->isAdmin()
            ? 'bg-blue-100 text-blue-700'
            : ($user->isDosen()
                ? 'bg-emerald-100 text-emerald-700'
                : 'bg-purple-100 text-purple-700');

        // Tab aktif: ikuti status session, atau buka tab yang ada error-nya
        $activeTab = match(session('status')) {
            'akun-updated'    => 'akun',
            'foto-updated'    => 'foto',
            default           => 'profil',
        };

        if ($errors->has('email') || $errors->has('password')) {
            $activeTab = 'akun';
        } elseif ($errors->has('foto_profil')) {
            $activeTab = 'foto';
        }
    @endphp

    {{-- Layout 2 Kolom --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6"
         x-data="{ tab: '{{ $activeTab }}' }">

        {{-- ═══════════════════════════════ --}}
        {{-- KOLOM KIRI: Kartu Profil        --}}
        {{-- ═══════════════════════════════ --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center text-center">

                {{-- Avatar --}}
                @if($user->foto_profil)
                    <img src="{{ Storage::url($user->foto_profil) }}"
                         alt="{{ $user->name }}"
                         class="w-24 h-24 rounded-full object-cover ring-4 {{ $avatarClass }} mb-4">
                @else
                    <div class="w-24 h-24 rounded-full flex items-center justify-center text-2xl font-bold ring-4 mb-4 {{ $avatarClass }}">
                        {{ $initials }}
                    </div>
                @endif

                <h3 class="text-lg font-bold text-gray-800 leading-tight">{{ $user->name }}</h3>

                <span class="mt-2 inline-flex items-center px-3 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                    {{ ucfirst($user->role) }}
                </span>

                <hr class="w-full my-5 border-gray-100">

                {{-- Info List --}}
                <ul class="w-full space-y-4 text-left text-sm">

                    {{-- NIM / NIP --}}
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 p-1.5 rounded-md bg-gray-100 flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">
                                {{ $user->isMahasiswa() ? 'NIM' : 'NIP' }}
                            </p>
                            <p class="text-gray-700 font-medium break-all">{{ $user->nim ?? '-' }}</p>
                        </div>
                    </li>

                    {{-- Email --}}
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 p-1.5 rounded-md bg-gray-100 flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Email</p>
                            <p class="text-gray-700 font-medium break-all">{{ $user->email }}</p>
                        </div>
                    </li>

                    {{-- Role --}}
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 p-1.5 rounded-md bg-gray-100 flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </span>
                        <div>
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Role</p>
                            <p class="text-gray-700 font-medium">{{ ucfirst($user->role) }}</p>
                        </div>
                    </li>

                </ul>
            </div>
        </div>

        {{-- ═══════════════════════════════ --}}
        {{-- KOLOM KANAN: Form dengan Tabs  --}}
        {{-- ═══════════════════════════════ --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

                {{-- Tab Header --}}
                <div class="border-b border-gray-100 px-6 flex gap-0">
                    @foreach([['key' => 'profil', 'label' => 'Edit Profil'], ['key' => 'akun', 'label' => 'Edit Akun'], ['key' => 'foto', 'label' => 'Edit Foto']] as $t)
                        <button
                            @click="tab = '{{ $t['key'] }}'"
                            :class="tab === '{{ $t['key'] }}'
                                ? 'border-b-2 border-blue-600 text-blue-600 font-semibold bg-blue-50/50'
                                : 'text-gray-400 hover:text-gray-600 hover:bg-gray-50'"
                            class="px-5 py-3.5 text-sm transition-all duration-200 -mb-px">
                            {{ $t['label'] }}
                        </button>
                    @endforeach
                </div>

                <div class="p-6 sm:p-8">

                    {{-- ─────────────────────────── --}}
                    {{-- TAB 1: Edit Profil           --}}
                    {{-- ─────────────────────────── --}}
                    <div x-show="tab === 'profil'" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">

                        <div class="mb-6">
                            <h3 class="text-base font-bold text-gray-800">Data Personal</h3>
                            <p class="text-sm text-gray-500 mt-0.5">Perbarui nama lengkap dan nomor identitas Anda.</p>
                        </div>

                        @if(session('status') === 'profile-updated')
                            <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm flex items-center gap-2.5">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Data profil berhasil diperbarui.
                            </div>
                        @endif

                        <form method="POST" action="{{ route('profile.update') }}"
                              x-data="{ confirmed: false }">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="_form" value="profil">

                            <div class="space-y-5">

                                {{-- Nama Lengkap --}}
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                                        Nama Lengkap
                                    </label>
                                    <input type="text" id="name" name="name"
                                           value="{{ old('name', $user->name) }}"
                                           autocomplete="name"
                                           placeholder="Masukkan nama lengkap"
                                           class="w-full px-4 py-2.5 rounded-lg border text-sm text-gray-800 placeholder-gray-400 transition-all duration-200
                                                  {{ $errors->has('name') ? 'border-red-400 bg-red-50 focus:ring-red-400' : 'border-gray-300 focus:ring-blue-500' }}
                                                  focus:outline-none focus:ring-2 focus:border-transparent">
                                    @error('name')
                                        <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- NIM / NIP --}}
                                <div>
                                    <label for="nim" class="block text-sm font-medium text-gray-700 mb-1.5">
                                        {{ $user->isMahasiswa() ? 'NIM' : 'NIP' }}
                                        <span class="text-gray-400 font-normal">(Opsional)</span>
                                    </label>
                                    <input type="text" id="nim" name="nim"
                                           value="{{ old('nim', $user->nim) }}"
                                           placeholder="{{ $user->isMahasiswa() ? 'Nomor Induk Mahasiswa' : 'Nomor Induk Pegawai' }}"
                                           class="w-full px-4 py-2.5 rounded-lg border text-sm text-gray-800 placeholder-gray-400 transition-all duration-200
                                                  {{ $errors->has('nim') ? 'border-red-400 bg-red-50 focus:ring-red-400' : 'border-gray-300 focus:ring-blue-500' }}
                                                  focus:outline-none focus:ring-2 focus:border-transparent">
                                    @error('nim')
                                        <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Checkbox Konfirmasi --}}
                            <div class="mt-6 flex items-start gap-2.5">
                                <input type="checkbox" id="confirm-profil" x-model="confirmed"
                                       class="mt-0.5 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                <label for="confirm-profil" class="text-sm text-gray-600 cursor-pointer leading-snug">
                                    Saya yakin akan mengubah data tersebut.
                                </label>
                            </div>

                            <div class="mt-6">
                                <button type="submit"
                                        :disabled="!confirmed"
                                        :class="confirmed
                                            ? 'bg-green-600 hover:bg-green-700 shadow-sm hover:shadow-md cursor-pointer'
                                            : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold text-white transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- ─────────────────────────── --}}
                    {{-- TAB 2: Edit Akun            --}}
                    {{-- ─────────────────────────── --}}
                    <div x-show="tab === 'akun'" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">

                        <div class="mb-6">
                            <h3 class="text-base font-bold text-gray-800">Kredensial Akun</h3>
                            <p class="text-sm text-gray-500 mt-0.5">Perbarui alamat email dan password akun Anda.</p>
                        </div>

                        @if(session('status') === 'akun-updated')
                            <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm flex items-center gap-2.5">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Data akun berhasil diperbarui.
                            </div>
                        @endif

                        <form method="POST" action="{{ route('profile.update') }}"
                              x-data="{ confirmed: false }">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="_form" value="akun">

                            <div class="space-y-5">

                                {{-- Email --}}
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                                    <input type="email" id="email" name="email"
                                           value="{{ old('email', $user->email) }}"
                                           autocomplete="email"
                                           placeholder="alamat@email.com"
                                           class="w-full px-4 py-2.5 rounded-lg border text-sm text-gray-800 placeholder-gray-400 transition-all duration-200
                                                  {{ $errors->has('email') ? 'border-red-400 bg-red-50 focus:ring-red-400' : 'border-gray-300 focus:ring-blue-500' }}
                                                  focus:outline-none focus:ring-2 focus:border-transparent">
                                    @error('email')
                                        <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Password Baru --}}
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                                        Password Baru
                                        <span class="text-gray-400 font-normal">(Opsional)</span>
                                    </label>
                                    <input type="password" id="password" name="password"
                                           autocomplete="new-password"
                                           placeholder="Kosongkan jika tidak ingin mengubah"
                                           class="w-full px-4 py-2.5 rounded-lg border text-sm text-gray-800 placeholder-gray-400 transition-all duration-200
                                                  {{ $errors->has('password') ? 'border-red-400 bg-red-50 focus:ring-red-400' : 'border-gray-300 focus:ring-blue-500' }}
                                                  focus:outline-none focus:ring-2 focus:border-transparent">
                                    @error('password')
                                        <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Konfirmasi Password --}}
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
                                        Konfirmasi Password Baru
                                    </label>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                           autocomplete="new-password"
                                           placeholder="Ulangi password baru"
                                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm text-gray-800 placeholder-gray-400 transition-all duration-200
                                                  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>

                            {{-- Checkbox Konfirmasi --}}
                            <div class="mt-6 flex items-start gap-2.5">
                                <input type="checkbox" id="confirm-akun" x-model="confirmed"
                                       class="mt-0.5 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                <label for="confirm-akun" class="text-sm text-gray-600 cursor-pointer leading-snug">
                                    Saya yakin akan mengubah data tersebut.
                                </label>
                            </div>

                            <div class="mt-6">
                                <button type="submit"
                                        :disabled="!confirmed"
                                        :class="confirmed
                                            ? 'bg-green-600 hover:bg-green-700 shadow-sm hover:shadow-md cursor-pointer'
                                            : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold text-white transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- ─────────────────────────── --}}
                    {{-- TAB 3: Edit Foto            --}}
                    {{-- ─────────────────────────── --}}
                    <div x-show="tab === 'foto'" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">

                        <div class="mb-6">
                            <h3 class="text-base font-bold text-gray-800">Foto Profil</h3>
                            <p class="text-sm text-gray-500 mt-0.5">Upload foto profil Anda. Format JPG / PNG, maksimal 2 MB.</p>
                        </div>

                        @if(session('status') === 'foto-updated')
                            <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm flex items-center gap-2.5">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Foto profil berhasil diperbarui.
                            </div>
                        @endif

                        <form method="POST" action="{{ route('profile.update-foto') }}"
                              enctype="multipart/form-data"
                              x-data="{ confirmed: false, preview: null, fileName: '' }">
                            @csrf

                            <div class="flex flex-col sm:flex-row items-start gap-6">

                                {{-- Preview Avatar --}}
                                <div class="flex-shrink-0 flex flex-col items-center gap-2">
                                    <div class="w-24 h-24 rounded-full overflow-hidden ring-4 {{ $avatarClass }}">
                                        <template x-if="preview">
                                            <img :src="preview" class="w-full h-full object-cover" alt="Preview">
                                        </template>
                                        <template x-if="!preview">
                                            <div class="w-full h-full flex items-center justify-center">
                                                @if($user->foto_profil)
                                                    <img src="{{ Storage::url($user->foto_profil) }}"
                                                         class="w-full h-full object-cover" alt="{{ $user->name }}">
                                                @else
                                                    <span class="text-2xl font-bold {{ str_replace('ring-', 'text-', $avatarClass) }}">{{ $initials }}</span>
                                                @endif
                                            </div>
                                        </template>
                                    </div>
                                    <p class="text-xs text-gray-400">Preview</p>
                                </div>

                                {{-- Upload Area --}}
                                <div class="flex-1 w-full">
                                    <label for="foto_profil"
                                           class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed rounded-xl cursor-pointer transition-all duration-200
                                                  {{ $errors->has('foto_profil') ? 'border-red-400 bg-red-50' : 'border-gray-300 hover:border-blue-400 hover:bg-blue-50/40' }}">
                                        <div class="flex flex-col items-center gap-2 px-4 text-center">
                                            <svg class="w-9 h-9 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <p class="text-sm text-gray-600 font-medium"
                                               x-text="fileName ? fileName : 'Klik untuk memilih foto'"></p>
                                            <p class="text-xs text-gray-400">JPG, JPEG, PNG — maks. 2 MB</p>
                                        </div>
                                    </label>
                                    <input type="file" id="foto_profil" name="foto_profil"
                                           accept="image/jpeg,image/png,image/jpg"
                                           class="hidden"
                                           @change="
                                               const file = $event.target.files[0];
                                               if (file) {
                                                   fileName = file.name;
                                                   const reader = new FileReader();
                                                   reader.onload = e => preview = e.target.result;
                                                   reader.readAsDataURL(file);
                                               }
                                           ">
                                    @error('foto_profil')
                                        <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Checkbox Konfirmasi --}}
                            <div class="mt-6 flex items-start gap-2.5">
                                <input type="checkbox" id="confirm-foto" x-model="confirmed"
                                       class="mt-0.5 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                <label for="confirm-foto" class="text-sm text-gray-600 cursor-pointer leading-snug">
                                    Saya yakin akan mengganti foto profil.
                                </label>
                            </div>

                            <div class="mt-6">
                                <button type="submit"
                                        :disabled="!confirmed || !preview"
                                        :class="(confirmed && preview)
                                            ? 'bg-green-600 hover:bg-green-700 shadow-sm hover:shadow-md cursor-pointer'
                                            : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold text-white transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    Simpan Foto
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

    </div>
</x-app-layout>
