<x-app-layout>
    <x-slot name="header">Log Aktivitas User</x-slot>

    <div class="space-y-6 animate-fade-in" x-data="{ 
        perPage: {{ request('per_page', 20) }}
    }">
        <!-- Action & Filter Bar -->
        <div class="glass-card p-4 rounded-2xl flex items-center justify-between mb-6 relative z-50 gap-3 animate-fade-in">
            <!-- Left: Module Title -->
            <div class="flex items-center gap-2">
                <i class="ph ph-clock-counter-clockwise text-primary-500 text-lg"></i>
                <h3 class="text-xs font-bold text-white uppercase tracking-wider">Log Aktivitas</h3>
            </div>

            <!-- Right: Filter Form -->
            <form action="{{ route('activity-logs.index') }}" method="GET" class="flex flex-wrap items-center gap-2 flex-1 justify-end">
                <select name="module" onchange="this.form.submit()"
                    class="bg-gray-800 border border-gray-700 text-gray-200 text-[11px] rounded-lg pl-2 pr-8 py-1.5 focus:ring-primary-500 min-w-[120px]">
                    <option value="">Semua Modul</option>
                    @foreach($modules as $module)
                        <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>{{ $module }}</option>
                    @endforeach
                </select>

                <select name="per_page" onchange="this.form.submit()"
                    class="bg-gray-800 border border-gray-700 text-gray-200 text-[11px] rounded-lg pl-2 pr-8 py-1.5 focus:ring-primary-500 w-24">
                    @foreach([20, 50, 100, 200] as $val)
                        <option value="{{ $val }}" {{ request('per_page', 20) == $val ? 'selected' : '' }}>{{ $val }}</option>
                    @endforeach
                </select>

                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..."
                        class="bg-gray-800/50 border border-gray-700 text-gray-200 text-[11px] rounded-lg pl-8 pr-3 py-1.5 focus:ring-primary-500 w-32 lg:w-40 transition-all">
                    <i class="ph ph-magnifying-glass absolute left-2.5 top-2 text-gray-500 text-xs"></i>
                </div>
                
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button type="button" @click="open = !open"
                        class="btn-compact bg-gray-800 hover:bg-gray-700 text-gray-300 font-semibold transition-all flex items-center border border-gray-700">
                        <i class="ph ph-file-arrow-down mr-1.5 text-lg text-primary-400"></i>
                        Export
                        <i class="ph ph-caret-down ml-1.5 text-xs"></i>
                    </button>
                    <div x-show="open" 
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-40 rounded-xl shadow-2xl bg-gray-800 border border-gray-700 z-50 overflow-hidden"
                        style="display: none;">
                        <a href="{{ route('activity-logs.export-pdf', request()->all()) }}" 
                            class="flex items-center px-4 py-2.5 text-[11px] text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                            <i class="ph ph-file-pdf mr-2 text-base text-red-500"></i>
                            Export PDF
                        </a>
                        <a href="{{ route('activity-logs.export-excel', request()->all()) }}" 
                            class="flex items-center px-4 py-2.5 text-[11px] text-gray-300 hover:bg-gray-700 hover:text-white transition-colors border-t border-gray-700/50">
                            <i class="ph ph-file-xls mr-2 text-base text-green-500"></i>
                            Export Excel
                        </a>
                    </div>
                </div>
                
                <a href="{{ route('activity-logs.index') }}" class="p-1.5 bg-gray-800 text-gray-400 hover:text-white rounded-lg border border-gray-700 transition-colors" title="Reset">
                    <i class="ph ph-arrow-counter-clockwise"></i>
                </a>
            </form>
        </div>

        <!-- Logs Table -->
        <div class="glass-card rounded-xl overflow-hidden shadow-2xl border border-white/5 mt-4">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="table-excel">
                    <thead>
                        <tr>
                            <th class="w-24">Waktu</th>
                            <th class="w-48">User</th>
                            <th class="w-32">Aktivitas</th>
                            <th>Detail</th>
                            <th class="w-24 text-center">Modul</th>
                            <th class="w-56">Info Perangkat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/50">
                        @forelse($logs as $log)
                            <tr class="transition-colors group">
                                <td>
                                    <div class="font-bold text-gray-500 uppercase">{{ $log->created_at->format('d M y') }}</div>
                                    <div class="text-primary-400 font-bold">{{ $log->created_at->format('H:i') }}</div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 rounded-full bg-primary-600/20 flex items-center justify-center text-primary-400 mr-2 font-bold border border-primary-500/20">
                                            {{ substr($log->user?->name ?? 'System', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-100 leading-none">{{ $log->user?->name ?? 'System' }}</div>
                                            <div class="text-gray-500 uppercase leading-none mt-1">{{ $log->user?->role ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $actColor = match (true) {
                                            str_starts_with($log->activity, 'Tambah') => 'bg-green-500/10 text-green-500 border-green-500/20',
                                            str_starts_with($log->activity, 'Update') => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
                                            str_starts_with($log->activity, 'Hapus') => 'bg-red-500/10 text-red-500 border-red-500/20',
                                            str_starts_with($log->activity, 'Login') => 'bg-primary-500/10 text-primary-500 border-primary-500/20',
                                            default => 'bg-gray-500/10 text-gray-400 border-gray-700/50'
                                        };
                                    @endphp
                                    <span class="badge-compact border {{ $actColor }}">
                                        {{ $log->activity }}
                                    </span>
                                </td>
                                <td>
                                    <div class="leading-tight max-w-xs truncate group-hover:whitespace-normal transition-all duration-300" title="{{ $log->description }}">
                                        {{ $log->description }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="font-bold text-gray-500 uppercase border border-gray-700/50 px-2 py-0.5 rounded-md bg-gray-800/50">
                                        {{ $log->module ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center text-gray-500 leading-none">
                                        <i class="ph ph-desktop mr-1 text-primary-400/50"></i>
                                        <span class="truncate max-w-[150px]" title="{{ $log->user_agent }}">{{ $log->user_agent }}</span>
                                    </div>
                                    <div class="flex items-center text-gray-500 mt-1 leading-none">
                                        <i class="ph ph-map-pin mr-1 text-primary-400/50"></i>
                                        <span>IP: {{ $log->ip_address ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center uppercase tracking-widest text-xs text-gray-500 font-bold">
                                    Belum ada catatan aktivitas.
                                </td>
<x-app-layout>
    <x-slot name="header">Log Aktivitas User</x-slot>

    <div class="space-y-4 animate-fade-in" x-data="{ 
        perPage: {{ request('per_page', 20) }}
    }">
        <!-- Action & Filter Bar -->
        <div class="glass-card p-4 rounded-2xl flex items-center justify-between mb-4 relative z-50 gap-3 animate-fade-in">
            <!-- Left: Module Title -->
            <div class="flex items-center gap-2">
                <i class="ph ph-clock-counter-clockwise text-primary-500 text-lg"></i>
                <h3 class="text-xs font-bold text-white uppercase tracking-wider">Log Aktivitas</h3>
            </div>

            <!-- Right: Filter Form -->
            <form action="{{ route('activity-logs.index') }}" method="GET" class="flex flex-wrap items-center gap-2 flex-1 justify-end">
                <select name="module" onchange="this.form.submit()"
                    class="bg-gray-800 border border-gray-700 text-gray-200 text-[11px] rounded-lg pl-2 pr-8 py-1.5 focus:ring-primary-500 min-w-[120px]">
                    <option value="">Semua Modul</option>
                    @foreach($modules as $module)
                        <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>{{ $module }}</option>
                    @endforeach
                </select>

                <select name="per_page" onchange="this.form.submit()"
                    class="bg-gray-800 border border-gray-700 text-gray-200 text-[11px] rounded-lg pl-2 pr-8 py-1.5 focus:ring-primary-500 w-24">
                    @foreach([20, 50, 100, 200] as $val)
                        <option value="{{ $val }}" {{ request('per_page', 20) == $val ? 'selected' : '' }}>{{ $val }}</option>
                    @endforeach
                </select>

                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..."
                        class="bg-gray-800/50 border border-gray-700 text-gray-200 text-[11px] rounded-lg pl-8 pr-3 py-1.5 focus:ring-primary-500 w-32 lg:w-40 transition-all">
                    <i class="ph ph-magnifying-glass absolute left-2.5 top-2 text-gray-500 text-xs"></i>
                </div>
                
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button type="button" @click="open = !open"
                        class="btn-compact bg-gray-800 hover:bg-gray-700 text-gray-300 font-semibold transition-all flex items-center border border-gray-700">
                        <i class="ph ph-file-arrow-down mr-1.5 text-lg text-primary-400"></i>
                        Export
                        <i class="ph ph-caret-down ml-1.5 text-xs"></i>
                    </button>
                    <div x-show="open" 
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-40 rounded-xl shadow-2xl bg-gray-800 border border-gray-700 z-50 overflow-hidden"
                        style="display: none;">
                        <a href="{{ route('activity-logs.export-pdf', request()->all()) }}" 
                            class="flex items-center px-4 py-2.5 text-[11px] text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                            <i class="ph ph-file-pdf mr-2 text-base text-red-500"></i>
                            Export PDF
                        </a>
                        <a href="{{ route('activity-logs.export-excel', request()->all()) }}" 
                            class="flex items-center px-4 py-2.5 text-[11px] text-gray-300 hover:bg-gray-700 hover:text-white transition-colors border-t border-gray-700/50">
                            <i class="ph ph-file-xls mr-2 text-base text-green-500"></i>
                            Export Excel
                        </a>
                    </div>
                </div>
                
                <a href="{{ route('activity-logs.index') }}" class="p-1.5 bg-gray-800 text-gray-400 hover:text-white rounded-lg border border-gray-700 transition-colors" title="Reset">
                    <i class="ph ph-arrow-counter-clockwise"></i>
                </a>
            </form>
        </div>

        <!-- Logs Table -->
        <div class="glass-card rounded-xl overflow-hidden shadow-2xl border border-white/5 mt-2">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="table-excel">
                    <thead>
                        <tr>
                            <th class="w-24">Waktu</th>
                            <th class="w-48">User</th>
                            <th class="w-32">Aktivitas</th>
                            <th>Detail</th>
                            <th class="w-24 text-center">Modul</th>
                            <th class="w-56">Info Perangkat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/50">
                        @forelse($logs as $log)
                            <tr class="transition-colors group">
                                <td>
                                    <div class="font-bold text-gray-500 uppercase">{{ $log->created_at->format('d M y') }}</div>
                                    <div class="text-primary-400 font-bold">{{ $log->created_at->format('H:i') }}</div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 rounded-full bg-primary-600/20 flex items-center justify-center text-primary-400 mr-2 font-bold border border-primary-500/20">
                                            {{ substr($log->user?->name ?? 'System', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-100 leading-none">{{ $log->user?->name ?? 'System' }}</div>
                                            <div class="text-gray-500 uppercase leading-none mt-1">{{ $log->user?->role ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $actColor = match (true) {
                                            str_starts_with($log->activity, 'Tambah') => 'bg-green-500/10 text-green-500 border-green-500/20',
                                            str_starts_with($log->activity, 'Update') => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
                                            str_starts_with($log->activity, 'Hapus') => 'bg-red-500/10 text-red-500 border-red-500/20',
                                            str_starts_with($log->activity, 'Login') => 'bg-primary-500/10 text-primary-500 border-primary-500/20',
                                            default => 'bg-gray-500/10 text-gray-400 border-gray-700/50'
                                        };
                                    @endphp
                                    <span class="badge-compact border {{ $actColor }}">
                                        {{ $log->activity }}
                                    </span>
                                </td>
                                <td>
                                    <div class="leading-tight max-w-xs truncate group-hover:whitespace-normal transition-all duration-300" title="{{ $log->description }}">
                                        {{ $log->description }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="font-bold text-gray-500 uppercase border border-gray-700/50 px-2 py-0.5 rounded-md bg-gray-800/50">
                                        {{ $log->module ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center text-gray-500 leading-none">
                                        <i class="ph ph-desktop mr-1 text-primary-400/50"></i>
                                        <span class="truncate max-w-[150px]" title="{{ $log->user_agent }}">{{ $log->user_agent }}</span>
                                    </div>
                                    <div class="flex items-center text-gray-500 mt-1 leading-none">
                                        <i class="ph ph-map-pin mr-1 text-primary-400/50"></i>
                                        <span>IP: {{ $log->ip_address ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center uppercase tracking-widest text-xs text-gray-500 font-bold">
                                    Belum ada catatan aktivitas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-4 py-0.5 bg-gray-800/10 border-t border-gray-800">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
