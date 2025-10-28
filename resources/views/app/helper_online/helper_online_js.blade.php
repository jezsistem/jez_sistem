<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
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
                $("#picked_online_trx").html(renderTransactions(r.transactions)); // Removed animation
                setTotalStatuses(
                    r.total_done_online,
                    r.total_waiting_packing,
                    r.total_waiting_receipt,
                    r.total_under_review,
                    r.total_waiting_online
                );
            }
        });
    }

    function setTotalStatuses(done_online, waiting_packing, waiting_receipt, under_review, waiting_online) {
        $('#done_online_count').text(done_online);
        $('#waiting_packing_count').text(waiting_packing);
        $('#waiting_receipt_count').text(waiting_receipt);
        $('#under_review_count').text(under_review);
        $('#waiting_online_count').text(waiting_online);
    }

    function renderTransactions(transactions) {
        
        let html = '';
        Object.values(transactions).forEach(transaction => {
            const statusClasses = {
                'WAITING ONLINE': 'warning',
                'UNDER REVIEW': 'secondary',
                'WAITING RECEIPT': 'primary',
                'WAITING PACKING': 'danger',
                'DONE ONLINE': 'dark',
                'DONE': 'dark',
            };

            const statusClass = statusClasses[transaction.internal_order_status] || 'default';
            html += `
                <div class="col-md-4 mb-4 text-left" id="${transaction.internal_order_status === 'WAITING RECEIPT' || transaction.internal_order_status === 'WAITING PACKING' || transaction.internal_order_status === 'DONE ONLINE'|| transaction.internal_order_status === 'DONE'? 'waiting_receipt_card' : 'transaction_card'}" data-transaction_id=${transaction.transaction_id} data-order_number="${transaction.order_number}" data-resi_number="${transaction.no_resi}" data-internal_order_status="${transaction.internal_order_status}"" data-print_status=${transaction.online_print} style="cursor: pointer;">
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
                case 'DONE ONLINE':
                    return '#5CE65C';
                case 'DONE':
                    return '#5CE65C';
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

    function initializeScanner(elementId) {
        return new Html5QrcodeScanner(elementId, {
            // Scanner will be initialized in DOM inside the element with the given id
            qrbox: {
                width: 200,
                height: 200,
            },
            fps: 30,
        });
    }

    // Example usage for multiple modals
    // let scanner_scan_out = initializeScanner('reader_scan_out');
    let scanner_scan_bin_out = initializeScanner('reader_scan_bin_out');
    let scanner_scan_resi = initializeScanner('reader_scan_resi');
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

            } else if (modal_opened == 'ScanPackingModal') {
                $('#scan_packing_result').focus();
                $('#scan_packing_result').val(hasil);
                // Trigger keyup event with ENTER key using native KeyboardEvent
                var event = new KeyboardEvent('keyup', {
                    key: 'Enter',
                    keyCode: 13,
                    which: 13,
                    bubbles: true,
                    cancelable: true
                });
                document.getElementById('scan_packing_result').dispatchEvent(event);
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

    function clearScanners() {
        scanner_scan_bin_out.clear();
        scanner_scan_resi.clear();
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
            clearScanners();
            e.preventDefault();
            modal_opened = 'ScanOutModal';
            jQuery.noConflict();
            $('#OnlineItemsModalLabel').text('Order Number: ' + orderNumber);
            $('#OnlineItemsModal').modal('show');
            online_items_table.draw();
        });

        $(document).on('click', '#open_modal_scan_manifest_btn', function(e) {
            jQuery.noConflict();
            $('#importManifestModal').modal('show');
        })

        $(document).on('click', '.close_scanner', function(e) {
            clearScanners();
        })

        $(document).on('click', '#waiting_receipt_card', function(e) {
            transactionId = $(this).data('transaction_id');
            orderNumber = $(this).data('order_number');
            resi_number = $(this).data('resi_number');
            status = $(this).data('internal_order_status');
            status_print = $(this).data('print_status');
            clearScanners();
            jQuery.noConflict();
            $('#trx_number_title_wr').text('Order Number: ' + orderNumber);
            $('#to_id_waiting_receipt').text(transactionId);
            $('#waitingReceiptModal').modal('show');
            $('#continuePackingBtn').data('to_id', transactionId);
            $('#continuePackingBtn').data('order_number', orderNumber);
            $('#continuePackingBtn').data('resi_number', resi_number);
            $('#continuePackingBtn').data('status', status);

            if (status == 'DONE' || status == 'DONE ONLINE') {
                $('#continuePackingBtn').prop('disabled', true);
            }

            if (status_print == 0) {
                $('#continuePackingBtn').prop('disabled', true);
            } else {
                $('#continuePackingBtn').prop('disabled', false);
            }

            waiting_receipt_table.draw();
        });

        $(document).on('click', '#continuePackingBtn', function(e) {
            // $('#waitingReceiptModal').modal('hide');
            transactionId = $(this).data('to_id');
            orderNumber = $(this).data('order_number');
            resi_number = $(this).data('resi_number');
            status = $(this).data('status');

            if (status == 'WAITING RECEIPT') {
                $.ajax({
                    url: "{{ url('helper_online_done_print') }}/" + transactionId,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === '200') {
                            toastr.success('Status diupdate ke WAITING PACKING');
                        } else {
                            toastr.error('Gagal mengupdate status');
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.error('Terjadi kesalahan saat mengupdate status');
                        console.error('Error:', error);
                    }
                });
            }

            clearScanners();
            scanner_scan_resi.render(success, error);
            e.preventDefault();
            modal_opened = 'ScanPackingModal';
            jQuery.noConflict();
            $('#order_number_scan_packing').text(orderNumber);
            $('#plst_id_scan_packing').text(transactionId);
            $('#resi_number_holder').text(resi_number);
            $('#scan_packing_result').val('');
            $('#scanPackingModal').modal('show');
        })

        $(document).on('click', '#close_scan_packing_modal_btn', function(e) {
            $('#scanPackingModal').modal('hide');
        });

        $(document).on('click', '#printResiBtn', function(e) {
            e.preventDefault();
            var to_id = $('#to_id_waiting_receipt').text();

            $.ajax({
                url: "{{ url('helper_online_print_resi') }}/" + to_id,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === '200') {
                        toastr.success('Resi berhasil dicetak');
                        if (response.pdf_url) {
                            window.open(response.pdf_url, '_blank');
                        }
                    } else {
                        toastr.error('Gagal mencetak resi');
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('Terjadi kesalahan saat mencetak resi');
                    console.error('Error:', error);
                }
            });
        });

        $(document).delegate('#printNotaBtn', 'click', function() {
            var numOrder = $('#trx_number_title_wr').text().replace('Order Number: ', '');

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to print this invoice #" + numOrder + "?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, print it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log(numOrder);

                    $('#loader').show();

                    $.ajax({
                        url: '{{ url('print_online_invoice') }}',
                        method: 'POST',
                        data: {
                            orderNumber: numOrder,
                            to_id : $('#to_id_waiting_receipt').text(),
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            console.log(response.status);
                            if (response.status == 200) {
                                var printUrl = '{{ url('print_online_nota') }}/' +
                                    numOrder;
                                window.open(printUrl, '_blank');
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message || 'There was a problem printing the invoice. Please try again.',
                                    icon: 'error',
                                    confirmButtonColor: '#3085d6'
                                });
                            }

                        },
                        error: function(xhr, status, error) {
                            // Handle errors here
                            Swal.fire({
                                title: 'Error!',
                                text: 'There was a problem printing the invoice. Please try again.',
                                icon: 'error',
                                confirmButtonColor: '#3085d6'
                            });
                        },
                        complete: function() {
                            $('#loader').hide();
                        }
                    });
                }
            });

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
                                    text: response.message || "Gagal update Qc",
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
                            to_id: $(this).data('to_id'),
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
                                    text: response.message || "Gagal update Qc",
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
                                                    $('#binModal')
                                                        .modal(
                                                            'hide');

                                                    $('#sku_send').val(
                                                        '');
                                                    $('#bin_out_search')
                                                        .val('');
                                                    $('#binTable tbody')
                                                        .empty();
                                                    $('#sku_search')
                                                        .remove();
                                                    $('#bin_out_search')
                                                        .prop(
                                                            'disabled',
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

        $('#scan_packing_result').focus().on('keyup', function(e) {
            if (e.key === 'Enter') {
                let enteredResi = $(this).val();
                let validResi = $('#resi_number_holder').text();
                // Lakukan sesuatu dengan SKU yang dimasukkan
                if (enteredResi === validResi) {
                    swal({
                        title: "Konfirmasi Packing",
                        text: "Yakin sudah selesai packing?",
                        icon: "warning",
                        buttons: [
                            'Batal',
                            'Yakin'
                        ],
                        dangerMode: false,
                    }).then(function(isConfirm) {
                        if (isConfirm) {
                            $.ajax({
                                type: "POST",
                                data: {
                                    to_id: $('#plst_id_scan_packing').text(),
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                dataType: 'json',
                                url: "{{ url('helper_online_scan_packing_single') }}",
                                success: function(r) {
                                    if (r.status == '200') {
                                        $('#scanPackingModal').modal('hide');
                                        $('#scan_packing_result').val('');
                                        swal({
                                            title: 'Berhasil',
                                            text: 'Packing berhasil dikonfirmasi',
                                            icon: 'success',
                                            button: 'OK',
                                        });
                                        getListPicked();
                                    } else {
                                        swal('Gagal', 'Gagal konfirmasi packing',
                                            'error');
                                    }
                                }
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Resi tidak sesuai',
                        text: 'Nomor resi yang dimasukkan tidak cocok!',
                    });
                }

            }
        });

        $(document).on('submit', '#f_upload_manifest', function(e) {
            e.preventDefault();

            var formData = new FormData(this);

            // 1. Show Loading Swal before AJAX call
            Swal.fire({
                title: 'Memproses...', // Processing...
                html: 'Mohon tunggu sebentar saat manifest diupload.', // Please wait a moment while the manifest is uploaded.
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                type: "POST",
                url: "{{ url('helper_online_scan_manifest_bulk') }}",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // 2. Dismiss Loading Swal on success
                    Swal.close();

                    if (response.status === '200') {
                        toastr.success('Manifest berhasil diimport');
                        $('#importManifestModal').modal('hide');
                        $('#f_upload_manifest')[0].reset();
                        getListPicked();
                    } else {
                        // Show failed resi in a table
                        if (response.failed_resi && response.failed_resi.length > 0) {
                            Swal.fire({
                                title: 'Import Gagal', // Import Failed
                                html: `
                            <div style="overflow-x:auto;">
                                <table class="table" style="width:100%; text-align:left; border-collapse: collapse;">
                                    <thead>
                                        <tr>
                                            <th style="border: 1px solid #ccc; padding: 8px;">Nomor Resi Gagal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="error-table-body">
                                        </tbody>
                                </table>
                                <br/>
                                <div style="text-align: center;">
                                    <button id="export_failed_resi" class="swal2-confirm swal2-styled" style="background-color:#28a745; margin-right:10px;">Export to Excel</button>
                                    <button id="close_error_alert" class="swal2-cancel swal2-styled" style="background-color:#dc3545;">Close</button>
                                </div>
                            </div>
                        `,
                                icon: 'warning',
                                width: '600px',
                                showConfirmButton: false,
                                didOpen: () => {
                                    let tbody = document.getElementById(
                                        'error-table-body');
                                    response.failed_resi.forEach(function(
                                        resi) {
                                        let row = document
                                            .createElement('tr');
                                        row.innerHTML = `
                                    <td style="border: 1px solid #ccc; padding: 8px;">${resi || '-'}</td>
                                `;
                                        tbody.appendChild(row);
                                    });

                                    // Export to Excel button
                                    document.getElementById(
                                            'export_failed_resi')
                                        .addEventListener('click', function() {
                                            let wb = XLSX.utils.book_new();
                                            let ws_data = [
                                                [
                                                    "Nomor Resi Gagal"
                                                ], // Header
                                                ...response.failed_resi
                                                .map(resi => [
                                                    resi
                                                ]) // Each resi in its own array
                                            ];
                                            let ws = XLSX.utils
                                                .aoa_to_sheet(ws_data);
                                            XLSX.utils.book_append_sheet(wb,
                                                ws, "Failed Resi");
                                            XLSX.writeFile(wb,
                                                `failed_resi_${new Date().getTime()}.xlsx`
                                            );
                                        });

                                    // Close button
                                    document.getElementById('close_error_alert')
                                        .addEventListener('click', function() {
                                            Swal.close();
                                        });
                                }
                            });
                        } else {
                            toastr.error(response.message ||
                                'Gagal mengimport manifest'); // Failed to import manifest
                        }
                    }
                },
                error: function(xhr, status, error) {
                    // 3. Dismiss Loading Swal on error
                    Swal.close();

                    toastr.error(
                        'Terjadi kesalahan saat mengimport manifest'
                        ); // An error occurred while importing the manifest
                    console.error('Error:', error);
                }
            });
        });
    });
</script>
