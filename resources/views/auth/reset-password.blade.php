<x-guest-layout>
    <div class="glass-panel p-8 rounded-2xl shadow-2xl animate-fade-in-up">
        <!-- Logo / Header -->
        <div class="text-center mb-8 font-outfit">
            <h1
                class="text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-red-500 via-orange-500 to-yellow-500 tracking-tight">
                INVENLOG
            </h1>
            <p class="text-gray-400 text-sm mt-2">Atur Ulang Kata Sandi</p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div class="relative group">
                <input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus
                    autocomplete="username"
                    class="peer w-full px-4 py-3 rounded-xl bg-gray-800/50 border border-gray-700 text-gray-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all placeholder-transparent"
                    placeholder="Email Address" />
                <label for="email"
                    class="absolute left-4 -top-2.5 bg-[#131d33] px-1 text-sm text-gray-400 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 peer-placeholder-shown:top-3.5 peer-focus:-top-2.5 peer-focus:text-gray-200 peer-focus:text-sm">
                    Alamat Email
                </label>
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400" />
            </div>

            <!-- Password -->
            <div class="relative group">
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="peer w-full px-4 py-3 rounded-xl bg-gray-800/50 border border-gray-700 text-gray-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all placeholder-transparent"
                    placeholder="Password" />
                <label for="password"
                    class="absolute left-4 -top-2.5 bg-[#131d33] px-1 text-sm text-gray-400 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 peer-placeholder-shown:top-3.5 peer-focus:-top-2.5 peer-focus:text-gray-200 peer-focus:text-sm">
                    Kata Sandi Baru
                </label>
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400" />
            </div>

            <!-- Confirm Password -->
            <div class="relative group">
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    autocomplete="new-password"
                    class="peer w-full px-4 py-3 rounded-xl bg-gray-800/50 border border-gray-700 text-gray-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all placeholder-transparent"
                    placeholder="Confirm Password" />
                <label for="password_confirmation"
                    class="absolute left-4 -top-2.5 bg-[#131d33] px-1 text-sm text-gray-400 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 peer-placeholder-shown:top-3.5 peer-focus:-top-2.5 peer-focus:text-gray-200 peer-focus:text-sm">
                    Ulangi Kata Sandi
                </label>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-400" />
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full py-3.5 px-4 bg-gradient-to-r from-red-600 to-yellow-500 hover:from-red-500 hover:to-yellow-400 text-white font-semibold rounded-xl shadow-lg shadow-orange-500/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 focus:ring-offset-gray-900 transition-all transform hover:scale-[1.02] active:scale-[0.98]">
                    {{ __('Atur Ulang Kata Sandi') }}
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>