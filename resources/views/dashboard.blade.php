<x-app-layout>
    <x-slot name="header">
        @if(auth()->user()->satker)
            {{ auth()->user()->satker->nama_satker }}
        @else
            {{ strtoupper(auth()->user()->role) }} - LOGISTIK
        @endif
    </x-slot>

    <div class="space-y-4 lg:space-y-6 animate-fade-in">
        <!-- Dashboard Welcome -->
        <div
            class="bg-gradient-to-r from-blue-500 to-purple-600 p-4 lg:p-8 rounded-xl lg:rounded-2xl relative overflow-hidden shadow-lg mb-4 lg:mb-8">
            <div class="absolute top-0 right-0 -m-8 w-48 lg:w-64 h-48 lg:h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="relative z-10">
                <h3 class="text-xl lg:text-3xl font-bold text-white mb-2">Selamat Datang,
                    {{ Auth::user()->name }}! 👋
                </h3>
                <p class="text-white/90 max-w-2xl text-xs lg:text-base">
                    @if(auth()->user()->satker)
                        {{ auth()->user()->satker->nama_satker }} —
                    @endif
                    Kelola aset dan inventaris alat dengan mudah dan cepat melalui dashboard terpadu ini.
                </p>
            </div>
        </div>

        <!-- Stats Grid - Main Totals -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 lg:gap-6">
            <!-- Card 1 - Senjata -->
            <a href="{{ route('senjata.index') }}"
                class="bg-gradient-to-br from-blue-400 to-blue-600 px-4 lg:px-6 py-3 lg:py-4 rounded-xl hover:scale-[1.02] transition-all transform duration-300 shadow-md hover:shadow-lg group">
                <div class="flex items-center justify-between mb-2 lg:mb-3">
                    <div
                        class="w-8 lg:w-10 h-8 lg:h-10 bg-white/20 rounded-lg flex items-center justify-center text-white backdrop-blur-sm group-hover:bg-white/30 transition-all">
                        <i class="ph ph-shield-check text-lg lg:text-xl"></i>
                    </div>
                    <div
                        class="bg-white/20 backdrop-blur-sm px-1.5 lg:px-2 py-0.5 rounded-full group-hover:bg-white/30 transition-all">
                        <i class="ph ph-arrow-right text-white text-[10px] lg:text-xs"></i>
                    </div>
                </div>
                <h4 class="text-white/80 font-medium text-[10px] lg:text-xs">Total Senjata</h4>
                <div class="flex items-baseline justify-between mt-0.5 lg:mt-1">
                    <p class="text-xl lg:text-3xl font-bold text-white leading-none">{{ $totalSenjata }}</p>
                    <div class="text-right flex flex-col items-end leading-tight">
                        <span class="text-[8px] lg:text-[9px] text-white/70 font-semibold uppercase tracking-wider">P:
                            <span class="text-white text-[10px] lg:text-xs">{{ $senjataPanjang }}</span></span>
                        <span class="text-[8px] lg:text-[9px] text-white/70 font-semibold uppercase tracking-wider">D:
                            <span class="text-white text-[10px] lg:text-xs">{{ $senjataPendek }}</span></span>
                    </div>
                </div>
            </a>

            <!-- Card 2 - Amunisi -->
            <a href="{{ route('amunisi.index') }}"
                class="bg-gradient-to-br from-red-400 to-red-600 px-4 lg:px-6 py-3 lg:py-4 rounded-xl hover:scale-[1.02] transition-all transform duration-300 shadow-md hover:shadow-lg">
                <div class="flex items-center justify-between mb-2 lg:mb-3">
                    <div
                        class="w-8 lg:w-10 h-8 lg:h-10 bg-white/20 rounded-lg flex items-center justify-center text-white backdrop-blur-sm">
                        <i class="ph ph-target text-lg lg:text-xl"></i>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm px-1.5 lg:px-2 py-0.5 rounded-full">
                        <i class="ph ph-arrow-right text-white text-[10px] lg:text-xs"></i>
                    </div>
                </div>
                <h4 class="text-white/80 font-medium text-[10px] lg:text-xs">Total Amunisi</h4>
                <p class="text-xl lg:text-3xl font-bold text-white mt-0.5 lg:mt-1 leading-none">
                    {{ number_format($totalAmunisi, 0, ',', '.') }}
                </p>
            </a>

            <!-- Card 3 - Kendaraan -->
            <a href="{{ route('kendaraan.index') }}"
                class="bg-gradient-to-br from-purple-400 to-purple-600 px-4 lg:px-6 py-3 lg:py-4 rounded-xl hover:scale-[1.02] transition-all transform duration-300 shadow-md hover:shadow-lg group">
                <div class="flex items-center justify-between mb-2 lg:mb-3">
                    <div
                        class="w-8 lg:w-10 h-8 lg:h-10 bg-white/20 rounded-lg flex items-center justify-center text-white backdrop-blur-sm group-hover:bg-white/30 transition-all">
                        <i class="ph ph-truck text-lg lg:text-xl"></i>
                    </div>
                    <div
                        class="bg-white/20 backdrop-blur-sm px-1.5 lg:px-2 py-0.5 rounded-full group-hover:bg-white/30 transition-all">
                        <i class="ph ph-arrow-right text-white text-[10px] lg:text-xs"></i>
                    </div>
                </div>
                <h4 class="text-white/80 font-medium text-[10px] lg:text-xs">Total Kendaraan</h4>
                <div class="flex items-baseline justify-between mt-0.5 lg:mt-1">
                    <p class="text-xl lg:text-3xl font-bold text-white leading-none">{{ $totalKendaraan }}</p>
                    <div class="grid grid-cols-2 gap-x-1.5 lg:gap-x-2 text-right leading-tight">
                        <span class="text-[8px] lg:text-[9px] text-white/70 font-semibold uppercase tracking-wider">R2:
                            <span class="text-white text-[10px] lg:text-xs">{{ $kendaraanR2 }}</span></span>
                        <span class="text-[8px] lg:text-[9px] text-white/70 font-semibold uppercase tracking-wider">R4:
                            <span class="text-white text-[10px] lg:text-xs">{{ $kendaraanR4 }}</span></span>
                    </div>
                </div>
            </a>

            <!-- Card 4 - Alsus -->
            <a href="{{ route('alsus.index') }}"
                class="bg-gradient-to-br from-orange-400 to-orange-600 px-4 lg:px-6 py-3 lg:py-4 rounded-xl hover:scale-[1.02] transition-all transform duration-300 shadow-md hover:shadow-lg">
                <div class="flex items-center justify-between mb-2 lg:mb-3">
                    <div
                        class="w-8 lg:w-10 h-8 lg:h-10 bg-white/20 rounded-lg flex items-center justify-center text-white backdrop-blur-sm">
                        <i class="ph ph-wrench text-lg lg:text-xl"></i>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm px-1.5 lg:px-2 py-0.5 rounded-full">
                        <i class="ph ph-arrow-right text-white text-[10px] lg:text-xs"></i>
                    </div>
                </div>
                <h4 class="text-white/80 font-medium text-[10px] lg:text-xs">Alat Khusus</h4>
                <p class="text-xl lg:text-3xl font-bold text-white mt-0.5 lg:mt-1 leading-none">{{ $totalAlsus }}</p>
            </a>

            <!-- Card 5 - Alsintor -->
            <a href="{{ route('alsintor.index') }}"
                class="bg-gradient-to-br from-green-400 to-green-600 px-4 lg:px-6 py-3 lg:py-4 rounded-xl hover:scale-[1.02] transition-all transform duration-300 shadow-md hover:shadow-lg">
                <div class="flex items-center justify-between mb-2 lg:mb-3">
                    <div
                        class="w-8 lg:w-10 h-8 lg:h-10 bg-white/20 rounded-lg flex items-center justify-center text-white backdrop-blur-sm">
                        <i class="ph ph-farm text-lg lg:text-xl"></i>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm px-1.5 lg:px-2 py-0.5 rounded-full">
                        <i class="ph ph-arrow-right text-white text-[10px] lg:text-xs"></i>
                    </div>
                </div>
                <h4 class="text-white/80 font-medium text-[10px] lg:text-xs">Alsintor</h4>
                <p class="text-xl lg:text-3xl font-bold text-white mt-0.5 lg:mt-1 leading-none">{{ $totalAlsintor }}</p>
            </a>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-8 mt-4 lg:mt-8">

            <!-- SIMSA Chart -->
            <div
                class="glass-card px-4 lg:px-8 py-4 lg:py-6 rounded-xl lg:rounded-2xl shadow-lg lg:shadow-xl border border-white/5 relative overflow-hidden group">
                <div
                    class="absolute top-0 right-0 -m-4 w-16 lg:w-24 h-16 lg:h-24 bg-orange-500/5 rounded-full blur-2xl group-hover:bg-orange-500/10 transition-all duration-500">
                </div>
                <div class="flex items-center justify-between mb-3 lg:mb-4 relative z-10">
                    <h4
                        class="font-bold text-[10px] lg:text-base text-gray-100 flex items-center tracking-tight text-orange-100">
                        <div
                            class="w-6 lg:w-8 h-6 lg:h-8 bg-orange-500/10 rounded-lg flex items-center justify-center mr-2 lg:mr-3 border border-orange-500/20">
                            <i class="ph ph-warning-circle text-orange-400 text-xs lg:text-xl"></i>
                        </div>
                        SIMSA (Habis Masa)
                    </h4>
                    <a href="{{ route('senjata.index') }}"
                        class="w-7 lg:w-8 h-7 lg:h-8 flex items-center justify-center rounded-full bg-white/5 hover:bg-white/10 text-white/50 hover:text-white transition-all"
                        title="Lihat Data Sumber">
                        <i class="ph ph-arrow-square-out text-base lg:text-lg"></i>
                    </a>
                </div>
                <div class="relative h-24 lg:h-48 z-10">
                    <canvas id="simsaChart"></canvas>
                </div>
            </div>

            <!-- Penghapusan Chart -->
            <div
                class="glass-card px-4 lg:px-8 py-4 lg:py-6 rounded-xl lg:rounded-2xl shadow-lg lg:shadow-xl border border-white/5 relative overflow-hidden group">
                <div
                    class="absolute top-0 right-0 -m-4 w-16 lg:w-24 h-16 lg:h-24 bg-red-500/5 rounded-full blur-2xl group-hover:bg-red-500/10 transition-all duration-500">
                </div>
                <div class="flex items-center justify-between mb-3 lg:mb-4 relative z-10">
                    <h4
                        class="font-bold text-[10px] lg:text-base text-gray-100 flex items-center tracking-tight text-red-100">
                        <div
                            class="w-6 lg:w-8 h-6 lg:h-8 bg-red-500/10 rounded-lg flex items-center justify-center mr-2 lg:mr-3 border border-red-500/20">
                            <i class="ph ph-trash text-red-400 text-xs lg:text-xl"></i>
                        </div>
                        Penghapusan
                    </h4>
                    <a href="{{ route('pengajuan-berkas.index', ['kategori' => 'penghapusan']) }}"
                        class="w-7 lg:w-8 h-7 lg:h-8 flex items-center justify-center rounded-full bg-white/5 hover:bg-white/10 text-white/50 hover:text-white transition-all"
                        title="Lihat Data Sumber">
                        <i class="ph ph-arrow-square-out text-base lg:text-lg"></i>
                    </a>
                </div>
                <div class="relative h-24 lg:h-48 z-10">
                    <canvas id="penghapusanChart"></canvas>
                </div>
            </div>

            <!-- Penetapan Chart -->
            <div
                class="glass-card px-4 lg:px-8 py-4 lg:py-6 rounded-xl lg:rounded-2xl shadow-lg lg:shadow-xl border border-white/5 relative overflow-hidden group">
                <div
                    class="absolute top-0 right-0 -m-4 w-16 lg:w-24 h-16 lg:h-24 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-all duration-500">
                </div>
                <div class="flex items-center justify-between mb-3 lg:mb-4 relative z-10">
                    <h4
                        class="font-bold text-[10px] lg:text-base text-gray-100 flex items-center tracking-tight text-blue-100">
                        <div
                            class="w-6 lg:w-8 h-6 lg:h-8 bg-blue-500/10 rounded-lg flex items-center justify-center mr-2 lg:mr-3 border border-blue-500/20">
                            <i class="ph ph-certificate text-blue-400 text-xs lg:text-xl"></i>
                        </div>
                        Penetapan Status
                    </h4>
                    <a href="{{ route('pengajuan-berkas.index', ['kategori' => 'penetapan_status']) }}"
                        class="w-7 lg:w-8 h-7 lg:h-8 flex items-center justify-center rounded-full bg-white/5 hover:bg-white/10 text-white/50 hover:text-white transition-all"
                        title="Lihat Data Sumber">
                        <i class="ph ph-arrow-square-out text-base lg:text-lg"></i>
                    </a>
                </div>
                <div class="relative h-24 lg:h-48 z-10">
                    <canvas id="penetapanChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Common plugin to draw text in center of doughnut
                const centerTextPlugin = {
                    id: 'centerText',
                    beforeDraw: function (chart) {
                        if (chart.config.options.elements.center) {
                            var ctx = chart.ctx;
                            var centerConfig = chart.config.options.elements.center;
                            var fontStyle = centerConfig.fontStyle || 'Outfit';
                            var txt = centerConfig.text;
                            var color = centerConfig.color || '#f1f5f9';
                            var sidePadding = centerConfig.sidePadding || 20;
                            var sidePaddingCalculated = (sidePadding / 100) * (chart.innerRadius * 2);
                            ctx.font = "bold 30px " + fontStyle;
                            var stringWidth = ctx.measureText(txt).width;
                            var elementWidth = (chart.innerRadius * 2) - sidePaddingCalculated;
                            var widthRatio = elementWidth / stringWidth;
                            var newFontSize = Math.floor(30 * widthRatio);
                            var elementHeight = (chart.innerRadius * 2);
                            var fontSizeToUse = Math.min(newFontSize, elementHeight, 40);
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            var centerX = ((chart.chartArea.left + chart.chartArea.right) / 2);
                            var centerY = ((chart.chartArea.top + chart.chartArea.bottom) / 2);
                            ctx.font = "bold " + fontSizeToUse + "px " + fontStyle;
                            ctx.fillStyle = color;
                            ctx.fillText(txt, centerX, centerY);
                        }
                    }
                };

                // SIMSA Chart
                new Chart(document.getElementById('simsaChart'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Habis', 'Hampir', 'Berlaku'],
                        datasets: [{
                            data: [{{ $simsaExpiredCount }}, {{ $simsaNearExpiryCount }}, {{ $simsaSafeCount }}],
                            backgroundColor: ['#ef4444', '#facc15', '#22c55e'],
                            hoverOffset: 12,
                            borderWidth: 2,
                            borderColor: '#1e293b'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15,
                                    color: '#f8fafc',
                                    font: { family: 'Outfit', size: 10, weight: 'bold' }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                titleFont: { family: 'Outfit', size: 11 },
                                bodyFont: { family: 'Outfit', size: 10 },
                                padding: 10,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function (context) {
                                        return ' ' + context.label + ': ' + context.raw + ' Pucuk';
                                    }
                                }
                            }
                        },
                        cutout: '75%',
                        elements: {
                            center: {
                                text: '{{ $totalSimsa }}',
                                color: '#f1f5f9'
                            }
                        }
                    },
                    plugins: [centerTextPlugin]
                });

                // Penghapusan Chart
                new Chart(document.getElementById('penghapusanChart'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Diajukan', 'Proses', 'Revisi', 'Selesai'],
                        datasets: [{
                            data: [
                                {{ $penghapusanCounts['diajukan'] + $penghapusanCounts['diterima'] }},
                                {{ $penghapusanCounts['diproses'] + $penghapusanCounts['naik_ke_kapolda'] + $penghapusanCounts['ditandatangani'] }},
                                {{ $penghapusanCounts['dikembalikan'] }},
                                {{ $penghapusanCounts['selesai'] }}
                            ],
                            backgroundColor: ['#3b82f6', '#a855f7', '#ef4444', '#22c55e'],
                            hoverOffset: 12,
                            borderWidth: 2,
                            borderColor: '#1e293b'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15,
                                    color: '#f8fafc',
                                    font: { family: 'Outfit', size: 10, weight: 'bold' }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                titleFont: { family: 'Outfit', size: 11 },
                                bodyFont: { family: 'Outfit', size: 10 },
                                padding: 10,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function (context) {
                                        return ' ' + context.label + ': ' + context.raw + ' Berkas';
                                    }
                                }
                            }
                        },
                        cutout: '75%',
                        elements: {
                            center: {
                                text: '{{ $totalPenghapusan }}',
                                color: '#f1f5f9'
                            }
                        }
                    },
                    plugins: [centerTextPlugin]
                });

                // Penetapan Status Chart
                new Chart(document.getElementById('penetapanChart'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Diajukan', 'Proses', 'Revisi', 'Selesai'],
                        datasets: [{
                            data: [
                                {{ $penetapanCounts['diajukan'] + $penetapanCounts['diterima'] }},
                                {{ $penetapanCounts['diproses'] + $penetapanCounts['naik_ke_kapolda'] + $penetapanCounts['ditandatangani'] }},
                                {{ $penetapanCounts['dikembalikan'] }},
                                {{ $penetapanCounts['selesai'] }}
                            ],
                            backgroundColor: ['#3b82f6', '#a855f7', '#ef4444', '#22c55e'],
                            hoverOffset: 12,
                            borderWidth: 2,
                            borderColor: '#1e293b'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15,
                                    color: '#f8fafc',
                                    font: { family: 'Outfit', size: 10, weight: 'bold' }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                titleFont: { family: 'Outfit', size: 11 },
                                bodyFont: { family: 'Outfit', size: 10 },
                                padding: 10,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function (context) {
                                        return ' ' + context.label + ': ' + context.raw + ' Berkas';
                                    }
                                }
                            }
                        },
                        cutout: '75%',
                        elements: {
                            center: {
                                text: '{{ $totalPenetapan }}',
                                color: '#f1f5f9'
                            }
                        }
                    },
                    plugins: [centerTextPlugin]
                });
            });
        </script>

    </div>
</x-app-layout>