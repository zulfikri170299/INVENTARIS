<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="themeManager()" x-init="init()">
<script>
    // Immediate theme detection to prevent flash
    (function() {
        const isDark = localStorage.getItem('isDark') !== 'false';
        const theme = localStorage.getItem('theme') || 'Azure';
        
        if (isDark) {
            document.documentElement.classList.add('dark');
            document.documentElement.style.backgroundColor = '#0b1120';
        } else {
            document.documentElement.classList.remove('dark');
            document.documentElement.style.backgroundColor = '#f1f5f9';
        }
        
        const themes = {
            'Azure': {'50': '#f0f9ff', '100': '#e0f2fe', '200': '#bae6fd', '300': '#7dd3fc', '400': '#38bdf8', '500': '#0ea5e9', '600': '#0284c7', '700': '#0369a1', '800': '#075985', '900': '#0c4a6e'},
            'Emerald': {'50': '#ecfdf5', '100': '#d1fae5', '200': '#a7f3d0', '300': '#6ee7b7', '400': '#34d399', '500': '#10b981', '600': '#059669', '700': '#047857', '800': '#065f46', '900': '#064e3b'},
            'Indigo': {'50': '#eef2ff', '100': '#e0e7ff', '200': '#c7d2fe', '300': '#a5b4fc', '400': '#818cf8', '500': '#6366f1', '600': '#4f46e5', '700': '#4338ca', '800': '#3730a3', '900': '#312e81'},
            'Rose': {'50': '#fff1f2', '100': '#ffe4e6', '200': '#fecdd3', '300': '#fda4af', '400': '#fb7185', '500': '#f43f5e', '600': '#e11d48', '700': '#be123c', '800': '#9f1239', '900': '#881337'},
            'Amber': {'50': '#fffbeb', '100': '#fef3c7', '200': '#fde68a', '300': '#fcd34d', '400': '#fbbf24', '500': '#f59e0b', '600': '#d97706', '700': '#b45309', '800': '#92400e', '900': '#78350f'},
            'Slate': {'50': '#f8fafc', '100': '#f1f5f9', '200': '#e2e8f0', '300': '#cbd5e1', '400': '#94a3b8', '500': '#64748b', '600': '#475569', '700': '#334155', '800': '#1e293b', '900': '#0f172a'}
        };
        const colors = themes[theme];
        if (colors) {
            Object.keys(colors).forEach(key => {
                document.documentElement.style.setProperty(`--primary-${key}`, colors[key]);
            });
        }
    })();
</script>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Inventaris') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('rolog.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <!-- Styles & Scripts (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style id="theme-styles">
        :root {
            /* Default Blue */
            --primary-50: #f0f9ff;
            --primary-100: #e0f2fe;
            --primary-200: #bae6fd;
            --primary-300: #7dd3fc;
            --primary-400: #38bdf8;
            --primary-500: #0ea5e9;
            --primary-600: #0284c7;
            --primary-700: #0369a1;
            --primary-800: #075985;
            --primary-900: #0c4a6e;
            --secondary: #1e293b;
            
            --bg-main: #0b1120;
            --bg-sidebar: #0f172a;
            --bg-header: rgba(15, 23, 42, 0.5);
            --bg-card: rgba(30, 41, 59, 0.7);
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --border-color: rgba(255, 255, 255, 0.05);
            --table-header: #1a1e2e;
        }

        html:not(.dark) {
            --bg-main: #f1f5f9;
            --bg-sidebar: #ffffff;
            --bg-header: rgba(255, 255, 255, 0.8);
            --bg-card: rgba(255, 255, 255, 0.95);
            --text-main: #0f172a;
            --text-muted: #475569;
            --border-color: rgba(0, 0, 0, 0.08);
            --table-header: #e2e8f0;
        }

        /* Standardize text and table elements to use variables */
        .text-gray-50, .text-gray-100, .text-gray-200, .text-gray-300, .text-gray-400, .text-white, .text-slate-50, .text-slate-100, .text-slate-200, .text-slate-300, .text-slate-400 { color: var(--text-main) !important; }
        .text-gray-500, .text-gray-600, .text-gray-700, .text-gray-800, .text-gray-900, .text-slate-500, .text-slate-600, .text-slate-700, .text-slate-800, .text-slate-900 { color: var(--text-muted) !important; }
        
        .border-gray-800, .border-gray-700, .border-gray-600, .border-slate-800, .border-slate-700, .border-slate-600 { border-color: var(--border-color) !important; }
        
        .bg-gray-800, .bg-gray-900, .bg-slate-800, .bg-slate-900, .bg-[#1a1e2e], .bg-[#0f172a] { 
            background-color: var(--bg-card) !important; 
        }
        
        /* Buttons should be slightly different from cards to remain visible */
        .btn-compact.bg-gray-800, .bg-gray-800[class*="hover:"], .p-1.5.bg-gray-800, button.bg-gray-800 {
            background-color: var(--bg-main) !important;
            border-color: var(--border-color) !important;
        }

        .bg-gray-700, .bg-slate-700 { background-color: var(--bg-main) !important; }
        
        /* Table and Input specific contrast fixes */
        .table-excel { background: transparent !important; }
        .table-excel th { color: var(--text-main) !important; background: var(--table-header) !important; border-color: var(--border-color) !important; }
        .table-excel td { color: var(--text-main) !important; border-color: var(--border-color) !important; }
        
        input, select, textarea { 
            background-color: var(--bg-main) !important; 
            color: var(--text-main) !important;
            border-color: var(--border-color) !important;
        }
        
        input::placeholder {
            color: var(--text-muted) !important;
            opacity: 0.5;
        }

        /* Sidebar background fix */
        aside, .bg-[#0f172a] { background-color: var(--bg-sidebar) !important; }
        header, .bg-[#0f172a]\/50 { background-color: var(--bg-header) !important; }
        
        /* Premium Sidebar Styling */
        aside {
            background-color: var(--bg-sidebar) !important;
            border-right: 1px solid var(--border-color);
        }

        aside a {
            position: relative;
            color: var(--text-muted) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin: 1px 8px;
            padding-top: 4px !important;
            padding-bottom: 4px !important;
            border-radius: 8px;
            display: flex;
            align-items: center;
        }

        aside a i, aside a span {
            background-color: transparent !important; /* Fix the boxes issue */
            color: inherit !important;
        }
        
        aside a:hover {
            color: var(--text-main) !important;
            background-color: rgba(255, 255, 255, 0.05) !important;
        }

        aside a.sidebar-link-active {
            color: white !important;
            background: linear-gradient(135deg, var(--primary-600), var(--primary-500)) !important;
            box-shadow: 0 4px 15px -1px var(--primary-600);
        }

        /* Glassmorphism for mode toggle/theme switcher area */
        .sidebar-footer {
            border-top: 1px solid var(--border-color);
            background: rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(4px);
        }

        html:not(.dark) .sidebar-footer {
            background: rgba(255, 255, 255, 0.5);
        }

        /* Standardize action buttons for high visibility */
        .btn-compact, .ph-arrow-counter-clockwise, .ph-magnifying-glass {
            color: #ffffff !important;
        }
        
        .bg-gray-800.text-gray-300, .bg-slate-800.text-slate-300, .bg-gray-800.text-gray-400 {
            color: #ffffff !important;
        }

        /* Navbar/Header glass effect */
        header {
            background-color: var(--bg-header) !important;
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
        }
        
        /* Ensure table card uses theme background */
        .glass-card { background-color: var(--bg-card) !important; }

        /* Action Icons Alignment Fix */
        .table-excel td .flex.justify-end.items-center,
        .table-excel td .flex.justify-center.items-center {
            gap: 4px;
        }

        .table-excel td form.inline {
            display: inline-flex !important;
            vertical-align: middle;
            margin: 0 !important;
            padding: 0 !important;
        }

        .table-excel td button, 
        .table-excel td a {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            vertical-align: middle;
        }
    </style>
    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-main);
            transition: background-color 0.3s, color 0.3s;
        }

        .sidebar-link-active {
            background: linear-gradient(to right, var(--primary-500), transparent);
            background-opacity: 0.15;
            border-left: 4px solid var(--primary-500);
            color: var(--primary-400);
        }

        html:not(.dark) .sidebar-link-active {
            background: linear-gradient(to right, var(--primary-100), transparent);
            color: var(--primary-600);
        }

        .glass-card {
            background: var(--bg-card);
            backdrop-filter: blur(8px);
            border: 1px solid var(--border-color);
        }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Ultra-Compact Excel-like Table Styles */
        .table-excel {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 14px;
            background: transparent !important;
        }

        .table-excel thead th {
            background-color: var(--table-header) !important;
            color: var(--text-main) !important;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.01em;
            padding: 4px 6px;
            border-bottom: 1px solid var(--border-color);
            border-right: 1px solid var(--border-color);
            font-size: 12px;
            white-space: nowrap;
        }

        .table-excel tbody td {
            padding: 4px 6px;
            border-bottom: 1px solid var(--border-color);
            border-right: 1px solid var(--border-color);
            vertical-align: middle;
            line-height: 1.4;
            color: var(--text-main) !important;
        }

        .table-excel tbody tr:hover td {
            background-color: var(--primary-500);
            color: white !important;
        }

        .table-excel .badge-compact {
            display: inline-flex;
            align-items: center;
            padding: 1px 6px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.01em;
            line-height: 1.4;
        }

        /* Tighter Card & Button Spacing */
        .glass-card {
            background: var(--bg-card);
            backdrop-filter: blur(8px);
            border: 1px solid var(--border-color);
        }

        .btn-compact {
            padding: 3px 6px !important;
            font-size: 12px !important;
            border-radius: 6px !important;
            color: white !important;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s;
        }

        .btn-compact:hover {
            transform: translateY(-1px);
            filter: brightness(1.1);
        }

        /* Input fields readability */
        input::placeholder, select::placeholder {
            color: var(--text-muted) !important;
            opacity: 0.6;
        }

        input {
            color: var(--text-main) !important;
        }
    </style>
    </style>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .swal2-popup {
            background: var(--bg-card) !important;
            backdrop-filter: blur(16px) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 24px !important;
            color: var(--text-main) !important;
        }

        .swal2-title {
            color: var(--text-main) !important;
            font-family: 'Outfit', sans-serif !important;
        }

        .swal2-html-container {
            color: var(--text-muted) !important;
        }

        .swal2-confirm {
            background: var(--primary-600) !important;
            border-radius: 12px !important;
            font-weight: 600 !important;
            padding: 10px 24px !important;
        }

        .swal2-cancel {
            background: var(--bg-sidebar) !important;
            border-radius: 12px !important;
            font-weight: 600 !important;
            border: 1px solid var(--border-color) !important;
            padding: 10px 24px !important;
            color: var(--text-muted) !important;
        }
    </style>
    <script>
        window.confirmDelete = function (formId, name) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data " + name + " akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#1e293b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            })
        }
    </script>

    <!-- Hotwire Turbo -->
    <script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.0/dist/turbo.es2017-umd.js"></script>
</head>

<body class="font-sans antialiased overflow-hidden" 
    :class="{ 'text-gray-200': isDark, 'text-gray-900': !isDark }">
    <div class="h-screen flex bg-inherit overflow-hidden relative">

        <!-- Mobile Sidebar Overlay -->
        <div x-show="isMobile && sidebarOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 lg:hidden">
        </div>

        <!-- Sidebar -->
        <aside id="main-sidebar" data-turbo-permanent
            class="fixed inset-y-0 left-0 z-50 w-64 lg:w-52 bg-inherit border-r border-gray-800/10 transition-all duration-300 ease-in-out lg:static lg:translate-x-0 h-full flex flex-col flex-shrink-0 shadow-2xl lg:shadow-none"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:hidden'"
            style="background-color: var(--bg-sidebar);">
            <div class="flex items-center justify-between px-6 py-2 h-14">
                <div class="flex items-center">
                    <img src="{{ asset('rolog.png') }}" alt="Logo" class="h-8 mr-3">
                    <span
                        class="text-lg font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-500 via-orange-500 to-yellow-500 uppercase tracking-tighter">
                        Invenlog Polda NTB
                    </span>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-white">
                    <i class="ph ph-x text-2xl"></i>
                </button>
            </div>

            <nav class="mt-2 px-2 space-y-0.5 overflow-y-auto flex-1 custom-scrollbar">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center px-4 py-1.5 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : '' }}">
                    <i class="ph ph-squares-four text-xl mr-2"></i>
                    <span class="font-medium text-sm text-decoration-none">Dashboard</span>
                </a>

                <div class="text-[9px] font-bold text-gray-500 uppercase tracking-widest px-4 mt-3 mb-1 opacity-50">Manajemen</div>

                <a href="{{ route('senjata.index') }}"
                    class="flex items-center px-4 py-1.5 rounded-xl transition-all {{ request()->routeIs('senjata.index') ? 'sidebar-link-active' : '' }}">
                    <i class="ph ph-shield text-xl mr-2"></i>
                    <span class="font-medium text-sm">Senjata (Gudang)</span>
                </a>
                <a href="{{ route('senjata.pembawa') }}"
                    class="flex items-center px-4 py-1.5 rounded-xl transition-all {{ request()->routeIs('senjata.pembawa') ? 'sidebar-link-active' : '' }}">
                    <i class="ph ph-user-focus text-xl mr-2"></i>
                    <span class="font-medium text-sm">Pembawa Senjata</span>
                </a>
                <a href="{{ route('kendaraan.index') }}"
                    class="flex items-center px-4 py-1.5 rounded-xl transition-all {{ request()->routeIs('kendaraan.*') && !request()->routeIs('kendaraan.laporan-ringkas') ? 'sidebar-link-active' : '' }}">
                    <i class="ph ph-car text-xl mr-2"></i>
                    <span class="font-medium text-sm">Kendaraan</span>
                </a>
                <a href="{{ route('alsus.index') }}"
                    class="flex items-center px-4 py-1.5 rounded-xl transition-all {{ request()->routeIs('alsus.*') ? 'sidebar-link-active' : '' }}">
                    <i class="ph ph-gear text-xl mr-2"></i>
                    <span class="font-medium text-sm">Alsus</span>
                </a>
                <a href="{{ route('alsintor.index') }}"
                    class="flex items-center px-4 py-1.5 rounded-xl transition-all {{ request()->routeIs('alsintor.*') ? 'sidebar-link-active' : '' }}">
                    <i class="ph ph-farm text-xl mr-2"></i>
                    <span class="font-medium text-sm">Alsintor</span>
                </a>
                <a href="{{ route('amunisi.index') }}"
                    class="flex items-center px-4 py-1.5 rounded-xl transition-all {{ request()->routeIs('amunisi.index') ? 'sidebar-link-active' : '' }}">
                    <i class="ph ph-bounding-box text-xl mr-2"></i>
                    <span class="font-medium text-sm">Amunisi</span>
                </a>
                <a href="{{ route('amunisi-history.index') }}"
                    class="flex items-center px-4 py-1.5 rounded-xl transition-all {{ request()->routeIs('amunisi-history.*') ? 'sidebar-link-active' : '' }}">
                    <i class="ph ph-clock-counter-clockwise text-xl mr-2"></i>
                    <span class="font-medium text-sm">Riwayat Amunisi</span>
                </a>

                <!-- Laporan Ringkas Dropdown -->
                <div
                    x-data="{ open: {{ request()->routeIs('senjata.laporan-ringkas') || request()->routeIs('kendaraan.laporan-ringkas') || request()->routeIs('alsus-alsintor.laporan-ringkas') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-1.5 rounded-xl transition-all {{ request()->routeIs('senjata.laporan-ringkas') || request()->routeIs('kendaraan.laporan-ringkas') || request()->routeIs('alsus-alsintor.laporan-ringkas') ? 'sidebar-link-active' : '' }}">
                        <div class="flex items-center whitespace-nowrap">
                            <i class="ph ph-chart-bar text-xl mr-2"></i>
                            <span class="font-medium text-sm">Laporan Ringkas</span>
                        </div>
                        <i class="ph ph-caret-down text-xs transition-transform duration-200"
                            :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-collapse>
                        <div class="ml-7 mt-1 space-y-0.5 border-l border-gray-700/50 pl-3">
                            <a href="{{ route('senjata.laporan-ringkas') }}"
                                class="flex items-center px-3 py-1.5 rounded-lg transition-all text-xs {{ request()->routeIs('senjata.laporan-ringkas') ? 'bg-primary-600/20 text-primary-400 font-bold' : 'text-gray-500 hover:text-gray-300 hover:bg-gray-800/50' }}">
                                <i class="ph ph-shield mr-2 text-sm"></i> Senjata
                            </a>
                            <a href="{{ route('kendaraan.laporan-ringkas') }}"
                                class="flex items-center px-3 py-1.5 rounded-lg transition-all text-xs {{ request()->routeIs('kendaraan.laporan-ringkas') ? 'bg-primary-600/20 text-primary-400 font-bold' : 'text-gray-500 hover:text-gray-300 hover:bg-gray-800/50' }}">
                                <i class="ph ph-car mr-2 text-sm"></i> Kendaraan
                            </a>
                            <a href="{{ route('alsus-alsintor.laporan-ringkas') }}"
                                class="flex items-center px-3 py-1.5 rounded-lg transition-all text-xs {{ request()->routeIs('alsus-alsintor.laporan-ringkas') ? 'bg-primary-600/20 text-primary-400 font-bold' : 'text-gray-500 hover:text-gray-300 hover:bg-gray-800/50' }}">
                                <i class="ph ph-gear mr-2 text-sm"></i> Alsus & Alsintor
                            </a>
                        </div>
                    </div>
                </div>

                <div class="text-[9px] font-bold text-gray-500 uppercase tracking-widest px-4 mt-3 mb-1 opacity-50">Pengajuan Berkas</div>

                <!-- Pengajuan Berkas Dropdown -->
                <div
                    x-data="{ open: {{ request()->routeIs('pengajuan-berkas.*') || request()->routeIs('persyaratan-berkas.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-1 rounded-xl transition-all hover:bg-gray-800 {{ request()->routeIs('pengajuan-berkas.*') || request()->routeIs('persyaratan-berkas.*') ? 'sidebar-link-active' : 'text-gray-400' }}">
                        <div class="flex items-center whitespace-nowrap">
                            <i class="ph ph-folder-open text-xl mr-2"></i>
                            <span class="font-medium text-sm">Pengajuan</span>
                        </div>
                        <i class="ph ph-caret-down text-xs transition-transform duration-200"
                            :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-collapse>
                        <div class="ml-7 mt-1 space-y-0.5 border-l border-gray-700/50 pl-3">
                            <a href="{{ route('pengajuan-berkas.index', ['kategori' => 'penghapusan']) }}"
                                class="flex items-center px-3 py-1.5 rounded-lg transition-all text-xs {{ request()->routeIs('pengajuan-berkas.*') && request('kategori') === 'penghapusan' ? 'bg-red-600/20 text-red-400 font-bold' : 'text-gray-500 hover:text-gray-300 hover:bg-gray-800/50' }}">
                                <i class="ph ph-trash mr-2 text-sm"></i> Penghapusan Barang
                            </a>
                            <a href="{{ route('pengajuan-berkas.index', ['kategori' => 'penetapan_status']) }}"
                                class="flex items-center px-3 py-1.5 rounded-lg transition-all text-xs {{ request()->routeIs('pengajuan-berkas.*') && request('kategori') === 'penetapan_status' ? 'bg-blue-600/20 text-blue-400 font-bold' : 'text-gray-500 hover:text-gray-300 hover:bg-gray-800/50' }}">
                                <i class="ph ph-stamp mr-2 text-sm"></i> Penetapan Status
                            </a>
                            @if(in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2']))
                                <a href="{{ route('persyaratan-berkas.index') }}"
                                    class="flex items-center px-3 py-1.5 rounded-lg transition-all text-xs {{ request()->routeIs('persyaratan-berkas.*') ? 'bg-primary-600/20 text-primary-400 font-bold' : 'text-gray-500 hover:text-gray-300 hover:bg-gray-800/50' }}">
                                    <i class="ph ph-list-checks mr-2 text-sm"></i> Persyaratan
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                @if(in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2']))
                    <div class="px-4 mt-3 mb-1">
                        <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest opacity-50">Master Data</p>
                    </div>
                    <div class="px-3 space-y-0.5 mb-1">
                        @if(auth()->user()->role === 'Super Admin')
                            <a href="{{ route('activity-logs.index') }}"
                                class="flex items-center px-4 py-1.5 rounded-xl transition-all {{ request()->routeIs('activity-logs.*') ? 'sidebar-link-active' : '' }}">
                                <i class="ph ph-clock-counter-clockwise mr-2 text-xl"></i>
                                <span class="text-sm font-semibold">Log Aktivitas</span>
                            </a>
                            <a href="{{ route('users.index') }}"
                                class="flex items-center px-4 py-1.5 rounded-xl transition-all {{ request()->routeIs('users.*') ? 'sidebar-link-active' : '' }}">
                                <i class="ph ph-users mr-2 text-xl"></i>
                                <span class="text-sm font-semibold">User</span>
                            </a>
                        @endif
                        <a href="{{ route('satkers.index') }}"
                            class="flex items-center px-4 py-1.5 rounded-xl transition-all {{ request()->routeIs('satkers.*') ? 'sidebar-link-active' : '' }}">
                            <i class="ph ph-buildings mr-2 text-xl"></i>
                            <span class="text-sm font-semibold">Satker</span>
                        </a>
                    </div>
                @endif

            </nav>

            <!-- Theme Switcher (Fixed Bottom) -->
            <div class="px-4 py-3 sidebar-footer mt-auto">
                <div class="flex items-center justify-between mb-2 text-[9px] font-bold text-gray-500 uppercase tracking-widest">
                    <span>Tema & Warna</span>
                    <button @click="toggleDark()" class="p-1 hover:bg-gray-200 dark:hover:bg-gray-800 rounded-md transition-colors" :title="isDark ? 'Pindah ke Terang' : 'Pindah ke Gelap'">
                        <i class="ph text-base" :class="isDark ? 'ph-sun text-amber-500' : 'ph-moon text-indigo-500'"></i>
                    </button>
                </div>
                
                <div class="flex flex-wrap gap-1.5">
                    <template x-for="(themeColor, name) in themes" :key="name">
                        <button @click="setTheme(name)" 
                            class="w-5 h-5 rounded-full border border-white/20 transition-all flex items-center justify-center group overflow-hidden relative"
                            :class="currentTheme === name ? 'ring-2 ring-primary-500 ring-offset-2 ring-offset-[#0f172a]' : 'opacity-60 hover:opacity-100'"
                            :title="name">
                            <div class="absolute inset-0" :style="`background-color: ${themeColor['500']}`"></div>
                            <i x-show="currentTheme === name" class="ph ph-check text-white text-[8px] z-10"></i>
                        </button>
                    </template>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 h-full overflow-y-auto custom-scrollbar">
            <!-- Top Header -->
            <header
                class="h-16 lg:h-20 backdrop-blur-md border-b border-gray-800/10 flex items-center justify-between px-4 lg:px-8 sticky top-0 z-40 transition-colors"
                style="background-color: var(--bg-header);">
                <div class="flex items-center">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="mr-4 lg:mr-6 text-gray-400 hover:text-white transition-colors p-2 rounded-lg hover:bg-gray-800">
                        <i class="ph ph-list text-2xl"></i>
                    </button>
                    <h2 class="text-base lg:text-lg font-semibold text-gray-100 truncate max-w-[150px] sm:max-w-none">
                        {{ $header ?? 'Beranda' }}
                    </h2>
                </div>

                <div class="flex items-center space-x-3">
                    <!-- Notification Bell -->
                    <div class="relative" x-data="{
                        notifOpen: false,
                        notifications: [],
                        unreadCount: 0,
                        fetchNotifications() {
                            fetch('{{ route('notifications.index') }}')
                                .then(res => res.json())
                                .then(data => {
                                    this.notifications = data.notifications;
                                    this.unreadCount = data.unread_count;
                                });
                        },
                        markAsRead(id) {
                            fetch(`/notifications/${id}/read`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                }
                            }).then(() => this.fetchNotifications());
                        },
                        markAllAsRead() {
                            fetch('{{ route('notifications.read-all') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                }
                            }).then(() => {
                                this.fetchNotifications();
                            });
                        }
                    }" x-init="fetchNotifications(); setInterval(() => fetchNotifications(), 30000)">
                        <button @click="notifOpen = !notifOpen" @click.away="notifOpen = false"
                            class="relative p-2 text-gray-400 hover:text-white transition-colors focus:outline-none rounded-full bg-gray-800/50 border border-gray-700 hover:bg-gray-700/50">
                            <i class="ph ph-bell text-xl"></i>
                            <template x-if="unreadCount > 0">
                                <span class="absolute top-0 right-0 flex h-3 w-3">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span
                                        class="relative inline-flex rounded-full h-3 w-3 bg-red-500 border border-[#0f172a]"></span>
                                </span>
                            </template>
                        </button>

                        <!-- Dropdown Notifikasi -->
                        <div x-show="notifOpen" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-[-10px]"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 translate-y-[-10px]"
                            class="fixed lg:absolute left-4 right-4 lg:left-auto lg:right-0 top-16 lg:top-auto lg:mt-3 lg:w-96 bg-[#0f172a] border border-gray-800 rounded-2xl shadow-2xl z-50 overflow-hidden flex flex-col max-h-[80vh] lg:max-h-none"
                            x-cloak>

                            <div class="p-3 border-b border-gray-800 flex justify-between items-center bg-gray-900/50">
                                <h3 class="text-sm font-bold text-white">Notifikasi</h3>
                                <button @click="markAllAsRead()"
                                    class="text-[10px] text-primary-400 hover:text-primary-300 font-medium">Tandai semua
                                    dibaca</button>
                            </div>

                            <div class="max-h-80 overflow-y-auto custom-scrollbar">
                                <template x-if="notifications.length === 0">
                                    <div
                                        class="p-6 text-center text-gray-500 flex flex-col items-center justify-center">
                                        <i class="ph ph-bell-slash text-2xl mb-2 opacity-50"></i>
                                        <span class="text-xs">Tidak ada notifikasi</span>
                                    </div>
                                </template>

                                <template x-for="notif in notifications" :key="notif.id">
                                    <div class="p-3 border-b border-gray-800/50 hover:bg-white/5 transition-colors cursor-pointer flex gap-3 relative"
                                        :class="!notif.read_at ? 'bg-primary-900/10' : ''"
                                        @click="if(!notif.read_at) markAsRead(notif.id)">

                                        <div class="flex-shrink-0 mt-1">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                                :class="!notif.read_at ? 'bg-primary-500/20 text-primary-400' : 'bg-gray-800 text-gray-400'">
                                                <i class="ph ph-bell-ringing text-sm"></i>
                                            </div>
                                        </div>

                                        <div class="flex-1 min-w-0 pr-4">
                                            <div class="flex justify-between items-start mb-0.5">
                                                <p class="text-sm font-semibold text-gray-200 truncate pr-2"
                                                    x-text="notif.data.title"></p>
                                                <span class="text-[9px] text-gray-500 whitespace-nowrap"
                                                    x-text="new Date(notif.created_at).toLocaleDateString('id-ID', {day:'numeric', month:'short', hour:'2-digit', minute:'2-digit'})"></span>
                                            </div>
                                            <p class="text-xs text-gray-400 line-clamp-2" x-text="notif.data.message">
                                            </p>

                                            <template x-if="notif.data.pengajuan_id">
                                                <a :href="`/pengajuan-berkas/${notif.data.pengajuan_id}`"
                                                    class="inline-block mt-2 text-[10px] text-primary-400 hover:text-primary-300 font-medium bg-primary-500/10 px-2 py-1 rounded-md transition-colors hover:bg-primary-500/20 z-10 relative">Lihat
                                                    Pengajuan</a>
                                            </template>
                                        </div>

                                        <template x-if="!notif.read_at">
                                            <div
                                                class="absolute right-3 top-1/2 -translate-y-1/2 w-2 h-2 rounded-full bg-primary-500">
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ profileOpen: false }">
                        <button @click="profileOpen = !profileOpen" @click.away="profileOpen = false"
                            class="flex items-center px-4 py-2 bg-gray-800/50 rounded-full border border-gray-700 hover:bg-gray-700/50 transition-colors focus:outline-none">
                            <div
                                class="w-8 h-8 rounded-full bg-primary-500 flex items-center justify-center text-white mr-3 font-bold text-xs">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <span class="text-sm font-medium text-gray-200 mr-2">{{ Auth::user()->name }}</span>
                            <i class="ph ph-caret-down text-gray-400 text-xs transition-transform duration-200"
                                :class="profileOpen ? 'rotate-180' : ''"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="profileOpen" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-[-10px]"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 translate-y-[-10px]"
                            class="absolute right-0 mt-3 w-48 bg-[#0f172a] border border-gray-800 rounded-2xl shadow-2xl py-2 z-50"
                            x-cloak>

                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center px-4 py-3 text-sm text-gray-300 hover:bg-gray-800 transition-colors">
                                <i class="ph ph-user-circle text-lg mr-3"></i>
                                Profil Saya
                            </a>

                            <a href="{{ route('profile.edit') }}#update-password"
                                class="flex items-center px-4 py-3 text-sm text-gray-300 hover:bg-gray-800 transition-colors">
                                <i class="ph ph-lock text-lg mr-3"></i>
                                Ganti Password
                            </a>

                            <div class="border-t border-gray-800 my-1"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center px-4 py-3 text-sm text-red-400 hover:bg-red-500/10 transition-colors">
                                    <i class="ph ph-sign-out text-lg mr-3"></i>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 overflow-y-auto p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Floating Chat Widget -->
    <div x-data="chatWidget()" class="fixed bottom-6 right-6 z-[60]">

        <!-- Chat Trigger Button -->
        <button @click="toggleChat"
            class="w-14 h-14 bg-gradient-to-tr from-red-600 to-yellow-500 rounded-2xl shadow-2xl flex items-center justify-center text-white hover:scale-110 active:scale-95 transition-all relative group">
            <i class="ph ph-chat-centered-dots text-2xl group-hover:rotate-12 transition-transform"></i>
            <template x-if="unreadCount > 0">
                <span
                    class="absolute -top-2 -right-2 bg-blue-500 text-white text-[10px] font-bold px-2 py-1 rounded-full border-2 border-[#0f172a]"
                    x-text="unreadCount"></span>
            </template>
        </button>

        <!-- Chat Window -->
        <div x-show="chatOpen" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-10 scale-90"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-10 scale-90"
            class="absolute bottom-20 right-0 w-96 max-w-[calc(100vw-2rem)] h-[500px] bg-[#0f172a]/95 backdrop-blur-xl border border-white/10 rounded-3xl shadow-2xl flex flex-col overflow-hidden">

            <!-- Chat Header -->
            <div
                class="p-4 bg-gradient-to-r from-red-600/20 to-yellow-500/20 border-b border-white/10 flex items-center justify-between">
                <div class="flex items-center">
                    <template x-if="activeUser">
                        <button @click="activeUser = null" class="mr-3 text-gray-400 hover:text-white">
                            <i class="ph ph-arrow-left text-xl"></i>
                        </button>
                    </template>
                    <div>
                        <h4 class="font-bold text-white text-sm"
                            x-text="activeUser ? activeUser.name : 'Konsultasi Logistik'"></h4>
                        <p class="text-[10px] text-gray-400"
                            x-text="activeUser ? (activeUser.last_seen_at ? 'Aktif' : 'Offline') : 'Pilih personel untuk memulai'">
                        </p>
                    </div>
                </div>
                <button @click="chatOpen = false" class="text-gray-400 hover:text-white">
                    <i class="ph ph-x text-xl"></i>
                </button>
            </div>

            <!-- Chat Body -->
            <div class="flex-1 overflow-y-auto p-4 custom-scrollbar" id="chat-messages">
                <!-- User List -->
                <template x-if="!activeUser">
                    <div class="space-y-2">
                        <template x-for="user in users" :key="user.id">
                            <button @click="selectUser(user)"
                                class="w-full flex items-center p-3 rounded-2xl hover:bg-white/5 transition-colors border border-transparent hover:border-white/10 text-left cursor-pointer">
                                <div
                                    class="w-10 h-10 rounded-full bg-primary-500 flex items-center justify-center text-white font-bold mr-3 relative">
                                    <span x-text="user.name.substring(0,1)"></span>
                                    <div :class="user.last_seen_at ? 'bg-green-500' : 'bg-gray-500'"
                                        class="absolute bottom-0 right-0 w-3 h-3 border-2 border-[#0f172a] rounded-full">
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <div class="text-sm font-semibold text-white" x-text="user.name"></div>
                                        <template x-if="user.unread_count > 0">
                                            <span
                                                class="bg-blue-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full"
                                                x-text="user.unread_count"></span>
                                        </template>
                                    </div>
                                    <div class="text-[10px] text-gray-400" x-text="user.role"></div>
                                </div>
                            </button>
                        </template>
                    </div>
                </template>

                <!-- Message History -->
                <template x-if="activeUser">
                    <div class="space-y-3">
                        <template x-for="msg in messages" :key="msg.id">
                            <div :class="msg.sender_id == {{ Auth::id() }} ? 'flex justify-end' : 'flex justify-start'">
                                <div :class="msg.sender_id == {{ Auth::id() }} ? 'bg-red-600 text-white rounded-t-2xl rounded-l-2xl' : 'bg-gray-800 text-gray-100 rounded-t-2xl rounded-r-2xl'"
                                    class="max-w-[80%] px-4 py-2.5 shadow-sm">
                                    <p class="text-sm" x-text="msg.message"></p>
                                    <div class="flex items-center justify-end mt-1 space-x-1">
                                        <span class="text-[9px] opacity-60"
                                            x-text="new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></span>
                                        <template x-if="msg.sender_id == {{ Auth::id() }}">
                                            <i class="ph ph-checks text-[10px]"
                                                :class="msg.is_read ? 'text-blue-300' : 'text-gray-300'"></i>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <!-- Chat Footer -->
            <template x-if="activeUser">
                <div class="p-4 border-t border-white/10 bg-white/5">
                    <form @submit.prevent="sendMessage" class="flex items-center space-x-2">
                        <input type="text" x-model="newMessage" placeholder="Ketik pesan..."
                            class="flex-1 bg-gray-900/50 border border-white/10 rounded-xl px-4 py-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-yellow-500/50">
                        <button type="submit"
                            class="w-10 h-10 bg-yellow-500 rounded-xl flex items-center justify-center text-[#0f172a] hover:bg-yellow-400 transition-colors cursor-pointer">
                            <i class="ph ph-paper-plane-right text-xl"></i>
                        </button>
                    </form>
                </div>
            </template>
        </div>
    </div>

    <!-- Session Alerts -->
    <script>
        function themeManager() {
            return {
                sidebarOpen: window.innerWidth >= 1024,
                isMobile: window.innerWidth < 1024,
                isDark: true,
                currentTheme: 'Azure',
                themes: {
                    'Azure': {'50': '#f0f9ff', '100': '#e0f2fe', '200': '#bae6fd', '300': '#7dd3fc', '400': '#38bdf8', '500': '#0ea5e9', '600': '#0284c7', '700': '#0369a1', '800': '#075985', '900': '#0c4a6e'},
                    'Emerald': {'50': '#ecfdf5', '100': '#d1fae5', '200': '#a7f3d0', '300': '#6ee7b7', '400': '#34d399', '500': '#10b981', '600': '#059669', '700': '#047857', '800': '#065f46', '900': '#064e3b'},
                    'Indigo': {'50': '#eef2ff', '100': '#e0e7ff', '200': '#c7d2fe', '300': '#a5b4fc', '400': '#818cf8', '500': '#6366f1', '600': '#4f46e5', '700': '#4338ca', '800': '#3730a3', '900': '#312e81'},
                    'Rose': {'50': '#fff1f2', '100': '#ffe4e6', '200': '#fecdd3', '300': '#fda4af', '400': '#fb7185', '500': '#f43f5e', '600': '#e11d48', '700': '#be123c', '800': '#9f1239', '900': '#881337'},
                    'Amber': {'50': '#fffbeb', '100': '#fef3c7', '200': '#fde68a', '300': '#fcd34d', '400': '#fbbf24', '500': '#f59e0b', '600': '#d97706', '700': '#b45309', '800': '#92400e', '900': '#78350f'},
                    'Slate': {'50': '#f8fafc', '100': '#f1f5f9', '200': '#e2e8f0', '300': '#cbd5e1', '400': '#94a3b8', '500': '#64748b', '600': '#475569', '700': '#334155', '800': '#1e293b', '900': '#0f172a'}
                },

                init() {
                    window.addEventListener('resize', () => {
                        this.isMobile = window.innerWidth < 1024;
                        if (!this.isMobile) this.sidebarOpen = true;
                    });

                    this.isDark = localStorage.getItem('isDark') === 'false' ? false : true;
                    this.currentTheme = localStorage.getItem('theme') || 'Azure';
                    
                    // Initial theme apply
                    this.applyTheme();
                    
                    // Simple watcher for dark mode to sync with tailwind class
                    this.$watch('isDark', val => {
                        localStorage.setItem('isDark', val);
                        if (val) {
                            document.documentElement.classList.add('dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                        }
                    });
                },

                toggleDark() {
                    this.isDark = !this.isDark;
                },

                setTheme(name) {
                    this.currentTheme = name;
                    localStorage.setItem('theme', name);
                    this.applyTheme();
                },

                applyTheme() {
                    const colors = this.themes[this.currentTheme];
                    const root = document.documentElement;
                    Object.keys(colors).forEach(key => {
                        root.style.setProperty(`--primary-${key}`, colors[key]);
                    });
                }
            };
        }

        function chatWidget() {
            return {
                chatOpen: false, 
                activeUser: null, 
                users: [], 
                messages: [], 
                newMessage: '', 
                unreadCount: 0,
                pollingInterval: null,
                msgInterval: null,
                init() {
                    this.updateStatus();
                    this.pollingInterval = setInterval(() => this.updateStatus(), 10000);
                },
                toggleChat() {
                    this.chatOpen = !this.chatOpen;
                    if (this.chatOpen && !this.activeUser) {
                        this.fetchUsers();
                    }
                },
                fetchUsers() {
                    fetch('{{ route('chat.users') }}')
                        .then(res => res.json())
                        .then(data => { this.users = data; });
                },
                selectUser(user) {
                    this.activeUser = user;
                    this.fetchMessages();
                    if (this.msgInterval) clearInterval(this.msgInterval);
                    this.msgInterval = setInterval(() => this.fetchMessages(), 3000);
                },
                fetchMessages() {
                    if (!this.activeUser) return;
                    fetch(`/chat/messages/${this.activeUser.id}`)
                        .then(res => res.json())
                        .then(data => { 
                            const hadNew = data.length > this.messages.length && data[data.length-1].sender_id != {{ Auth::id() }};
                            this.messages = data; 
                            if (hadNew && !this.chatOpen) {
                                this.showNotification(data[data.length-1]);
                            }
                            this.$nextTick(() => {
                                const el = document.getElementById('chat-messages');
                                if (el) el.scrollTop = el.scrollHeight;
                            });
                        });
                },
                sendMessage() {
                    if (!this.newMessage.trim()) return;
                    const body = { receiver_id: this.activeUser.id, message: this.newMessage };
                    fetch('{{ route('chat.send') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(body)
                    }).then(() => {
                        this.newMessage = '';
                        this.fetchMessages();
                    });
                },
                updateStatus() {
                    fetch('{{ route('chat.status') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    })
                    .then(res => res.json())
                    .then(data => { 
                        if (data.unread_count > this.unreadCount) {
                            if (this.chatOpen && !this.activeUser) this.fetchUsers();
                        }
                        this.unreadCount = data.unread_count; 
                    });
                },
                showNotification(msg) {
                    if (!('Notification' in window)) return;
                    Notification.requestPermission().then(permission => {
                        if (permission === 'granted') {
                            new Notification('Pesan Baru', { body: msg.message });
                        }
                    });
                }
            };
        }

        // Safe Download: handles session-expired downloads gracefully
        window.safeDownload = function (url, fallbackFilename) {
            // Show loading indicator
            Swal.fire({
                title: 'Mengunduh...',
                text: 'Sedang memproses file, mohon tunggu.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(url, {
                credentials: 'same-origin'
            })
                .then(response => {
                    // Check if response is a redirect to login (session expired)
                    if (response.redirected && response.url.includes('/login')) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Sesi Telah Berakhir',
                            text: 'Silakan login kembali untuk mengunduh file.',
                            confirmButtonText: 'Login',
                            confirmButtonColor: '#0ea5e9'
                        }).then(() => {
                            window.location.href = '/login';
                        });
                        return null;
                    }

                    // Check content type to ensure it's not HTML
                    const contentType = response.headers.get('Content-Type') || '';
                    if (contentType.includes('text/html')) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Sesi Telah Berakhir',
                            text: 'Silakan login kembali untuk mengunduh file.',
                            confirmButtonText: 'Login',
                            confirmButtonColor: '#0ea5e9'
                        }).then(() => {
                            window.location.href = '/login';
                        });
                        return null;
                    }

                    if (!response.ok) {
                        throw new Error('Download gagal: ' + response.status);
                    }

                    // Extract filename from Content-Disposition header
                    const disposition = response.headers.get('Content-Disposition');
                    let filename = fallbackFilename || 'download';
                    if (disposition && disposition.includes('filename=')) {
                        const match = disposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                        if (match && match[1]) {
                            filename = match[1].replace(/['"]/g, '');
                        }
                    }

                    return response.blob().then(blob => ({ blob, filename }));
                })
                .then(result => {
                    if (!result) return;

                    const { blob, filename } = result;
                    const downloadUrl = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = downloadUrl;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(downloadUrl);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'File berhasil diunduh.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                })
                .catch(error => {
                    console.error('Download error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mengunduh',
                        text: 'Terjadi kesalahan saat mengunduh file. Silakan coba lagi.'
                    });
                });
        };

        document.addEventListener('turbo:load', function () {
            // Update Sidebar active links automatically
            const currentPath = window.location.pathname;
            const sidebarLinks = Array.from(document.querySelectorAll('#main-sidebar a, #main-sidebar button')).filter(el => el.href);
            
            // Find the longest matching path to avoid overlaps (e.g., /senjata vs /senjata/laporan)
            let longestMatchPath = "";
            
            sidebarLinks.forEach(link => {
                try {
                    const linkPath = new URL(link.href).pathname;
                    if (currentPath === linkPath || (linkPath !== '/' && currentPath.startsWith(linkPath + '/'))) {
                        if (linkPath.length > longestMatchPath.length) {
                            longestMatchPath = linkPath;
                        }
                    }
                } catch(e) {}
            });

            sidebarLinks.forEach(link => {
                try {
                    const linkPath = new URL(link.href).pathname;
                    const isActive = (currentPath === linkPath) || (longestMatchPath === linkPath && linkPath !== "/");
                    
                    if (isActive) {
                        link.classList.add('sidebar-link-active');
                    } else {
                        link.classList.remove('sidebar-link-active');
                    }
                } catch(e) {}
            });

            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Akses Ditolak',
                    text: "{{ session('error') }}",
                });
            @endif
        });
    </script>
    @stack('scripts')
</body>

</html>