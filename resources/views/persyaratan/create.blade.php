<x-app-layout>
    <x-slot name="header">
        Tambah Persyaratan
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('persyaratan-berkas.index', ['kategori' => $kategori]) }}"
                class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-800 text-gray-400 hover:text-white hover:bg-gray-700 transition-colors">
                <i class="ph ph-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-white">Tambah Persyaratan</h1>
                <p class="text-gray-400 text-sm mt-0.5">
                    {{ $kategori === 'penghapusan' ? 'Penghapusan Barang' : 'Penetapan Status Penggunaan' }}
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('persyaratan-berkas.store') }}" enctype="multipart/form-data"
            class="glass-card rounded-2xl p-6 space-y-4">
            @csrf
            <input type="hidden" name="kategori" value="{{ $kategori }}">

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Nama Persyaratan <span
                        class="text-red-400">*</span></label>
                <input type="text" name="nama_persyaratan" value="{{ old('nama_persyaratan') }}" required
                    placeholder="Contoh: Surat Pengajuan Penghapusan dari Satker"
                    class="w-full bg-gray-900/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-primary-500/50 placeholder-gray-600">
                @error('nama_persyaratan')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="3" placeholder="Jelaskan dokumen apa yang harus diupload..."
                    class="w-full bg-gray-900/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-primary-500/50 placeholder-gray-600 resize-none">{{ old('deskripsi') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Contoh Format (Excel/Word/PDF)</label>
                <input type="file" name="file_contoh" accept=".pdf,.doc,.docx,.xls,.xlsx"
                    class="w-full bg-gray-900/50 border border-white/10 rounded-xl px-4 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-primary-500/50 file:mr-4 file:py-1 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-600 file:text-white hover:file:bg-primary-500 transition-all">
                <p class="text-[10px] text-gray-500 mt-1">Format: PDF, Word, atau Excel. Maksimal 10MB.</p>
                @error('file_contoh')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Urutan</label>
                    <input type="number" name="urutan" value="{{ old('urutan', 0) }}" min="0"
                        class="w-full bg-gray-900/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-primary-500/50">
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-3 cursor-pointer group pb-3">
                        <input type="checkbox" name="wajib" value="1" {{ old('wajib', true) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-gray-600 bg-gray-800 text-primary-500 focus:ring-primary-500/50">
                        <span class="text-sm text-gray-300 group-hover:text-white transition-colors">Wajib
                            diupload</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('persyaratan-berkas.index', ['kategori' => $kategori]) }}"
                    class="px-5 py-2.5 bg-gray-800 text-gray-300 rounded-xl text-sm font-semibold hover:bg-gray-700 transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="px-5 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-500 transition-colors">
                    <i class="ph ph-plus mr-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>