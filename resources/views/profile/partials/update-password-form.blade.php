<section>
    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <!-- Current Password -->
        <div class="group" x-data="{ show: false }">
            <label for="update_password_current_password"
                class="block text-sm font-medium text-gray-400 mb-2 group-focus-within:text-yellow-400 transition-colors">
                Kata Sandi Saat Ini
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                    <i class="ph ph-key"></i>
                </div>
                <input id="update_password_current_password" name="current_password" :type="show ? 'text' : 'password'"
                    class="block w-full pl-11 pr-12 py-3 bg-gray-900/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-yellow-500/50 focus:border-yellow-500 transition-all outline-none"
                    placeholder="••••••••" autocomplete="current-password" />
                <button type="button" @click="show = !show"
                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300 transition-colors focus:outline-none z-10">
                    <i class="ph ph-eye text-xl" x-show="!show"></i>
                    <i class="ph ph-eye-slash text-xl" x-show="show" x-cloak></i>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <!-- New Password -->
        <div class="group" x-data="{ show: false }">
            <label for="update_password_password"
                class="block text-sm font-medium text-gray-400 mb-2 group-focus-within:text-yellow-400 transition-colors">
                Kata Sandi Baru
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                    <i class="ph ph-shield-plus"></i>
                </div>
                <input id="update_password_password" name="password" :type="show ? 'text' : 'password'"
                    class="block w-full pl-11 pr-12 py-3 bg-gray-900/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-yellow-500/50 focus:border-yellow-500 transition-all outline-none"
                    placeholder="Minimal 8 karakter" autocomplete="new-password" />
                <button type="button" @click="show = !show"
                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300 transition-colors focus:outline-none z-10">
                    <i class="ph ph-eye text-xl" x-show="!show"></i>
                    <i class="ph ph-eye-slash text-xl" x-show="show" x-cloak></i>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="group" x-data="{ show: false }">
            <label for="update_password_password_confirmation"
                class="block text-sm font-medium text-gray-400 mb-2 group-focus-within:text-yellow-400 transition-colors">
                Konfirmasi Kata Sandi Baru
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                    <i class="ph ph-check-square-offset"></i>
                </div>
                <input id="update_password_password_confirmation" name="password_confirmation"
                    :type="show ? 'text' : 'password'"
                    class="block w-full pl-11 pr-12 py-3 bg-gray-900/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-yellow-500/50 focus:border-yellow-500 transition-all outline-none"
                    placeholder="Ketik ulang kata sandi baru" autocomplete="new-password" />
                <button type="button" @click="show = !show"
                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300 transition-colors focus:outline-none z-10">
                    <i class="ph ph-eye text-xl" x-show="!show"></i>
                    <i class="ph ph-eye-slash text-xl" x-show="show" x-cloak></i>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center pt-2 gap-4">
            <button type="submit"
                class="flex items-center px-6 py-3 bg-gradient-to-r from-red-600 to-yellow-500 text-white font-bold rounded-xl shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 hover:scale-[1.02] active:scale-[0.98] transition-all">
                <i class="ph ph-arrows-counter-clockwise mr-2"></i>
                Perbarui Kata Sandi
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm text-green-400 font-medium flex items-center bg-green-500/10 px-3 py-1.5 rounded-full border border-green-500/20">
                    <i class="ph ph-check-circle mr-1.5"></i> Berhasil disimpan
                </p>
            @endif
        </div>
    </form>
</section>