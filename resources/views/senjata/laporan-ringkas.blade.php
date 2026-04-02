<x-app-layout>
    <x-slot name="header">
        Laporan Ringkas Senjata
    </x-slot>

    <div class="space-y-4 animate-fade-in">

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="glass-card rounded-2xl p-3 text-center border-t-2 border-primary-500">
                <div class="text-3xl font-black text-primary-400">{{ number_format($stats['total']) }}</div>
                <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mt-1">Total Senjata</div>
            </div>
            <div class="glass-card rounded-2xl p-4 text-center border-t-2 border-green-500">
                <div class="text-3xl font-black text-green-400">{{ number_format($stats['total_baik']) }}</div>
                <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mt-1">Baik</div>
            </div>
            <div class="glass-card rounded-2xl p-4 text-center border-t-2 border-yellow-500">
                <div class="text-3xl font-black text-yellow-400">{{ number_format($stats['total_rusak_ringan']) }}</div>
                <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mt-1">Rusak Ringan</div>
            </div>
            <div class="glass-card rounded-2xl p-4 text-center border-t-2 border-red-500">
                <div class="text-3xl font-black text-red-400">{{ number_format($stats['total_rusak_berat']) }}</div>
                <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mt-1">Rusak Berat</div>
            </div>
            <div class="glass-card rounded-2xl p-4 text-center border-t-2 border-indigo-500">
                <div class="text-3xl font-black text-indigo-400">{{ number_format($stats['total_panjang']) }}</div>
                <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mt-1">Laras Panjang</div>
            </div>
            <div class="glass-card rounded-2xl p-4 text-center border-t-2 border-purple-500">
                <div class="text-3xl font-black text-purple-400">{{ number_format($stats['total_pendek']) }}</div>
                <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mt-1">Laras Pendek</div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                <a href="javascript:void(0)"
                    onclick="safeDownload('{{ route('senjata.export-excel', request()->all()) }}', 'laporan-senjata.xlsx')"
                    class="px-5 py-2.5 bg-green-600 hover:bg-green-500 text-white rounded-xl font-semibold shadow-lg shadow-green-500/20 transition-all flex items-center">
                    <i class="ph ph-file-xls mr-2 text-lg"></i>
                    Export Excel
                </a>
                <a href="javascript:void(0)" onclick="window.print()"
                    class="px-5 py-2.5 bg-red-600 hover:bg-red-500 text-white rounded-xl font-semibold shadow-lg shadow-red-500/20 transition-all flex items-center">
                    <i class="ph ph-printer mr-2 text-lg"></i>
                    Cetak
                </a>
            </div>

            <!-- Filter -->
            <form action="{{ route('senjata.laporan-ringkas') }}" method="GET"
                class="flex flex-wrap items-center gap-3">
                <select name="laras"
                    class="bg-gray-800/50 border border-gray-700 text-gray-200 text-sm rounded-xl px-4 py-2.5 focus:ring-primary-500 focus:border-primary-500 w-44 transition-all">
                    <option value="">Semua Laras</option>
                    <option value="Panjang" {{ ($laras ?? '') == 'Panjang' ? 'selected' : '' }}>Panjang</option>
                    <option value="Pendek" {{ ($laras ?? '') == 'Pendek' ? 'selected' : '' }}>Pendek</option>
                </select>
                <select name="kondisi"
                    class="bg-gray-800/50 border border-gray-700 text-gray-200 text-sm rounded-xl px-4 py-2.5 focus:ring-primary-500 focus:border-primary-500 w-48 transition-all">
                    <option value="">Semua Kondisi</option>
                    <option value="Baik" {{ ($kondisi ?? '') == 'Baik' ? 'selected' : '' }}>Baik</option>
                    <option value="Rusak Ringan" {{ ($kondisi ?? '') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan
                    </option>
                    <option value="Rusak Berat" {{ ($kondisi ?? '') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat
                    </option>
                </select>
                @if(!auth()->user()->satker_id || in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2']))
                    <select name="satker_id"
                        class="bg-gray-800/50 border border-gray-700 text-gray-200 text-sm rounded-xl px-4 py-2.5 focus:ring-primary-500 focus:border-primary-500 w-56 transition-all">
                        <option value="">Semua Satker</option>
                        @foreach($satkers as $satker)
                            <option value="{{ $satker->id }}" {{ $satkerId == $satker->id ? 'selected' : '' }}>
                                {{ $satker->nama_satker }}
                            </option>
                        @endforeach
                    </select>
                @endif
                <button type="submit"
                    class="px-5 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-xl font-semibold transition-all">
                    Filter
                </button>
                @if($satkerId || $laras || $kondisi)
                    <a href="{{ route('senjata.laporan-ringkas') }}"
                        class="px-5 py-2.5 bg-gray-800 text-gray-400 hover:text-white rounded-xl border border-gray-700 transition-all">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="glass-card rounded-3xl overflow-hidden">
            <div class="px-6 py-3 bg-gradient-to-r from-indigo-600/10 to-purple-600/10 border-b border-gray-800">
                <h3 class="text-base font-bold text-gray-100 flex items-center">
                    <i class="ph ph-shield mr-3 text-xl text-indigo-400"></i>
                    Laporan Ringkas Data Senjata
                </h3>
                <p class="text-xs text-gray-500 mt-1">Menampilkan {{ $data->count() }} jenis senjata dari
                    {{ $data->pluck('satker')->unique()->count() }} satker</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-800/50">
                        <tr>
                            <th rowspan="2"
                                class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-center border-b border-r border-gray-700 w-12">
                                No</th>
                            <th rowspan="2"
                                class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-r border-gray-700">
                                Satker</th>
                            <th rowspan="2"
                                class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-r border-gray-700">
                                Jenis Senjata</th>
                            <th colspan="3"
                                class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-center border-b border-r border-gray-700">
                                Kondisi</th>
                            <th rowspan="2"
                                class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-center border-b border-gray-700 w-24">
                                Jumlah</th>
                        </tr>
                        <tr>
                            <th
                                class="px-4 py-2 text-xs font-bold text-green-400 uppercase tracking-wider text-center border-b border-r border-gray-700">
                                Baik</th>
                            <th
                                class="px-4 py-2 text-xs font-bold text-yellow-400 uppercase tracking-wider text-center border-b border-r border-gray-700">
                                RR</th>
                            <th
                                class="px-4 py-2 text-xs font-bold text-red-400 uppercase tracking-wider text-center border-b border-r border-gray-700">
                                RB</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800 text-sm text-gray-300">
                        @forelse($data as $index => $row)
                            <tr class="hover:bg-gray-800/30 transition-colors">
                                <td class="px-4 py-3 text-center font-bold text-gray-500 border-r border-gray-800/50">
                                    {{ $index + 1 }}</td>
                                <td class="px-6 py-3 border-r border-gray-800/50">
                                    <span class="text-xs font-semibold text-gray-300">{{ $row['satker'] }}</span>
                                </td>
                                <td class="px-6 py-3 border-r border-gray-800/50">
                                    <span class="font-medium text-gray-100">{{ $row['jenis_senpi'] }}</span>
                                </td>
                                <td class="px-4 py-3 text-center border-r border-gray-800/50">
                                    @if($row['baik'] > 0)
                                        <span
                                            class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full bg-green-500/10 text-green-400 text-xs font-bold ring-1 ring-green-500/20">{{ $row['baik'] }}</span>
                                    @else
                                        <span class="text-gray-600">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center border-r border-gray-800/50">
                                    @if($row['rusak_ringan'] > 0)
                                        <span
                                            class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full bg-yellow-500/10 text-yellow-400 text-xs font-bold ring-1 ring-yellow-500/20">{{ $row['rusak_ringan'] }}</span>
                                    @else
                                        <span class="text-gray-600">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center border-r border-gray-800/50">
                                    @if($row['rusak_berat'] > 0)
                                        <span
                                            class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full bg-red-500/10 text-red-400 text-xs font-bold ring-1 ring-red-500/20">{{ $row['rusak_berat'] }}</span>
                                    @else
                                        <span class="text-gray-600">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex items-center justify-center min-w-[32px] px-3 py-1 rounded-full bg-primary-500/10 text-primary-400 text-sm font-black ring-1 ring-primary-500/20">{{ $row['jumlah'] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-8 py-12 text-center text-gray-500">
                                    <i class="ph ph-shield text-4xl mb-3 block opacity-30"></i>
                                    Tidak ada data ditemukan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($data->count() > 0)
                        <tfoot class="bg-gray-800/60">
                            <tr class="text-sm font-bold">
                                <td colspan="3"
                                    class="px-6 py-4 text-center text-gray-200 uppercase tracking-wider border-r border-gray-700">
                                    Total</td>
                                <td class="px-4 py-4 text-center border-r border-gray-700">
                                    <span class="text-green-400 text-base font-black">{{ $stats['total_baik'] }}</span>
                                </td>
                                <td class="px-4 py-4 text-center border-r border-gray-700">
                                    <span
                                        class="text-yellow-400 text-base font-black">{{ $stats['total_rusak_ringan'] }}</span>
                                </td>
                                <td class="px-4 py-4 text-center border-r border-gray-700">
                                    <span class="text-red-400 text-base font-black">{{ $stats['total_rusak_berat'] }}</span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="text-primary-400 text-lg font-black">{{ $stats['total'] }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body {
                background: white !important;
                color: black !important;
            }

            .glass-card {
                background: white !important;
                border: 1px solid #ccc !important;
                backdrop-filter: none !important;
            }

            aside,
            header,
            .ph-chat-centered-dots,
            button,
            a[onclick],
            form {
                display: none !important;
            }

            table th,
            table td {
                color: black !important;
                border: 1px solid #ccc !important;
            }

            table thead {
                background: #f3f4f6 !important;
            }

            table tfoot {
                background: #f3f4f6 !important;
            }

            span {
                color: black !important;
            }

            .text-green-400,
            .text-yellow-400,
            .text-red-400,
            .text-primary-400,
            .text-indigo-400,
            .text-purple-400 {
                color: black !important;
            }
        }
    </style>
</x-app-layout>