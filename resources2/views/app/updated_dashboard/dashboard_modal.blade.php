<!-- Modal-->
<div class="modal fade" id="ActivityModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">User Activity Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <!--begin: Datatable-->
                <input type="search" class="form-control form-control-sm col-6" id="user_activity_search" placeholder="Cari user / aktifitas"/><br/>
                <table class="table table-hover table-checkable" id="Activitytb">
                    <thead class="bg-primary text-light">
                        <tr>
                            <th class="text-light">No</th>
                            <th class="text-light">Store</th>
                            <th class="text-light">Nama User</th>
                            <th class="text-light">Aktifitas</th>
                            <th class="text-light">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <!--end: Datatable-->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="LoadingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <center><h5 class="modal-title text-light" id="exampleModalLabel">Tunggu 1-5 detik yah boss, lagi diproses</h5></center>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->
