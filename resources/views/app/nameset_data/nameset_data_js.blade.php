<script>
    // $('body').addClass('kt-primary--minimize aside-minimize');

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var nameset_table = $('#NamesetDatatb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: '<"text-right"l>rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('nameset_datatables') }}",
                data : function (d) {
                    d.search = $('#nameset_search').val();
                    d.status = $('#status_filter').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'ptd_id', searchable: false},
            { data: 'pos_invoice', name: 'pos_invoice' },
            { data: 'stt_name', name: 'stt_name' },
            { data: 'article', name: 'article' },
            { data: 'pos_created', name: 'pos_created' },
            { data: 'pos_note', name: 'pos_note' },
            { data: 'action', name: 'action' },
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

        $('#nameset_search').on('change', function() {
            nameset_table.draw();
        });

        $(document).delegate('#nameset_finish_btn', 'click', function() {
            var ptd_id = $(this).attr('data-ptd_id');
            swal({
                title: "Selesai..?",
                text: "Yakin nameset sudah selesai ?",
                icon: "info",
                buttons: [
                    'Batal',
                    'Yakin'
                ],
                dangerMode: false,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    jQuery.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    jQuery.ajax({
                        type: "POST",
                        data: {_type:'finish_nameset', _ptd_id:ptd_id},
                        dataType: 'json',
                        url: "{{ url('update_data') }}",
                        success: function(r) {
                            if (r.status=='200') {
                                nameset_table.draw();
                                toast('Berhasil', 'Berhasil menyelesaikan nameset', 'success');
                            } else {
                                toast('Gagal', 'Gagal menyelesaikan nameset', 'warning');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        nameset_table.buttons().container().appendTo($('#stock_tracking_excel_btn' ));
        $('#nameset_search').on('keyup', function() {
            nameset_table.draw();
        });

        $('#NamesetDatatb tbody').on('click', 'tr', function () {
            var id = nameset_table.row(this).data().plst_id;
            jQuery.noConflict();
            //$('#HistoryModal').modal('show');
        });
    });
</script>