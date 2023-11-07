
<!-- Modal-->
<div class="modal fade" id="PoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">PO <span id="po_invoice_label"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body table-responsive">
                    <!--begin: Datatable-->
                    <input type="hidden" id="_po_id" />
                    <input type="search" class="form-control form-control-sm" id="po_search" placeholder="Cari artikel" style="border:1px solid black; padding:20px;"/><br/>
                    <table class="table table-hover table-checkable" id="Potb">
                        <thead class="bg-primary text-light">
                            <tr>
                                <th class="text-light">No</th>
                                <th class="text-light">Artikel</th>
                                <th class="text-light">Qty PO</th>
                                <th class="text-light">Qty Terima</th>
                                <th class="text-light">Total PO</th>
                                <th class="text-light">Total Terima</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    <!--end: Datatable-->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->