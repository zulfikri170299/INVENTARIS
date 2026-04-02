<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<script>
    // Immediate theme locking for Hybrid Light (Dark Sidebar, Light Content)
    (function() {
        document.documentElement.classList.remove('dark');
        document.documentElement.style.backgroundColor = '#f8fafc'; // SLATE 50 / WHITE
        
        // Deep Maroon Palette (#991b1b / #450a0a)
        const colors = {
            '50': '#fef2f2', '100': '#fee2e2', '200': '#fecaca', '300': '#fca5a5', '400': '#f87171', 
            '500': '#b91c1c', '600': '#991b1b', '700': '#7f1d1d', '800': '#450a0a', '900': '#2d0606'
        };
        Object.keys(colors).forEach(key => {
            document.documentElement.style.setProperty(`--primary-${key}`, colors[key]);
        });
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
            /* SPBP Amber Gold Palette */
            --primary-50: #fef2f2;
            --primary-100: #fee2e2;
            --primary-200: #fecaca;
            --primary-300: #fca5a5;
            --primary-400: #f87171;
            --primary-500: #b91c1c;
            --primary-600: #991b1b;
            --primary-700: #7f1d1d;
            --primary-800: #450a0a;
            --primary-900: #2d0606;

            /* Light Theme Content Defaults */
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(0, 0, 0, 0.08);
            --glass-shadow: 0 8px 30px 0 rgba(0, 0, 0, 0.04);
            
            --text-main: #0f172a; /* SLATE 900 */
            --text-muted: #475569; /* SLATE 600 */
            --table-header: #f1f5f9;
        }

        /* Global Text Enforcement for Light Mode */
        html:not(.dark) main {
            color: var(--text-main) !important;
        }

        html:not(.dark) .text-white:not(aside *, .bg-gradient-to-br *, .bg-gradient-to-r *, .btn-*, .swal2-*) {
            color: var(--text-main) !important;
        }

        html:not(.dark) .text-gray-400:not(aside *, .bg-gradient-to-br *, .bg-gradient-to-r *, .glass-card *, .ultra-glass *) {
            color: var(--text-muted) !important;
        }

        /* Sidebar Dark Enforcement (Slate 950) */
        #main-sidebar {
            background-color: #020617 !important;
            border-right: 1px solid rgba(255, 255, 255, 0.05) !important;
        }

        #main-sidebar .text-gray-500 { color: #94a3b8 !important; }
        #main-sidebar .text-primary-500 { color: var(--primary-500) !important; }
        #main-sidebar .text-white { color: #f8fafc !important; }
        #main-sidebar .sidebar-link-active {
            background: rgba(217, 119, 6, 0.15) !important;
            color: var(--primary-400) !important;
        }

        .ultra-glass, .glass-card, .swal2-popup {
            background: #ffffff;
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px -5px rgba(0,0,0,0.05);
        }

        /* --- HYBRID LIGHT THEME INVERSION (FIX INVISIBLE TEXT) --- */
        html:not(.dark) main :where(.bg-gray-800, .bg-gray-900, .bg-slate-800, .bg-slate-900, .bg-[#0f172a]) {
            background-color: #ffffff !important;
        }
        
        html:not(.dark) main :where(.text-white, .text-gray-100, .text-slate-100, .text-gray-200) {
            color: #0f172a !important;
        }

        html:not(.dark) main :where(.text-gray-400, .text-gray-500, .text-slate-400, .text-slate-500) {
            color: #475569 !important;
        }

        html:not(.dark) main :where(.border-gray-700, .border-gray-800, .border-slate-700, .border-slate-800, .divide-gray-800) {
            border-color: #e2e8f0 !important;
        }

        /* Input / Form Readability */
        html:not(.dark) main :where(input:not([type="checkbox"]), select, textarea) {
            background-color: #ffffff !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }

        html:not(.dark) main :where(input::placeholder, select::placeholder, textarea::placeholder) {
            color: #94a3b8 !important;
        }

        /* --- CHECKBOX VISIBILITY FIX --- */
        input[type="checkbox"] {
            appearance: none !important;
            -webkit-appearance: none !important;
            width: 1.15rem;
            height: 1.15rem;
            border: 2px solid #cbd5e1 !important;
            border-radius: 4px !important;
            background-color: #ffffff !important;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-grid;
            place-content: center;
            vertical-align: middle;
            position: relative;
        }

        input[type="checkbox"]:checked {
            background-color: #991b1b !important;
            border-color: #991b1b !important;
        }

        input[type="checkbox"]:checked::after {
            content: "";
            width: 0.65rem;
            height: 0.65rem;
            background-color: white;
            clip-path: polygon(14% 44%, 0 58%, 50% 100%, 100% 16%, 80% 0%, 43% 62%);
            transform: scale(1);
        }

        html:not(.dark) input[type="checkbox"]:checked {
            box-shadow: 0 0 10px rgba(153, 27, 27, 0.2);
        }

        /* Action Buttons Contrast handled by specific icon rules below */

        /* EXCEPTION for Vibrant Dashboard Cards & Primary Buttons */
        html:not(.dark) main :where(.bg-gradient-to-br, .bg-gradient-to-r, .bg-primary-600, .bg-primary-700) *,
        html:not(.dark) main :where(.bg-gradient-to-br, .bg-gradient-to-r, .bg-primary-600, .bg-primary-700) {
            color: #ffffff !important;
            border-color: rgba(255,255,255,0.2) !important;
        }
        
        /* Specific Fix for Icons in Primary Buttons */
        html:not(.dark) main :where(.bg-primary-600, .bg-primary-700) i {
            color: #ffffff !important;
        }
        
        /* Table Cell Specific Fix */
        .table-excel tbody tr { background-color: white !important; }
        .table-excel tbody td { color: #0f172a !important; }
        .table-excel thead th { border: 1px solid #e2e8f0 !important; }

        /* Sidebar Item Styling (Dark Sidebar, Light Text) */
        aside a, aside button {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            margin: 0px 16px;
            padding: 5px 16px !important;
            border-radius: 8px;
            color: #94a3b8 !important; /* Force light gray for dark sidebar */
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        aside a:hover, aside button:hover {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.05) !important;
            transform: translateX(4px);
        }

        aside a.sidebar-link-active {
            background: rgba(217, 119, 6, 0.1) !important;
            color: var(--primary-400) !important;
            border-left: 3px solid var(--primary-500) !important;
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
            box-shadow: none !important;
        }

        aside a.sidebar-link-active i {
            color: var(--primary-500) !important;
        }

        /* Table excel refinement */
        .table-excel thead th {
            background: var(--table-header) !important;
            color: var(--text-main) !important;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-size: 10px;
            padding: 8px 12px;
            border: none !important;
        }

        .table-excel tbody td {
            padding: 5px 10px;
            border-bottom: 1px solid var(--glass-border) !important;
            color: var(--text-main) !important;
            font-size: 11px;
            font-weight: 500;
        }

        .table-excel tbody tr:hover td {
            background: rgba(217, 119, 6, 0.08) !important;
            color: var(--text-main) !important;
        }

        /* --- GLOBAL ACTION BUTTON COLORS (TAMBAH, IMPORT, EXPORT) --- */
        /* Tambah Button (Red) */
        .btn-compact:has(.ph-plus-circle) {
            background-color: #ef4444 !important; /* Red 500 */
            color: #ffffff !important;
            border: none !important;
            box-shadow: 0 4px 12px -2px rgba(239, 68, 68, 0.3) !important;
        }
        .btn-compact:has(.ph-plus-circle):hover {
            background-color: #dc2626 !important; /* Red 600 */
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 6px 15px -3px rgba(239, 68, 68, 0.4) !important;
        }

        /* Import Button (Green) */
        .btn-compact:has(.ph-file-arrow-up) {
            background-color: #10b981 !important; /* Emerald 500 */
            color: #ffffff !important;
            border: none !important;
            box-shadow: 0 4px 12px -2px rgba(16, 185, 129, 0.3) !important;
        }
        .btn-compact:has(.ph-file-arrow-up):hover {
            background-color: #059669 !important; /* Emerald 600 */
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 6px 15px -3px rgba(16, 185, 129, 0.4) !important;
        }

        /* Export Button (Blue) */
        .btn-compact:has(.ph-export) {
            background-color: #3b82f6 !important; /* Blue 500 */
            color: #ffffff !important;
            border: none !important;
            box-shadow: 0 4px 12px -2px rgba(59, 130, 246, 0.3) !important;
        }
        .btn-compact:has(.ph-export):hover {
            background-color: #2563eb !important; /* Blue 600 */
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 6px 15px -3px rgba(59, 130, 246, 0.4) !important;
        }

        .btn-compact {
            border-radius: 8px !important;
            padding: 4px 10px !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 10px !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

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

        /* --- GLOBAL COMPACT PAGINATION --- */
        nav[role="navigation"] {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        nav[role="navigation"] p {
            font-size: 10px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            color: #64748b !important;
        }
        nav[role="navigation"] .relative.z-0.inline-flex {
            border-radius: 8px !important;
            overflow: hidden !important;
            border: 1px solid #e2e8f0 !important;
        }
        nav[role="navigation"] .relative.z-0.inline-flex a,
        nav[role="navigation"] .relative.z-0.inline-flex span[aria-current="page"] span,
        nav[role="navigation"] .relative.z-0.inline-flex span[disabled] span {
            padding: 0 !important;
            font-size: 9px !important;
            font-weight: 800 !important;
            min-width: 22px !important;
            height: 22px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            text-align: center !important;
            border-color: rgba(255,255,255,0.05) !important;
        }
        nav[role="navigation"] svg {
            width: 8px !important;
            height: 8px !important;
            display: block !important;
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
            background-color: rgba(217, 119, 6, 0.08) !important;
            color: var(--text-main) !important;
        }

        .table-excel .badge-compact {
            display: inline-flex;
            align-items: center;
            padding: 0px 4px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            line-height: 1.4;
        }

        /* Modal label fix */
        .glass-card label, .modal-content label, label.block.text-gray-400 {
            color: #ff8a8a !important; /* Soft bright red for labels on dark */
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            font-size: 10px !important;
            opacity: 1 !important;
            margin-bottom: 0.25rem !important;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }

        /* Extreme compact forms */
        .glass-card .space-y-5 {
            gap: 0.75rem !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .glass-card input, .glass-card select, .glass-card textarea {
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
            font-size: 12px !important;
        }

        .glass-card .grid {
            gap: 0.75rem !important;
        }

        /* Tighter Card & Button Spacing */
        .glass-card {
            background: var(--bg-card);
            backdrop-filter: blur(8px);
            border: 1px solid var(--border-color);
        }

        /* Modal button fix */
        .glass-card button[type="button"], .modal-content button[type="button"] {
            background-color: #1e293b !important;
            color: #f8fafc !important;
            font-weight: 700 !important;
        }

        .glass-card button[type="submit"], .modal-content button[type="submit"] {
            background-color: var(--primary-600) !important;
            color: #ffffff !important;
            border: 1px solid var(--primary-500) !important;
            font-weight: 800 !important;
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.2) !important;
        }

        .glass-card button:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
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
        input::placeholder, select::placeholder, textarea::placeholder {
            color: var(--text-muted) !important;
            opacity: 0.8 !important;
        }

        input, select, textarea {
            color: var(--text-main) !important;
            background-color: white !important;
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

<body class="font-sans antialiased overflow-hidden mesh-gradient-bg transition-colors duration-500">
    <div class="h-screen flex overflow-hidden relative">

        <!-- Mobile Sidebar Overlay -->
        <div x-show="isMobile && sidebarOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 lg:hidden">
        </div>

        <!-- Sidebar -->
        <aside id="main-sidebar" data-turbo-permanent
            class="fixed inset-y-0 left-0 z-50 w-64 lg:w-64 bg-[#020617] transition-all duration-500 ease-in-out lg:static lg:translate-x-0 h-full flex flex-col flex-shrink-0 shadow-2xl"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:hidden'">
            
            <div class="flex items-center justify-between px-6 py-4 h-16">
                <div class="flex items-center group cursor-pointer">
                    <div class="w-10 h-10 bg-gradient-to-tr from-primary-600 to-indigo-500 rounded-xl flex items-center justify-center shadow-lg shadow-primary-500/20 group-hover:scale-110 transition-transform">
                        <img src="{{ asset('rolog.png') }}" alt="Logo" class="h-6">
                    </div>
                    <div class="ml-3 flex flex-col">
                        <span class="text-xs font-black text-primary-500 uppercase tracking-[0.2em] leading-none mb-1">Invenlog</span>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-widest leading-none">Polda NTB</span>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-primary-500 transition-colors">
                    <i class="ph ph-x-circle text-2xl"></i>
                </button>
            </div>

            <nav class="mt-2 px-2 space-y-0.5 overflow-y-auto flex-1 custom-scrollbar">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center px-4 py-1.5 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : '' }}">
                    <i class="ph ph-squares-four text-xl mr-2"></i>
                    <span class="font-medium text-sm text-decoration-none">Dashboard</span>
                </a>

                <div class="text-[9px] font-bold text-gray-500 uppercase tracking-widest px-4 mt-1.5 mb-0.5 opacity-50">Manajemen</div>

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
                        class="w-full flex items-center justify-between px-4 py-1 rounded-xl transition-all {{ request()->routeIs('senjata.laporan-ringkas') || request()->routeIs('kendaraan.laporan-ringkas') || request()->routeIs('alsus-alsintor.laporan-ringkas') ? 'sidebar-link-active' : '' }}"
                        style="margin: 0px 16px; width: calc(100% - 32px); padding: 5px 16px !important;">
                        <div class="flex items-center whitespace-nowrap">
                            <i class="ph ph-chart-bar text-xl mr-2"></i>
                            <span class="font-medium text-sm">Laporan Ringkas</span>
                        </div>
                        <i class="ph ph-caret-down text-xs transition-transform duration-200"
                            :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-collapse>
                        <div class="ml-7 mt-0 space-y-0.5 border-l border-gray-700/50 pl-3">
                            <a href="{{ route('senjata.laporan-ringkas') }}"
                                class="flex items-center px-3 py-0.5 rounded-lg transition-all text-xs {{ request()->routeIs('senjata.laporan-ringkas') ? 'bg-primary-600/20 text-primary-400 font-bold' : 'text-gray-500 hover:text-gray-300 hover:bg-gray-800/50' }}">
                                <i class="ph ph-shield mr-2 text-sm"></i> Senjata
                            </a>
                            <a href="{{ route('kendaraan.laporan-ringkas') }}"
                                class="flex items-center px-3 py-1 rounded-lg transition-all text-xs {{ request()->routeIs('kendaraan.laporan-ringkas') ? 'bg-primary-600/20 text-primary-400 font-bold' : 'text-gray-500 hover:text-gray-300 hover:bg-gray-800/50' }}">
                                <i class="ph ph-car mr-2 text-sm"></i> Kendaraan
                            </a>
                            <a href="{{ route('alsus-alsintor.laporan-ringkas') }}"
                                class="flex items-center px-3 py-1 rounded-lg transition-all text-xs {{ request()->routeIs('alsus-alsintor.laporan-ringkas') ? 'bg-primary-600/20 text-primary-400 font-bold' : 'text-gray-500 hover:text-gray-300 hover:bg-gray-800/50' }}">
                                <i class="ph ph-gear mr-2 text-sm"></i> Alsus & Alsintor
                            </a>
                        </div>
                    </div>
                </div>

                @if(in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2']))
                    <div class="px-4 mt-1.5 mb-0.5">
                        <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest opacity-50">Master Data</p>
                    </div>
                    <div class="px-3 space-y-0.5 mb-1">
                        @if(auth()->user()->role === 'Super Admin')
                            <a href="{{ route('activity-logs.index') }}"
                                class="flex items-center px-4 py-1 rounded-xl transition-all {{ request()->routeIs('activity-logs.*') ? 'sidebar-link-active' : '' }}">
                                <i class="ph ph-clock-counter-clockwise mr-2 text-xl"></i>
                                <span class="text-sm font-semibold">Log Aktivitas</span>
                            </a>
                            <a href="{{ route('users.index') }}"
                                class="flex items-center px-4 py-1 rounded-xl transition-all {{ request()->routeIs('users.*') ? 'sidebar-link-active' : '' }}">
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
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden bg-slate-50/50">
            <!-- Top Header -->
            <header class="h-16 flex items-center justify-between px-6 lg:px-8 z-40 transition-all border-b border-slate-200/50 bg-white/50 backdrop-blur-md">
                <div class="flex items-center">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="mr-4 lg:mr-6 text-primary-500 hover:scale-110 transition-all p-2.5 ultra-glass rounded-2xl flex items-center justify-center">
                        <i class="ph ph-list text-2xl"></i>
                    </button>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-primary-500/60 uppercase tracking-[0.2em] mb-0.5">Halaman</span>
                        <h2 class="text-lg lg:text-2xl font-black text-gray-800 dark:text-white tracking-tight">
                            {{ $header ?? 'Beranda' }}
                        </h2>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
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
                            class="relative w-11 h-11 flex items-center justify-center text-primary-500 hover:scale-105 active:scale-95 transition-all ultra-glass rounded-2xl">
                            <i class="ph ph-bell text-2xl"></i>
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
                            class="flex items-center pl-1.5 pr-4 py-1.5 ultra-glass rounded-2xl hover:scale-[1.02] active:scale-95 transition-all focus:outline-none">
                            <div
                                class="w-8 h-8 rounded-xl bg-gradient-to-tr from-primary-600 to-indigo-500 shadow-lg shadow-primary-500/30 flex items-center justify-center text-white mr-3 font-black text-xs uppercase">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="flex flex-col items-start mr-3">
                                <span class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-wider leading-none mb-0.5">{{ Auth::user()->name }}</span>
                                <span class="text-[9px] font-bold text-primary-500/60 uppercase tracking-widest leading-none">{{ auth()->user()->role }}</span>
                            </div>
                            <i class="ph ph-caret-down text-primary-500 text-xs transition-transform duration-300"
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
            <main class="flex-1 overflow-y-auto custom-scrollbar p-4 lg:p-6">
                <div class="max-w-[1600px] mx-auto">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    <!-- Floating Chat Widget -->
    <div x-data="chatWidget()" 
        class="fixed z-[60] select-none"
        :style="'bottom: ' + posY + 'px; right: ' + posX + 'px;'"
        @mousedown="startDrag($event)"
        @touchstart="startDrag($event)">

        <!-- Chat Trigger Button -->
        <button @click="toggleChat"
            class="w-12 h-12 bg-gradient-to-tr from-red-600 to-yellow-500 rounded-2xl shadow-2xl flex items-center justify-center text-white hover:scale-110 active:scale-95 transition-transform relative group cursor-move">
            <i class="ph ph-chat-centered-dots text-xl group-hover:rotate-12 transition-transform"></i>
            <template x-if="unreadCount > 0">
                <span
                    class="absolute -top-1 -right-1 bg-blue-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full border-2 border-[#0f172a]"
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
            class="absolute bottom-16 right-0 w-80 max-w-[calc(100vw-2rem)] h-[450px] bg-[#0f172a]/95 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl flex flex-col overflow-hidden">

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
                isDark: false,
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

                    // Change default detect logic
                    this.isDark = localStorage.getItem('isDark') === 'true';
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
                posX: 24,
                posY: 24,
                isDragging: false,
                startX: 0,
                startY: 0,
                init() {
                    this.updateStatus();
                    this.pollingInterval = setInterval(() => this.updateStatus(), 10000);
                },
                startDrag(e) {
                    if (this.chatOpen) return; // Disable drag when chat is open
                    this.isDragging = true;
                    this.startX = (e.clientX || e.touches[0].clientX);
                    this.startY = (e.clientY || e.touches[0].clientY);
                    
                    const onMove = (e) => {
                        if (!this.isDragging) return;
                        const currX = (e.clientX || e.touches[0].clientX);
                        const currY = (e.clientY || e.touches[0].clientY);
                        
                        const dx = this.startX - currX;
                        const dy = this.startY - currY;
                        
                        this.posX += dx;
                        this.posY += dy;
                        
                        this.startX = currX;
                        this.startY = currY;
                    };
                    
                    const onEnd = () => {
                        this.isDragging = false;
                        window.removeEventListener('mousemove', onMove);
                        window.removeEventListener('mouseup', onEnd);
                        window.removeEventListener('touchmove', onMove);
                        window.removeEventListener('touchend', onEnd);
                    };
                    
                    window.addEventListener('mousemove', onMove);
                    window.addEventListener('mouseup', onEnd);
                    window.addEventListener('touchmove', onMove, { passive: false });
                    window.addEventListener('touchend', onEnd);
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

        window.addEventListener('turbo:before-cache', function() {
            if (typeof Swal !== 'undefined') {
                Swal.close();
            }
        });

        window.addEventListener('turbo:before-cache', function() {
            if (typeof Swal !== 'undefined') {
                Swal.close();
            }
        });

        window.addEventListener('turbo:visit', function() {
            if (typeof Swal !== 'undefined') {
                Swal.close();
            }
        });

        document.addEventListener('turbo:load', function (event) {
            // JANGAN tampilkan alert jika ini hanya preview dari cache Turbo
            if (document.body.hasAttribute('data-turbo-preview')) {
                if (typeof Swal !== 'undefined') Swal.close();
                return;
            }
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
                // Pastikan alert hanya muncul sekali per flash message
                const msg = "{{ session('success') }}";
                const lastMsg = sessionStorage.getItem('last_success_msg');
                
                if (lastMsg !== msg) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: msg,
                        timer: 2500,
                        showConfirmButton: false
                    });
                    sessionStorage.setItem('last_success_msg', msg);
                }
            @endif

            @if(session('error'))
                const errMsg = "{{ session('error') }}";
                const lastErrMsg = sessionStorage.getItem('last_error_msg');
                
                if (lastErrMsg !== errMsg) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Akses Ditolak',
                        text: errMsg,
                    });
                    sessionStorage.setItem('last_error_msg', errMsg);
                }
            @endif
        });
    </script>
    @stack('scripts')
</body>

</html>