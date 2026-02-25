<!-- Import Modal -->
<div x-show="showImportModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showImportModal" @click="showImportModal = false"
            class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm"></div>
        <div
            class="inline-block w-full max-w-lg p-8 my-8 overflow-hidden text-left align-middle transition-all transform glass-card rounded-3xl shadow-2xl relative">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-white">Import Satker</h3>
                <button @click="showImportModal = false" class="text-gray-400 hover:text-white transition-colors">
                    <i class="ph ph-x text-xl"></i>
                </button>
            </div>

            <form action="{{ route('satkers.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="p-6 border-2 border-dashed border-gray-700 rounded-2xl bg-gray-800/30 text-center">
                    <i class="ph ph-cloud-arrow-up text-5xl text-primary-400 mb-4"></i>
                    <p class="text-gray-300 mb-2">Pilih file Excel (.xlsx) Anda</p>
                    <input type="file" name="file" required class="hidden" id="import-file-satker"
                        accept=".xlsx,.xls,.csv"
                        onchange="document.getElementById('file-name-satker').innerText = this.files[0].name">
                    <label for="import-file-satker"
                        class="cursor-pointer inline-block px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-all text-sm">
                        Cari File
                    </label>
                    <p id="file-name-satker" class="mt-2 text-xs text-primary-400 font-medium"></p>
                </div>

                <div class="bg-primary-500/5 border border-primary-500/20 rounded-xl p-4">
                    <div class="flex">
                        <i class="ph ph-info text-primary-400 text-xl mr-3 mt-0.5"></i>
                        <div>
                            <p class="text-xs text-primary-400 font-semibold mb-1 uppercase tracking-wider">Petunjuk:
                            </p>
                            <p class="text-xs text-gray-400 leading-relaxed">
                                Gunakan template standar untuk menghindari error format. Sistem akan mengecek duplikasi
                                nama satker secara otomatis.
                            </p>
                            <a href="{{ route('satkers.download-template') }}"
                                class="inline-flex items-center text-primary-400 text-xs mt-2 hover:underline font-bold">
                                <i class="ph ph-download-simple mr-1"></i> Download Template Excel
                            </a>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="showImportModal = false"
                        class="px-6 py-2.5 bg-gray-800 text-gray-300 rounded-xl hover:bg-gray-700 transition-all">Batal</button>
                    <button type="submit"
                        class="px-6 py-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-500 shadow-lg shadow-primary-500/30 transition-all font-bold">
                        Mulai Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Conflict Resolver Modal -->
@if(session('import_conflicts'))
    <div x-show="showConflictModal" class="fixed inset-0 z-[60] overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showConflictModal" @click="showConflictModal = false"
                class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm"></div>
            <div
                class="inline-block w-full max-w-4xl p-8 my-8 overflow-hidden text-left align-middle transition-all transform glass-card rounded-3xl shadow-2xl relative">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-white">Deteksi Data Ganda</h3>
                        <p class="text-sm text-gray-400 mt-1">Sistem menemukan beberapa data Satker yang sudah ada di
                            database.</p>
                    </div>
                    <button @click="showConflictModal = false" class="text-gray-400 hover:text-white transition-colors">
                        <i class="ph ph-x text-xl"></i>
                    </button>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-gray-800 mb-6">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-800/50 text-gray-400 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3">Nama Satker (Di Database)</th>
                                <th class="px-6 py-3">Nama Satker (Di Excel)</th>
                                <th class="px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800 text-gray-300">
                            @foreach(session('import_conflicts') as $conflict)
                                <tr>
                                    <td class="px-6 py-4 font-medium text-gray-100">{{ $conflict['existing']['nama_satker'] }}
                                    </td>
                                    <td class="px-6 py-4 font-medium text-primary-400">{{ $conflict['new']['nama_satker'] }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="bg-amber-500/10 text-amber-500 px-2 py-1 rounded text-[10px] font-bold border border-amber-500/20 uppercase">Duplikat</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <form action="{{ route('satkers.confirm-import') }}" method="POST">
                    @csrf
                    <input type="hidden" name="conflicts" value="{{ json_encode(session('import_conflicts')) }}">
                    <input type="hidden" name="pending" value="{{ json_encode(session('pending_import')) }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <button type="submit" name="action" value="skip"
                            class="p-4 bg-gray-800 hover:bg-gray-700 rounded-2xl border border-gray-700 transition-all text-left">
                            <div class="flex items-center mb-2">
                                <i class="ph ph-skip-forward text-2xl text-gray-400 mr-2"></i>
                                <span class="font-bold text-white">Lewati Duplikat</span>
                            </div>
                            <p class="text-xs text-gray-400">Hanya import data Satker baru. Data yang duplikat tidak akan
                                disentuh.</p>
                        </button>
                        <button type="submit" name="action" value="update"
                            class="p-4 bg-primary-600/10 hover:bg-primary-600/20 rounded-2xl border border-primary-500/30 transition-all text-left group">
                            <div class="flex items-center mb-2">
                                <i class="ph ph-arrows-counter-clockwise text-2xl text-primary-400 mr-2"></i>
                                <span class="font-bold text-white">Update & Import Semua</span>
                            </div>
                            <p class="text-xs text-gray-400 group-hover:text-gray-300">Timpa data lama dengan data baru dari
                                Excel dan import sisanya.</p>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif