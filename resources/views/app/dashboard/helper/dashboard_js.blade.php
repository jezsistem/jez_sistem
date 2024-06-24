
<script type="text/javascript" src="{{ asset('scanner') }}/js/qrcodelib.js"></script>
<script type="text/javascript" src="{{ asset('scanner') }}/js/webcodecamjs.js"></script>
<script type="text/javascript" src="{{ asset('scanner') }}/js/main.js"></script>
<script>
	function reloadOrderList()
	{
		var qr = $('#invoice_number').text();
		$.ajaxSetup({
			headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			type: "POST",
			data: {_qr:qr},
			dataType: 'html',
			url: "{{ url('order_list_by_invoice')}}",
			success: function(r) {
				$('#orderListItem').html(r);
			}
		});
	}

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var stock_data_table = $('#StockDatatb').DataTable({
        destroy: true,
        processing: false,
        serverSide: true,
        responsive: false,
        dom: 'rt<"text-right"ipl>',
        buttons: [
            { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
        ],
        ajax: {
            url : "{{ url('stock_data_datatables') }}",
            data : function (d) {
                d.search = $('#stock_data_search').val();
                d.search_scan = $('#stock_data_search_scan').val();
                d.br_id = $('#br_id').val();
                d.pc_id = $('#pc_id').val();
                d.psc_id = $('#psc_id').val();
                d.pssc_id = $('#pssc_id').val();
                d.sz_id = $('#sz_id').val();
                d.gender_id = $('#gender_id').val();
                d.main_color_id = $('#main_color_id').val();
                d.display_status = $('#display_status').val();
                d.st_id = $('#st_id_filter').val();
            }
        },
        columns: [
            { data: 'article_name', name: 'article_name', orderable: false },
            { data: 'article_stock', name: 'article_stock', orderable: false },
        ],
        columnDefs: [
            {
                "targets": 0,
                "className": "text-left",
                "width": "0%"
            }],
        rowCallback: function( row, data, index) {
            if (data.article_stock.indexOf("<table></table>") >= 0) {
                $(row).hide();
            }
        },
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        language: {
            "lengthMenu": "_MENU_",
        },
        order: [[0, 'desc']],
    });
    var oSettings = stock_data_table.settings();


    var aging_table = $('#Agingtb').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        responsive: false,
        dom: 'B<"text-right"l>rt<"text-right"ip>',
        buttons: [
            { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
        ],
        ajax: {
            url : "{{ url('aging_datatables') }}",
            data : function (d) {
                d.search = $('#aging_search').val();
                d.br_id = $('#br_id').val();
                d.pc_id = $('#pc_id').val();
                d.psc_id = $('#psc_id').val();
                d.pssc_id = $('#pssc_id').val();
                d.sz_id = $('#sz_id').val();
                d.st_id = $('#st_id_filter').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'pst_id', searchable: false},
            { data: 'st_name', name: 'st_name'},
            { data: 'aging_po', name: 'aging_po', orderable: false},
            { data: 'aging_tf', name: 'aging_tf', orderable: false},
            { data: 'pc_name', name: 'pc_name'},
            { data: 'psc_name', name: 'psc_name'},
            { data: 'pssc_name', name: 'pssc_name'},
            { data: 'br_name', name: 'br_name'},
            { data: 'p_name', name: 'p_name'},
            { data: 'p_color', name: 'p_color'},
            { data: 'sz_name', name: 'sz_name'},

            { data: 'stock', name: 'aging', orderable: false},
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

    var pickup_list_table = $('#PickupListtb').DataTable({
        destroy: true,
        processing: false,
        serverSide: true,
        responsive: false,
        dom: 'rt<"text-right"ip>',
        ajax: {
            url : "{{ url('pickup_list_datatables') }}",
            data : function (d) {
                d.search = $('#pick_data_search').val();
                d.st_id = "{{ $data['user']->st_id }}";
            }
        },
        columns: [
            { data: 'article', name: 'p_name', orderable: false },
            { data: 'bin', name: 'pl_code, orderable: false' },
            { data: 'datetime', name: 'plst_created', orderable: false },
            { data: 'user', name: 'user', orderable: false },
            { data: 'status', name: 'p_name', orderable: false },
            { data: 'action', name: 'p_name', orderable: false },
        ],
        columnDefs: [
            {
                "targets": 0,
                "className": "text-left",
                "width": "0%"
            }],
        order: [[0, 'desc']],
    });

    stock_data_table.buttons().container().appendTo($('#stock_data_excel_btn' ));
    $('#stock_data_search').on('keyup', function() {
        var query = jQuery(this).val();
        if (jQuery.trim(query).length > 2) {
            stock_data_table.draw();
        } else if (jQuery.trim(query).length == 0) {
            stock_data_table.draw();
        }
    });

    $('#pick_data_search').on('keyup', function() {
        pickup_list_table.draw();
    });

    $('#aging_search').on('keyup', function() {
        aging_table.draw();
    });

    $('#aging_btn').on('click', function() {
        $('#AgingModal').modal('show');
        aging_table.draw();
    });

    $('#st_id_filter_aging').on('change', function() {
        aging_table.draw();
    });

    $(document).delegate('#aging_detail', 'click', function(e) {
        swal($(this).attr('title'));
    });

    $(document).delegate('#pickup_item', 'click', function(e) {
        e.preventDefault();
        var st_id = {{ $data['user']->st_id }};

        var pst_id = $(this).attr('data-pst_id');
        var pl_id = $(this).attr('data-pl_id');
        var qty = $(this).attr('data-qty');
        var pls_id = $(this).attr('data-pls_id');
        var p_name = $(this).attr('data-p_name');
        var pl_code = $(this).attr('data-pl_code');
        var bin = $(this).attr('data-bin');
        @if (strtolower($data['user']->stt_name) == 'offline')
        if (st_id == {{ $data['user']->st_id }}) {
            swal({
                title: "Pickup..?",
                text: "Yakin pickup item "+p_name+" dari bin "+bin+" ?",
                icon: "warning",
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
                        data: {_pls_id:pls_id, _pst_id:pst_id, _pl_id:pl_id, _pl_code:pl_code},
                        dataType: 'json',
                        url: "{{ url('pickup_item')}}",
                        success: function(r) {
                            if (r.status == '200'){
                                toast("Berhasil", "Item berhasil dipickup", "success");
                                stock_data_table.draw();
                                pickup_list_table.draw();
                            } else {
                                toast('Gagal', 'Gagal pickup item', 'error');
                            }
                        }
                    });
                    return false;
                }
            })
        }
        @endif
    });

	function reloadPackingList()
	{
		var qr = $('#invoice_number').text();
		$.ajaxSetup({
			headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			type: "POST",
			data: {_qr:qr},
			dataType: 'html',
			url: "{{ url('packing_list_by_invoice')}}",
			success: function(r) {
				$('#orderListItem').html(r);
			}
		});
	}

	var out_table = $('#Outtb').DataTable({
		destroy: true,
		processing: false,
		serverSide: true,
		responsive: false,
		dom: 'rt<"text-right"ip>',
		ajax: {
			url : "{{ url('product_out_datatables') }}",
			data : function (d) {
				// d.pl_id = $('#pl_id_out').val();
				d.search = $('#out_search').val();
				d.st_id = $('#st_id').val();
			}
		},
		columns: [
		{ data: 'article', name: 'article' , sortable: false },
		], 
		columnDefs: [
		{
			"targets": 0,
			"className": "text-left",
			"width": "0%"
		}],
		order: [[0, 'desc']],
	});

    var scan_out_table = $('#ScanOuttb').DataTable({
		destroy: true,
		processing: false,
		serverSide: true,
		responsive: false,
		dom: 'rt<"text-right"ip>',
		ajax: {
			url : "{{ url('scan_product_out_datatables') }}",
			data : function (d) {
				// d.pl_id = $('#pl_id_out').val();
				d.search = $('#scan_out_search').val();
				d.st_id = $('#st_id').val();
			}
		},
		columns: [
		{ data: 'article', name: 'article' , sortable: false },
		],
		columnDefs: [
		{
			"targets": 0,
			"className": "text-left",
			"width": "0%"
		}],
        order: [[0, 'desc']],
	});

	var in_table = $('#Intb').DataTable({
		destroy: true,
		processing: false,
		serverSide: true,
		responsive: false,
		dom: 'rt<"text-right"ip>',
		ajax: {
			url : "{{ url('product_in_datatables') }}",
			data : function (d) {
				// d.pl_id = $('#pl_id_out').val();
				d.search = $('#in_search').val();
				d.st_id = $('#st_id').val();
				d.waiting = $('#waiting_filter').val();
			}
		},
		columns: [
		{ data: 'article', name: 'article' , sortable: false },
		], 
		columnDefs: [
		{
			"targets": 0,
			"className": "text-left",
			"width": "0%"
		}],
		order: [[0, 'desc']],
	});

    var scan_in_table = $('#ScanIntb').DataTable({


        destroy: true,
        processing: false,
        serverSide: true,
        responsive: false,
        dom: 'rt<"text-right"ip>',
        ajax: {
            url : "{{ url('scan_product_in_datatables') }}",
            data : function (d) {
                // d.pl_id = $('#pl_id_out').val();
                d.search = $('#scan_in_search').val();
                d.st_id = $('#st_id').val();
                d.waiting = $('#waiting_filter').val();
            }
        },
        columns: [
            { data: 'article', name: 'article' , sortable: false },
        ],
        columnDefs: [
            {
                "targets": 0,
                "className": "text-left",
                "width": "0%"
            }],
        order: [[0, 'desc']],
    });
	
	$('#waiting_filter').on('change', function() {
		in_table.draw();
	});

    var stock_data_table = $('#HelperStockDatatb').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        responsive: false,
        dom: '<"text-right"l>rt<"text-right"ip>',
        buttons: [
            { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
        ],
        ajax: {
            url : "{{ url('helper_stock_data_datatables') }}",
            data : function (d) {
                d.search = $('#stock_data_search').val();
                d.st_id = $('#st_filter').val();
            }
        },
        columns: [
        { data: 'article_name', name: 'article_name', orderable: false },
        { data: 'article_stock', name: 'article_stock', orderable: false },
        ], 
        columnDefs: [
        {
            "targets": 0,
            "className": "text-left",
            "width": "0%"
        }],
        rowCallback: function( row, data, index) {
            if (data.article_stock.indexOf("<table></table>") >= 0) {
                $(row).hide();
            }
        },
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        language: {
            "lengthMenu": "_MENU_",
        },
        order: [[0, 'desc']],
    });
    
    $('#in_search').on('keyup', function() {
		in_table.draw();
	});

    $('#scan_in_search').on('keyup', function(event) {
        // scan_in_table.draw();
        console.log(event.keyCode)

        var rowData = this.data();

        console.log(this.data());

        if (event.keyCode === 13) {
            var scan_in_data = [];
            var totalRequests = 0;
            var completedRequests = 0;


            scan_in_table.rows().every(function () {
                totalRequests++;
                var rowData = this.data();
                var current_qty = $(this).attr('data-qty');
                console.log(this.data());
                scan_in_data.push({
                    _plst_id: rowData.plst_id,
                    _pls_id: rowData.pls_id,
                    _qty: rowData.plst_qty,

                });
            });

            scan_in_data.forEach(function(item) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ url('save_in_activity') }}",
                    type: "POST",
                    data: {
                        _plst_id: item._plst_id,
                        _pls_id: item._pls_id,
                        _qty: item._qty,
                    },
                    success: function (response) {
                        var responseObject = JSON.parse(response);
                        var status = responseObject.status;

                        completedRequests++;
                        if (completedRequests === totalRequests) {
                            // All requests have completed
                            if (status == 200) {
                                scan_in_table.draw();
                                toast('Dikeluarkan', ' berhasil dimasukkan', 'success');
                                // swal('Dikeluarkan', 'Berhasil keluar produk', 'success');
                            } else {
                                // toast('Dikeluarkan', ' berhasil dikeluarkan', 'success');
                                swal('Gagal', 'Gagal masuk produk', 'error');
                            }
                        }
                    },
                });
            });

        }
    });

	$('#out_search').on('keyup', function() {
		out_table.draw();
	});

    $('#scan_out_search').on('keyup', function(event) {
        scan_out_table.draw();
        if (event.keyCode === 13) {
            var scan_out_data = [];
            var totalRequests = 0;
            var completedRequests = 0;

            scan_out_table.rows().every(function () {
                totalRequests++;
                var rowData = this.data();
                scan_out_data.push({
                    _plst_id: rowData.plst_id,
                    _pls_id: rowData.pls_id,
                });
            });

            // Iterate through scan_out_data and make separate AJAX requests
            scan_out_data.forEach(function(item) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ url('save_out_activity') }}",
                    type: "POST",
                    data: {
                        _plst_id: item._plst_id,
                        _pls_id: item._pls_id,
                    },
                    success: function (response) {
                        var responseObject = JSON.parse(response);
                        var status = responseObject.status;

                        completedRequests++;
                        if (status == '200') {
                            scan_out_table.draw();
                            if (completedRequests === totalRequests) {
                                // All requests have completed
                                if (status == 200) {
                                    scan_out_table.draw();
                                    toast('Dikeluarkan', ' berhasil dikeluarkan', 'success');
                                    // swal('Dikeluarkan', 'Berhasil keluar produk', 'success');
                                } else {
                                    // toast('Dikeluarkan', ' berhasil dikeluarkan', 'success');
                                    swal('Gagal', 'Gagal keluar produk', 'error');
                                }
                            }
                        }
                    },
                });
            });
        }
    });


    var transfer_list_table = $('#TransferListtb').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        responsive: false,
        dom: '<"text-right"l>rt<"text-right"ip>',
        buttons: [
            { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
        ],
        ajax: {
            url : "{{ url('stock_transfer_list_datatables') }}",
            data : function (d) {
                d.search = $('#transfer_search').val();
                d.invoice = $('#transfer_invoice_label').val();
                d.mode = $('#_transfer_mode').val();
            }
        },
        columns: [
        { data: 'DT_RowIndex', name: 'stfd_id', searchable: false},
        { data: 'article', name: 'article', orderable: false },
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
    })

    var scan_transfer_list_table = $('#ScanTransferListtb').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        responsive: false,
        dom: '<"text-right"l>rt<"text-right"ip>',
        buttons: [
            { "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
        ],
        ajax: {
            url : "{{ url('stock_transfer_list_datatables') }}",
            data : function (d) {
                d.search = $('#scan_transfer_search').val();
                d.invoice = $('#scan_transfer_invoice_label').val();
                d.mode = $('#_scan_transfer_mode').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'stfd_id', searchable: false},
            { data: 'article', name: 'article', orderable: false },
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
    
    $('#transfer_search').on('keyup', function() {
		transfer_list_table.draw();
	});

    $('#scan_transfer_search').on('keyup', function(event) {
        scan_transfer_list_table.draw();

        if (event.keyCode === 13)
        {
            var scan_transfer_data = [];
            var totalRequests = 0;
            var completedRequests = 0;

            scan_transfer_list_table.rows().every(function () {
                totalRequests++;
                var rowData = this.data();
                scan_transfer_data.push({
                    _stfd_id: rowData.stfd_id,
                });
            });

            scan_transfer_data.forEach(function(item) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ url('get_transfer_item') }}",
                    type: "POST",
                    data: {
                        _stfd_id: item._stfd_id,
                    },
                    success: function (response) {
                        var responseObject = JSON.parse(response);
                        var status = responseObject.status;

                        completedRequests++;
                        if (status == '200') {
                            scan_transfer_list_table.draw();
                            if (completedRequests === totalRequests) {
                                // All requests have completed
                                if (status == 200) {
                                    scan_transfer_list_table.draw();
                                    toast('Dipindahkan', ' berhasil dipindahkan', 'success');
                                    // swal('Dipindahkan', 'Berhasil dipindahkan produk', 'success');
                                } else {
                                    // toast('Dipindahkan', ' berhasil dipindahkan', 'success');
                                    swal('Gagal', 'Gagal dipindahkan produk', 'error');
                                }
                            }
                        }
                    },
                });
            });
        }
    });


	var take_cross_order_table = $('#TakeCrossOrdertb').DataTable({
        destroy: true,
		processing: true,
		serverSide: true,
		responsive: false,
		dom: '<"text-right"l>rt<"text-right"ip>',
		buttons: [
			{ "extend": 'excelHtml5', "text":'Excel',"className": 'btn btn-primary btn-xs' }
		],
		ajax: {
			url : "{{ url('take_confirmation_datatables') }}",
			data : function (d) {
				d.pt_id = $('#cross_invoice').val();
			}
		},
		columns: [
		{ data: 'DT_RowIndex', name: 'ptd_id', searchable: false},
		{ data: 'article', name: 'article', orderable:false },
		{ data: 'pos_td_qty', name: 'pos_td_qty', orderable:false },
		{ data: 'pl_code', name: 'pl_code', orderable:false },
		{ data: 'action', name: 'action', orderable:false },
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

    $('#stock_data_search').on('keyup', function() {
        stock_data_table.draw();
    });
    
    $('#st_filter').on('change', function() {
		stock_data_table.draw();
	});
	
	$('#invoice').select2({
	 	width: "100%",
	 	dropdownParent: $('#invoice_parent')
	});
	$('#invoice').on('select2:open', function (e) {
	 	const evt = "scroll.select2";
	 	$(e.target).parents().off(evt);
	 	$(window).off(evt);
	});

	$('#pl_id_out').on('change', function (e) {
		out_table.draw();
	});

	$(document).delegate('#get_cross_item_btn', 'click', function() {
		var ptd_id = $(this).attr('data-ptd_id');
		var ptd_qty = $(this).attr('data-ptd_qty');
		swal({
			title: "Ambil..?",
			text: "Yakin sudah benar ?",
			icon: "warning",
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
					data: {_ptd_id:ptd_id, _ptd_qty:ptd_qty},
					dataType: 'json',
					url: "{{ url('get_cross_item_status')}}",
					success: function(r) {
						if (r.status == '200'){
							toast("Berhasil", "Berhasil ambil artikel", "success");
							take_cross_order_table.draw();
						} else {
							toast('Gagal', 'Gagal', 'error');
						}
					}
				});
				return false;
			}
		})
	});

	$(document).delegate('#get_out_btn', 'click', function() {
		var pls_id = $(this).attr('data-pls_id');
		var plst_id = $(this).attr('data-plst_id');
		var plst_qty = $(this).attr('data-plst_qty');
		var current_qty = $(this).attr('data-qty');
		var p_name = $(this).attr('data-p_name');
		var bin = $('#pl_id_out option:selected').text();
		var secret_code = $('#u_secret_code').val();
		var status = $(this).attr('data-status');
		if (current_qty >= 0) {
			swal({
				title: "Keluar..?",
				text: "Yakin keluarin produk "+p_name+" dari BIN "+bin+" ?",
				icon: "warning",
				buttons: [
					'Batal',
					'Yakin'
				],
				dangerMode: false,
			}).then(function(isConfirm) {
				if (isConfirm) {
				    $(this).prop('disabled', true);
					$.ajaxSetup({
						headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						}
					});
					$.ajax({
						type: "POST",
						data: {
                            _plst_qty:plst_qty,
                            _plst_id:plst_id,
                            _pls_id:pls_id,
                            _secret_code:secret_code,
                            _status:status},
						dataType: 'json',
						url: "{{ url('save_out_activity')}}",
						success: function(r) {
							if (r.status == '200'){
								out_table.draw();
								$(this).prop('disabled', false);
								toast('Dikeluarkan', p_name+' berhasil dikeluarkan', 'success');
							} else {
							    $(this).prop('disabled', false);
								swal('Gagal', 'Gagal keluar produk', 'error');
							}
						}
					});
					return false;
				}
			})
		}
	});

    $(document).delegate('#scan_get_out_btn', 'click', function() {
        console.log('running ga nih?');
		var pls_id = $(this).attr('data-pls_id');
		var plst_id = $(this).attr('data-plst_id');
		var plst_qty = $(this).attr('data-plst_qty');
		var current_qty = $(this).attr('data-qty');
		var p_name = $(this).attr('data-p_name');
		var bin = $('#pl_id_out option:selected').text();
		var secret_code = $('#u_secret_code').val();
		var status = $(this).attr('data-status');
		if (current_qty >= 0) {
			swal({
				title: "Keluar..?",
				text: "Yakin keluarin produk "+p_name+" dari BIN "+bin+" ?",
				icon: "warning",
				buttons: [
					'Batal',
					'Yakin'
				],
				dangerMode: false,
			}).then(function(isConfirm) {
				if (isConfirm) {
				    $(this).prop('disabled', true);
					$.ajaxSetup({
						headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						}
					});
					$.ajax({
						type: "POST",
						data: {
                            _plst_qty:plst_qty,
                            _plst_id:plst_id,
                            _pls_id:pls_id,
                            _secret_code:secret_code,
                            _status:status},
						dataType: 'json',
						url: "{{ url('save_out_activity')}}",
						success: function(r) {
							if (r.status == '200'){
								scan_out_table.draw();
								$(this).prop('disabled', false);
								toast('Dikeluarkan', p_name+' berhasil dikeluarkan', 'success');
							} else {
							    $(this).prop('disabled', false);
								swal('Gagal', 'Gagal keluar produk', 'error');
							}
						}
					});
					return false;
				}
			})
		}
	});

	$(document).delegate('#get_in_btn', 'click', function() {
		var plst_id = $(this).attr('data-plst_id');
		var pls_id = $(this).attr('data-pls_id');
		var plst_qty = $(this).attr('data-plst_qty');
		var p_name = $(this).attr('data-p_name');
		var bin = $(this).attr('data-bin');
		var current_qty = $(this).attr('data-qty');
		var secret_code = $('#u_secret_code').val();
		//alert(plst_id+' '+pls_id+' '+p_name+' '+bin+' '+current_qty+' '+secret_code);
		if (current_qty != 0) {
			swal({
				title: "Masuk..?",
				text: "Yakin sudah masukin produk "+p_name+" ke BIN "+bin+" ?",
				icon: "warning",
				buttons: [
					'Batal',
					'Yakin'
				],
				dangerMode: false,
			}).then(function(isConfirm) {
				if (isConfirm) {
				    $(this).prop('disabled', true);
					$.ajaxSetup({
						headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						}
					});
					$.ajax({
						type: "POST",
						data: {_qty:current_qty, _plst_qty:plst_qty, _pls_id:pls_id, _plst_id:plst_id, _secret_code:secret_code},
						dataType: 'json',
						url: "{{ url('save_in_activity')}}",
						success: function(r) {
							if (r.status == '200'){
								in_table.draw();
								$(this).prop('disabled', false);
								toast('Dimasukkan', p_name+' berhasil dimasukkan', 'success');
							} else {
							    $(this).prop('disabled', false);
								swal('Gagal', 'Gagal masukin produk', 'error');
							}
						}
					});
					return false;
				}
			})
		}
	});

    $(document).delegate('#scan_get_in_btn', 'click', function() {
		var plst_id = $(this).attr('data-plst_id');
		var pls_id = $(this).attr('data-pls_id');
		var plst_qty = $(this).attr('data-plst_qty');
		var p_name = $(this).attr('data-p_name');
		var bin = $(this).attr('data-bin');
		var current_qty = $(this).attr('data-qty');
		var secret_code = $('#u_secret_code').val();
		//alert(plst_id+' '+pls_id+' '+p_name+' '+bin+' '+current_qty+' '+secret_code);
		if (current_qty != 0) {
			swal({
				title: "Masuk..?",
				text: "Yakin sudah masukin produk "+p_name+" ke BIN "+bin+" ?",
				icon: "warning",
				buttons: [
					'Batal',
					'Yakin'
				],
				dangerMode: false,
			}).then(function(isConfirm) {
				if (isConfirm) {
				    $(this).prop('disabled', true);
					$.ajaxSetup({
						headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						}
					});
					$.ajax({
						type: "POST",
						data: {_qty:current_qty, _plst_qty:plst_qty, _pls_id:pls_id, _plst_id:plst_id, _secret_code:secret_code},
						dataType: 'json',
						url: "{{ url('save_in_activity')}}",
						success: function(r) {
							if (r.status == '200'){
								scan_in_table.draw();
								$(this).prop('disabled', false);
								toast('Dimasukkan', p_name+' berhasil dimasukkan', 'success');
							} else {
							    $(this).prop('disabled', false);
								swal('Gagal', 'Gagal masukin produk', 'error');
							}
						}
					});
					return false;
				}
			})
		}
	});

    $(document).delegate('#get_transfer_item', 'click', function() {
		var stfd_id = $(this).attr('data-stfd_id');
		var p_name = $(this).attr('data-p_name');
		var bin = $(this).attr('data-bin');
        swal({
            title: "Ambil..?",
            text: "Yakin sudah ambil produk "+p_name+" dari "+bin+" ?",
            icon: "warning",
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
                    data: {_stfd_id:stfd_id},
                    dataType: 'json',
                    url: "{{ url('get_transfer_item')}}",
                    success: function(r) {
                        if (r.status == '200'){
                            toast('Diambil', p_name+' berhasil diambil', 'success');
                            transfer_list_table.draw();
                        } else {
                            swal('Gagal', 'Gagal ambil produk', 'error');
                        }
                    }
                });
                return false;
            }
        })
	});

	$('#out_modal_finish, #in_modal_finish, #transfer_modal_finish, #transfer_detail_modal_finish, #scan_out_modal_finish, #scan_in_modal_finish, #scan_transfer_modal_finish').on('click', function (e) {
		$('#OutModal').modal('hide');
		$('#ScanOutModal').modal('hide');
		$('#InModal').modal('hide');
		$('#TrackingTypeModal').modal('hide');
		$('#InputCodeModal').modal('hide');
		$('#TransferModal').modal('hide');
        $('#ScanTransferModal').modal('hide');
		$('#_transfer_mode').val('');
		$('#TransferDetailModal').modal('hide');
        $('#ScanTransferDetailModal').modal('hide');
		$('#pl_id_out').val('').trigger('change');
	});

	$(document).delegate('#pl_id', 'click', function() {
		var ptd_id = $(this).attr('data-id');
		var pst_id = $(this).attr('data-pst');
		var pl_id = $(this).attr('data-pl_id');
		var pos_td_qty = $(this).attr('data-pos_td_qty');
		var p_name = $(this).attr('data-name');
		var invoice = $('#invoice_number').text();
		var secret_code = $('#u_secret_code').val();
		var bin = $(this).text();
		swal({
			title: "Ambil..?",
			text: "Yakin ambil produk "+p_name+" dari BIN "+bin+"",
			icon: "warning",
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
					data: {_pos_td_qty:pos_td_qty, _ptd_id:ptd_id, _pst_id:pst_id , _pl_id:pl_id, _invoice:invoice, _secret_code:secret_code},
					dataType: 'json',
					url: "{{ url('save_tracking_activity')}}",
					success: function(r) {
						if (r.status == '200'){
							reloadOrderList();
						} else {
							swal('Gagal', 'Gagal simpan data', 'error');
						}
					}
				});
				return false;
			}
		})
	});
	
	$(document).delegate('.cancel_order_list', 'click', function() {
		var ptd_id = $(this).attr('data-id');
		var pst_id = $(this).attr('data-pst');
		var pl_id = $(this).attr('data-pl_id');
		var pos_td_qty = $(this).attr('data-pos_td_qty');
		var p_name = $(this).attr('data-name');
		var invoice = $('#invoice_number').text();
		var secret_code = $('#u_secret_code').val();
		var bin = $(this).text();
		swal({
			title: "Yakin Batal..?",
			text: "Yakin batalkan produk "+p_name+" dari BIN "+bin+" ?",
			icon: "warning",
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
					data: {_pos_td_qty:pos_td_qty, _ptd_id:ptd_id, _pst_id:pst_id , _pl_id:pl_id, _invoice:invoice, _secret_code:secret_code},
					dataType: 'json',
					url: "{{ url('cancel_tracking_activity')}}",
					success: function(r) {
						if (r.status == '200'){
							reloadOrderList();
						} else {
							swal('Gagal', 'Gagal simpan data', 'error');
						}
					}
				});
				return false;
			}
		})
	});

	$(document).delegate('#done_btn', 'click', function() {
		var plst_id = $(this).attr('data-plst_id');
		var secret_code = $('#u_secret_code').val();
		var p_name = $(this).attr('data-name');
		swal({
			title: "Sudah Dipacking..?",
			text: "Yakin sudah packing produk "+p_name+" ?",
			icon: "warning",
			buttons: [
				'Batal',
				'Sudah'
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
					data: {_plst_id:plst_id, _secret_code:secret_code},
					dataType: 'json',
					url: "{{ url('save_packing_activity')}}",
					success: function(r) {
						if (r.status == '200'){
							reloadPackingList();
						} else {
							swal('Gagal', 'Gagal packing data', 'error');
						}
					}
				});
				return false;
			}
		})
	});

	$(document).delegate('#reject_btn', 'click', function() {
		var plst_id = $(this).attr('data-plst_id');
		var secret_code = $('#u_secret_code').val();
		var p_name = $(this).attr('data-name');
		swal({
			title: "Reject..?",
			text: "Yakin reject produk "+p_name+" ?",
			icon: "warning",
			buttons: [
				'Batal',
				'Reject'
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
					data: {_plst_id:plst_id, _secret_code:secret_code},
					dataType: 'json',
					url: "{{ url('save_reject_activity')}}",
					success: function(r) {
						if (r.status == '200'){
							reloadPackingList();
						} else {
							swal('Gagal', 'Gagal reject data', 'error');
						}
					}
				});
				return false;
			}
		})
	});

    $('#f_login').on('submit', function(e) {
        e.preventDefault();
		var data = $(this).serialize();
        var email = $('#u_email').val();
        var password = $('#password').val();
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

        if ($.trim(email)=='') {
            swal("Email","Silahkan input email", "warning");
            return false;
        } else if (!emailReg.test(email)) {
            swal("Email","Silahkan input email sesuai format", "warning");
            return false;
        } else if ($.trim(password)=='') {
            swal("Password","Silahkan input password", "warning");
            return false;
        // } else if(grecaptcha.getResponse() == "") {
		// 	swal("Recaptcha","Silahkan validasi recaptcha", "warning");
        //     return false;
		} else {
			$.ajax({
				type: "POST",
				data: data,
				dataType: 'json',
				url: "{{ url('user_login') }}",
				success: function(r) {
					if (r.status=='200') {
						swal('Berhasil','Login berhasil','success');
						setTimeout(() => {
							window.location.href="dashboard";
						}, 600);
					} else if (r.status=='400') {
						swal('Gagal','Email atau password salah','error');
					} else {
						swal('Nonaktif','Status akun anda tidak aktif / dihapus','error');
					}
				}
			});
			return false;
		}
    });

	$('#article_btn').on('click', function(e) {
		e.preventDefault();
		$('#ArticleModal').on('show.bs.modal', function() {
            stock_data_table.draw();
        }).modal('show');
	});

	$('#transfer_btn').on('click', function(e) {
		e.preventDefault();
		$('#_transfer_mode').val('get');
		$('#TransferModal').on('show.bs.modal', function() {
            $.ajax({
				type: "GET",
				dataType: 'html',
				url: "{{ url('reload_transfer_invoice') }}",
				success: function(r) {
					$('.transfer_invoice').html(r);
				}
			});
        }).modal('show');
	});

    $('#scan_transfer_btn').on('click', function(e) {
        e.preventDefault();
        $('#_scan_transfer_mode').val('get');
        $('#ScanTransferModal').on('show.bs.modal', function() {
            $.ajax({
                type: "GET",
                dataType: 'html',
                url: "{{ url('reload_scan_transfer_invoice') }}",
                success: function(r) {
                    $('.scan_transfer_invoice').html(r);
                }
            });
        }).modal('show');
    });

	$('#transfer_invoice_btn').on('click', function(e) {
		e.preventDefault();
		$('#_transfer_mode').val('check');
		$('#TransferModal').on('show.bs.modal', function() {
            $.ajax({
				type: "GET",
				dataType: 'html',
				url: "{{ url('reload_transfer_invoice_check') }}",
				success: function(r) {
					$('.transfer_invoice').html(r);
				}
			});
        }).modal('show');
	});
	
	$('#invoice_take_btn').on('click', function(e) {
		e.preventDefault();
		$('#_type').val('helper');
		$('#ScannerModal').on('show.bs.modal', function() {
            $.ajax({
				type: "GET",
				dataType: 'html',
				url: "{{ url('reload_order_invoice') }}",
				success: function(r) {
					$('.invoice_list').html(r);
				}
			});
        }).modal('show');
        $('#play').trigger('click');
	});

	$('#invoice_pack_btn').on('click', function(e) {
		e.preventDefault();
		$('#_type').val('packer');
		$('#ScannerModal').on('show.bs.modal', function() {
            $.ajax({
				type: "GET",
				dataType: 'html',
				url: "{{ url('reload_order_invoice') }}",
				success: function(r) {
					$('.invoice_list').html(r);
				}
			});
        }).modal('show');
        $('#play').trigger('click');
	});

	$('#ScannerModal').on('hide.bs.modal', function() {
		$('#stop').trigger('click');
	});

    jQuery.noConflict();
    $('#data_stok_btn').on('click', function(e) {
        e.preventDefault();
        $('#st_id').val('');
        $('#dataStokModal').modal('show');
        out_table.draw();
    });

    jQuery.noConflict();
	$('#out_btn').on('click', function(e) {
		e.preventDefault();
		$('#st_id').val('');
		$('#OutModal').modal('show');
		out_table.draw();
	});

    jQuery.noConflict();
    $('#scan_out_btn').on('click', function(e) {
        e.preventDefault();
        $('#st_id').val('');
        $('#ScanOutModal').modal('show');
        scan_out_table.draw();
    });

	$('#in_btn').on('click', function(e) {
		e.preventDefault();
		$('#st_id').val('');
		$('#InModal').modal('show');
		in_table.draw();
	});

    $('#scan_in_btn').on('click', function(e) {
		e.preventDefault();
		$('#st_id').val('');
		$('#ScanInModal').modal('show');
		scan_in_table.draw();
	});

	$('#urban_out_btn').on('click', function(e) {
		e.preventDefault();
		$('#st_id').val('4');
		$('#OutModal').modal('show');
		out_table.draw();
	});

	$('#urban_in_btn').on('click', function(e) {
		e.preventDefault();
		$('#st_id').val('4');
		$('#InModal').modal('show');
		in_table.draw();
	});

	$('#take_cross_order_btn').on('click', function(e) {
		e.preventDefault();
		$.ajaxSetup({
			headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			url:"{{ url('reload_cross_order_invoice') }}",
			method:"POST",
			success:function(data){
				$('#cross_invoice_content').html(data);
				$('#TakeCrossOrderModal').modal('show');
			}
		});
	});

	$(document).delegate('#cross_invoice', 'change', function() {
		if ($(this).val() == '') {
			$('#TakeCrossOrdertb').addClass('d-none');
		} else {
			$('#TakeCrossOrdertb').removeClass('d-none');
			take_cross_order_table.draw();
		}
	});

	$('#TakeCrossOrderModal').on('hide.bs.modal', function() {
		$('#TakeCrossOrdertb').addClass('d-none');
	}).modal('hide');

	$('#article_name').keyup(function(){ 
        var query = $(this).val();
        if($.trim(query) != '') {
			$.ajaxSetup({
				headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				url:"{{ url('autocomplete_fetch') }}",
				method:"POST",
				data:{query:query},
				success:function(data){
					$('#articleList').fadeIn();  
					$('#articleList').html(data);
				}
			});
        } else {
			$('#articleList').fadeOut();  
		}
    });

	$(document).delegate('#show_item', 'click', function(){  
        $('#ShowArticleModal').modal('show');
		var pid = $(this).attr('data-id');
		var p_name = $(this).attr('data-p_name');
		var br_name = $(this).attr('data-br_name');
		$.ajaxSetup({
			headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			type: "POST",
			data: {_pid:pid, _p_name:p_name, _br_name:br_name},
			dataType: 'html',
			url: "{{ url('check_article')}}",
			success: function(r) {
				$('#article_detail_content').html(r);
			}
		});
    });

	$('#scanned-result').on('change', function(e) {
		e.preventDefault();
		var qr = $(this).val();
		var type = $('#_type').val();
		//alert(qr);
		$('#invoice_number').text(qr);
		$('#OrderListModal').modal('show');
		if (type == 'helper') {
			$.ajaxSetup({
				headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				type: "POST",
				data: {_qr:qr},
				dataType: 'html',
				url: "{{ url('order_list_by_invoice')}}",
				success: function(r) {
					//$('#u_secret_code').val('');
					$('#stop').trigger('click');
					$('#scanned-result').val('');
					$('#InputCodeModal').modal('hide');
					$('#TrackingTypeModal').modal('hide');
					$('#ScannerModal').modal('hide');
					$('#orderListItem').html(r);
				}
			});
		} else {
			$.ajaxSetup({
				headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				type: "POST",
				data: {_qr:qr},
				dataType: 'html',
				url: "{{ url('packing_list_by_invoice')}}",
				success: function(r) {
					//$('#u_secret_code').val('');
					$('#stop').trigger('click');
					$('#scanned-result').val('');
					$('#InputCodeModal').modal('hide');
					$('#TrackingTypeModal').modal('hide');
					$('#ScannerModal').modal('hide');
					$('#orderListItem').html(r);
				}
			});
		}
	});

    $(document).on('change','#transfer_invoice', function(e) {
		e.preventDefault();
		var invoice = $('#transfer_invoice option:selected').text();
		$('#transfer_invoice_modal_label').text(invoice);
		$('#transfer_invoice_label').val(invoice);
		$('#TransferDetailModal').on('show.bs.modal', function() {
            transfer_list_table.draw();
        }).modal('show');
	});

    $(document).on('change','#scan_transfer_invoice', function(e) {
        e.preventDefault();
        var invoice = $('#scan_transfer_invoice option:selected').text();
        $('#scan_transfer_invoice_modal_label').text(invoice);
        $('#scan_transfer_invoice_label').val(invoice);
        $('#ScanTransferDetailModal').on('show.bs.modal', function() {
            scan_transfer_list_table.draw();
        }).modal('show');
    });
</script>