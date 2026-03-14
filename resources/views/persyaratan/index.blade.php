<x-app-layout>
    <x-slot name="header">
        Kelola Persyaratan Berkas
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Header --}}
        <!-- Action Bar -->
        <div class="glass-card p-2 px-3 rounded-xl flex items-center justify-between gap-3 animate-fade-in transition-all">
            <div class="flex items-center gap-2">
                <i class="ph ph-list-checks text-primary-500 text-lg"></i>
                <h3 class="text-xs font-bold text-white uppercase tracking-wider">Persyaratan Berkas</h3>
            </div>
            <a href="{{ route('persyaratan-berkas.create', ['kategori' => $kategori]) }}"
                class="btn-compact bg-primary-600 hover:bg-primary-500 text-white font-semibold shadow-lg shadow-primary-500/20 transition-all flex items-center">
                <i class="ph ph-plus mr-1.5 text-lg"></i>
                Tambah Persyaratan
            </a>
        </div>

        {{-- Tab Kategori --}}
        <div class="flex gap-2">
            <a href="{{ route('persyaratan-berkas.index', ['kategori' => 'penghapusan']) }}"
                class="px-4 py-2 rounded-xl text-sm font-semibold transition-all
                {{ $kategori === 'penghapusan' ? 'bg-red-600 text-white shadow-lg shadow-red-500/20' : 'bg-gray-800 text-gray-400 hover:bg-gray-700' }}">
                <i class="ph ph-trash mr-1"></i> Penghapusan Barang
            </a>
            <a href="{{ route('persyaratan-berkas.index', ['kategori' => 'penetapan_status']) }}"
                class="px-4 py-2 rounded-xl text-sm font-semibold transition-all
                {{ $kategori === 'penetapan_status' ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'bg-gray-800 text-gray-400 hover:bg-gray-700' }}">
                <i class="ph ph-stamp mr-1"></i> Penetapan Status
            </a>
        </div>

        {{-- List --}}
        <div class="glass-card rounded-2xl overflow-hidden">
            @forelse($persyaratan as $item)
                <div
                    class="flex items-center p-4 border-b border-white/5 last:border-0 hover:bg-white/[0.02] transition-colors">
                    <div
                        class="w-8 h-8 rounded-lg bg-primary-600/20 flex items-center justify-center text-primary-400 font-bold text-sm flex-shrink-0 mr-4">
                        {{ $item->urutan }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-white flex items-center gap-2">
                            {{ $item->nama_persyaratan }}
                            @if($item->wajib)
                                <span
                                    class="px-1.5 py-0.5 bg-red-500/20 text-red-400 text-[10px] rounded-md font-bold">WAJIB</span>
                            @else
                                <span
                                    class="px-1.5 py-0.5 bg-gray-500/20 text-gray-400 text-[10px] rounded-md font-bold">OPSIONAL</span>
                            @endif
                        </div>
                        @if($item->deskripsi)
                            <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $item->deskripsi }}</p>
                        @endif
                        @if($item->file_contoh)
                            <a href="{{ route('persyaratan-berkas.download-contoh', $item->id) }}"
                                class="inline-flex items-center gap-1.5 mt-2 text-[10px] font-bold text-primary-400 hover:text-primary-300 transition-colors">
                                <i class="ph ph-file-pdf"></i>
                                Unduh Contoh Format
                            </a>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 ml-4">
                        <a href="{{ route('persyaratan-berkas.edit', $item->id) }}"
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-yellow-500/10 text-yellow-400 hover:bg-yellow-500/20 transition-colors">
                            <i class="ph ph-pencil-simple"></i>
                        </a>
                        <form method="POST" action="{{ route('persyaratan-berkas.destroy', $item->id) }}"
                            id="delete-form-{{ $item->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button"
                                onclick="confirmDelete('delete-form-{{ $item->id }}', '{{ $item->nama_persyaratan }}')"
                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-colors">
                                <i class="ph ph-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <i class="ph ph-list-dashes text-4xl text-gray-600 mb-3"></i>
                    <p class="text-gray-400 text-sm">Belum ada persyaratan untuk kategori ini.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>