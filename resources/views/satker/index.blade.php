<x-app-layout>
    <x-slot name="header">
        Manajemen Satker
    </x-slot>

    <div class="space-y-6 animate-fade-in" x-data="{ 
        showAddModal: false, 
        showEditModal: false,
        showImportModal: false,
        showConflictModal: {{ session('import_conflicts') ? 'true' : 'false' }},
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
            <div class="flex items-center space-x-3">
                <button @click="showImportModal = true"
                    class="px-5 py-2.5 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-xl font-semibold transition-all flex items-center border border-gray-700">
                    <i class="ph ph-file-arrow-up mr-2 text-lg text-primary-400"></i>
                    Import Satker
                </button>
                <button @click="showAddModal = true"
                    class="px-5 py-2.5 bg-primary-600 hover:bg-primary-500 text-white rounded-xl font-semibold shadow-lg shadow-primary-500/20 transition-all flex items-center">
                    <i class="ph ph-plus-circle mr-2 text-lg"></i>
                    Tambah Satker
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
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-800/50 text-gray-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-2 py-4 w-10 text-center">NO</th>
                            <th class="px-8 py-4">Nama Satuan Kerja</th>
                            <th class="px-8 py-4">Total Inventaris</th>
                            <th class="px-8 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800 text-sm text-gray-300">
                        @forelse($satkers as $index => $satker)
                            <tr class="hover:bg-gray-800/30 transition-colors">
                                <td class="px-2 py-4 text-center font-bold text-gray-500">
                                    {{ $loop->iteration + ($satkers->firstItem() - 1) }}
                                </td>
                                <td class="px-8 py-4 font-medium text-gray-100">{{ $satker->nama_satker }}</td>
                                <td class="px-8 py-4">
                                    @php
                                        $total = ($satker->senjatas_count ?? 0) +
                                            ($satker->kendaraans_count ?? 0) +
                                            ($satker->alsuses_count ?? 0) +
                                            ($satker->alsintors_count ?? 0) +
                                            ($satker->amunisis_count ?? 0);
                                    @endphp
                                    <span
                                        class="bg-primary-500/10 text-primary-400 px-3 py-1 rounded-full text-xs font-bold border border-primary-500/20">
                                        {{ $total }} Item
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <div class="flex justify-end space-x-2">
                                        <button @click='openEdit(@json($satker))'
                                            class="p-2 text-gray-400 hover:text-white transition-colors cursor-pointer"
                                            title="Edit">
                                            <i class="ph ph-pencil-simple text-lg"></i>
                                        </button>
                                        <form id="delete-form-{{ $satker->id }}"
                                            action="{{ route('satkers.destroy', $satker->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="button"
                                                onclick="confirmDelete('delete-form-{{ $satker->id }}', 'Satker ini')"
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