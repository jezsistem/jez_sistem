<script>
    // Global reference for DataTable
    window.externalAssignmentTable = null;

    document.addEventListener('DOMContentLoaded', function() {
        const detailsTable = document.getElementById('detailsTable');
        const addRowBtn = document.getElementById('addRow');

        if (!detailsTable || !addRowBtn) return;

        let rowIndex = detailsTable.querySelectorAll('tbody tr').length;

        // Add row
        addRowBtn.addEventListener('click', function() {
            const tbody = detailsTable.querySelector('tbody');
            const tr = document.createElement('tr');

            tr.innerHTML = `
            <td><input type="text" name="details[${rowIndex}][activity]" class="form-control" required></td>
            <td><input type="date" name="details[${rowIndex}][date]" class="form-control"></td>
            <td><input type="time" name="details[${rowIndex}][start_time]" class="form-control"></td>
            <td><input type="time" name="details[${rowIndex}][end_time]" class="form-control"></td>
            <td><textarea name="details[${rowIndex}][description]" class="form-control" rows="1"></textarea></td>
            <td><button type="button" class="btn btn-danger btn-sm btn-remove-row">&times;</button></td>
        `;

            tbody.appendChild(tr);
            rowIndex++;
        });

        // Remove row
        detailsTable.addEventListener('click', function(e) {
            const removeBtn = e.target.closest('.btn-remove-row');
            if (!removeBtn) return;

            const tr = removeBtn.closest('tr');
            const tbody = detailsTable.querySelector('tbody');

            if (tbody.querySelectorAll('tr').length === 1) {
                tr.querySelectorAll('input, textarea').forEach(input => input.value = '');
                return;
            }

            tr.remove();

            // re-index supaya name[] tetap urut
            const rows = Array.from(tbody.querySelectorAll('tr'));
            rows.forEach((row, idx) => {
                row.querySelectorAll('input, textarea').forEach(input => {
                    input.name = input.name.replace(/details\[\d+\]/,
                    `details[${idx}]`);
                });
            });

            rowIndex = rows.length;
        });
    });

    function copyLeaveLink(id) {
        const url = `${window.location.origin}/external-assignment-requests/${id}`;

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

    let cashRowIndex = 1;

    $(document).on('click', '#addCashDetailRow', function() {
        let row = `
        <tr>
            <td>
                <input type="text" name="cash_details[${cashRowIndex}][cash_purpose]" class="form-control" placeholder="Purpose">
            </td>
            <td>
                <input type="number" step="0.01" name="cash_details[${cashRowIndex}][cash_amount]" class="form-control cash-amount" placeholder="0.00">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger removeCashDetailRow">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
        $('#cashDetailTable tbody').append(row);
        cashRowIndex++;
        calculateTotalCash();
    });

    $(document).on('click', '.removeCashDetailRow', function() {
        $(this).closest('tr').remove();
        calculateTotalCash();
    });

    // Initialize Select2 for user_id dropdown
    $('#user_id').select2({
        placeholder: 'All Staffs',
        parent: $('#user_id').parent(),
        allowClear: true,
        width: '100%'
    });

    $(document).on('input', '.cash-amount', function() {
        calculateTotalCash();
    });

    function calculateTotalCash() {
        let total = 0;
        $('.cash-amount').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#total_cash_used').val(total.toFixed(2));
    }

    // Date filter functionality
    function handleDateFilterChange(value) {
        console.log('handleDateFilterChange called with value:', value);

        const startDateContainer = document.getElementById('start_date_container');
        const endDateContainer = document.getElementById('end_date_container');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        console.log('Found elements:', {
            startDateContainer: !!startDateContainer,
            endDateContainer: !!endDateContainer,
            startDateInput: !!startDateInput,
            endDateInput: !!endDateInput
        });

        if (value === 'custom') {
            console.log('Setting custom mode - showing date inputs');
            startDateContainer.style.display = 'block';
            endDateContainer.style.display = 'block';
        } else {
            console.log('Setting predefined filter mode - hiding date inputs');
            startDateContainer.style.display = 'none';
            endDateContainer.style.display = 'none';

            // Set default dates based on filter
            const today = new Date();
            let startDate, endDate;

            switch (value) {
                case 'this_week':
                    startDate = new Date(today.getTime());
                    startDate.setDate(today.getDate() - today.getDay() + 1); // Monday
                    endDate = new Date(today.getTime());
                    endDate.setDate(today.getDate() - today.getDay() + 7); // Sunday
                    break;
                case 'past_week':
                    startDate = new Date(today.getTime());
                    startDate.setDate(today.getDate() - today.getDay() - 6); // Last Monday
                    endDate = new Date(today.getTime());
                    endDate.setDate(today.getDate() - today.getDay()); // Last Sunday
                    break;
                case 'next_week':
                    startDate = new Date(today.getTime());
                    startDate.setDate(today.getDate() - today.getDay() + 8); // Next Monday
                    endDate = new Date(today.getTime());
                    endDate.setDate(today.getDate() - today.getDay() + 14); // Next Sunday
                    break;
                case 'this_month':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1); // First day of month
                    endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0); // Last day of month
                    break;
                case 'last_month':
                    startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1); // First day of last month
                    endDate = new Date(today.getFullYear(), today.getMonth(), 0); // Last day of last month
                    break;
                case 'next_month':
                    startDate = new Date(today.getFullYear(), today.getMonth() + 1, 1); // First day of next month
                    endDate = new Date(today.getFullYear(), today.getMonth() + 2, 0); // Last day of next month
                    break;
            }

            console.log('Calculated dates for', value, ':', {
                startDate,
                endDate
            });

            // Format dates for input fields
            if (startDate && endDate) {
                const formatDate = (date) => {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                };

                const formattedStartDate = formatDate(startDate);
                const formattedEndDate = formatDate(endDate);

                console.log('Setting input values for', value, ':', {
                    formattedStartDate,
                    formattedEndDate
                });

                startDateInput.value = formattedStartDate;
                endDateInput.value = formattedEndDate;

                // Update breadcrumb display
                updateBreadcrumbDates(formattedStartDate, formattedEndDate, value);

                // Reload DataTable with new filter dates
                if (value !== 'custom' && window.externalAssignmentTable) {
                    console.log('Reloading DataTable for filter:', value);
                    console.log('Current start_date:', startDateInput.value);
                    console.log('Current end_date:', endDateInput.value);
                    console.log('Current date_filter:', value);

                    // Force DataTable to reload with new parameters
                    window.externalAssignmentTable.ajax.reload();

                    // Update stats with new filter
                    updateStats();
                } else if (value !== 'custom') {
                    console.log('DataTable not available yet, waiting for initialization...');
                    // Wait for DataTable to be ready
                    setTimeout(() => {
                        if (window.externalAssignmentTable) {
                            console.log('DataTable now available, reloading...');
                            window.externalAssignmentTable.ajax.reload();
                            updateStats();
                        }
                    }, 500);
                }
            }
        }
    }

    // Function to update stats
    function updateStats() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const dateFilter = document.getElementById('date_filter').value;
        const userId = document.getElementById('user_id').value;
        const leaveTypeId = document.getElementById('leave_type_id').value;
        const status = document.getElementById('status').value;

        console.log('Updating stats with filters:', {
            startDate,
            endDate,
            dateFilter,
            userId,
            leaveTypeId,
            status
        });

        // Make AJAX request to get updated stats
        $.ajax({
            url: "{{ route('leave-requests.stats') }}",
            method: 'GET',
            data: {
                start_date: startDate,
                end_date: endDate,
                date_filter: dateFilter,
                user_id: userId,
                leave_type_id: leaveTypeId,
                status: status
            },
            success: function(response) {
                console.log('Stats updated:', response);

                // Update stats display
                document.getElementById('total-requests').textContent = response.total;
                document.getElementById('pending-requests').textContent = response.pending;
                document.getElementById('approved-requests').textContent = response.approved;
                document.getElementById('rejected-requests').textContent = response.rejected;
            },
            error: function(xhr, status, error) {
                console.error('Failed to update stats:', error);
            }
        });
    }

    // Function to update breadcrumb dates
    function updateBreadcrumbDates(startDate, endDate, dateFilter) {
        const breadcrumbElement = document.querySelector('.breadcrumb-item .text-muted');
        if (breadcrumbElement) {
            if (dateFilter && dateFilter !== 'custom') {
                // Format dates for display
                const formatDisplayDate = (dateString) => {
                    const date = new Date(dateString);
                    const day = date.getDate();
                    const month = date.toLocaleDateString('en-US', {
                        month: 'short'
                    });
                    const year = date.getFullYear();
                    return `${day} ${month} ${year}`;
                };

                const displayStartDate = formatDisplayDate(startDate);
                const displayEndDate = formatDisplayDate(endDate);
                breadcrumbElement.textContent = `${displayStartDate} to ${displayEndDate}`;
            } else {
                breadcrumbElement.textContent = `${startDate} to ${endDate}`;
            }
        }
    }

    // Robust DataTable initialization with retry mechanism
    function initDataTable() {
        if (typeof $.fn.DataTable === 'undefined') {
            console.log('DataTable not available, retrying in 100ms...');
            setTimeout(initDataTable, 100);
            return;
        }

        try {
            console.log('Initializing leave request DataTable...');

            window.externalAssignmentTable = $('#externalAssignmentTable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                dom: 'rt<"pagination-class"ip>',
                ajax: {
                    url: "{{ route('external-assignment.datatables') }}",
                    data: function(d) {
                        d.search = $('#leave_request_search').val();
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.user_id = $('#user_id').val();
                        d.ea_type_id = $('#ea_type_id').val();
                        d.status = $('#status').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        width: '5%'
                    },
                    {
                        data: 'request_date',
                        name: 'request_date',
                        width: '10%'
                    },
                    {
                        data: 'u_name',
                        name: 'u_name',
                        width: '15%'
                    },
                    {
                        data: 'ud_name',
                        name: 'ud_name',
                        width: '12%'
                    },
                    {
                        data: 'ea_name',
                        name: 'ea_name',
                        width: '12%'
                    },
                    {
                        data: 'ear_locations',
                        name: 'ear_locations',
                        width: '12%'
                    },
                    {
                        data: 'ear_date_start',
                        name: 'ear_date_start',
                        width: '10%'
                    },
                    {
                        data: 'ear_date_end',
                        name: 'ear_date_end',
                        width: '10%'
                    },
                    {
                        data: 'ear_status',
                        name: 'ear_status',
                        width: '10%'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '16%'
                    },
                ],
                order: [
                    [1, 'desc']
                ],
                pageLength: 25,
                language: {
                    "sProcessing": "Loading...",
                    "sZeroRecords": "Tidak ditemukan data yang sesuai",
                    "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                    "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                }
            });

            console.log('Leave request DataTable initialized successfully');
            console.log('DataTable instance:', window.externalAssignmentTable);

            // Initialize dropdown menu system
            initializeSimpleDropdown();

            // Refresh table when filter form is submitted
            $('form').on('submit', function() {
                window.externalAssignmentTable.draw();
            });

            // Auto refresh table when filter values change
            $('#start_date, #end_date, #user_id, #ea_type_id, #status').on('change', function() {
                window.externalAssignmentTable.draw();
            });

            // Search functionality with debounce
            var searchTimeout;
            $('#leave_request_search').on('keyup input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    window.externalAssignmentTable.draw();
                }, 300);
            });

        } catch (error) {
            console.error('Error initializing leave request DataTable:', error);
            setTimeout(initDataTable, 100);
        }
    }

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Initialize DataTable
        initDataTable();
    });

    // Simple working dropdown solution
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

    // Re-initialize dropdown on each draw
    if (window.externalAssignmentTable) {
        window.externalAssignmentTable.on('draw.dt', function() {
            setTimeout(initializeSimpleDropdown, 100);
        });
    }

    // Global variables for approval
    var currentLeaveRequestId = null;
    var currentAction = null;

    // Modal functions using vanilla JavaScript (like staff)
    function showModal(modalId) {
        console.log('showModal called with:', modalId);
        var modal = document.getElementById(modalId);
        console.log('Modal element found:', modal);

        if (modal) {
            modal.style.display = 'flex';
            modal.classList.add('show');
            document.body.classList.add('modal-open');
            console.log('Modal should now be visible');
        } else {
            console.error('Modal element not found:', modalId);
        }
    }

    function hideModal(modalId) {
        var modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.classList.remove('modal-open');
        }
    }

    // Function to show approval modal
    function showApprovalModal(leaveRequestId, action) {
        console.log('showApprovalModal called with:', {
            leaveRequestId,
            action
        });

        currentLeaveRequestId = leaveRequestId;
        currentAction = action;

        // Update modal title and button
        if (action === 'approve') {
            document.getElementById('approvalModalLabel').textContent = 'Approve Leave Request';
            document.getElementById('approvalSubmitBtn').className = 'btn btn-success';
            document.getElementById('approvalSubmitBtn').textContent = 'Approve';

            // Update notes label and help for approve
            document.getElementById('notes_label').textContent = 'Notes (Optional)';
            document.getElementById('notes_help').textContent = 'Notes are optional for approval';
            document.getElementById('notes_help').className = 'text-muted';
        } else if (action === 'reject') {
            document.getElementById('approvalModalLabel').textContent = 'Reject Leave Request';
            document.getElementById('approvalSubmitBtn').className = 'btn btn-danger';
            document.getElementById('approvalSubmitBtn').textContent = 'Reject';

            // Update notes label and help for reject
            document.getElementById('notes_label').textContent = 'Notes (Required)';
            document.getElementById('notes_help').textContent = 'Notes are required for rejection';
            document.getElementById('notes_help').className = 'text-danger';
        }

        // Clear previous notes
        document.getElementById('approval_notes').value = '';

        // Show modal
        console.log('Showing modal...');
        showModal('approvalModal');
        console.log('Modal should be visible now');
    }

    // Handle approval form submission - will be initialized in DOMContentLoaded
    function initializeApprovalForm() {
        var form = document.getElementById('approvalForm');
        if (form) {
            console.log('Approval form found, adding event listener');
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Form submitted');

                try {

                    var notes = document.getElementById('approval_notes').value;
                    var url = '';

                    // Validation for reject - notes are required
                    if (currentAction === 'reject' && !notes.trim()) {
                        alert('Notes are required for rejection. Please enter a reason.');
                        return;
                    }

                    if (currentAction === 'approve') {
                        url = "{{ route('leave-requests.approve', ':id') }}".replace(':id',
                            currentLeaveRequestId);
                    } else if (currentAction === 'reject') {
                        url = "{{ route('leave-requests.reject', ':id') }}".replace(':id',
                            currentLeaveRequestId);
                    }

                    // Disable submit button
                    var submitBtn = document.getElementById('approvalSubmitBtn');
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Processing...';

                    // Send AJAX request
                    console.log('Sending approval request to:', url);

                    // Check CSRF token
                    var csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        console.error('CSRF token meta tag not found');
                        alert('CSRF token not found. Please refresh the page.');
                        return;
                    }

                    var tokenValue = csrfToken.getAttribute('content');
                    if (!tokenValue) {
                        console.error('CSRF token value is empty');
                        alert('CSRF token is empty. Please refresh the page.');
                        return;
                    }

                    console.log('CSRF token found:', tokenValue);
                    console.log('Data:', {
                        lr_admin_notes: notes,
                        _token: tokenValue
                    });

                    console.log('AJAX request starting...');

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            lr_admin_notes: notes,
                            _token: tokenValue
                        },
                        success: function(response) {
                            console.log('Success response:', response);
                            console.log('Response type:', typeof response);
                            console.log('Response success:', response.success);

                            // Hide modal
                            hideModal('approvalModal');

                            // Show success message
                            if (response.success) {
                                toastr.success(response.message ||
                                    'Leave request processed successfully');
                                console.log('Success message shown');
                            } else {
                                toastr.error(response.message || 'Failed to process leave request');
                                console.log('Error message shown');
                            }

                            // Refresh table
                            console.log('Attempting to refresh table...');
                            if (window.externalAssignmentTable) {
                                console.log('Table found, refreshing...');
                                window.externalAssignmentTable.draw();
                                console.log('Table refresh completed');
                            } else {
                                console.log('Table not found, trying alternative refresh...');
                                // Try to find table by ID or class
                                var table = $('.dataTable').DataTable();
                                if (table) {
                                    table.draw();
                                    console.log('Alternative table refresh completed');
                                } else {
                                    console.log('No DataTable found, reloading page...');
                                    location.reload();
                                }
                            }
                        },
                        error: function(xhr) {
                            console.log('Error response:', xhr);
                            console.log('Status:', xhr.status);
                            console.log('Response text:', xhr.responseText);
                            console.log('Response headers:', xhr.getAllResponseHeaders());

                            var errorMessage = 'An error occurred while processing the request';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            toastr.error(errorMessage);

                            // Re-enable submit button on error
                            var submitBtn = document.getElementById('approvalSubmitBtn');
                            submitBtn.disabled = false;
                            if (currentAction === 'approve') {
                                submitBtn.textContent = 'Approve';
                            } else {
                                submitBtn.textContent = 'Reject';
                            }
                        },
                        complete: function() {
                            console.log('AJAX request completed');
                            // Re-enable submit button
                            submitBtn.disabled = false;
                            if (currentAction === 'approve') {
                                submitBtn.textContent = 'Approve';
                            } else {
                                submitBtn.textContent = 'Reject';
                            }
                            console.log('Submit button re-enabled');
                        }
                    });

                    console.log('AJAX request sent successfully');

                } catch (error) {
                    console.error('Error in form submission:', error);
                    alert('Error: ' + error.message);

                    // Re-enable submit button on error
                    var submitBtn = document.getElementById('approvalSubmitBtn');
                    submitBtn.disabled = false;
                    if (currentAction === 'approve') {
                        submitBtn.textContent = 'Approve';
                    } else {
                        submitBtn.textContent = 'Reject';
                    }
                }
            });
        } else {
            console.error('Approval form not found');
        }
    }

    // Reset modal when closed
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM Content Loaded - initializing leave request functionality');

        // Initialize approval form
        initializeApprovalForm();

        // Add form submit event listener for date filter
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault(); // Prevent form submission

                // Update breadcrumb before reloading DataTable
                const dateFilter = document.getElementById('date_filter').value;
                const startDate = document.getElementById('start_date').value;
                const endDate = document.getElementById('end_date').value;

                if (dateFilter && dateFilter !== 'custom') {
                    updateBreadcrumbDates(startDate, endDate, dateFilter);
                } else {
                    updateBreadcrumbDates(startDate, endDate, 'custom');
                }

                // Reload DataTable
                console.log('Reloading DataTable...');
                console.log('window.externalAssignmentTable:', window.externalAssignmentTable);
                console.log('DataTable type:', typeof window.externalAssignmentTable);

                if (window.externalAssignmentTable && typeof window.externalAssignmentTable.draw ===
                    'function') {
                    console.log('DataTable found, calling draw()...');
                    try {
                        window.externalAssignmentTable.draw();
                        console.log('DataTable draw() called successfully');
                    } catch (drawError) {
                        console.error('Error calling DataTable draw():', drawError);
                        // Fallback to page reload
                        location.reload();
                    }
                } else {
                    console.log(
                        'DataTable not found or draw method not available, trying alternative...');
                    // Try alternative method
                    try {
                        const table = $('.dataTable').DataTable();
                        if (table && typeof table.draw === 'function') {
                            table.draw();
                            console.log('Alternative DataTable reload successful');
                        } else {
                            console.log('No valid DataTable found, reloading page...');
                            location.reload();
                        }
                    } catch (altError) {
                        console.error('Error with alternative DataTable reload:', altError);
                        location.reload();
                    }
                }
            });
        }

        // Initialize breadcrumb with current values
        const dateFilter = document.getElementById('date_filter').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        if (dateFilter && dateFilter !== 'custom') {
            updateBreadcrumbDates(startDate, endDate, dateFilter);
        }

        // Handle modal close buttons
        document.querySelectorAll('[data-dismiss="modal"]').forEach(function(button) {
            button.addEventListener('click', function() {
                var modal = this.closest('.modal');
                if (modal) {
                    hideModal(modal.id);
                }
            });
        });

        // Handle modal backdrop click
        document.getElementById('approvalModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideModal('approvalModal');
            }
        });

        // Handle escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideModal('approvalModal');
            }
        });

        // Handle modal backdrop click
        document.querySelectorAll('.modal').forEach(function(modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    hideModal(this.id);
                }
            });
        });

        // Reset modal state when closed
        function resetModal() {
            currentLeaveRequestId = null;
            currentAction = null;
            document.getElementById('approval_notes').value = '';
            var submitBtn = document.getElementById('approvalSubmitBtn');
            submitBtn.disabled = false;
        }

        // Add event listener for modal close
        document.getElementById('approvalModal').addEventListener('click', function(e) {
            if (e.target === this) {
                resetModal();
            }
        });

        // Add event listener for close button
        document.querySelector('#approvalModal .close').addEventListener('click', function() {
            hideModal('approvalModal');
            resetModal();
        });
    });

    // Function to view attachment
    function viewAttachment(leaveRequestId, filePath, fileName, fileType) {
        console.log('viewAttachment called with:', {
            leaveRequestId,
            filePath,
            fileName,
            fileType
        });

        const modal = document.getElementById('attachmentModal');
        const content = document.getElementById('attachmentContent');
        const downloadLink = document.getElementById('downloadAttachment');
        const modalTitle = document.getElementById('attachmentModalLabel');

        console.log('Modal elements found:', {
            modal: !!modal,
            content: !!content,
            downloadLink: !!downloadLink,
            modalTitle: !!modalTitle
        });

        // Set modal title
        modalTitle.textContent = `View Attachment: ${fileName}`;

        // Set download link
        downloadLink.href = `/storage/${filePath}`;
        downloadLink.download = fileName;

        // Clear previous content
        content.innerHTML = '';

        // Show loading
        content.innerHTML =
            '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Loading attachment...</p></div>';

        // Show modal
        showModal('attachmentModal');

        // Load attachment content based on file type
        if (fileType && fileType.includes('image')) {
            // For images, show directly
            content.innerHTML = `
                <div class="text-center">
                    <img src="/storage/${filePath}" alt="${fileName}" class="img-fluid" style="max-height: 500px;">
                    <p class="mt-2"><strong>${fileName}</strong></p>
                </div>
            `;
        } else if (fileType && fileType.includes('pdf')) {
            // For PDFs, show in iframe
            content.innerHTML = `
                <div class="text-center">
                    <iframe src="/storage/${filePath}" width="100%" height="500" frameborder="0"></iframe>
                    <p class="mt-2"><strong>${fileName}</strong></p>
                </div>
            `;
        } else {
            // For other file types, show file info
            content.innerHTML = `
                <div class="text-center">
                    <div class="alert alert-info">
                        <i class="fas fa-file fa-3x mb-3"></i>
                        <h5>${fileName}</h5>
                        <p>This file type cannot be previewed directly.</p>
                        <p>Please download the file to view its contents.</p>
                    </div>
                </div>
            `;
        }
    }

    // Function to delete leave request
    function deleteLeaveRequest(id, staffName) {
        console.log('deleteLeaveRequest called with:', {
            id,
            staffName
        });

        if (confirm('Are you sure you want to delete the leave request for "' + staffName +
                '"? This action cannot be undone.')) {
            var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            console.log('CSRF Token:', token);

            // Show loading state
            if (typeof toastr !== 'undefined') {
                toastr.info('Deleting leave request...');
            }

            const url = "{{ route('leave-requests.destroy', ':id') }}".replace(':id', id);
            console.log('DELETE URL:', url);

            // Use jQuery AJAX like working implementations
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    _token: token
                },
                success: function(response) {
                    console.log('Delete response:', response);

                    if (response.success) {
                        // Show success message
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message || 'Leave request deleted successfully!');
                        }

                        // Reload DataTable using the same method as break time
                        if (window.externalAssignmentTable && typeof window.externalAssignmentTable.draw ===
                            'function') {
                            console.log('Reloading DataTable with draw() method');
                            window.externalAssignmentTable.draw();
                        } else if (window.externalAssignmentTable && typeof window.externalAssignmentTable
                            .ajax !== 'undefined') {
                            console.log('Reloading DataTable with ajax.reload() method');
                            window.externalAssignmentTable.ajax.reload();
                        } else {
                            console.log('DataTable not found, trying alternative method');
                            try {
                                const table = $('#externalAssignmentTable').DataTable();
                                if (table && typeof table.draw === 'function') {
                                    table.draw();
                                    console.log('Alternative DataTable reload successful');
                                } else {
                                    console.log('Alternative method failed, reloading page');
                                    setTimeout(() => location.reload(), 1000);
                                }
                            } catch (error) {
                                console.error('Alternative method error:', error);
                                console.log('Falling back to page reload');
                                setTimeout(() => location.reload(), 1000);
                            }
                        }
                    } else {
                        // Show error message
                        if (typeof toastr !== 'undefined') {
                            toastr.error(response.message || 'Failed to delete leave request');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {
                        xhr,
                        status,
                        error
                    });

                    // Show error message
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Error deleting leave request. Please try again.');
                    }
                }
            });
        }
    }

    {{-- $('#externalAssignmentForm').on('submit', function(e) { --}}
    {{--    e.preventDefault(); --}}

    {{--    let formData = new FormData(this); --}}

    {{--    // Rundown details --}}
    {{--    let rundowns = []; --}}
    {{--    $('#detailsTable tbody tr').each(function() { --}}
    {{--        rundowns.push({ --}}
    {{--            activity: $(this).find('input[name*="[activity]"]').val(), --}}
    {{--            rundown_date: $(this).find('input[name*="[date]"]').val(), --}}
    {{--            start_time: $(this).find('input[name*="[start_time]"]').val(), --}}
    {{--            end_time: $(this).find('input[name*="[end_time]"]').val(), --}}
    {{--            description: $(this).find('textarea[name*="[description]"]').val(), --}}
    {{--        }); --}}
    {{--    }); --}}
    {{--    formData.append('rundowns', JSON.stringify(rundowns)); --}}

    {{--    // Cash details --}}
    {{--    let cashDetails = []; --}}
    {{--    $('#cashDetailTable tbody tr').each(function() { --}}
    {{--        cashDetails.push({ --}}
    {{--            cash_purpose: $(this).find('input[name*="[cash_purpose]"]').val(), --}}
    {{--            cash_amount: $(this).find('input[name*="[cash_amount]"]').val(), --}}
    {{--        }); --}}
    {{--    }); --}}
    {{--    formData.append('cash_details', JSON.stringify(cashDetails)); --}}

    {{--    $.ajax({ --}}
    {{--        url: "{{ route('external-assignment-requests.store') }}" --}}
    {{--        method: "POST", --}}
    {{--        data: formData, --}}
    {{--        processData: false, --}}
    {{--        contentType: false, --}}
    {{--        success: function(res) { --}}
    {{--            if (res.status === 'success') { --}}
    {{--                toastr.success(res.message); --}}
    {{--            } else { --}}
    {{--                toastr.error(res.message); --}}
    {{--            } --}}
    {{--        }, --}}
    {{--        error: function(xhr) { --}}
    {{--            toastr.error(xhr.responseJSON?.message || 'Something went wrong'); --}}
    {{--        } --}}
    {{--    }); --}}
    {{-- }); --}}



    $(document).ready(function() {
        $('#externalAssignmentForm').on('submit', function(e) {
            e.preventDefault();

            let rundowns = [];
            $('#detailsTable tbody tr').each(function(index) {
                rundowns.push({
                    activity: $(this).find(
                        'input[name^="details["][name$="[activity]"]').val(),
                    date: $(this).find('input[name^="details["][name$="[date]"]').val(),
                    start_time: $(this).find(
                        'input[name^="details["][name$="[start_time]"]').val(),
                    end_time: $(this).find(
                        'input[name^="details["][name$="[end_time]"]').val(),
                    description: $(this).find(
                        'textarea[name^="details["][name$="[description]"]').val(),
                });
            });

            // Ambil data cash detail (pakai #cashDetailTable)
            let cashDetails = [];
            $('#cashDetailTable tbody tr').each(function(index) {
                cashDetails.push({
                    cash_purpose: $(this).find(
                        'input[name^="cash_details["][name$="[cash_purpose]"]')
                    .val(),
                    cash_amount: $(this).find(
                            'input[name^="cash_details["][name$="[cash_amount]"]')
                    .val(),
                });
            });


            // Buat payload
            let payload = {
                _token: "{{ csrf_token() }}",
                ea_id: $('#ea_id').val(),
                ear_cash_advance: $('#ear_cash_advance').val(),
                ear_date_start: $('#ear_date_start').val(),
                ear_time_start: $('#ear_time_start').val(),
                ear_date_end: $('#ear_date_end').val(),
                ear_time_end: $('#ear_time_end').val(),
                ear_locations: $('#ear_locations').val(),
                ear_note: $('#ear_note').val(),
                rundowns: rundowns,
                cashDetails: cashDetails,
            };

            console.log("Data yang akan dikirim:", payload);

            // Kalau mau disable submit ke backend dulu saat debugging:
            // return;

            $.ajax({
                url: "{{ route('external-assignment-requests.store') }}",
                type: "POST",
                data: payload,
                success: function(res) {
                    console.log("Response dari server:", res);
                    if (res.status === 'success') {
                        toastr.success(res.message);
                        window.location.href = "{{ url('external-assignment') }}";
                    } else {
                        toastr.error(res.message);
                    }
                },
                error: function(xhr) {
                    console.error("Error response:", xhr.responseText);
                    toastr.error('Something went wrong');
                }
            });
        });
    });
    $(document).ready(function() {
        $('#externalAssignmentFormUpdate').on('submit', function(e) {
            e.preventDefault();

            let rundowns = [];
            $('#detailsTable tbody tr').each(function(index) {
                rundowns.push({
                    activity: $(this).find(
                        'input[name^="details["][name$="[activity]"]').val(),
                    rundown_date: $(this).find('input[name^="details["][name$="[date]"]').val(),
                    start_time: $(this).find(
                        'input[name^="details["][name$="[start_time]"]').val(),
                    end_time: $(this).find(
                        'input[name^="details["][name$="[end_time]"]').val(),
                    description: $(this).find(
                        'textarea[name^="details["][name$="[description]"]').val(),
                });
            });

            // Ambil data cash detail (pakai #cashDetailTable)
            let cashDetails = [];
            $('#cashDetailTable tbody tr').each(function(index) {
                cashDetails.push({
                    cash_purpose: $(this).find(
                        'input[name^="cash_details["][name$="[cash_purpose]"]')
                    .val(),
                    cash_amount: $(this).find(
                            'input[name^="cash_details["][name$="[cash_amount]"]')
                    .val(),
                });
            });


            // Buat payload
            let payload = {
                _token: "{{ csrf_token() }}",
                ea_id: $('#ea_id').val(),
                ear_cash_advance: $('#ear_cash_advance').val(),
                ear_date_start: $('#ear_date_start').val(),
                ear_time_start: $('#ear_time_start').val(),
                ear_date_end: $('#ear_date_end').val(),
                ear_time_end: $('#ear_time_end').val(),
                ear_locations: $('#ear_locations').val(),
                ear_note: $('#ear_note').val(),
                rundowns: rundowns,
                cashDetails: cashDetails,
            };

            console.log("Data yang akan dikirim:", payload);

            // Kalau mau disable submit ke backend dulu saat debugging:
            // return;

            $.ajax({
                url: "{{ route('external-assignment-requests.update', '') }}/" + $('#external_assignment_request_id').val(),
                type: "POST",
                data: payload,
                success: function(res) {
                    console.log("Response dari server:", res);
                    if (res.status === 'success') {
                        toastr.success(res.message);
                        window.location.href = "{{ url('external-assignment') }}";
                    } else {
                        toastr.error(res.message);
                    }
                },
                error: function(xhr) {
                    console.error("Error response:", xhr.responseText);
                    toastr.error('Something went wrong');
                }
            });
        });
    });
</script>
