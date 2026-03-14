<x-guest-layout>
    <div class="glass-panel p-8 rounded-2xl shadow-2xl animate-fade-in-up">
        <!-- Logo / Header -->
        <div class="text-center mb-8 font-outfit">
            <div class="flex items-center justify-center mb-4">
                <img src="{{ asset('rolog.png') }}" alt="Logo" class="h-16 mr-4 drop-shadow-lg">
                <h1
                    class="text-3xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-red-500 via-orange-500 to-yellow-500 tracking-tight uppercase">
                    Invenlog <br> Polda NTB
                </h1>
            </div>
            <p class="text-gray-400 text-sm mt-2">Sistem Inventaris Logistik</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Email Address -->
            <div class="relative group">
                <input id="email" type="email" name="email" :value="old('email')" required autofocus
                    autocomplete="username"
                    class="peer w-full px-4 py-3 rounded-xl bg-gray-800/50 border border-gray-700 text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all placeholder-transparent"
                    placeholder="Email Address" />
                <label for="email"
                    class="absolute left-4 -top-2.5 bg-[#131d33] px-1 text-sm text-gray-400 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 peer-placeholder-shown:top-3.5 peer-focus:-top-2.5 peer-focus:text-gray-200 peer-focus:text-sm">
                    Email Address
                </label>
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400" />
            </div>

            <!-- Password -->
            <div class="relative group" x-data="{ show: false }">
                <input id="password" :type="show ? 'text' : 'password'" name="password" required
                    autocomplete="current-password"
                    class="peer w-full px-4 py-3 rounded-xl bg-gray-800/50 border border-gray-700 text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all placeholder-transparent"
                    placeholder="Password" />
                <label for="password"
                    class="absolute left-4 -top-2.5 bg-[#131d33] px-1 text-sm text-gray-400 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 peer-placeholder-shown:top-3.5 peer-focus:-top-2.5 peer-focus:text-gray-200 peer-focus:text-sm">
                    Password
                </label>
                <button type="button" @click="show = !show"
                    class="absolute right-4 top-3.5 text-gray-400 hover:text-gray-200 transition-colors focus:outline-none z-10">
                    <i class="ph ph-eye text-xl" x-show="!show"></i>
                    <i class="ph ph-eye-slash text-xl" x-show="show" x-cloak></i>
                </button>
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox"
                        class="rounded border-gray-700 bg-gray-800 text-primary-500 focus:ring-offset-gray-900 focus:ring-primary-500"
                        name="remember">
                    <span class="ms-2 text-sm text-gray-400">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-400 hover:text-primary-400 transition-colors"
                        href="{{ route('password.request') }}">
                        {{ __('Lupa password?') }}
                    </a>
                @endif
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full py-3.5 px-4 bg-gradient-to-r from-primary-600 to-primary-500 hover:from-primary-500 hover:to-primary-400 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-gray-900 transition-all transform hover:scale-[1.02] active:scale-[0.98]">
                    {{ __('Log in') }}
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>