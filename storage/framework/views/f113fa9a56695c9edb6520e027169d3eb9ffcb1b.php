
<!-- Modal-->
<div class="modal fade" id="PoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">PO <span id="po_invoice_label"></span></h5>
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
                        <thead class="bg-light text-dark">
                            <tr>
                                <th class="text-dark">No</th>
                                <th class="text-dark">Artikel</th>
                                <th class="text-dark">Qty PO</th>
                                <th class="text-dark">Qty Terima</th>
                                <th class="text-dark">Total PO</th>
                                <th class="text-dark">Total Terima</th>
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
<!-- /Modal --><?php /**PATH /home/jezpro.id/public_html/resources/views/app/report/po_receive/po_receive_modal.blade.php ENDPATH**/ ?>