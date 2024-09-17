<script>
    function saveAdjustment(pls_id, index, pst_id, pls_qty, pl_id)
    {
        var ba_qty = $('.adjustment_qty'+index).val();
        var ba_note = $('.adjustment_note'+index).val();
        if  (ba_qty == '') {
            return true;
        }
        //alert(pls_id+' | '+pl_id_end+' | '+pmt_qty);
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {_pls_id:pls_id, _ba_qty:ba_qty, _ba_note:ba_note, _pst_id:pst_id, _pls_qty:pls_qty, _pl_id:pl_id},
            dataType: 'json',
            url: "<?php echo e(url('sv_adjustment')); ?>",
            success: function(r) {
                if (r.status == '200'){

                } else if (r.status == '420') {

                } else {
                    toast('Error', 'Ada error, info ke programmer', 'error');
                }
            }
        });
        return false;
    }

    function reloadLocation()
    {
        $.ajax({
            type: "GET",
            dataType: 'html',
            url: "<?php echo e(url('reload_location')); ?>",
            success: function(r) {
                $('#pl_id').html(r);
            }
        });
    }

    $(document).ready(function() {
        // $('body').addClass('kt-primary--minimize aside-minimize');

        var article_table = $('#Articletb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            bPaginate: true,
            dom: 'lBrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('article_adjustment_datatables')); ?>",
                data : function (d) {
                    d.search = $('#article_search').val();
                    if ($('#_mode').val() == 'adjust') {
                        d.pl_id = $('#pl_id').val();
                    } else {
                        d.pl_id = $('#pl_id_hidden').val();
                    }
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'pls_id', searchable: false},
            { data: 'article', name: 'article', orderable: false },
            { data: 'qty', name: 'qty', orderable: false },
            { data: 'action', name: 'action', orderable: false },
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
            article_table.draw();
        });

        var adjustment_history_table = $('#AdjustmentHistorytb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lB<"text-right"l>rt<"text-right"p>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('adjustment_history_datatables')); ?>",
                data : function (d) {
                    d.search = $('#history_search').val();
                    d.st_id = $('#st_id_filter').val();
                    d.adjustment_date = $('#adjustment_date').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'ba_id', searchable: false},
            { data: 'ba_code', name: 'ba_code' },
            { data: 'st_name', name: 'st_name' },
            { data: 'pl_code', name: 'pl_code' },
            { data: 'u_name', name: 'u_name' },
            { data: 'br_name', name: 'br_name'},
            { data: 'p_name', name: 'p_name'},
            { data: 'p_color', name: 'p_color'},
            { data: 'sz_name', name: 'sz_name'},
            { data: 'ba_created', name: 'ba_created', orderable: false },
            { data: 'ba_old_qty', name: 'ba_old_qty', orderable: false },
            { data: 'ba_new_qty', name: 'ba_new_qty', orderable: false },
            { data: 'adjust', name: 'adjust', orderable: false },
            { data: 'ba_note', name: 'ba_note', orderable: false },
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

        $('#history_search').on('keyup', function() {
            adjustment_history_table.draw();
        });

        var validated_table = $('#Validatedtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lB<"text-right"l>rt<"text-right"p>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('validated_datatables')); ?>",
                data : function (d) {
                    //d.search = $('#history_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'pl_id', searchable: false},
            { data: 'pl_code', name: 'pl_code' },
            { data: 'u_name', name: 'u_name' },
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

        var not_validated_table = $('#NotValidatedtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'lB<"text-right"l>rt<"text-right"p>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('not_validated_datatables')); ?>",
                data : function (d) {
                    //d.search = $('#history_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'pl_code', name: 'pl_code' },
            { data: 'pl_name', name: 'pl_name' },
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

        $('#pl_id').select2({
            width: "100%",
            dropdownParent: $('#pl_id_parent')
        });
        $('#pl_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#pst_id').select2({
            width: "100%",
            dropdownParent: $('#pst_id_parent')
        });
        $('#pst_id').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('#pl_id').on('change', function() {
            jQuery.noConflict();
            var label = $('option:selected', this).text();
            var pl_id = $(this).val();
            $('#_pl_id').val(pl_id);
            $('#_mode').val('adjust');
            $('#AdjustmentModal').modal('show');
            $('#adjustment_label').text(label);
            article_table.draw();
        });

        $(document).delegate('#validated_bin', 'click', function() {
            jQuery.noConflict();
            var pl_id = $(this).attr('data-pl_id');
            var label = $(this).attr('data-pl_code');
            //alert(pl_id);
            $('#_mode').val('readjust');
            $('#pl_id_hidden').val(pl_id);
            $('#_pl_id').val(pl_id);
            $('#AdjustmentModal').modal('show');
            $('#adjustment_label').text(label);
            article_table.draw();
        });

        $('#save_adjustment_btn').on('click', function(e) {
            e.preventDefault();
            swal({
                title: "Adjustment..?",
                text: "Yakin adjust data ?",
                icon: "info",
                buttons: [
                    'Batal',
                    'Yakin'
                ],
                dangerMode: false,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $(this).prop('disabled', true);
                    var total_row = $('input[data-adjustment-qty]').length;
                    var finish = '';
                    //alert(total_row);
                    for (let i = 1; i <= total_row+1; ++i) {
                        $('#saveAdjustment'+i).trigger('click');
                        if (i == total_row) {
                            finish = 'true';
                        }
                    }
                    if (finish == 'true') {
                        validated_table.draw();
                        not_validated_table.draw();
                        adjustment_history_table.draw();
                        $('#AdjustmentModal').modal('hide');
                        reloadLocation();
                        $(this).prop('disabled', false);
                        toast('Berhasil', 'Berhasil adjustment', 'success');
                    }
                }
            })
        });

        $('#product_name_input').on('keyup', function(){ 
            var query = $(this).val();
            if($.trim(query) != '' || $.trim(query) != null) {
                jQuery.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url:"<?php echo e(url('autocomplete_article')); ?>",
                    method:"POST",
                    data:{query:query},
                    success:function(data){
                        $('#itemList').fadeIn();
                        $('#itemList').html(data);
                    }
                });
            } else {
                $('#itemList').fadeOut();
            }
        });

        jQuery(document).on('click', '#add_to_item_list', function(e) {
            var pst_id = $(this).attr('data-pst_id');
            var p_name = $(this).attr('data-p_name');
            //alert(p_name);
            $('#pst_id_hidden').val(pst_id);
            $('#product_name_input').val(p_name);
        });

        jQuery(document).on('click', 'body', function(e) {
            jQuery('#itemList').fadeOut();
        });

        $('#f_article').on('submit', function(e) {
            e.preventDefault();
            swal({
                title: "Tambah Artikel..?",
                text: "Menambah artikel akan menambah jumlah QTY artikel pada bin ini dan juga QTY secara global tanpa melewati PO ?",
                icon: "info",
                buttons: [
                    'Batal',
                    'Yakin'
                ],
                dangerMode: false,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $('#save_add_article_btn').prop('disabled', true);
                    var pst_id = $('#pst_id_hidden').val();
                    var pls_qty = $('#pls_qty').val();
                    var article_note = $('#article_note').val();
                    var bin_label = $('#adjustment_label').text().split(" ");
                    var bin = $('#_pl_id').val();
                    //alert(pst_id+' '+pls_qty+' '+bin);
                    $.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        dataType: 'json',
                        data: {_pst_id:pst_id, _pls_qty:pls_qty, _bin:bin, _article_note:article_note},
                        url: "<?php echo e(url('add_article_adjustment')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                validated_table.draw();
                                not_validated_table.draw();
                                reloadLocation();
                                article_table.draw();
                                adjustment_history_table.draw();
                                $('#f_article')[0].reset();
                                $('#pst_id').val('').trigger('change');
                                $('#AddArticleModal').modal('hide');
                                $('#save_add_article_btn').prop('disabled', false);
                                swal('Berhasil', 'Artikel baru berhasil ditambah pada bin ini', 'success');
                            } else {
                                $('#save_add_article_btn').prop('disabled', false);
                                toast('Error', 'Ada error, info ke programmer', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#add_article_btn').on('click', function(e) {
            $('#AddArticleModal').modal('show');
        });

        $('#adjustment_finish_btn').on('click', function(e) {
            e.preventDefault();
            swal({
                title: "Selesai Adjustment..?",
                text: "Yakin sudah selesai?",
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
                        dataType: 'json',
                        url: "<?php echo e(url('finish_adjustment')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                validated_table.draw();
                                not_validated_table.draw();
                                reloadLocation();
                                swal('Selesai', 'Adjustment telah diselesaikan, kode transaksi adjustment akan diperbaharui', 'success');
                            } else {
                                toast('Error', 'Ada error, info ke programmer', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#st_id_filter').on('change', function() {
            adjustment_history_table.draw();
        });

        jQuery.noConflict();
        var picker = $('#kt_dashboard_daterangepicker');
        if ($('#kt_dashboard_daterangepicker').length == 0) {
            return;
        }
        var start = moment();
        var end = moment();

        function cb(start, end, label) {
            var title = '';
            var range = '';
            var hidden_range = '';
            var st_id = $('#st_id_filter').val();

            if ((end - start) < 100 || label == 'Hari Ini') {
                title = 'Hari Ini:';
                range = start.format('DD MMM YYYY');
                hidden_range = start.format('YYYY-MM-DD');
            } else if (label == 'Kemarin') {
                title = 'Kemarin:';
                range = start.format('DD MMM YYYY');
                hidden_range = start.format('YYYY-MM-DD');
            } else {
                range = start.format('DD MMM YYYY') + ' - ' + end.format('DD MMM YYYY');
                hidden_range = start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD');
            }
            //alert(range+' | '+hidden_range);
            $('#adjustment_date').val(hidden_range);
            $('#kt_dashboard_daterangepicker_date').html(range);
            $('#kt_dashboard_daterangepicker_title').html(title);
            adjustment_history_table.draw();
            //alert(hidden_range);
        }

        picker.daterangepicker({
            direction: KTUtil.isRTL(),
            startDate: start,
            endDate: end,
            opens: 'center',
            applyClass: 'btn-primary',
            cancelClass: 'btn-light-primary',
            ranges: {
                'Hari Ini': [moment(), moment()],
                'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
                'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                'Bulan Kemarin': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);
        cb(start, end, '');
    });
</script><?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/adjustment/adjustment_js.blade.php ENDPATH**/ ?>