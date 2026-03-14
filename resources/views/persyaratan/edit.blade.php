<x-app-layout>
    <x-slot name="header">
        Edit Persyaratan
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('persyaratan-berkas.index', ['kategori' => $persyaratan->kategori]) }}"
                class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-800 text-gray-400 hover:text-white hover:bg-gray-700 transition-colors">
                <i class="ph ph-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-white">Edit Persyaratan</h1>
                <p class="text-gray-400 text-sm mt-0.5">
                    {{ $persyaratan->kategori === 'penghapusan' ? 'Penghapusan Barang' : 'Penetapan Status Penggunaan' }}
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('persyaratan-berkas.update', $persyaratan->id) }}"
            enctype="multipart/form-data" class="glass-card rounded-2xl p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Nama Persyaratan <span
                        class="text-red-400">*</span></label>
                <input type="text" name="nama_persyaratan"
                    value="{{ old('nama_persyaratan', $persyaratan->nama_persyaratan) }}" required
                    class="w-full bg-gray-900/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-primary-500/50">
                @error('nama_persyaratan')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="3"
                    class="w-full bg-gray-900/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-primary-500/50 resize-none">{{ old('deskripsi', $persyaratan->deskripsi) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Contoh Format (Excel/Word/PDF)</label>
                @if($persyaratan->file_contoh)
                    <div class="mb-3 p-3 bg-white/5 rounded-xl border border-white/10 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="ph ph-file-pdf text-xl text-primary-400"></i>
                            <span class="text-xs text-gray-300">{{ $persyaratan->nama_file_contoh }}</span>
                        </div>
                        <a href="{{ route('persyaratan-berkas.download-contoh', $persyaratan->id) }}"
                            class="text-[10px] text-primary-400 font-semibold hover:underline">Download</a>
                    </div>
                @endif
                <input type="file" name="file_contoh" accept=".pdf,.doc,.docx,.xls,.xlsx"
                    class="w-full bg-gray-900/50 border border-white/10 rounded-xl px-4 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-primary-500/50 file:mr-4 file:py-1 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-600 file:text-white hover:file:bg-primary-500 transition-all">
                <p class="text-[10px] text-gray-400 mt-1">Pilih file baru untuk mengganti file contoh yang ada.</p>
                @error('file_contoh')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Urutan</label>
                    <input type="number" name="urutan" value="{{ old('urutan', $persyaratan->urutan) }}" min="0"
                        class="w-full bg-gray-900/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-primary-500/50">
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-3 cursor-pointer group pb-3">
                        <input type="checkbox" name="wajib" value="1" {{ old('wajib', $persyaratan->wajib) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-gray-600 bg-gray-800 text-primary-500 focus:ring-primary-500/50">
                        <span class="text-sm text-gray-300 group-hover:text-white transition-colors">Wajib
                            diupload</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('persyaratan-berkas.index', ['kategori' => $persyaratan->kategori]) }}"
                    class="px-5 py-2.5 bg-gray-800 text-gray-300 rounded-xl text-sm font-semibold hover:bg-gray-700 transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="px-5 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-500 transition-colors">
                    <i class="ph ph-floppy-disk mr-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>