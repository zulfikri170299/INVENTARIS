<!-- Add Modal -->
<div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showAddModal" @click="showAddModal = false"
            class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm"></div>
        <div
            class="inline-block w-full max-w-2xl p-8 my-8 overflow-hidden text-left align-middle transition-all transform glass-card rounded-3xl shadow-2xl relative border border-gray-800">
            <h3 class="text-2xl font-bold text-white mb-6">Tambah Alsintor</h3>
            <form action="{{ route('alsintor.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Satker</label>
                        @if(auth()->user()->satker_id)
                            <div
                                class="w-full bg-gray-800/30 border border-gray-700/50 text-gray-400 rounded-xl px-4 py-3 cursor-not-allowed">
                                {{ auth()->user()->satker->nama_satker ?? 'Satker Anda' }}
                                <input type="hidden" name="satker_id" value="{{ auth()->user()->satker_id }}">
                            </div>
                        @else
                            <select name="satker_id" required
                                class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                                @foreach($satkers as $satker)
                                    <option value="{{ $satker->id }}">{{ $satker->nama_satker }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Jenis Barang</label>
                        <input type="text" name="jenis_barang" required
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">NUP</label>
                        <input type="text" name="nup"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Kondisi</label>
                        <select name="kondisi"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                            <option value="Baik">Baik</option>
                            <option value="Rusak Ringan">Rusak Ringan</option>
                            <option value="Rusak Berat">Rusak Berat</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Keterangan</label>
                    <textarea name="keterangan" rows="3"
                        class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="showAddModal = false"
                        class="px-6 py-2.5 bg-gray-800 text-gray-300 rounded-xl hover:bg-gray-700 transition-all">Batal</button>
                    <button type="submit"
                        class="px-6 py-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-500 shadow-lg shadow-primary-500/30 transition-all">Simpan
                        Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showEditModal" @click="showEditModal = false"
            class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm"></div>
        <div
            class="inline-block w-full max-w-2xl p-8 my-8 overflow-hidden text-left align-middle transition-all transform glass-card rounded-3xl shadow-2xl relative border border-gray-800">
            <h3 class="text-2xl font-bold text-white mb-6">Edit Alsintor</h3>
            <form :action="'{{ url('alsintor') }}/' + formData.id" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <input type="hidden" name="satker_id" :value="formData.satker_id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Satker</label>
                        @if(auth()->user()->satker_id)
                            <div
                                class="w-full bg-gray-800/30 border border-gray-700/50 text-gray-400 rounded-xl px-4 py-3 cursor-not-allowed text-sm">
                                {{ auth()->user()->satker->nama_satker ?? 'Satker Anda' }}
                            </div>
                        @else
                            <select name="satker_id" x-model="formData.satker_id" required
                                class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                                @foreach($satkers as $satker)
                                    <option value="{{ $satker->id }}">{{ $satker->nama_satker }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Jenis Barang</label>
                        <input type="text" name="jenis_barang" x-model="formData.jenis_barang" required
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">NUP</label>
                        <input type="text" name="nup" x-model="formData.nup"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Kondisi</label>
                        <select name="kondisi" x-model="formData.kondisi"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                            <option value="Baik">Baik</option>
                            <option value="Rusak Ringan">Rusak Ringan</option>
                            <option value="Rusak Berat">Rusak Berat</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Keterangan</label>
                    <textarea name="keterangan" x-model="formData.keterangan" rows="3"
                        class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="showEditModal = false"
                        class="px-6 py-2.5 bg-gray-800 text-gray-300 rounded-xl hover:bg-gray-700 transition-all">Batal</button>
                    <button type="submit"
                        class="px-6 py-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-500 shadow-lg shadow-primary-500/30 transition-all">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div x-show="showImportModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showImportModal" @click="showImportModal = false"
            class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm"></div>
        <div
            class="inline-block w-full max-w-md p-8 my-8 overflow-hidden text-left align-middle transition-all transform glass-card rounded-3xl shadow-2xl relative border border-gray-800">
            <h3 class="text-2xl font-bold text-white mb-6">Import Alsintor</h3>

            <div class="bg-primary-500/10 border border-primary-500/20 rounded-xl p-4 mb-6">
                <h4 class="text-xs font-bold text-primary-400 uppercase tracking-widest mb-2 flex items-center">
                    <i class="ph ph-info mr-2"></i> Panduan Kolom Excel
                </h4>
                <ul class="text-[11px] text-gray-400 space-y-1">
                    <li><span class="text-gray-300 font-medium">Wajib:</span> <code
                            class="text-primary-300">jenis_barang</code>, <code class="text-primary-300">nup</code></li>
                    <li><span class="text-gray-300 font-medium">Opsional:</span> <code
                            class="text-gray-300">kondisi</code>, <code class="text-gray-300">keterangan</code></li>
                </ul>
                <a href="{{ route('alsintor.download-template') }}"
                    class="inline-flex items-center mt-3 text-[11px] font-bold text-primary-400 hover:text-primary-300 transition-colors">
                    <i class="ph ph-file-arrow-down mr-1.5 text-sm"></i> Download Format Excel
                </a>
            </div>

            <form @submit.prevent="handleImport(event)" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Pilih File Excel</label>
                    <input type="file" name="file" required
                        class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500 file:bg-primary-600 file:text-white file:border-0 file:rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Satker (Opsional)</label>
                    @if(auth()->user()->satker_id)
                        <div
                            class="w-full bg-gray-800/30 border border-gray-700/50 text-gray-400 rounded-xl px-4 py-3 cursor-not-allowed">
                            {{ auth()->user()->satker->nama_satker ?? 'Satker Anda' }}
                            <input type="hidden" name="satker_id" value="{{ auth()->user()->satker_id }}">
                        </div>
                    @else
                        <select name="satker_id"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                            <option value="">Deteksi dari file</option>
                            @foreach($satkers as $satker)
                                <option value="{{ $satker->id }}">{{ $satker->nama_satker }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="showImportModal = false"
                        class="px-6 py-2.5 bg-gray-800 text-gray-300 rounded-xl hover:bg-gray-700 transition-all">Batal</button>
                    <button type="submit" :disabled="loading"
                        class="px-6 py-2.5 bg-green-600 text-white rounded-xl hover:bg-green-500 transition-all shadow-lg shadow-green-500/20 disabled:opacity-50">
                        <span x-show="!loading">Import Data</span>
                        <span x-show="loading">Memproses...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Conflict Resolution Modal -->
<div x-show="showConflictModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showConflictModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm"
            @click="showConflictModal = false"></div>

        <div x-show="showConflictModal" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            class="inline-block w-full max-w-4xl p-8 my-8 overflow-hidden text-left align-middle transition-all transform glass-card rounded-3xl shadow-2xl relative">

            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-white">Deteksi Data Ganda (Alsintor)</h3>
                <button @click="showConflictModal = false" class="text-gray-400 hover:text-white"><i
                        class="ph ph-x text-2xl"></i></button>
            </div>

            <p class="text-gray-400 mb-6 text-sm">Beberapa data yang Anda unggah sudah ada di sistem (NUP sama). Silakan
                pilih perlakuan untuk masing-masing data.</p>

            <div class="overflow-x-auto max-h-96 mb-6 custom-scrollbar border border-white/5 rounded-2xl">
                <table class="w-full text-left text-sm">
                    <thead class="bg-white/5 text-gray-400 uppercase text-[10px] tracking-widest">
                        <tr>
                            <th class="px-4 py-3">NUP</th>
                            <th class="px-4 py-3">Data Lama</th>
                            <th class="px-4 py-3">Data Terbaru (Excel)</th>
                            <th class="px-4 py-3 w-48">Keputusuan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <template x-for="(conflict, index) in conflicts" :key="index">
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-white" x-text="conflict.new.nup"></div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-xs text-gray-400" x-text="conflict.existing.jenis_barang"></div>
                                    <div class="text-[10px] text-gray-500" x-text="conflict.existing.kondisi"></div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-xs text-primary-400" x-text="conflict.new.jenis_barang"></div>
                                    <div class="text-[10px] text-primary-500/80" x-text="conflict.new.kondisi"></div>
                                </td>
                                <td class="px-4 py-3">
                                    <select x-model="conflict.decision"
                                        class="bg-gray-800 border-white/10 text-white rounded-lg px-2 py-1 w-full text-xs">
                                        <option value="overwrite">Pakai Terbaru</option>
                                        <option value="keep">Keep Data Lama</option>
                                    </select>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" @click="showConflictModal = false"
                    class="px-6 py-2.5 bg-gray-800 text-gray-300 rounded-xl hover:bg-gray-700 transition-all">Batal</button>
                <button type="button" @click="submitResolvedConflicts" :disabled="loading"
                    class="px-6 py-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-500 shadow-lg shadow-primary-500/30 transition-all disabled:opacity-50">
                    <span x-show="!loading">Proses Import</span>
                    <span x-show="loading">Memproses...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Transfer Modal -->
<div x-show="showTransferModal"
    class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-[#0f172a]/80 backdrop-blur-sm"
    @click="showTransferModal = false" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0" x-cloak>

    <div class="glass-card w-full max-w-md rounded-3xl overflow-hidden shadow-2xl border border-gray-700/50" @click.stop
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 transform scale-100 translate-y-0 text-decoration-none">

        <div class="px-8 py-6 border-b border-gray-800 flex justify-between items-center bg-gray-800/20">
            <div class="flex items-center">
                <div class="p-2 bg-primary-500/10 rounded-xl mr-4 text-decoration-none">
                    <i class="ph ph-paper-plane-tilt text-2xl text-primary-500"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-100">Mutasi Alsintor</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Kirim alsintor ke satker lain</p>
                </div>
            </div>
            <button @click="showTransferModal = false" class="text-gray-500 hover:text-white transition-colors">
                <i class="ph ph-x text-2xl"></i>
            </button>
        </div>

        <form :action="'{{ url('alsintor') }}/' + formData.id + '/transfer'" method="POST" class="p-8 space-y-5">
            @csrf
            <div class="bg-gray-800/30 border border-gray-700/50 rounded-xl p-4 mb-2">
                <p class="text-xs text-gray-400 mb-1">Barang:</p>
                <p class="text-lg font-bold text-white text-decoration-none" x-text="formData.jenis_barang"></p>
                <p class="text-xs text-primary-400 mt-1">NUP: <span x-text="formData.nup || '-'"></span></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2 text-decoration-none">Satker Tujuan</label>
                <select name="satker_id" required
                    class="w-full bg-gray-900/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    <option value="">-- Pilih Satker --</option>
                    @foreach($satkers as $satker)
                        <template x-if="formData.satker_id != {{ $satker->id }}">
                            <option value="{{ $satker->id }}">{{ $satker->nama_satker }}</option>
                        </template>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-800">
                <button type="button" @click="showTransferModal = false"
                    class="px-6 py-2.5 text-sm font-semibold text-gray-400 hover:text-white transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-8 py-2.5 bg-primary-600 hover:bg-primary-500 text-white rounded-xl font-bold transition-all shadow-lg shadow-primary-500/20 text-decoration-none">
                    Kirim Sekarang
                </button>
            </div>
        </form>
    </div>
</div>