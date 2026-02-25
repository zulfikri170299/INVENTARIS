<x-app-layout>
    <x-slot name="header">
        Manajemen Satker
    </x-slot>

    <div class="space-y-6 animate-fade-in" x-data="{ 
        showAddModal: false, 
        showEditModal: false,
        selectedItem: null,
        formData: {
            id: '',
            nama_satker: ''
        },
        openEdit(item) {
            this.selectedItem = item;
            this.formData = { ...item };
            this.showEditModal = true;
        }
    }">

        <!-- Action Bar -->
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-white">Daftar Satuan Kerja</h3>
            <button @click="showAddModal = true"
                class="px-5 py-2.5 bg-primary-600 hover:bg-primary-500 text-white rounded-xl font-semibold shadow-lg shadow-primary-500/20 transition-all flex items-center">
                <i class="ph ph-plus-circle mr-2 text-lg"></i>
                Tambah Satker
            </button>
        </div>

        <!-- Table -->
        <div class="glass-card rounded-3xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-800/50 text-gray-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-8 py-4 w-16 text-center">NO</th>
                            <th class="px-8 py-4">Nama Satuan Kerja</th>
                            <th class="px-8 py-4">Total Inventaris</th>
                            <th class="px-8 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800 text-sm text-gray-300">
                        @forelse($satkers as $index => $satker)
                            <tr class="hover:bg-gray-800/30 transition-colors">
                                <td class="px-8 py-4 text-center font-bold text-gray-500">
                                    {{ $loop->iteration + ($satkers->firstItem() - 1) }}
                                </td>
                                <td class="px-8 py-4 font-medium text-gray-100">{{ $satker->nama_satker }}</td>
                                <td class="px-8 py-4">
                                    <span
                                        class="bg-primary-500/10 text-primary-400 px-3 py-1 rounded-full text-xs font-bold border border-primary-500/20">
                                        {{ $satker->senjatas_count ?? 0 }} Item
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <div class="flex justify-end space-x-2">
                                        <button @click='openEdit(@json($satker))'
                                            class="p-2 text-gray-400 hover:text-white transition-colors cursor-pointer"
                                            title="Edit">
                                            <i class="ph ph-pencil-simple text-lg"></i>
                                        </button>
                                        <form action="{{ route('satkers.destroy', $satker->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus Satker ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="p-2 text-gray-400 hover:text-red-400 transition-colors cursor-pointer"
                                                title="Hapus">
                                                <i class="ph ph-trash text-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-12 text-center text-gray-500">Belum ada data Satker</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($satkers->hasPages())
                <div class="px-8 py-4 border-t border-gray-800 bg-gray-800/20">
                    {{ $satkers->links() }}
                </div>
            @endif
        </div>

        @include('satker.modals')
    </div>
</x-app-layout>