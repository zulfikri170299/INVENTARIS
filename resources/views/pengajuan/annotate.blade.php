<x-app-layout>
    <x-slot name="header">
        <span class="flex items-center gap-2">
            <i class="ph-duotone ph-pencil-line text-orange-500 text-2xl"></i>
            Koreksi Dokumen
        </span>
    </x-slot>

    <div class="flex flex-col h-[calc(100vh-10rem)] gap-4 animate-in fade-in slide-in-from-bottom-4 duration-700">
        {{-- Glassmorphism Toolbar --}}
        <div class="bg-[#0f172a]/80 backdrop-blur-xl border border-white/10 rounded-3xl p-4 shadow-[0_8px_32px_0_rgba(0,0,0,0.3)] z-40 transition-all hover:border-white/20" x-data="annotationToolbar()">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                {{-- Left Info Section --}}
                <div class="flex items-center gap-4 min-w-0">
                    <a href="{{ route('pengajuan-berkas.show', $dokumen->pengajuanBerkas->id) }}"
                        class="group flex items-center justify-center w-10 h-10 rounded-2xl bg-gray-800/50 border border-white/5 text-gray-400 hover:text-white hover:bg-gray-700/50 hover:border-white/10 transition-all hover:scale-105 active:scale-95 shadow-lg"
                        title="Kembali ke Detail">
                        <i class="ph ph-arrow-left text-lg group-hover:-translate-x-0.5 transition-transform"></i>
                    </a>
                    <div class="min-w-0">
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                             <div class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></div>
                            <span class="truncate">{{ $dokumen->persyaratan->nama_persyaratan }}</span>
                        </h3>
                        <p class="text-[11px] text-gray-400 font-medium">Mode Koreksi Aktif</p>
                    </div>
                </div>

                {{-- Center: Drawing Tools --}}
                <div class="flex items-center gap-4 flex-wrap justify-center lg:justify-start">
                    {{-- Color Palette --}}
                    <div class="flex items-center bg-black/40 border border-white/5 rounded-2xl p-1 gap-1 shadow-inner">
                        <template x-for="c in colors" :key="c.value">
                            <button type="button" @click="setColor(c.value)"
                                :class="activeColor === c.value ? 'ring-2 ring-white/40 scale-110 shadow-lg' : 'opacity-40 hover:opacity-100'"
                                class="w-8 h-8 rounded-xl cursor-pointer transition-all border border-black/20"
                                :style="'background:' + c.value" 
                                :title="c.label"></button>
                        </template>
                    </div>

                    {{-- Brush Size --}}
                    <div class="flex items-center bg-black/40 border border-white/5 rounded-2xl px-4 py-2 gap-3 shadow-inner group">
                        <i class="ph ph-pencil-line text-gray-500 text-sm group-hover:text-orange-400 transition-colors"></i>
                        <input type="range" min="1" max="15" x-model="brushSize"
                            @input="setBrushSize($event.target.value)" 
                            class="w-24 h-1.5 bg-gray-700 rounded-lg appearance-none cursor-pointer accent-orange-500 hover:accent-orange-400 transition-all">
                        <span class="text-[10px] font-mono text-gray-400 w-4 text-right" x-text="String(brushSize).padStart(2, '0')"></span>
                    </div>

                    {{-- Zoom Controls --}}
                    <div class="flex items-center bg-black/40 border border-white/5 rounded-2xl overflow-hidden shadow-inner font-bold">
                        <button type="button" @click="zoomOut()"
                            class="px-3 py-2 text-gray-400 hover:bg-white/5 hover:text-white transition-all active:scale-90" title="Zoom Out">
                            <i class="ph ph-minus text-xs"></i>
                        </button>
                        <div class="w-[60px] text-center border-x border-white/5">
                            <span class="text-[10px] font-mono text-orange-400" x-text="zoomPercent + '%'"></span>
                        </div>
                        <button type="button" @click="zoomIn()"
                            class="px-3 py-2 text-gray-400 hover:bg-white/5 hover:text-white transition-all active:scale-90" title="Zoom In">
                            <i class="ph ph-plus text-xs"></i>
                        </button>
                    </div>
                </div>

                {{-- Right Actions --}}
                <div class="flex items-center gap-3">
                    <button type="button" @click="clearAll()"
                        class="flex items-center gap-2 px-4 py-2.5 bg-white/5 border border-white/10 text-gray-300 rounded-2xl text-[11px] font-bold hover:bg-white/10 hover:text-white hover:border-white/20 transition-all active:scale-95">
                        <i class="ph ph-eraser text-base"></i> Bersihkan
                    </button>

                    <button type="button" @click="savePdf()" id="saveBtn"
                        class="group relative flex items-center gap-2 px-6 py-2.5 bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-wider hover:from-orange-400 hover:to-orange-500 transition-all shadow-[0_4px_20px_0_rgba(249,115,22,0.3)] hover:shadow-[0_4px_25px_0_rgba(249,115,22,0.4)] active:scale-95 active:shadow-none overflow-hidden min-w-[140px] justify-center">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full duration-1000 transition-transform"></div>
                        <i class="ph ph-floppy-disk text-base"></i> 
                        <span id="saveBtnText">Simpan Hasil</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Main Viewer --}}
        <div class="flex-1 relative bg-[#020617] border border-white/5 rounded-[2rem] overflow-hidden shadow-2xl group/viewer">
            {{-- Background Pattern --}}
            <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(#334155 1px, transparent 1px); background-size: 24px 24px;"></div>

            {{-- Loading Overlay --}}
            <div id="loadingOverlay" class="absolute inset-0 z-50 bg-[#020617]/95 backdrop-blur-md flex flex-col items-center justify-center">
                <div class="relative w-20 h-20 mb-6">
                    <div class="absolute inset-0 border-4 border-orange-500/20 rounded-full"></div>
                    <div class="absolute inset-0 border-4 border-orange-500 border-t-transparent rounded-full animate-spin"></div>
                    <i class="ph-duotone ph-file-pdf absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-3xl text-orange-500"></i>
                </div>
                <div class="flex flex-col items-center">
                    <p class="text-sm text-gray-300 font-bold uppercase tracking-[0.2em]" id="loadingText">Mempersiapkan Dokumen</p>
                    <div class="flex gap-1 mt-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-bounce [animation-delay:-0.3s]"></div>
                        <div class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-bounce [animation-delay:-0.15s]"></div>
                        <div class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-bounce"></div>
                    </div>
                </div>
            </div>

            {{-- Error Overlay --}}
            <div id="errorOverlay" class="absolute inset-0 z-50 bg-[#020617]/95 flex flex-col items-center justify-center hidden p-8 text-center animate-in fade-in zoom-in-95 duration-300">
                <div class="w-20 h-20 bg-red-500/10 rounded-full flex items-center justify-center mb-6 border border-red-500/20 shadow-[0_0_40px_rgba(239,68,68,0.1)]">
                    <i class="ph ph-warning-circle text-4xl text-red-500"></i>
                </div>
                <h4 class="text-xl font-bold text-white mb-2">Terjadi Kesalahan</h4>
                <p class="text-sm text-red-400/80 font-medium max-w-sm mx-auto leading-relaxed" id="errorText">Gagal memuat dokumen PDF</p>
                <button onclick="location.reload()" class="mt-8 px-8 py-3 bg-gray-800 hover:bg-gray-700 text-white rounded-2xl text-xs font-bold transition-all shadow-lg active:scale-95 border border-white/5">
                    <i class="ph ph-arrow-clockwise mr-2"></i> Muat Ulang Halaman
                </button>
            </div>

            {{-- Custom Internal Scrollbar --}}
            <style>
                #pdfScroller::-webkit-scrollbar { width: 8px; height: 8px; }
                #pdfScroller::-webkit-scrollbar-track { background: transparent; }
                #pdfScroller::-webkit-scrollbar-thumb { 
                    background: rgba(255,255,255,0.05); 
                    border-radius: 20px;
                    border: 2px solid #020617;
                }
                #pdfScroller::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.1); }
            </style>

            {{-- Scrollable Canvas Area --}}
            <div id="pdfScroller" class="absolute inset-0 overflow-auto scroll-smooth">
                <div id="pdfPages" class="flex flex-col items-center gap-12 py-16 px-8 min-w-fit">
                    {{-- Generated Pages --}}
                </div>
            </div>

            {{-- Floating Page Indicator --}}
            <div id="floatingPageInfo" class="absolute bottom-8 left-1/2 -translate-x-1/2 z-30 transition-all opacity-0 pointer-events-none translate-y-4">
                <div class="bg-black/60 backdrop-blur-md border border-white/10 px-6 py-2 rounded-full shadow-2xl">
                    <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest" id="pageNumLabel">HALAMAN 1 / 1</span>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    {{-- PDF.js & PDF-Lib --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
    {{-- SweetAlert2 is usually in app.blade.php, but let's ensure it's available or fallback --}}

    <script>
    (function() {
        const PDF_URL  = @json(route('pengajuan-berkas.preview-dokumen', $dokumen->id));
        const SAVE_URL = @json(route('pengajuan-berkas.save-annotation', $dokumen->id));
        const CSRF     = @json(csrf_token());

        pdfjsLib.GlobalWorkerOptions.workerSrc =
            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

        const state = {
            pdfBytes: null,
            pdfDoc: null,
            pages: [],
            modified: {},
            color: '#ef4444',
            brushSize: 3,
            saving: false,
            currentScale: 1.5,
        };
        window._annotateState = state;

        // Custom Swal Helper
        const toast = (icon, title) => {
            if (window.Swal) {
                Swal.fire({
                    icon, title,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    background: '#1e293b',
                    color: '#f8fafc'
                });
            } else {
                alert(title);
            }
        };

        // ── Alpine Component ──
        document.addEventListener('alpine:init', () => {
            Alpine.data('annotationToolbar', () => ({
                colors: [
                    { label: 'Merah Terang', value: '#ef4444' },
                    { label: 'Biru Elektrik', value: '#3b82f6' },
                    { label: 'Indigo',        value: '#6366f1' },
                    { label: 'Hijau Segar',   value: '#10b981' },
                    { label: 'Hitam Solid',   value: '#111827' },
                ],
                activeColor: '#ef4444',
                brushSize: 3,
                zoomPercent: 100,

                init() {
                    this.zoomPercent = Math.round(state.currentScale / 1.5 * 100);
                },
                setColor(v)     { this.activeColor = v; state.color = v; },
                setBrushSize(v) { this.brushSize = +v; state.brushSize = +v; },
                clearAll() {
                    const hasMod = Object.values(state.modified).some(m => m);
                    if (!hasMod) return toast('info', 'Belum ada coretan untuk dibersihkan');

                    if (window.Swal) {
                        Swal.fire({
                            title: 'Hapus Coretan?',
                            text: "Semua tanda yang Anda buat di dokumen ini akan hilang.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#ef4444',
                            cancelButtonColor: '#334155',
                            confirmButtonText: 'Ya, Bersihkan!',
                            background: '#0f172a',
                            color: '#fff'
                        }).then((result) => {
                            if (result.isConfirmed) this.executeClear();
                        });
                    } else if (confirm('Bersihkan semua coretan?')) {
                        this.executeClear();
                    }
                },
                executeClear() {
                    state.pages.forEach(p => {
                        p.drawCanvas.getContext('2d').clearRect(0, 0, p.drawCanvas.width, p.drawCanvas.height);
                        state.modified[p.pageNum] = false;
                    });
                    toast('success', 'Semua coretan telah dibersihkan');
                },
                zoomIn() {
                    if (state.currentScale >= 4) return;
                    state.currentScale += 0.25;
                    this.updateZoom();
                },
                zoomOut() {
                    if (state.currentScale <= 0.5) return;
                    state.currentScale -= 0.25;
                    this.updateZoom();
                },
                updateZoom() {
                    this.zoomPercent = Math.round(state.currentScale / 1.5 * 100);
                    reRender();
                },
                savePdf() { saveAnnotation(); }
            }));
        });

        // ── Drawing ──
        function bindDraw(canvas, pageNum) {
            const ctx = canvas.getContext('2d');
            let drawing = false;

            function pos(e) {
                const r = canvas.getBoundingClientRect();
                const t = e.touches?.[0];
                return { x: (t||e).clientX - r.left, y: (t||e).clientY - r.top };
            }
            function down(e) {
                drawing = true;
                state.modified[pageNum] = true;
                const p = pos(e);
                ctx.beginPath();
                ctx.arc(p.x, p.y, state.brushSize / 2, 0, Math.PI * 2);
                ctx.fillStyle = state.color;
                ctx.fill();
                ctx.beginPath();
                ctx.moveTo(p.x, p.y);
                e.preventDefault();
            }
            function move(e) {
                if (!drawing) return;
                const p = pos(e);
                ctx.lineTo(p.x, p.y);
                ctx.strokeStyle = state.color;
                ctx.lineWidth   = state.brushSize;
                ctx.lineCap     = 'round';
                ctx.lineJoin    = 'round';
                ctx.stroke();
                e.preventDefault();
            }
            function up(e) { drawing = false; e.preventDefault(); }

            canvas.addEventListener('mousedown', down);
            canvas.addEventListener('mousemove', move);
            canvas.addEventListener('mouseup',   up);
            canvas.addEventListener('mouseleave', up);
            canvas.addEventListener('touchstart', down, { passive: false });
            canvas.addEventListener('touchmove',  move, { passive: false });
            canvas.addEventListener('touchend',   up,   { passive: false });
        }

        // ── PDF Logic ──
        async function reRender() {
            if (!state.pdfDoc) return;
            // Show subtle loading on scroller
            document.getElementById('pdfPages').style.opacity = '0.5';
            
            for (const pg of state.pages) {
                const page = await state.pdfDoc.getPage(pg.pageNum);
                const vp   = page.getViewport({ scale: state.currentScale });

                // Snapshot drawing
                const tmp = document.createElement('canvas');
                tmp.width = pg.drawCanvas.width; tmp.height = pg.drawCanvas.height;
                tmp.getContext('2d').drawImage(pg.drawCanvas, 0, 0);

                // Resize
                pg.pdfCanvas.width = vp.width; pg.pdfCanvas.height = vp.height;
                pg.drawCanvas.width = vp.width; pg.drawCanvas.height = vp.height;
                pg.wrapper.style.width = vp.width + 'px'; pg.wrapper.style.height = vp.height + 'px';

                // Paint
                await page.render({ canvasContext: pg.pdfCanvas.getContext('2d'), viewport: vp }).promise;
                pg.drawCanvas.getContext('2d').drawImage(tmp, 0, 0, tmp.width, tmp.height, 0, 0, vp.width, vp.height);
                pg.viewport = vp;
            }
            document.getElementById('pdfPages').style.opacity = '1';
        }

        async function init() {
            const $loading = document.getElementById('loadingOverlay');
            const $error   = document.getElementById('errorOverlay');
            const $errText = document.getElementById('errorText');
            const $pages   = document.getElementById('pdfPages');

            try {
                const resp = await fetch(PDF_URL);
                if (!resp.ok) throw new Error(`Server PDF (HTTP ${resp.status})`);
                state.pdfBytes = await resp.arrayBuffer();

                state.pdfDoc = await pdfjsLib.getDocument({ data: new Uint8Array(state.pdfBytes) }).promise;

                for (let i = 1; i <= state.pdfDoc.numPages; i++) {
                    const page = await state.pdfDoc.getPage(i);
                    const vp   = page.getViewport({ scale: state.currentScale });

                    const wrap = document.createElement('div');
                    wrap.className = 'relative shadow-[0_20px_60px_-15px_rgba(0,0,0,0.5)] bg-white rounded-lg overflow-hidden transition-transform duration-500 hover:scale-[1.01]';
                    wrap.style.cssText = `width:${vp.width}px;height:${vp.height}px;`;

                    const cv = document.createElement('canvas');
                    cv.width = vp.width; cv.height = vp.height;
                    cv.style.cssText = 'position:absolute;top:0;left:0;';
                    await page.render({ canvasContext: cv.getContext('2d'), viewport: vp }).promise;

                    const dc = document.createElement('canvas');
                    dc.width = vp.width; dc.height = vp.height;
                    dc.style.cssText = 'position:absolute;top:0;left:0;cursor:crosshair;touch-action:none;';

                    wrap.append(cv, dc);
                    $pages.append(wrap);

                    state.pages.push({ pageNum: i, viewport: vp, drawCanvas: dc, pdfCanvas: cv, wrapper: wrap });
                    state.modified[i] = false;
                    bindDraw(dc, i);

                    if (i === 1) $loading.classList.add('opacity-0', 'pointer-events-none');
                }
                setTimeout(() => $loading.style.display = 'none', 500);

            } catch (err) {
                console.error(err);
                $loading.style.display = 'none';
                $errText.textContent = err.message;
                $error.classList.remove('hidden');
            }
        }

        async function saveAnnotation() {
            if (state.saving) return;
            const hasMod = Object.values(state.modified).some(m => m);
            if (!hasMod) return toast('error', 'Belum ada coretan untuk disimpan!');

            state.saving = true;
            const $btn = document.getElementById('saveBtn');
            const $text = document.getElementById('saveBtnText');
            $text.textContent = 'Menyimpan...';
            $btn.disabled = true;

            try {
                const { PDFDocument } = PDFLib;
                const doc   = await PDFDocument.load(state.pdfBytes);
                const pages = doc.getPages();

                for (const p of state.pages) {
                    if (!state.modified[p.pageNum]) continue;
                    const png = await doc.embedPng(p.drawCanvas.toDataURL('image/png'));
                    const pg  = pages[p.pageNum - 1];
                    pg.drawImage(png, { x: 0, y: 0, width: pg.getSize().width, height: pg.getSize().height });
                }

                const fd = new FormData();
                fd.append('annotated_pdf', new Blob([await doc.save()], { type: 'application/pdf' }), 'fixed.pdf');
                fd.append('_token', CSRF);

                const resp = await fetch(SAVE_URL, { 
                    method: 'POST', body: fd, headers: { 'Accept': 'application/json' }
                });
                const res = await resp.json();

                if (resp.ok && res.success) {
                    if (window.Swal) {
                        Swal.fire({
                            title: 'Tersimpan!',
                            text: 'Koreksi dokumen berhasil diperbarui.',
                            icon: 'success',
                            confirmButtonColor: '#f97316',
                            background: '#0f172a', color: '#fff'
                        }).then(() => window.location.href = res.redirect);
                    } else {
                        alert('Berhasil!'); window.location.href = res.redirect;
                    }
                } else throw new Error(res.error || 'Server error');

            } catch (err) {
                toast('error', err.message);
                $text.textContent = 'Simpan Hasil'; $btn.disabled = false; state.saving = false;
            }
        }

        // Page Indicator Auto-show on scroll
        const scroller = document.getElementById('pdfScroller');
        const indicator = document.getElementById('floatingPageInfo');
        const label = document.getElementById('pageNumLabel');
        let scrollTimer;

        scroller.addEventListener('scroll', () => {
            indicator.classList.remove('opacity-0', 'translate-y-4');
            clearTimeout(scrollTimer);
            
            // Calc current page
            const scrollPos = scroller.scrollTop + 200;
            const currentPage = state.pages.find(p => {
                const r = p.wrapper.getBoundingClientRect();
                const containerR = scroller.getBoundingClientRect();
                return (r.top - containerR.top) <= 300 && (r.bottom - containerR.top) >= 300;
            });
            if (currentPage) label.textContent = `HALAMAN ${currentPage.pageNum} / ${state.pages.length}`;

            scrollTimer = setTimeout(() => {
                indicator.classList.add('opacity-0', 'translate-y-4');
            }, 1500);
        });

        init();
    })();
    </script>
    @endpush
</x-app-layout>