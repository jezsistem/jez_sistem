<script>
    let chat_status = 'closed';
    let ot_id = null;

    function openChat($trx_id) {
        jQuery.noConflict();
        $('#chatModal').modal('show');
        // Get trx_number from the button's data attribute and update the modal title
        var trx_number = $(event.target).closest('button').data('trx_number');
        $('.trx_number_title').text(trx_number);
        setChatOpenStatus();
        ot_id = $trx_id;
        getChatData(ot_id);
    }

    function closeChat() {
        jQuery.noConflict();
        $('#chatModal').modal('hide');
        setChatOpenStatus();
        ot_id = null;
    }

    function setChatOpenStatus() {
        if (chat_status == 'closed') {
            chat_status = 'opened'
        } else {
            chat_status = 'closed'
        }
    }

    function startChatPolling() {
        setInterval(function() {
            if (chat_status === 'opened' && ot_id !== null) {
                getChatData(ot_id);
            }
        }, 5000);
    }

    function getChatData($ot_id) {
        $.ajax({
            url: "{{ url('get_chat_history_online_transaction') }}/" + $ot_id,
            type: 'GET',
            data: {
                is_amp: 0,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === '200') {
                    var chatHistory = response.data;
                    var chatContainer = $('.chat-messages');
                    chatContainer.empty(); // Clear existing messages

                    chatHistory.forEach(function(chat) {
                        var messageElement;

                        if (chat.is_amp == 0) {
                            // Sent message (You)
                            messageElement = $(`
                                <div class="d-flex justify-content-end mb-3">
                                    <div class="bg-danger text-white rounded px-6 py-2" style="max-width: 70%;">
                                        <small class="text-light font-weight-bold">${chat.u_name ? chat.u_name : 'You'}</small>
                                        <p class="mb-1">${chat.messages}</p>
                                        <small class="text-light">${new Date(chat.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</small>
                                    </div>
                                </div>
                            `);
                        } else {
                            // Received message (Other user)
                            messageElement = $(`
                                <div class="d-flex justify-content-start mb-3">
                                    <div class="bg-secondary border rounded px-6 py-2" style="max-width: 70%;">
                                        <small class="text-muted font-weight-bold">${chat.u_name ? chat.u_name : 'User'}</small>
                                        <p class="mb-1">${chat.messages}</p>
                                        <small class="text-muted">${new Date(chat.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</small>
                                    </div>
                                </div>
                            `);
                        }

                        chatContainer.append(messageElement);
                    });

                    // Scroll to the bottom of the chat container
                    chatContainer.scrollTop(chatContainer[0].scrollHeight);
                } else {
                    toastr.error('Failed to load chat history. Please try again.');
                }
            },
            error: function(xhr, status, error) {
                toastr.error('An error occurred while fetching chat history. Please try again.');
                console.error('Error fetching chat history:', error);
            }
        });

    }

    function sendChatMessage() {
        var message = $('#text_input').val();
        var ot_id_new = ot_id; // Use JavaScript variable, not PHP variable

        if (message.trim() === '') {
            return;
        }

        $.ajax({
            url: "{{ url('send_chat_history_online_transaction') }}",
            type: 'POST',
            data: {
                ot_id: ot_id_new,
                message: message,
                is_amp: 0,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === '200') {
                    // Show success toast
                    toastr.success('Message sent successfully!');
                    $('#text_input').val('');
                    // Refresh chat data
                    getChatData(ot_id);
                }
            },
            error: function(xhr, status, error) {
                toastr.error('Failed to send message. Please try again.');
                console.error('Error sending message:', error);
            }
        });
    }


    function loadStorageAreas() {
        $.ajax({
            type: "GET",
            dataType: 'html',
            url: "{{ url('reload_storage_area') }}",
            success: function(r) {
                $("#storage_area_select").html(r);
            }
        });
        return false;
    }
    modal_opened = null;
    function reloadOrderList() {
        var qr = $('#invoice_number').text();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {
                _qr: qr
            },
            dataType: 'html',
            url: "{{ url('order_list_by_invoice') }}",
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

    {{--var stock_data_table = $('#StockDatatb').DataTable({--}}
    {{--    destroy: true,--}}
    {{--    processing: false,--}}
    {{--    serverSide: true,--}}
    {{--    responsive: false,--}}
    {{--    dom: 'rt<"text-right"ipl>',--}}
    {{--    buttons: [{--}}
    {{--        "extend": 'excelHtml5',--}}
    {{--        "text": 'Excel',--}}
    {{--        "className": 'btn btn-primary btn-xs'--}}
    {{--    }],--}}
    {{--    ajax: {--}}
    {{--        url: "{{ url('stock_data_datatables') }}",--}}
    {{--        data: function(d) {--}}
    {{--            d.search = $('#stock_data_search').val();--}}
    {{--            d.search_scan = $('#stock_data_search_scan').val();--}}
    {{--            d.br_id = $('#br_id').val();--}}
    {{--            d.pc_id = $('#pc_id').val();--}}
    {{--            d.psc_id = $('#psc_id').val();--}}
    {{--            d.pssc_id = $('#pssc_id').val();--}}
    {{--            d.sz_id = $('#sz_id').val();--}}
    {{--            d.gender_id = $('#gender_id').val();--}}
    {{--            d.main_color_id = $('#main_color_id').val();--}}
    {{--            d.display_status = $('#display_status').val();--}}
    {{--            d.st_id = $('#st_id_filter').val();--}}
    {{--        }--}}
    {{--    },--}}
    {{--    columns: [{--}}
    {{--            data: 'article_name',--}}
    {{--            name: 'article_name',--}}
    {{--            orderable: false--}}
    {{--        },--}}
    {{--        {--}}
    {{--            data: 'article_stock',--}}
    {{--            name: 'article_stock',--}}
    {{--            orderable: false--}}
    {{--        },--}}
    {{--    ],--}}
    {{--    columnDefs: [{--}}
    {{--        "targets": 0,--}}
    {{--        "className": "text-left",--}}
    {{--        "width": "0%"--}}
    {{--    }],--}}
    {{--    rowCallback: function(row, data, index) {--}}
    {{--        if (data.article_stock.indexOf("<table></table>") >= 0) {--}}
    {{--            $(row).hide();--}}
    {{--        }--}}
    {{--    },--}}
    {{--    lengthMenu: [--}}
    {{--        [10, 25, 50, 100, -1],--}}
    {{--        [10, 25, 50, 100, "Semua"]--}}
    {{--    ],--}}
    {{--    language: {--}}
    {{--        "lengthMenu": "_MENU_",--}}
    {{--    },--}}
    {{--    order: [--}}
    {{--        [0, 'desc']--}}
    {{--    ],--}}
    {{--});--}}
    {{--var oSettings = stock_data_table.settings();--}}


    {{--var aging_table = $('#Agingtb').DataTable({--}}
    {{--    destroy: true,--}}
    {{--    processing: true,--}}
    {{--    serverSide: true,--}}
    {{--    responsive: false,--}}
    {{--    dom: 'B<"text-right"l>rt<"text-right"ip>',--}}
    {{--    buttons: [{--}}
    {{--        "extend": 'excelHtml5',--}}
    {{--        "text": 'Excel',--}}
    {{--        "className": 'btn btn-primary btn-xs'--}}
    {{--    }],--}}
    {{--    ajax: {--}}
    {{--        url: "{{ url('aging_datatables') }}",--}}
    {{--        data: function(d) {--}}
    {{--            d.search = $('#aging_search').val();--}}
    {{--            d.br_id = $('#br_id').val();--}}
    {{--            d.pc_id = $('#pc_id').val();--}}
    {{--            d.psc_id = $('#psc_id').val();--}}
    {{--            d.pssc_id = $('#pssc_id').val();--}}
    {{--            d.sz_id = $('#sz_id').val();--}}
    {{--            d.st_id = $('#st_id_filter').val();--}}
    {{--        }--}}
    {{--    },--}}
    {{--    columns: [{--}}
    {{--            data: 'DT_RowIndex',--}}
    {{--            name: 'pst_id',--}}
    {{--            searchable: false--}}
    {{--        },--}}
    {{--        {--}}
    {{--            data: 'st_name',--}}
    {{--            name: 'st_name'--}}
    {{--        },--}}
    {{--        {--}}
    {{--            data: 'aging_po',--}}
    {{--            name: 'aging_po',--}}
    {{--            orderable: false--}}
    {{--        },--}}
    {{--        {--}}
    {{--            data: 'aging_tf',--}}
    {{--            name: 'aging_tf',--}}
    {{--            orderable: false--}}
    {{--        },--}}
    {{--        {--}}
    {{--            data: 'pc_name',--}}
    {{--            name: 'pc_name'--}}
    {{--        },--}}
    {{--        {--}}
    {{--            data: 'psc_name',--}}
    {{--            name: 'psc_name'--}}
    {{--        },--}}
    {{--        {--}}
    {{--            data: 'pssc_name',--}}
    {{--            name: 'pssc_name'--}}
    {{--        },--}}
    {{--        {--}}
    {{--            data: 'br_name',--}}
    {{--            name: 'br_name'--}}
    {{--        },--}}
    {{--        {--}}
    {{--            data: 'p_name',--}}
    {{--            name: 'p_name'--}}
    {{--        },--}}
    {{--        {--}}
    {{--            data: 'p_color',--}}
    {{--            name: 'p_color'--}}
    {{--        },--}}
    {{--        {--}}
    {{--            data: 'sz_name',--}}
    {{--            name: 'sz_name'--}}
    {{--        },--}}

    {{--        {--}}
    {{--            data: 'stock',--}}
    {{--            name: 'aging',--}}
    {{--            orderable: false--}}
    {{--        },--}}
    {{--    ],--}}
    {{--    columnDefs: [{--}}
    {{--        "targets": 0,--}}
    {{--        "className": "text-center",--}}
    {{--        "width": "0%"--}}
    {{--    }],--}}
    {{--    lengthMenu: [--}}
    {{--        [10, 25, 50, 100, -1],--}}
    {{--        [10, 25, 50, 100, "Semua"]--}}
    {{--    ],--}}
    {{--    language: {--}}
    {{--        "lengthMenu": "_MENU_",--}}
    {{--    },--}}
    {{--    order: [--}}
    {{--        [0, 'desc']--}}
    {{--    ],--}}
    {{--});--}}

    var pickup_list_table = $('#PickupListtb').DataTable({
        destroy: true,
        processing: false,
        serverSide: true,
        responsive: false,
        dom: 'rt<"text-right"ip>',
        ajax: {
            url: "{{ url('pickup_list_datatables') }}",
            data: function(d) {
                d.search = $('#pick_data_search').val();
                d.st_id = "{{ $data['user']->st_id }}";
            }
        },
        columns: [{
                data: 'article',
                name: 'p_name',
                orderable: false
            },
            {
                data: 'bin',
                name: 'pl_code, orderable: false'
            },
            {
                data: 'datetime',
                name: 'plst_created',
                orderable: false
            },
            {
                data: 'user',
                name: 'user',
                orderable: false
            },
            {
                data: 'status',
                name: 'p_name',
                orderable: false
            },
            {
                data: 'action',
                name: 'p_name',
                orderable: false
            },
        ],
        columnDefs: [{
            "targets": 0,
            "className": "text-left",
            "width": "0%"
        }],
        order: [
            [0, 'desc']
        ],
    });

    // stock_data_table.buttons().container().appendTo($('#stock_data_excel_btn'));
    // $('#stock_data_search').on('keyup', function() {
    //     var query = jQuery(this).val();
    //     if (jQuery.trim(query).length > 2) {
    //         stock_data_table.draw();
    //     } else if (jQuery.trim(query).length == 0) {
    //         stock_data_table.draw();
    //     }
    // });

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

    {{--$(document).delegate('#aging_detail', 'click', function(e) {--}}
    {{--    swal($(this).attr('title'));--}}
    {{--});--}}

    {{--$(document).delegate('#pickup_item', 'click', function(e) {--}}
    {{--    e.preventDefault();--}}
    {{--    var st_id = {{ $data['user']->st_id }};--}}

    {{--    var pst_id = $(this).attr('data-pst_id');--}}
    {{--    var pl_id = $(this).attr('data-pl_id');--}}
    {{--    var qty = $(this).attr('data-qty');--}}
    {{--    var pls_id = $(this).attr('data-pls_id');--}}
    {{--    var p_name = $(this).attr('data-p_name');--}}
    {{--    var pl_code = $(this).attr('data-pl_code');--}}
    {{--    var bin = $(this).attr('data-bin');--}}
    {{--    @if (strtolower($data['user']->stt_name) == 'offline')--}}
    {{--        if (st_id == {{ $data['user']->st_id }}) {--}}
    {{--            swal({--}}
    {{--                title: "Pickup..?",--}}
    {{--                text: "Yakin pickup item " + p_name + " dari bin " + bin + " ?",--}}
    {{--                icon: "warning",--}}
    {{--                buttons: [--}}
    {{--                    'Batal',--}}
    {{--                    'Yakin'--}}
    {{--                ],--}}
    {{--                dangerMode: false,--}}
    {{--            }).then(function(isConfirm) {--}}
    {{--                if (isConfirm) {--}}
    {{--                    $.ajaxSetup({--}}
    {{--                        headers: {--}}
    {{--                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
    {{--                        }--}}
    {{--                    });--}}
    {{--                    $.ajax({--}}
    {{--                        type: "POST",--}}
    {{--                        data: {--}}
    {{--                            _pls_id: pls_id,--}}
    {{--                            _pst_id: pst_id,--}}
    {{--                            _pl_id: pl_id,--}}
    {{--                            _pl_code: pl_code--}}
    {{--                        },--}}
    {{--                        dataType: 'json',--}}
    {{--                        url: "{{ url('pickup_item') }}",--}}
    {{--                        success: function(r) {--}}
    {{--                            if (r.status == '200') {--}}
    {{--                                toast("Berhasil", "Item berhasil dipickup", "success");--}}
    {{--                                stock_data_table.draw();--}}
    {{--                                pickup_list_table.draw();--}}
    {{--                            } else {--}}
    {{--                                toast('Gagal', 'Gagal pickup item', 'error');--}}
    {{--                            }--}}
    {{--                        }--}}
    {{--                    });--}}
    {{--                    return false;--}}
    {{--                }--}}
    {{--            })--}}
    {{--        }--}}
    {{--    @endif--}}
    {{--});--}}

    function reloadPackingList() {
        var qr = $('#invoice_number').text();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            data: {
                _qr: qr
            },
            dataType: 'html',
            url: "{{ url('packing_list_by_invoice') }}",
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
            url: "{{ url('product_out_datatables') }}",
            data: function(d) {
                // d.pl_id = $('#pl_id_out').val();
                d.search = $('#out_search').val();
                d.st_id = $('#st_id').val();
                d.sa_id = $('#storage_area').val();
            }
        },
        columns: [{
            data: 'article',
            name: 'article',
            sortable: false
        }, ],
        columnDefs: [{
            "targets": 0,
            "className": "text-left",
            "width": "0%"
        }],
        order: [
            [0, 'desc']
        ],
    });
    //
    // function refreshTable() {
    //     out_table.ajax.reload(null, false); // user paging is not reset on reload
    // }
    //
    // // Set interval to refresh the DataTable every 30 seconds (30000 milliseconds)
    // setInterval(refreshTable, 3000);

    var scanOutTbEnterPressed = false;
    var previousItemCount = 0;

    // Preload the audio element
    var audio = new Audio("{{ asset('music/ADELE.mp3') }}");
    audio.preload = 'auto';

    // Function to play sound after user interaction
    function playSound() {
        audio.play().catch(function(error) {
            console.log('Autoplay prevented, user interaction required');
        });
    }

    // Waiting Online
    var scan_keep_table = $('#ScanInOnlinetb').DataTable({
        destroy: true,
        processing: false,
        serverSide: true,
        responsive: false,
        dom: 'rt<"text-right"ip>',
        ajax: {
            url: "{{ url('scan_product_online_datatables') }}",
            data: function(d) {
                d.search = $('#scan_in_search').val();
                d.st_id = $('#st_id').val();
                d.waiting = 'WAITING ONLINE';
            }
        },
        columns: [{
            data: 'article',
            name: 'article',
            sortable: false
        }],
        columnDefs: [{
            "targets": 0,
            "className": "text-left",
            "width": "0%"
        }],
        order: [
            [0, 'desc']
        ],
        drawCallback: function(settings) {
            var api = this.api();
            $('#scan_out_search').off('keyup').on('keyup', function(event) {
                if (event.keyCode === 13) {
                    scanInTbEnterPressed = true;
                    api.search(this.value).draw();
                }
            });
        }
    });

    var scan_out_table = $('#ScanOuttb').DataTable({
        destroy: true,
        processing: false,
        serverSide: true,
        responsive: false,
        dom: 'rt<"text-right"ip>',
        ajax: {
            url: "{{ url('scan_product_out_datatables') }}",
            data: function(d) {
                d.search = $('#scan_out_search').val();
                d.st_id = $('#st_id').val();
                d.sa_id = $('#storage_area').val();
            }
        },
        columns: [{
            data: 'article',
            name: 'article',
            sortable: false
        }],
        columnDefs: [{
            "targets": 0,
            "className": "text-left",
            "width": "0%"
        }],
        order: [
            [0, 'desc']
        ],
        drawCallback: function(settings) {
            var api = this.api();
            var currentItemCount = api.rows({ filter: 'applied' }).count();

            // Check if the current item count is greater than the previous count
            if (currentItemCount > previousItemCount) {
                playSound();
            }

            // Update the previous item count
            previousItemCount = currentItemCount;

            $('#scan_out_search').off('keyup').on('keyup', function(event) {
                if (event.keyCode === 13) {
                    scanOutTbEnterPressed = true;
                    api.search(this.value).draw();
                }
            });
        }
    });

    function refreshScanOutTable() {
        scan_out_table.ajax.reload(null, false);
    }

    setInterval(refreshScanOutTable, 3000);

    $('#ScanOuttb').on('draw.dt', function() {
        if (!scanOutTbEnterPressed) {
            return;
        }

        // if input is empty, do nothing
        if ($('#scan_out_search').val().trim() === '') {
            return;
        }

        scanInTbEnterPressed = false;

        var rowsData = scan_out_table.rows({
            page: 'current'
        }).data();
        var scan_out_data = [];

        if (rowsData.length > 0) {
            var rowData = rowsData[0];

            scan_out_data.push({
                _plst_id: rowData.plst_id,
                _pls_id: rowData.pls_id,
                _qty: rowData.plst_qty,
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var item = scan_out_data[0]; // Only process the first item

            console.log(item);

            $.ajax({
                url: "{{ url('save_out_activity') }}",
                type: "POST",
                data: {
                    _plst_id: item._plst_id,
                    _pls_id: item._pls_id,
                    _qty: item._qty,
                },
                success: function(response) {
                    var responseObject = JSON.parse(response);
                    var status = responseObject.status;
                    $('#scan_out_search').val('');
                    scan_out_table.ajax.reload();

                    if (status == 200) {
                        toast('Dikeluarkan', ' berhasil dikeluarkan', 'success');
                    } else {
                        swal('Gagal', 'Gagal masuk produk', 'error');
                    }
                },
            });
        }
    });

    var in_table = $('#Intb').DataTable({
        destroy: true,
        processing: false,
        serverSide: true,
        responsive: false,
        dom: 'rt<"text-right"ip>',
        ajax: {
            url: "{{ url('product_in_datatables') }}",
            data: function(d) {
                // d.pl_id = $('#pl_id_out').val();
                d.search = $('#in_search').val();
                d.st_id = $('#st_id').val();
                d.waiting = $('#waiting_filter').val();
            }
        },
        columns: [{
            data: 'article',
            name: 'article',
            sortable: false
        }, ],
        columnDefs: [{
            "targets": 0,
            "className": "text-left",
            "width": "0%"
        }],
        order: [
            [0, 'desc']
        ],

    });

    var scanInTbEnterPressed = false;

    var scan_in_table = $('#ScanIntb').DataTable({
        destroy: true,
        processing: false,
        serverSide: true,
        responsive: false,
        dom: 'rt<"text-right"ip>',
        ajax: {
            url: "{{ url('scan_product_in_datatables') }}",
            data: function(d) {
                d.search = $('#scan_in_search').val();
                d.st_id = $('#st_id').val();
                d.waiting = $('#waiting_filter').val();
            }
        },
        columns: [{
            data: 'article',
            name: 'article',
            sortable: false
        }],
        columnDefs: [{
            "targets": 0,
            "className": "text-left",
            "width": "0%"
        }],
        order: [
            [0, 'desc']
        ],
        drawCallback: function(settings) {
            var api = this.api();
            $('#scan_out_search').off('keyup').on('keyup', function(event) {
                if (event.keyCode === 13) {
                    scanInTbEnterPressed = true;
                    api.search(this.value).draw();
                }
            });
        }
    });

    // const scanner = new Html5QrcodeScanner('reader', {
    //     // Scanner will be initialized in DOM inside element with id of 'reader'
    //     qrbox: {
    //         width: 250,
    //         height: 250,
    //     },
    //     fps: 30,
    // });

    // scanner.render(success, error);

    // function success(result) {
    //
    //     var hasil = result;
    //
    //     if (hasil.startsWith(']C1')) {
    //         hasil = hasil.replace(']C1', '');
    //     }
    //
    //     alert(hasil);
    //
    //     $('#scan_in_search').val(hasil);
    //
    //     scan_in_table.ajax.reload();
    //
    // }

    // function error(err) {
    //     console.error(err);
    //     // Prints any errors to the console
    // }

    // function console_log(result) {
    //     console.log(result);
    // }


    $('#ScanIntb').on('draw.dt', function() {
        if (!scanInTbEnterPressed) {
            return;
        }

        if ($('#scan_in_search').val().trim() === '') {
            return;
        }

        scanInTbEnterPressed = false;

        var rowsData = scan_in_table.rows({
            page: 'current'
        }).data();
        var scan_in_data = [];

        if (rowsData.length > 0) {
            var rowData = rowsData[0];

            console.log(rowData);

            scan_in_data.push({
                _plst_id: rowData.plst_id,
                _pls_id: rowData.pls_id,
                _qty: rowData.plst_qty,
            });

            swal({
                title: rowData.pl_name + "..?",
                text: "Yakin BIN Kembali sudah benar ?",
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

                    var item = scan_in_data[0]; // Only process the first item

                    $.ajax({
                        url: "{{ url('save_in_activity') }}",
                        type: "POST",
                        data: {
                            _plst_id: item._plst_id,
                            _pls_id: item._pls_id,
                            _qty: item._qty,
                        },
                        success: function(response) {
                            var responseObject = JSON.parse(response);
                            var status = responseObject.status;
                            $('#scan_in_search').val('');
                            scan_in_table.ajax.reload();

                            if (status == 200) {
                                toast('Dikeluarkan', ' berhasil dimasukkan', 'success');
                            } else {
                                swal('Gagal', 'Gagal masuk produk', 'error');
                            }
                        },
                    });
                }
            })


        }
    });


    //Refund
    var scanInRefundTbEnterPressed = false;

    var scan_in_refund_table = $('#ScanInRefundtb').DataTable({
        destroy: true,
        processing: false,
        serverSide: true,
        responsive: false,
        dom: 'rt<"text-right"ip>',
        ajax: {
            url: "{{ url('scan_product_in_refund_datatables') }}",
            data: function(d) {
                d.search = $('#scan_in_refund_search').val();
                d.st_id = $('#st_id').val();
                d.waiting = $('#waiting_refund_filter').val();
            }
        },
        columns: [{
            data: 'article',
            name: 'article',
            sortable: false
        }],
        columnDefs: [{
            "targets": 0,
            "className": "text-left",
            "width": "0%"
        }],
        order: [
            [0, 'desc']
        ],
        drawCallback: function(settings) {
            var api = this.api();
            $('#scan_out_search').off('keyup').on('keyup', function(event) {
                if (event.keyCode === 13) {
                    scanInRefundTbEnterPressed = true;
                    api.search(this.value).draw();
                }
            });
        }
    });

    function initializeScanner(elementId) {
        return new Html5QrcodeScanner(elementId, {
            // Scanner will be initialized in DOM inside the element with the given id
            qrbox: {
                width: 250,
                height: 250,
            },
            fps: 30,
        });
    }

    // Example usage for multiple modals
    // let scanner_scan_out = initializeScanner('reader_scan_out');
    let scanner_scan_bin_out = initializeScanner('reader_scan_bin_out');
    let scanner_scan_in = initializeScanner('reader_scan_in');
    let scanner_scan_in_refund = initializeScanner('reader_scan_in_refund');
    let scanner_pick_online = initializeScanner('reader_pick_online');
    let scanner_take_transfer = initializeScanner('reader_take_transfer');
    let scanner_scan_default = initializeScanner('reader_default');
    //

    var scan_timer = null;

    function success(result) {
        if (scan_timer) {
            clearTimeout(scan_timer);
        }

        scan_timer = setTimeout(function() {
            var hasil = result;

            if (hasil.startsWith(']C1')) {
                hasil = hasil.replace(']C1', '');
            }

            if (modal_opened == 'ScanOutModal') {
                alert(hasil);
                $('#scan_out_search').val(hasil);
                scan_out_table.ajax.reload();

            } else if (modal_opened == 'ScanInModal') {
                alert(hasil);
                $('#scan_in_search').val(hasil);
                scan_in_table.ajax.reload();

            } else if (modal_opened == 'ScanInRefundModal') {
                alert(hasil);
                $('#scan_in_refund_search').val(hasil);
                scan_in_refund_table.ajax.reload();

            } else if (modal_opened == 'binModal') {
                // alert(hasil);
                $('#sku_search').focus().val(hasil);

                // Trigger keyup event with ENTER key using native KeyboardEvent
                var event = new KeyboardEvent('keyup', {
                    key: 'Enter',
                    keyCode: 13,
                    which: 13,
                    bubbles: true,
                    cancelable: true
                });
                document.getElementById('sku_search').dispatchEvent(event);
                // scan_in_refund_table.ajax.reload();

            } else if (modal_opened == 'PickOnModal') {
                alert(hasil);
                $('#scan_pick_on_search').val(hasil);
                scan_keep_table.ajax.reload();

            } else if (modal_opened == 'TakeTransferItemModal') {
                var bin_code = $('#take_transfer_bin_info').text();
                var sku_code = $('#take_transfer_sku_info').text();

                // Focus to BIN input first, then after BIN is filled, focus to SKU input
                // If BIN already filled, skip setting it again
                if ($('#take_transfer_bin').val().trim() === '') {
                    $('#take_transfer_bin').val(hasil).focus();
                    if (hasil !== bin_code) {
                        swal({
                            title: "Kode BIN tidak sesuai",
                            text: "Silahkan scan ulang kode BIN yang benar.",
                            icon: "warning",
                            button: "OK"
                        }).then(function() {
                            $('#take_transfer_bin').val('').focus();
                        });
                        return;
                    }
                    return;
                }

                // If BIN already filled, focus to SKU and set hasil to SKU
                if ($('#take_transfer_bin').val().trim() !== '' && $('#take_transfer_barcode').val().trim() === '') {
                    // Check if the scanned barcode matches the expected SKU
                    if (hasil !== sku_code) {
                        swal({
                            title: "Kode SKU tidak sesuai",
                            text: "Silahkan scan ulang kode SKU yang benar.",
                            icon: "warning",
                            button: "OK"
                        }).then(function() {
                            $('#take_transfer_barcode').val('').focus();
                        });
                        return;
                    }

                    setTimeout(function() {
                        $('#take_transfer_barcode').focus();
                    }, 200);

                    $('#take_transfer_barcode').val(hasil);
                }

                // If BIN and SKU already filled, optionally auto-submit
                if (
                    $('#take_transfer_bin').val().trim() !== '' &&
                    $('#take_transfer_barcode').val().trim() !== '' &&
                    $('#take_transfer_quantity').val().trim() !== ''
                ) {
                    $('#btn_submit_scan_item_transfer').click();

                    $('#take_transfer_bin').val('');
                    $('#take_transfer_barcode').val('');
                }
            }

        }, 1000); // Add a delay of 1s to prevent spamming
    }

    function error(err) {
        console.error(err);
    }
    //
    // function console_log(result) {
    //     console.log(result);
    // }

    $('#take_transfer_barcode').on('keydown', function(e) {
        if (e.key === 'Enter' || e.key === 'Tab') {
            e.preventDefault();
            $('#btn_submit_scan_item_transfer').trigger('click');
            $('#take_transfer_bin').val(''); // Clear the input after submission
            $('#take_transfer_barcode').val(''); // Clear the input after submission
            $('#take_transfer_bin').focus();

        }
    });


    $('#ScanIntb').on('draw.dt', function() {
        if (!scanInRefundTbEnterPressed) {
            return;
        }

        if ($('#scan_in_refund_search').val().trim() === '') {
            return;
        }

        scanInRefundTbEnterPressed = false;

        var rowsData = scan_in_refund_table.rows({
            page: 'current'
        }).data();
        var scan_in_data = [];

        if (rowsData.length > 0) {
            var rowData = rowsData[0];

            console.log(rowData);

            scan_in_data.push({
                _plst_id: rowData.plst_id,
                _pls_id: rowData.pls_id,
                _qty: rowData.plst_qty,
            });

            swal({
                title: rowData.pl_name + "..?",
                text: "Yakin BIN Kembali sudah benar ?",
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

                    var item = scan_in_data[0]; // Only process the first item

                    $.ajax({
                        url: "{{ url('save_in_activity') }}",
                        type: "POST",
                        data: {
                            _plst_id: item._plst_id,
                            _pls_id: item._pls_id,
                            _qty: item._qty,
                        },
                        success: function(response) {
                            var responseObject = JSON.parse(response);
                            var status = responseObject.status;
                            $('#scan_in_refund_search').val('');
                            scan_in_refund_table.ajax.reload();

                            if (status == 200) {
                                toast('Dikeluarkan', ' berhasil dimasukkan', 'success');
                            } else {
                                swal('Gagal', 'Gagal masuk produk', 'error');
                            }
                        },
                    });
                }
            })


        }
    });

    //End Refund

    // Disini
    $('#scan_in_search').on('keyup', function(event) {
        if (event.keyCode === 13 && this.value.trim() !== '') {
            scanInTbEnterPressed = true;

            scan_in_table.ajax.reload();
            console.log('testing')
        }

        // check if the input is empty cannot enter
        if (this.value.trim() === '') {
            scanInTbEnterPressed = false;
        }

        scan_in_table.ajax.reload();
    });

    // Disini
    $('#scan_in_refund_search').on('keyup', function(event) {
        if (event.keyCode === 13 && this.value.trim() !== '') {
            scanInTbEnterPressed = true;

            scan_in_table.ajax.reload();
            console.log('testing')
        }

        // check if the input is empty cannot enter
        if (this.value.trim() === '') {
            scanInTbEnterPressed = false;
        }

        scan_in_refund_table.ajax.reload();
    });

    $('#scan_out_search').on('keyup', function(event) {
        if (event.keyCode === 13 && this.value.trim() !== '') {
            scanInTbEnterPressed = true;
            scan_out_table.ajax.reload();
        }

        // check if the input is empty cannot enter
        if (this.value.trim() === '') {
            scanOutTbEnterPressed = false;
        }

        scan_out_table.ajax.reload();

        console.log($('#scan_out_search').val())
    });
    
    $('#waiting_filter').on('change', function() {
        var selectedValue = $('#waiting_filter').val(); // Get the selected value
        console.log(selectedValue); // Log the selected value to the console
        scan_in_table.ajax.reload();
    });

    $('#in_search').on('keyup', function() {
        in_table.draw();
    });

    $('#out_search').on('keyup', function() {
        out_table.draw();
    });

    var transfer_list_table = $('#TransferListtb').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        responsive: false,
        dom: '<"text-right"l>rt<"text-right"ip>',
        buttons: [{
            "extend": 'excelHtml5',
            "text": 'Excel',
            "className": 'btn btn-primary btn-xs'
        }],
        ajax: {
            url: "{{ url('stock_transfer_list_datatables') }}",
            data: function(d) {
                d.search = $('#transfer_search').val();
                d.invoice = $('#transfer_invoice_label').val();
                d.mode = $('#_transfer_mode').val();
            }
        },
        columns: [{
                data: 'DT_RowIndex',
                name: 'stfd_id',
                searchable: false
            },
            {
                data: 'article',
                name: 'article',
                orderable: false
            },
        ],
        columnDefs: [{
            "targets": 0,
            "className": "text-center",
            "width": "0%"
        }],
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "Semua"]
        ],
        language: {
            "lengthMenu": "_MENU_",
        },
        order: [
            [0, 'desc']
        ],
    })

    var scan_transfer_list_table = $('#ScanTransferListtb').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        responsive: false,
        dom: '<"text-right"l>rt<"text-right"ip>',
        buttons: [{
            "extend": 'excelHtml5',
            "text": 'Excel',
            "className": 'btn btn-primary btn-xs'
        }],
        ajax: {
            url: "{{ url('stock_transfer_list_datatables') }}",
            data: function(d) {
                d.search = $('#scan_transfer_search').val();
                d.invoice = $('#scan_transfer_invoice_label').val();
                d.mode = $('#_scan_transfer_mode').val();
            }
        },
        columns: [{
                data: 'DT_RowIndex',
                name: 'stfd_id',
                searchable: false
            },
            {
                data: 'article',
                name: 'article',
                orderable: false
            },
        ],
        columnDefs: [{
            "targets": 0,
            "className": "text-center",
            "width": "0%"
        }],
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "Semua"]
        ],
        language: {
            "lengthMenu": "_MENU_",
        },
        order: [
            [0, 'desc']
        ],
    });

    $('#transfer_search').on('keyup', function() {
        transfer_list_table.draw();
    });

    $('#scan_transfer_search').on('change', function(event) {
        scan_transfer_list_table.draw();

        // if (event.keyCode === 13) {
        //     var scan_transfer_data = [];
        //     var totalRequests = 0;
        //     var completedRequests = 0;

        //     scan_transfer_list_table.rows().every(function() {
        //         totalRequests++;
        //         var rowData = this.data();
        //         scan_transfer_data.push({
        //             _stfd_id: rowData.stfd_id,
        //         });
        //     });

        //     scan_transfer_data.forEach(function(item) {
        //         $.ajaxSetup({
        //             headers: {
        //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //             }
        //         });

        //         $.ajax({
        //             url: "{{ url('get_transfer_item') }}",
        //             type: "POST",
        //             data: {
        //                 _stfd_id: item._stfd_id,
        //             },
        //             success: function(response) {
        //                 var responseObject = JSON.parse(response);
        //                 var status = responseObject.status;

        //                 completedRequests++;
        //                 if (status == '200') {
        //                     scan_transfer_list_table.draw();
        //                     if (completedRequests === totalRequests) {
        //                         // All requests have completed
        //                         if (status == 200) {
        //                             scan_transfer_list_table.draw();
        //                             toast('Dipindahkan', ' berhasil dipindahkan',
        //                                 'success');
        //                             // swal('Dipindahkan', 'Berhasil dipindahkan produk', 'success');
        //                         } else {
        //                             // toast('Dipindahkan', ' berhasil dipindahkan', 'success');
        //                             swal('Gagal', 'Gagal dipindahkan produk', 'error');
        //                         }
        //                     }
        //                 }
        //             },
        //         });
        //     });
        // }
    });


    var take_cross_order_table = $('#TakeCrossOrdertb').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        responsive: false,
        dom: '<"text-right"l>rt<"text-right"ip>',
        buttons: [{
            "extend": 'excelHtml5',
            "text": 'Excel',
            "className": 'btn btn-primary btn-xs'
        }],
        ajax: {
            url: "{{ url('take_confirmation_datatables') }}",
            data: function(d) {
                d.pt_id = $('#cross_invoice').val();
            }
        },
        columns: [{
                data: 'DT_RowIndex',
                name: 'ptd_id',
                searchable: false
            },
            {
                data: 'article',
                name: 'article',
                orderable: false
            },
            {
                data: 'pos_td_qty',
                name: 'pos_td_qty',
                orderable: false
            },
            {
                data: 'pl_code',
                name: 'pl_code',
                orderable: false
            },
            {
                data: 'action',
                name: 'action',
                orderable: false
            },
        ],
        columnDefs: [{
            "targets": 0,
            "className": "text-center",
            "width": "0%"
        }],
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "Semua"]
        ],
        language: {
            "lengthMenu": "_MENU_",
        },
        order: [
            [0, 'desc']
        ],
    });

    // $('#stock_data_search').on('keyup', function() {
    //     stock_data_table.draw();
    // });
    //
    // $('#st_filter').on('change', function() {
    //     stock_data_table.draw();
    // });

    $('#invoice').select2({
        width: "100%",
        dropdownParent: $('#invoice_parent')
    });
    $('#invoice').on('select2:open', function(e) {
        const evt = "scroll.select2";
        $(e.target).parents().off(evt);
        $(window).off(evt);
    });

    $('#pl_id_out').on('change', function(e) {
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
                    data: {
                        _ptd_id: ptd_id,
                        _ptd_qty: ptd_qty
                    },
                    dataType: 'json',
                    url: "{{ url('get_cross_item_status') }}",
                    success: function(r) {
                        if (r.status == '200') {
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
        swal({
            title: "Keluar..?",
            text: "Yakin keluarin produk " + p_name + " dari BIN " + bin + " ?",
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
                        _plst_qty: plst_qty,
                        _plst_id: plst_id,
                        _pls_id: pls_id,
                        _secret_code: secret_code,
                        _status: status
                    },
                    dataType: 'json',
                    url: "{{ url('save_out_activity') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            out_table.draw();
                            $(this).prop('disabled', false);
                            toast('Dikeluarkan', p_name + ' berhasil dikeluarkan',
                                'success');
                        } else {
                            $(this).prop('disabled', false);
                            swal('Gagal', 'Gagal keluar produk', 'error');
                        }
                    }
                });
                return false;
            }
        })
    });

    // $(document).delegate('#scan_get_out_btn', 'click', function() {
    //     var plst_id = $(this).attr('data-plst_id');
    //     var plst_qty = $(this).attr('data-plst_qty');
    //     var current_qty = $(this).attr('data-qty');
    //     var p_name = $(this).attr('data-p_name');
    //     var sa_id = $(this).attr('data-sa_id');
    //
    //     console.log(sa_id);
    //     var bin = $('#pl_id_out option:selected').text();
    //     var secret_code = $('#u_secret_code').val();
    //     var status = $(this).attr('data-status');
    //     // if (current_qty >= 0) {
    //
    //     // }
    // });

    $('#close_scan_out_modal').on('click', function() {
        $('#sku_send').val('');
        $('#bin_out_search').val('');
        $('#binTable tbody').empty();
        $('#sku_search').remove();
        $('#bin_out_search').prop('disabled', false);
    });

    $(document).on('click', '.ambil-dari-bin', function(e) {
        // e.preventDefault();
        var pl_code = $(this).data('pl_code');
        var bin_id = $(this).data('bin_id');
        $('#bin_out_search').focus().val(pl_code).data('bin_id', bin_id);

        // Trigger keyup event with ENTER key using native KeyboardEvent
        var event = new KeyboardEvent('keyup', {
            key: 'Enter',
            keyCode: 13,
            which: 13,
            bubbles: true,
            cancelable: true
        });
        document.getElementById('bin_out_search').dispatchEvent(event);
    });

    $(document).on('click', '#pick_get_bin_products', function (e) {
        e.preventDefault();

        let plst_id = $(this).data('plst_id');
        let sa_id = $(this).data('sa_id');
        let qty = $(this).data('qty');
        let sku = $(this).data('sku');
        let p_name = $(this).data('p_name');

        console.log("SKU:", sku);

        $('#sku_selected').text(sku);

        // AJAX ambil data BIN
        $.ajax({
            url: "{{ url('get_bin_by_sa') }}",
            method: 'GET',
            data: {
                sa_id: sa_id,
                plst_id: plst_id,
                sku: sku
            },
            success: function (response) {
                // Kosongkan isi tabel
                $('#binTable tbody').empty();

                // Masukkan data BIN ke tabel
                $.each(response.data, function (index, bin) {
                    $('#binTable tbody').append(`
                        <tr>
                            <td>${bin.pl_code}</td>
                            <td>${bin.pls_qty}</td>
                            <td>
                                <button class="btn btn-primary btn-sm ambil-dari-bin"
                                        data-bin_id="${bin.pls_id}"
                                        data-pl_code="${bin.pl_code}">
                                    Ambil
                                </button>
                            </td>
                        </tr>
                    `);
                });

                // Set nama produk
                $('#product_name').text(p_name);
                $('#plst_id').text(plst_id);


                modal_opened = 'binModal';
                                // Tampilkan modal
                $('#binModal').modal('show');
                // scan_in_table.draw();
                scanner_scan_bin_out.render(success, error);

            }
        });
    });

    $('#bin_out_search').on('keyup', function (event) {
        let searchText = $(this).val().toLowerCase();
        let bin_search = $(this).val();
        let bin_id = $(this).data('bin_id');
        let matchingRows = [];

        let bin = bin_search;

        $('#binTable tbody tr').each(function () {
            let binText = $(this).find('td:first').text().toLowerCase();

            if (binText.includes(searchText)) {
                $(this).show();
                matchingRows.push(this);
            } else {
                $(this).hide();
            }
        });

        if (event.key === 'Enter') {
            if (matchingRows.length === 1) {
                // Disable BIN input dan trigger klik
                $('#bin_out_search').prop('disabled', true);
                $(matchingRows[0]).find('.ambil-dari-bin').trigger('click');

                let selectedRow = $(matchingRows[0]);
                let validSku =  document.getElementById('sku_selected').textContent;
                let plst_id =  document.getElementById('plst_id').textContent;
                let product_name = document.getElementById('product_name').textContent;

                let bin_name = $('#bin_out_search').val();

                // Tambahkan input SKU
                if ($('#sku_search').length === 0) {
                    $('#bin_out_search').after(`
                    <input type="text" id="sku_search" class="form-control mt-2" placeholder="Scan / Ketik SKU...">
                `);
                }

                // var sku_send = $('#sku_send').val(validSku)

                $('#sku_search').focus().on('keyup', function (e) {
                    if (e.key === 'Enter') {
                        let enteredSku = $(this).val();

                        if (enteredSku === validSku) {
                            swal({
                                title: "Keluar..?",
                                text: "Yakin keluarin produk " + product_name + " dari BIN " + bin_name + " ?",
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
                                        data: {
                                            // cari ini
                                            _sku: enteredSku,
                                            _bin: bin,
                                            _bin_id: bin_id,
                                            _plst_qty: 1,
                                            _plst_id: plst_id,
                                            _status: status
                                        },
                                        dataType: 'json',
                                        url: "{{ url('save_out_activity_bin_selected') }}",
                                        success: function(r) {
                                            if (r.status == '200') {
                                                out_table.draw();
                                                $('#binModal').modal('hide');

                                                $('#sku_send').val('');
                                                $('#bin_out_search').val('');
                                                $('#binTable tbody').empty();
                                                $('#sku_search').remove();
                                                $('#bin_out_search').prop('disabled', false);

                                                swal({
                                                    title: 'Berhasil',
                                                    text: ' berhasil dikeluarkan',
                                                    icon: 'success',
                                                    button: 'OK',
                                                });
                                            } else {
                                                swal('Gagal', 'Gagal keluar produk', 'error');
                                            }
                                        }
                                    });
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'SKU tidak ditemukan',
                                text: 'SKU tidak cocok dengan BIN yang dipilih!',
                            });
                        }
                    }
                });

            } else if (matchingRows.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'BIN tidak ditemukan',
                    text: 'Pastikan kode BIN yang kamu masukkan benar!',
                });
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Terlalu banyak hasil',
                    text: 'Lebih dari satu BIN cocok. Harap perjelas pencarian.',
                });
            }
        }
    });





    $(document).ready(function () {
        startChatPolling();
        scanner_scan_default.render(success, error);
        $('#binModal').on('shown.bs.modal', function () {

            if ($('#bin_out_search').val().trim() === '') {
                return;
            }
        });
    });


    // Hapus blur saat modal ditutup
    $('#binModal').on('hidden.bs.modal', function () {
        $('.modal-backdrop').removeClass('blur');
    });

    $('#binModal').on('hidden.bs.modal', function () {
        $('.modal-backdrop').removeClass('blur');
    });

    $(document).delegate('#pick_get_bin_productss', 'click', function() {
        console.log('running ga nih?');
        // var pls_id = $(this).attr('data-pls_id');
        var plst_id = $(this).attr('data-plst_id');
        var plst_qty = $(this).attr('data-plst_qty');
        var current_qty = $(this).attr('data-qty');
        var p_name = $(this).attr('data-p_name');
        var sa_id = $(this).attr('data-sa_id');

        console.log(sa_id);
        var bin = $('#pl_id_out option:selected').text();
        var secret_code = $('#u_secret_code').val();
        var status = $(this).attr('data-status');
        // if (current_qty >= 0) {
        swal({
            title: "Keluar..?",
            text: "Yakin keluarin produk " + p_name + " dari BIN " + sa_id + " ?",
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
                        _plst_qty: plst_qty,
                        _plst_id: plst_id,
                        _pls_id: pls_id,
                        _secret_code: secret_code,
                        _status: status
                    },
                    dataType: 'json',
                    url: "{{ url('save_out_activity') }}",
                    success: function(r) {
                        if (r.status == '200') {
                            scan_out_table.draw();
                            $(this).prop('disabled', false);
                            toast('Dikeluarkan', p_name + ' berhasil dikeluarkan',
                                'success');
                        } else {
                            $(this).prop('disabled', false);
                            swal('Gagal', 'Gagal keluar produk', 'error');
                        }
                    }
                });
                return false;
            }
        })
        // }
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
                text: "Yakin sudah masukin produk " + p_name + " ke BIN " + bin + " ?",
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
                            _qty: current_qty,
                            _plst_qty: plst_qty,
                            _pls_id: pls_id,
                            _plst_id: plst_id,
                            _secret_code: secret_code
                        },
                        dataType: 'json',
                        url: "{{ url('save_in_activity') }}",
                        success: function(r) {
                            if (r.status == '200') {
                                in_table.draw();
                                $(this).prop('disabled', false);
                                toast('Dimasukkan', p_name + ' berhasil dimasukkan',
                                    'success');
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
                text: "Yakin sudah masukin produk " + p_name + " ke BIN " + bin + " ?",
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
                            _qty: current_qty,
                            _plst_qty: plst_qty,
                            _pls_id: pls_id,
                            _plst_id: plst_id,
                            _secret_code: secret_code
                        },
                        dataType: 'json',
                        url: "{{ url('save_in_activity') }}",
                        success: function(r) {
                            if (r.status == '200') {
                                scan_in_table.draw();
                                scan_keep_table.draw();
                                $(this).prop('disabled', false);
                                toast('Dimasukkan', p_name + ' berhasil dimasukkan',
                                    'success');
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


    $(document).delegate('#scan_get_in_refund_btn', 'click', function() {
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
                text: "Yakin sudah masukin produk " + p_name + " ke BIN " + bin + " ?",
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
                            _qty: current_qty,
                            _plst_qty: plst_qty,
                            _pls_id: pls_id,
                            _plst_id: plst_id,
                            _secret_code: secret_code
                        },
                        dataType: 'json',
                        url: "{{ url('save_in_refund_activity') }}",
                        success: function(r) {
                            if (r.status == '200') {
                                scan_in_refund_table.draw();
                                scan_keep_table.draw();
                                $(this).prop('disabled', false);
                                toast('Dimasukkan', p_name + ' berhasil dimasukkan',
                                    'success');
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
        jQuery.noConflict();
        scanner_scan_default.clear();

        var stfd_id = $(this).attr('data-stfd_id');
        var p_name = $(this).attr('data-p_name');
        var bin = $(this).attr('data-bin');
        var qty = $(this).attr('data-stfd_qty');
        var barcode = $(this).attr('data-ps-barcode');

        $('#take_transfer_bin_info').text(bin);
        $('#take_transfer_sku_info').text(barcode);
        $('#take_transfer_qty_info').text(qty);
        $('#take_transfer_p_name_info').text(p_name);

        modal_opened = 'TakeTransferItemModal';
        $('#TakeTransferItemModal').modal('show');
        $('#_stfd_id').val(stfd_id);
        scanner_take_transfer.render(success, error);
        localStorage.removeItem('take_transfer_item_form_data');
        loadTakeTransferItemFormData();
    });

    $(document).on('click', '#btn_take_transfer_item', function(e) {
        e.preventDefault();

        var cacheKey = 'take_transfer_item_form_data';
        var data = localStorage.getItem(cacheKey);

        if (!data) {
            swal('Kosong', 'Tidak ada data yang akan dikirim', 'warning');
            return;
        }

        var arr;
        try {
            arr = JSON.parse(data);
            if (!Array.isArray(arr) || arr.length === 0) {
                swal('Kosong', 'Tidak ada data yang akan dikirim', 'warning');
                return;
            }
        } catch (e) {
            swal('Error', 'Data cache rusak', 'error');
            return;
        }

        swal({
            title: "Ambil Barang?",
            text: "Yakin ingin mengambil item transfer ini?",
            icon: "warning",
            buttons: ['Batal', 'Yakin'],
            dangerMode: false,
        }).then(function(isConfirm) {
            if (isConfirm) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ url('get_transfer_item') }}",
                    type: "POST",
                    data: {
                        stfd_id: $('#_stfd_id').val(),
                        bin: $('#take_transfer_bin_info').text(),
                        sku: $('#take_transfer_sku_info').text(),
                        qty: $('#take_transfer_qty_info').text(),
                    },
                    dataType: 'json',
                    success: function(r) {
                        if (r.status == '200') {
                            swal('Berhasil', 'Barang berhasil diambil', 'success');
                            transfer_list_table.draw();
                            scan_transfer_list_table.draw();
                            $('#TakeTransferItemModal').modal('hide');
                            localStorage.removeItem(cacheKey);
                        } else {
                            swal('Gagal', 'Gagal mengambil barang', 'error');
                        }
                    },
                    error: function() {
                        swal('Error', 'Terjadi kesalahan server', 'error');
                    }
                });
            }
        });
    });

    $(document).on('click', '#btn_submit_scan_item_transfer', function(e) {
        e.preventDefault();
        // Get values from the form fields
        var bin=$.trim($('#take_transfer_bin').val());
        var sku=$.trim($('#take_transfer_barcode').val());
        var qty = parseInt($('#take_transfer_quantity').val(), 10);

        // Get max qty from the modal info
        var maxQty = parseInt($('#take_transfer_qty_info').text(), 10);
        var binInfo = $('#take_transfer_bin_info').text();
        var skuInfo = $('#take_transfer_sku_info').text();

        if (bin !== binInfo) {
            swal('BIN tidak sesuai', 'BIN yang diinput tidak sesuai', 'warning');
            return;
        }
        if (sku !== skuInfo) {
            swal('SKU tidak sesuai', 'SKU yang diinput tidak sesuai', 'warning');
            return;
        }

        // Check how much data is already in cache and sum qty
        var cacheKey = 'take_transfer_item_form_data';
        var dataArr = [];
        var existing = localStorage.getItem(cacheKey);
        var totalQty = 0;
        if (existing) {
            try {
                dataArr = JSON.parse(existing);
                if (!Array.isArray(dataArr)) dataArr = [];
                totalQty = dataArr.reduce(function(sum, item) {
                    return sum + (parseInt(item.qty, 10) || 0);
                }, 0);
            } catch (e) {
                dataArr = [];
                totalQty = 0;
            }
        }

        if (isNaN(qty) || qty <= 0) {
            swal('Qty tidak valid', 'Qty harus lebih dari 0', 'warning');
            return;
        }
        if (isNaN(maxQty) || (totalQty + qty) > maxQty) {
            swal('Qty melebihi batas', 'Total qty tidak boleh lebih dari ' + maxQty, 'warning');
            return;
        }

        dataArr.push({ bin: bin, sku: sku, qty: qty });
        localStorage.setItem(cacheKey, JSON.stringify(dataArr));
        toast('Tersimpan', 'Data berhasil disimpan ke cache', 'success');
        loadTakeTransferItemFormData();
    });

    // Update loadTakeTransferItemFormData to handle array
    function loadTakeTransferItemFormData() {
        $('#btn_take_transfer_item').prop('disabled', true);
        var cacheKey = 'take_transfer_item_form_data';
        var data = localStorage.getItem(cacheKey);
        var tbody = $('#TakeTransferItemTable tbody');
        var maxQty = parseInt($('#take_transfer_qty_info').text(), 10);

        tbody.empty();
        if (data) {
            try {
                var arr = JSON.parse(data);
                if (!Array.isArray(arr)) arr = [];
                var totalQty = arr.reduce((sum, item) => sum + (parseInt(item.qty, 10) || 0), 0);
                $('#btn_take_transfer_item').prop('disabled', totalQty !== maxQty);
                arr.forEach((obj, idx) => {
                    tbody.append(`<tr>
                        <td>${obj.bin || ''}</td>
                        <td>${obj.sku || ''}</td>
                        <td>${obj.qty || ''}</td>
                        <td><button class="btn btn-sm btn-danger delete-take-transfer-item" data-idx="${idx}">Delete</button></td>
                    </tr>`);
                });
            } catch (e) {
                console.error('Failed to parse cached form data', e);
            }
        }
    }

    // Delete action for TakeTransferItemTable (delete only selected row)
    $(document).on('click', '.delete-take-transfer-item', function() {
        var cacheKey = 'take_transfer_item_form_data';
        var idx = $(this).data('idx');
        var data = localStorage.getItem(cacheKey);
        if (data) {
            try {
                var arr = JSON.parse(data);
                if (Array.isArray(arr)) {
                    arr.splice(idx, 1);
                    localStorage.setItem(cacheKey, JSON.stringify(arr));
                }
            } catch (e) {}
        }
        loadTakeTransferItemFormData();
        toast('Dihapus', 'Data berhasil dihapus dari cache', 'success');
    });

    // Clear take_transfer_item_form_data when modal closed
    // Make sure this binding is outside of any other event or function and only bound once
    $(document).ready(function() {
        loadStorageAreas();
        $('#TakeTransferItemModal').off('hide.bs.modal').on('hide.bs.modal', function() {
            modal_opened = '';
            $('#take_transfer_bin').val('');
            $('#take_transfer_barcode').val('');
            scanner_take_transfer.clear();
            localStorage.removeItem('take_transfer_item_form_data');
        });
    });

    $('#out_modal_finish, #in_modal_finish, #transfer_modal_finish, #transfer_detail_modal_finish, #scan_out_modal_finish, #scan_in_modal_finish, #scan_transfer_modal_finish, #scan_in_refund_modal_finish')
        .on('click', function(e) {
            $('#OutModal').modal('hide');
            $('#ScanOutModal').modal('hide');
            $('#KeepOnModal').modal('hide');
            $('#InModal').modal('hide');
            $('#ScanInModal').modal('hide');
            $('#TrackingTypeModal').modal('hide');
            $('#ScanInRefundModal').modal('hide');
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
            text: "Yakin ambil produk " + p_name + " dari BIN " + bin + "",
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
                    data: {
                        _pos_td_qty: pos_td_qty,
                        _ptd_id: ptd_id,
                        _pst_id: pst_id,
                        _pl_id: pl_id,
                        _invoice: invoice,
                        _secret_code: secret_code
                    },
                    dataType: 'json',
                    url: "{{ url('save_tracking_activity') }}",
                    success: function(r) {
                        if (r.status == '200') {
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
            text: "Yakin batalkan produk " + p_name + " dari BIN " + bin + " ?",
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
                    data: {
                        _pos_td_qty: pos_td_qty,
                        _ptd_id: ptd_id,
                        _pst_id: pst_id,
                        _pl_id: pl_id,
                        _invoice: invoice,
                        _secret_code: secret_code
                    },
                    dataType: 'json',
                    url: "{{ url('cancel_tracking_activity') }}",
                    success: function(r) {
                        if (r.status == '200') {
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
            text: "Yakin sudah packing produk " + p_name + " ?",
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
                    data: {
                        _plst_id: plst_id,
                        _secret_code: secret_code
                    },
                    dataType: 'json',
                    url: "{{ url('save_packing_activity') }}",
                    success: function(r) {
                        if (r.status == '200') {
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
            text: "Yakin reject produk " + p_name + " ?",
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
                    data: {
                        _plst_id: plst_id,
                        _secret_code: secret_code
                    },
                    dataType: 'json',
                    url: "{{ url('save_reject_activity') }}",
                    success: function(r) {
                        if (r.status == '200') {
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

        if ($.trim(email) == '') {
            swal("Email", "Silahkan input email", "warning");
            return false;
        } else if (!emailReg.test(email)) {
            swal("Email", "Silahkan input email sesuai format", "warning");
            return false;
        } else if ($.trim(password) == '') {
            swal("Password", "Silahkan input password", "warning");
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
                    if (r.status == '200') {
                        swal('Berhasil', 'Login berhasil', 'success');
                        setTimeout(() => {
                            window.location.href = "dashboard";
                        }, 600);
                    } else if (r.status == '400') {
                        swal('Gagal', 'Email atau password salah', 'error');
                    } else {
                        swal('Nonaktif', 'Status akun anda tidak aktif / dihapus', 'error');
                    }
                }
            });
            return false;
        }
    });

    // $('#article_btn').on('click', function(e) {
    //     e.preventDefault();
    //     $('#ArticleModal').on('show.bs.modal', function() {
    //         stock_data_table.draw();
    //     }).modal('show');
    // });

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
        scanner_scan_bin_out.clear();
        e.preventDefault();
        modal_opened = 'ScanOutModal';
        $('#st_id').val('');
        $('#ScanOutModal').modal('show');
        out_table.draw();
        // scanner_scan_out.render(success, error);
    });

    $('#ScanOutModal').on('hide.bs.modal', function() {
        // scanner_scan_out.clear();
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
        scanner_scan_bin_out.clear();
        e.preventDefault();
        modal_opened = 'ScanInModal';
        $('#st_id').val('');
        $('#ScanInModal').modal('show');
        scan_in_table.draw();
        scanner_scan_in.render(success, error);
    });

    $('#ScanInModal').on('hide.bs.modal', function() {
        scanner_scan_in.clear();
    });

    $('#scan_in_refund_btn').on('click', function(e) {
        scanner_scan_default.clear();
        e.preventDefault();
        modal_opened = 'ScanInRefundModal';
        $('#st_id').val('');
        $('#ScanInRefundModal').modal('show');
        scan_in_table.draw();
        scanner_scan_in_refund.render(success, error);
    });

    $('#ScanInRefundModal').on('hide.bs.modal', function() {
        scanner_scan_in_refund.clear();
    });

    $('#binModal').on('hide.bs.modal', function() {
        scanner_scan_bin_out.clear();
    });

    $('#scan_keep_btn').on('click', function(e) {
        e.preventDefault();
        $('#st_id').val('');
        $('#KeepOnModal').modal('show');
        scan_keep_table.draw();
    });

    $('#take_online_btn').on('click', function(e) {
        scanner_scan_default.clear();
        e.preventDefault();
        modal_opened = 'PickOnModal';
        $('#st_id').val('');
        $('#PickOnModal').modal('show');
        scan_keep_table.draw();
        scanner_pick_online.render(success, error);
    });

    $('#PickOnModal').on('hide.bs.modal', function() {
        scanner_pick_online.clear();
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
            url: "{{ url('reload_cross_order_invoice') }}",
            method: "POST",
            success: function(data) {
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

    $('#article_name').keyup(function() {
        var query = $(this).val();
        if ($.trim(query) != '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ url('autocomplete_fetch') }}",
                method: "POST",
                data: {
                    query: query
                },
                success: function(data) {
                    $('#articleList').fadeIn();
                    $('#articleList').html(data);
                }
            });
        } else {
            $('#articleList').fadeOut();
        }
    });

    $(document).delegate('#show_item', 'click', function() {
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
            data: {
                _pid: pid,
                _p_name: p_name,
                _br_name: br_name
            },
            dataType: 'html',
            url: "{{ url('check_article') }}",
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
                data: {
                    _qr: qr
                },
                dataType: 'html',
                url: "{{ url('order_list_by_invoice') }}",
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
                data: {
                    _qr: qr
                },
                dataType: 'html',
                url: "{{ url('packing_list_by_invoice') }}",
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

    $(document).on('change', '#transfer_invoice', function(e) {
        e.preventDefault();
        var invoice = $('#transfer_invoice option:selected').text();
        $('#transfer_invoice_modal_label').text(invoice);
        $('#transfer_invoice_label').val(invoice);
        $('#TransferDetailModal').on('show.bs.modal', function() {
            transfer_list_table.draw();
        }).modal('show');
    });

    $(document).on('change', '#scan_transfer_invoice', function(e) {
        e.preventDefault();
        var invoice = $('#scan_transfer_invoice option:selected').text();
        $('#scan_transfer_invoice_modal_label').text(invoice);
        $('#scan_transfer_invoice_label').val(invoice);
        $('#ScanTransferDetailModal').on('show.bs.modal', function() {
            scan_transfer_list_table.draw();
        }).modal('show');
    });

    $(document).on('click', '#change_transfer_quantity', function() {
        var stfd_id = $(this).data('stfd_id');
        var current_qty = $(this).data('stfd_qty');
        var current_stock = $(this).data('current_stock');
        var p_name = $(this).data('p_name');

        // Remove any existing modal
        $('#changeQtyModal').remove();

        // Append modal HTML to body
        $('body').append(`
            <div class="modal fade" id="changeQtyModal" tabindex="-1" role="dialog" aria-labelledby="changeQtyModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
            <form id="changeQtyForm">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="changeQtyModalLabel">Ubah Qty untuk ${p_name}</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                <div class="form-group row align-items-center mb-3">
                    <label for="current_qty" class="col-sm-2 col-form-label font-weight-bold">Stok</label>
                    <div class="col-sm-3">
                        <input type="number" class="form-control-plaintext text-center bg-light border rounded" id="current_qty" value="${current_stock}" disabled>
                    </div>
                    <label for="new_qty" class="col-sm-2 col-form-label font-weight-bold">Qty Baru</label>
                    <div class="col-sm-3">
                        <input type="number" class="form-control text-center border-primary" id="new_qty" name="new_qty" min="1" value="${current_qty}" required>
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                  <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
              </div>
            </form>
              </div>
            </div>
        `);

        // Show modal
        $('#changeQtyModal').modal('show');

        // Handle form submit
        $('#changeQtyForm').on('submit', function(e) {
            e.preventDefault();
            var qty = parseInt($('#new_qty').val(), 10);
            if (qty && !isNaN(qty) && qty > 0) {
                swal({
                    title: "Konfirmasi",
                    text: "Yakin ingin mengubah qty menjadi " + qty + "?",
                    icon: "info",
                    buttons: [
                        'Batal',
                        'Yakin'
                    ],
                }).then(function(confirmChange) {
                    if (confirmChange) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            url: "{{ url('change_transfer_qty') }}",
                            type: "POST",
                            data: {
                                id: stfd_id,
                                qty: qty
                            },
                            dataType: 'json',
                            success: function(r) {
                                if (r.status == '200') {
                                    toast('Berhasil', 'Qty berhasil diubah', 'success');
                                    transfer_list_table.draw();
                                    scan_transfer_list_table.draw();
                                    $('#changeQtyModal').modal('hide');
                                } else {
                                    swal('Gagal', r.message, 'error');
                                }
                            }
                        });
                    }
                });
            }
        });

        // Remove modal from DOM after it's hidden
        $('#changeQtyModal').on('hidden.bs.modal', function () {
            transfer_list_table.draw();
            scan_transfer_list_table.draw();
            $(this).remove();
        });
    });


</script>