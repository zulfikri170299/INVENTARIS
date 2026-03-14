<x-app-layout>
    <x-slot name="header">
        Pengajuan Berkas
    </x-slot>

    <div class="space-y-6">
        {{-- Header --}}
        <!-- Action & Filter Bar -->
        <div class="glass-card p-2 px-3 rounded-xl flex flex-wrap items-center justify-between gap-3 animate-fade-in text-xs">
            <!-- Left: Actions -->
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-2 mr-2">
                    <i class="ph ph-envelope-open text-primary-500 text-lg"></i>
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider">Pengajuan</h3>
                </div>
                
                @if(!in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2']))
                    @if(request('kategori') === 'penetapan_status')
                        <a href="{{ route('pengajuan-berkas.create', ['kategori' => 'penetapan_status']) }}"
                            class="btn-compact bg-blue-600 text-white font-semibold hover:bg-blue-500 transition-all shadow-lg shadow-blue-500/20 flex items-center">
                            <i class="ph ph-stamp mr-1.5 text-lg"></i> Penetapan
                        </a>
                    @else
                        <a href="{{ route('pengajuan-berkas.create', ['kategori' => 'penghapusan']) }}"
                            class="btn-compact bg-red-600 text-white font-semibold hover:bg-red-500 transition-all shadow-lg shadow-red-500/20 flex items-center">
                            <i class="ph ph-trash mr-1.5 text-lg"></i> Penghapusan
                        </a>
                    @endif
                @endif
            </div>

            <!-- Right: Filter Form -->
            <form action="{{ route('pengajuan-berkas.index') }}" method="GET" class="flex flex-wrap items-center gap-2 flex-1 justify-end">
                <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                
                <div class="flex items-center gap-1.5">
                    <span class="text-gray-500 text-[10px] uppercase font-bold">Periode:</span>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        class="bg-gray-800 border border-gray-700 text-gray-200 text-[11px] rounded-lg px-2 py-1 focus:ring-primary-500">
                    <span class="text-gray-600">-</span>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="bg-gray-800 border border-gray-700 text-gray-200 text-[11px] rounded-lg px-2 py-1 focus:ring-primary-500">
                </div>

                <select name="status" onchange="this.form.submit()"
                    class="bg-gray-800 border border-gray-700 text-gray-200 text-[11px] rounded-lg px-2 py-1.5 focus:ring-primary-500 min-w-[120px]">
                    <option value="">Semua Status</option>
                    <option value="diajukan" {{ request('status') === 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                    <option value="diterima" {{ request('status') === 'diterima' ? 'selected' : '' }}>Diterima</option>
                    <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="dikembalikan" {{ request('status') === 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                    <option value="naik_ke_kapolda" {{ request('status') === 'naik_ke_kapolda' ? 'selected' : '' }}>Naik ke Kapolda</option>
                    <option value="ditandatangani" {{ request('status') === 'ditandatangani' ? 'selected' : '' }}>Ditandatangani</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>

                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..."
                        class="bg-gray-800/50 border border-gray-700 text-gray-200 text-[11px] rounded-lg pl-8 pr-3 py-1.5 focus:ring-primary-500 w-32 lg:w-40 transition-all">
                    <i class="ph ph-magnifying-glass absolute left-2.5 top-2 text-gray-500 text-xs"></i>
                </div>
                
                <a href="{{ route('pengajuan-berkas.index', ['kategori' => request('kategori')]) }}" class="p-1.5 bg-gray-800 text-gray-400 hover:text-white rounded-lg border border-gray-700 transition-colors" title="Reset">
                    <i class="ph ph-arrow-counter-clockwise"></i>
                </a>
            </form>
        </div>

        {{-- Table --}}
        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/10 flex justify-between items-center bg-white/5">
                <div class="flex items-center space-x-2 text-gray-400">
                    <span class="text-xs uppercase font-semibold">Tampilkan:</span>
                    <select onchange="window.location.href = this.value"
                        class="bg-gray-900/50 border border-white/10 text-gray-200 text-sm rounded-xl px-3 py-1.5 focus:ring-primary-500 focus:border-primary-500 transition-all">
                        @foreach([10, 25, 50, 100] as $size)
                            <option value="{{ request()->fullUrlWithQuery(['per_page' => $size]) }}" {{ (request('per_page') ?? 10) == $size ? 'selected' : '' }}>
                                {{ $size }}
                            </option>
                        @endforeach
                    </select>
                    <span class="text-xs text-gray-500 ml-2">data per halaman</span>
                </div>
            </div>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="table-excel">
                    <thead>
                        <tr>
                            <th class="w-10 text-center">No</th>
                            <th>Satker</th>
                            <th>Judul Pengajuan</th>
                            <th>Status Berkas</th>
                            <th class="text-center">Tanggal</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($pengajuan as $i => $item)
                            <tr class="transition-colors">
                                <td class="text-center font-bold text-gray-500">
                                    {{ $pengajuan->firstItem() + $i }}
                                </td>
                                <td>{{ $item->satker->nama_satker ?? '-' }}</td>
                                <td>
                                    <div class="font-bold text-white">{{ $item->judul }}</div>
                                    <div class="text-[11px] text-gray-500">Oleh: {{ $item->user->name }}</div>
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'diajukan' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20',
                                            'diterima' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                            'diproses' => 'bg-cyan-500/10 text-cyan-400 border-cyan-500/20',
                                            'dikembalikan' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                            'naik_ke_kapolda' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
                                            'ditandatangani' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20',
                                            'selesai' => 'bg-green-500/10 text-green-400 border-green-500/20',
                                        ];
                                    @endphp
                                    <span class="badge-compact border {{ $statusColors[$item->status] ?? 'bg-gray-500/10 text-gray-400 border-gray-700/50' }}">
                                        {{ $item->status_label }}
                                    </span>
                                </td>
                                <td class="text-center font-mono text-gray-400">{{ $item->created_at->format('d/m/Y') }}</td>
                                <td class="text-right">
                                    <a href="{{ route('pengajuan-berkas.show', $item->id) }}"
                                        class="p-1 px-3 bg-primary-600/20 text-primary-400 rounded-md font-bold hover:bg-primary-600/30 transition-colors border border-primary-500/20 inline-flex items-center">
                                        <i class="ph ph-eye mr-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center uppercase tracking-widest text-xs text-gray-500 font-bold">
                                    Belum ada pengajuan berkas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pengajuan->hasPages())
                <div class="px-6 py-4 border-t border-white/10">
                    {{ $pengajuan->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
