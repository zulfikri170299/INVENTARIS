<x-app-layout>
    <x-slot name="header">
        Detail Pengajuan — {{ $pengajuan->judul }}
    </x-slot>

    @php
        $user = auth()->user();
        $isSuperAdmin = in_array($user->role, ['Super Admin', 'Super Admin 2']);
        $isOwner = $pengajuan->satker_id === $user->satker_id;

        $stages = [
            'diajukan' => ['label' => 'Diajukan', 'icon' => 'ph-paper-plane-tilt', 'color' => 'yellow'],
            'diterima' => ['label' => 'Diterima', 'icon' => 'ph-check-circle', 'color' => 'blue'],
            'diproses' => ['label' => 'Diproses', 'icon' => 'ph-arrows-clockwise', 'color' => 'cyan'],
            'dikembalikan' => ['label' => 'Dikembalikan', 'icon' => 'ph-arrow-counter-clockwise', 'color' => 'red'],
            'naik_ke_kapolda' => ['label' => 'Naik ke Kapolda', 'icon' => 'ph-arrow-up', 'color' => 'purple'],
            'ditandatangani' => ['label' => 'Ditandatangani', 'icon' => 'ph-pen', 'color' => 'indigo'],
            'selesai' => ['label' => 'Selesai', 'icon' => 'ph-flag-checkered', 'color' => 'green'],
        ];

        // Determine progress step (0-based)
        $progressOrder = ['diajukan', 'diterima', 'diproses', 'naik_ke_kapolda', 'ditandatangani', 'selesai'];
        $currentStep = array_search($pengajuan->status, $progressOrder);
        if ($currentStep === false && $pengajuan->status === 'dikembalikan') {
            $currentStep = 0; // dikembalikan is at "diajukan" level
        }
    @endphp

    <div class="max-w-5xl mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('pengajuan-berkas.index') }}"
                class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-800 text-gray-400 hover:text-white hover:bg-gray-700 transition-colors">
                <i class="ph ph-arrow-left text-xl"></i>
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-white">{{ $pengajuan->judul }}</h1>
                <div class="flex items-center gap-3 mt-1">
                    <span
                        class="px-2.5 py-0.5 rounded-lg text-xs font-semibold {{ $pengajuan->kategori === 'penghapusan' ? 'bg-red-500/20 text-red-400' : 'bg-blue-500/20 text-blue-400' }}">
                        {{ $pengajuan->kategori_label }}
                    </span>
                    @php
                        $statusColors = [
                            'diajukan' => 'bg-yellow-500/20 text-yellow-400',
                            'diterima' => 'bg-blue-500/20 text-blue-400',
                            'diproses' => 'bg-cyan-500/20 text-cyan-400',
                            'dikembalikan' => 'bg-red-500/20 text-red-400',
                            'naik_ke_kapolda' => 'bg-purple-500/20 text-purple-400',
                            'ditandatangani' => 'bg-indigo-500/20 text-indigo-400',
                            'selesai' => 'bg-green-500/20 text-green-400',
                        ];
                    @endphp
                    <span
                        class="px-2.5 py-0.5 rounded-lg text-xs font-semibold {{ $statusColors[$pengajuan->status] ?? '' }}">
                        {{ $pengajuan->status_label }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Progress Stepper --}}
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-6">Progress Pengajuan</h3>
            <div class="flex items-center justify-between relative">
                {{-- Background line --}}
                <div class="absolute top-5 left-0 right-0 h-0.5 bg-gray-700"></div>
                @php
                    $completedWidth = $currentStep !== false && count($progressOrder) > 1
                        ? ($currentStep / (count($progressOrder) - 1)) * 100
                        : 0;
                @endphp
                <div class="absolute top-5 left-0 h-0.5 bg-gradient-to-r from-primary-500 to-green-500 transition-all duration-500"
                    style="width: {{ $completedWidth }}%"></div>

                @foreach($progressOrder as $idx => $stageKey)
                            @php
                                $stage = $stages[$stageKey];
                                $isDone = $currentStep !== false && $idx <= $currentStep;
                                $isCurrent = $pengajuan->status === $stageKey;
                                $isReturned = $pengajuan->status === 'dikembalikan' && $stageKey === 'diajukan';
                            @endphp
                            <div class="flex flex-col items-center relative z-10">
                                <div
                                    class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300
                                                                                                    {{ $isReturned ? 'bg-red-500/30 border-2 border-red-500 text-red-400' :
                    ($isDone ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30' :
                        'bg-gray-800 border-2 border-gray-700 text-gray-500') }}
                                                                                                    {{ $isCurrent && !$isReturned ? 'ring-4 ring-primary-500/20' : '' }}">
                                    <i class="ph {{ $isReturned ? 'ph-arrow-counter-clockwise' : $stage['icon'] }} text-lg"></i>
                                </div>
                                <span
                                    class="text-[10px] mt-2 font-medium text-center whitespace-nowrap
                                                                                                    {{ $isReturned ? 'text-red-400' : ($isDone ? 'text-primary-400' : 'text-gray-500') }}">
                                    {{ $isReturned ? 'Dikembalikan' : $stage['label'] }}
                                </span>
                            </div>
                @endforeach
            </div>
        </div>

        {{-- Alert for Dikembalikan --}}
        @if($pengajuan->status === 'dikembalikan' && $pengajuan->catatan_super_admin)
            <div class="bg-red-500/10 border border-red-500/20 rounded-2xl p-5">
                <div class="flex items-start gap-3">
                    <i class="ph ph-warning-circle text-2xl text-red-400 flex-shrink-0 mt-0.5"></i>
                    <div>
                        <h4 class="text-sm font-bold text-red-400 mb-1">Berkas Dikembalikan</h4>
                        <p class="text-sm text-gray-300">{{ $pengajuan->catatan_super_admin }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column: Info & Documents --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Info Card --}}
                <div class="glass-card rounded-2xl p-6 space-y-4">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="ph ph-info text-primary-400 mr-2"></i> Informasi Pengajuan
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">Pengaju</p>
                            <p class="text-sm text-white font-medium">{{ $pengajuan->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">Satker</p>
                            <p class="text-sm text-white font-medium">{{ $pengajuan->satker->nama_satker ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">Tanggal Pengajuan</p>
                            <p class="text-sm text-white font-medium">{{ $pengajuan->created_at->format('d F Y, H:i') }}
                                WITA</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">Terakhir Update</p>
                            <p class="text-sm text-white font-medium">{{ $pengajuan->updated_at->format('d F Y, H:i') }}
                                WITA</p>
                        </div>
                    </div>
                    @if($pengajuan->keterangan)
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">Keterangan</p>
                            <p class="text-sm text-gray-300">{{ $pengajuan->keterangan }}</p>
                        </div>
                    @endif
                </div>

                {{-- Dokumen Persyaratan --}}
                <div class="glass-card rounded-2xl p-6 space-y-4">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="ph ph-files text-orange-400 mr-2"></i> Berkas Persyaratan
                    </h3>
                    <div class="space-y-3">
                        @foreach($persyaratan as $syarat)
                            @php
                                $dokumen = $pengajuan->dokumen->firstWhere('persyaratan_berkas_id', $syarat->id);
                            @endphp
                            <div class="flex items-center p-3 rounded-xl border border-white/10 bg-white/[0.02]">
                                <div
                                    class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mr-3
                                                            {{ $dokumen ? 'bg-green-500/20 text-green-400' : 'bg-gray-700/50 text-gray-500' }}">
                                    <i class="ph {{ $dokumen ? 'ph-check' : 'ph-x' }} text-lg"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-white truncate">{{ $syarat->nama_persyaratan }}
                                    </div>
                                    @if($dokumen)
                                        <div class="text-xs text-gray-500 truncate">{{ $dokumen->nama_file }}</div>
                                    @else
                                        <div class="text-xs text-gray-600">Belum diupload</div>
                                    @endif
                                    @if($syarat->file_contoh)
                                        <a href="{{ route('persyaratan-berkas.download-contoh', $syarat->id) }}"
                                            class="inline-flex items-center gap-1 mt-1 text-[10px] font-bold text-primary-400 hover:text-primary-300 transition-colors">
                                            <i class="ph ph-download-simple"></i>
                                            Unduh Contoh Format
                                        </a>
                                    @endif
                                </div>
                                @if($dokumen)
                                    <a href="{{ route('pengajuan-berkas.preview-dokumen', $dokumen->id) }}" target="_blank"
                                        class="ml-2 px-3 py-1.5 bg-cyan-600/20 text-cyan-400 rounded-lg text-xs font-semibold hover:bg-cyan-600/30 transition-colors flex-shrink-0">
                                        <i class="ph ph-eye mr-1"></i> Lihat
                                    </a>
                                    @if($isSuperAdmin && in_array($pengajuan->status, ['diajukan', 'diterima', 'diproses', 'dikembalikan']))
                                        <a href="{{ route('pengajuan-berkas.annotate-dokumen', $dokumen->id) }}"
                                            class="ml-2 px-3 py-1.5 bg-orange-600/20 text-orange-400 rounded-lg text-xs font-semibold hover:bg-orange-600/30 transition-colors flex-shrink-0">
                                            <i class="ph ph-pencil-simple mr-1"></i> Koreksi
                                        </a>
                                    @endif
                                    <a href="{{ route('pengajuan-berkas.download-dokumen', $dokumen->id) }}"
                                        class="ml-2 px-3 py-1.5 bg-primary-600/20 text-primary-400 rounded-lg text-xs font-semibold hover:bg-primary-600/30 transition-colors flex-shrink-0">
                                        <i class="ph ph-download mr-1"></i> Unduh
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Berkas Final (jika selesai) --}}
                @if($pengajuan->status === 'selesai' && $pengajuan->berkas_final)
                    <div class="bg-green-500/10 border border-green-500/20 rounded-2xl p-6">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-green-500/20 flex items-center justify-center">
                                <i class="ph ph-file-pdf text-3xl text-green-400"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-green-400 mb-0.5">Berkas Final dari Kapolda</h4>
                                <p class="text-xs text-gray-400">Berkas telah ditandatangani dan siap diunduh.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('pengajuan-berkas.preview-final', $pengajuan->id) }}" target="_blank"
                                    class="px-4 py-2 bg-cyan-600/20 text-cyan-400 rounded-xl text-sm font-semibold hover:bg-cyan-600/30 transition-colors">
                                    <i class="ph ph-eye mr-1"></i> Lihat
                                </a>
                                <a href="{{ route('pengajuan-berkas.download-final', $pengajuan->id) }}"
                                    class="px-5 py-2.5 bg-green-600 text-white rounded-xl text-sm font-semibold hover:bg-green-500 transition-colors shadow-lg shadow-green-500/20">
                                    <i class="ph ph-download mr-1"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Form Perbaiki (jika dikembalikan dan pemilik) --}}
                @if($pengajuan->status === 'dikembalikan' && $isOwner && !$isSuperAdmin)
                    <div class="glass-card rounded-2xl p-6 space-y-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <i class="ph ph-arrow-clockwise text-yellow-400 mr-2"></i> Perbaiki & Ajukan Ulang
                        </h3>
                        <p class="text-sm text-gray-400">Upload ulang berkas yang perlu diperbaiki, lalu ajukan kembali.</p>

                        <form method="POST" action="{{ route('pengajuan-berkas.perbaiki', $pengajuan->id) }}"
                            enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            @foreach($persyaratan as $syarat)
                                @php
                                    $oldDok = $pengajuan->dokumen->where('persyaratan_berkas_id', $syarat->id)->first();
                                @endphp
                                @if($oldDok && $oldDok->butuh_revisi)
                                    <div class="p-3 rounded-xl border border-red-500/30 bg-red-500/5" x-data="{ fileName: '' }">
                                        <div class="flex items-center gap-3 mb-2">
                                            <i class="ph ph-warning-circle text-red-500 text-lg"></i>
                                            <div>
                                                <span class="text-sm font-medium text-white">{{ $syarat->nama_persyaratan }}</span>
                                                <p class="text-xs text-red-400">Dokumen ini perlu direvisi</p>
                                                @if($syarat->file_contoh)
                                                    <a href="{{ route('persyaratan-berkas.download-contoh', $syarat->id) }}"
                                                        class="inline-flex items-center gap-1 mt-1 text-[10px] font-bold text-primary-400 hover:text-primary-300 transition-colors bg-primary-400/10 px-2 py-0.5 rounded-md">
                                                        <i class="ph ph-download-simple"></i>
                                                        Unduh Contoh Format
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        <label
                                            class="flex items-center justify-center w-full h-16 border-2 border-dashed border-red-500/30 rounded-xl cursor-pointer hover:border-red-500/50 hover:bg-red-500/10 transition-all">
                                            <input type="file" name="dokumen_{{ $syarat->id }}" accept=".pdf" class="hidden"
                                                required @change="fileName = $event.target.files[0]?.name || ''">
                                            <div class="text-center" x-show="!fileName">
                                                <p class="text-xs text-gray-400"><i class="ph ph-upload-simple mr-1"></i>Klik untuk
                                                    upload PDF baru</p>
                                            </div>
                                            <div class="text-center" x-show="fileName" x-cloak>
                                                <p class="text-xs text-green-400 font-medium"><i
                                                        class="ph ph-file-pdf mr-1"></i><span x-text="fileName"></span></p>
                                            </div>
                                        </label>
                                    </div>
                                @endif
                            @endforeach

                            <div class="flex justify-end">
                                <button type="submit"
                                    class="px-5 py-2.5 bg-gradient-to-r from-yellow-600 to-yellow-500 text-white rounded-xl text-sm font-semibold hover:from-yellow-500 hover:to-yellow-400 transition-all shadow-lg shadow-yellow-500/20">
                                    <i class="ph ph-paper-plane-tilt mr-1"></i> Ajukan Ulang
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            {{-- Right Column: Timeline & Actions --}}
            <div class="space-y-6">
                {{-- Actions Card (Super Admin) --}}
                @if($isSuperAdmin)
                    <div class="glass-card rounded-2xl p-6 space-y-3">
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Aksi Super Admin</h3>

                        @if($pengajuan->status === 'diajukan')
                            <form method="POST" action="{{ route('pengajuan-berkas.terima', $pengajuan->id) }}">
                                @csrf
                                <button type="submit"
                                    class="w-full px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-500 transition-colors">
                                    <i class="ph ph-check-circle mr-1"></i> Terima Pengajuan
                                </button>
                            </form>

                            <div x-data="{ showReturn: false }">
                                <button @click="showReturn = !showReturn"
                                    class="w-full px-4 py-2.5 bg-red-600/20 text-red-400 rounded-xl text-sm font-semibold hover:bg-red-600/30 transition-colors">
                                    <i class="ph ph-arrow-counter-clockwise mr-1"></i> Kembalikan
                                </button>
                                <div x-show="showReturn" x-collapse x-cloak class="mt-3">
                                    <form method="POST" action="{{ route('pengajuan-berkas.kembalikan', $pengajuan->id) }}"
                                        class="space-y-3">
                                        @csrf
                                        <div
                                            class="space-y-2 mb-3 max-h-40 overflow-y-auto custom-scrollbar pr-2 bg-black/20 p-2 rounded-lg border border-red-500/20">
                                            <p class="text-xs font-semibold text-red-400 uppercase tracking-wider mb-2">Pilih
                                                Dokumen yang Salah (Opsional):</p>
                                            @foreach($pengajuan->dokumen as $dok)
                                                <label class="flex items-start space-x-2 text-sm text-gray-300 cursor-pointer">
                                                    <input type="checkbox" name="revisi_dokumen[]" value="{{ $dok->id }}"
                                                        class="rounded mt-0.5 border-white/20 bg-gray-900/50 text-red-500 focus:ring-red-500/50">
                                                    <span class="leading-tight">{{ $dok->persyaratan->nama_persyaratan }} <br><span
                                                            class="text-[10px] text-gray-500">{{ $dok->nama_file }}</span></span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <textarea name="catatan" rows="3" required
                                            placeholder="Tulis catatan tentang apa yang perlu diperbaiki..."
                                            class="w-full bg-gray-900/50 border border-white/10 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-red-500/50 placeholder-gray-600 resize-none"></textarea>
                                        <button type="submit"
                                            class="w-full px-4 py-2 bg-red-600 text-white rounded-xl text-sm font-semibold hover:bg-red-500 transition-colors">
                                            Kirim
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif

                        @if($pengajuan->status === 'diterima' || $pengajuan->status === 'diproses')
                            @if($pengajuan->status === 'diterima')
                                <form method="POST" action="{{ route('pengajuan-berkas.proses', $pengajuan->id) }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full px-4 py-2.5 bg-cyan-600 text-white rounded-xl text-sm font-semibold hover:bg-cyan-500 transition-colors">
                                        <i class="ph ph-arrows-clockwise mr-1"></i> Mulai Proses
                                    </button>
                                </form>
                            @endif
                            @if($pengajuan->status === 'diproses')
                                <form method="POST" action="{{ route('pengajuan-berkas.naik-kapolda', $pengajuan->id) }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full px-4 py-2.5 bg-purple-600 text-white rounded-xl text-sm font-semibold hover:bg-purple-500 transition-colors">
                                        <i class="ph ph-arrow-up mr-1"></i> Naikkan ke Kapolda
                                    </button>
                                </form>
                            @endif

                            <div x-data="{ showReturn: false }">
                                <button @click="showReturn = !showReturn"
                                    class="w-full mt-3 px-4 py-2.5 bg-red-600/20 text-red-400 rounded-xl text-sm font-semibold hover:bg-red-600/30 transition-colors">
                                    <i class="ph ph-arrow-counter-clockwise mr-1"></i> Kembalikan
                                </button>
                                <div x-show="showReturn" x-collapse x-cloak class="mt-3">
                                    <form method="POST" action="{{ route('pengajuan-berkas.kembalikan', $pengajuan->id) }}"
                                        class="space-y-3">
                                        @csrf
                                        <div
                                            class="space-y-2 mb-3 max-h-40 overflow-y-auto custom-scrollbar pr-2 bg-black/20 p-2 rounded-lg border border-red-500/20">
                                            <p class="text-xs font-semibold text-red-400 uppercase tracking-wider mb-2">Pilih
                                                Dokumen yang Salah (Opsional):</p>
                                            @foreach($pengajuan->dokumen as $dok)
                                                <label class="flex items-start space-x-2 text-sm text-gray-300 cursor-pointer">
                                                    <input type="checkbox" name="revisi_dokumen[]" value="{{ $dok->id }}"
                                                        class="rounded mt-0.5 border-white/20 bg-gray-900/50 text-red-500 focus:ring-red-500/50">
                                                    <span class="leading-tight">{{ $dok->persyaratan->nama_persyaratan }} <br><span
                                                            class="text-[10px] text-gray-500">{{ $dok->nama_file }}</span></span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <textarea name="catatan" rows="3" required placeholder="Tulis catatan..."
                                            class="w-full bg-gray-900/50 border border-white/10 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-red-500/50 placeholder-gray-600 resize-none"></textarea>
                                        <button type="submit"
                                            class="w-full px-4 py-2 bg-red-600 text-white rounded-xl text-sm font-semibold hover:bg-red-500 transition-colors">
                                            Kirim
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif

                        @if($pengajuan->status === 'naik_ke_kapolda')
                            <form method="POST" action="{{ route('pengajuan-berkas.tandatangan', $pengajuan->id) }}">
                                @csrf
                                <button type="submit"
                                    class="w-full px-4 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-semibold hover:bg-indigo-500 transition-colors">
                                    <i class="ph ph-pen mr-1"></i> Tandai Sudah Ditandatangani
                                </button>
                            </form>
                        @endif

                        @if($pengajuan->status === 'ditandatangani')
                            <div x-data="{ fileName: '' }">
                                <form method="POST" action="{{ route('pengajuan-berkas.kirim-final', $pengajuan->id) }}"
                                    enctype="multipart/form-data" class="space-y-3">
                                    @csrf
                                    <label
                                        class="flex items-center justify-center w-full h-20 border-2 border-dashed border-green-500/30 rounded-xl cursor-pointer hover:border-green-500/50 hover:bg-green-500/5 transition-all">
                                        <input type="file" name="berkas_final" accept=".pdf" required class="hidden"
                                            @change="fileName = $event.target.files[0]?.name || ''">
                                        <div class="text-center" x-show="!fileName">
                                            <i class="ph ph-upload-simple text-xl text-green-400 mb-1"></i>
                                            <p class="text-xs text-green-400">Upload berkas final PDF</p>
                                        </div>
                                        <div class="text-center" x-show="fileName" x-cloak>
                                            <i class="ph ph-file-pdf text-xl text-green-400 mb-1"></i>
                                            <p class="text-xs text-green-400" x-text="fileName"></p>
                                        </div>
                                    </label>
                                    @error('berkas_final')
                                        <p class="text-red-400 text-xs">{{ $message }}</p>
                                    @enderror
                                    <button type="submit"
                                        class="w-full px-4 py-2.5 bg-green-600 text-white rounded-xl text-sm font-semibold hover:bg-green-500 transition-colors">
                                        <i class="ph ph-paper-plane-tilt mr-1"></i> Kirim Berkas Final
                                    </button>
                                </form>
                            </div>
                        @endif

                        @if($pengajuan->status === 'selesai')
                            <div class="text-center py-4">
                                <i class="ph ph-check-circle text-4xl text-green-400 mb-2"></i>
                                <p class="text-sm text-green-400 font-semibold">Pengajuan Selesai</p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Timeline --}}
                <div class="glass-card rounded-2xl p-6">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Riwayat</h3>
                    <div class="space-y-0">
                        @foreach($pengajuan->riwayat as $riwayat)
                            @php
                                $rColors = [
                                    'diajukan' => 'bg-yellow-500',
                                    'diterima' => 'bg-blue-500',
                                    'diproses' => 'bg-cyan-500',
                                    'dikembalikan' => 'bg-red-500',
                                    'naik_ke_kapolda' => 'bg-purple-500',
                                    'ditandatangani' => 'bg-indigo-500',
                                    'selesai' => 'bg-green-500',
                                ];
                            @endphp
                            <div class="flex gap-3 relative pb-6 last:pb-0">
                                {{-- Vertical line --}}
                                @if(!$loop->last)
                                    <div class="absolute left-[9px] top-5 bottom-0 w-px bg-gray-700"></div>
                                @endif
                                <div
                                    class="w-[18px] h-[18px] rounded-full {{ $rColors[$riwayat->status] ?? 'bg-gray-500' }} flex-shrink-0 mt-1 ring-4 ring-[#0f172a]">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-semibold text-white">{{ $riwayat->status_label }}</div>
                                    <div class="text-[10px] text-gray-500 mt-0.5">
                                        {{ $riwayat->user->name }} • {{ $riwayat->created_at->format('d/m/Y H:i') }}
                                    </div>
                                    @if($riwayat->catatan)
                                        <div class="mt-1.5 p-2 bg-white/[0.03] rounded-lg border border-white/5">
                                            <p class="text-xs text-gray-400">{{ $riwayat->catatan }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>