<div class="col-xl-12">
    <!--begin::Tables Widget 9-->
    <div class="card card-xl-stretch mb-5 mb-xl-12">
        <!--begin::Header-->
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-3 mb-1">Profit</span>
                <div><i class="text-dark"><small>* note: jika Download Excel kemudian melakukan SUM, silahkan hasil SUM dikurangi biaya admin dibawah, karena result belum termasuk admin</small></i></div>
                <div><i class="text-success"><small>* tabel bisa digeser jika terpotong</small></i></div>
                <div class="badge badge-dark my-2 fs-4" id="admin_cost_result"></div>
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
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="profits_table">
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
                            <th class="min-w-150px">Price</th>
                            <th class="min-w-150px">Sales Total</th>
                            <th class="min-w-150px">Purchase Price</th>
                            <th class="min-w-150px">Purchase Total</th>
                            <th class="min-w-150px">Profit</th>
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
    var table = jQuery('#profits_table').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        responsive: false,
        dom: 'lrt<"text-right"ip>',
        ajax: {
            url : "<?php echo e(url('get_profits')); ?>",
            data : function (d) {
                d.date = "<?php echo e($data['date']); ?>";
                
                d.division = "<?php echo e($data['division']); ?>";
            }
        },
        columns: [
        { data: 'created_at', name: 'created_at', searchable: false },
        { data: 'pos_invoice', name: 'pos_invoice' },
        { data: 'br_name', name: 'br_name' },
        { data: 'p_name', name: 'p_name' },
        { data: 'p_color', name: 'p_color' },
        { data: 'sz_name', name: 'sz_name' },
        { data: 'pos_td_qty', name: 'pos_td_qty', searchable: false },
        { data: 'pos_td_sell_price', name: 'pos_td_sell_price', searchable: false },
        { data: 'total', name: 'pos_td_total_price', searchable: false },
        { data: 'purchase', name: 'purchase', searchable: false },
        { data: 'purchase_total', name: 'purchase', searchable: false },
        { data: 'profit', name: 'profit', searchable: false, orderable: false },
        ],
        order: [[0, 'desc']],
    });
</script>
<?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/updated_dashboard/_load_profits.blade.php ENDPATH**/ ?>