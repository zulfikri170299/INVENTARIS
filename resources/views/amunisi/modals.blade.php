<!-- Modals Container -->
<div x-cloak>
    <!-- Add Amunisi Modal -->
    <div x-show="showAddModal"
        class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-[#0f172a]/80 backdrop-blur-sm"
        @click="showAddModal = false" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        <div class="glass-card w-full max-w-2xl rounded-3xl overflow-hidden shadow-2xl border border-gray-700/50"
            @click.stop x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 transform scale-100 translate-y-0">

            <div class="px-8 py-6 border-b border-gray-800 flex justify-between items-center bg-gray-800/20">
                <div class="flex items-center">
                    <div class="p-2 bg-primary-500/10 rounded-xl mr-4">
                        <i class="ph ph-plus-circle text-2xl text-primary-500"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-100">Tambah Amunisi</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Lengkapi informasi amunisi di bawah ini</p>
                    </div>
                </div>
                <button @click="showAddModal = false" class="text-gray-500 hover:text-white transition-colors">
                    <i class="ph ph-x text-2xl"></i>
                </button>
            </div>

            <form action="{{ route('amunisi.store') }}" method="POST" class="p-8">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if(!auth()->user()->satker_id || in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2']))
                        <div class="space-y-2 col-span-2">
                            <label class="block text-sm font-medium text-gray-400">Pilih Satker</label>
                            <select name="satker_id" required
                                class="w-full bg-gray-900/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500 focus:border-primary-500 transition-all">
                                <option value="">Pilih Satker</option>
                                @foreach($satkers as $satker)
                                    <option value="{{ $satker->id }}">{{ $satker->nama_satker }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="satker_id" value="{{ auth()->user()->satker_id }}">
                    @endif

                    <div class="space-y-2 col-span-2 md:col-span-1">
                        <label class="block text-sm font-medium text-gray-400">Jenis Amunisi</label>
                        <input type="text" name="jenis_amunisi" required placeholder="Contoh: 9mm, 5.56mm"
                            class="w-full bg-gray-900/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500 focus:border-primary-500 transition-all">
                    </div>

                    <div class="space-y-2 col-span-2 md:col-span-1">
                        <label class="block text-sm font-medium text-gray-400">Jumlah</label>
                        <input type="number" name="jumlah" required min="0" value="0"
                            class="w-full bg-gray-900/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500 focus:border-primary-500 transition-all font-mono">
                    </div>



                    <div class="space-y-2 col-span-2">
                        <label class="block text-sm font-medium text-gray-400">Keterangan</label>
                        <textarea name="keterangan" rows="3" placeholder="Tambahkan catatan tambahan jika diperlukan..."
                            class="w-full bg-gray-900/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500 focus:border-primary-500 transition-all resize-none"></textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-800">
                    <button type="button" @click="showAddModal = false"
                        class="px-6 py-2.5 text-sm font-semibold text-gray-400 hover:text-white transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-8 py-2.5 bg-primary-600 hover:bg-primary-500 text-white rounded-xl font-bold transition-all shadow-lg shadow-primary-500/20">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Amunisi Modal -->
    <div x-show="showEditModal"
        class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-[#0f172a]/80 backdrop-blur-sm"
        @click="showEditModal = false" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        <div class="glass-card w-full max-w-2xl rounded-3xl overflow-hidden shadow-2xl border border-gray-700/50"
            @click.stop x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 transform scale-100 translate-y-0">

            <div class="px-8 py-6 border-b border-gray-800 flex justify-between items-center bg-gray-800/20">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-500/10 rounded-xl mr-4">
                        <i class="ph ph-note-pencil text-2xl text-yellow-500"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-100">Edit Amunisi</h3>
                        <p class="text-xs text-gray-500 mt-0.5" x-text="'Perbarui data ' + formData.jenis_amunisi"></p>
                    </div>
                </div>
                <button @click="showEditModal = false" class="text-gray-500 hover:text-white transition-colors">
                    <i class="ph ph-x text-2xl"></i>
                </button>
            </div>

            <form :action="'/amunisi/' + formData.id" method="POST" class="p-8">
                @csrf
                @method('PATCH')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if(!auth()->user()->satker_id || in_array(auth()->user()->role, ['Super Admin', 'Super Admin 2']))
                        <div class="space-y-2 col-span-2">
                            <label class="block text-sm font-medium text-gray-400">Pilih Satker</label>
                            <select name="satker_id" required x-model="formData.satker_id"
                                class="w-full bg-gray-900/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500 focus:border-primary-500 transition-all">
                                @foreach($satkers as $satker)
                                    <option value="{{ $satker->id }}">{{ $satker->nama_satker }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="satker_id" x-model="formData.satker_id">
                    @endif

                    <div class="space-y-2 col-span-2 md:col-span-1">
                        <label class="block text-sm font-medium text-gray-400">Jenis Amunisi</label>
                        <input type="text" name="jenis_amunisi" required x-model="formData.jenis_amunisi"
                            class="w-full bg-gray-900/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500 focus:border-primary-500 transition-all">
                    </div>

                    <div class="space-y-2 col-span-2 md:col-span-1">
                        <label class="block text-sm font-medium text-gray-400">Jumlah</label>
                        <input type="number" name="jumlah" required min="0" x-model="formData.jumlah"
                            class="w-full bg-gray-900/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500 focus:border-primary-500 transition-all font-mono">
                    </div>



                    <div class="space-y-2 col-span-2">
                        <label class="block text-sm font-medium text-gray-400">Keterangan</label>
                        <textarea name="keterangan" rows="3" x-model="formData.keterangan"
                            class="w-full bg-gray-900/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500 focus:border-primary-500 transition-all resize-none"></textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-800">
                    <button type="button" @click="showEditModal = false"
                        class="px-6 py-2.5 text-sm font-semibold text-gray-400 hover:text-white transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-8 py-2.5 bg-yellow-600 hover:bg-yellow-500 text-white rounded-xl font-bold transition-all shadow-lg shadow-yellow-500/20">
                        Perbarui Data
                    </button>
                </div>
            </form>
        </div>
    </div>





    <!-- Import Modal -->
    <div x-show="showImportModal"
        class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-[#0f172a]/80 backdrop-blur-sm"
        @click="showImportModal = false" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        <div class="glass-card w-full max-w-md p-8 my-8 overflow-hidden text-left align-middle transition-all transform rounded-3xl shadow-2xl relative border border-gray-700/50"
            @click.stop>

            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-white">Import dari Excel</h3>
                <button @click="showImportModal = false" class="text-gray-400 hover:text-white"><i
                        class="ph ph-x text-2xl"></i></button>
            </div>

            <div class="bg-primary-500/10 border border-primary-500/20 rounded-xl p-4 mb-6">
                <h4 class="text-xs font-bold text-primary-400 uppercase tracking-widest mb-2 flex items-center">
                    <i class="ph ph-info mr-2"></i> Panduan Kolom Excel
                </h4>
                <ul class="text-[11px] text-gray-400 space-y-1">
                    <li><span class="text-gray-300 font-medium">Wajib:</span> <code
                            class="text-primary-300">jenis_amunisi</code>, <code class="text-primary-300">jumlah</code>
                    </li>
                    <li><span class="text-gray-300 font-medium">Status:</span> <code
                            class="text-primary-300">Gudang</code> / <code class="text-primary-300">Personel</code></li>
                </ul>
                <a href="{{ route('amunisi.download-template') }}"
                    class="inline-flex items-center mt-3 text-[11px] font-bold text-primary-400 hover:text-primary-300 transition-colors">
                    <i class="ph ph-file-arrow-down mr-1.5 text-sm"></i> Download Format Excel
                </a>
            </div>

            <form @submit.prevent="handleImport(event)" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Pilih File Excel (.xlsx, .csv)</label>
                    <input type="file" name="file" required
                        class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-500 file:text-white hover:file:bg-primary-600">
                </div>

                @if(!auth()->user()->satker_id)
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Pilih Satker</label>
                        <select name="satker_id" required
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                            @foreach($satkers as $satker)
                                <option value="{{ $satker->id }}">{{ $satker->nama_satker }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="showImportModal = false"
                        class="px-6 py-2.5 bg-gray-800 text-gray-300 rounded-xl hover:bg-gray-700 transition-all">Batal</button>
                    <button type="submit" :disabled="loading"
                        class="px-6 py-2.5 bg-green-600 text-white rounded-xl hover:bg-green-500 shadow-lg shadow-green-500/30 transition-all disabled:opacity-50">
                        <span x-show="!loading">Mulai Import</span>
                        <span x-show="loading">Memproses...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Conflict Resolution Modal -->
    <div x-show="showConflictModal"
        class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-[#0f172a]/90 backdrop-blur-md"
        @click="showConflictModal = false" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        <div class="glass-card w-full max-w-4xl p-8 my-8 overflow-hidden text-left align-middle transition-all transform rounded-3xl shadow-2xl relative border border-gray-700/50"
            @click.stop>

            <div class="flex items-center justify-between mb-6 text-decoration-none">
                <h3 class="text-2xl font-bold text-white">Deteksi Data Ganda</h3>
                <button @click="showConflictModal = false" class="text-gray-400 hover:text-white"><i
                        class="ph ph-x text-2xl"></i></button>
            </div>

            <p class="text-gray-400 mb-6 text-sm">Beberapa data amunisi sudah ada di sistem. Silakan pilih perlakuan
                untuk masing-masing data.</p>

            <div class="overflow-x-auto max-h-96 mb-6 custom-scrollbar border border-white/5 rounded-2xl">
                <table class="w-full text-left text-sm">
                    <thead
                        class="bg-white/5 text-gray-400 uppercase text-[10px] tracking-widest text-decoration-none text-decoration-none">
                        <tr>
                            <th class="px-4 py-3">Jenis Amunisi & Status</th>
                            <th class="px-4 py-3">Jumlah Lama</th>
                            <th class="px-4 py-3">Jumlah Baru (Excel)</th>
                            <th class="px-4 py-3 w-48">Keputusuan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <template x-for="(conflict, index) in conflicts" :key="index">
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-white" x-text="conflict.new.jenis_amunisi"></div>
                                    <div class="text-[10px] text-gray-500 font-bold uppercase"
                                        x-text="conflict.new.status_penyimpanan"></div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-400" x-text="conflict.existing.jumlah"></div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-primary-400 font-bold" x-text="conflict.new.jumlah"></div>
                                </td>
                                <td class="px-4 py-3">
                                    <select x-model="conflict.decision"
                                        class="bg-gray-800 border-white/10 text-white rounded-lg px-2 py-1 w-full text-xs">
                                        <option value="overwrite">Update (Ganti Jumlah)</option>
                                        <option value="keep">Keep (Tetap Data Lama)</option>
                                    </select>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
                <div class="flex justify-end gap-3 text-decoration-none">
                <button type="button" @click="showConflictModal = false"
                    class="px-6 py-2.5 bg-gray-800 text-gray-300 rounded-xl hover:bg-gray-700 transition-all">Batal</button>
                <button type="button" @click="submitResolvedConflicts" :disabled="loading"
                        class="px-6 py-2.5 bg-primary-500/10 hover:bg-primary-500/20 text-primary-500 border border-primary-500/30 rounded-xl font-bold transition-all" disabled:opacity-50">
                    <span x-show="!loading">Proses Import</span>
                    <span x-show="loading">Memproses...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Transfer Amunisi Modal -->
<div x-show="showTransferModal"
    class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-[#0f172a]/80 backdrop-blur-sm"
    @click="showTransferModal = false" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0">

    <div class="glass-card w-full max-w-md rounded-3xl overflow-hidden shadow-2xl border border-gray-700/50" @click.stop
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 transform scale-100 translate-y-0">

        <div class="px-8 py-6 border-b border-gray-800 flex justify-between items-center bg-gray-800/20">
            <div class="flex items-center">
                <div class="p-2 bg-primary-500/10 rounded-xl mr-4">
                    <i class="ph ph-paper-plane-tilt text-2xl text-primary-500"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-100">Mutasi Amunisi</h3>
                    <p class="text-xs text-gray-500 mt-0.5" x-text="'Kirim amunisi ke satker lain'"></p>
                </div>
            </div>
            <button @click="showTransferModal = false" class="text-gray-500 hover:text-white transition-colors">
                <i class="ph ph-x text-2xl"></i>
            </button>
        </div>

        <form :action="'/amunisi/' + formData.id + '/transfer'" method="POST" class="p-8 space-y-5">
            @csrf
            <div class="bg-gray-800/30 border border-gray-700/50 rounded-xl p-4 mb-2">
                <p class="text-xs text-gray-400 mb-1">Amunisi:</p>
                <p class="text-lg font-bold text-white" x-text="formData.jenis_amunisi"></p>
                <p class="text-xs text-primary-400 mt-1">Stok saat ini: <span x-text="formData.jumlah"></span> butir</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Satker Tujuan</label>
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

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Jumlah Dikirim</label>
                <input type="number" name="jumlah_transfer" required min="1" :max="formData.jumlah"
                    class="w-full bg-gray-900/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500 font-mono text-xl">
            </div>

            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-800 text-decoration-none">
                <button type="button" @click="showTransferModal = false"
                    class="px-6 py-2.5 text-sm font-semibold text-gray-400 hover:text-white transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-8 py-2.5 bg-primary-600 hover:bg-primary-500 text-white rounded-xl font-bold transition-all shadow-lg shadow-primary-500/20">
                    Kirim Sekarang
                </button>
            </div>
        </form>
    </div>
</div>
</div>