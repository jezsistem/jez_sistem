<script>
    let chat_status = 'closed';
    let ot_id = null;
    var detail_table = '';
    var online_transaction_table = '';

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
        $('.close-modal').trigger('click');

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
                is_amp: 1,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === '200') {
                    var chatHistory = response.data;
                    var chatContainer = $('.chat-messages');
                    chatContainer.empty(); // Clear existing messages

                    chatHistory.forEach(function(chat) {
                        var messageElement;

                        if (chat.is_amp == 1) {
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

    function deleteItem(otd_id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('transaksi_online_delete_item') }}",
                    type: 'POST',
                    data: {
                        otd_id: otd_id,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === '200') {
                            Swal.fire(
                                'Terhapus!',
                                'Item berhasil dihapus.',
                                'success'
                            );
                            detail_table.draw(false);
                        } else {
                            Swal.fire(
                                'Gagal!',
                                response.message || 'Terjadi kesalahan saat menghapus item.',
                                'error'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire(
                            'Error!',
                            'Terjadi kesalahan saat menghapus item.',
                            'error'
                        );
                        console.error('Error deleting item:', error);
                    }
                });
            }
        });
    }




    document.getElementById('splitForm').addEventListener('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);
        let uploadBtn = document.getElementById('uploadBtn');
        let loading = document.getElementById('loadingOverlay');

        uploadBtn.disabled = true;
        loading.style.display = 'block'; // tampilkan loading

        fetch("{{ route('pdf.split') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                loading.style.display = 'none';
                uploadBtn.disabled = false;
                $('#ImportModal').modal('hide');

                if (data.message) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // ✅ reset form
                    document.getElementById('splitForm').reset();
                }
            })
            .catch(err => {
                loading.style.display = 'none';
                uploadBtn.disabled = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan saat memproses file.'
                });
            });
    });


    // ketika tombol history diklik
    document.getElementById('historyBtn').addEventListener('click', function() {
        const modal = $('#historyModal');
        const content = $('#historyContent');

        modal.modal('show');
        content.html(
            '<div class="text-center p-4"><div class="spinner-border text-info"></div><p class="mt-2">Memuat data...</p></div>'
        );

        fetch('{{ route('split.history.ajax') }}')
            .then(res => res.text())
            .then(html => content.html(html))
            .catch(() => content.html('<div class="text-danger p-4 text-center">Gagal memuat data</div>'));
    });

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
                is_amp: 1,
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

    function pickItems(warehouse_st_id, to_id, sku, to_detail_id, qty) {
        console.log(to_id, sku, to_detail_id, qty);

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: `Yakin mau pick SKU ${sku} dengan qty ${qty}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, pick!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('transaksi_online_pick_items') }}",
                    type: 'POST',
                    data: {
                        to_id: to_id,
                        sku: sku,
                        to_detail_id: to_detail_id,
                        qty: qty,
                        warehouse_st_id: warehouse_st_id,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === '200') {
                            toastr.success('Items picked successfully!');
                            detail_table.draw(false);
                        } else {
                            toastr.error(response.message ||
                                'Failed to pick items. Please try again.');
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.error('An error occurred while picking items. Please try again.');
                        console.error('Error picking items:', error);
                    }
                });
            }
        });
    }

    function clearPrintStatus(ot_id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Status cetak invoice akan direset!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, reset!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('clear_print_status_online_transaction') }}/" + ot_id,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === '200') {
                            Swal.fire(
                                'Berhasil!',
                                'Status cetak invoice telah direset.',
                                'success'
                            );
                            online_transaction_table.draw(false);
                        } else {
                            Swal.fire(
                                'Gagal!',
                                response.message ||
                                'Terjadi kesalahan saat mereset status cetak invoice.',
                                'error'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire(
                            'Error!',
                            'Terjadi kesalahan saat mereset status cetak invoice.',
                            'error'
                        );
                        console.error('Error resetting print status:', error);
                    }
                });
            }
        });
    }

    $(document).ready(function() {
        startChatPolling();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        // var online_transaction_table = $('#OnlineTransactionb').DataTable({
        //     destroy: true,
        //     processing: true,
        //     serverSide: true,
        //     responsive: true,
        //     dom: '<"text-right"l>rt<"text-right"ip>',
        //     buttons: [{
        //         "extend": 'excelHtml5',
        //         "text": 'Excel',
        //         "className": 'btn btn-primary btn-xs'
        //     }],
        //     ajax: {
        //         url: "{{ url('transaksi_online_datatables') }}",
        //         data: function(d) {
        //             d.search = $('#online_transaction_search').val();
        //             d.st_id = $('#st_id_filter').val();
        //             d.status = $('#filter_status').val();
        //         }
        //     },
        //     columns: [{
        //             data: 'DT_RowIndex',
        //             name: 'to_id',
        //             searchable: false
        //         },
        //         {
        //             data: 'order_number',
        //             name: 'to_order_number'
        //         },
        //         {
        //             data: 'no_resi',
        //             name: 'no_resi'
        //         },
        //         {
        //             data: 'platform_name',
        //             name: 'platform_name'
        //         },
        //         {
        //             data: 'order_date_created',
        //             name: 'order_date_created'
        //         },
        //         {
        //             data: 'total_item',
        //             name: 'total_item'
        //         },
        //         {
        //             data: 'shipping_fee',
        //             name: 'shipping_fee',
        //             render: function(data, type, row) {
        //                 return !data || isNaN(data) ? '-' : formatRupiah(parseInt(data));
        //             }
        //         },
        //         {
        //             data: 'total_payment',
        //             name: 'total_payment',
        //             render: function(data, type, row) {
        //                 return !data || isNaN(data) ? '-' : formatRupiah(parseInt(data));
        //             }

        //         },
        //         {
        //             data: 'order_status',
        //             name: 'order_status'
        //         },
        //     ],
        //     columnDefs: [{
        //         "targets": 0,
        //         "className": "text-center",
        //         "width": "0%"
        //     }],
        //     language: {
        //         "lengthMenu": "MENU",
        //     }
        // });


        // $('#st_id_filter').on('change', function() {
        //     online_transaction_table.draw(false);
        // });

        // // Initialize Select2 on the select element
        // $('#filter_status').select2({
        //     width: "200px",
        //     dropdownParent: $('#filter_status_parent')
        // });

        // $('#filter_status').on('change', function() {
        //     console.log($(this).val()); // Logs the selected value (0 or 1)
        //     online_transaction_table.draw();
        // });


        // $(document).delegate('#import_modal', 'click', function() {
        //     $('#ImportModal').modal('show');
        // });

        // $('#online_transaction_search').on('keyup', function() {
        //     online_transaction_table.draw(false);
        //     console.log($('#online_transaction_search').val())
        // });



        online_transaction_table = $('#OnlineTransactionb').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: true,
            dom: '<"text-right"l>rt<"text-right"ip>',
            buttons: [{
                "extend": 'excelHtml5',
                "text": 'Excel',
                "className": 'btn btn-primary btn-xs'
            }],
            ajax: {
                url: "{{ url('transaksi_online_datatables') }}", // Endpoint DataTables
                data: function(d) {
                    d.search = $('#online_transaction_search').val();
                    d.st_id = $('#st_id_filter').val();
                    d.status = $('#filter_status').val(); // Status aktif dari tab
                    d.tab_status = $('#tab_status').val(); // Status aktif dari tab
                    d.chat_status = $('#filter_status_chat').val(); // Status chat dari filter
                    d.warehouse = $('#filter_warehouse').val(); // Warehouse dari filter
                    d.courier = $('#filter_courier').val(); // Courier dari filter
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'to_id',
                    searchable: false
                },
                {
                    data: 'order_number',
                    name: 'to_order_number'
                },
                {
                    data: 'no_resi',
                    name: 'no_resi'
                },
                {
                    data: 'platform_name',
                    name: 'platform_name'
                },
                {
                    data: 'order_date_created',
                    name: 'order_date_created'
                },
                {
                    data: 'total_item',
                    name: 'total_item'
                },
                {
                    data: 'shipping_fee',
                    name: 'shipping_fee',
                    render: function(data) {
                        return !data || isNaN(data) ? '-' : formatRupiah(parseInt(data));
                    }
                },
                {
                    data: 'total_payment',
                    name: 'total_payment',
                    render: function(data) {
                        return !data || isNaN(data) ? '-' : formatRupiah(parseInt(data));
                    }
                },
                {
                    data: 'order_status',
                    name: 'order_status'
                },
                {
                    data: 'internal_order_status',
                    name: 'internal_order_status'
                },
                {
                    data: 'action',
                    name: 'action'
                }
            ],
            columnDefs: [{
                "targets": 0,
                "className": "text-center",
                "width": "5%"
            }],
            language: {
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "zeroRecords": "Tidak ada data ditemukan",
                "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                "infoEmpty": "Tidak ada data yang tersedia",
                "infoFiltered": "(disaring dari total _MAX_ data)"
            }
        });

        $('#trxTabs .nav-link').on('click', function(e) {
            e.preventDefault();

            $('#trxTabs .nav-link').removeClass('active');
            $(this).addClass('active');

            var status = $(this).data('status');
            $('#tab_status').val(status);

            console.log(status)

            online_transaction_table.ajax.reload();
        });

        $('.close-modal').on('click', function() {
            online_transaction_table.draw(false);
        });

        $('#st_id_filter').on('change', function() {
            online_transaction_table.draw(false); // Memuat ulang tabel tanpa reset halaman
        });

        $('#filter_status').select2({
            width: "200px",
            dropdownParent: $('#filter_status_parent') // Menentukan parent untuk dropdown
        });

        $('#filter_status').on('change', function() {
            console.log($(this).val()); // Log nilai yang dipilih (0 atau 1)
            online_transaction_table.draw(); // Memuat ulang tabel sesuai dengan filter status
        });

        $('#filter_status_chat').select2({
            width: "200px",
            dropdownParent: $('#filter_status_chat_parent') // Menentukan parent untuk dropdown
        });

        $('#filter_status_chat').on('change', function() {
            console.log($(this).val()); // Log nilai yang dipilih (0 atau 1)
            online_transaction_table.draw(); // Memuat ulang tabel sesuai dengan filter status
        });

        $('#filter_warehouse').select2({
            width: "200px",
            dropdownParent: $('#filter_warehouse_parent') // Menentukan parent untuk dropdown
        });

        $('#filter_warehouse').on('change', function() {
            console.log($(this).val()); // Log nilai yang dipilih (0 atau 1)
            online_transaction_table.draw(); // Memuat ulang tabel sesuai dengan filter status
        });

        $('#filter_courier').select2({
            width: "200px",
            dropdownParent: $('#filter_courier_parent') // Menentukan parent untuk dropdown
        });

        $('#filter_courier').on('change', function() {
            console.log($(this).val()); // Log nilai yang dipilih (0 atau 1)
            online_transaction_table.draw(); // Memuat ulang tabel sesuai dengan filter status
        });

        $('#online_transaction_search').on('keyup', function() {
            online_transaction_table.draw(); // Memuat ulang tabel setiap kali ada perubahan pencarian
        });

        {{-- $('#f_import').on('submit' , function (e) { --}}
        {{--    e.preventDefault(); --}}
        {{--    jQuery.noConflict(); --}}
        {{--    $('#import_data_btn').html('Proses...'); --}}
        {{--    $('#import_data_btn').attr('disabled', true); --}}
        {{--    var formData = new FormData(this); --}}

        {{--    $.ajax({ --}}
        {{--        type: 'POST', --}}
        {{--        url: "{{ url('transaksi_online_import')}}", --}}
        {{--        data: formData, --}}
        {{--        dataType: 'json', --}}
        {{--        cache:false, --}}
        {{--        contentType: false, --}}
        {{--        processData: false, --}}
        {{--        success: function(data) { --}}
        {{--            // console.log(data.data['missingBarcode']); --}}
        {{--            $("#import_data_btn").html('Import'); --}}
        {{--            $("#import_data_btn").attr("disabled", false); --}}
        {{--            jQuery.noConflict(); --}}
        {{--            if (data.status == '200') { --}}
        {{--                $("#ImportModal").modal('hide'); --}}

        {{--                swal('Berhasil', 'Data berhasil diimport', 'success'); --}}
        {{--                $('#f_import')[0].reset(); --}}
        {{--                excelImportData = data.data['processedData']; --}}
        {{--                console.log(data.data); --}}
        {{--                console.log(data.name); --}}
        {{--                // shopee_tables.draw(); --}}
        {{--                // tiktok_tables.draw(); --}}
        {{--            } else if (data.status == '400') { --}}
        {{--                $("#ImportModal").modal('hide'); --}}
        {{--                console.log(data.data) --}}
        {{--                swal('Error', 'File yang anda import kosong atau format tidak tepat', 'warning'); --}}
        {{--            } else { --}}
        {{--                $("#ImportModal").modal('hide'); --}}

        {{--            } --}}
        {{--        }, --}}
        {{--        error: function(data){ --}}
        {{--            swal('Error', data, 'error'); --}}
        {{--        } --}}
        {{--    }); --}}
        {{-- }); --}}

        {{-- $('#f_import').on('submit', function (e) { --}}
        {{--    e.preventDefault(); --}}
        {{--    jQuery.noConflict(); --}}

        {{--    // Show the spinner and disable the button --}}
        {{--    $('#import_data_btn').html('<span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses...'); --}}
        {{--    $('#import_data_btn').attr('disabled', true); --}}

        {{--    var formData = new FormData(this); --}}

        {{--    // Manually append the value of st_id_form to the FormData --}}
        {{--    var st_id_form_value = $('#st_id_form').val();  // Get the value of the disabled input --}}
        {{--    formData.append('st_id_form', st_id_form_value); // Append it to the FormData --}}
        {{--    --}}
        {{--    console.log($('#st_id_form').val()); --}}

        {{--    $.ajax({ --}}
        {{--        type: 'POST', --}}
        {{--        url: "{{ url('transaksi_online_import')}}", --}}
        {{--        data: formData, --}}
        {{--        dataType: 'json', --}}
        {{--        cache: false, --}}
        {{--        contentType: false, --}}
        {{--        processData: false, --}}
        {{--        success: function (data) { --}}
        {{--            // Hide the spinner and enable the button --}}
        {{--            $('#import_data_btn').html('Import'); --}}
        {{--            $('#import_data_btn').attr("disabled", false); --}}

        {{--            if (data.status == '200') { --}}
        {{--                $("#ImportModal").modal('hide'); --}}
        {{--                swal('Berhasil', 'Data berhasil diimport', 'success'); --}}
        {{--                $('#f_import')[0].reset(); --}}
        {{--                excelImportData = data.data['processedData']; --}}
        {{--                console.log(data.data); --}}
        {{--                console.log(data.name); --}}
        {{--            } else if (data.status == '400') { --}}
        {{--                $("#ImportModal").modal('hide'); --}}
        {{--                console.log(data.data); --}}
        {{--                swal('Error', 'File yang anda import kosong atau format tidak tepat', 'warning'); --}}
        {{--            } else { --}}
        {{--                $("#ImportModal").modal('hide'); --}}
        {{--            } --}}
        {{--        }, --}}
        {{--        error: function (data) { --}}
        {{--            // Hide the spinner in case of error --}}
        {{--            $('#import_data_btn').html('Import'); --}}
        {{--            $('#import_data_btn').attr("disabled", false); --}}

        {{--            swal('Error', data, 'error'); --}}
        {{--        } --}}
        {{--    }); --}}
        {{--    online_transaction_table.draw(false); --}}
        {{-- }); --}}

        $('#f_import').on('submit', function(e) {
            e.preventDefault();
            jQuery.noConflict();

            // Show the spinner and disable the button
            $('#import_data_btn').html(
                '<span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses...'
            );
            $('#import_data_btn').attr('disabled', true);

            // Create a new FormData object from the form
            var formData = new FormData(this);

            // Manually append the value of st_id_form to the FormData
            var st_id_form_value = $('#st_id_form').val(); // Get the value of the disabled input
            formData.append('st_id_form', st_id_form_value); // Append it to the FormData

            console.log(formData);

            $.ajax({
                type: 'POST',
                url: "{{ url('transaksi_online_import') }}",
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    // Hide the spinner and enable the button
                    $('#import_data_btn').html('Import');
                    $('#import_data_btn').attr("disabled", false);

                    if (data.status == '200') {
                        $("#ImportModal").modal('hide');
                        swal('Berhasil', 'Data berhasil diimport', 'success');
                        $('#f_import')[0].reset();
                        excelImportData = data.data['processedData'];
                        console.log(data.data);
                        console.log(data.name);
                    } else if (data.status == '422') {
                        $("#ImportModal").modal('hide');
                        console.log(data.data);
                        swal('Belum dimacro ya jez? 😒😒',
                            data.message ||
                            'File yang anda import kosong atau format tidak tepat',
                            'warning');
                    } else if (data.status == '400') {
                        $("#ImportModal").modal('hide');
                        console.log(data.data);
                        swal('Error',
                            'File yang anda import kosong atau format tidak tepat',
                            'warning');
                    } else {
                        $("#ImportModal").modal('hide');
                        swal('Error',
                            data.message || 'Terjadi kesalahan saat mengimpor data',
                            'error');
                    }
                },
                error: function(data) {
                    // Hide the spinner in case of error
                    $('#import_data_btn').html('Import');
                    $('#import_data_btn').attr("disabled", false);

                    swal('Error', data, 'error');
                }
            });
            online_transaction_table.draw(false);
        });
        {{-- $('#f_import').on('submit', function (e) { --}}
        {{--    e.preventDefault(); --}}
        {{--    jQuery.noConflict(); --}}

        {{--    // Show the spinner and disable the button --}}
        {{--    $('#import_data_btn').html('<span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Proses...'); --}}
        {{--    $('#import_data_btn').attr('disabled', true); --}}

        {{--    // Create a new FormData object from the form --}}
        {{--    var formData = new FormData(this); --}}

        {{--    // Manually append the value of st_id_form to the FormData --}}
        {{--    var st_id_form_value = $('#st_id_form').val();  // Get the value of the disabled input --}}
        {{--    formData.append('st_id_form', st_id_form_value); // Append it to the FormData --}}

        {{--    console.log(formData); --}}

        {{--    $.ajax({ --}}
        {{--        type: 'POST', --}}
        {{--        url: "{{ url('transaksi_online_import') }}", --}}
        {{--        data: formData, --}}
        {{--        dataType: 'json', --}}
        {{--        cache: false, --}}
        {{--        contentType: false, --}}
        {{--        processData: false, --}}
        {{--        success: function (data) { --}}
        {{--            // Hide the spinner and enable the button --}}
        {{--            $('#import_data_btn').html('Import'); --}}
        {{--            $('#import_data_btn').attr("disabled", false); --}}

        {{--            if (data.status == '200') { --}}
        {{--                $("#ImportModal").modal('hide'); --}}
        {{--                swal('Berhasil', 'Data berhasil diimport', 'success'); --}}
        {{--                $('#f_import')[0].reset(); --}}
        {{--                excelImportData = data.data['processedData']; --}}
        {{--                console.log(data.data); --}}
        {{--                console.log(data.name); --}}
        {{--            } else if (data.status == '400') { --}}
        {{--                $("#ImportModal").modal('hide'); --}}
        {{--                console.log(data.data); --}}
        {{--                swal('Error', 'File yang anda import kosong atau format tidak tepat', 'warning'); --}}
        {{--            } else { --}}
        {{--                $("#ImportModal").modal('hide'); --}}
        {{--            } --}}
        {{--        }, --}}
        {{--        error: function (data) { --}}
        {{--            // Hide the spinner in case of error --}}
        {{--            $('#import_data_btn').html('Import'); --}}
        {{--            $('#import_data_btn').attr("disabled", false); --}}

        {{--            swal('Error', data, 'error'); --}}
        {{--        } --}}
        {{--    }); --}}
        {{--    online_transaction_table.draw(false); --}}
        {{-- }); --}}

        function formatRupiah(angka) {
            return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        detail_table = $('#Detailtb').DataTable({

            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            dom: 'rt<"text-right"ip>',
            buttons: [{
                "extend": 'excelHtml5',
                "text": 'Excel',
                "className": 'btn btn-primary btn-xs'
            }],
            ajax: {
                url: "{{ url('transaksi_online_datatables_detail') }}",
                data: function(d) {
                    d.to_id = $('#to_id').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'to_id'
                },
                {
                    data: 'article',
                    name: 'article'
                },
                {
                    data: 'ps_barcode',
                    name: 'ps_barcode'
                },
                {
                    data: 'sku',
                    name: 'sku'
                },
                {
                    data: 'to_qty',
                    name: 'to_qty',
                    render: function(data, type, row) {
                        return !data || isNaN(data) ? '-' : formatRupiah(parseInt(data));
                    }
                },
                {
                    data: 'shopee_price',
                    name: 'shopee_price',
                    render: function(data, type, row) {
                        return !data || isNaN(data) ? '-' : formatRupiah(parseInt(data));
                    }

                },
                {
                    data: 'jez_price',
                    name: 'jez_price',
                    render: function(data, type, row) {
                        return !data || isNaN(data) ? '-' : formatRupiah(parseInt(data));
                    }
                },
                {
                    data: 'gap_price',
                    name: 'gap_price',
                    render: function(data, type, row) {
                        return !data || isNaN(data) ? '-' : formatRupiah(parseInt(data));
                    }
                },
                {
                    data: 'total_discount',
                    name: 'total_discount',
                    render: function(data, type, row) {
                        return !data || isNaN(data) ? '-' : formatRupiah(parseInt(data));
                    }
                },
                {
                    data: 'ns_before_admin',
                    name: 'ns_before_admin',
                    render: function(data, type, row) {
                        return !data || isNaN(data) ? '-' : formatRupiah(parseInt(data));
                    }
                },
                {
                    data: 'final_price',
                    name: 'final_price',
                    render: function(data, type, row) {
                        const platformPrice = parseInt(row.ns_before_admin);
                        const quantity = parseInt(row.to_qty);

                        if (isNaN(platformPrice) || isNaN(quantity)) {
                            return '-';
                        }

                        const calculatedFinalPrice = platformPrice * quantity;
                        return formatRupiah(calculatedFinalPrice);
                    }
                },
                {
                    data: 'warehouse',
                    name: 'warehouse'
                },
                {
                    data: 'pick_status',
                    name: 'pick_status'
                },
                {
                    data: 'action',
                    name: 'action'
                },
            ],
            columnDefs: [{
                "targets": 0,
                "className": "text-center",
                "width": "0%"
            }],
            order: [
                [0, 'desc']
            ],
        });
        jQuery.noConflict();

        $(document).delegate('#add_new_item_btn', 'click', function() {
            jQuery.noConflict();
            $to_id = $('#to_id').val();
            $.ajax({
                url: "{{ url('transaksi_online_get_items') }}",
                type: 'GET',
                data: {
                    to_id: $to_id,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === '200') {
                        var items = response.data;
                        var select = $('#item_sejenis_select');
                        select.empty();
                        select.append('<option value="">Pilih Item</option>');

                        items.forEach(function(item) {
                            select.append('<option value="' + item.id + '">' + item
                                .sku + '</option>');
                        });
                    } else {
                        toastr.error('Failed to load items. Please try again.');
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error(
                        'An error occurred while fetching items. Please try again.');
                    console.error('Error fetching items:', error);
                }
            });

            $('#tambahItemModal').modal('show');
        });

        $(document).delegate('#edit_item_btn', 'click', function() {
            jQuery.noConflict();
            var otd_id = $(this).data('otd_id');
            var qty = $(this).data('qty');
            var to_id = $('#to_id').val();

            $('#edit_item_otd_id').val(otd_id);
            $('#edit_item_qty').val(qty);
            $('#edit_item_to_id').val(to_id);
            $('#editItemModal').modal('show');
        });

        $('#f_edit_item').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            var otd_id = $(this).data('otd_id');

            formData.append('otd_id', otd_id);

            $.ajax({
                url: "{{ url('transaksi_online_edit_item') }}",
                type: 'POST',
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.status === '200') {
                        $('#editItemModal').modal('hide');
                        toastr.success('Item berhasil diedit!');
                        $('#f_edit_item')[0].reset();
                        detail_table.draw(false);
                    } else {
                        toastr.error(response.message ||
                            'Failed to edit item. Please try again.');
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error(response.message ||
                        'An error occurred while editing the item. Please try again.');
                    console.error('Error editing item:', error);
                }
            });
        });

        $('#f_tambah_item').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);

            $.ajax({
                url: "{{ url('transaksi_online_add_new_item') }}",
                type: 'POST',
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.status === '200') {
                        $('#tambahItemModal').modal('hide');
                        toastr.success('Item berhasil ditambahkan!');
                        $('#f_tambah_item')[0].reset();
                        detail_table.draw(false);
                    } else {
                        toastr.error(response.message ||
                            'Failed to add item. Please try again.');
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error(response.message ||
                        'An error occurred while adding the item. Please try again.');
                    console.error('Error adding item:', error);
                }
            });
        });

        $(document).delegate('#print_invoice', 'click', function() {
            var numOrder = document.getElementById('num_order').textContent;

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
                            to_id: $('#to_id').val(),
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            console.log(response.status);
                            if (response.status == 200) {
                                var printUrl = '{{ url('print_online_nota') }}/' +
                                    numOrder;
                                window.open(printUrl, '_blank');
                                online_transaction_table.draw(false);
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message ||
                                        'There was a problem printing the invoice. Please try again.',
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


        $(document).delegate('#detail_btn', 'click', function() {
            console.log('tes');
            var to_id = $(this).attr('data-to_id');
            var num_order = $(this).attr('data-num_order');
            var status = $(this).attr('data-status');
            $('#to_id').val(to_id);
            $('#num_order').text(num_order);
            $('#status_pesanan').val(status);

            if (status == 'Batal') {
                $('#add_item_detail_btn').hide();
            } else {
                $('#add_item_detail_btn').show();
            }

            $('#DetailModal').off('show.bs.modal').on('show.bs.modal', function() {
                detail_table.draw(false);
            }).modal('show');
        });


        $(document).delegate('#add_item_detail_btn', 'click', function() {
            let to_id = $('#to_id').val();
            let no_pesanan = $('#num_order').text();

            console.log(no_pesanan);
            $('#add_item_to_id').val(to_id);
            $('#no_pesanan').val(no_pesanan);
            $('#DetailModal').modal('hide');
            $('#addItemModal').modal('show');
        })

        // $('#sales_online_export').on('click', function () {
        $(document).delegate('#sales_online_export', 'click', function(e) {
            e.preventDefault();
            let date = $('#kt_dashboard_daterangepicker_date')
                .text(); // Ensure this gets the correct date range
            let branch_trx = $('#branch_trx').val();
            let status_trx = $('#status_trx').val();
            let changeplatform = $('#changeplatform').val();

            console.log("Branch:", branch_trx);
            console.log("Status:", status_trx);
            console.log("Platform:", changeplatform);
            console.log("Date:", date);

            // Redirect with parameters
            window.location.href = "{{ url('online_sales_export') }}?branch=" + branch_trx +
                "&date=" + date +
                "&status=" + status_trx +
                "&changeplatform=" + changeplatform;
        });





        $('#download_template_shopee').on('click', function() {
            console.log('Halo');
        });

        $('#download_template_tiktok').on('click', function() {
            console.log('Halo tiktok');
        });

        var picker = $('#kt_dashboard_daterangepicker');
        if ($('#kt_dashboard_daterangepicker').length == 0) {
            return;
        }
        var start = moment();
        var end = moment();

        function cb(start, end, label) {
            var title = '';
            var range = '';
            var hidden_range = '';
            var st_id = $('#st_id_filter').val();

            if ((end - start) < 100 || label == 'Hari Ini') {
                title = 'Hari Ini:';
                range = start.format('DD MMM YYYY');
                hidden_range = start.format('YYYY-MM-DD');
            } else if (label == 'Kemarin') {
                title = 'Kemarin:';
                range = start.format('DD MMM YYYY');
                hidden_range = start.format('YYYY-MM-DD');
            } else {
                range = start.format('DD MMM YYYY') + ' - ' + end.format('DD MMM YYYY');
                hidden_range = start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD');
            }
            $('#sales_date').val(hidden_range);
            $('#kt_dashboard_daterangepicker_date').html(range);
            $('#kt_dashboard_daterangepicker_title').html(title);
            // online_transaction_table.draw();
            // article_report_table.draw();
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
                'Bulan Kemarin': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                    'month').endOf('month')]
            }
        }, cb);
        cb(start, end, '');


    });
</script>
