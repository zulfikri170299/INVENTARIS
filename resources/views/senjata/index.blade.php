<x-app-layout>
    <x-slot name="header">
        Daftar Senjata
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

        <!-- Action Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                <button @click="showAddModal = true"
                    class="px-5 py-2.5 bg-primary-600 hover:bg-primary-500 text-white rounded-xl font-semibold shadow-lg shadow-primary-500/20 transition-all flex items-center">
                    <i class="ph ph-plus-circle mr-2 text-lg"></i>
                    Tambah Data
                </button>
                <button @click="showImportModal = true"
                    class="px-5 py-2.5 bg-green-600 hover:bg-green-500 text-white rounded-xl font-semibold shadow-lg shadow-green-500/20 transition-all flex items-center">
                    <i class="ph ph-file-arrow-up mr-2 text-lg"></i>
                    Import Excel
                </button>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-semibold shadow-lg shadow-indigo-500/20 transition-all flex items-center">
                        <i class="ph ph-export mr-2 text-lg"></i>
                        Export
                        <i class="ph ph-caret-down ml-2 text-sm transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute left-0 mt-2 w-48 bg-gray-800 border border-gray-700 rounded-xl shadow-xl z-50 overflow-hidden">
                        <a href="{{ route('senjata.export-pdf', request()->all()) }}"
                            class="flex items-center px-4 py-3 text-sm text-gray-300 hover:bg-gray-700/50 transition-colors">
                            <i class="ph ph-file-pdf mr-3 text-red-500 text-lg"></i>
                            Cetak PDF
                        </a>
                        <a href="{{ route('senjata.export-excel', request()->all()) }}"
                            class="flex items-center px-4 py-3 text-sm text-gray-300 hover:bg-gray-700/50 transition-colors">
                            <i class="ph ph-file-xls mr-3 text-green-500 text-lg"></i>
                            Export Excel
                        </a>
                    </div>
                </div>
            </div>

            <!-- Simple Search -->
            <form action="{{ route('senjata.index') }}" method="GET" class="flex items-center space-x-2">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari senjata..."
                        class="bg-gray-800/50 border border-gray-700 text-gray-200 text-sm rounded-xl px-10 py-2.5 focus:ring-primary-500 focus:border-primary-500 w-64 transition-all">
                    <i class="ph ph-magnifying-glass absolute left-3 top-3 text-gray-500"></i>
                </div>
                @if(request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif
                <button type="submit"
                    class="p-2.5 bg-gray-800 text-gray-400 hover:text-white rounded-xl border border-gray-700 transition-colors">
                    <i class="ph ph-funnel text-xl"></i>
                </button>
            </form>
        </div>

        <!-- Filter Bar -->
        <div class="glass-card p-6 rounded-2xl">
            @php
                $showSatkerFilter = !auth()->user()->satker_id || in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2']);
            @endphp
            <form action="{{ route('senjata.index') }}" method="GET"
                class="grid grid-cols-1 {{ $showSatkerFilter ? 'md:grid-cols-6' : 'md:grid-cols-5' }} gap-4">
                @if($showSatkerFilter)
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Satker</label>
                        <select name="satker_id"
                            class="w-full bg-gray-800 border border-gray-700 text-gray-200 text-sm rounded-xl px-4 py-2.5 focus:ring-primary-500">
                            <option value="">Semua Satker</option>
                            @foreach($satkers as $satker)
                                <option value="{{ $satker->id }}" {{ request('satker_id') == $satker->id ? 'selected' : '' }}>
                                    {{ $satker->nama_satker }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Laras</label>
                    <select name="laras"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 text-sm rounded-xl px-4 py-2.5 focus:ring-primary-500">
                        <option value="">Semua Laras</option>
                        <option value="Panjang" {{ request('laras') == 'Panjang' ? 'selected' : '' }}>Panjang</option>
                        <option value="Pendek" {{ request('laras') == 'Pendek' ? 'selected' : '' }}>Pendek</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Kondisi</label>
                    <select name="kondisi"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 text-sm rounded-xl px-4 py-2.5 focus:ring-primary-500">
                        <option value="">Semua Kondisi</option>
                        <option value="Baik" {{ request('kondisi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                        <option value="Rusak Ringan" {{ request('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak
                            Ringan</option>
                        <option value="Rusak Berat" {{ request('kondisi') == 'Rusak Berat' ? 'selected' : '' }}>Rusak
                            Berat</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Status</label>
                    <select name="status_penyimpanan"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 text-sm rounded-xl px-4 py-2.5 focus:ring-primary-500">
                        <option value="">Semua Status</option>
                        <option value="Gudang" {{ request('status_penyimpanan') == 'Gudang' ? 'selected' : '' }}>Gudang</option>
                        <option value="Personel" {{ request('status_penyimpanan') == 'Personel' ? 'selected' : '' }}>Personel</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Masa SIMSA</label>
                    <select name="masa_simsa"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 text-sm rounded-xl px-4 py-2.5 focus:ring-primary-500">
                        <option value="">Semua Masa</option>
                        <option value="Aktif" {{ request('masa_simsa') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Akan Habis" {{ request('masa_simsa') == 'Akan Habis' ? 'selected' : '' }}>Akan Habis</option>
                        <option value="Habis" {{ request('masa_simsa') == 'Habis' ? 'selected' : '' }}>Habis</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit"
                        class="w-full px-5 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-xl font-semibold transition-all">
                        Filter
                    </button>
                </div>
            </form>
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
                            @if(!auth()->user()->satker_id || in_array(auth()->user()->role, ['Super Admin']))
                                <th class="px-8 py-4">Satker</th>
                            @endif
                            <th class="px-8 py-4">Nama Senjata</th>
                            <th class="px-8 py-4">NUP</th>
                            <th class="px-8 py-4">No. Senpi</th>
                            <th class="px-8 py-4">Kondisi</th>
                            <th class="px-8 py-4">Status</th>
                            <th class="px-8 py-4">Penanggung Jawab</th>
                            <th class="px-8 py-4 text-center">Masa Berlaku SIMSA</th>
                            <th class="px-8 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800 text-sm text-gray-300">
                        @forelse($senjatas as $senjata)
                            @php
                                $daysToExpiry = $senjata->masa_berlaku_simsa ? \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($senjata->masa_berlaku_simsa), false) : null;
                                $isExpired = $daysToExpiry !== null && $daysToExpiry <= 0;
                                $isNearExpiry = $daysToExpiry !== null && $daysToExpiry > 0 && $daysToExpiry <= 30;

                                $rowClass = '';
                                if ($isExpired) {
                                    $rowClass = 'bg-red-500/10 border-l-4 border-red-500';
                                } elseif ($isNearExpiry) {
                                    $rowClass = 'bg-yellow-500/10 border-l-4 border-yellow-500';
                                }
                            @endphp
                            <tr class="hover:bg-gray-800/30 transition-colors {{ $rowClass }}">
                                <td class="px-2 py-4 text-center font-bold text-gray-500">
                                    {{ $loop->iteration + ($senjatas->firstItem() - 1) }}
                                </td>
                                @if(!auth()->user()->satker_id || in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2']))
                                    <td class="px-8 py-4 text-xs">{{ $senjata->satker->nama_satker ?? '-' }}</td>
                                @endif
                                <td class="px-8 py-4">
                                    <span class="font-medium text-gray-100">{{ $senjata->jenis_senpi }}</span>
                                    <span class="block text-xs text-gray-500">{{ $senjata->laras }}</span>
                                </td>
                                <td class="px-8 py-4 font-mono text-xs">{{ $senjata->nup ?? '-' }}</td>
                                <td class="px-8 py-4 font-mono text-xs">{{ $senjata->no_senpi ?? '-' }}</td>
                                <td class="px-8 py-4">
                                    @php
                                        $color = match ($senjata->kondisi) {
                                            'Baik' => 'bg-green-500/10 text-green-400 ring-green-500/20',
                                            'Rusak Ringan' => 'bg-yellow-500/10 text-yellow-400 ring-yellow-500/20',
                                            default => 'bg-red-500/10 text-red-400 ring-red-500/20'
                                        };
                                    @endphp
                                    <span class="px-2 py-1 rounded-lg {{ $color }} text-xs font-bold ring-1">
                                        {{ $senjata->kondisi }}
                                    </span>
                                </td>
                                <td class="px-8 py-4">
                                    @php
                                        $statusColor = match ($senjata->status_penyimpanan) {
                                            'Gudang' => 'bg-blue-500/10 text-blue-400 ring-blue-500/20',
                                            'Personel' => 'bg-purple-500/10 text-purple-400 ring-purple-500/20',
                                            'Dipinjamkan' => 'bg-orange-500/10 text-orange-400 ring-orange-500/20',
                                            default => 'bg-gray-500/10 text-gray-400 ring-gray-500/20'
                                        };
                                    @endphp
                                    <span class="px-2 py-1 rounded-lg {{ $statusColor }} text-[10px] font-bold ring-1 uppercase">
                                        {{ $senjata->status_penyimpanan ?? '-' }}
                                    </span>
                                    @if($senjata->status_penyimpanan === 'Personel' && $senjata->jenis_amunisi_dibawa)
                                        <div class="mt-1.5 flex items-center gap-1.5">
                                            <i class="ph ph-bullets text-orange-400 text-xs"></i>
                                            <span class="text-[10px] text-orange-300 font-semibold">
                                                {{ $senjata->jenis_amunisi_dibawa }}
                                                @if($senjata->jumlah_amunisi_dibawa)
                                                    <span class="font-bold text-orange-200">({{ $senjata->jumlah_amunisi_dibawa }} butir)</span>
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-8 py-4">
                                    <span class="text-gray-100">{{ $senjata->penanggung_jawab }}</span>
                                    <span class="block text-xs text-gray-500">{{ $senjata->nrp }}</span>
                                </td>
                                <td class="px-8 py-4 text-center text-xs">
                                    @if($senjata->masa_berlaku_simsa)
                                        <div class="space-y-1">
                                            <span
                                                class="block {{ $isExpired ? 'text-red-400 font-bold' : ($isNearExpiry ? 'text-yellow-400 font-bold' : 'text-gray-300') }}">
                                                {{ \Carbon\Carbon::parse($senjata->masa_berlaku_simsa)->format('d/m/Y') }}
                                            </span>
                                            @if($isExpired)
                                                <span
                                                    class="px-2 py-0.5 rounded-full bg-red-500/20 text-red-500 text-[10px] uppercase font-bold border border-red-500/30">
                                                    Habis
                                                </span>
                                            @elseif($isNearExpiry)
                                                <span
                                                    class="px-2 py-0.5 rounded-full bg-yellow-500/20 text-yellow-500 text-[10px] uppercase font-bold border border-yellow-500/30">
                                                    Hampir Habis
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-600">-</span>
                                    @endif
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <div class="flex justify-end space-x-2">
                                        @if($senjata->status_penyimpanan === 'Personel' && $senjata->jumlah_amunisi_dibawa > 0)
                                            <button @click='openReturn(@json($senjata))'
                                                class="p-2 text-orange-400 hover:text-orange-300 transition-colors cursor-pointer"
                                                title="Kembalikan Amunisi">
                                                <i class="ph ph-arrow-u-down-left text-lg"></i>
                                            </button>
                                        @endif
                                        <button @click='openEdit(@json($senjata))'
                                            class="p-2 text-gray-400 hover:text-white transition-colors cursor-pointer"
                                            title="Edit">
                                            <i class="ph ph-pencil-simple text-lg"></i>
                                        </button>
                                        <form id="delete-form-{{ $senjata->id }}" action="{{ route('senjata.destroy', $senjata->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="button" 
                                                onclick="confirmDelete('delete-form-{{ $senjata->id }}', 'data ini')"
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
                                <td colspan="{{ (!auth()->user()->satker_id || in_array(auth()->user()->role, ['Super Admin'])) ? 8 : 7 }}"
                                    class="px-8 py-12 text-center text-gray-500">Tidak ada data ditemukan</td>
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