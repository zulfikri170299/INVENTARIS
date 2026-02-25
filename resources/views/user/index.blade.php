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
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-white">Daftar Pengguna</h3>
            <button @click="showAddModal = true"
                class="px-5 py-2.5 bg-primary-600 hover:bg-primary-500 text-white rounded-xl font-semibold shadow-lg shadow-primary-500/20 transition-all flex items-center">
                <i class="ph ph-user-plus mr-2 text-lg"></i>
                Tambah User
            </button>
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
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-800/50 text-gray-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-2 py-4 w-10 text-center">NO</th>
                            <th class="px-8 py-4">Satker</th>
                            <th class="px-8 py-4">Nama</th>
                            <th class="px-8 py-4">Email</th>
                            <th class="px-8 py-4">Role</th>
                            <th class="px-8 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800 text-sm text-gray-300">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-800/30 transition-colors">
                                <td class="px-2 py-4 text-center font-bold text-gray-500">
                                    {{ $loop->iteration + ($users->firstItem() - 1) }}
                                </td>
                                <td class="px-8 py-4 text-xs">{{ $user->satker->nama_satker ?? 'Global / Admin' }}</td>
                                <td class="px-8 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="w-8 h-8 rounded-full bg-primary-500/20 text-primary-400 flex items-center justify-center font-bold mr-3 border border-primary-500/30">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <span class="font-medium text-gray-100">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-4 text-gray-400">{{ $user->email }}</td>
                                <td class="px-8 py-4">
                                    @php
                                        $roleColor = match ($user->role) {
                                            'Super Admin' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                            'Admin Satker' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
                                            'Pimpinan' => 'bg-green-500/10 text-green-400 border-green-500/20',
                                            default => 'bg-blue-500/10 text-blue-400 border-blue-500/20'
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-lg {{ $roleColor }} text-xs font-bold border">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <div class="flex justify-end space-x-2">
                                        <button @click='openEdit(@json($user))'
                                            class="p-2 text-gray-400 hover:text-white transition-colors cursor-pointer"
                                            title="Edit">
                                            <i class="ph ph-pencil-simple text-lg"></i>
                                        </button>
                                        @if($user->id !== auth()->id())
                                            <form id="reset-form-{{ $user->id }}"
                                                action="{{ route('users.reset-password', $user->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                <button type="button" onclick="confirmReset('{{ $user->id }}')"
                                                    class="p-2 text-gray-400 hover:text-orange-400 transition-colors cursor-pointer"
                                                    title="Reset Password">
                                                    <i class="ph ph-password text-lg"></i>
                                                </button>
                                            </form>
                                            <form id="delete-form-{{ $user->id }}"
                                                action="{{ route('users.destroy', $user->id) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="button"
                                                    onclick="confirmDelete('delete-form-{{ $user->id }}', 'user ini')"
                                                    class="p-2 text-gray-400 hover:text-red-400 transition-colors cursor-pointer"
                                                    title="Hapus">
                                                    <i class="ph ph-trash text-lg"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-12 text-center text-gray-500">Belum ada data User</td>
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