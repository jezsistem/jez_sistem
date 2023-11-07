<!-- Modal-->
<div class="modal fade" id="ActivityModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">User Activity Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <!--begin: Datatable-->
                <input type="search" class="form-control  col-6" id="user_activity_search" placeholder="Cari user / aktifitas"/><br/>
                <table class="table table-hover table-checkable" id="Activitytb">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark">Store</th>
                            <th class="text-dark">Nama User</th>
                            <th class="text-dark">Aktifitas</th>
                            <th class="text-dark">Waktu</th>
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
            <div class="modal-header bg-light">
                <center><h5 class="modal-title text-dark" id="exampleModalLabel">Tunggu 1-5 detik yah boss, lagi diproses</h5></center>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->
