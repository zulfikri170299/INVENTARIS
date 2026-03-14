<x-app-layout>
    <x-slot name="header">
        Riwayat Penggunaan Amunisi
    </x-slot>

    <div class="space-y-6 animate-fade-in">
        <!-- Action & Filter Bar -->
        <div class="glass-card p-2 px-3 rounded-xl flex flex-wrap items-center justify-between gap-3 animate-fade-in text-xs">
            <!-- Left: Title -->
            <div class="flex items-center gap-2">
                <i class="ph ph-clock-counter-clockwise text-primary-500 text-lg"></i>
                <h3 class="text-xs font-bold text-white uppercase tracking-wider">Log Amunisi</h3>
            </div>

            <!-- Right: Filter Form -->
            <form action="{{ route('amunisi-history.index') }}" method="GET" class="flex flex-wrap items-center gap-2 flex-1 justify-end">
                @if(!auth()->user()->satker_id || in_array(auth()->user()->role, ['Super Admin']))
                    <select name="satker_id" onchange="this.form.submit()"
                        class="bg-gray-800 border border-gray-700 text-gray-200 text-[11px] rounded-lg px-2 py-1.5 focus:ring-primary-500 min-w-[120px]">
                        <option value="">Semua Satker</option>
                        @foreach($satkers as $satker)
                            <option value="{{ $satker->id }}" {{ request('satker_id') == $satker->id ? 'selected' : '' }}>
                                {{ $satker->nama_satker }}
                            </option>
                        @endforeach
                    </select>
                @endif

                <div class="flex items-center gap-1.5">
                    <span class="text-gray-500 text-[10px] uppercase font-bold">Periode:</span>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        class="bg-gray-800 border border-gray-700 text-gray-200 text-[11px] rounded-lg px-2 py-1 focus:ring-primary-500">
                    <span class="text-gray-600">-</span>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="bg-gray-800 border border-gray-700 text-gray-200 text-[11px] rounded-lg px-2 py-1 focus:ring-primary-500">
                </div>

                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..."
                        class="bg-gray-800/50 border border-gray-700 text-gray-200 text-[11px] rounded-lg pl-8 pr-3 py-1.5 focus:ring-primary-500 w-32 lg:w-40 transition-all">
                    <i class="ph ph-magnifying-glass absolute left-2.5 top-2 text-gray-500 text-xs"></i>
                </div>
                
                <a href="{{ route('amunisi-history.index') }}" class="p-1.5 bg-gray-800 text-gray-400 hover:text-white rounded-lg border border-gray-700 transition-colors" title="Reset">
                    <i class="ph ph-arrow-counter-clockwise"></i>
                </a>
            </form>
        </div>

        <!-- Table -->
        <div class="glass-card rounded-3xl overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="table-excel">
                    <thead>
                        <tr>
                            <th class="w-32">Tanggal</th>
                            @if(!auth()->user()->satker_id || in_array(auth()->user()->role, ['Super Admin']))
                                <th>Satker</th>
                            @endif
                            <th>Personel</th>
                            <th>Jenis Amunisi</th>
                            <th class="w-24 text-center">Jumlah</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @forelse($histories as $history)
                            <tr class="transition-colors">
                                <td class="font-mono text-[11px] text-gray-400">
                                    {{ \Carbon\Carbon::parse($history->tanggal)->format('d/m/Y') }}
                                </td>
                                @if(!auth()->user()->satker_id || in_array(auth()->user()->role, ['Super Admin']))
                                    <td class="text-[10px]">{{ $history->satker->nama_satker ?? '-' }}</td>
                                @endif
                                <td>
                                    <div class="font-bold text-gray-100">{{ $history->nama_personel }}</div>
                                    <div class="text-[9px] text-gray-500 uppercase">{{ $history->pangkat_nrp }}</div>
                                </td>
                                <td>
                                    <span class="badge-compact border border-orange-500/20 bg-orange-500/10 text-orange-400">
                                        {{ $history->jenis_amunisi }}
                                    </span>
                                </td>
                                <td class="text-center font-bold">
                                    @if($history->jumlah < 0)
                                        <span class="text-red-400">{{ $history->jumlah }}</span>
                                    @else
                                        <span class="text-green-400">+{{ $history->jumlah }}</span>
                                    @endif
                                </td>
                                <td class="text-[10px] text-gray-400 italic">
                                    {{ $history->keterangan }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center uppercase tracking-widest text-xs text-gray-500 font-bold">
                                    Belum ada data riwayat amunisi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($histories->hasPages())
                <div class="px-6 py-4 border-t border-gray-800 bg-gray-800/20">
                    {{ $histories->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>