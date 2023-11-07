<script>
    var br_id = [];
    var psc_id = [];
    var sa_id = '';
    var done = 0;

    $(document).delegate('#custom_mode', 'click', function(e) {
        if ($(this).prop('checked')) {
            $('#custom_panel').removeClass('d-none');
            br_id = [];
            $('.brand-item').remove();
            psc_id = [];
            $('.sub-category-item').remove();
        } else {
            $('#custom_panel').addClass('d-none');
        }
    });

    function loadBrand(query)
    {
        if($.trim(query) != '' || $.trim(query) != null) {
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:"{{  url('fetch_start_scan_adjustment_brand') }}",
                method:"POST",
                data:{query:query},
                success:function(data){
                    $('#brandList').fadeIn();
                    $('#brandList').html(data);
                }
            });
        } else {
            $('#brandList').fadeOut();
        }
    }

    function loadSubCategory(query)
    {
        if($.trim(query) != '' || $.trim(query) != null) {
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url:"{{  url('fetch_start_scan_adjustment_sub_category') }}",
                method:"POST",
                data:{query:query},
                success:function(data){
                    $('#subCategoryList').fadeIn();
                    $('#subCategoryList').html(data);
                }
            });
        } else {
            $('#subCategoryList').fadeOut();
        }
    }

    $(document).delegate('#brand_input', 'click', function(e) {
        e.preventDefault();
        $(this).val('');
    });

    $(document).delegate('#brand_input', 'keyup', function(e) {
        e.preventDefault();
        var query = $(this).val();
        loadBrand(query);
    });

    $(document).delegate('#sub_category_input', 'click', function(e) {
        e.preventDefault();
        $(this).val('');
    });

    $(document).delegate('#sub_category_input', 'keyup', function(e) {
        e.preventDefault();
        var query = $(this).val();
        loadSubCategory(query);
    });

    $(document).delegate('#add_brand_to_list', 'click', function(e) {
        var id = $(this).attr('data-id');
        var br_name = $(this).attr('data-br_name');
        br_id.push(id);
        $('#brandPanel').append("<a class='btn btn-sm btn-primary mr-2 mb-2 brand-item' data-id='"+id+"'>"+br_name+"</a>");
        $('#brandList').fadeOut();
        $('#brand_input').val('');
    });

    $(document).delegate('#add_sub_category_to_list', 'click', function(e) {
        var id = $(this).attr('data-id');
        var psc_name = $(this).attr('data-psc_name');
        psc_id.push(id);
        $('#subCategoryPanel').append("<a class='btn btn-sm btn-primary mr-2 mb-2 sub-category-item' data-id='"+id+"'>"+psc_name+"</a>");
        $('#subCategoryList').fadeOut();
        $('#sub_category_input').val('');
    });

    $(document).delegate('.brand-item', 'click', function(e) {
        var id = $(this).attr('data-id');
        br_id = $.grep(br_id, function(value) {
                return value != id;
            });
        $(this).remove();
    });

    $(document).delegate('.sub-category-item', 'click', function(e) {
        var id = $(this).attr('data-id');
        psc_id = $.grep(psc_id, function(value) {
                return value != id;
            });
        $(this).remove();
    });

    $(document).on('click', 'body', function() {
        $('#brandList').fadeOut();
        $('#subCategoryList').fadeOut();
        $('#customBrandList').fadeOut();
        $('#customPscList').fadeOut();
    });

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var scan_adjustment_table = $('#SAtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('scan_adjustment_datatables') }}",
                data : function (d) {
                    d.search = $('#scan_adjustment_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'created_at', name: 'created_at' },
            { data: 'sa_code', name: 'sa_code' },
            { data: 'st_name', name: 'st_name' },
            { data: 'sa_custom', name: 'sa_custom' },
            { data: 'u_name', name: 'u_name' },
            { data: 'approve', name: 'approve' },
            { data: 'executor', name: 'executor' },
            { data: 'sa_description', name: 'sa_description' },
            { data: 'sa_status', name: 'sa_status' },
            { data: 'action', name: 'action', orderable:false },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        scan_adjustment_table.buttons().container().appendTo($('#scan_adjustment_excel_btn' ));
        $('#scan_adjustment_search').on('keyup', function() {
            scan_adjustment_table.draw(false);
        });

        $('#SAtb tbody').on('click', 'tr td:not(:nth-child(5), :nth-child(11))', function () {
            var id = scan_adjustment_table.row(this).data().id;
            var st_id = scan_adjustment_table.row(this).data().st_id;
            var sa_description = scan_adjustment_table.row(this).data().sa_description;
            jQuery.noConflict();
            $('#SAModal').modal('show');
            $('#sa_description').val(sa_description);
            $('#st_id').val(st_id);
            $('#_id').val(id);
            $('#_mode').val('edit');
            $('.custom-panel').addClass('d-none');
            $('#delete_scan_adjustment_btn').show();
            br_id = [];
            psc_id = [];
            $('#custom_panel').addClass('d-none');
        });

        $('#add_scan_adjustment_btn').on('click', function() {
            jQuery.noConflict();
            $('#SAModal').modal('show');
            $('.custom-panel').removeClass('d-none');
            $('#_id').val('');
            $('#_mode').val('add');
            br_id = [];
            psc_id = [];
            $('#custom_panel').addClass('d-none');
            $('#f_scan_adjustment')[0].reset();
            $('#delete_scan_adjustment_btn').hide();
        });

        $('#f_scan_adjustment').on('submit', function(e) {
            e.preventDefault();
            $("#save_scan_adjustment_btn").html('Proses ..');
            $("#save_scan_adjustment_btn").attr("disabled", true);
            var formData = new FormData(this);
            if (br_id.length > 0) {
                formData.append('br_id', br_id);
            }
            if (psc_id.length > 0) {
                formData.append('psc_id', psc_id);
            }
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type:'POST',
                url: "{{ url('sa_save')}}",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_scan_adjustment_btn").html('Simpan');
                    $("#save_scan_adjustment_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#SAModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        scan_adjustment_table.draw(false);
                    } else if (data.status == '400') {
                        $("#SAModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_scan_adjustment_btn').on('click', function(){
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
                        data: {_id:$('#_id').val()},
                        dataType: 'json',
                        url: "{{ url('sa_delete')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#SAModal').modal('hide');
                                scan_adjustment_table.draw(false);
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $(document).delegate('#approve_btn', 'click', function(e){
            e.preventDefault();
            var id = $(this).attr('data-id');
            swal({
                title: "Approve..?",
                text: "Yakin approve ?",
                icon: "warning",
                buttons: [
                    'Batalkan',
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
                        data: {id:id},
                        dataType: 'json',
                        url: "{{ url('scan_adjustment_approval')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil diapprove", "success");
                                scan_adjustment_table.draw(false);
                            } else {
                                swal('Gagal', 'Gagal approve data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $(document).delegate('#finish_btn', 'click', function(e){
            e.preventDefault();
            var id = $(this).attr('data-id');
            swal({
                title: "Eksekusi..?",
                text: "Yakin eksekusi penyesuaian ?",
                icon: "warning",
                buttons: [
                    'Batalkan',
                    'Eksekusi'
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
                        data: {id:id},
                        dataType: 'json',
                        url: "{{ url('scan_adjustment_finish')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil diselesaikan", "success");
                                scan_adjustment_table.draw(false);
                            } else {
                                swal('Gagal', 'Gagal selesaikan data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $(document).delegate('#report_btn', 'click', function(e){
            e.preventDefault();
            var id = $(this).attr('data-id');
            var data = new FormData();
            data.append('id', id);
            swal({
                title: "Export Hasil SO..?",
                text: "",
                icon: "warning",
                buttons: [
                    'Batalkan',
                    'Export'
                ],
                dangerMode: false,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    window.location.href = "{{ url('scan_adjustment_export') }}?id="+id+"";
                }
            })
        });

        var product = $('#Ptb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('scan_adjustment_product_datatables') }}",
                data : function (d) {
                    d.search = $('#p_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'br_name', name: 'br_name' },
            { data: 'p_name', name: 'p_name' },
            { data: 'p_color', name: 'p_color' },
            { data: 'sz_name', name: 'sz_name' },
            { data: 'ps_barcode_show', name: 'ps_barcode' },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        $('#p_search').on('keyup', function() {
            product.draw(false);
        });

        $(document).delegate('#product_barcode_btn', 'click', function(e) {
            e.preventDefault();
            jQuery.noConflict();
            $('#ProductBarcodeModal').modal('show');
            product.draw(false);
        });

        $(document).delegate('#input_barcode', 'change', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            var barcode = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {id:id, barcode:barcode},
                dataType: 'json',
                url: "{{ url('scan_adjustment_barcode_update')}}",
                success: function(r) {
                    if (r.status == '200'){
                        swal("Berhasil", "Data berhasil diupdate", "success");
                        product.draw(false);
                    } else {
                        swal('Gagal', 'Gagal update data', 'error');
                    }
                }
            });
            return false;
        });

        $(document).delegate('#start_btn', 'click', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            swal({
                title: "Mulai Adjustment..?",
                text: "Yakin mulai adjustment ?",
                icon: "warning",
                buttons: [
                    'Batal',
                    'Mulai'
                ],
                dangerMode: false,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    window.location.href = url;
                }
            })
        });

        var brand_table = $('#Brtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('scan_adjustment_custom_datatables') }}",
                data : function (d) {
                    d.type = 'brand';
                    d.id = sa_id;
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'br_name', name: 'br_name' },
            { data: 'action', name: 'br_name' },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        var psc_table = $('#Psctb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('scan_adjustment_custom_datatables') }}",
                data : function (d) {
                    d.type = 'psc';
                    d.id = sa_id;
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'psc_name', name: 'psc_name' },
            { data: 'action', name: 'psc_name' },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        var sm_table = $('#Smtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            paginate: false,
            dom: 'Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "{{ url('scan_adjustment_custom_datatables') }}",
                data : function (d) {
                    d.type = 'summary';
                    d.id = sa_id;
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'br_name', name: 'br_name' },
            { data: 'qty_before', name: 'qty_before' },
            { data: 'qty_after', name: 'qty_after' },
            { data: 'value_before', name: 'qty_before' },
            { data: 'value_after', name: 'qty_after' },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        $(document).delegate('#brand_btn', 'click', function(e) {
            e.preventDefault();
            sa_id = $(this).attr('data-id');
            done = $(this).attr('data-status');
            brand_table.draw();
            jQuery.noConflict();
            $('#BrandModal').modal('show');
        });

        $(document).delegate('#psc_btn', 'click', function(e) {
            e.preventDefault();
            sa_id = $(this).attr('data-id');
            done = $(this).attr('data-status');
            psc_table.draw();
            jQuery.noConflict();
            $('#SubCategoryModal').modal('show');
        });

        $(document).delegate('#summary_btn', 'click', function(e) {
            e.preventDefault();
            sa_id = $(this).attr('data-id');
            sm_table.draw();
            jQuery.noConflict();
            $('#SummaryModal').modal('show');
        });

        $(document).delegate('#bin_btn', 'click', function(e){
            e.preventDefault();
            var id = $(this).attr('data-id');
            var data = new FormData();
            data.append('id', id);
            swal({
                title: "Download Rekomendasi BIN Custom..?",
                text: "",
                icon: "warning",
                buttons: [
                    'Batal',
                    'Download'
                ],
                dangerMode: false,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    window.location.href = "{{ url('export_start_scan_adjustment_bin') }}?id="+id+"";
                }
            })
        });

        function loadCustomItem(query, type)
        {
            if($.trim(query) != '' || $.trim(query) != null) {
                $.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url:"{{  url('fetch_start_scan_adjustment_custom') }}",
                    method:"POST",
                    data:{query:query, type:type},
                    success:function(data){
                        if (type == 'brand') {
                            $('#customBrandList').fadeIn();
                            $('#customBrandList').html(data);
                        } else {
                            $('#customPscList').fadeIn();
                            $('#customPscList').html(data);
                        }
                    }
                });
            } else {
                if (type == 'brand') {
                    $('#customBrandList').fadeOut();
                } else {
                    $('#customPscList').fadeOut();
                }
            }
        }

        $(document).delegate('#custom_brand_search', 'keyup', function(e) {
            e.preventDefault();
            var query = $(this).val();
            loadCustomItem(query, 'brand');
        });

        $(document).delegate('#custom_psc_search', 'keyup', function(e) {
            e.preventDefault();
            var query = $(this).val();
            loadCustomItem(query, 'psc');
        });

        function addCustom(id, type)
        {
            if (done == '1') {
                swal('Dilarang', 'Anda tidak bisa menambah data ini, karena status SO sudah selesai', 'warning');
                return false;
            }
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {id:id, type:type, sa_id:sa_id},
                dataType: 'json',
                url: "{{ url('add_start_scan_adjustment_custom')}}",
                success: function(r) {
                    if (r.status == '200'){
                        if (type == 'brand') {
                            brand_table.draw();
                        } else {
                            psc_table.draw();
                        }
                        toast("Berhasil", "Data berhasil ditambah", "success");
                    } else {
                        toast('Gagal', 'Gagal tambah data', 'error');
                    }
                    if (type == 'brand') {
                        $('#customBrandList').fadeOut();
                    } else {
                        $('#customPscList').fadeOut();
                    }
                }
            });
            return false;
        }

        $(document).delegate('#add_custom_psc', 'click', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            addCustom(id, 'psc');
            $('#custom_psc_search').val('');
        });

        $(document).delegate('#add_custom_brand', 'click', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            addCustom(id, 'brand');
            $('#custom_brand_search').val('');
        });

        function deleteCustom(id, type)
        {
            if (done == '1') {
                swal('Dilarang', 'Anda tidak bisa menghapus data ini, karena status SO sudah selesai', 'warning');
                return false;
            }
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {id:id, type:type},
                dataType: 'json',
                url: "{{ url('delete_start_scan_adjustment_custom')}}",
                success: function(r) {
                    if (r.status == '200'){
                        if (type == 'brand') {
                            brand_table.draw();
                        } else {
                            psc_table.draw();
                        }
                        toast("Berhasil", "Data berhasil dihapus", "success");
                    } else {
                        toast('Gagal', 'Gagal hapus data', 'error');
                    }
                }
            });
            return false;
        }

        $(document).delegate('#delete_br_custom', 'click', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            swal({
                title: "Hapus..?",
                text: "Yakin hapus data ini ?",
                icon: "warning",
                buttons: [
                    'Batal',
                    'Hapus'
                ],
                dangerMode: true,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    deleteCustom(id, 'brand');
                }
            })
        });

        $(document).delegate('#delete_psc_custom', 'click', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            swal({
                title: "Hapus..?",
                text: "Yakin hapus data ini ?",
                icon: "warning",
                buttons: [
                    'Batal',
                    'Hapus'
                ],
                dangerMode: true,
            }).then(function(isConfirm) {
                if (isConfirm) {
                    deleteCustom(id, 'psc');
                }
            })
        });

    });
</script>
