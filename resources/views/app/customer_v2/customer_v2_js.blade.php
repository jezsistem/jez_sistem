<script>

    function initializeSimpleDropdown() {
        console.log('Initializing simple dropdown system for Leave Request');

        // Remove any existing event handlers
        $(document).off('click', '[data-kt-menu-trigger="click"]');

        // Add click handler for dropdown toggle
        $(document).on('click', '[data-kt-menu-trigger="click"]', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var $this = $(this);
            var $menu = $this.siblings('.menu');
            var $cardBody = $this.closest('.card.card-custom').find('> .card-body');
            var $row = $this.closest('tr');

            // Tutup semua menu lain
            $('.menu').not($menu).removeClass('show');
            $cardBody.removeClass('pb-extra2'); // reset padding

            // Toggle menu ini
            $menu.toggleClass("show");

            // Jika menu terbuka & baris ini adalah row terakhir
            if ($menu.hasClass('show') && $row.is(':last-child')) {
                $cardBody.addClass('pb-extra2');
            }
        });

        // Close menu when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.menu').removeClass('show');
            }
        });

        // Close menu when clicking on menu items
        $(document).on('click', '.menu-link', function(e) {
            if ($(this).attr('onclick')) {
                // For buttons with onclick, let the onclick handle it
                return;
            }
            // For other links, close menu after a short delay
            setTimeout(function() {
                $('.menu').removeClass('show');
            }, 100);
        });

        console.log('Simple dropdown system initialized for Leave Request');
        console.log('Dropdown elements found:', document.querySelectorAll('.dropdown').length);
    }

    function exportRequestToExcel() {
        // Implement the export functionality here
        let status = $('#status').val();
        let division = $('#division').val();
        let start_date = $('#start_date').val();
        let end_date = $('#end_date').val();
        let staff = $('#staff').val();

        $.ajax({
            url: "{{ route('overtime.export.excel') }}",
            type: "GET",
            data: {
                status: status,
                division: division,
                start_date: start_date,
                end_date: end_date,
                staff: staff
            },
            xhrFields: {
                responseType: 'blob' // Important for binary data
            },
            success: function(response, status, xhr) {
                // Get the filename from the Content-Disposition header
                let filename = "";
                let disposition = xhr.getResponseHeader('Content-Disposition');
                if (disposition && disposition.indexOf('attachment') !== -1) {
                    let filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                    let matches = filenameRegex.exec(disposition);
                    if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
                }

                // Create a link to download the file
                let link = document.createElement('a');
                let url = window.URL.createObjectURL(response);
                link.href = url;
                link.download = filename || 'overtime_requests.xlsx';
                document.body.appendChild(link);
                link.click();
                setTimeout(function() {
                    document.body.removeChild(link);
                    window.URL.revokeObjectURL(url);
                }, 100);
            },
            error: function(xhr) {
                Swal.fire('Error', 'Failed to export data to Excel.', 'error');
            }
        });
    }

    function copyOvertimeLink(id) {
        const url = `${window.location.origin}/overtime/${id}`;

        navigator.clipboard.writeText(url).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Link disalin',
                text: 'URL telah disalin ke clipboard!',
                timer: 1500,
                showConfirmButton: false
            });
        });
    }

    $(document).ready(function () {

        const table = $('#customerTable').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
                url: "{{ route('customers-v2.data') }}",
                data: function (d) {
                    d.tier = $('select[name="cust_tier"]').val();
                    d.coin_active = $('select[name="cust_coin_active"]').val();
                    d.phone = $('input[name="phone"]').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },

                { data: 'cust_name', name: 'cust_name' },

                { data: 'cust_phone', name: 'cust_phone' },

                { data: 'cust_tier', name: 'cust_tier' },

                {
                    data: 'cust_coin',
                    name: 'cust_coin',
                    render: function (data) {
                        return `<span class="badge bg-info text-white">${data ?? 0} Coin</span>`;
                    }
                },

                { data: 'trx_value', name: 'trx_value', orderable: false, searchable: false },

                {
                    data: 'cust_member_card',
                    name: 'cust_member_card',
                    render: function (data) {
                        return data == '1'
                            ? '<span class="badge bg-success">Yes</span>'
                            : '<span class="badge bg-secondary">No</span>';
                    }
                },

                { data: 'coin_status', name: 'coin_status', orderable: false, searchable: false },

                { data: 'created_at', name: 'created_at' },

                { data: 'action', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']]
        });

        // FILTER
        $('#filterForm').on('submit', function (e) {
            e.preventDefault();
            table.ajax.reload();
        });

        $('select, input').on('change', function () {
            table.ajax.reload();
        });

    });



    $(document).ready(function() {
        console.log('Select2 init...');

        $('#assigned_staff').select2({
            // placeholder: "Select staff from your department",
            width: '100%'
        });
    });


    $(document).ready(function() {
        console.log('Select2 init...');
        $('#assigned_staff').select2({
            width: '100%'
        });

        // 🔹 Handle submit via AJAX
        $('#overtimeRequestForm').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const formData = new FormData(this);

            console.log('Submitting form...'); // Debug

            $.ajax({
                url: "{{ route('overtime.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    console.log('Sending AJAX request...');
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Berhasil', response.message, 'success');
                        form[0].reset();
                        $('#assigned_staff').val(null).trigger('change');
                        window.location.href = "{{ url('overtime') }}";
                    } else {
                        Swal.fire('Gagal', response.message || 'Terjadi kesalahan', 'error');
                    }
                },
                error: function(xhr) {
                    console.log("xhr.responseJSON:", xhr.responseJSON);

                    if (xhr.status === 422) {

                        // Jika respons berisi message (BUKAN errors)
                        if (xhr.responseJSON.message) {
                            Swal.fire('Validasi Gagal', xhr.responseJSON.message, 'warning');
                            return;
                        }

                        // Jika respons bentuknya errors
                        let errors = xhr.responseJSON.errors;
                        let messages = Object.values(errors).flat().join('\n');
                        Swal.fire('Validasi Gagal', messages, 'warning');

                    } else {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan server', 'error');
                    }
                }
            });
        });
    });
</script>