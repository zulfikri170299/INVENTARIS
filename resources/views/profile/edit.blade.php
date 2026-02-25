<x-app-layout>
    <x-slot name="header">
        Pengaturan Profil
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-8 pb-12">
        <!-- Profile Header Section -->
        <div class="bg-gradient-to-r from-red-600 to-yellow-500 p-8 rounded-3xl shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 -m-8 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="relative z-10 flex items-center space-x-6">
                <div
                    class="w-24 h-24 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center text-white text-4xl font-bold border border-white/30 shadow-inner">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div>
                    <h3 class="text-3xl font-extrabold text-white mb-1">{{ Auth::user()->name }}</h3>
                    <p class="text-white/80 flex items-center">
                        <i class="ph ph-envelope-simple mr-2"></i>
                        {{ Auth::user()->email }}
                    </p>
                    <div
                        class="mt-3 inline-flex items-center px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-semibold text-white border border-white/20">
                        <i class="ph ph-shield-check mr-1 text-sm"></i>
                        {{ Auth::user()->role }} Account
                    </div>
                </div>
            </div>
        </div>

        <!-- Information Section -->
        <div id="update-profile"
            class="bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-3xl shadow-xl transition-all duration-300 hover:border-white/20">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-blue-500/20 rounded-xl flex items-center justify-center mr-4">
                    <i class="ph ph-user-circle text-blue-400 text-2xl"></i>
                </div>
                <div>
                    <h4 class="text-xl font-bold text-white">Informasi Profil</h4>
                    <p class="text-gray-400 text-sm">Perbarui informasi dasar akun Anda.</p>
                </div>
            </div>
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Password Section -->
        <div id="update-password"
            class="bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-3xl shadow-xl transition-all duration-300 hover:border-white/20">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-yellow-500/20 rounded-xl flex items-center justify-center mr-4">
                    <i class="ph ph-lock-key text-yellow-400 text-2xl"></i>
                </div>
                <div>
                    <h4 class="text-xl font-bold text-white">Keamanan Akun</h4>
                    <p class="text-gray-400 text-sm">Ganti kata sandi secara berkala untuk menjaga keamanan.</p>
                </div>
            </div>
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Danger Zone -->
        @if(Auth::user()->role !== 'Super Admin')
            <div
                class="bg-red-500/5 backdrop-blur-xl border border-red-500/10 p-8 rounded-3xl shadow-xl transition-all duration-300 hover:border-red-500/20">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-red-500/20 rounded-xl flex items-center justify-center mr-4">
                        <i class="ph ph-warning-octagon text-red-500 text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-red-500">Zona Bahaya</h4>
                        <p class="text-red-400/60 text-sm">Tindakan ini permanen dan tidak dapat dibatalkan.</p>
                    </div>
                </div>
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        @endif
    </div>
</x-app-layout>