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
                    is_amp: is_amp,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === '200') {
                        var chatHistory = response.data;
                        var chatContainer = $('.chat-messages');
                        chatContainer.empty(); // Clear existing messages

                        chatHistory.forEach(function(chat) {
                            var messageElement;

                            if (chat.is_amp == is_amp) {
                                // Sent message (You)
                                messageElement = $(`
                                <div class="d-flex justify-content-end mb-3">
                                    <div class="bg-danger text-white rounded px-6 py-2" style="max-width: 70%;">
                                        <small class="text-light font-weight-bold">${chat.u_name ? chat.u_name : 'You'}</small>
                                        <p class="mb-1">${chat.messages}</p>
                                        <small class="text-light">${new Date(chat.created_at).toLocaleTimeString([], {
                                hour: '2-digit',
                                minute: '2-digit'
                            })}</small>
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
                                        <small class="text-muted">${new Date(chat.created_at).toLocaleTimeString([], {
                                hour: '2-digit',
                                minute: '2-digit'
                            })}</small>
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
                    is_amp: is_amp,
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
    </script>
