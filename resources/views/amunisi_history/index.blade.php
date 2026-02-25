<x-app-layout>
    <x-slot name="header">
        Riwayat Penggunaan Amunisi
    </x-slot>

    <div class="space-y-6 animate-fade-in">
        <!-- Action Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="text-xl font-bold text-gray-100 flex items-center">
                <i class="ph ph-clock-counter-clockwise mr-3 text-primary-500"></i>
                Log Transaksi Amunisi
            </h2>

            <form action="{{ route('amunisi-history.index') }}" method="GET" class="flex items-center space-x-2">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari personel/jenis..."
                        class="bg-gray-800/50 border border-gray-700 text-gray-200 text-sm rounded-xl px-10 py-2.5 focus:ring-primary-500 focus:border-primary-500 w-64 transition-all">
                    <i class="ph ph-magnifying-glass absolute left-3 top-3 text-gray-500"></i>
                </div>
                <button type="submit"
                    class="p-2.5 bg-gray-800 text-gray-400 hover:text-white rounded-xl border border-gray-700 transition-colors">
                    <i class="ph ph-funnel text-xl"></i>
                </button>
            </form>
        </div>

        <!-- Filter Bar -->
        @if(!auth()->user()->satker_id || in_array(auth()->user()->role, ['Super Admin']))
            <div class="glass-card p-6 rounded-2xl">
                <form action="{{ route('amunisi-history.index') }}" method="GET"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Satker</label>
                        <select name="satker_id"
                            class="w-full bg-gray-800 border border-gray-700 text-gray-200 text-sm rounded-xl px-4 py-2.5 focus:ring-primary-500">
                            <option value="">Semua Satker</option>
                            @foreach($satkers as $satker)
                                <option value="{{ $satker->id }}" {{ request('satker_id') == $satker->id ? 'selected' : '' }}>
                                    {{ $satker->nama_satker }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full px-5 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-xl font-semibold transition-all">
                            Filter
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Table -->
        <div class="glass-card rounded-3xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-800/50 text-gray-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            @if(!auth()->user()->satker_id || in_array(auth()->user()->role, ['Super Admin']))
                                <th class="px-6 py-4">Satker</th>
                            @endif
                            <th class="px-6 py-4">Personel</th>
                            <th class="px-6 py-4">Jenis Amunisi</th>
                            <th class="px-6 py-4 text-center">Jumlah</th>
                            <th class="px-6 py-4">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800 text-sm text-gray-300">
                        @forelse($histories as $history)
                            <tr class="hover:bg-gray-800/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($history->tanggal)->format('d/m/Y') }}
                                </td>
                                @if(!auth()->user()->satker_id || in_array(auth()->user()->role, ['Super Admin']))
                                    <td class="px-6 py-4">{{ $history->satker->nama_satker ?? '-' }}</td>
                                @endif
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-100">{{ $history->nama_personel }}</div>
                                    <div class="text-xs text-gray-500">{{ $history->pangkat_nrp }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 bg-orange-500/10 text-orange-400 rounded-lg text-xs font-bold ring-1 ring-orange-500/20">
                                        {{ $history->jenis_amunisi }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($history->jumlah > 0)
                                        <span class="text-red-400 font-bold">-{{ $history->jumlah }}</span>
                                    @else
                                        <span class="text-green-400 font-bold">+{{ abs($history->jumlah) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-400 italic">
                                    {{ $history->keterangan }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">
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