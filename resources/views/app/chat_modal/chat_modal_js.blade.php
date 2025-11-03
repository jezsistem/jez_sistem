    <script>
        let chat_status = 'closed';
        let ot_id = null;

        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('file_input');
        const filePreview = document.getElementById('filePreview');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');

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
                            var fileContent = '';

                            // Check if there's a file attached
                            if (chat.file_path) {
                                var fileUrl = "{{ url('storage') }}/" + chat.file_path;
                                var fileExtension = chat.file_path.split('.').pop().toLowerCase();
                                var imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp',
                                    'svg'
                                ];

                                if (imageExtensions.includes(fileExtension)) {
                                    // Show image preview with modal trigger
                                    fileContent = `
                                                            <div class="mb-2">
                                                                <img src="${fileUrl}" alt="Image" style="max-width: 200px; max-height: 200px; border-radius: 5px; cursor: pointer;" class="img-thumbnail image-preview" data-image-url="${fileUrl}">
                                                            </div>
                                                        `;
                                } else {
                                    // Show file name as link
                                    var fileName = chat.file_path.split('/').pop();
                                    fileContent = `
                                                            <div class="mb-2">
                                                                <a href="${fileUrl}" target="_blank" class="text-decoration-underline">
                                                                    <i class="fas fa-file"></i> ${fileName}
                                                                </a>
                                                            </div>
                                                        `;
                                }
                            }

                            if (chat.is_amp == is_amp) {
                                // Sent message (You)
                                messageElement = $(`
                                                        <div class="d-flex justify-content-end mb-3">
                                                            <div class="bg-danger text-white rounded px-6 py-2" style="max-width: 70%;">
                                                                <small class="text-light font-weight-bold">${chat.u_name ? chat.u_name : 'You'}</small>
                                                                ${fileContent}
                                                                ${chat.messages ? `<p class="mb-1">${chat.messages}</p>` : ''}
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
                                                                ${fileContent}
                                                                ${chat.messages ? `<p class="mb-1">${chat.messages}</p>` : ''}
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

                        // Add click event for image preview
                        $('.image-preview').off('click').on('click', function() {
                            var imageUrl = $(this).data('image-url');
                            showImageModal(imageUrl);
                        });
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

        function showImageModal(imageUrl) {
            var modalHtml = `
                <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-center">
                                <img src="${imageUrl}" alt="Image" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('body').append(modalHtml);
            $('#imageModal').modal('show');
            $('#imageModal').on('hidden.bs.modal', function() {
                $(this).remove();
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

        function sendFileMessage() {
            var fileInput = $('#file_input')[0];
            var caption = $('#file_caption').val();
            var ot_id_new = ot_id; // Use JavaScript variable, not PHP variable

            if (fileInput.files.length === 0) {
                toastr.error('Please select a file to send.');
                return;
            }

            var file = fileInput.files[0];
            var maxSize = 3 * 1024 * 1024; // 3 MB
            if (file.size > maxSize) {
                toastr.error('File size exceeds 3 MB. Please choose a smaller file.');
                // Clear selection and hide preview
                $('#file_input').val('');
                if (typeof filePreview !== 'undefined' && filePreview) {
                    filePreview.classList.add('d-none');
                }
                return;
            }

            var formData = new FormData();
            formData.append('ot_id', ot_id_new);
            formData.append('file', file);
            formData.append('message', caption);
            formData.append('is_amp', is_amp);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            // Show loading swal
            Swal.fire({
                title: 'Sending...',
                text: 'Please wait while we upload your file',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ url('send_chat_history_online_transaction') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.close(); // Close loading swal
                    if (response.status === '200') {
                        toastr.success('File sent successfully!');
                        $('#uploadFileModal').modal('hide');
                        $('#file_input').val('');
                        $('#file_caption').val('');
                        if (typeof filePreview !== 'undefined' && filePreview) {
                            filePreview.classList.add('d-none');
                        }
                        // Refresh chat data
                        getChatData(ot_id);
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close(); // Close loading swal
                    toastr.error('Failed to send file. Please try again.');
                    console.error('Error sending file:', error);
                }
            });
        }

        // Click to browse
        dropZone.addEventListener('click', () => fileInput.click());

        // Drag and drop events
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                displayFileInfo(files[0]);
            }
        });

        // File input change
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                displayFileInfo(e.target.files[0]);
            }
        });

        function displayFileInfo(file) {
            fileName.textContent = file.name;
            fileSize.textContent = `(${(file.size / 1024).toFixed(2)} KB)`;
            filePreview.classList.remove('d-none');
        }

        function removeFile() {
            fileInput.value = '';
            filePreview.classList.add('d-none');
        }

        filePreview.addEventListener('click', () => {
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const fileURL = URL.createObjectURL(file);
                window.open(fileURL, '_blank');
            }
        });
    </script>
