<x-app-layout>
    <x-slot name="header">
        Daftar Senjata (Gudang)
    </x-slot>

    <!-- Alerts -->
    @if(session('success'))
        <div class="glass-card border-l-4 border-green-500 p-4 mb-6 animate-slide-in">
            <div class="flex items-center">
                <i class="ph ph-check-circle text-green-500 text-2xl mr-3"></i>
                <p class="text-green-200 text-sm font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="glass-card border-l-4 border-red-500 p-4 mb-6 animate-slide-in">
            <div class="flex items-start">
                <i class="ph ph-warning-circle text-red-500 text-2xl mr-3 mt-0.5"></i>
                <div class="space-y-1">
                    <p class="text-red-200 text-sm font-medium">Beberapa kesalahan ditemukan:</p>
                    <ul class="list-disc list-inside text-red-300/80 text-xs space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="space-y-6 animate-fade-in" x-data="{ 
        showAddModal: false, 
        showEditModal: false, 
        showReturnModal: false,
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
            jenis_senpi: '',
            laras: '',
            nup: '',
            no_senpi: '',
            kondisi: 'Baik',
            penanggung_jawab: '',
            nrp: '',
            status_penyimpanan: 'Gudang',
            jenis_amunisi_dibawa: '',
            jumlah_amunisi_dibawa: 0,
            masa_berlaku_simsa: '',
            keterangan: ''
        },
        addStatus: 'Gudang',
        openEdit(item) {
            this.selectedItem = item;
            this.formData = { ...item };
            this.showEditModal = true;
        },
        openReturn(item) {
            this.selectedItem = item;
            this.formData = { ...item, jumlah_kembali_input: item.jumlah_amunisi_dibawa };
            this.showReturnModal = true;
        },
        async handleImport(e) {
            this.loading = true;
            const formData = new FormData(e.target);
            try {
                const res = await fetch('{{ route('senjata.import') }}', {
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
                const res = await fetch('{{ route('senjata.confirm-import') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({
                        valid_data: this.validData,
                        resolved_conflicts: this.conflicts.map(c => ({
                            existing_id: c.existing.id,
                            decision: c.decision, // 'overwrite' or 'keep'
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
        }
    }">

        <!-- Action & Filter Bar -->
        <div class="glass-card p-2 px-3 rounded-xl flex flex-wrap items-center justify-between gap-3 relative z-50">
            <!-- Left: Action Buttons -->
            <div class="flex items-center gap-2">
                <button @click="showAddModal = true; addStatus = 'Gudang'"
                    class="btn-compact bg-primary-600 hover:bg-primary-500 text-white font-semibold transition-all shadow-lg shadow-primary-500/20 group flex items-center">
                    <i class="ph ph-plus-circle text-lg mr-1.5 group-hover:rotate-90 transition-transform"></i>
                    Tambah
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
                        <a href="{{ route('senjata.export-pdf', array_merge(request()->all(), ['context' => 'Gudang'])) }}"
                            class="flex items-center px-3 py-2 text-[11px] text-gray-300 hover:bg-gray-700/50 transition-colors">
                            <i class="ph ph-file-pdf mr-2 text-red-500 text-base"></i>
                            Cetak PDF
                        </a>
                        <a href="javascript:void(0)" onclick="safeDownload('{{ route('senjata.export-excel', array_merge(request()->all(), ['context' => 'Gudang'])) }}', 'laporan-senjata-gudang.xlsx')"
                            class="flex items-center px-3 py-2 text-[11px] text-gray-300 hover:bg-gray-700/50 transition-colors">
                            <i class="ph ph-file-xls mr-2 text-green-500 text-base"></i>
                            Export Excel
                        </a>
                    </div>
                </div>

                <button @click="showImportModal = true"
                    class="btn-compact bg-gray-800 text-gray-300 border border-gray-700 hover:bg-gray-700 transition-all flex items-center px-2">
                    <i class="ph ph-file-arrow-up text-lg mr-1"></i>
                    Import
                </button>
            </div>

            <!-- Right: Filter Form -->
            <form action="{{ route('senjata.index') }}" method="GET" class="flex flex-wrap items-center gap-2 flex-1 justify-end">
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

                <select name="laras" onchange="this.form.submit()"
                    class="bg-gray-800 border border-gray-700 text-gray-200 text-[11px] rounded-lg px-2 py-1.5 focus:ring-primary-500 min-w-[100px]">
                    <option value="">Laras</option>
                    <option value="Panjang" {{ request('laras') == 'Panjang' ? 'selected' : '' }}>Panjang</option>
                    <option value="Pendek" {{ request('laras') == 'Pendek' ? 'selected' : '' }}>Pendek</option>
                </select>

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
                
                <a href="{{ route('senjata.index') }}" class="p-1.5 bg-gray-800 text-gray-400 hover:text-white rounded-lg border border-gray-700 transition-colors" title="Reset">
                    <i class="ph ph-arrow-counter-clockwise"></i>
                </a>
            </form>
        </div>

        <!-- Table Section -->
        <div class="glass-card rounded-2xl lg:rounded-3xl overflow-hidden">
            <div class="px-4 lg:px-8 py-4 bg-gray-800/10 border-b border-gray-800 flex flex-col sm:flex-row justify-between items-center gap-3">
                <div class="flex items-center space-x-2 text-gray-400">
                    <span class="text-[10px] lg:text-xs uppercase font-semibold">Tampilkan:</span>
                    <select onchange="window.location.href = this.value"
                        class="bg-gray-800/50 border border-gray-700 text-gray-200 text-xs rounded-xl px-3 py-1 focus:ring-primary-500">
                        @foreach([10, 25, 50, 100] as $size)
                            <option value="{{ request()->fullUrlWithQuery(['per_page' => $size]) }}" {{ (request('per_page') ?? 10) == $size ? 'selected' : '' }}>
                                {{ $size }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="text-[10px] lg:text-xs text-gray-500">
                    Menampilkan {{ $senjatas->firstItem() ?? 0 }} - {{ $senjatas->lastItem() ?? 0 }} dari {{ $senjatas->total() }} data
                </div>
            </div>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="table-excel">
                    <thead>
                        <tr>
                            <th class="w-10 text-center">NO</th>
                            @if(!auth()->user()->satker_id || in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2']))
                                <th>Satker</th>
                            @endif
                            <th>Jenis Senjata</th>
                            <th>Laras</th>
                            <th>NUP</th>
                            <th>No. Senpi</th>
                            <th>Kondisi</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @forelse($senjatas as $senjata)
                            <tr class="transition-colors">
                                <td class="text-center font-bold text-gray-500">
                                    {{ $loop->iteration + ($senjatas->firstItem() - 1) }}
                                </td>
                                @if(!auth()->user()->satker_id || in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2']))
                                    <td>{{ $senjata->satker->nama_satker ?? '-' }}</td>
                                @endif
                                <td class="font-bold text-gray-100">{{ $senjata->jenis_senpi }}</td>
                                <td>{{ $senjata->laras }}</td>
                                <td class="font-mono text-xs">{{ $senjata->nup ?? '-' }}</td>
                                <td class="font-mono text-xs">{{ $senjata->no_senpi ?? '-' }}</td>
                                <td>
                                    @php
                                        $condColor = match ($senjata->kondisi) {
                                            'Baik' => 'bg-green-500/10 text-green-400 border-green-500/20',
                                            'Rusak Ringan' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20',
                                            default => 'bg-red-500/10 text-red-400 border-red-500/20'
                                        };
                                    @endphp
                                    <span class="badge-compact border {{ $condColor }}">
                                        {{ $senjata->kondisi }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <div class="flex justify-end items-center space-x-1">
                                        <button @click='formData = { ...@json($senjata) }; showTransferModal = true' class="p-1.5 text-gray-400 hover:text-primary-400 hover:bg-primary-500/10 rounded" title="Kirim">
                                            <i class="ph ph-paper-plane-tilt"></i>
                                        </button>
                                        <button @click='openEdit(@json($senjata))' class="p-1.5 text-gray-400 hover:text-white hover:bg-gray-700 rounded" title="Edit">
                                            <i class="ph ph-pencil-simple"></i>
                                        </button>
                                        <form id="delete-form-{{ $senjata->id }}" action="{{ route('senjata.destroy', $senjata->id) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmDelete('delete-form-{{ $senjata->id }}', 'data ini')" class="p-1.5 text-gray-500 hover:text-red-400 hover:bg-red-500/10 rounded" title="Hapus">
                                                <i class="ph ph-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ (!auth()->user()->satker_id || in_array(auth()->user()->role, ['Super Admin'])) ? 9 : 8 }}"
                                    class="px-8 py-12 text-center text-gray-500 uppercase tracking-widest text-xs">Tidak ada data ditemukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($senjatas->hasPages())
                <div class="px-8 py-4 border-t border-gray-800 bg-gray-800/20">
                    {{ $senjatas->links() }}
                </div>
            @endif
        </div>

        <!-- Modals would go here (Add, Edit, Import) -->
        @include('senjata.modals')
    </div>
</x-app-layout>