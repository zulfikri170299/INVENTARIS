<x-app-layout>
    <x-slot name="header">
        @if(auth()->user()->satker)
            {{ auth()->user()->satker->nama_satker }}
        @else
            {{ strtoupper(auth()->user()->role) }} - LOGISTIK
        @endif
    </x-slot>

    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-xl font-bold text-slate-800 tracking-tight">Dashboard</h1>
    </div>

    <div class="space-y-4 animate-fade-in">
        <!-- Main Stats Row - Vibrant Gradients -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Satker Card -->
            <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-indigo-600 to-indigo-500 p-3 shadow-lg shadow-indigo-500/20 group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-white backdrop-blur-md border border-white/30">
                        <i class="ph ph-buildings text-xl"></i>
                    </div>
                    <a href="{{ route('satkers.index') }}" class="px-3 py-1 bg-white/10 hover:bg-white/20 text-white rounded text-[9px] font-black uppercase tracking-wider backdrop-blur-md border border-white/20 transition-all">
                        Kelola
                    </a>
                </div>
                <div class="flex items-end space-x-3 text-white">
                    <span class="text-3xl font-black leading-none">{{ $satkers_count ?? 24 }}</span>
                    <span class="text-[11px] font-bold opacity-80 pb-0.5">Total Satker</span>
                </div>
            </div>

            <!-- Users Card -->
            <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-emerald-600 to-emerald-500 p-4 shadow-lg shadow-emerald-500/20 group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-white backdrop-blur-md border border-white/30">
                        <i class="ph ph-users text-xl"></i>
                    </div>
                    <a href="{{ route('users.index') }}" class="px-3 py-1 bg-white/10 hover:bg-white/20 text-white rounded text-[9px] font-black uppercase tracking-wider backdrop-blur-md border border-white/20 transition-all">
                        Kelola
                    </a>
                </div>
                <div class="flex items-end space-x-3 text-white">
                    <span class="text-3xl font-black leading-none">{{ $users_count ?? 230 }}</span>
                    <span class="text-[11px] font-bold opacity-80 pb-0.5">Total Users</span>
                </div>
            </div>

            <!-- Kendaraan Card -->
            <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-orange-600 to-orange-500 p-4 shadow-lg shadow-orange-500/20 group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-white backdrop-blur-md border border-white/30">
                        <i class="ph ph-truck text-xl"></i>
                    </div>
                    <a href="{{ route('kendaraan.index') }}" class="px-3 py-1 bg-white/10 hover:bg-white/20 text-white rounded text-[9px] font-black uppercase tracking-wider backdrop-blur-md border border-white/20 transition-all">
                        Kelola
                    </a>
                </div>
                <div class="flex items-end space-x-3 text-white">
                    <span class="text-3xl font-black leading-none">{{ $totalKendaraan }}</span>
                    <span class="text-[11px] font-bold opacity-80 pb-0.5">Total Kendaraan</span>
                </div>
            </div>

            <!-- Transaksi Card -->
            <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-pink-600 to-pink-500 p-4 shadow-lg shadow-pink-500/20 group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-white backdrop-blur-md border border-white/30">
                        <i class="ph ph-chart-line-up text-xl"></i>
                    </div>
                    <a href="{{ route('activity-logs.index') }}" class="px-3 py-1 bg-white/10 hover:bg-white/20 text-white rounded text-[9px] font-black uppercase tracking-wider backdrop-blur-md border border-white/20 transition-all">
                        Riwayat
                    </a>
                </div>
                <div class="flex items-end space-x-3 text-white">
                    <span class="text-3xl font-black leading-none">{{ $activity_logs_count ?? '1.149' }}</span>
                    <span class="text-[11px] font-bold opacity-80 pb-0.5">Total Log</span>
                </div>
            </div>
        </div>

        <!-- Horizontal Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- horizontal card 1 (Senjata) -->
            <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-indigo-700 to-indigo-500 p-3 shadow-lg shadow-indigo-500/10 group">
                <div class="flex items-center justify-between relative z-10">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-white backdrop-blur-md border border-white/30">
                            <i class="ph ph-shield-check text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="text-[9px] font-black text-white/70 uppercase tracking-widest leading-none mb-1">TOTAL DATA SENJATA</h4>
                            <div class="flex items-end space-x-2">
                                <span class="text-2xl font-black text-white leading-none">{{ $totalSenjata }}</span>
                                <span class="text-[10px] font-bold text-white/80 pb-0.5">Unit</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- horizontal card 2 (Amunisi) -->
            <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-red-600 to-rose-500 p-4 shadow-lg shadow-red-500/10 group">
                <div class="flex items-center justify-between relative z-10">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-white backdrop-blur-md border border-white/30">
                            <i class="ph ph-target text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="text-[9px] font-black text-white/70 uppercase tracking-widest leading-none mb-1">TOTAL STOK AMUNISI</h4>
                            <div class="flex items-end space-x-2">
                                <span class="text-2xl font-black text-white leading-none">{{ number_format($totalAmunisi, 0, ',', '.') }}</span>
                                <span class="text-[10px] font-bold text-white/80 pb-0.5">Butir</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SIMSA Row (Warning/Debt Style) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-[1.5rem] border border-orange-100 shadow-xl shadow-orange-500/5 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 bg-orange-100 rounded-2xl flex items-center justify-center text-orange-600 border border-orange-200">
                        <i class="ph ph-warning-circle text-3xl"></i>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-widest leading-none mb-1">SIMSA (HABIS MASA)</h4>
                        <div class="flex items-end space-x-2">
                            <span class="text-3xl font-black text-slate-800 leading-none">{{ $totalSimsa }}</span>
                            <span class="text-sm font-bold text-slate-500 pb-0.5">Personel</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[1.5rem] border border-red-100 shadow-xl shadow-red-500/5 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center text-red-600 border border-red-200">
                        <i class="ph ph-scales text-3xl"></i>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-widest leading-none mb-1">DATA RUSAK BERAT</h4>
                        <div class="flex items-end space-x-2">
                            <span class="text-3xl font-black text-slate-800 leading-none">{{ $total_rusak ?? '0' }}</span>
                            <span class="text-sm font-bold text-slate-500 pb-0.5">Unit</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Chart Row -->
        <div class="grid grid-cols-1 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-xl shadow-slate-200/50">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 border border-indigo-100">
                            <i class="ph ph-chart-pie-slice text-xl"></i>
                        </div>
                        <h4 class="text-base font-bold text-slate-800 tracking-tight">Statistik Distribusi Item</h4>
                    </div>
                </div>
                <div class="relative h-64">
                    <canvas id="simsaChart"></canvas>
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
                            var color = '#1e293b';
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
                            borderColor: '#ffffff'
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
                                    color: getComputedStyle(document.documentElement).getPropertyValue('--text-muted').trim(),
                                    font: { family: 'Outfit', size: 11, weight: '600' }
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
                                color: getComputedStyle(document.documentElement).getPropertyValue('--text-main').trim()
                            }
                        }
                    },
                    plugins: [centerTextPlugin]
                });
            });
        </script>

    </div>
</x-app-layout>