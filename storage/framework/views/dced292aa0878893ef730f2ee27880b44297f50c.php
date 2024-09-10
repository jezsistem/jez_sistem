<div class="bg-light-primary rounded mb-4">
    <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1 row">
        <span class="btn-sm btn-primary col-sm-5 col-5 col-md-5 col-xl-4">
            <div class="font-weight-bold fs-4">[<span id="beginning_qty">calculating...</span>]</div>
            <div class="font-weight-bold fs-4">Rp <span id="beginning_value">calculating...</span></div>
            <div>Beginning</div>
        </span>
        <span class="btn-sm btn-primary col-sm-5 col-5 col-md-5 col-xl-4" style="text-align:right;">
            <div class="font-weight-bold fs-4">[<span id="cogs_qty">calculating...</span> %]</div>
            <div class="font-weight-bold fs-4">Rp <span id="cogs_value">calculating...</span></div>
            <div>COGS</div>
        </span>
    </div>
    <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1 row">
        <span class="btn-sm btn-primary col-sm-5 col-5 col-md-5 col-xl-4">
            <div class="font-weight-bold fs-4">[<span id="purchase_qty">calculating...</span>]</div>
            <div class="font-weight-bold fs-4">Rp <span id="purchase_value">calculating...</span></div>
            <div>Purchase</div>
        </span>
        <span class="btn-sm btn-primary col-sm-5 col-5 col-md-5 col-xl-4" style="text-align:right;">
            <div class="font-weight-bold fs-4">[<span id="sales_qty">calculating...</span>]</div>
            <div class="font-weight-bold fs-4">Rp <span id="sales_value">calculating...</span></div>
            <div>Sales</div>
        </span>
    </div>
    <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1 row">
        <span class="btn-sm btn-primary col-sm-5 col-5 col-md-5 col-xl-4">
            <div class="font-weight-bold fs-4">[<span id="profit_qty">calculating...</span>]</div>
            <div class="font-weight-bold fs-4">Rp <span id="profit_value">calculating...</span></div>
            <div>Profit</div>
        </span>
        <span class="btn-sm btn-primary col-sm-5 col-5 col-md-5 col-xl-4" style="text-align:right;">
            <div class="font-weight-bold fs-4">[<span id="ending_qty">calculating...</span>]</div>
            <div class="font-weight-bold fs-4">Rp <span id="ending_value">calculating...</span></div>
            <div>Ending</div>
        </span>
    </div>
    <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1 row">
        <span class="btn-sm btn-primary col-sm-5 col-5 col-md-5 col-xl-4">
            <div class="font-weight-bold fs-4">[<span id="transin_qty">calculating...</span>]</div>
            <div class="font-weight-bold fs-4">Rp <span id="transin_value">calculating...</span></div>
            <div>Transfer In</div>
        </span>
        <span class="btn-sm btn-primary col-sm-5 col-5 col-md-5 col-xl-4" style="text-align:right;">
            <div class="font-weight-bold fs-4">[<span id="transout_qty">calculating...</span>]</div>
            <div class="font-weight-bold fs-4">Rp <span id="transout_value">calculating...</span></div>
            <div>Transfer Out</div>
        </span>
    </div>
    <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1 row">
        <span class="btn-sm btn-primary col-sm-5 col-5 col-md-5 col-xl-4">
            <div class="font-weight-bold fs-4">[<span id="gid_qty">calculating...</span>]</div>
            <div class="font-weight-bold fs-4">Rp <span id="gid_value">calculating...</span></div>
            <div>GID</div>
        </span>
        <span class="btn-sm btn-primary col-sm-5 col-5 col-md-5 col-xl-4" style="text-align:right;">
            <div class="font-weight-bold fs-4">[<span id="git_qty">calculating...</span>]</div>
            <div class="font-weight-bold fs-4">Rp <span id="git_value">calculating...</span></div>
            <div>GIT</div>
        </span>
    </div>
</div>
<input type="search" class="form-control  col-6" id="data_search" placeholder="Cari article"/><br/>
<table class="table table-hover table-checkable" id="Datatb">
    <thead class="bg-light text-dark">
        <tr>
            <th class="text-dark">No</th>
            <th class="text-dark">Brand</th>
            <th class="text-dark">Beginning Qty</th>
            <th class="text-dark">Beginning</th>
            <th class="text-dark">Purchase Qty</th>
            <th class="text-dark">Purchase</th>
            <th class="text-dark">TransIn Qty</th>
            <th class="text-dark">TransIn</th>
            <th class="text-dark">TransOut Qty</th>
            <th class="text-dark">TransOut</th>
            <th class="text-dark">GID Qty</th>
            <th class="text-dark">GID</th>
            <th class="text-dark">GIT Qty</th>
            <th class="text-dark">GIT</th>
            <th class="text-dark">Sales Qty</th>
            <th class="text-dark">Sales</th>
            <th class="text-dark">Profit</th>
            <th class="text-dark">COGS</th>
            <th class="text-dark">Ending Qty</th>
            <th class="text-dark">Ending</th>
        </tr>
    </thead>
    <tbody>

    </tbody>
</table>

<script>

    function reloadSummary() {
        jQuery.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            type: "POST",
            data: {start:'<?php echo e($dt['start']); ?>', end:'<?php echo e($dt['end']); ?>', st_id:'<?php echo e($dt['st_id']); ?>'},
            dataType: 'json',
            url: "<?php echo e(url('get_asset_sales_summaries')); ?>",
            success: function(r) {
                if (r.status == 200){
                    jQuery('#beginning_qty').text(r.beginning_qty);
                    jQuery('#beginning_value').text(r.beginning_value);
                    jQuery('#cogs_qty').text(r.cogs_qty);
                    jQuery('#cogs_value').text(r.cogs_value);
                    jQuery('#purchase_qty').text(r.purchase_qty);
                    jQuery('#purchase_value').text(r.purchase_value);
                    jQuery('#sales_qty').text(r.sales_qty);
                    jQuery('#sales_value').text(r.sales_value);
                    jQuery('#profit_qty').text(r.profit_qty);
                    jQuery('#profit_value').text(r.profit_value);
                    jQuery('#ending_qty').text(r.ending_qty);
                    jQuery('#ending_value').text(r.ending_value);
                    jQuery('#transin_qty').text(r.transin_qty);
                    jQuery('#transin_value').text(r.transin_value);
                    jQuery('#transout_qty').text(r.transout_qty);
                    jQuery('#transout_value').text(r.transout_value);
                    jQuery('#gid_qty').text(r.gid_qty);
                    jQuery('#gid_value').text(r.gid_value);
                    jQuery('#git_qty').text(r.git_qty);
                    jQuery('#git_value').text(r.git_value);
                } else {
                    toast('E', 'E', 'error');
                }
            }
        });
        return false;
    }

    jQuery(document).ready(function() {
        reloadSummary();
        var data_table = jQuery('#Datatb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lBrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('ad_brand_datatables')); ?>",
                data : function (d) {
                    d.search = jQuery('#data_search').val();
                    d.st_id = '<?php echo e($dt['st_id']); ?>';
                    d.starts = '<?php echo e($dt['start']); ?>';
                    d.ends = '<?php echo e($dt['end']); ?>';
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'br_name', name: 'br_name' },
            { data: 'beginning', name: 'beginning', orderable:false },
            { data: 'beginning_value', name: 'beginning_value', orderable:false },
            { data: 'purchase_qty', name: 'purchase_qty', orderable:false },
            { data: 'purchase', name: 'purchase', orderable:false },
            { data: 'transin_qty', name: 'transin_qty', orderable:false },
            { data: 'transin', name: 'transin', orderable:false },
            { data: 'transout_qty', name: 'transout_qty', orderable:false },
            { data: 'transout', name: 'transout', orderable:false },
            { data: 'gid_qty', name: 'gid_qty', orderable:false },
            { data: 'gid', name: 'gid', orderable:false },
            { data: 'git_qty', name: 'git_qty', orderable:false },
            { data: 'git', name: 'git', orderable:false },
            { data: 'sales_qty', name: 'sales_qty', orderable:false },
            { data: 'sales', name: 'sales', orderable:false },
            { data: 'profit', name: 'profit', orderable:false },
            { data: 'cogs', name: 'cogs', orderable:false },
            { data: 'ending_qty', name: 'ending_qty', orderable:false },
            { data: 'ending', name: 'ending', orderable:false },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            language: {
                "lengthMenu": "_MENU_",
            },
            order: [[0, 'desc']],
        });

        jQuery('#data_search').on('keyup', function(e) {
            e.preventDefault();
            var val = jQuery(this).val();
            if (val.length > 2) {
                data_table.draw();
            } else if (val.length <= 0) {
                data_table.draw();
            }
        });
    });
</script><?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/asset_detail/_load_brand.blade.php ENDPATH**/ ?>