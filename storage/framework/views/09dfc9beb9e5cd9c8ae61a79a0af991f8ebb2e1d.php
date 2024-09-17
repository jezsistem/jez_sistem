<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var wr_table = $('#Wrtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('wr_datatables')); ?>",
                data : function (d) {
                    d.search = $('#wr_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'ur_name', name: 'ur_name' },
            { data: 'ur_product', name: 'ur_product' },
            { data: 'ur_size', name: 'ur_size' },
            { data: 'ur_phone', name: 'ur_phone' },
            { data: 'ur_ip', name: 'ur_ip' },
            { data: 'created_at_show', name: 'created_at' },
            ], 
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        wr_table.buttons().container().appendTo($('#wr_excel_btn' ));
        $('#wr_search').on('keyup', function() {
            wr_table.draw();
        });

    });
</script><?php /**PATH C:\laragon\www\JEZ_S7\JEZ sistem\jez_sistem\resources\views/app/web_reminder/web_reminder_js.blade.php ENDPATH**/ ?>