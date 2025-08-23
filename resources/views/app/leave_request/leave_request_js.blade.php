<script>
    // Global reference for DataTable
    window.leaveRequestTable = null;

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
                case 'this_month':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1); // First day of month
                    endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0); // Last day of month
                    break;
                case 'last_month':
                    startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1); // First day of last month
                    endDate = new Date(today.getFullYear(), today.getMonth(), 0); // Last day of last month
                    break;
            }
            
            console.log('Calculated dates for', value, ':', { startDate, endDate });
            
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
                
                console.log('Setting input values for', value, ':', { formattedStartDate, formattedEndDate });
                
                startDateInput.value = formattedStartDate;
                endDateInput.value = formattedEndDate;
                
                // Update breadcrumb display
                updateBreadcrumbDates(formattedStartDate, formattedEndDate, value);
            }
        }
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
                    const month = date.toLocaleDateString('en-US', { month: 'short' });
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
            
            window.leaveRequestTable = $('#leaveRequestTable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                responsive: false, // Set to false to prevent conflicts
                dom: 'rt<"pagination-class"ip>',
                ajax: {
                    url : "{{ route('leave-requests.datatables') }}",
                    data : function (d) {
                        d.search = $('#leave_request_search').val();
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.date_filter = $('#date_filter').val();
                        d.user_id = $('#user_id').val();
                        d.leave_type_id = $('#leave_type_id').val();
                        d.status = $('#status').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%' },
                    { data: 'lr_date', name: 'lr_date', width: '10%' },
                    { data: 'u_name', name: 'u_name', width: '15%' },
                    { data: 'ud_name', name: 'ud_name', width: '12%' },
                    { data: 'lt_name', name: 'lt_name', width: '12%' },
                    { data: 'lr_start_date', name: 'lr_start_date', width: '10%' },
                    { data: 'lr_end_date', name: 'lr_end_date', width: '10%' },
                    { data: 'lr_status', name: 'lr_status', width: '10%' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, width: '16%' },
                ],
                columnDefs: [
                    {
                        "targets": 0,
                        "className": "text-center",
                        "width": "5%"
                    },
                    {
                        "targets": [1, 5, 6],
                        "className": "text-center"
                    },
                    {
                        "targets": 7,
                        "className": "text-center"
                    },
                    {
                        "targets": 8,
                        "className": "text-center"
                    }
                ],
                order: [[1, 'desc']],
                pageLength: 25,
                language: {
                    "sProcessing":   "Loading...",
                    "sLengthMenu":   "Tampilkan _MENU_ entri",
                    "sZeroRecords":  "Tidak ditemukan data yang sesuai",
                    "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
                    "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                    "sInfoPostFix":  "",
                    "sSearch":       "Cari:",
                    "sUrl":          ""
                }
            });
            
            console.log('Leave request DataTable initialized successfully');
            console.log('DataTable instance:', window.leaveRequestTable);
            
            // Initialize dropdown menu system
            initializeSimpleDropdown();
            
            // Refresh table when filter form is submitted
            $('form').on('submit', function() {
                window.leaveRequestTable.draw();
            });
            
            // Auto refresh table when filter values change
            $('#start_date, #end_date, #user_id, #leave_type_id, #status').on('change', function() {
                window.leaveRequestTable.draw();
            });
            
            // Search functionality with debounce
            var searchTimeout;
            $('#leave_request_search').on('keyup input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    window.leaveRequestTable.draw();
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
            
            console.log('Dropdown clicked, menu found:', $menu.length);
            
            // Close all other menus first
            $('.menu').not($menu).removeClass('show');
            
            // Toggle current menu
            $menu.toggleClass('show');
            
            console.log('Menu toggled, has show class:', $menu.hasClass('show'));
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
    if (window.leaveRequestTable) {
        window.leaveRequestTable.on('draw.dt', function() {
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
        console.log('showApprovalModal called with:', { leaveRequestId, action });
        
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
                    url = "{{ route('leave-requests.approve', ':id') }}".replace(':id', currentLeaveRequestId);
                } else if (currentAction === 'reject') {
                    url = "{{ route('leave-requests.reject', ':id') }}".replace(':id', currentLeaveRequestId);
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
                            toastr.success(response.message || 'Leave request processed successfully');
                            console.log('Success message shown');
                        } else {
                            toastr.error(response.message || 'Failed to process leave request');
                            console.log('Error message shown');
                        }
                        
                        // Refresh table
                        console.log('Attempting to refresh table...');
                        if (window.leaveRequestTable) {
                            console.log('Table found, refreshing...');
                            window.leaveRequestTable.draw();
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
                if (window.leaveRequestTable) {
                    window.leaveRequestTable.ajax.reload();
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
    
</script> 