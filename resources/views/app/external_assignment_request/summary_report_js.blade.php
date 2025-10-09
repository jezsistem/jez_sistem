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



    // Export functions
    function exportToExcel() {
        const url = new URL('{{ route("leave-requests.summary-report-export-excel") }}');

        // Add current filters to URL
        const dateFilter = document.getElementById('date_filter');
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        const divisionId = document.getElementById('division_id');
        const summarySearch = document.getElementById('summary_search');

        if (dateFilter && dateFilter.value && dateFilter.value !== 'custom') {
            url.searchParams.append('date_filter', dateFilter.value);
        }
        if (startDate && startDate.value) {
            url.searchParams.append('start_date', startDate.value);
        }
        if (endDate && endDate.value) {
            url.searchParams.append('end_date', endDate.value);
        }
        if (divisionId && divisionId.value) {
            url.searchParams.append('division_id', divisionId.value);
        }
        if (summarySearch && summarySearch.value) {
            url.searchParams.append('search', summarySearch.value);
        }

        console.log('Export Excel URL:', url.toString());

        // Create download link for Excel
        const link = document.createElement('a');
        link.href = url.toString();
        link.download = 'leave_summary_export.xlsx';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function exportToPDF() {
        const url = new URL('{{ route("leave-requests.summary-report-export-pdf") }}');

        // Add current filters to URL
        const dateFilter = document.getElementById('date_filter');
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        const divisionId = document.getElementById('division_id');
        const summarySearch = document.getElementById('summary_search');

        if (dateFilter && dateFilter.value && dateFilter.value !== 'custom') {
            url.searchParams.append('date_filter', dateFilter.value);
        }
        if (startDate && startDate.value) {
            url.searchParams.append('start_date', startDate.value);
        }
        if (endDate && endDate.value) {
            url.searchParams.append('end_date', endDate.value);
        }
        if (divisionId && divisionId.value) {
            url.searchParams.append('division_id', divisionId.value);
        }
        if (summarySearch && summarySearch.value) {
            url.searchParams.append('search', summarySearch.value);
        }

        console.log('Export PDF URL:', url.toString());

        // Create download link for PDF
        const link = document.createElement('a');
        link.href = url.toString();
        link.download = 'leave_summary_export.pdf';
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
            console.log('Initializing leave summary report DataTable...');

            // Get leave types for dynamic columns
            const statuses = [
                'Pending Approval',
                'Approved',
                'Rejected',
                'Reporting',
                'HR Check',
                'Finance Process',
                'DONE'
            ];

            const columns = [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'u_nip', name: 'u_nip' },
                { data: 'u_name', name: 'u_name' },
                { data: 'position_name', name: 'position_name' },
                { data: 'division_name', name: 'division_name' },
                { data: 'work_type', name: 'work_type' }
            ];

// Tambah kolom per status
            statuses.forEach(status => {
                const colKey = 'status_' + status.toLowerCase().replace(/\s+/g, '_');
                columns.push({
                    data: colKey,
                    name: colKey,
                    className: 'text-center',
                    render: function(data) {
                        return data > 0 ? data : '-';
                    }
                });
            });

// Tambah total kolom
            columns.push({
                data: 'total_requests',
                name: 'total_requests',
                className: 'text-center',
                render: function(data) {
                    return data > 0 ? data : '-';
                }
            });

            $('#summaryTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('external-assignment.summary-report-datatables') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.division_id = $('#division_id').val();
                        d.search = $('#summary_search').val();
                    }
                },
                columns: columns,
                order: [[2, 'asc']]
            });

        } catch (error) {
            console.error('Error initializing Leave Summary Report DataTable:', error);
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
