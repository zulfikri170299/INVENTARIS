<x-app-layout>
    <x-slot name="header">
        Manajemen Satker
    </x-slot>

    <div class="space-y-4 animate-fade-in" x-data="{ 
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
        },
        selectedIds: [],
        get allSelected() {
            return this.selectedIds.length === {{ $satkers->count() }} && {{ $satkers->count() }} > 0;
        },
        toggleAll() {
            if (this.allSelected) {
                this.selectedIds = [];
            } else {
                this.selectedIds = [
                    @foreach($satkers as $s)
                        {{ $s->id }},
                    @endforeach
                ];
            }
        },
        async submitBulkDelete() {
            const { isConfirmed } = await Swal.fire({
                title: 'Hapus Terpilih?',
                text: `Anda akan menghapus ${this.selectedIds.length} data Satker secara masal!`,
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
                    const res = await fetch('{{ route('satkers.bulk-delete') }}', {
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
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.message,
                            icon: 'error',
                            background: '#0f172a',
                            color: '#ffffff',
                            confirmButtonColor: '#991b1b'
                        });
                    }
                } catch (err) {
                    Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                }
            }
        }
    }">

        <!-- Action Bar -->
        <div class="glass-card p-3 rounded-2xl flex items-center justify-between mb-4 relative z-50">
            <h3 class="text-sm font-black text-gray-800 uppercase tracking-wider">Daftar Satuan Kerja</h3>
            <div class="flex items-center space-x-2">
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
                        class="absolute right-0 mt-2 w-48 rounded-xl shadow-2xl bg-white border border-slate-200 z-50 overflow-hidden"
                        style="display: none;">
                        <a href="{{ route('satkers.export-pdf', request()->all()) }}" 
                            class="flex items-center px-4 py-2.5 text-[11px] text-gray-700 hover:bg-slate-50 transition-colors">
                            <i class="ph ph-file-pdf mr-2 text-base text-red-500"></i>
                            Export PDF
                        </a>
                        <a href="{{ route('satkers.export-excel', request()->all()) }}" 
                            class="flex items-center px-4 py-2.5 text-[11px] text-gray-700 hover:bg-slate-50 transition-colors border-t border-slate-100">
                            <i class="ph ph-file-xls mr-2 text-base text-green-600"></i>
                            Export Excel
                        </a>
                    </div>
                </div>
                <button @click="showImportModal = true"
                    class="btn-compact bg-slate-100 hover:bg-slate-200 text-gray-700 font-bold transition-all flex items-center border border-slate-200">
                    <i class="ph ph-file-arrow-up mr-1.5 text-lg text-primary-600"></i>
                    Import Satker
                </button>
                <button @click="showAddModal = true"
                    class="btn-compact bg-primary-500/10 hover:bg-primary-500/20 text-primary-500 border border-primary-500/30 font-bold transition-all group flex items-center">
                    <i class="ph ph-plus-circle text-lg mr-1.5 group-hover:rotate-90 transition-transform"></i>
                    Tambah Satker
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
        <div class="glass-card rounded-xl overflow-hidden mt-2">
            <div class="px-4 py-1.5 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <div class="flex items-center space-x-2 text-slate-500">
                    <span class="text-[10px] uppercase font-bold tracking-wider">Tampilkan:</span>
                    <select onchange="window.location.href = this.value"
                        class="bg-white border border-slate-200 text-gray-900 text-[10px] rounded-lg pl-2 pr-8 py-0.5 focus:ring-primary-500 focus:border-primary-500 transition-all">
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
                            <th class="w-10 text-center">
                                <input type="checkbox" @click="toggleAll" :checked="allSelected"
                                    class="rounded border-gray-300 bg-white text-primary-600 focus:ring-primary-600 accent-primary-600 transition-all cursor-pointer">
                            </th>
                            <th class="w-10 text-center">NO</th>
                            <th>Nama Satuan Kerja</th>
                            <th>Total Inventaris</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($satkers as $index => $satker)
                            <tr class="transition-colors" :class="selectedIds.includes({{ $satker->id }}) ? 'bg-primary-500/5' : ''">
                                <td class="text-center">
                                    <input type="checkbox" :value="{{ $satker->id }}" x-model="selectedIds"
                                        class="rounded border-gray-300 bg-white text-primary-600 focus:ring-primary-600 accent-primary-600 transition-all cursor-pointer">
                                </td>
                                <td class="text-center font-bold text-slate-400">
                                    {{ $loop->iteration + ($satkers->firstItem() - 1) }}
                                </td>
                                <td class="font-black text-gray-800">{{ $satker->nama_satker }}</td>
                                <td>
                                    @php
                                        $total = ($satker->senjatas_count ?? 0) +
                                            ($satker->kendaraans_count ?? 0) +
                                            ($satker->alsuses_count ?? 0) +
                                            ($satker->alsintors_count ?? 0) +
                                            ($satker->amunisis_count ?? 0);
                                    @endphp
                                    <span class="badge-compact border bg-primary-500/10 text-primary-700 border-primary-500/20">
                                        {{ $total }} Item
                                    </span>
                                </td>
                                <td class="text-right">
                                    <div class="flex justify-end items-center space-x-1">
                                        <button @click='openEdit(@json($satker))'
                                            class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded transition-all" title="Edit">
                                            <i class="ph ph-pencil-simple"></i>
                                        </button>
                                        <form id="delete-form-{{ $satker->id }}"
                                            action="{{ route('satkers.destroy', $satker->id) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="button"
                                                onclick="confirmDelete('delete-form-{{ $satker->id }}', 'Satker ini')"
                                                class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded transition-all" title="Hapus">
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
                <div class="px-4 py-0.5 border-t border-slate-100 bg-slate-50/50">
                    {{ $satkers->links() }}
                </div>
            @endif
        </div>

        @include('satker.modals')
    </div>
</x-app-layout>