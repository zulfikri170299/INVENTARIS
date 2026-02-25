<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <!-- Name -->
        <div class="group">
            <label for="name"
                class="block text-sm font-medium text-gray-400 mb-2 group-focus-within:text-blue-400 transition-colors">
                Nama Lengkap
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                    <i class="ph ph-identification-badge"></i>
                </div>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}"
                    class="block w-full pl-11 pr-4 py-3 bg-gray-900/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all outline-none"
                    required autofocus autocomplete="name" />
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email -->
        <div class="group">
            <label for="email"
                class="block text-sm font-medium text-gray-400 mb-2 group-focus-within:text-blue-400 transition-colors">
                Alamat Email
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                    <i class="ph ph-envelope"></i>
                </div>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
                    class="block w-full pl-11 pr-4 py-3 bg-gray-900/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all outline-none"
                    required autocomplete="username" />
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-yellow-500/10 rounded-xl border border-yellow-500/20">
                    <p class="text-xs text-yellow-500 flex items-center font-medium">
                        <i class="ph ph-warning-circle mr-1.5 text-sm"></i>
                        {{ __('Email Anda belum diverifikasi.') }}
                        <button form="send-verification" class="ml-auto underline hover:text-yellow-400 focus:outline-none">
                            {{ __('Klik untuk kirim ulang verifikasi.') }}
                        </button>
                    </p>
                </div>
            @endif
        </div>

        <div class="flex items-center pt-2 gap-4">
            <button type="submit"
                class="flex items-center px-6 py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:bg-blue-500 hover:scale-[1.02] active:scale-[0.98] transition-all">
                <i class="ph ph-floppy-disk mr-2"></i>
                Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm text-green-400 font-medium flex items-center bg-green-500/10 px-3 py-1.5 rounded-full border border-green-500/20">
                    <i class="ph ph-check-circle mr-1.5"></i> Profil diperbarui
                </p>
            @endif
        </div>
    </form>
</section>