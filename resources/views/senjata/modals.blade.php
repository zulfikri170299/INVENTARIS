<!-- Add Modal -->
<div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showAddModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm"
            @click="showAddModal = false"></div>

        <div x-show="showAddModal" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            class="inline-block w-full max-w-2xl p-8 my-8 overflow-hidden text-left align-middle transition-all transform glass-card rounded-3xl shadow-2xl relative">

            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-white">Tambah Senjata</h3>
                <button @click="showAddModal = false" class="text-gray-400 hover:text-white"><i
                        class="ph ph-x text-2xl"></i></button>
            </div>

            <form action="{{ route('senjata.store') }}" method="POST" class="space-y-5">
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
                        <label class="block text-sm font-medium text-gray-400 mb-2">Jenis Senpi</label>
                        <input type="text" name="jenis_senpi" required placeholder="Contoh: SS2-V1"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Laras</label>
                        <select name="laras" required
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                            <option value="Panjang">Panjang</option>
                            <option value="Pendek">Pendek</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">NUP</label>
                        <input type="text" name="nup" placeholder="Contoh: SN-123..."
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">No. Senpi</label>
                        <input type="text" name="no_senpi" placeholder="Contoh: AB-123456"
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
                        <label class="block text-sm font-medium text-gray-400 mb-2">Kondisi</label>
                        <select name="kondisi"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                            <option value="Baik">Baik</option>
                            <option value="Rusak Ringan">Rusak Ringan</option>
                            <option value="Rusak Berat">Rusak Berat</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Status Penyimpanan</label>
                        <select name="status_penyimpanan" x-model="addStatus"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                            <option value="Gudang">Gudang</option>
                            <option value="Personel">Pegang Personel</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Masa Berlaku SIMSA</label>
                        <input type="date" name="masa_berlaku_simsa"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                </div>

                <!-- Amunisi Kondisional (Personel) -->
                <div x-show="addStatus === 'Personel'" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2" x-cloak x-data="{ 
                        addAmunisiLainnya: false, 
                        currentStock: 0,
                        stocks: {{ $availableAmunisi->pluck('total_stok', 'jenis_amunisi')->toJson() }}
                    }">
                    <div class="rounded-2xl border border-orange-500/30 bg-orange-500/5 p-4 mt-1">
                        <p class="text-xs font-bold text-orange-400 uppercase tracking-widest mb-3 flex items-center">
                            <i class="ph ph-bullets mr-2 text-sm"></i> Data Amunisi Dibawa Personel
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Jenis Amunisi</label>
                                @if($availableAmunisi->isNotEmpty())
                                    <div x-show="!addAmunisiLainnya">
                                        <select name="jenis_amunisi_dibawa" :disabled="addAmunisiLainnya" @change="
                                                                        if($event.target.value === '__lainnya__') { 
                                                                            addAmunisiLainnya = true; 
                                                                            $event.target.value = ''; 
                                                                            currentStock = 0;
                                                                        } else {
                                                                            currentStock = stocks[$event.target.value] || 0;
                                                                        }
                                                                    "
                                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-orange-500 focus:border-orange-500">
                                            <option value="">-- Pilih Jenis Amunisi --</option>
                                            @foreach($availableAmunisi as $amunisi)
                                                <option value="{{ $amunisi->jenis_amunisi }}">
                                                    {{ $amunisi->jenis_amunisi }} (Stok: {{ $amunisi->total_stok }})
                                                </option>
                                            @endforeach
                                            <option value="__lainnya__">✏️ Ketik manual...</option>
                                        </select>
                                    </div>
                                    <div x-show="addAmunisiLainnya" class="flex gap-2">
                                        <input type="text" name="jenis_amunisi_dibawa" :disabled="!addAmunisiLainnya"
                                            placeholder="Ketik jenis amunisi..."
                                            class="flex-1 bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-orange-500 focus:border-orange-500">
                                        <button type="button" @click="addAmunisiLainnya = false; currentStock = 0;"
                                            class="px-3 py-3 bg-gray-700 hover:bg-gray-600 text-gray-400 rounded-xl transition-colors"
                                            title="Kembali ke daftar">
                                            <i class="ph ph-arrow-left text-sm"></i>
                                        </button>
                                    </div>
                                @else
                                    <input type="text" name="jenis_amunisi_dibawa"
                                        placeholder="Belum ada data amunisi, ketik manual..."
                                        class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-orange-500 focus:border-orange-500">
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">
                                    Jumlah Amunisi
                                    <template x-if="currentStock > 0 && !addAmunisiLainnya">
                                        <span class="text-[10px] text-orange-400 ml-1">(Maks: <span
                                                x-text="currentStock"></span>)</span>
                                    </template>
                                </label>
                                <input type="number" name="jumlah_amunisi_dibawa" min="0"
                                    :max="addAmunisiLainnya ? '' : currentStock"
                                    x-on:input="if(!addAmunisiLainnya && currentStock > 0 && $event.target.value > currentStock) $event.target.value = currentStock"
                                    value="0" placeholder="0"
                                    class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-orange-500 focus:border-orange-500 font-mono">
                                <template
                                    x-if="!addAmunisiLainnya && currentStock === 0 && availableAmunisi?.length > 0">
                                    <p class="text-[10px] text-red-400 mt-1 font-medium">Pilih jenis amunisi terlebih
                                        dahulu</p>
                                </template>
                            </div>
                        </div>
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
        <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm"
            @click="showEditModal = false"></div>

        <div x-show="showEditModal" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            class="inline-block w-full max-w-2xl p-8 my-8 overflow-hidden text-left align-middle transition-all transform glass-card rounded-3xl shadow-2xl relative">

            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-white">Edit Senjata</h3>
                <button @click="showEditModal = false" class="text-gray-400 hover:text-white"><i
                        class="ph ph-x text-2xl"></i></button>
            </div>

            <form :action="'{{ url('senjata') }}/' + formData.id" method="POST" class="space-y-5">
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
                        <label class="block text-sm font-medium text-gray-400 mb-2">Jenis Senpi</label>
                        <input type="text" name="jenis_senpi" x-model="formData.jenis_senpi" required
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Laras</label>
                        <select name="laras" x-model="formData.laras" required
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                            <option value="Panjang">Panjang</option>
                            <option value="Pendek">Pendek</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">NUP</label>
                        <input type="text" name="nup" x-model="formData.nup"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">No. Senpi</label>
                        <input type="text" name="no_senpi" x-model="formData.no_senpi"
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
                        <label class="block text-sm font-medium text-gray-400 mb-2">Status Penyimpanan</label>
                        <select name="status_penyimpanan" x-model="formData.status_penyimpanan"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                            <option value="Gudang">Gudang</option>
                            <option value="Personel">Pegang Personel</option>
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
                        <label class="block text-sm font-medium text-gray-400 mb-2">Masa Berlaku SIMSA</label>
                        <input type="date" name="masa_berlaku_simsa" x-model="formData.masa_berlaku_simsa"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                </div>

                <!-- Amunisi Kondisional (Personel) -->
                <div x-show="formData.status_penyimpanan === 'Personel'"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2" x-cloak x-data="{ 
                        editAmunisiLainnya: false, 
                        currentStock: 0,
                        stocks: {{ $availableAmunisi->pluck('total_stok', 'jenis_amunisi')->toJson() }}
                    }" x-init="
                        $watch('formData.jenis_amunisi_dibawa', (val) => {
                            currentStock = stocks[val] || 0;
                            if (selectedItem && val === selectedItem.jenis_amunisi_dibawa) {
                                currentStock += parseInt(selectedItem.jumlah_amunisi_dibawa || 0);
                            }
                        });
                        // Initial calculation
                        currentStock = stocks[formData.jenis_amunisi_dibawa] || 0;
                        if (selectedItem && formData.jenis_amunisi_dibawa === selectedItem.jenis_amunisi_dibawa) {
                            currentStock += parseInt(selectedItem.jumlah_amunisi_dibawa || 0);
                        }
                    ">
                    <div class="rounded-2xl border border-orange-500/30 bg-orange-500/5 p-4 mt-1">
                        <p class="text-xs font-bold text-orange-400 uppercase tracking-widest mb-3 flex items-center">
                            <i class="ph ph-bullets mr-2 text-sm"></i> Data Amunisi Dibawa Personel
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Jenis Amunisi</label>
                                @if($availableAmunisi->isNotEmpty())
                                    <div x-show="!editAmunisiLainnya">
                                        <select name="jenis_amunisi_dibawa" :disabled="editAmunisiLainnya" @change="
                                                                        if($event.target.value === '__lainnya__') { 
                                                                            editAmunisiLainnya = true; 
                                                                            $event.target.value = ''; 
                                                                            currentStock = 0;
                                                                        } else { 
                                                                            formData.jenis_amunisi_dibawa = $event.target.value; 
                                                                            currentStock = stocks[$event.target.value] || 0;
                                                                        }
                                                                    "
                                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-orange-500 focus:border-orange-500">
                                            <option value="">-- Pilih Jenis Amunisi --</option>
                                            @foreach($availableAmunisi as $amunisi)
                                                <option value="{{ $amunisi->jenis_amunisi }}"
                                                    :selected="formData.jenis_amunisi_dibawa === '{{ $amunisi->jenis_amunisi }}'">
                                                    {{ $amunisi->jenis_amunisi }} (Stok: {{ $amunisi->total_stok }})
                                                </option>
                                            @endforeach
                                            <option value="__lainnya__">✏️ Ketik manual...</option>
                                        </select>
                                    </div>
                                    <div x-show="editAmunisiLainnya" class="flex gap-2">
                                        <input type="text" name="jenis_amunisi_dibawa" :disabled="!editAmunisiLainnya"
                                            x-model="formData.jenis_amunisi_dibawa" placeholder="Ketik jenis amunisi..."
                                            class="flex-1 bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-orange-500 focus:border-orange-500">
                                        <button type="button" @click="editAmunisiLainnya = false; currentStock = 0;"
                                            class="px-3 py-3 bg-gray-700 hover:bg-gray-600 text-gray-400 rounded-xl transition-colors"
                                            title="Kembali ke daftar">
                                            <i class="ph ph-arrow-left text-sm"></i>
                                        </button>
                                    </div>
                                @else
                                    <input type="text" name="jenis_amunisi_dibawa" x-model="formData.jenis_amunisi_dibawa"
                                        placeholder="Belum ada data amunisi, ketik manual..."
                                        class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-orange-500 focus:border-orange-500">
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">
                                    Jumlah Amunisi
                                    <template x-if="currentStock > 0 && !editAmunisiLainnya">
                                        <span class="text-[10px] text-orange-400 ml-1">(Maks: <span
                                                x-text="currentStock"></span>)</span>
                                    </template>
                                </label>
                                <input type="number" name="jumlah_amunisi_dibawa"
                                    x-model="formData.jumlah_amunisi_dibawa" min="0"
                                    :max="editAmunisiLainnya ? '' : currentStock"
                                    x-on:input="if(!editAmunisiLainnya && currentStock > 0 && $event.target.value > currentStock) $event.target.value = currentStock"
                                    placeholder="0"
                                    class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-orange-500 focus:border-orange-500 font-mono">
                                <template
                                    x-if="!editAmunisiLainnya && currentStock === 0 && availableAmunisi?.length > 0">
                                    <p class="text-[10px] text-red-400 mt-1 font-medium">Pilih jenis amunisi terlebih
                                        dahulu</p>
                                </template>
                            </div>
                        </div>
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
        <div x-show="showImportModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm"
            @click="showImportModal = false"></div>

        <div x-show="showImportModal" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            class="inline-block w-full max-w-md p-8 my-8 overflow-hidden text-left align-middle transition-all transform glass-card rounded-3xl shadow-2xl relative">

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
                            class="text-primary-300">jenis_senpi</code>, <code class="text-primary-300">laras</code>
                        (Panjang/Pendek), <code class="text-primary-300">nup</code>, <code
                            class="text-primary-300">no_senpi</code>, <code
                            class="text-primary-300">penanggung_jawab</code>, <code
                            class="text-primary-300">pangkat_nrp</code>,
                        <code class="text-primary-300">status_penyimpanan</code>
                    </li>
                    <li><span class="text-gray-300 font-medium">Opsional:</span> <code
                            class="text-gray-300">kondisi</code>, <code class="text-gray-300">masa_berlaku_simsa</code>,
                        <code class="text-gray-300">keterangan</code>
                    </li>
                </ul>
                <a href="{{ route('senjata.download-template') }}"
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
                        class="px-6 py-2.5 bg-green-600 text-white rounded-xl hover:bg-green-500 shadow-lg shadow-green-500/30 transition-all disabled:opacity-50">
                        <span x-show="!loading">Mulai Import</span>
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
                            <th class="px-4 py-3">No. Senpi/NUP</th>
                            <th class="px-4 py-3">Data Lama</th>
                            <th class="px-4 py-3">Data Terbaru (Excel)</th>
                            <th class="px-4 py-3 w-48">Keputusuan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <template x-for="(conflict, index) in conflicts" :key="index">
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-white" x-text="conflict.new.no_senpi"></div>
                                    <div class="text-[10px] text-gray-500 font-mono" x-text="conflict.new.nup"></div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-xs text-gray-400" x-text="conflict.existing.jenis_senpi"></div>
                                    <div class="text-[10px] text-gray-500" x-text="conflict.existing.penanggung_jawab">
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-xs text-primary-400" x-text="conflict.new.jenis_senpi"></div>
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

<!-- Return Amunisi Modal -->
<div x-show="showReturnModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showReturnModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm"
            @click="showReturnModal = false"></div>

        <div x-show="showReturnModal" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            class="inline-block w-full max-w-md p-8 my-8 overflow-hidden text-left align-middle transition-all transform glass-card rounded-3xl shadow-2xl relative">

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-2xl font-bold text-white">Kembalikan Amunisi</h3>
                    <p class="text-xs text-gray-400 mt-1"
                        x-text="formData.jenis_senpi + ' (' + formData.no_senpi + ')'"></p>
                </div>
                <button @click="showReturnModal = false" class="text-gray-400 hover:text-white"><i
                        class="ph ph-x text-2xl"></i></button>
            </div>

            <div class="bg-orange-500/10 border border-orange-500/20 rounded-xl p-4 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-orange-400 uppercase tracking-widest mb-1">Amunisi di
                            Pegang</p>
                        <p class="text-sm text-white font-bold" x-text="formData.jenis_amunisi_dibawa || '-'"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold text-orange-400 uppercase tracking-widest mb-1">Jumlah</p>
                        <p class="text-sm text-white font-bold"
                            x-text="(formData.jumlah_amunisi_dibawa || 0) + ' butir'"></p>
                    </div>
                </div>
            </div>

            <form :action="'{{ url('senjata') }}/' + formData.id + '/return-amunisi'" method="POST" class="space-y-5"
                x-data="{ aksi: 'kembali' }">
                @csrf
                <div class="grid grid-cols-2 gap-3 mb-2">
                    <label class="cursor-pointer group">
                        <input type="radio" name="aksi" value="kembali" x-model="aksi" class="hidden peer">
                        <div
                            class="p-3 rounded-xl border border-gray-700 bg-gray-800/30 text-center transition-all peer-checked:border-primary-500 peer-checked:bg-primary-500/10 group-hover:bg-gray-700/50">
                            <i class="ph ph-hand-back-point text-xl mb-1 block"
                                :class="aksi === 'kembali' ? 'text-primary-400' : 'text-gray-500'"></i>
                            <span class="text-[10px] font-bold uppercase tracking-wider block"
                                :class="aksi === 'kembali' ? 'text-primary-300' : 'text-gray-500'">Dikembalikan</span>
                        </div>
                    </label>
                    <label class="cursor-pointer group">
                        <input type="radio" name="aksi" value="pakai" x-model="aksi" class="hidden peer">
                        <div
                            class="p-3 rounded-xl border border-gray-700 bg-gray-800/30 text-center transition-all peer-checked:border-red-500 peer-checked:bg-red-500/10 group-hover:bg-gray-700/50">
                            <i class="ph ph-fire text-xl mb-1 block"
                                :class="aksi === 'pakai' ? 'text-red-400' : 'text-gray-500'"></i>
                            <span class="text-[10px] font-bold uppercase tracking-wider block"
                                :class="aksi === 'pakai' ? 'text-red-300' : 'text-gray-500'">Dipakai</span>
                        </div>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Jumlah <span
                            x-text="aksi === 'kembali' ? 'dikembalikan' : 'dipakai'"></span></label>
                    <input type="number" name="jumlah_kembali" required min="1" :max="formData.jumlah_amunisi_dibawa"
                        x-model="formData.jumlah_kembali_input"
                        class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500 focus:border-primary-500 font-mono text-center text-xl"
                        :class="aksi === 'pakai' ? 'focus:ring-red-500 focus:border-red-500' : ''">
                    <p class="text-[10px] text-gray-500 mt-2">Maksimal: <span
                            x-text="formData.jumlah_amunisi_dibawa"></span> butir</p>
                </div>

                <div x-show="aksi === 'pakai'" x-transition class="mb-4">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Keterangan Penggunaan (Wajib)</label>
                    <textarea name="keterangan_pakai" :required="aksi === 'pakai'"
                        placeholder="Contoh: Latihan menembak rutin, Operasi X, dsb."
                        class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-red-500 focus:border-red-500 text-sm h-24 resize-none"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="showReturnModal = false"
                        class="px-6 py-2.5 bg-gray-800 text-gray-300 rounded-xl hover:bg-gray-700 transition-all">Batal</button>
                    <button type="submit" class="px-6 py-2.5 text-white rounded-xl shadow-lg transition-all"
                        :class="aksi === 'kembali' ? 'bg-primary-600 hover:bg-primary-500 shadow-primary-500/30' : 'bg-red-600 hover:bg-red-500 shadow-red-500/30'">
                        <span x-text="aksi === 'kembali' ? 'Kembalikan ke Gudang' : 'Konfirmasi Pakai Amunisi'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>