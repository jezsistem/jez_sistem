<script>
    var current_tab = '';
    let chat_status = 'closed';
    let ot_id = null;
    var detail_table = '';
    var transactionId = '';
    var modal_opened = null;

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

    function getListPicked() {
        $.ajax({
            type: "GET",
            dataType: 'json', // Changed to json
            url: "{{ url('helper_online_get_picked_item') }}",
            data: {
                st_id: $('#st_id').val(),
                status_filter: $('#status_filter').val(),
                order_number: $('#order_number').val()
            },
            success: function(r) {
                $("#picked_online_trx").html(renderTransactions(r)); // Removed animation
            }
        });
    }

    function renderTransactions(transactions) {
        let html = '';
        transactions.forEach(transaction => {
            const statusClasses = {
                'WAITING ONLINE': 'warning',
                'UNDER REVIEW': 'secondary',
                'WAITING RECEIPT': 'primary',
                'WAITING PACKING': 'danger'
            };

            const statusClass = statusClasses[transaction.internal_order_status] || 'default';
            html += `
                <div class="col-md-4 mb-4 text-left" id="${transaction.internal_order_status === 'WAITING RECEIPT' ? 'waiting_receipt_card' : 'transaction_card'}" data-transaction_id=${transaction.transaction_id} data-order_number=${transaction.order_number} style="cursor: pointer;">
                    <div class="card shadow-sm" style="border-radius: 10px; overflow: hidden; border: 2px solid ${getBorderColor(transaction.internal_order_status)};">
                        <div class="card-body" style="background-color: #f8f9fa;">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title text-primary" style="font-weight: bold;">Order Number: ${transaction.order_number}</h5>

                                <button class="btn position-relative" id="open_chat" onclick="openChat(${transaction.transaction_id})" data-trx_number=${transaction.order_number}>
                                    <i class="fas fa-comments"></i>
                                    ${transaction.unreaded_chat > 0 ? `
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger text-white">
                                        ${transaction.unreaded_chat}
                                    </span>
                                    ` : ''}
                                </button>
                            </div>
                            <p class="card-text">Resi: <strong>${transaction.no_resi}</strong></p>
                            <p class="card-text">Platform: <strong>${transaction.platform}</strong></p>
                            <p class="card-text">Toko: <strong>${transaction.store}</strong></p>
                            <p class="card-text">SKU: <code>${transaction.sku}</code></p>
                            <p class="card-text">Tanggal TRX: <em>${new Date(transaction.created_at).toLocaleDateString()}</em></p>
                            <p class="card-text">Waktu Pick: <em>${new Date(transaction.picked_time).toLocaleString()}</em></p>
                            <p class="card-text">Status TRX: 
                                <span class="badge badge-${statusClass}">${transaction.internal_order_status}</span>
                            </p>
                        </div>
                    </div>
                </div>
            `;
        });

        function getBorderColor(status) {
            switch (status) {
                case 'WAITING ONLINE':
                    return '#ffc107';
                case 'UNDER REVIEW':
                    return '#6c757d';
                case 'WAITING RECEIPT':
                    return '#007bff';
                case 'WAITING PACKING':
                    return '#f00c0c';
                default:
                    return '#000'; // Default color
            }
        }
        return html;
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
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        getListPicked();
        setInterval(() => {
            getListPicked();
        }, 3000); // Changed interval to 3 seconds

        startChatPolling();



        var helper_online_table = $('#helper_online_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('/helper_online_datatables') }}",
                data: function(d) {
                    d.st_id = $('#st_id').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'order_number',
                    name: 'order_number'
                },
                {
                    data: 'platform',
                    name: 'platform',
                },
                {
                    data: 'store',
                    name: 'store',
                },
                {
                    data: 'sku',
                    name: 'sku',
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'internal_order_status',
                    name: 'internal_order_status'
                }
            ]
        });

        var online_items_table = $('#online_items_table').DataTable({
            destroy: true,
            processing: false,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            deferLoading: 0,
            ajax: {
                url: "{{ url('/helper_online_get_online_items') }}",
                data: function(d) {
                    d.ot_id = transactionId;
                }
            },
            columns: [{
                data: 'item',
                name: 'item',
            }],
            columnDefs: [{
                "targets": 0,
                "className": "text-left",
                "width": "0%"
            }],
            order: [
                [0, 'desc']
            ],
        });

        var waiting_receipt_table = $('#waitingReceiptTable').DataTable({
            destroy: true,
            processing: false,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            deferLoading: 0,
            ajax: {
                url: "{{ url('/helper_online_get_waiting_receipt_items') }}",
                data: function(d) {
                    d.ot_id = transactionId;
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'article',
                    name: 'article',
                },
                {
                    data: 'sku',
                    name: 'sku',
                },
                {
                    data: 'qty',
                    name: 'qty',
                },
                {
                    data: 'platform_price',
                    name: 'platform_price',
                    render: function(data, type, row) {
                        return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                    }
                },
                {
                    data: 'jez_price',
                    name: 'jez_price',
                    render: function(data, type, row) {
                        return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                    }
                },
                {
                    data: 'seller_discount',
                    name: 'seller_discount',
                    render: function(data, type, row) {
                        return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                    }
                },
                {
                    data: 'final_price',
                    name: 'final_price',
                    render: function(data, type, row) {
                        return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                    }
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

        $('#close_scan_out_modal').on('click', function() {
            $('#sku_send').val('');
            $('#bin_out_search').val('');
            $('#binTable tbody').empty();
            $('#sku_search').remove();
            $('#bin_out_search').prop('disabled', false);
        });

        $(document).on('click', '#open_chat', function(e) {
            e.stopPropagation();
        });

        $(document).on('click', '#transaction_card', function(e) {
            transactionId = $(this).data('transaction_id');
            orderNumber = $(this).data('order_number');
            scanner_scan_bin_out.clear();
            e.preventDefault();
            modal_opened = 'ScanOutModal';
            jQuery.noConflict();
            $('#OnlineItemsModalLabel').text('Order Number: ' + orderNumber);
            $('#OnlineItemsModal').modal('show');
            online_items_table.draw();
        });

        $(document).on('click', '#waiting_receipt_card', function(e) {
            transactionId = $(this).data('transaction_id');
            orderNumber = $(this).data('order_number');
            jQuery.noConflict();
            $('#trx_number_title_wr').text('Order Number: ' + orderNumber);
            $('#waitingReceiptModal').modal('show');
            waiting_receipt_table.draw();
        });

        $(document).on('click', '#cancel_pick', function(e) {
            e.preventDefault();
            var plst_id = $(this).data('plst_id');

            $.ajax({
                url: "{{ url('helper_online_cancel_pick') }}/" + plst_id,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === '200') {
                        toastr.success('Pick berhasil dibatalkan');
                        online_items_table.draw();
                    } else {
                        toastr.error('Gagal membatalkan pick');
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('Terjadi kesalahan saat membatalkan pick');
                    console.error('Error:', error);
                }
            });
        });

        $(document).on('click', '.ambil-dari-bin', function(e) {
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

        $(document).on('click', '#pick_get_bin_products', function(e) {
            e.preventDefault();

            let plst_id = $(this).data('plst_id');
            let qty = $(this).data('qty');
            let sku = $(this).data('sku');
            let p_name = $(this).data('p_name');

            console.log("SKU:", sku);

            $('#sku_selected').text(sku);

            // AJAX ambil data BIN
            $.ajax({
                url: "{{ url('helper_online_get_bin') }}",
                method: 'GET',
                data: {
                    warehouse_id: $(this).data('warehouse_st_id'),
                    pst_id: $(this).data('pst_id'),
                },
                success: function(response) {
                    // Kosongkan isi tabel
                    $('#binTable tbody').empty();

                    // Masukkan data BIN ke tabel
                    $.each(response.data, function(index, bin) {
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

        $(document).on('click', '#submit_qc', function(e) {
            e.preventDefault();

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success",
                    denyButton: "btn btn-danger",
                    cancelButton: "btn btn-secondary"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "QC Confirmation",
                text: "Apakah lolos QC?",
                icon: "warning",
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: "Ya",
                denyButtonText: "Tidak",
                cancelButtonText: "Batal",
                customClass: {
                    denyButton: "bg-danger text-white border py-2 px-4 rounded mr-2 mt-15 fs-5",
                    confirmButton: "bg-success text-white border border-success py-2 px-4 rounded mt-15 fs-5",
                    cancelButton: "bg-secondary border py-2 px-4 rounded mr-32 mt-15 fs-5"
                },
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Handle "Ya" (passed QC)
                    $.ajax({
                        url: "{{ url('helper_online_quality_check_item') }}",
                        type: 'POST',
                        data: {
                            plst_id: $(this).data('plst_id'),
                            to_id: $(this).data('to_id'),
                            qc_status: 'passed',
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.status === '200') {
                                swalWithBootstrapButtons.fire({
                                    title: "Success",
                                    text: "Produk lolos QC",
                                    icon: "success"
                                });
                                online_items_table.draw();
                            } else {
                                swalWithBootstrapButtons.fire({
                                    title: "Error",
                                    text: "Gagal menyimpan hasil QC",
                                    icon: "error"
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            swalWithBootstrapButtons.fire({
                                title: "Error",
                                text: "Terjadi kesalahan saa    t menyimpan hasil QC",
                                icon: "error"
                            });
                        }
                    });
                } else if (result.isDenied) {
                    // Handle "Tidak" (failed QC)
                    $.ajax({
                        url: "{{ url('helper_online_quality_check_item') }}",
                        type: 'POST',
                        data: {
                            plst_id: $(this).data('plst_id'),
                            qc_status: 'failed',
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.status === '200') {
                                swalWithBootstrapButtons.fire({
                                    title: "Info",
                                    text: "Produk tidak lolos QC",
                                    icon: "info"
                                });
                                online_items_table.draw();
                            } else {
                                swalWithBootstrapButtons.fire({
                                    title: "Error",
                                    text: "Gagal menyimpan hasil QC",
                                    icon: "error"
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            swalWithBootstrapButtons.fire({
                                title: "Error",
                                text: "Terjadi kesalahan saat menyimpan hasil QC",
                                icon: "error"
                            });
                        }
                    });
                }
                // Cancel button will automatically close the dialog without further action
            });
        });

        $('#bin_out_search').on('keyup', function(event) {
            let searchText = $(this).val().toLowerCase();
            let bin_search = $(this).val();
            let bin_id = $(this).data('bin_id');
            let matchingRows = [];

            let bin = bin_search;

            $('#binTable tbody tr').each(function() {
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
                    let validSku = document.getElementById('sku_selected').textContent;
                    let plst_id = document.getElementById('plst_id').textContent;
                    let product_name = document.getElementById('product_name').textContent;

                    let bin_name = $('#bin_out_search').val();

                    // Tambahkan input SKU
                    if ($('#sku_search').length === 0) {
                        $('#bin_out_search').after(`
                    <input type="text" id="sku_search" class="form-control mt-2" placeholder="Scan / Ketik SKU...">
                `);
                    }

                    // var sku_send = $('#sku_send').val(validSku)

                    $('#sku_search').focus().on('keyup', function(e) {
                        if (e.key === 'Enter') {
                            let enteredSku = $(this).val();

                            if (enteredSku === validSku) {
                                swal({
                                    title: "Keluar..?",
                                    text: "Yakin keluarin produk " + product_name +
                                        " dari BIN " + bin_name + " ?",
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
                                                'X-CSRF-TOKEN': $(
                                                    'meta[name="csrf-token"]'
                                                ).attr('content')
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
                                            url: "{{ url('helper_online_pick_item') }}",
                                            success: function(r) {
                                                if (r.status == '200') {
                                                    online_items_table
                                                        .draw();
                                                    $('#binModal').modal(
                                                        'hide');

                                                    $('#sku_send').val('');
                                                    $('#bin_out_search')
                                                        .val('');
                                                    $('#binTable tbody')
                                                        .empty();
                                                    $('#sku_search')
                                                        .remove();
                                                    $('#bin_out_search')
                                                        .prop('disabled',
                                                            false);

                                                    swal({
                                                        title: 'Berhasil',
                                                        text: ' berhasil dikeluarkan',
                                                        icon: 'success',
                                                        button: 'OK',
                                                    });
                                                } else {
                                                    swal('Gagal',
                                                        'Gagal keluar produk',
                                                        'error');
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

                if (modal_opened == 'binModal') {
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

                } else {
                    alert('Scanner aktif di modal: ' + modal_opened);
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



    });
</script>
