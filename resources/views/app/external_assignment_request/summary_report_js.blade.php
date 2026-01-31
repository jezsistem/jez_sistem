<script>
    // Global reference for DataTable
    window.summaryTable = null;

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
                if (value !== 'custom' && window.summaryTable) {
                    console.log('Reloading DataTable for filter:', value);
                    console.log('Current start_date:', startDateInput.value);
                    console.log('Current end_date:', endDateInput.value);
                    console.log('Current date_filter:', value);

                    // Force DataTable to reload with new parameters
                    window.summaryTable.ajax.reload();
                } else if (value !== 'custom') {
                    console.log('DataTable not available yet, waiting for initialization...');
                    // Wait for DataTable to be ready
                    setTimeout(() => {
                        if (window.summaryTable) {
                            console.log('DataTable now available, reloading...');
                            window.summaryTable.ajax.reload();
                        }
                    }, 500);
                }
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



    // Export functions
    function exportToExcel() {
        const url = new URL('{{ route('external-assignment.summary-report-export-excel') }}');

        // Add current filters to URL
        const dateFilter = document.getElementById('date_filter');
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        const ea_type_id = document.getElementById('ea_type_id');
        const status = document.getElementById('status');

        if (dateFilter && dateFilter.value && dateFilter.value !== 'custom') {
            url.searchParams.append('date_filter', dateFilter.value);
        }
        if (startDate && startDate.value) {
            url.searchParams.append('start_date', startDate.value);
        }
        if (endDate && endDate.value) {
            url.searchParams.append('end_date', endDate.value);
        }
        if (ea_type_id && ea_type_id.value) {
            url.searchParams.append('ea_type_id', ea_type_id.value);
        }
        if (status && status.value) {
            url.searchParams.append('status', status.value);
        }

        console.log('Export Excel URL:', url.toString());

        // Create download link for Excel
        const link = document.createElement('a');
        link.href = url.toString();
        link.download = 'external_assigment_summary_export.xlsx';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }



    // Robust DataTable initialization with retry mechanism
    function initDataTable() {
        if (typeof $.fn.DataTable === 'undefined') {
            console.log('DataTable not available, retrying in 100ms...');
            setTimeout(initDataTable, 100);
            return;
        }

        try {
            console.log('Initializing external assignment summary report DataTable...');

            const columns = [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'nip',
                    name: 'requester.u_nip'
                },
                {
                    data: 'requester',
                    name: 'requester.u_name'
                },
                {
                    data: 'division',
                    name: 'division.ud_name'
                },
                {
                    data: 'assignment_type',
                    name: 'eat.ea_name'
                },
                {
                    data: 'start',
                    name: 'ear.ear_date_start',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString('en-GB') : '-';
                    }
                },
                {
                    data: 'end',
                    name: 'ear.ear_date_end',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString('en-GB') : '-';
                    }
                },
                {
                    data: 'location',
                    name: 'ear.ear_locations'
                },
                {
                    data: 'ear_cash_advance',
                    name: 'ear.ear_cash_advance',
                    className: 'text-right',
                    render: function(data) {
                        return data ? 'Rp ' + parseFloat(data).toLocaleString('id-ID') : '-';
                    }
                },
                {
                    data: 'cash_detail_sum',
                    name: 'cash_detail_sum',
                    className: 'text-right',
                    render: function(data) {
                        return data ? 'Rp ' + parseFloat(data).toLocaleString('id-ID') : '-';
                    }
                },
                {
                    data: 'report_cash_sum',
                    name: 'report_cash_sum',
                    className: 'text-right',
                    render: function(data) {
                        return data ? 'Rp ' + parseFloat(data).toLocaleString('id-ID') : '-';
                    }
                },
                {
                    data: 'approver',
                    name: 'approver.u_name'
                },
                {
                    data: 'hr',
                    name: 'hr_checker.u_name'
                },
                {
                    data: 'finance',
                    name: 'finance_checker.u_name'
                },
                {
                    data: 'file_url',
                    name: 'file_url',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return data ? '<a href="' + data +
                            '" target="_blank" class="btn btn-sm btn-info">View File</a>' : '-';
                    }
                },
                {
                    data: 'ear_status',
                    name: 'ear.ear_status',
                    className: 'text-center',
                    render: function(data) {
                        const statusMap = {
                            'Pending': 'badge badge-primary',
                            'Approved': 'badge bg-success',
                            'Rejected': 'badge bg-danger',
                            'Reporting': 'badge bg-info',
                            'HR Check': 'badge bg-warning',
                            'Finance Process': 'badge bg-secondary',
                            'DONE': 'badge bg-success'
                        };
                        const badgeClass = statusMap[data] || 'badge badge-light';
                        return '<span class="' + badgeClass + '">' + data + '</span>';
                    }
                }
            ];

            window.summaryTable = $('#summaryTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('external-assignment.summary-report-datatables') }}",
                    data: function(d) {
                        d.date_filter = $('#date_filter').val();
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.user_id = $('#user_id').val();
                        d.ea_type_id = $('#ea_type_id').val();
                        d.status = $('#status').val();
                        d.search = $('#summary_search').val();
                    }
                },
                columns: columns,
                order: [
                    [1, 'asc']
                ],
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                language: {
                    processing: "Loading data..."
                }
            });

            console.log('External Assignment Summary DataTable initialized successfully');

            $('#start_date, #end_date, #user_id, #ea_type_id, #status').on('change', function() {
                window.summaryTable.draw();
            });

        } catch (error) {
            console.error('Error initializing External Assignment Summary Report DataTable:', error);
            // Retry after a delay
            setTimeout(initDataTable, 500);
        }
    }



    // Initialize when document is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Add form submit event listener
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
                if (window.summaryTable) {
                    window.summaryTable.ajax.reload();
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

        // Initialize DataTable
        initDataTable();

        // Search functionality for summary_search input with debounce
        var searchTimeout;
        $('#summary_search').on('keyup input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                if (window.summaryTable) {
                    window.summaryTable.draw();
                }
            }, 300);
        });
    });
</script>
