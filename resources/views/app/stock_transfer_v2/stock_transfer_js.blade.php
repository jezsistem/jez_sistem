<script>
    function reloadPendingTransfer()
    {
        $.ajax({
            type: "GET",
            dataType: 'json',
            url: "{{ url('get_pending_stf_code')}}",
            success: function(r) {
                if (r.stf_code != '') {
                    $('#stf_code').text(r.stf_code);
                    return true;
                } else {
                    return false;
                }
            }
        });
    }

    $(document).ready(function() {
        // $('body').addClass('kt-primary--minimize aside-minimize');
        reloadPendingTransfer();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var transfer_bin_table = $('#TransferBintb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: '<"text-right"l>rt<"text-right"p>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('transfer_bin_datatables') }}",
                data : function (d) {
                    d.pl_id = $('#pl_id').val();
                    d.search = $('#article_search').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'pls_id', searchable: false},
                { data: 'br_name', name: 'br_name', orderable: false },
                { data: 'article', name: 'article', orderable: false },
                { data: 'qty', name: 'qty', orderable: false },
                { data: 'transfer', name: 'transfer', orderable: false },
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

        $('#article_search').on('keyup', function() {
            transfer_bin_table.draw();
        });

        var in_transfer_bin_table = $('#InTransferBintb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: '<"text-right"l>rt<"text-right"p>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('in_transfer_bin_datatables') }}",
                data : function (d) {
                    d.stf_code = $('#stf_code').text();
                    d.search = $('#in_transfer_search').val();
                }
            },
            columns: [
                { data: 'article', name: 'article', orderable: false },
                { data: 'pl_code', name: 'pl_code', orderable: false },
                { data: 'stfd_qty', name: 'stfd_qty', orderable: false },
                { data: 'st_start', name: 'st_start', orderable: false },
                { data: 'st_end', name: 'st_end', orderable: false },
                { data: 'status', name: 'status', orderable: false },
            ],
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            language: {
                "lengthMenu": "_MENU_",
            },
            order: [[0, 'desc']],
        });

        setTimeout(() => {
            if ($('#stf_code').text() != '') {
                in_transfer_bin_table.draw();
            }
        }, 2000);

        $('#article_search').on('keyup', function() {
            transfer_bin_table.draw();
        });

        $('#in_transfer_search').on('keyup', function() {
            in_transfer_bin_table.draw();
        });

        var transfer_history_table = $('#TransferHistorytb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: '<"text-right"l>rt<"text-right"p>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('transfer_history_datatables') }}",
                data : function (d) {
                    d.search = $('#history_search').val();
                    d.status = $('#status_filter').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'stf_id', searchable: false},
                { data: 'stf_code', name: 'stf_code' },
                { data: 'u_name', name: 'u_name', orderable: false },
                { data: 'qty', name: 'qty', orderable: false },
                { data: 'start_store', name: 'start_store', orderable: false },
                { data: 'end_store', name: 'end_store', orderable: false },
                { data: 'u_name_receive', name: 'u_name_receive', orderable: false },
                { data: 'stf_created', name: 'stf_created' },
                { data: 'stf_status', name: 'stf_status' },
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

        $('#status_filter').on('change', function() {
            transfer_history_table.draw();
        });

        $('#history_search').on('keyup', function() {
            transfer_history_table.draw();
        });

        $('#pl_id').select2({
            width: "100%",
            dropdownParent: $('#pl_id_parent')
        });
        $('#pl_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#pl_id').on('change', function() {
            transfer_bin_table.draw();
        });

        $('#st_id_start').select2({
            width: "100%",
            dropdownParent: $('#st_id_start_parent')
        });
        $('#st_id_start').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#st_id_end').select2({
            width: "100%",
            dropdownParent: $('#st_id_end_parent')
        });
        $('#st_id_end').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#st_id_start').on('change', function() {
            var st_id = $('#st_id_start').val();
            $.ajax({
                type: "GET",
                data: {_st_id:st_id},
                dataType: 'html',
                url: "{{ url('reload_transfer_bin')}}",
                success: function(r) {
                    $('#pl_id').html(r);
                }
            });
        });

        $('#st_id_end').on('change', function() {
            var st_id_start = $('#st_id_start').val();
            var st_id_end = $(this).val();
            if (st_id_start == st_id_end) {
                swal('Tujuan Sama', 'Store awal dan store tujuan tidak boleh sama', 'warning');
                $(this).val('').trigger('change');
                return false;
            }
        });

        $(document).delegate('#cancel_transfer_item', 'click', function() {
            if ($('#transfer_done_btn').hasClass('d-none')) {
                swal('Dilarang', 'Anda tidak boleh melakukan tindakan ini, karena status sudah done / in progress', 'warning');
                return false;
            }
            var stfd_id = $(this).attr('data-stfd_id');
            var pst_id = $(this).attr('data-pst_id');
            var pl_id = $(this).attr('data-pl_id');
            var stfd_qty = $(this).attr('data-stfd_qty');
            //alert(stfd_id+' '+pst_id+' '+pl_id+' '+stfd_qty);
            swal({
                title: "Batal..?",
                text: "Batalkan item ini ?",
                icon: "info",
                buttons: [
                    'Tidak',
                    'Ya'
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
                        data: {_stfd_id:stfd_id, _pst_id:pst_id, _pl_id:pl_id, _stfd_qty:stfd_qty},
                        dataType: 'json',
                        url: "{{ url('cancel_transfer_item')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                transfer_bin_table.draw();
                                transfer_history_table.draw();
                                in_transfer_bin_table.draw();
                                swal("Berhasil", "Data berhasil ditransfer dan menunggu diterima ", "success");
                                //console.log(r.data);
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });


        $('#transfer_done_btn').on('click', function() {
            var stf_code = $('#stf_code').text();
            swal({
                title: "Selesai..?",
                text: "Yakin item sudah fix dan siap ditransfer ?",
                icon: "info",
                buttons: [
                    'Batal',
                    'Yakin'
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
                        data: {_stf_code:stf_code},
                        dataType: 'json',
                        url: "{{ url('stock_transfer_done')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                $('#stf_code').text('');
                                transfer_history_table.draw();
                                in_transfer_bin_table.draw();
                                swal("Berhasil", "Invoice transfer berhasil diterbitkan dan siap diterima", "success");
                            } else {
                                swal('Gagal', 'Masih ada item yang belum diambil oleh helper', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $(document).delegate('#transfer_qty', 'change', function(e) {
            var pls_qty = $(this).attr('data-qty');
            var qty = $(this).val();
            if (parseInt(qty) > parseInt(pls_qty)) {
                swal('Qty', 'Melebihi batas stok', 'warning');
                $(this).val('');
                return false;
            } else if (parseInt(qty) < 0) {
                swal('Qty', 'Tidak boleh minus', 'warning');
                $(this).val('');
                return false;
            }
        });

        $('#transfer_btn').on('click', function() {
            var st_start = $('#st_id_start option:selected').text();
            var st_end = $('#st_id_end option:selected').text();
            var bin = $('#pl_id option:selected').val();
            if (st_start == '- Store Awal -' || st_end == '- Store Tujuan -') {
                swal('Periksa Store', 'Silahkan periksa Store Awal dan Store Tujuan', 'warning');
                return false;
            }
            if (st_start == st_end) {
                swal('Periksa Store', 'Silahkan periksa Store Awal dan Store Tujuan', 'warning');
                return false;
            }
            if (bin == '') {
                swal('Pilih BIN', 'Silahkan pilih BIN untuk ditransfer', 'warning');
                return false;
            }
            swal({
                title: "Transfer..?",
                text: "Yakin transfer data dari store "+st_start+" menuju store "+st_end+" ?",
                icon: "info",
                buttons: [
                    'Batal',
                    'Yakin'
                ],
                dangerMode: false,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $(this).addClass('disabled');
                    var arr = [];
                    var i = 0;
                    var st_start = $('#st_id_start').val();
                    var st_end = $('#st_id_end').val();
                    var bin = $('#pl_id option:selected').val();
                    $('.transfer_qty').each(function() {
                        var pls_id = $(this).attr('data-pls_id');
                        var pst_id = $(this).attr('data-pst_id');
                        var pls_qty = $(this).attr('data-pls_qty');
                        var stf_qty = $(this).val();
                        if (stf_qty == '') {
                            return true;
                        }
                        arr[i++] = [pls_id, pst_id, pls_qty, stf_qty];
                    });
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        data: {_st_start:st_start, _st_end:st_end, _bin:bin, _arr:arr},
                        dataType: 'json',
                        url: "{{ url('stock_transfer_exec')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                if ($('#stf_code').text() == '') {
                                    $('#stf_code').text(r.code);
                                }
                                transfer_bin_table.draw();
                                transfer_history_table.draw();
                                in_transfer_bin_table.draw();
                                $(this).removeClass('disabled');
                                swal("Berhasil", "Data berhasil ditransfer dan menunggu diterima ", "success");
                            } else {
                                $(this).removeClass('disabled');
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $(document).delegate('#transfer_draft_btn', 'click', function() {
            var inv = $('#stf_code').text();
            if (inv == '') {
                swal('No Trf', 'Tidak ada nomor transfer yang aktif', 'warning');
                return false;
            }
            swal({
                title: "Draft..?",
                text: "Yakin simpan draft invoice "+inv+" ?",
                icon: "info",
                buttons: [
                    'Batal',
                    'Yakin'
                ],
                dangerMode: false,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $(this).addClass('disabled');
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        data: {inv:inv},
                        dataType: 'json',
                        url: "{{ url('stock_transfer_draft')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                $('#stf_code').text('');
                                in_transfer_bin_table.draw();
                                transfer_history_table.draw();
                                $(this).removeClass('disabled');
                                swal("Berhasil", "Data berhasil disimpan", "success");
                            } else {
                                $(this).removeClass('disabled');
                                swal('Gagal', 'Gagal simpan data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $(document).delegate('#transfer_cancel_btn', 'click', function() {
            var inv = $('#stf_code').text();
            if (inv == '') {
                swal('No Trf', 'Tidak ada nomor transfer yang aktif', 'warning');
                return false;
            }
            swal({
                title: "Delete..?",
                text: "Yakin delete invoice "+inv+" ?",
                icon: "warning",
                buttons: [
                    'Batal',
                    'Yakin'
                ],
                dangerMode: true,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $(this).addClass('disabled');
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        data: {inv:inv},
                        dataType: 'json',
                        url: "{{ url('stock_transfer_cancel')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                $('#stf_code').text('');
                                transfer_bin_table.draw();
                                in_transfer_bin_table.draw();
                                transfer_history_table.draw();
                                $(this).removeClass('disabled');
                                swal("Berhasil", "Data berhasil dibatalkan", "success");
                            } else {
                                $(this).removeClass('disabled');
                                swal('Gagal', 'Gagal batalkan data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $(document).delegate('#draft_btn', 'click', function() {
            var inv = $(this).attr('data-code');
            $('#stf_code').text(inv);
            in_transfer_bin_table.draw();
        });

        $(document).delegate('#view_btn', 'click', function() {
            var inv = $(this).attr('data-code');
            $('#stf_code').text(inv);
            in_transfer_bin_table.draw(false);
            $('#transfer_cancel_btn').addClass('d-none');
            $('#transfer_draft_btn').addClass('d-none');
            $('#transfer_done_btn').addClass('d-none');
        });

        $(document).delegate('#f_import', 'submit', function(e) {
            e.preventDefault();
            var template_size = document.getElementById('template').files.length;
            var formData = new FormData(this);
            if (template_size < 1) {
                swal('Template', 'Silahkan pilih template yang akan diupload', 'warning');
                return false;
            }
            swal({
                title: "Import..?",
                text: "Yakin import template ini ?",
                icon: "warning",
                buttons: [
                    'Batal',
                    'Yakin'
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
                        url: "{{ url('stock_transfer_import')}}",
                        data: formData,
                        dataType: 'json',
                        cache:false,
                        contentType: false,
                        processData: false,
                        success: function(r) {
                            if (r.status == '200'){
                                $('#f_import')[0].reset();
                                transfer_history_table.draw();
                                swal("Berhasil", "Data berhasil import", "success");
                            } else {
                                swal('Gagal', 'Gagal import data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

    });
</script>