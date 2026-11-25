<!-- Modal Aturan Penilaian Skor Host -->
<div class="modal-overlay" id="scoringRulesModal" tabindex="-1" role="dialog" aria-labelledby="scoringRulesTitle" aria-hidden="true">
    <div class="modal" style="max-width: 520px; width: 95%; max-height: 90vh; display: flex; flex-direction: column; border-radius: 12px; box-shadow: var(--shadow-lg, 0 10px 25px -5px rgba(0,0,0,0.2));">
        <!-- Modal Header -->
        <div class="modal-header" style="padding: 0.75rem 1rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; background: var(--surface);">
            <div class="flex items-center gap-2">
                <div style="width: 28px; height: 28px; border-radius: 7px; background: rgba(99, 102, 241, 0.12); color: var(--primary); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="modal-title" id="scoringRulesTitle" style="font-size: 0.95rem; font-weight: 700; margin: 0; color: var(--text-primary); line-height: 1.2;">
                    Aturan Penilaian Skor Host
                </h3>
            </div>
            <button type="button" class="modal-close" onclick="closeModal(document.getElementById('scoringRulesModal'))" style="background: none; border: none; font-size: 1.35rem; line-height: 1; cursor: pointer; color: var(--text-muted); padding: 0 4px;">&times;</button>
        </div>

        <!-- Modal Body (Scrollable & Dense) -->
        <div class="modal-body" style="padding: 0.85rem 1rem; overflow-y: auto; flex: 1; background: var(--bg-primary);">
            <!-- Formula Summary Card -->
            <div style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.08) 0%, rgba(139, 92, 246, 0.05) 100%); border: 1px solid rgba(99, 102, 241, 0.22); border-radius: 9px; padding: 0.6rem 0.85rem; margin-bottom: 0.75rem; text-align: center;">
                <div style="font-size: 0.88rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.2rem; line-height: 1.35;">
                    Skor = <span style="color: #059669;">(Penjualan × 40%)</span> + <span style="color: #4f46e5;">(Durasi Live × 35%)</span> + <span style="color: #d97706;">(Konten × 25%)</span>
                </div>
                <div class="text-muted" style="font-size: 0.68rem; line-height: 1.3;">
                    *Skor dihitung relatif (0–100) terhadap capaian tertinggi host pada periode yang dipilih.
                </div>
            </div>

            <!-- 3 Bobot Penilaian (Padat & Profesional) -->
            <div style="margin-bottom: 0.75rem;">
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.35rem; display: flex; align-items: center; gap: 4px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="13" height="13" style="color: var(--primary);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                    </svg>
                    <span>Komponen Bobot</span>
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.4rem;" class="scoring-rules-grid-3">
                    <!-- Penjualan 40% -->
                    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 7px; padding: 0.45rem 0.55rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.2rem;">
                            <div class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="13" height="13" style="color: #059669; flex-shrink: 0;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                <strong style="font-size: 0.75rem; color: var(--text-primary);">Penjualan</strong>
                            </div>
                            <span class="badge badge-success" style="font-size: 0.65rem; padding: 0.1rem 0.35rem; font-weight: 700;">40%</span>
                        </div>
                        <div style="font-size: 0.67rem; color: var(--text-muted); line-height: 1.25;">
                            Total pcs barang terjual saat live.
                        </div>
                    </div>

                    <!-- Durasi Live 35% -->
                    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 7px; padding: 0.45rem 0.55rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.2rem;">
                            <div class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="13" height="13" style="color: #4f46e5; flex-shrink: 0;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <strong style="font-size: 0.75rem; color: var(--text-primary);">Durasi Live</strong>
                            </div>
                            <span class="badge" style="background: rgba(99, 102, 241, 0.12); color: #4f46e5; font-size: 0.65rem; padding: 0.1rem 0.35rem; font-weight: 700;">35%</span>
                        </div>
                        <div style="font-size: 0.67rem; color: var(--text-muted); line-height: 1.25;">
                            Total menit/jam live tervalidasi.
                        </div>
                    </div>

                    <!-- Konten 25% -->
                    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 7px; padding: 0.45rem 0.55rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.2rem;">
                            <div class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="13" height="13" style="color: #d97706; flex-shrink: 0;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <strong style="font-size: 0.75rem; color: var(--text-primary);">Konten</strong>
                            </div>
                            <span class="badge" style="background: rgba(245, 158, 11, 0.12); color: #d97706; font-size: 0.65rem; padding: 0.1rem 0.35rem; font-weight: 700;">25%</span>
                        </div>
                        <div style="font-size: 0.67rem; color: var(--text-muted); line-height: 1.25;">
                            Total video promosi (VT &amp; live cut).
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tingkatan / Tier Host (Grid Compact) -->
            <div style="margin-bottom: 0.75rem;">
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.35rem; display: flex; align-items: center; gap: 4px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="13" height="13" style="color: var(--primary);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                    <span>Kategori Tier Peringkat</span>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 0.35rem;">
                    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 6px; padding: 0.35rem 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                        <span class="badge badge-diamond" style="font-size: 0.67rem; font-weight: 700;">Diamond Host</span>
                        <strong style="font-size: 0.72rem; color: var(--primary);">≥ 85.0</strong>
                    </div>
                    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 6px; padding: 0.35rem 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                        <span class="badge badge-gold" style="font-size: 0.67rem; font-weight: 700;">Gold Star</span>
                        <strong style="font-size: 0.72rem; color: #b45309;">70 – 84.9</strong>
                    </div>
                    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 6px; padding: 0.35rem 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                        <span class="badge badge-silver" style="font-size: 0.67rem; font-weight: 700;">Silver Pro</span>
                        <strong style="font-size: 0.72rem; color: #475569;">50 – 69.9</strong>
                    </div>
                    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 6px; padding: 0.35rem 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                        <span class="badge badge-bronze" style="font-size: 0.67rem; font-weight: 700;">Bronze Host</span>
                        <strong style="font-size: 0.72rem; color: #c2410c;">25 – 49.9</strong>
                    </div>
                    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 6px; padding: 0.35rem 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                        <span class="badge badge-secondary" style="font-size: 0.67rem; font-weight: 700;">Rising Star</span>
                        <strong style="font-size: 0.72rem; color: var(--text-muted);">&lt; 25.0</strong>
                    </div>
                </div>
            </div>

            <!-- Penghargaan Kategori / Awards (Kompak 2x2) -->
            <div>
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.35rem; display: flex; align-items: center; gap: 4px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="13" height="13" style="color: #b45309;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    <span>Penghargaan Khusus</span>
                </div>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.35rem; font-size: 0.68rem;">
                    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 6px; padding: 0.35rem 0.5rem; display: flex; align-items: center; gap: 5px;">
                        <span style="font-weight: 700; color: #b45309;">MVP:</span>
                        <span style="color: var(--text-secondary);">Skor total tertinggi</span>
                    </div>
                    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 6px; padding: 0.35rem 0.5rem; display: flex; align-items: center; gap: 5px;">
                        <span style="font-weight: 700; color: var(--primary);">Top Jam:</span>
                        <span style="color: var(--text-secondary);">Total live terlama</span>
                    </div>
                    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 6px; padding: 0.35rem 0.5rem; display: flex; align-items: center; gap: 5px;">
                        <span style="font-weight: 700; color: #059669;">Top Sales:</span>
                        <span style="color: var(--text-secondary);">Penjualan pcs terbanyak</span>
                    </div>
                    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 6px; padding: 0.35rem 0.5rem; display: flex; align-items: center; gap: 5px;">
                        <span style="font-weight: 700; color: #d97706;">Top Konten:</span>
                        <span style="color: var(--text-secondary);">Produksi konten terbanyak</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer" style="padding: 0.5rem 1rem; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; background: var(--surface);">
            <button type="button" class="btn btn-primary btn-sm" onclick="closeModal(document.getElementById('scoringRulesModal'))" style="height: 30px; padding: 0 1rem; font-size: 0.78rem; font-weight: 600;">
                Mengerti &amp; Tutup
            </button>
        </div>
    </div>
</div>

<style>
@media (max-width: 480px) {
    .scoring-rules-grid-3 {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
    if (typeof window.openModal !== 'function') {
        window.openModal = function(id) {
            const m = document.getElementById(id);
            if (m) {
                m.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        };
    }
    if (typeof window.closeModal !== 'function') {
        window.closeModal = function(el) {
            if (el) {
                el.classList.remove('active');
                document.body.style.overflow = '';
            }
        };
    }
</script>
