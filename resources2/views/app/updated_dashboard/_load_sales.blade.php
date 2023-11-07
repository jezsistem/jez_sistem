<div class="col-xl-12">
    <!--begin::Tables Widget 9-->
    <div class="card card-xl-stretch mb-5 mb-xl-12">
        <!--begin::Header-->
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-3 mb-1">Nett Sales</span>
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
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="nett_sales_table">
                    <!--begin::Table head-->
                    <thead>
                        <tr class="fw-bold text-black">
                            <th class="min-w-200px">Date</th>
                            <th class="min-w-200px">INV</th>
                            <th class="min-w-150px">Item</th>
                            <th class="min-w-150px">Total</th>
                            <th class="min-w-150px">AdminCost</th>
                            <th class="min-w-150px">Nett Sales</th>
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
    var table = jQuery('#nett_sales_table').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        responsive: false,
        dom: 'flrt<"text-right"ip>',
        ajax: {
            url : "{{ url('get_nettsales') }}",
            data : function (d) {
                d.date = "{{ $data['date'] }}";
                d.division = "{{ $data['division'] }}";
            }
        },
        columns: [
        { data: 'created_at', name: 'created_at', searchable: false },
        { data: 'pos_invoice', name: 'pos_invoice' },
        { data: 'item', name: 'item', searchable: false },
        { data: 'item_total', name: 'item_total_1', searchable: false},
        { data: 'pos_admin_cost', name: 'pos_admin_cost', searchable: false },
        { data: 'total', name: 'total', searchable: false, orderable: false },
        ],
        order: [[0, 'desc']],
    });
</script>
