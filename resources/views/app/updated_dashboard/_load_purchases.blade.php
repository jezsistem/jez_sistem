<div class="col-xl-12">
    <!--begin::Tables Widget 9-->
    <div class="card card-xl-stretch mb-5 mb-xl-12">
        <!--begin::Header-->
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-3 mb-1">Purchase Order</span>
                <div><i class="text-success"><small>* tabel bisa digeser jika terpotong</small></i></div>
            </h3>
            <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover" title="Detail">
                <a href="#" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#kt_modal_invite_friends" id="export">
                <i class="fa fa-download"></i> Download Excel</a>
            </div>
        </div>
        <!--end::Header-->
        <!--begin::Body-->
        <div class="card-body py-3">
            <!--begin::Table container-->
            <div class="table-responsive">
                <!--begin::Table-->
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="purchases_table">
                    <!--begin::Table head-->
                    <thead>
                        <tr class="fw-bold text-black">
                            <th class="min-w-150px">Date</th>
                            <th class="min-w-150px">INV</th>
                            <th class="min-w-150px">Brand</th>
                            <th class="min-w-150px">Article</th>
                            <th class="min-w-150px">Color</th>
                            <th class="min-w-150px">Size</th>
                            <th class="min-w-150px">Qty</th>
                            <th class="min-w-150px">Purchase Price</th>
                            <th class="min-w-150px">Total</th>
                        </tr>
                    </thead>
                    <!--end::Table head-->
                    <!--begin::Table body-->
                    <tbody>

                    </tbody>
                    <!--end::Table body-->
                </table>
                <!--end::Table-->
            </div>
            <!--end::Table container-->
        </div>
        <!--begin::Body-->
    </div>
    <!--end::Tables Widget 9-->
</div>
<!--end::Col-->
<script>
    var table = jQuery('#purchases_table').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        responsive: false,
        dom: 'lrt<"text-right"ip>',
        ajax: {
            url : "{{ url('get_purchases') }}",
            data : function (d) {
                d.date = "{{ $data['date'] }}";
                
            }
        },
        columns: [
        { data: 'created_at', name: 'created_at', searchable: false },
        { data: 'po_invoice', name: 'po_invoice' },
        { data: 'br_name', name: 'br_name' },
        { data: 'p_name', name: 'p_name' },
        { data: 'p_color', name: 'p_color' },
        { data: 'sz_name', name: 'sz_name' },
        { data: 'qty', name: 'qty', searchable: false },
        { data: 'poads_purchase_price', name: 'poads_purchase_price' },
        { data: 'total', name: 'total' },
        ],
        order: [[0, 'desc']],
    });
</script>
