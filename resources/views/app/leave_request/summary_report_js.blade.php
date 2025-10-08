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
            const leaveTypes = @json($data['leaveTypes']);

            // Build columns configuration dynamically
            const columns = [
                {
                    data: null,
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    width: '5%',
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'u_nip',
                    name: 'u_nip',
                    width: '10%',
                    render: function(data, type, row) {
                        return data || '-';
                    }
                },
                {
                    data: 'u_name',
                    name: 'u_name',
                    width: '15%',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="{{ route("leave-requests.staff-detail", "") }}/' + row.user_id + '" class="text-primary font-weight-bold" style="cursor: pointer;line-height: 1.2;">' + (data || '-') + '</a><br><span class="text-muted" style="line-height: 1.8;>' + (row.u_nip || '-') + '</span>';
                        }
                        return data || '-';
                    }
                },
                {
                    data: 'position_name',
                    name: 'position_name',
                    width: '12%',
                    render: function(data, type, row) {
                        return data || '-';
                    }
                },
                {
                    data: 'division_name',
                    name: 'division_name',
                    width: '12%',
                    render: function(data, type, row) {
                        return data || '-';
                    }
                },
                {
                    data: 'work_type',
                    name: 'work_type',
                    width: '15%',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            let badgeClass = 'secondary';
                            if (data === 'Full Time') badgeClass = 'primary';
                            else if (data === 'Part Time') badgeClass = 'warning';
                            return '<span class="badge bg-' + badgeClass + '">' + (data || '-') + '</span>';
                        }
                        return data || '-';
                    }
                }
            ];

            // Add dynamic leave type columns
            leaveTypes.forEach(function(leaveType) {
                const columnName = 'leave_' + leaveType.lt_code.toLowerCase();
                columns.push({
                    data: columnName,
                    name: columnName,
                    width: '8%',
                    className: 'text-center',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return data && data > 0 ? data : '-';
                        }
                        return data || 0;
                    }
                });
            });

            // Add total and balance columns
            columns.push(
                {
                    data: 'total_leave_requests',
                    name: 'total_leave_requests',
                    width: '8%',
                    className: 'text-center',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            const totalDays = row.total_days || 0;
                            const totalHours = row.total_hours || 0;
                            const total = totalDays + totalHours;
                            return total > 0 ? total : '-';
                        }
                        return (row.total_days || 0) + (row.total_hours || 0);
                    }
                },
                {
                    data: 'annual_leave_balance',
                    name: 'annual_leave_balance',
                    width: '8%',
                    className: 'text-center',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return data && data > 0 ? data : '-';
                        }
                        return data || 0;
                    }
                }
            );

            window.summaryTable = $('#summaryTable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                responsive: false,
                dom: 'rt<"pagination-class"ip>',
                ajax: {
                    url: "{{ route('leave-requests.summary-report-datatables') }}",
                    data: function (d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.date_filter = $('#date_filter').val();
                        d.division_id = $('#division_id').val();
                        d.search = $('#summary_search').val();

                        console.log('Leave Summary Report AJAX Data sent:', d);
                    },
                    dataSrc: function(json) {
                        console.log('Leave Summary Report DataTables response:', json);
                        console.log('Data rows:', json.data);

                        // Log each row to see the structure
                        if (json.data && json.data.length > 0) {
                            console.log('First row structure:', json.data[0]);
                        }

                        return json.data || [];
                    }
                },
                columns: columns,
                columnDefs: [
                    {
                        "targets": 0,
                        "className": "text-center",
                        "width": "5%"
                    }
                ],
                order: [[2, 'asc']], // Sort by name column
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                language: {
                    "emptyTable": "No data available in table",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                    "infoFiltered": "(filtered from _MAX_ total entries)",
                    "lengthMenu": "Show _MENU_ entries",
                    "loadingRecords": "Loading...",
                    "processing": "Processing...",
                    "search": "Search:",
                    "zeroRecords": "No matching records found"
                }
            });

            console.log('Leave Summary Report DataTable initialized successfully');

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
