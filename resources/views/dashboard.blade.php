<x-app-layout>
    <x-slot name="header">
        Statistik Inventaris
    </x-slot>

    <div class="space-y-8 animate-fade-in">
        <!-- Dashboard Welcome -->
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-8 rounded-3xl relative overflow-hidden shadow-xl">
            <div class="absolute top-0 right-0 -m-8 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="relative z-10">
                <h3 class="text-3xl font-bold text-white mb-2">Selamat Datang, {{ Auth::user()->name }}! 👋</h3>
                <p class="text-white/90 max-w-2xl text-lg">Kelola aset dan inventaris alat dengan mudah dan cepat
                    melalui dashboard terpadu ini.</p>
            </div>
        </div>

        <!-- Stats Grid - Main Totals -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card 1 - Senjata -->
            <a href="{{ route('senjata.index') }}"
                class="bg-gradient-to-br from-blue-400 to-blue-600 p-6 rounded-2xl hover:scale-[1.05] transition-all transform duration-300 shadow-lg hover:shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-white backdrop-blur-sm">
                        <i class="ph ph-shield-check text-2xl"></i>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full">
                        <i class="ph ph-arrow-right text-white"></i>
                    </div>
                </div>
                <h4 class="text-white/80 font-medium text-sm">Total Senjata</h4>
                <p class="text-4xl font-bold text-white mt-1">{{ $totalSenjata }}</p>
            </a>

            <!-- Card 2 - Kendaraan -->
            <a href="{{ route('kendaraan.index') }}"
                class="bg-gradient-to-br from-purple-400 to-purple-600 p-6 rounded-2xl hover:scale-[1.05] transition-all transform duration-300 shadow-lg hover:shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-white backdrop-blur-sm">
                        <i class="ph ph-truck text-2xl"></i>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full">
                        <i class="ph ph-arrow-right text-white"></i>
                    </div>
                </div>
                <h4 class="text-white/80 font-medium text-sm">Total Kendaraan</h4>
                <p class="text-4xl font-bold text-white mt-1">{{ $totalKendaraan }}</p>
            </a>

            <!-- Card 3 - Alsus -->
            <a href="{{ route('alsus.index') }}"
                class="bg-gradient-to-br from-orange-400 to-orange-600 p-6 rounded-2xl hover:scale-[1.05] transition-all transform duration-300 shadow-lg hover:shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-white backdrop-blur-sm">
                        <i class="ph ph-wrench text-2xl"></i>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full">
                        <i class="ph ph-arrow-right text-white"></i>
                    </div>
                </div>
                <h4 class="text-white/80 font-medium text-sm">Alat Khusus</h4>
                <p class="text-4xl font-bold text-white mt-1">{{ $totalAlsus }}</p>
            </a>

            <!-- Card 4 - Alsintor -->
            <a href="{{ route('alsintor.index') }}"
                class="bg-gradient-to-br from-green-400 to-green-600 p-6 rounded-2xl hover:scale-[1.05] transition-all transform duration-300 shadow-lg hover:shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-white backdrop-blur-sm">
                        <i class="ph ph-farm text-2xl"></i>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full">
                        <i class="ph ph-arrow-right text-white"></i>
                    </div>
                </div>
                <h4 class="text-white/80 font-medium text-sm">Alsintor</h4>
                <p class="text-4xl font-bold text-white mt-1">{{ $totalAlsintor }}</p>
            </a>
        </div>

        <!-- Kendaraan Details by Roda -->
        <div class="bg-white p-6 rounded-3xl shadow-lg border border-gray-200">
            <h4 class="font-bold text-xl text-gray-800 mb-4 flex items-center">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center mr-3">
                    <i class="ph ph-truck text-purple-600 text-xl"></i>
                </div>
                Rincian Kendaraan Berdasarkan Roda
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div
                    class="bg-gradient-to-br from-pink-50 to-pink-100 p-5 rounded-xl border-2 border-pink-200 hover:shadow-md transition-shadow">
                    <div class="text-xs font-semibold text-pink-600 uppercase mb-2">Roda 2 (R2)</div>
                    <div class="text-3xl font-bold text-pink-700">{{ $kendaraanR2 }}</div>
                    <i class="ph ph-motorcycle text-pink-300 text-4xl absolute right-4 bottom-4 opacity-20"></i>
                </div>
                <div
                    class="bg-gradient-to-br from-blue-50 to-blue-100 p-5 rounded-xl border-2 border-blue-200 hover:shadow-md transition-shadow">
                    <div class="text-xs font-semibold text-blue-600 uppercase mb-2">Roda 4 (R4)</div>
                    <div class="text-3xl font-bold text-blue-700">{{ $kendaraanR4 }}</div>
                    <i class="ph ph-car text-blue-300 text-4xl absolute right-4 bottom-4 opacity-20"></i>
                </div>
                <div
                    class="bg-gradient-to-br from-purple-50 to-purple-100 p-5 rounded-xl border-2 border-purple-200 hover:shadow-md transition-shadow">
                    <div class="text-xs font-semibold text-purple-600 uppercase mb-2">Roda 6 (R6)</div>
                    <div class="text-3xl font-bold text-purple-700">{{ $kendaraanR6 }}</div>
                    <i class="ph ph-truck text-purple-300 text-4xl absolute right-4 bottom-4 opacity-20"></i>
                </div>
                <div
                    class="bg-gradient-to-br from-indigo-50 to-indigo-100 p-5 rounded-xl border-2 border-indigo-200 hover:shadow-md transition-shadow">
                    <div class="text-xs font-semibold text-indigo-600 uppercase mb-2">Roda 8 (R8)</div>
                    <div class="text-3xl font-bold text-indigo-700">{{ $kendaraanR8 }}</div>
                    <i class="ph ph-truck-trailer text-indigo-300 text-4xl absolute right-4 bottom-4 opacity-20"></i>
                </div>
            </div>
        </div>

        <!-- Senjata Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Senjata by Laras -->
            <div class="bg-white p-6 rounded-3xl shadow-lg border border-gray-200">
                <h4 class="font-bold text-xl text-gray-800 mb-4 flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mr-3">
                        <i class="ph ph-shield-check text-blue-600 text-xl"></i>
                    </div>
                    Senjata Berdasarkan Laras
                </h4>
                <div class="space-y-3">
                    <div
                        class="bg-gradient-to-r from-cyan-50 to-cyan-100 p-5 rounded-xl border-2 border-cyan-200 flex justify-between items-center hover:shadow-md transition-shadow">
                        <div>
                            <div class="text-xs font-semibold text-cyan-600 uppercase">Laras Panjang</div>
                            <div class="text-3xl font-bold text-cyan-700 mt-1">{{ $senjataPanjang }}</div>
                        </div>
                        <div class="w-16 h-16 bg-cyan-200/50 rounded-full flex items-center justify-center">
                            <i class="ph ph-crosshair text-4xl text-cyan-600"></i>
                        </div>
                    </div>
                    <div
                        class="bg-gradient-to-r from-teal-50 to-teal-100 p-5 rounded-xl border-2 border-teal-200 flex justify-between items-center hover:shadow-md transition-shadow">
                        <div>
                            <div class="text-xs font-semibold text-teal-600 uppercase">Laras Pendek</div>
                            <div class="text-3xl font-bold text-teal-700 mt-1">{{ $senjataPendek }}</div>
                        </div>
                        <div class="w-16 h-16 bg-teal-200/50 rounded-full flex items-center justify-center">
                            <i class="ph ph-target text-4xl text-teal-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Senjata by Status Penyimpanan -->
            <div class="bg-white p-6 rounded-3xl shadow-lg border border-gray-200">
                <h4 class="font-bold text-xl text-gray-800 mb-4 flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mr-3">
                        <i class="ph ph-package text-green-600 text-xl"></i>
                    </div>
                    Status Penyimpanan Senjata
                </h4>
                <div class="space-y-3">
                    <div
                        class="bg-gradient-to-r from-blue-50 to-blue-100 p-5 rounded-xl border-2 border-blue-200 flex justify-between items-center hover:shadow-md transition-shadow">
                        <div>
                            <div class="text-xs font-semibold text-blue-600 uppercase">Di Gudang</div>
                            <div class="text-3xl font-bold text-blue-700 mt-1">{{ $senjataGudang }}</div>
                        </div>
                        <div class="w-16 h-16 bg-blue-200/50 rounded-full flex items-center justify-center">
                            <i class="ph ph-warehouse text-4xl text-blue-600"></i>
                        </div>
                    </div>
                    <div
                        class="bg-gradient-to-r from-emerald-50 to-emerald-100 p-5 rounded-xl border-2 border-emerald-200 flex justify-between items-center hover:shadow-md transition-shadow">
                        <div>
                            <div class="text-xs font-semibold text-emerald-600 uppercase">Pegang Personel</div>
                            <div class="text-3xl font-bold text-emerald-700 mt-1">{{ $senjataPersonel }}</div>
                        </div>
                        <div class="w-16 h-16 bg-emerald-200/50 rounded-full flex items-center justify-center">
                            <i class="ph ph-user-circle text-4xl text-emerald-600"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SIMSA Monitoring -->
        <div class="bg-white rounded-3xl overflow-hidden shadow-lg border border-gray-200">
            <div
                class="px-8 py-6 bg-gradient-to-r from-amber-50 to-orange-50 border-b border-orange-200 flex items-center justify-between">
                <h4 class="font-bold text-xl text-gray-800 flex items-center">
                    <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center mr-3">
                        <i class="ph ph-warning-circle text-orange-600 text-xl"></i>
                    </div>
                    Monitoring Masa Berlaku SIMSA
                </h4>
            </div>

            @if($simsaExpired->count() > 0 || $simsaNearExpiry->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider border-b border-gray-200">
                            @if(in_array(auth()->user()->role, ['Super Admin', 'Pimpinan']))
                                <th class="px-8 py-4 font-semibold">Satker</th>
                            @endif
                            <th class="px-8 py-4 font-semibold">Jenis Senpi</th>
                            <th class="px-8 py-4 font-semibold">Penanggung Jawab</th>
                            <th class="px-8 py-4 font-semibold">PANGKAT/NRP</th>
                            <th class="px-8 py-4 font-semibold">Masa Berlaku</th>
                            <th class="px-8 py-4 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @foreach($simsaExpired as $item)
                                <tr class="hover:bg-red-50 transition-colors bg-red-50/50">
                                    @if(in_array(auth()->user()->role, ['Super Admin', 'Pimpinan']))
                                        <td class="px-8 py-4 text-xs">{{ $item->satker->nama_satker ?? '-' }}</td>
                                    @endif
                                    <td class="px-8 py-4 font-semibold text-gray-900">{{ $item->jenis_senpi }}</td>
                                    <td class="px-8 py-4">{{ $item->penanggung_jawab ?? '-' }}</td>
                                    <td class="px-8 py-4 text-xs font-mono text-gray-600">{{ $item->nrp ?? '-' }}</td>
                                    <td class="px-8 py-4 text-red-600 font-bold">
                                        {{ \Carbon\Carbon::parse($item->masa_berlaku_simsa)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-8 py-4">
                                        <span
                                            class="px-3 py-1.5 rounded-full bg-red-100 text-red-700 text-xs font-bold border border-red-200">
                                            <i class="ph ph-x-circle mr-1"></i>HABIS
                                        </span>
                                    </td>
                                </tr>
                            @endforeach

                            @foreach($simsaNearExpiry as $item)
                                @php
                                    $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($item->masa_berlaku_simsa), false);
                                @endphp
                                <tr class="hover:bg-yellow-50 transition-colors bg-yellow-50/50">
                                    @if(in_array(auth()->user()->role, ['Super Admin', 'Pimpinan']))
                                        <td class="px-8 py-4 text-xs">{{ $item->satker->nama_satker ?? '-' }}</td>
                                    @endif
                                    <td class="px-8 py-4 font-semibold text-gray-900">{{ $item->jenis_senpi }}</td>
                                    <td class="px-8 py-4">{{ $item->penanggung_jawab ?? '-' }}</td>
                                    <td class="px-8 py-4 text-xs font-mono text-gray-600">{{ $item->nrp ?? '-' }}</td>
                                    <td class="px-8 py-4 text-amber-600 font-bold">
                                        {{ \Carbon\Carbon::parse($item->masa_berlaku_simsa)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-8 py-4">
                                        <span
                                            class="px-3 py-1.5 rounded-full bg-amber-100 text-amber-700 text-xs font-bold border border-amber-200">
                                            <i class="ph ph-warning mr-1"></i>{{ ceil($daysLeft) }} HARI LAGI
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-8 py-16 text-center bg-gradient-to-b from-green-50 to-emerald-50">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ph ph-check-circle text-5xl text-green-600"></i>
                    </div>
                    <p class="text-gray-600 font-medium text-lg">Semua SIMSA masih dalam masa berlaku yang aman</p>
                    <p class="text-gray-500 text-sm mt-2">Tidak ada senjata yang memerlukan perhatian khusus</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>