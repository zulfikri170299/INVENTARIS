<x-app-layout>
    <x-slot name="header">Log Aktivitas User</x-slot>

    <div class="space-y-6 animate-fade-in" x-data="{ 
        perPage: {{ request('per_page', 20) }}
    }">
        <!-- Search & Filter Bar -->
        <div class="glass-card rounded-3xl p-6 shadow-2xl relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity">
                <i class="ph ph-clock-counter-clockwise text-8xl text-primary-400"></i>
            </div>
            
            <form action="{{ route('activity-logs.index') }}" method="GET" class="flex flex-col md:flex-row md:items-end gap-6 relative z-10">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-3 px-1">Cari Aktivitas</label>
                    <div class="relative">
                        <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                        <input type="text" name="search" value="{{ request('search') }}" 
                            placeholder="Cari berdasarkan user, aktivitas, atau deskripsi..."
                            class="w-full bg-gray-800/40 border border-gray-700/50 text-gray-100 rounded-2xl pl-12 pr-4 py-3.5 focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 transition-all placeholder:text-gray-500">
                    </div>
                </div>

                <div class="w-full md:w-56">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-3 px-1">Filter Modul</label>
                    <select name="module" onchange="this.form.submit()"
                        class="w-full bg-gray-800/40 border border-gray-700/50 text-gray-100 rounded-2xl px-4 py-3.5 focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 transition-all appearance-none cursor-pointer">
                        <option value="">Semua Modul</option>
                        @foreach($modules as $module)
                            <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>{{ $module }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full md:w-32">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-3 px-1">Tampilkan</label>
                    <select name="per_page" onchange="this.form.submit()"
                        class="w-full bg-gray-800/40 border border-gray-700/50 text-gray-100 rounded-2xl px-4 py-3.5 focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 transition-all appearance-none cursor-pointer">
                        @foreach([20, 50, 100, 200] as $val)
                            <option value="{{ $val }}" {{ request('per_page', 20) == $val ? 'selected' : '' }}>{{ $val }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="p-3.5 bg-primary-600 hover:bg-primary-500 text-white rounded-2xl shadow-lg shadow-primary-500/20 transition-all">
                        <i class="ph ph-funnel text-xl"></i>
                    </button>
                    <a href="{{ route('activity-logs.index') }}" class="p-3.5 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-2xl border border-gray-700 transition-all">
                        <i class="ph ph-arrow-counter-clockwise text-xl"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Logs Table -->
        <div class="glass-card rounded-3xl overflow-hidden shadow-2xl border border-white/5">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-800/30">
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-800">Waktu</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-800">User</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-800">Aktivitas</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-800">Detail</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-800 text-center">Modul</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-800">Info Perangkat</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-300 divide-y divide-gray-800/50">
                        @forelse($logs as $log)
                            <tr class="hover:bg-white/5 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-xs font-medium text-gray-400">{{ $log->created_at->format('d M Y') }}</div>
                                    <div class="text-xs text-primary-400 font-bold">{{ $log->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-primary-600/20 flex items-center justify-center text-primary-400 mr-3 text-[10px] font-bold border border-primary-500/20">
                                            {{ substr($log->user?->name ?? 'System', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-xs font-bold text-gray-100">{{ $log->user?->name ?? 'System' }}</div>
                                            <div class="text-[10px] text-gray-500 uppercase tracking-tighter">{{ $log->user?->role ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider border border-white/10 shadow-sm
                                        {{ str_starts_with($log->activity, 'Tambah') ? 'bg-green-500/10 text-green-500 border-green-500/20' : 
                                           (str_starts_with($log->activity, 'Update') ? 'bg-blue-500/10 text-blue-500 border-blue-500/20' : 
                                           (str_starts_with($log->activity, 'Hapus') ? 'bg-red-500/10 text-red-500 border-red-500/20' : 
                                           (str_starts_with($log->activity, 'Login') ? 'bg-primary-500/10 text-primary-500 border-primary-500/20' : 'bg-gray-500/10 text-gray-400'))) }}">
                                        {{ $log->activity }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-[11px] leading-relaxed max-w-sm truncate group-hover:whitespace-normal transition-all duration-300" title="{{ $log->description }}">
                                        {{ $log->description }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-[10px] font-bold text-gray-500 uppercase border border-gray-700 px-2 py-0.5 rounded-full bg-gray-800/50">
                                        {{ $log->module ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center text-[10px] text-gray-500">
                                        <i class="ph ph-desktop mr-1.5 text-xs text-primary-400/50"></i>
                                        <span class="truncate max-w-[120px]" title="{{ $log->user_agent }}">{{ $log->user_agent }}</span>
                                    </div>
                                    <div class="flex items-center text-[10px] text-gray-500 mt-1">
                                        <i class="ph ph-map-pin mr-1.5 text-xs text-primary-400/50"></i>
                                        <span>IP: {{ $log->ip_address ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="ph ph-clipboard-text text-6xl text-gray-700 mb-4 animate-pulse"></i>
                                        <p class="text-gray-500 font-medium">Belum ada catatan aktivitas yang ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-6 bg-gray-800/10 border-t border-gray-800">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
