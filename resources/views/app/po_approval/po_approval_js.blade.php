<script>
    var approval = '';

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var po_approval_table = $('#APtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('ap_datatables') }}",
                data : function (d) {
                    d.search = $('#po_approval_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'st_name', name: 'st_name' },
            { data: 'po_invoice', name: 'po_invoice' },
            { data: 'poads_invoice_show', name: 'poads_invoice' },
            { data: 'invoice_date_show', name: 'invoice_date' },
            { data: 'receive_date_show', name: 'created_at' },
            { data: 'u_name', name: 'u_name' },
            { data: 'u_receive', name: 'u_receive', orderable: false },
            { data: 'qty', name: 'qty' },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        var apd_table = $('#APDtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brtl<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('apd_datatables') }}",
                data : function (d) {
                    d.poads_invoice = $('#invoice_label').text();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'created_at_show', name: 'created_at' },
            { data: 'poads_invoice', name: 'poads_invoice' },
            { data: 'br_name', name: 'br_name' },
            { data: 'p_name', name: 'p_name' },
            { data: 'p_color', name: 'p_color' },
            { data: 'sz_name', name: 'sz_name' },
            { data: 'stkt_name', name: 'stkt_name' },
            { data: 'poads_qty', name: 'poads_qty' },
            { data: 'poads_purchase_price', name: 'poads_purchase_price' },
            { data: 'poads_total_price', name: 'poads_total_price' },
            { data: 'delete', name: 'poads_total_price' },
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

        po_approval_table.buttons().container().appendTo($('#po_approval_excel_btn' ));
        $('#po_approval_search').on('keyup', function() {
            po_approval_table.draw(false);
        });

        $('#APtb tbody').on('click', 'tr', function () {
            var id = po_approval_table.row(this).data().id;
            var poads_invoice = po_approval_table.row(this).data().poads_invoice;
            var u_id_approve = po_approval_table.row(this).data().u_id_approve;
            approval = po_approval_table.row(this).data().u_receive;
            jQuery.noConflict();
            $('#ApproveModal').modal('show');
            $('#invoice_label').text(poads_invoice.replace("&amp;", "&"));
            apd_table.draw();
        });

        $(document).delegate('#delete_poads', 'click', function(e){
            e.preventDefault();
            var id = $(this).attr('data-id');
            swal({
                title: "Hapus..?",
                text: "Yakin hapus data ini ?",
                icon: "warning",
                buttons: [
                    'Batalkan',
                    'Hapus'
                ],
                dangerMode: true,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        data: {_id:id},
                        dataType: 'json',
                        url: "{{ url('dl_poads_revision')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                apd_table.draw(false);
                                swal("Berhasil", "Data berhasil dihapus", "success");
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#approve_btn').on('click', function(){
            if (approval != 'Menunggu Approval') {
                swal('Sudah Approve', 'Invoice ini sudah diapprove', 'warning');
                return false;
            }
            swal({
                title: "Approve..?",
                text: "Yakin approve ?",
                icon: "warning",
                buttons: [
                    'Batalkan',
                    'Approve'
                ],
                dangerMode: false,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        data: {invoice:$('#invoice_label').text()},
                        dataType: 'json',
                        url: "{{ url('apd_approve')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                $('#ApproveModal').modal('hide');
                                po_approval_table.draw(false);
                                swal("Berhasil", "Data berhasil diapprove", "success");
                            } else {
                                swal('Gagal', 'Gagal approve data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

    });
</script>
