<x-app-layout>
    <x-slot name="header">
        Buat Pengajuan — {{ $kategori === 'penghapusan' ? 'Penghapusan Barang' : 'Penetapan Status Penggunaan' }}
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('pengajuan-berkas.index') }}"
                class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-800 text-gray-400 hover:text-white hover:bg-gray-700 transition-colors">
                <i class="ph ph-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-white">Buat Pengajuan Baru</h1>
                <p class="text-gray-400 text-sm mt-0.5">
                    {{ $kategori === 'penghapusan' ? 'Penghapusan Barang' : 'Penetapan Status Penggunaan Barang' }}
                </p>
            </div>
        </div>

        @if($persyaratan->isEmpty())
            <div class="glass-card rounded-2xl p-8 text-center">
                <i class="ph ph-warning text-5xl text-yellow-400 mb-4"></i>
                <h3 class="text-lg font-bold text-white mb-2">Persyaratan Belum Tersedia</h3>
                <p class="text-gray-400 text-sm">Super Admin belum menentukan persyaratan berkas untuk kategori ini. Silakan hubungi Super Admin.</p>
            </div>
        @else
            <form method="POST" action="{{ route('pengajuan-berkas.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="kategori" value="{{ $kategori }}">

                {{-- Info Pengajuan --}}
                <div class="glass-card rounded-2xl p-6 space-y-4">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="ph ph-info text-primary-400 mr-2"></i>
                        Informasi Pengajuan
                    </h3>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Judul Pengajuan <span class="text-red-400">*</span></label>
                        <input type="text" name="judul" value="{{ old('judul') }}" required
                            placeholder="Contoh: Pengajuan Penghapusan Kendaraan Dinas R2"
                            class="w-full bg-gray-900/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-primary-500/50 placeholder-gray-600">
                        @error('judul')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Keterangan</label>
                        <textarea name="keterangan" rows="3"
                            placeholder="Tambahkan keterangan jika diperlukan..."
                            class="w-full bg-gray-900/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-primary-500/50 placeholder-gray-600 resize-none">{{ old('keterangan') }}</textarea>
                    </div>
                </div>

                {{-- Upload Berkas --}}
                <div class="glass-card rounded-2xl p-6 space-y-5">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="ph ph-file-pdf text-red-400 mr-2"></i>
                        Upload Berkas Persyaratan
                    </h3>
                    <p class="text-gray-400 text-xs -mt-3">Upload file PDF untuk setiap persyaratan. Berkas yang bertanda <span class="text-red-400">*</span> wajib diupload.</p>

                    @foreach($persyaratan as $syarat)
                        <div class="p-4 rounded-xl border border-white/10 bg-white/[0.02] space-y-3" x-data="{ fileName: '' }">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-primary-600/20 flex items-center justify-center text-primary-400 font-bold text-sm flex-shrink-0 mt-0.5">
                                    {{ $loop->iteration }}
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-semibold text-white">
                                        {{ $syarat->nama_persyaratan }}
                                        @if($syarat->wajib)
                                            <span class="text-red-400 ml-0.5">*</span>
                                        @else
                                            <span class="text-gray-500 text-xs ml-1">(opsional)</span>
                                        @endif
                                    </div>
                                    @if($syarat->deskripsi)
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $syarat->deskripsi }}</p>
                                    @endif
                                    @if($syarat->file_contoh)
                                        <a href="{{ route('persyaratan-berkas.download-contoh', $syarat->id) }}"
                                           class="inline-flex items-center gap-1 mt-1 text-[10px] font-bold text-primary-400 hover:text-primary-300 transition-colors bg-primary-400/10 px-2 py-1 rounded-lg">
                                            <i class="ph ph-download-simple"></i>
                                            Unduh Contoh Format
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="ml-11">
                                <label class="flex items-center justify-center w-full h-24 border-2 border-dashed border-white/10 rounded-xl cursor-pointer hover:border-primary-500/50 hover:bg-primary-500/5 transition-all relative">
                                    <input type="file" name="dokumen_{{ $syarat->id }}" accept=".pdf" class="hidden"
                                        @change="fileName = $event.target.files[0]?.name || ''"
                                        {{ $syarat->wajib ? 'required' : '' }}>
                                    <div class="text-center" x-show="!fileName">
                                        <i class="ph ph-upload-simple text-2xl text-gray-500 mb-1"></i>
                                        <p class="text-xs text-gray-500">Klik untuk upload PDF</p>
                                    </div>
                                    <div class="text-center" x-show="fileName" x-cloak>
                                        <i class="ph ph-file-pdf text-2xl text-green-400 mb-1"></i>
                                        <p class="text-xs text-green-400 font-medium" x-text="fileName"></p>
                                    </div>
                                </label>
                                @error("dokumen_{$syarat->id}")
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Checklist Konfirmasi --}}
                <div class="glass-card rounded-2xl p-6 space-y-4">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="ph ph-check-square text-green-400 mr-2"></i>
                        Konfirmasi
                    </h3>

                    <label class="flex items-start gap-3 cursor-pointer group">
                        <input type="checkbox" required
                            class="mt-1 w-4 h-4 rounded border-gray-600 bg-gray-800 text-primary-500 focus:ring-primary-500/50">
                        <span class="text-sm text-gray-300 group-hover:text-white transition-colors">
                            Saya menyatakan bahwa semua berkas yang diupload adalah dokumen asli dan benar sesuai dengan persyaratan yang telah ditentukan.
                        </span>
                    </label>
                </div>

                {{-- Submit --}}
                <div class="flex justify-end gap-3">
                    <a href="{{ route('pengajuan-berkas.index') }}"
                        class="px-6 py-3 bg-gray-800 text-gray-300 rounded-xl text-sm font-semibold hover:bg-gray-700 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-500 text-white rounded-xl text-sm font-semibold hover:from-primary-500 hover:to-primary-400 transition-all shadow-lg shadow-primary-500/20">
                        <i class="ph ph-paper-plane-tilt mr-1"></i> Ajukan Berkas
                    </button>
                </div>
            </form>
        @endif
    </div>
</x-app-layout>
