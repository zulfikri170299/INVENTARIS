<!-- Add Modal -->
<div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showAddModal" @click="showAddModal = false"
            class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm"></div>
        <div
            class="inline-block w-full max-w-lg p-8 my-8 overflow-hidden text-left align-middle transition-all transform glass-card rounded-3xl shadow-2xl relative">
            <h3 class="text-2xl font-bold text-white mb-6">Tambah Satker</h3>
            <form action="{{ route('satkers.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Nama Satuan Kerja</label>
                    <input type="text" name="nama_satker" required placeholder="Contoh: Polres Metro Jaya"
                        class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500 focus:border-primary-500 transition-all">
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
            class="inline-block w-full max-w-lg p-8 my-8 overflow-hidden text-left align-middle transition-all transform glass-card rounded-3xl shadow-2xl relative">
            <h3 class="text-2xl font-bold text-white mb-6">Edit Satker</h3>
            <form :action="'{{ url('satkers') }}/' + formData.id" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Nama Satuan Kerja</label>
                    <input type="text" name="nama_satker" x-model="formData.nama_satker" required
                        class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500 focus:border-primary-500 transition-all">
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