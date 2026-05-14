<!-- Modal Simulasi KPR -->
<div class="modal fade" id="kprSimulatorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-calculator-fill me-2"></i>Simulasi KPR</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formSimulasiKPR" action="{{ route('simulasi.kpr') }}" method="POST">
                    @csrf
                    <input type="hidden" id="tipeUnitId" name="unit_id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Harga Unit</label>
                            <input type="text" class="form-control" id="hargaUnitDisplay" readonly>
                            <input type="hidden" id="hargaUnit" name="harga_unit">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">DP (%)</label>
                            <input type="range" class="range-slider w-100" id="dpSlider" name="dp_persen" min="5" max="50" step="5" value="20">
                            <div class="d-flex justify-content-between">
                                <span>5%</span>
                                <span id="dpValue">20%</span>
                                <span>50%</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tenor (Tahun)</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="tahun-btn" data-tenor="5">5 Th</button>
                                <button type="button" class="tahun-btn" data-tenor="10">10 Th</button>
                                <button type="button" class="tahun-btn active-tahun" data-tenor="15">15 Th</button>
                                <button type="button" class="tahun-btn" data-tenor="20">20 Th</button>
                                <button type="button" class="tahun-btn" data-tenor="30">30 Th</button>
                            </div>
                            <input type="hidden" id="tenorInput" name="tenor" value="15">
                        </div>
                    </div>
                    
                    <div class="result-box mt-4" id="hasilSimulasi" style="display: none;">
                        <h6 class="mb-3">Hasil Simulasi KPR</h6>
                        <div class="row">
                            <div class="col-6">DP</div>
                            <div class="col-6 text-end" id="hasilDP">Rp 0</div>
                            <div class="col-6">Pokok KPR</div>
                            <div class="col-6 text-end" id="hasilPokok">Rp 0</div>
                            <div class="col-6">Angsuran/Bulan</div>
                            <div class="col-6 text-end fw-bold fs-5" id="hasilAngsuran">Rp 0</div>
                            <div class="col-6">Penghasilan Minimal</div>
                            <div class="col-6 text-end" id="hasilPenghasilan">Rp 0</div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>