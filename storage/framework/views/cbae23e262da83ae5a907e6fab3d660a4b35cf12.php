<script>
    var dashboard_date = '';
    var std_id = $('#std_filter').val();

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var master_table = $('#Mastertb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('master_datatables')); ?>",
                data : function (d) {
                    d.search = $('#master_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'pst_id', name: 'pst_id' },
            { data: 'brand', name: 'brand' },
            { data: 'name', name: 'name' },
            { data: 'color', name: 'color' },
            { data: 'size', name: 'size' },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        $('#master_search').on('keyup', function() {
            master_table.draw();
        });

        $(document).delegate('#fetch_btn', 'click', function(e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type:'GET',
                url: "<?php echo e(url('fetch_master')); ?>",
                dataType: 'json',
                success: function(r) {
                    if (r.status == '200') {
                        master_table.draw();
                        toast('Berhasil', 'Berhasil update data terbaru', 'success');
                    } else {
                        toast('Gagal', 'Gagal update data terbaru', 'error');
                    }
                },
            });
            return false;
        });

        var stock_table = $('#Stocktb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('marketplace_stock_datatables')); ?>",
                data : function (d) {
                    d.search = $('#stock_search').val();
                    d.std_id = std_id;
                    d.code = $('#code_filter').val();
                    d.br_id = $('#br_filter').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'pst_id', searchable: false},
            { data: 'marketplace_code_show', name: 'marketplace_code_show', orderable: false},
            { data: 'br_name', name: 'br_name' },
            { data: 'p_name', name: 'p_name' },
            { data: 'p_color', name: 'p_color' },
            { data: 'sz_name', name: 'sz_name' },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        $('#stock_search').on('keyup', function() {
            stock_table.draw();
        });

        $(document).delegate('#std_filter', 'change', function(e) {
            e.preventDefault();
            std_id = $(this).val();
            stock_table.draw();
        });

        $(document).delegate('#code_filter, #br_filter', 'change', function(e) {
            e.preventDefault();
            stock_table.draw();
        });

        $(document).delegate('#import_template_btn', 'click', function(e) {
            e.preventDefault();
            if (std_id == 'all') {
                swal('Marketplace Divisi', 'Tentukan marketplace divisi terlebih dahulu', 'warning');
                return false;
            }
            jQuery.noConflict();
            $('#ImportTemplateModal').modal('show');
        });

        $(document).delegate('#import_btn', 'click', function(e) {
            e.preventDefault();
            jQuery.noConflict();
            $('#ImportModal').modal('show');
        });

        function exportTable(std_id)
        {
            var data = new FormData();
            data.append('std_id', std_id);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('export_marketplace_table')); ?>",
                data: data,
                cache:false,
                contentType: false,
                processData: false,
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(blob, status, xhr) {
                    var filename = "";
                    var disposition = xhr.getResponseHeader('Content-Disposition');
                    if (disposition && disposition.indexOf('attachment') !== -1) {
                        var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                        var matches = filenameRegex.exec(disposition);
                        if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
                    }

                    if (typeof window.navigator.msSaveBlob !== 'undefined') {
                        window.navigator.msSaveBlob(blob, filename);
                    } else {
                        var URL = window.URL || window.webkitURL;
                        var downloadUrl = URL.createObjectURL(blob);

                        if (filename) {
                            var a = document.createElement("a");
                            if (typeof a.download === 'undefined') {
                                window.location.href = downloadUrl;
                            } else {
                                a.href = downloadUrl;
                                a.download = filename;
                                document.body.appendChild(a);
                                a.click();
                            }
                        } else {
                            window.location.href = downloadUrl;
                        }
                        setTimeout(function () { URL.revokeObjectURL(downloadUrl); }, 10000);
                    }
                },

            });
        }

        function checkCode(mm_id, pst_id, code)
        {
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('check_marketplace_code')); ?>",
                data: {code:code},
                dataType: 'json',
                success: function(r) {
                    if (r.status == '200') {
                        swal('Sudah Ada', 'Kode sudah ada disystem, silahkan periksa kembali kode', 'info');
                    } else {
                        updateCode(mm_id, pst_id, code);
                    }
                },
            });
        }

        function updateCode(mm_id, pst_id, code)
        {
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('update_marketplace_code')); ?>",
                data: {mm_id:mm_id, pst_id:pst_id, code:code, std_id:std_id},
                dataType: 'json',
                success: function(r) {
                    if (r.status == '200') {
                        toast('Berhasil', 'Berhasil update kode template', 'success');
                    } else {
                        toast('Gagal', 'Gagal update kode template', 'warning');
                    }
                },
            });
        }

        $(document).delegate('#marketplace_code_input', 'change', function(e) {
            e.preventDefault();
            var code = $(this).val();
            var mm_id = $(this).attr('data-mm_id');
            var pst_id = $(this).attr('data-pst_id');
            checkCode(mm_id, pst_id, code);
        });

        $('#f_import').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            // $("#import_data_btn").html('Proses ..');
            // $("#import_data_btn").attr("disabled", true);
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('marketplace_stock_refresh')); ?>",
                data: formData,
                cache:false,
                contentType: false,
                processData: false,
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(blob, status, xhr) {
                    var filename = "";
                    var disposition = xhr.getResponseHeader('Content-Disposition');
                    if (disposition && disposition.indexOf('attachment') !== -1) {
                        var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                        var matches = filenameRegex.exec(disposition);
                        if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
                    }

                    if (typeof window.navigator.msSaveBlob !== 'undefined') {
                        window.navigator.msSaveBlob(blob, filename);
                    } else {
                        var URL = window.URL || window.webkitURL;
                        var downloadUrl = URL.createObjectURL(blob);

                        if (filename) {
                            var a = document.createElement("a");
                            if (typeof a.download === 'undefined') {
                                window.location.href = downloadUrl;
                            } else {
                                a.href = downloadUrl;
                                a.download = filename;
                                document.body.appendChild(a);
                                a.click();
                            }
                        } else {
                            window.location.href = downloadUrl;
                        }
                        setTimeout(function () { URL.revokeObjectURL(downloadUrl); }, 10000);
                    }
                    // $("#import_data_btn").html('Import');
                    // $("#import_data_btn").attr("disabled", false);
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#f_import_template').on('submit', function(e) {
            e.preventDefault();
            // $("#import_template_data_btn").html('Proses ..');
            // $("#import_template_data_btn").attr("disabled", true);
            var formData = new FormData(this);
            formData.append('std_id', std_id);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('marketplace_template_import')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    // $("#import_data_template_btn").html('Import');
                    // $("#import_data_template_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $(this)[0].reset();
                        stock_table.draw();
                        toast('Berhasil', 'Data kode berhasil diupdate', 'success');
                    } else {
                        
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });
    });
</script>
<?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/marketplace_manager/marketplace_manager_js.blade.php ENDPATH**/ ?>