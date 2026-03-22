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
        <div class="glass-card p-4 rounded-2xl flex items-center justify-between mb-6 relative z-50">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Daftar Satuan Kerja</h3>
            <div class="flex items-center space-x-2">
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button type="button" @click="open = !open"
                        class="btn-compact bg-gray-800 hover:bg-gray-700 text-gray-300 font-semibold transition-all flex items-center border border-gray-700 text-[11px] h-[34px]">
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
                        <a href="{{ route('satkers.export-pdf', request()->all()) }}" 
                            class="flex items-center px-4 py-2.5 text-[11px] text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                            <i class="ph ph-file-pdf mr-2 text-base text-red-500"></i>
                            Export PDF
                        </a>
                        <a href="{{ route('satkers.export-excel', request()->all()) }}" 
                            class="flex items-center px-4 py-2.5 text-[11px] text-gray-300 hover:bg-gray-700 hover:text-white transition-colors border-t border-gray-700/50">
                            <i class="ph ph-file-xls mr-2 text-base text-green-500"></i>
                            Export Excel
                        </a>
                    </div>
                </div>
                <button @click="showImportModal = true"
                    class="btn-compact bg-gray-800 hover:bg-gray-700 text-gray-300 font-semibold transition-all flex items-center border border-gray-700">
                    <i class="ph ph-file-arrow-up mr-1.5 text-lg text-primary-400"></i>
                    Import Satker
                </button>
                <button @click="showAddModal = true"
                    class="btn-compact bg-primary-600 hover:bg-primary-500 text-white font-semibold shadow-lg shadow-primary-500/20 transition-all flex items-center">
                    <i class="ph ph-plus-circle mr-1.5 text-lg"></i>
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
            <div class="overflow-x-auto custom-scrollbar">
                <table class="table-excel">
                    <thead>
                        <tr>
                            <th class="w-10 text-center">NO</th>
                            <th>Nama Satuan Kerja</th>
                            <th>Total Inventaris</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @forelse($satkers as $index => $satker)
                            <tr class="transition-colors">
                                <td class="text-center font-bold text-gray-500">
                                    {{ $loop->iteration + ($satkers->firstItem() - 1) }}
                                </td>
                                <td class="font-bold text-gray-100">{{ $satker->nama_satker }}</td>
                                <td>
                                    @php
                                        $total = ($satker->senjatas_count ?? 0) +
                                            ($satker->kendaraans_count ?? 0) +
                                            ($satker->alsuses_count ?? 0) +
                                            ($satker->alsintors_count ?? 0) +
                                            ($satker->amunisis_count ?? 0);
                                    @endphp
                                    <span class="badge-compact border bg-primary-500/10 text-primary-400 border-primary-500/20">
                                        {{ $total }} Item
                                    </span>
                                </td>
                                <td class="text-right">
                                    <div class="flex justify-end items-center space-x-1">
                                        <button @click='openEdit(@json($satker))'
                                            class="p-1.5 text-gray-400 hover:text-white hover:bg-gray-700 rounded" title="Edit">
                                            <i class="ph ph-pencil-simple"></i>
                                        </button>
                                        <form id="delete-form- Satker ini"
                                            action="{{ route('satkers.destroy', $satker->id) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="button"
                                                onclick="confirmDelete('delete-form-{{ $satker->id }}', 'Satker ini')"
                                                class="p-1.5 text-gray-500 hover:text-red-400 hover:bg-red-500/10 rounded" title="Hapus">
                                                <i class="ph ph-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-12 text-center text-gray-500 uppercase tracking-widest text-xs">Belum ada data Satker</td>
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