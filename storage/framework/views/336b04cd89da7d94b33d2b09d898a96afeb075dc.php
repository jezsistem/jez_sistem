
<div id="chart"></div>
<script>
    var date = '';

    const primary = '#072544';
    const provinceChart = "#province_chart";
    const dateChart = "#date_chart";

    var provinceChart_options = {
        series: [],
        chart: {
            height: 350,
            type: 'area',
            zoom: {
                enabled: true
            }
        },
        dataLabels: {
            enabled: false
        },
        plotOptions: {
        bar: {
            borderRadius: 4,
            horizontal: true,
        }
        },
        grid: {
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            },
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return addCommas(val)
                },
                show: false
            },
        },
        colors: [primary]
    };
    var provinceChart_render = new ApexCharts(document.querySelector(provinceChart), provinceChart_options);
    provinceChart_render.render();

    var dateChart_options = {
        series: [],
        chart: {
            height: 350,
            type: 'area',
            zoom: {
                enabled: true
            }
        },
        dataLabels: {
            enabled: false
        },
        plotOptions: {
        bar: {
            borderRadius: 4,
            horizontal: true,
        }
        },
        grid: {
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            },
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return addCommas(val)
                },
                show: false
            },
        },
        colors: [primary]
    };
    var dateChart_render = new ApexCharts(document.querySelector(dateChart), dateChart_options);
    dateChart_render.render();

    function loadGraph(stt_label, ct_id, date)
    {
        $('#GraphModal').modal('show');
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type:'POST',
            url: "<?php echo e(url('get_customer_graph')); ?>",
            data: {stt_label:stt_label, ct_id:ct_id, date:date},
            dataType: 'html',
            success: function(r) {
                $('#chart').html(r);
                window.dispatchEvent(new Event('resize'));
            },
        });
    }

    function addCommas(nStr)
    {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }

    function reloadCustomerType()
    {
        $.ajax({
            type: "GET",
            dataType: 'html',
            url: "<?php echo e(url('reload_customer_type')); ?>",
            success: function(r) {
                $('#ct_id').html(r);
            }
        });
    }

    function reloadCity(province)
    {
        $.ajax({
            type: "GET",
            data: {_province:province},
            dataType: 'html',
            url: "<?php echo e(url('reload_city')); ?>",
            success: function(r) {
                $('#cust_city').html(r);
            }
        });
    }

    function reloadSubdistrict(city)
    {
        $.ajax({
            type: "GET",
            data: {_city:city},
            dataType: 'html',
            url: "<?php echo e(url('reload_subdistrict')); ?>",
            success: function(r) {
                $('#cust_subdistrict').html(r);
            }
        });
    }

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var province_table = $('#Provincetb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('province_datatables')); ?>",
                data : function (d) {
                    //d.search = $('#customer_type_search').val();
                    d.stt_filter = $('#stt_filter').val();
                    d.cust_type_filter = $('#cust_type_filter').val();
                    d.date_filter = $('#date_filter').val();
                    d.date = date;
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'kode', searchable: false},
            { data: 'nama_show', name: 'nama' },
            { data: 'customer_show', name: 'customer' },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[2, 'desc']],
        });

        var city_table = $('#Citytb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('city_datatables')); ?>",
                data : function (d) {
                    d.province_code = $('#_province_code').val();
                    d.stt_filter = $('#stt_filter').val();
                    d.cust_type_filter = $('#cust_type_filter').val();
                    d.date_filter = $('#date_filter').val();
                    d.date = date;
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'kode', searchable: false},
            { data: 'nama_show', name: 'nama' },
            { data: 'customer_show', name: 'customer' },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[2, 'desc']],
        });

        var city_rank_table = $('#CityRanktb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('city_rank_datatables')); ?>",
                data : function (d) {
                    //d.province_code = $('#_province_code').val();
                    d.stt_filter = $('#stt_filter').val();
                    d.cust_type_filter = $('#cust_type_filter').val();
                    d.date_filter = $('#date_filter').val();
                    d.date = date;
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'kode', searchable: false},
            { data: 'nama_show', name: 'nama' },
            { data: 'customer_show', name: 'customer' },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[2, 'desc']],
        });

        var subdistrict_table = $('#Subdistricttb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('subdistrict_datatables')); ?>",
                data : function (d) {
                    d.city_code = $('#_city_code').val();
                    d.stt_filter = $('#stt_filter').val();
                    d.cust_type_filter = $('#cust_type_filter').val();
                    d.date_filter = $('#date_filter').val();
                    d.date = date;
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'kode', searchable: false},
            { data: 'nama_show', name: 'nama' },
            { data: 'customer_show', name: 'customer' },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[2, 'desc']],
        });

        var customer_type_table = $('#CustomerTypetb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('customer_type_datatables')); ?>",
                data : function (d) {
                    d.search = $('#customer_type_search').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'id', searchable: false},
            { data: 'ct_name', name: 'ct_name' },
            { data: 'ct_description', name: 'ct_description' },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [[0, 'desc']],
        });

        customer_type_table.buttons().container().appendTo($('#customer_type_excel_btn' ));

        var customer_table = $('#Customertb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"lip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('customer_datatables')); ?>",
                data : function (d) {
                    d.search = $('#customer_search').val();
                    d.stt_filter = $('#stt_filter').val();
                    d.cust_type_filter = $('#cust_type_filter').val();
                    d.date_filter = $('#date_filter').val();
                    d.date = date;
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'cid', searchable: false},
            { data: 'cust_name', name: 'cust_name' },
            { data: 'ct_name', name: 'ct_name' },
            { data: 'cust_store', name: 'cust_store' },
            { data: 'cust_phone', name: 'cust_phone' },
            { data: 'cust_email', name: 'cust_email' },
            { data: 'cust_address_show', name: 'cust_address' },
            { data: 'cust_shopping_show', name: 'cust_shopping' },
            { data: 'cust_created', name: 'cust_created' },
            { data: 'total_pembelian', name: 'total_pembelian' },
            ],
            columnDefs: [
            {
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            lengthMenu: [[10, 25], [10, 25]],
            language: {
                "lengthMenu": "_MENU_",
            },
            order: [[7, 'desc']],
        });

        var customer_detail_table = $('#CustomerDetailtb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Blrt<"text-right"ip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('customer_detail_datatables')); ?>",
                data : function (d) {
                    d.code = $('#_code').val();
                    d.code_type = $('#_code_type').val();
                    d.stt_filter = $('#stt_filter').val();
                    d.cust_type_filter = $('#cust_type_filter').val();
                    d.date_filter = $('#date_filter').val();
                    d.date = date;
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'cid', searchable: false},
            { data: 'cust_name', name: 'cust_name' },
            { data: 'ct_name', name: 'ct_name' },
            { data: 'cust_store', name: 'cust_store' },
            { data: 'cust_phone', name: 'cust_phone' },
            { data: 'cust_email', name: 'cust_email' },
            { data: 'cust_address_show', name: 'cust_address' },
            { data: 'cust_shopping_show', name: 'cust_shopping' },
            { data: 'cust_created', name: 'cust_created' },
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
            order: [[7, 'desc']],
        });

        var customer_transaction_table = $('#CustomerTransactiontb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'Brt<"text-right"lip>',
            buttons: [
                { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
            ],
            ajax: {
                url : "<?php echo e(url('customer_transaction_datatables')); ?>",
                data : function (d) {
                    d.cust_id = $('#_cust_id').val();
                }
            },
            columns: [
            { data: 'DT_RowIndex', name: 'pt_id', searchable: false},
            { data: 'pos_created', name: 'pos_created' },
            { data: 'pos_invoice', name: 'pos_invoice' },
            { data: 'qty', name: 'qty', orderable: false },
            { data: 'total', name: 'total', orderable: false },
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

        var sales_item_detail_table = $('#SalesItemDetailtb').DataTable({
			destroy: true,
			processing: true,
			serverSide: true,
			responsive: false,
			deferRender: false,
			dom: 'rt<"text-right"ip>',
			buttons: [
				{ "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
			],
			ajax: {
				url : "<?php echo e(url('sales_item_detail_datatables')); ?>",
				data : function (d) {
					d.pt_id = $('#pt_id').val();
				}
			},
			columns: [
			{ data: 'DT_RowIndex', name: 'ptd_id', searchable: false},
			{ data: 'article', name: 'article'},
			{ data: 'pos_td_qty', name: 'pos_td_qty', orderable: false },
			{ data: 'pos_td_total_price', name: 'pos_td_total_price', orderable: false },
			{ data: 'created_at', name: 'created_at', orderable: false },
			],
			columnDefs: [
			{
				"targets": 0,
				"className": "text-center",
				"width": "0%"
			}],
			order: [[0, 'desc']],
		});

        customer_table.buttons().container().appendTo($('#customer_excel_btn' ));
        $('#customer_search').on('keyup', function() {
            customer_table.draw(false);
        });

        $(document).delegate('#sales_item_detail_btn', 'click', function() {
			var pt_id = $(this).attr('data-pt_id');
			var invoice = $(this).text();
            jQuery.noConflict();
			$('#pt_id').val(pt_id);
			$('#sales_item_detail_label').text(invoice);
			$('#SalesItemDetailModal').on('show.bs.modal', function() {
				sales_item_detail_table.draw(false);
			}).modal('show');
		});

        $(document).delegate('#import_btn', 'click', function() {
            $('#ImportModal').modal('show');
        });

        $('#stt_filter, #cust_type_filter, #stt_filter, #date_filter').on('change', function() {
            province_table.draw(false);
            city_table.draw(false);
            city_rank_table.draw(false);
            subdistrict_table.draw(false);
            customer_table.draw(false);
            customer_detail_table.draw(false);
        });

        $('#Customertb tbody').on('click', 'tr td:not(:nth-child(8))', function () {
            $('#imagePreview').attr('src', '');
            var id = customer_table.row(this).data().cid;
            var ct_id = customer_table.row(this).data().ct_id;
            var cust_store = customer_table.row(this).data().cust_store;
            var cust_name = customer_table.row(this).data().cust_name;
            var cust_phone = customer_table.row(this).data().cust_phone;
            var cust_username = customer_table.row(this).data().cust_username;
            var cust_email = customer_table.row(this).data().cust_email;
            var cust_province = customer_table.row(this).data().cust_province;
            var cust_city = customer_table.row(this).data().cust_city;
            var cust_subdistrict = customer_table.row(this).data().cust_subdistrict;
            var cust_address = customer_table.row(this).data().cust_address;
            jQuery.noConflict();
            $('#CustomerModal').modal('show');
            jQuery('#ct_id').val(ct_id).trigger('change');
            $('#cust_store').val(cust_store);
            $('#cust_name').val(cust_name);
            $('#cust_phone').val(cust_phone);
            $('#cust_username').val(cust_username);
            $('#cust_email').val(cust_email);
            $('#cust_province').val(cust_province);
            reloadCity(cust_province);
            setTimeout(() => {
                $('#cust_city').val(cust_city);
            }, 1000);
            reloadSubdistrict(cust_city);
            setTimeout(() => {
                $('#cust_subdistrict').val(cust_subdistrict);
            }, 2000);
            $('#cust_address').val(cust_address);
            $('#_id').val(id);
            $('#_mode').val('edit');
            <?php if( $data['user']->delete_access == '1' ): ?>
            $('#delete_customer_btn').show();
            <?php endif; ?>
        });

        $('#CustomerTypetb tbody').on('click', 'tr', function () {
            $('#imagePreview').attr('src', '');
            var id = customer_type_table.row(this).data().id;
            var ct_name = customer_type_table.row(this).data().ct_name;
            var ct_description = customer_type_table.row(this).data().ct_description;
            jQuery.noConflict();
            $('#CustomerTypeModal').modal('show');
            $('#ct_name').val(ct_name);
            $('#ct_description').val(ct_description);
            $('#_id_ct').val(id);
            $('#_mode_ct').val('edit');
            <?php if( $data['user']->g_name == 'administrator' ): ?>
            $('#delete_customer_type_btn').show();
            <?php endif; ?>
        });

        $('#add_customer_type_btn').on('click', function() {
            jQuery.noConflict();
            $('#CustomerTypeModal').modal('show');
            $('#_id_ct').val('');
            $('#_mode_ct').val('add');
            $('#f_customer_type')[0].reset();
            $('#delete_customer_type_btn').hide();
        });

        $('#add_customer_btn').on('click', function() {
            jQuery.noConflict();
            $('#CustomerModal').modal('show');
            jQuery('#ct_id').val('').trigger('change');
            $('#_id').val('');
            $('#_mode').val('add');
            $('#f_customer')[0].reset();
            $('#delete_customer_btn').hide();
        });

        $('#cust_phone').on('change', function() {
            var cust_phone = $(this).val();
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {_cust_phone:cust_phone},
                dataType: 'json',
                url: "<?php echo e(url('check_exists_customer')); ?>",
                success: function(r) {
                    if (r.status == '200') {
                        swal('No Telepon', 'Nomor telepon sudah ada disistem, silahkan ganti dengan yang lain', 'warning');
                        $('#cust_phone').val('');
                        return false;
                    }
                }
            });
        });

        $('#cust_province').on('change', function() {
            var province = $(this).val();
            reloadCity(province);
        });

        $('#cust_city').on('change', function() {
            var city = $(this).val();
            reloadSubdistrict(city);
        });

        $(document).delegate('#cust_shopping', 'click', function() {
            jQuery.noConflict();
            var cust_id = $(this).attr('data-cust_id');
            $('#_cust_id').val(cust_id);
            $("#CustomerTransactionModal").modal('show');
            customer_transaction_table.draw(false);
        });

        $(document).delegate('#province_detail_btn', 'click', function() {
            jQuery.noConflict();
            var code = $(this).attr('data-kode');
            $('#_province_code').val(code);
            $("#CustomerCityModal").modal('show');
            city_table.draw(false);
        });

        $(document).delegate('#city_detail_btn', 'click', function() {
            jQuery.noConflict();
            var code = $(this).attr('data-kode');
            $('#_city_code').val(code);
            $("#CustomerSubdistrictModal").modal('show');
            subdistrict_table.draw(false);
        });

        $(document).delegate('#customer_detail_btn', 'click', function() {
            jQuery.noConflict();
            var code = $(this).attr('data-kode');
            var code_type = $(this).attr('data-type');
            $('#_code').val(code);
            $('#_code_type').val(code_type);
            $("#CustomerDetailModal").modal('show');
            customer_detail_table.draw(false);
        });

        $('#f_import').on('submit', function(e) {
            e.preventDefault();
            $("#import_data_btn").html('Proses ..');
            $("#import_data_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('cust_import')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#import_data_btn").html('Import');
                    $("#import_data_btn").attr("disabled", false);
                    jQuery.noConflict();
                    if (data.status == '200') {
                        $("#ImportModal").modal('hide');
                        swal('Berhasil', 'Data berhasil diimport', 'success');
                        $('#f_import')[0].reset();
                        customer_table.draw(false);
                    } else if (data.status == '400') {
                        $("#ImportModal").modal('hide');
                        swal('Gagal', 'Data gagal diimport', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#f_customer').on('submit', function(e) {
            e.preventDefault();
            $("#save_customer_btn").html('Proses ..');
            $("#save_customer_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('cust_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_customer_btn").html('Simpan');
                    $("#save_customer_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#CustomerModal").modal('hide');
                        $('#f_customer')[0].reset();
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        customer_table.draw(false);
                    } else if (data.status == '400') {
                        $("#CustomerModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_customer_btn').on('click', function(){
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
                        url: "<?php echo e(url('cust_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#CustomerModal').modal('hide');
                                customer_table.draw(false);
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $('#f_customer_type').on('submit', function(e) {
            e.preventDefault();
            $("#save_customer_type_btn").html('Proses ..');
            $("#save_customer_type_btn").attr("disabled", true);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: "<?php echo e(url('ct_save')); ?>",
                data: formData,
				dataType: 'json',
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $("#save_customer_type_btn").html('Simpan');
                    $("#save_customer_type_btn").attr("disabled", false);
                    if (data.status == '200') {
                        $("#CustomerTypeModal").modal('hide');
                        swal('Berhasil', 'Data berhasil disimpan', 'success');
                        customer_type_table.draw(false);
                        reloadCustomerType();
                    } else if (data.status == '400') {
                        $("#CustomerTypeModal").modal('hide');
                        swal('Gagal', 'Data tidak tersimpan', 'warning');
                    }
                },
                error: function(data){
                    swal('Error', data, 'error');
                }
            });
        });

        $('#delete_customer_type_btn').on('click', function(){
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
                        data: {_id:$('#_id_ct').val()},
                        dataType: 'json',
                        url: "<?php echo e(url('ct_delete')); ?>",
                        success: function(r) {
                            if (r.status == '200'){
                                swal("Berhasil", "Data berhasil dihapus", "success");
                                $('#CustomerTypeModal').modal('hide');
                                customer_type_table.draw(false);
                                reloadCustomerType();
                            } else {
                                swal('Gagal', 'Gagal hapus data', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        });

        $(document).delegate('#graph_btn', 'click', function(e) {
            e.preventDefault();
            if ($('#date_filter').val() == '0') {
                swal('Filter Tanggal', 'Silahkan aktifkan terlebih dahulu filter tanggal untuk menggunakan fitur ini', 'info');
                return false;
            }
            loadGraph($('#stt_filter').val(), $('#cust_type_filter').val(), date);
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

            if ((end - start) < 100 || label == 'Hari Ini') {
                title = 'Hari Ini:';
                range = start.format('DD MMM YYYY');
                date = start.format('YYYY-MM-DD');
            } else if (label == 'Kemarin') {
                title = 'Kemarin:';
                range = start.format('DD MMM YYYY');
                date = start.format('YYYY-MM-DD');
            } else {
                range = start.format('DD MMM YYYY') + ' - ' + end.format('DD MMM YYYY');
                date = start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD');
            }
            $('#kt_dashboard_daterangepicker_date').html(range);
            $('#kt_dashboard_daterangepicker_title').html(title);
            province_table.draw(false);
            city_table.draw(false);
            city_rank_table.draw(false);
            subdistrict_table.draw(false);
            customer_table.draw(false);
            customer_detail_table.draw(false);
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
</script>
<?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/customer/customer_js.blade.php ENDPATH**/ ?>