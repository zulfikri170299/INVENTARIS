<x-app-layout>
    <x-slot name="header">
        Manajemen User
    </x-slot>

    <div class="space-y-4 animate-fade-in" x-data="{ 
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
        },
        selectedIds: [],
        get allSelected() {
            const deletableCount = {{ $users->filter(fn($u) => $u->id !== auth()->id())->count() }};
            return this.selectedIds.length === deletableCount && deletableCount > 0;
        },
        toggleAll() {
            if (this.allSelected) {
                this.selectedIds = [];
            } else {
                this.selectedIds = [
                    @foreach($users as $u)
                        @if($u->id !== auth()->id())
                            {{ $u->id }},
                        @endif
                    @endforeach
                ];
            }
        },
        async submitBulkDelete() {
            const { isConfirmed } = await Swal.fire({
                title: 'Hapus Terpilih?',
                text: `Anda akan menghapus ${this.selectedIds.length} user secara masal!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#991b1b',
                cancelButtonColor: '#1e293b',
                confirmButtonText: 'Ya, Hapus Semua!',
                cancelButtonText: 'Batal',
                background: '#0f172a',
                color: '#ffffff'
            });

            if (isConfirmed) {
                try {
                    const res = await fetch('{{ route('users.bulk-delete') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ ids: this.selectedIds })
                    });
                    const data = await res.json();
                    if (data.status === 'success') {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message,
                            icon: 'success',
                            background: '#0f172a',
                            color: '#ffffff',
                            confirmButtonColor: '#991b1b'
                        }).then(() => location.reload());
                    }
                } catch (err) {
                    Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                }
            }
        }
    }">

        @if(session('success'))
            <div class="glass-card border-l-4 border-green-500 p-4 mb-4 animate-slide-in">
                <div class="flex items-center">
                    <i class="ph ph-check-circle text-green-600 text-2xl mr-3"></i>
                    <p class="text-green-800 text-sm font-bold">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="glass-card border-l-4 border-red-500 p-4 mb-4 animate-slide-in">
                <div class="flex items-start">
                    <i class="ph ph-warning-circle text-red-600 text-2xl mr-3 mt-0.5"></i>
                    <div class="space-y-1">
                        <p class="text-red-800 text-sm font-bold">Beberapa kesalahan ditemukan:</p>
                        <ul class="list-disc list-inside text-red-700 text-xs space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Action Bar -->
        <div class="glass-card p-3 rounded-2xl flex items-center justify-between mb-4 relative z-50">
            <h3 class="text-sm font-black text-gray-800 uppercase tracking-wider">Daftar Pengguna Sistem</h3>
            <div class="flex items-center space-x-3">
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button type="button" @click="open = !open"
                        class="btn-compact bg-slate-100 hover:bg-slate-200 text-gray-700 font-bold transition-all flex items-center border border-slate-200 text-[11px] h-[34px]">
                        <i class="ph ph-file-arrow-down mr-1.5 text-lg text-primary-600"></i>
                        Export
                        <i class="ph ph-caret-down ml-1.5 text-xs"></i>
                    </button>
                    <div x-show="open" 
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        class="absolute right-0 mt-2 w-40 rounded-xl shadow-2xl bg-white border border-slate-200 z-50 overflow-hidden"
                        style="display: none;">
                        <a href="{{ route('users.export-pdf', request()->all()) }}" 
                            class="flex items-center px-4 py-2.5 text-[11px] text-gray-600 hover:bg-slate-50 hover:text-gray-900 transition-colors">
                            <i class="ph ph-file-pdf mr-2 text-base text-red-500"></i>
                            Export PDF
                        </a>
                        <a href="{{ route('users.export-excel', request()->all()) }}" 
                            class="flex items-center px-4 py-2.5 text-[11px] text-gray-600 hover:bg-slate-50 hover:text-gray-900 transition-colors border-t border-slate-100">
                            <i class="ph ph-file-xls mr-2 text-base text-green-500"></i>
                            Export Excel
                        </a>
                    </div>
                </div>
                <button @click="showAddModal = true"
                    class="btn-compact bg-primary-600 hover:bg-primary-700 text-white font-bold transition-all group flex items-center h-[34px] text-[11px]">
                    <i class="ph ph-plus-circle text-lg mr-1.5 group-hover:rotate-90 transition-transform"></i>
                    Tambah User
                </button>

                <!-- Bulk Action Button -->
                <button x-show="selectedIds.length > 0" @click="submitBulkDelete"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    class="btn-compact bg-primary-600 hover:bg-primary-700 text-white border border-primary-500 font-bold transition-all flex items-center px-3 shadow-lg shadow-primary-900/20 h-[34px] text-[11px]">
                    <i class="ph ph-trash text-lg mr-1.5"></i>
                    Hapus Terpilih (<span x-text="selectedIds.length"></span>)
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="glass-card rounded-xl overflow-hidden mt-4">
            <div class="px-4 py-1.5 bg-slate-50/50 border-b border-slate-100 flex justify-between items-center">
                <div class="flex items-center space-x-2 text-slate-500">
                    <span class="text-[10px] uppercase font-bold tracking-wider">Tampilkan:</span>
                    <select onchange="window.location.href = this.value"
                        class="bg-white border border-slate-200 text-slate-700 text-[10px] rounded-lg pl-2 pr-8 py-0.5 focus:ring-primary-500 focus:border-primary-500 transition-all">
                        @foreach([10, 25, 50, 100] as $size)
                            <option value="{{ request()->fullUrlWithQuery(['per_page' => $size]) }}" {{ (request('per_page') ?? 10) == $size ? 'selected' : '' }}>
                                {{ $size }}
                            </option>
                        @endforeach
                    </select>
                    <span class="text-xs text-slate-400 ml-2">data per halaman</span>
                </div>
            </div>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="table-excel">
                    <thead>
                        <tr>
                            <th class="w-10 text-center">
                                <input type="checkbox" @click="toggleAll" :checked="allSelected"
                                    class="rounded border-gray-300 bg-white text-primary-600 focus:ring-primary-600 accent-primary-600 transition-all cursor-pointer">
                            </th>
                            <th class="w-10 text-center">NO</th>
                            <th>Satker</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $user)
                            <tr class="transition-colors hover:bg-slate-50/50" :class="selectedIds.includes({{ $user->id }}) ? 'bg-primary-500/5' : ''">
                                <td class="text-center">
                                    <input type="checkbox" :value="{{ $user->id }}" x-model="selectedIds"
                                        :disabled="{{ $user->id === auth()->id() ? 'true' : 'false' }}"
                                        class="rounded border-gray-300 bg-white text-primary-600 focus:ring-primary-600 accent-primary-600 transition-all cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed">
                                    @if($user->id === auth()->id())
                                        <i class="ph ph-user-circle text-slate-300 text-lg opacity-50" title="Anda"></i>
                                    @endif
                                </td>
                                <td class="text-center font-bold text-slate-400">
                                    {{ $loop->iteration + ($users->firstItem() - 1) }}
                                </td>
                                <td class="text-slate-600">{{ $user->satker->nama_satker ?? 'Global / Admin' }}</td>
                                <td>
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-bold mr-2 border border-primary-200">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <span class="font-bold text-gray-800">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="text-slate-500 font-mono">{{ $user->email }}</td>
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
                <div class="px-4 py-0.5 border-t border-slate-100 bg-slate-50/50">
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