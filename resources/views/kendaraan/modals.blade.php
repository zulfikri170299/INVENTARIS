<!-- Add Modal -->
<div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showAddModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm"
            @click="showAddModal = false"></div>

        <div x-show="showAddModal" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            class="inline-block w-full max-w-2xl p-8 my-8 overflow-hidden text-left align-middle transition-all transform glass-card rounded-3xl shadow-2xl relative border border-gray-800">

            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-white">Tambah Kendaraan</h3>
                <button @click="showAddModal = false" class="text-gray-400 hover:text-white"><i
                        class="ph ph-x text-2xl"></i></button>
            </div>

            <form action="{{ route('kendaraan.store') }}" method="POST" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
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
                        <label class="block text-sm font-medium text-gray-400 mb-2">Jenis Kendaraan</label>
                        <input type="text" name="jenis_kendaraan" required placeholder="Contoh: Roda 4 / Sedan"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">NUP</label>
                        <input type="text" name="nup" placeholder="Contoh: 12345..."
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Tahun Pembuatan</label>
                        <input type="text" name="tahun_pembuatan" placeholder="Contoh: 2024"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">No. Rangka</label>
                        <input type="text" name="no_rangka"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">No. Mesin</label>
                        <input type="text" name="no_mesin"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">No. Polisi</label>
                        <input type="text" name="nopol" placeholder="Contoh: B 1234 ABC"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Penanggung Jawab</label>
                        <input type="text" name="penanggung_jawab"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Pangkat/NRP</label>
                        <input type="text" name="nrp"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Jenis Roda</label>
                        <select name="jenis_roda" required
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                            <option value="R2">R2 (Roda 2)</option>
                            <option value="R4" selected>R4 (Roda 4)</option>
                            <option value="R6">R6 (Roda 6)</option>
                            <option value="R8">R8 (Roda 8)</option>
                        </select>
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
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Bahan Bakar</label>
                        <select name="bahan_bakar" required
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                            <option value="Pertalite">Pertalite</option>
                            <option value="Pertamax">Pertamax</option>
                            <option value="Pertamina Dex">Pertamina Dex</option>
                            <option value="Listrik">Listrik</option>
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
            <h3 class="text-2xl font-bold text-white mb-6">Edit Kendaraan</h3>
            <form :action="'{{ url('kendaraan') }}/' + formData.id" method="POST" class="space-y-5">
                @csrf @method('PUT')
                <input type="hidden" name="satker_id" :value="formData.satker_id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
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
                        <label class="block text-sm font-medium text-gray-400 mb-2">Jenis Kendaraan</label>
                        <input type="text" name="jenis_kendaraan" x-model="formData.jenis_kendaraan" required
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">NUP</label>
                        <input type="text" name="nup" x-model="formData.nup"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Tahun Pembuatan</label>
                        <input type="text" name="tahun_pembuatan" x-model="formData.tahun_pembuatan"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">No. Rangka</label>
                        <input type="text" name="no_rangka" x-model="formData.no_rangka"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">No. Mesin</label>
                        <input type="text" name="no_mesin" x-model="formData.no_mesin"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">No. Polisi</label>
                        <input type="text" name="nopol" x-model="formData.nopol"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Penanggung Jawab</label>
                        <input type="text" name="penanggung_jawab" x-model="formData.penanggung_jawab"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Pangkat/NRP</label>
                        <input type="text" name="nrp" x-model="formData.nrp"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Jenis Roda</label>
                        <select name="jenis_roda" x-model="formData.jenis_roda" required
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                            <option value="R2">R2 (Roda 2)</option>
                            <option value="R4">R4 (Roda 4)</option>
                            <option value="R6">R6 (Roda 6)</option>
                            <option value="R8">R8 (Roda 8)</option>
                        </select>
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
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Bahan Bakar</label>
                        <select name="bahan_bakar" x-model="formData.bahan_bakar" required
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                            <option value="Pertalite">Pertalite</option>
                            <option value="Pertamax">Pertamax</option>
                            <option value="Pertamina Dex">Pertamina Dex</option>
                            <option value="Listrik">Listrik</option>
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
            class="inline-block w-full max-w-md p-8 my-8 overflow-hidden text-left align-middle transition-all transform glass-card rounded-3xl shadow-2xl relative">
            <h3 class="text-2xl font-bold text-white mb-6">Import Kendaraan</h3>

            <div class="bg-primary-500/10 border border-primary-500/20 rounded-xl p-4 mb-6">
                <h4 class="text-xs font-bold text-primary-400 uppercase tracking-widest mb-2 flex items-center">
                    <i class="ph ph-info mr-2"></i> Panduan Kolom Excel
                </h4>
                <ul class="text-[11px] text-gray-400 space-y-1">
                    <li><span class="text-gray-300 font-medium">Wajib:</span> <code
                            class="text-primary-300">jenis_kendaraan</code>, <code
                            class="text-primary-300">jenis_roda</code> (R2/R4/R6/R8), <code
                            class="text-primary-300">bahan_bakar</code></li>
                    <li><span class="text-gray-300 font-medium">Opsional:</span> <code class="text-gray-300">nup</code>,
                        <code class="text-gray-300">tahun_pembuatan</code>, <code
                            class="text-gray-300">no_rangka</code>, <code class="text-gray-300">nopol</code>, <code
                            class="text-gray-300">kondisi</code>, <code class="text-gray-300">penanggung_jawab</code>,
                        <code class="text-gray-300">pangkat_nrp</code>, <code class="text-gray-300">keterangan</code>
                    </li>
                </ul>
                <a href="{{ route('kendaraan.download-template') }}"
                    class="inline-flex items-center mt-3 text-[11px] font-bold text-primary-400 hover:text-primary-300 transition-colors">
                    <i class="ph ph-file-arrow-down mr-1.5 text-sm"></i> Download Format Excel
                </a>
            </div>

            <form @submit.prevent="handleImport(event)" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Pilih File Excel</label>
                    <input type="file" name="file" required
                        class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
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
                        class="px-6 py-2.5 bg-green-600 text-white rounded-xl hover:bg-green-500 transition-all disabled:opacity-50">
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
                <h3 class="text-2xl font-bold text-white">Deteksi Data Ganda</h3>
                <button @click="showConflictModal = false" class="text-gray-400 hover:text-white"><i
                        class="ph ph-x text-2xl"></i></button>
            </div>

            <p class="text-gray-400 mb-6 text-sm">Beberapa data yang Anda unggah sudah ada di sistem. Silakan pilih
                perlakuan untuk masing-masing data.</p>

            <div class="overflow-x-auto max-h-96 mb-6 custom-scrollbar border border-white/5 rounded-2xl">
                <table class="w-full text-left text-sm">
                    <thead class="bg-white/5 text-gray-400 uppercase text-[10px] tracking-widest">
                        <tr>
                            <th class="px-4 py-3">Plat Nomor / Rangka</th>
                            <th class="px-4 py-3">Data Lama</th>
                            <th class="px-4 py-3">Data Terbaru (Excel)</th>
                            <th class="px-4 py-3 w-48">Keputusuan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <template x-for="(conflict, index) in conflicts" :key="index">
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-white" x-text="conflict.new.nopol"></div>
                                    <div class="text-[10px] text-gray-500 font-mono"
                                        x-text="'Rangka: ' + conflict.new.no_rangka + ' | Thn: ' + conflict.new.tahun_pembuatan">
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-xs text-gray-400" x-text="conflict.existing.jenis_kendaraan"></div>
                                    <div class="text-[10px] text-gray-500" x-text="conflict.existing.penanggung_jawab">
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-xs text-primary-400" x-text="conflict.new.jenis_kendaraan"></div>
                                    <div class="text-[10px] text-primary-500/80" x-text="conflict.new.penanggung_jawab">
                                    </div>
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