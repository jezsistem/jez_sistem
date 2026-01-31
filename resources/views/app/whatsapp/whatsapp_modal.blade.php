<!-- Modal-->
<div class="modal fade" id="WaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_whatsapp" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Pesan Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Tipe</label>
                            <select class="form-control" id="wa_type" name="wa_type" required>
                                <option value="">- Pilih Tipe -</option>
                                <option value="people">Perorang</option>
                                <option value="all">Semua Customer</option>
                            </select>
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">No WA</label>
                            <input type="text" class="form-control" id="wa_phone" name="wa_phone" />
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Pesan</label>
                            <input type="text" class="form-control" id="wa_message" name="wa_message" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold"
                        data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-dark font-weight-bold" id="save_whatsapp_btn">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- ===================== MODAL ADD JOB ===================== -->
<div class="modal fade" id="addJobModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Job Broadcast WA</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form id="addJobForm">
                @csrf
                <div class="modal-body">

                    <div class="form-group">
                        <label>Nama Job</label>
                        <input type="text" name="job_name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Tanggal & Jam Mulai</label>
                        <input type="datetime-local" name="start_at" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Tanggal & Jam Selesai</label>
                        <input type="datetime-local" name="end_at" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Interval Pengiriman (Dalam jam)</label>
                        <input type="number" name="interval_hours" class="form-control" required min="1">
                    </div>

                    <div class="form-group">
                        <label>Jumlah Pesan Dalam 1 Batch</label>
                        <input type="number" name="batch_size" class="form-control" required min="1">
                    </div>

                    <div class="form-group">
                        <label>Konten Pesan</label>
                        <textarea name="message" class="form-control" rows="4" required></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">Simpan Job</button>
                </div>
            </form>

        </div>
    </div>
</div>
