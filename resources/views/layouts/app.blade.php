<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Inventaris') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        secondary: '#1e293b',
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            background-color: #0f172a;
            color: #f1f5f9;
        }

        .sidebar-link-active {
            background: linear-gradient(to right, rgba(14, 165, 233, 0.15), transparent);
            border-left: 4px solid #0ea5e9;
            color: #38bdf8;
        }

        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>

    <!-- Alpine.js (for Dropdowns/Sidebar) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="font-sans antialiased text-gray-200" x-data="{ sidebarOpen: true }">
    <div class="min-h-screen flex bg-[#0b1120]">

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0f172a] border-r border-gray-800 transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="flex items-center justify-between px-6 py-6 h-20">
                <div class="flex items-center">
                    <img src="{{ asset('rolog.png') }}" alt="Logo" class="h-8 mr-3">
                    <span
                        class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-500 via-orange-500 to-yellow-500 uppercase tracking-tighter">
                        INVENLOG
                    </span>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-white">
                    <i class="ph ph-x text-2xl"></i>
                </button>
            </div>

            <nav class="mt-6 px-4 space-y-2">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center px-4 py-3 rounded-xl transition-all hover:bg-gray-800 {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : 'text-gray-400' }}">
                    <i class="ph ph-squares-four text-xl mr-3"></i>
                    <span class="font-medium text-sm text-decoration-none">Dashboard</span>
                </a>

                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 mt-8 mb-2">Manajemen</div>

                <a href="{{ route('senjata.index') }}"
                    class="flex items-center px-4 py-3 rounded-xl transition-all hover:bg-gray-800 {{ request()->routeIs('senjata.*') ? 'sidebar-link-active' : 'text-gray-400' }}">
                    <i class="ph ph-shield text-xl mr-3"></i>
                    <span class="font-medium text-sm">Senjata</span>
                </a>
                <a href="{{ route('kendaraan.index') }}"
                    class="flex items-center px-4 py-3 rounded-xl transition-all hover:bg-gray-800 {{ request()->routeIs('kendaraan.*') ? 'sidebar-link-active' : 'text-gray-400' }}">
                    <i class="ph ph-car text-xl mr-3"></i>
                    <span class="font-medium text-sm">Kendaraan</span>
                </a>
                <a href="{{ route('alsus.index') }}"
                    class="flex items-center px-4 py-3 rounded-xl transition-all hover:bg-gray-800 {{ request()->routeIs('alsus.*') ? 'sidebar-link-active' : 'text-gray-400' }}">
                    <i class="ph ph-gear text-xl mr-3"></i>
                    <span class="font-medium text-sm">Alsus</span>
                </a>
                <a href="{{ route('alsintor.index') }}"
                    class="flex items-center px-4 py-3 rounded-xl transition-all hover:bg-gray-800 {{ request()->routeIs('alsintor.*') ? 'sidebar-link-active' : 'text-gray-400' }}">
                    <i class="ph ph-farm text-xl mr-3"></i>
                    <span class="font-medium text-sm">Alsintor</span>
                </a>
                @if(in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2']))
                    <div class="px-6 mb-2">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Master Data</p>
                    </div>
                    <div class="px-3 space-y-1 mb-6">
                        @if(auth()->user()->role === 'Super Admin')
                            <a href="{{ route('users.index') }}"
                                class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 {{ request()->routeIs('users.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30' : 'text-gray-400 hover:bg-gray-800/50 hover:text-white' }}">
                                <i class="ph ph-users mr-3 text-xl"></i>
                                <span class="text-sm font-semibold">Manajemen User</span>
                            </a>
                        @endif
                        <a href="{{ route('satkers.index') }}"
                            class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 {{ request()->routeIs('satkers.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30' : 'text-gray-400 hover:bg-gray-800/50 hover:text-white' }}">
                            <i class="ph ph-buildings mr-3 text-xl"></i>
                            <span class="text-sm font-semibold">Manajemen Satker</span>
                        </a>
                    </div>
                @endif
            </nav>

        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Header -->
            <header
                class="h-20 bg-[#0f172a]/50 backdrop-blur-md border-b border-gray-800 flex items-center justify-between px-8 sticky top-0 z-40">
                <div class="flex items-center">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="mr-6 text-gray-400 hover:text-white transition-colors">
                        <i class="ph ph-list text-2xl"></i>
                    </button>
                    <h2 class="text-lg font-semibold text-gray-100">
                        {{ $header ?? 'Beranda' }}
                    </h2>
                </div>

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
    <div x-data="{ 
            chatOpen: false, 
            activeUser: null, 
            users: [], 
            messages: [], 
            newMessage: '', 
            unreadCount: 0,
            pollingInterval: null,
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
                // Set interval for messages polling
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
        }" class="fixed bottom-6 right-6 z-[60]">

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
</body>

</html>