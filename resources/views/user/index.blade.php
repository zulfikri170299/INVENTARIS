<x-app-layout>
    <x-slot name="header">
        Manajemen User
    </x-slot>

    <div class="space-y-6 animate-fade-in" x-data="{ 
        showAddModal: false, 
        showEditModal: false,
        selectedItem: null,
        formData: {
            id: '',
            name: '',
            email: '',
            role: 'Super Admin',
            satker_id: '',
            password: '',
            password_confirmation: ''
        },
        openEdit(item) {
            this.selectedItem = item;
            this.formData = { ...item, password: '', password_confirmation: '' };
            this.showEditModal = true;
        }
    }">

        <!-- Action Bar -->
        <div class="glass-card p-4 rounded-2xl flex items-center justify-between mb-6 relative z-50 gap-3 animate-fade-in">
            <div class="flex items-center gap-2">
                <i class="ph ph-users text-primary-500 text-lg"></i>
                <h3 class="text-xs font-bold text-white uppercase tracking-wider">Manajemen User</h3>
            </div>
            <div class="flex items-center space-x-2">
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button type="button" @click="open = !open"
                        class="btn-compact bg-gray-800 hover:bg-gray-700 text-gray-300 font-semibold transition-all flex items-center border border-gray-700">
                        <i class="ph ph-file-arrow-down mr-1.5 text-lg text-primary-400"></i>
                        Export
                        <i class="ph ph-caret-down ml-1.5 text-xs"></i>
                    </button>
                    <div x-show="open" 
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        class="absolute right-0 mt-2 w-40 rounded-xl shadow-2xl bg-gray-800 border border-gray-700 z-50 overflow-hidden"
                        style="display: none;">
                        <a href="{{ route('users.export-pdf', request()->all()) }}" 
                            class="flex items-center px-4 py-2.5 text-[11px] text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                            <i class="ph ph-file-pdf mr-2 text-base text-red-500"></i>
                            Export PDF
                        </a>
                        <a href="{{ route('users.export-excel', request()->all()) }}" 
                            class="flex items-center px-4 py-2.5 text-[11px] text-gray-300 hover:bg-gray-700 hover:text-white transition-colors border-t border-gray-700/50">
                            <i class="ph ph-file-xls mr-2 text-base text-green-500"></i>
                            Export Excel
                        </a>
                    </div>
                </div>
                <button @click="showAddModal = true"
                    class="btn-compact bg-primary-600 hover:bg-primary-500 text-white font-semibold shadow-lg shadow-primary-500/20 transition-all flex items-center">
                    <i class="ph ph-user-plus mr-1.5 text-lg"></i>
                    Tambah User
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="glass-card rounded-3xl overflow-hidden">
            <div class="px-8 py-4 bg-gray-800/10 border-b border-gray-800 flex justify-between items-center">
                <div class="flex items-center space-x-2 text-gray-400">
                    <span class="text-xs uppercase font-semibold">Tampilkan:</span>
                    <select onchange="window.location.href = this.value"
                        class="bg-gray-800/50 border border-gray-700 text-gray-200 text-sm rounded-xl px-3 py-1.5 focus:ring-primary-500 focus:border-primary-500 transition-all">
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
                            <th class="w-10 text-center">NO</th>
                            <th>Satker</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @forelse($users as $user)
                            <tr class="transition-colors">
                                <td class="text-center font-bold text-gray-500">
                                    {{ $loop->iteration + ($users->firstItem() - 1) }}
                                </td>
                                <td>{{ $user->satker->nama_satker ?? 'Global / Admin' }}</td>
                                <td>
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 rounded-full bg-primary-500/20 text-primary-400 flex items-center justify-center font-bold mr-2 border border-primary-500/30">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <span class="font-bold text-gray-100">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="text-gray-400 font-mono">{{ $user->email }}</td>
                                <td>
                                    @php
                                        $roleColor = match ($user->role) {
                                            'Super Admin' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                            'Admin Satker' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
                                            'Pimpinan' => 'bg-green-500/10 text-green-400 border-green-500/20',
                                            default => 'bg-blue-500/10 text-blue-400 border-blue-500/20'
                                        };
                                    @endphp
                                    <span class="badge-compact border {{ $roleColor }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <div class="flex justify-end items-center space-x-1">
                                        <button @click='openEdit(@json($user))'
                                            class="p-1.5 text-gray-400 hover:text-white hover:bg-gray-700 rounded" title="Edit">
                                            <i class="ph ph-pencil-simple"></i>
                                        </button>
                                        @if($user->id !== auth()->id())
                                            <form id="reset-form-{{ $user->id }}"
                                                action="{{ route('users.reset-password', $user->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                <button type="button" onclick="confirmReset('{{ $user->id }}')"
                                                    class="p-1.5 text-gray-400 hover:text-orange-400 hover:bg-orange-500/10 rounded"
                                                    title="Reset Password">
                                                    <i class="ph ph-password"></i>
                                                </button>
                                            </form>
                                            <form id="delete-form-{{ $user->id }}"
                                                action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="button"
                                                    onclick="confirmDelete('delete-form-{{ $user->id }}', 'user ini')"
                                                    class="p-1.5 text-gray-500 hover:text-red-400 hover:bg-red-500/10 rounded" title="Hapus">
                                                    <i class="ph ph-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-12 text-center text-gray-500 uppercase tracking-widest text-xs">Belum ada data User</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="px-8 py-4 border-t border-gray-800 bg-gray-800/20">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        @include('user.modals')

        <script>
            function confirmReset(id) {
                Swal.fire({
                    title: 'Reset Password?',
                    text: "Password user ini akan direset menjadi 'password'!",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#f97316',
                    cancelButtonColor: '#1e293b',
                    confirmButtonText: 'Ya, Reset!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('reset-form-' + id).submit();
                    }
                })
            }
        </script>
    </div>
</x-app-layout>