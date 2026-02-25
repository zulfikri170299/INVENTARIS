<!-- Add Modal -->
<div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showAddModal" @click="showAddModal = false"
            class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm"></div>
        <div
            class="inline-block w-full max-w-2xl p-8 my-8 overflow-hidden text-left align-middle transition-all transform glass-card rounded-3xl shadow-2xl relative">
            <h3 class="text-2xl font-bold text-white mb-6">Tambah User Baru</h3>
            <form action="{{ route('users.store') }}" method="POST" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" required
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500 focus:border-primary-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Email</label>
                        <input type="email" name="email" required
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500 focus:border-primary-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Role</label>
                        <select name="role" required
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                            <option value="Super Admin">Super Admin</option>
                            <option value="Admin Satker">Admin Satker</option>
                            <option value="Pimpinan">Pimpinan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Satker</label>
                        <select name="satker_id"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                            <option value="">-- Pilih Satker --</option>
                            @foreach($satkers as $satker)
                                <option value="{{ $satker->id }}">{{ $satker->nama_satker }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Password</label>
                        <input type="password" name="password" required
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="showAddModal = false"
                        class="px-6 py-2.5 bg-gray-800 text-gray-300 rounded-xl hover:bg-gray-700 transition-all">Batal</button>
                    <button type="submit"
                        class="px-6 py-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-500 shadow-lg shadow-primary-500/30 transition-all">Simpan
                        User</button>
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
            class="inline-block w-full max-w-2xl p-8 my-8 overflow-hidden text-left align-middle transition-all transform glass-card rounded-3xl shadow-2xl relative">
            <h3 class="text-2xl font-bold text-white mb-6">Edit User</h3>
            <form :action="'{{ url('users') }}/' + formData.id" method="POST" class="space-y-5">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" x-model="formData.name" required
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Email</label>
                        <input type="email" name="email" x-model="formData.email" required
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Role</label>
                        <select name="role" x-model="formData.role" required
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                            <option value="Super Admin">Super Admin</option>
                            <option value="Admin Satker">Admin Satker</option>
                            <option value="Pimpinan">Pimpinan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Satker</label>
                        <select name="satker_id" x-model="formData.satker_id"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                            <option value="">-- Pilih Satker --</option>
                            @foreach($satkers as $satker)
                                <option value="{{ $satker->id }}">{{ $satker->nama_satker }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div
                        class="col-span-2 bg-yellow-500/10 text-yellow-400 p-4 rounded-xl border border-yellow-500/20 text-xs">
                        Kosongkan password jika tidak ingin mengganti.
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Password Baru</label>
                        <input type="password" name="password"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation"
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-100 rounded-xl px-4 py-3 focus:ring-primary-500">
                    </div>
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