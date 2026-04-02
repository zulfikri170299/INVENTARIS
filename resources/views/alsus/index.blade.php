<x-app-layout>
    <x-slot name="header">
        Daftar Alat Khusus (Alsus)
    </x-slot>

    <!-- Alerts -->
    @if(session('success'))
        <div class="glass-card border-l-4 border-green-500 p-4 mb-6 animate-slide-in">
            <div class="flex items-center">
                <i class="ph ph-check-circle text-green-600 text-2xl mr-3"></i>
                <p class="text-green-800 text-sm font-bold">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="glass-card border-l-4 border-red-500 p-4 mb-6 animate-slide-in">
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

    <div class="space-y-4 animate-fade-in" x-data="{ 
        showAddModal: false, 
        showEditModal: false, 
        showImportModal: false,
        showConflictModal: false,
        showTransferModal: false,
        loading: false,
        conflicts: [],
        validData: [],
        selectedItem: null,
        formData: {
            id: '',
            satker_id: '',
            jenis_barang: '',
            nup: '',
            kondisi: 'Baik',
            keterangan: ''
        },
        openEdit(item) {
            this.selectedItem = item;
            this.formData = { ...item };
            this.showEditModal = true;
        },
        async handleImport(e) {
            this.loading = true;
            const formData = new FormData(e.target);
            try {
                const res = await fetch('{{ route('alsus.import') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData
                });
                const data = await res.json();
                if (data.status === 'conflict') {
                    this.conflicts = data.conflicts.map(c => ({ ...c, decision: 'overwrite' }));
                    this.validData = data.valid_data;
                    this.showImportModal = false;
                    this.showConflictModal = true;
                } else {
                    location.reload();
                }
            } catch (err) {
                console.error(err);
                Swal.fire('Error', 'Terjadi kesalahan saat mengimpor data.', 'error');
            } finally {
                this.loading = false;
            }
        },
        async submitResolvedConflicts() {
            this.loading = true;
            try {
                const res = await fetch('{{ route('alsus.confirm-import') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({
                        valid_data: this.validData,
                        resolved_conflicts: this.conflicts.map(c => ({
                            existing_id: c.existing.id,
                            decision: c.decision,
                            new_data: c.new
                        }))
                    })
                });
                const data = await res.json();
                if (data.status === 'success') {
                    location.reload();
                }
            } catch (err) {
                console.error(err);
            } finally {
                this.loading = false;
            }
        },
        selectedIds: [],
        get allSelected() {
            return this.selectedIds.length === {{ $alsuses->count() }} && {{ $alsuses->count() }} > 0;
        },
        toggleAll() {
            if (this.allSelected) {
                this.selectedIds = [];
            } else {
                this.selectedIds = [
                    @foreach($alsuses as $a)
                        {{ $a->id }},
                    @endforeach
                ];
            }
        },
        async submitBulkDelete() {
            const { isConfirmed } = await Swal.fire({
                title: 'Hapus Terpilih?',
                text: `Anda akan menghapus ${this.selectedIds.length} data Alsus secara masal!`,
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
                    const res = await fetch('{{ route('alsus.bulk-delete') }}', {
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

        <!-- Action & Filter Bar -->
        <div class="glass-card p-4 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-primary-500/5 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
            
            <div class="relative flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-2">
                    <button @click="showAddModal = true" 
                        class="btn-compact bg-primary-600 hover:bg-primary-700 shadow-lg shadow-primary-900/20 px-4 py-2 text-sm font-bold flex items-center gap-2 rounded-xl transition-all active:scale-95 text-white">
                        <i class="ph-bold ph-plus-circle text-lg"></i>
                        TAMBAH
                    </button>
                
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="btn-compact bg-gray-800 text-gray-300 border border-gray-700 hover:bg-gray-700 transition-all flex items-center px-2">
                        <i class="ph ph-export text-lg mr-1"></i>
                        Export
                        <i class="ph ph-caret-down ml-1 text-[10px]"></i>
                    </button>
                    <div x-show="open" @click.away="open = false"
                        class="absolute left-0 mt-1 w-40 bg-gray-800 border border-gray-700 rounded-xl shadow-xl z-30 transition-all overflow-hidden"
                        x-cloak>
                        <a href="javascript:void(0)" onclick="safeDownload('{{ route('alsus.export-pdf', request()->all()) }}', 'laporan-alsus.pdf')"
                            class="flex items-center px-3 py-2 text-[11px] text-gray-300 hover:bg-gray-700/50 transition-colors">
                            <i class="ph ph-file-pdf mr-2 text-red-500 text-base"></i>
                            Cetak PDF
                        </a>
                        <a href="javascript:void(0)" onclick="safeDownload('{{ route('alsus.export-excel', request()->all()) }}', 'laporan-alsus.xlsx')"
                            class="flex items-center px-3 py-2 text-[11px] text-gray-300 hover:bg-gray-700/50 transition-colors">
                            <i class="ph ph-file-xls mr-2 text-green-500 text-base"></i>
                            Export Excel
                        </a>
                        <a href="javascript:void(0)" onclick="safeDownload('{{ route('alsus-alsintor.export-summary', request()->all()) }}', 'laporan-ringkas-alsus-alsintor.xlsx')"
                            class="flex items-center px-3 py-2 text-[11px] text-gray-300 hover:bg-gray-700/50 transition-colors">
                            <i class="ph ph-file-xls mr-2 text-blue-500 text-base"></i>
                            Laporan Ringkas
                        </a>
                    </div>
                </div>

                <button @click="showImportModal = true"
                    class="btn-compact bg-gray-800 text-gray-300 border border-gray-700 hover:bg-gray-700 transition-all flex items-center px-2">
                    <i class="ph ph-file-arrow-up text-lg mr-1"></i>
                    Import
                </button>

                <!-- Bulk Action Button -->
                <button x-show="selectedIds.length > 0" @click="submitBulkDelete"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    class="btn-compact bg-primary-600 hover:bg-primary-700 text-white border border-primary-500 font-bold transition-all flex items-center px-3 shadow-lg shadow-primary-900/20">
                    <i class="ph ph-trash text-lg mr-1.5"></i>
                    Hapus Terpilih (<span x-text="selectedIds.length"></span>)
                </button>
            </div>

            <!-- Right: Filter Form -->
            <form action="{{ route('alsus.index') }}" method="GET" class="flex flex-wrap items-center gap-2 flex-1 justify-end">
                @php
                    $showSatkerFilter = !auth()->user()->satker_id || in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2']);
                @endphp
                
                @if($showSatkerFilter)
                    <select name="satker_id" onchange="this.form.submit()"
                        class="bg-gray-800 border border-gray-700 text-gray-200 text-[11px] rounded-lg px-2 py-1.5 focus:ring-primary-500 min-w-[120px]">
                        <option value="">Semua Satker</option>
                        @foreach($satkers as $satker)
                            <option value="{{ $satker->id }}" {{ request('satker_id') == $satker->id ? 'selected' : '' }}>
                                {{ $satker->nama_satker }}
                            </option>
                        @endforeach
                    </select>
                @endif

                <select name="kondisi" onchange="this.form.submit()"
                    class="bg-gray-800 border border-gray-700 text-gray-200 text-[11px] rounded-lg px-2 py-1.5 focus:ring-primary-500 min-w-[100px]">
                    <option value="">Kondisi</option>
                    <option value="Baik" {{ request('kondisi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                    <option value="Rusak Ringan" {{ request('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                    <option value="Rusak Berat" {{ request('kondisi') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                </select>

                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..."
                        class="bg-gray-800/50 border border-gray-700 text-gray-200 text-[11px] rounded-lg pl-8 pr-3 py-1.5 focus:ring-primary-500 w-32 lg:w-40 transition-all">
                    <i class="ph ph-magnifying-glass absolute left-2.5 top-2 text-gray-500 text-xs"></i>
                </div>
                
                <a href="{{ route('alsus.index') }}" class="p-1.5 bg-gray-800 text-gray-400 hover:text-white rounded-lg border border-gray-700 transition-colors" title="Reset">
                    <i class="ph ph-arrow-counter-clockwise"></i>
                </a>
            </form>
        </div>

        <!-- Table -->
        <div class="glass-card rounded-xl overflow-hidden mt-4">
            <div class="px-4 py-2 border-b border-gray-800 bg-gray-800/10 flex justify-between items-center">
                <div class="flex items-center space-x-2 text-gray-400">
                    <span class="text-[10px] uppercase font-bold tracking-wider">Tampilkan:</span>
                    <select onchange="window.location.href = this.value"
                        class="bg-gray-800/50 border border-gray-700 text-gray-200 text-[10px] rounded-lg pl-2 pr-8 py-0.5 focus:ring-primary-500 focus:border-primary-500 transition-all">
                        @foreach([10, 25, 50, 100] as $size)
                            <option value="{{ request()->fullUrlWithQuery(['per_page' => $size]) }}" {{ (request('per_page') ?? 10) == $size ? 'selected' : '' }}>
                                {{ $size }}
                            </option>
                        @endforeach
                    </select>
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
                            @if(!auth()->user()->satker_id || in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2']))
                                <th>Satker</th>
                            @endif
                            <th>Nama Barang</th>
                            <th>NUP</th>
                            <th>Kondisi</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @forelse($alsuses as $alsus)
                            <tr class="transition-colors" :class="selectedIds.includes({{ $alsus->id }}) ? 'bg-primary-500/5' : ''">
                                <td class="text-center">
                                    <input type="checkbox" :value="{{ $alsus->id }}" x-model="selectedIds"
                                        class="rounded border-gray-300 bg-white text-primary-600 focus:ring-primary-600 accent-primary-600 transition-all cursor-pointer">
                                </td>
                                <td class="text-center font-bold text-gray-500">
                                    {{ $loop->iteration + ($alsuses->firstItem() - 1) }}
                                </td>
                                @if(!auth()->user()->satker_id || in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2']))
                                    <td class="font-medium">{{ $alsus->satker->nama_satker ?? '-' }}</td>
                                @endif
                                <td class="font-bold text-gray-100">{{ $alsus->jenis_barang }}</td>
                                <td class="font-mono">{{ $alsus->nup ?? '-' }}</td>
                                <td>
                                    @php
                                        $condColor = match ($alsus->kondisi) {
                                            'Baik' => 'bg-green-500/10 text-green-400 border-green-500/20',
                                            'Rusak Ringan' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20',
                                            default => 'bg-red-500/10 text-red-400 border-red-500/20'
                                        };
                                    @endphp
                                    <span class="badge-compact border {{ $condColor }}">
                                        {{ $alsus->kondisi }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <div class="flex justify-end items-center space-x-1">
                                        <button @click='formData = { ...@json($alsus) }; showTransferModal = true'
                                            class="p-1.5 text-gray-400 hover:text-primary-400 hover:bg-primary-500/10 rounded" title="Kirim">
                                            <i class="ph ph-paper-plane-tilt"></i>
                                        </button>
                                        <button @click='openEdit(@json($alsus))'
                                            class="p-1.5 text-gray-400 hover:text-white hover:bg-gray-700 rounded" title="Edit">
                                            <i class="ph ph-pencil-simple"></i>
                                        </button>
                                        <form id="delete-form-{{ $alsus->id }}"
                                            action="{{ route('alsus.destroy', $alsus->id) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="button"
                                                onclick="confirmDelete('delete-form-{{ $alsus->id }}', 'data ini')"
                                                class="p-1.5 text-gray-500 hover:text-red-400 hover:bg-red-500/10 rounded" title="Hapus">
                                                <i class="ph ph-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ (!auth()->user()->satker_id || in_array(auth()->user()->role, ['Super Admin'])) ? 6 : 5 }}"
                                    class="px-8 py-12 text-center text-gray-500 uppercase tracking-widest text-xs">Tidak ada data ditemukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($alsuses->hasPages())
                <div class="px-4 py-0.5 border-t border-gray-800 bg-gray-800/10">
                    {{ $alsuses->links() }}
                </div>
            @endif
        </div>

        @include('alsus.modals')
    </div>
</x-app-layout>