<script>
    var current_tab = '';
    let chat_status = 'closed';
    let ot_id = null;
    var detail_table = '';

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
                <div class="col-md-4 mb-4 text-left" id="transaction_card" data-transaction_id=${transaction.transaction_id} style="cursor: pointer;">
                    <div class="card shadow-sm" style="border-radius: 10px; overflow: hidden; border: 2px solid ${getBorderColor(transaction.internal_order_status)};">
                        <div class="card-body" style="background-color: #f8f9fa;">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title text-primary" style="font-weight: bold;">Order Number: ${transaction.order_number}</h5>

                                <button class="btn position-relative" onclick="openChat(${transaction.transaction_id})" data-trx_number=${transaction.order_number}>
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

        $(document).on('click', '#transaction_card', function() {
            var transactionId = $(this).data('transaction_id');
            // Add your logic here to handle the click event, e.g., open a modal or redirect
            console.log('Transaction ID:', transactionId);
        });



    });
</script>
