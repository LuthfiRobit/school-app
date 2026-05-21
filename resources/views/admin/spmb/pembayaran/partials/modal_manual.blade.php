<div class="modal fade" id="manualModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true" x-ref="manualModal">
    <div class="modal-dialog">
        <form @submit.prevent="submitManualPayment">
            <div class="modal-content">
                <div class="modal-header bg-success text-white py-3">
                    <h5 class="modal-title text-white"><i class="ti ti-cash me-2"></i>Input Pembayaran Tunai (Cash)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-1"></i> Gunakan form ini untuk mencatat pembayaran tunai/manual secara langsung di sekolah. Pembayaran ini akan berstatus **dikonfirmasi** otomatis.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Tagihan/Invoice Pendaftar <span class="text-danger">*</span></label>
                        <select class="form-select" x-model="manualForm.invoice_id" @change="onInvoiceSelected($event)" required>
                            <option value="">-- Pilih Invoice --</option>
                            @foreach($unpaidInvoices as $inv)
                                @php
                                    $rem = max(0, $inv->total_amount - $inv->paid_amount);
                                @endphp
                                <option value="{{ $inv->id }}" data-rem-amount="{{ $rem }}">
                                    {{ $inv->invoice_number }} - {{ $inv->enrollment->applicant->full_name }} ({{ $inv->enrollment->spmbTrack->trackType->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3" x-show="manualForm.remAmount > 0">
                        <label class="form-label fw-bold">Sisa Tagihan Harus Dibayar</label>
                        <div class="p-3 bg-light rounded text-danger fw-bold font-monospace fs-5">
                            Rp <span x-text="formatRupiahSimple(manualForm.remAmount)"></span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nominal Pembayaran Tunai <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">Rp</span>
                            <input type="number" step="0.01" min="0.01" class="form-control font-monospace fw-bold" x-model="manualForm.amount" required>
                        </div>
                        <small class="text-muted d-block mt-1">Masukkan nominal uang tunai yang diterima.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan Pembayaran</label>
                        <textarea class="form-control" x-model="manualForm.notes" rows="2" placeholder="Contoh: Nomor kuitansi manual K-1234, atau memo tambahan"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" :disabled="loading">
                        <span x-show="!loading"><i class="ti ti-check me-1"></i>Simpan Pembayaran</span>
                        <span x-show="loading">Memproses...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
