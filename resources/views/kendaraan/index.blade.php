<x-app-layout>
    <x-slot name="header">
        Daftar Kendaraan
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
        showImportModal: false,
        showConflictModal: false,
        loading: false,
        conflicts: [],
        validData: [],
        selectedItem: null,
        formData: {
            id: '',
            satker_id: '',
            jenis_kendaraan: '',
            nup: '',
            no_rangka: '',
            nopol: '',
            kondisi: 'Baik',
            bahan_bakar: 'Pertalite',
            jenis_roda: 'R4',
            penanggung_jawab: '',
            nrp: '',
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
                const res = await fetch('{{ route('kendaraan.import') }}', {
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
                const res = await fetch('{{ route('kendaraan.confirm-import') }}', {
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
                        <i class="ph ph-caret-down ml-2 text-sm transition-transform"
                            :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute left-0 mt-2 w-48 bg-gray-800 border border-gray-700 rounded-xl shadow-xl z-50 overflow-hidden">
                        <a href="{{ route('kendaraan.export-pdf', request()->all()) }}"
                            class="flex items-center px-4 py-3 text-sm text-gray-300 hover:bg-gray-700/50 transition-colors">
                            <i class="ph ph-file-pdf mr-3 text-red-500 text-lg"></i>
                            Cetak PDF
                        </a>
                        <a href="{{ route('kendaraan.export-excel', request()->all()) }}"
                            class="flex items-center px-4 py-3 text-sm text-gray-300 hover:bg-gray-700/50 transition-colors">
                            <i class="ph ph-file-xls mr-3 text-green-500 text-lg"></i>
                            Export Excel
                        </a>
                    </div>
                </div>
            </div>

            <!-- Simple Search -->
            <form action="{{ route('kendaraan.index') }}" method="GET" class="flex items-center space-x-2">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kendaraan..."
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
            <form action="{{ route('kendaraan.index') }}" method="GET"
                class="grid grid-cols-1 {{ $showSatkerFilter ? 'md:grid-cols-4' : 'md:grid-cols-3' }} gap-4">
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
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Jenis Roda</label>
                    <select name="jenis_roda"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-200 text-sm rounded-xl px-4 py-2.5 focus:ring-primary-500">
                        <option value="">Semua Roda</option>
                        <option value="R2" {{ request('jenis_roda') == 'R2' ? 'selected' : '' }}>R2</option>
                        <option value="R4" {{ request('jenis_roda') == 'R4' ? 'selected' : '' }}>R4</option>
                        <option value="R6" {{ request('jenis_roda') == 'R6' ? 'selected' : '' }}>R6</option>
                        <option value="R8" {{ request('jenis_roda') == 'R8' ? 'selected' : '' }}>R8</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit"
                        class="w-full px-5 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-xl font-semibold transition-all">
                        Terapkan Filter
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
                            <th class="px-8 py-4">Jenis Kendaraan</th>
                            <th class="px-8 py-4">Plat Nomor</th>
                            <th class="px-8 py-4">No. Rangka</th>
                            <th class="px-8 py-4">No. Mesin</th>
                            <th class="px-8 py-4">Tahun</th>
                            <th class="px-8 py-4">Roda</th>
                            <th class="px-8 py-4">Kondisi</th>
                            <th class="px-8 py-4">Penanggung Jawab</th>
                            <th class="px-8 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800 text-sm text-gray-300">
                        @forelse($kendaraans as $kendaraan)
                            <tr class="hover:bg-gray-800/30 transition-colors">
                                <td class="px-2 py-4 text-center font-bold text-gray-500">
                                    {{ $loop->iteration + ($kendaraans->firstItem() - 1) }}
                                </td>
                                @if(!auth()->user()->satker_id || in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2']))
                                    <td class="px-8 py-4 text-xs">{{ $kendaraan->satker->nama_satker ?? '-' }}</td>
                                @endif
                                <td class="px-8 py-4">
                                    <span class="font-medium text-gray-100">{{ $kendaraan->jenis_kendaraan }}</span>
                                    <span class="block text-xs text-gray-500">{{ $kendaraan->bahan_bakar }}</span>
                                </td>
                                <td class="px-8 py-4 font-mono text-xs">{{ $kendaraan->nopol ?? '-' }}</td>
                                <td class="px-8 py-4 font-mono text-xs">{{ $kendaraan->no_rangka ?? '-' }}</td>
                                <td class="px-8 py-4 font-mono text-xs">{{ $kendaraan->no_mesin ?? '-' }}</td>
                                <td class="px-8 py-4 text-xs">{{ $kendaraan->tahun_pembuatan ?? '-' }}</td>
                                <td class="px-8 py-4 text-xs font-bold text-gray-400">{{ $kendaraan->jenis_roda }}</td>
                                <td class="px-8 py-4">
                                    @php
                                        $color = match ($kendaraan->kondisi) {
                                            'Baik' => 'bg-green-500/10 text-green-400 ring-green-500/20',
                                            'Rusak Ringan' => 'bg-yellow-500/10 text-yellow-400 ring-yellow-500/20',
                                            default => 'bg-red-500/10 text-red-400 ring-red-500/20'
                                        };
                                    @endphp
                                    <span class="px-2 py-1 rounded-lg {{ $color }} text-xs font-bold ring-1">
                                        {{ $kendaraan->kondisi }}
                                    </span>
                                </td>
                                <td class="px-8 py-4">
                                    <span class="text-gray-100">{{ $kendaraan->penanggung_jawab ?? '-' }}</span>
                                    <span class="block text-xs text-gray-500">{{ $kendaraan->nrp ?? '' }}</span>
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <div class="flex justify-end space-x-2">
                                        <button @click='openEdit(@json($kendaraan))'
                                            class="p-2 text-gray-400 hover:text-white transition-colors cursor-pointer"
                                            title="Edit">
                                            <i class="ph ph-pencil-simple text-lg"></i>
                                        </button>
                                        <form id="delete-form-{{ $kendaraan->id }}"
                                            action="{{ route('kendaraan.destroy', $kendaraan->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="button"
                                                onclick="confirmDelete('delete-form-{{ $kendaraan->id }}', 'data ini')"
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
            @if($kendaraans->hasPages())
                <div class="px-8 py-4 border-t border-gray-800 bg-gray-800/20">
                    {{ $kendaraans->links() }}
                </div>
            @endif
        </div>

        @include('kendaraan.modals')
    </div>
</x-app-layout>