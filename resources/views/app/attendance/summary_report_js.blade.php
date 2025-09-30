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
                case 'this_month':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1); // First day of month
                    endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0); // Last day of month
                    break;
                case 'last_month':
                    startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1); // First day of last month
                    endDate = new Date(today.getFullYear(), today.getMonth(), 0); // Last day of last month
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
            }
        }
    }

    // Export functions - using same method as index attendance
    function exportToExcel() {
        const url = new URL('{{ route('attendance.summary-report-export-excel') }}');

        // Add current filters to URL
        const dateFilter = document.getElementById('date_filter').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const divisionId = document.getElementById('division_id').value;
        const search = document.getElementById('search').value;

        if (dateFilter && dateFilter !== 'custom') {
            url.searchParams.append('date_filter', dateFilter);
        }
        if (startDate) {
            url.searchParams.append('start_date', startDate);
        }
        if (endDate) {
            url.searchParams.append('end_date', endDate);
        }
        if (divisionId) {
            url.searchParams.append('division_id', divisionId);
        }
        if (search) {
            url.searchParams.append('search', search);
        }

        console.log('Export Excel URL:', url.toString());

        // Create download link
        const link = document.createElement('a');
        link.href = url.toString();
        link.download = 'attendance_summary_export.xlsx';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function exportToPDF() {
        const url = new URL('{{ route('attendance.summary-report-export-pdf') }}');

        // Add current filters to URL
        const dateFilter = document.getElementById('date_filter').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const divisionId = document.getElementById('division_id').value;
        const search = document.getElementById('search').value;

        if (dateFilter && dateFilter !== 'custom') {
            url.searchParams.append('date_filter', dateFilter);
        }
        if (startDate) {
            url.searchParams.append('start_date', startDate);
        }
        if (endDate) {
            url.searchParams.append('end_date', endDate);
        }
        if (divisionId) {
            url.searchParams.append('division_id', divisionId);
        }
        if (search) {
            url.searchParams.append('search', search);
        }

        console.log('Export PDF URL:', url.toString());

        // Create download link
        const link = document.createElement('a');
        link.href = url.toString();
        link.download = 'attendance_summary_export.pdf';
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
            console.log('Initializing summary report DataTable...');

            window.summaryTable = $('#summaryTable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                responsive: false, // Set to false to prevent conflicts
                dom: 'rt<"pagination-class"ip>',
                ajax: {
                    url: "{{ route('attendance.summary-report-datatables') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.division_id = $('#division_id').val();
                        d.search = $('#search').val();

                        console.log('Summary Report AJAX Data sent:', d);
                    },
                    dataSrc: function(json) {
                        console.log('Summary Report DataTables response:', json);
                        return json.data || [];
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
                        data: 'u_nip',
                        name: 'u_nip',
                        width: '10%'
                    },
                    {
                        data: 'u_name',
                        name: 'u_name',
                        width: '15%',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return '<a href="/attendance/staff/' + row.user_id +
                                    '" class="text-primary font-weight-bold" style="cursor: pointer;line-height: 1.2;">' +
                                    data +
                                    '</a><br><span class="text-muted" style="line-height: 1.8;>' + (row
                                        .u_nip || '-') + '</span>';
                            }
                            return data;
                        }
                    },
                    {
                        data: 'position_name',
                        name: 'position_name',
                        width: '12%'
                    },
                    {
                        data: 'division_name',
                        name: 'division_name',
                        width: '12%'
                    },
                    {
                        data: 'work_type',
                        name: 'work_type',
                        width: '10%',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                let badgeClass = 'secondary';
                                if (data === 'Full Time') badgeClass = 'primary';
                                else if (data === 'Part Time') badgeClass = 'warning';
                                return '<span class="badge bg-' + badgeClass + '">' + (data || '-') +
                                    '</span>';
                            }
                            return data;
                        }
                    },
                    {
                        data: 'total_shifts',
                        name: 'total_shifts',
                        width: '8%',
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return data && data > 0 ? data : '-';
                            }
                            return data || 0;
                        }
                    },
                    {
                        data: 'present_days',
                        name: 'present_days',
                        width: '8%',
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return data && data > 0 ? data : '-';
                            }
                            return data || 0;
                        }
                    },
                    {
                        data: 'total_libur',
                        name: 'total_libur',
                        width: '8%',
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return data && data > 0 ? data : '-';
                            }
                            return data || 0;
                        }
                    },
                    {
                        data: 'leave_days',
                        name: 'leave_days',
                        width: '10%',
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return data && data !== "0 days 0 hours" ? data : '-';
                            }
                            return data;
                        }
                    },
                    {
                        data: 'sick_days',
                        name: 'sick_days',
                        width: '8%',
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return data && data > 0 ? data : '-';
                            }
                            return data || 0;
                        }
                    },
                    {
                        data: 'late_days',
                        name: 'late_days',
                        width: '8%',
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return data && data > 0 ? data : '-';
                            }
                            return data || 0;
                        }
                    },
                    {
                        data: 'scan_once_days',
                        name: 'scan_once_days',
                        width: '8%',
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return data && data > 0 ? data : '-';
                            }
                            return data || 0;
                        }
                    },
                    {
                        data: 'alpha_days',
                        name: 'alpha_days',
                        width: '8%',
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return data && data > 0 ? data : '-';
                            }
                            return data || 0;
                        }
                    }
                ],
                columnDefs: [{
                        "targets": 0,
                        "className": "text-center",
                        "width": "5%"
                    },
                    {
                        "targets": [6, 7, 8, 9, 10, 11, 12, 13],
                        "className": "text-center"
                    }
                ],
                order: [
                    [2, 'asc']
                ], // Sort by name column
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "Semua"]
                ],
                language: {
                    // Use English language to avoid CORS issues
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

            console.log('Summary Report DataTable initialized successfully');

        } catch (error) {
            console.error('Error initializing Summary Report DataTable:', error);
            // Retry after a delay
            setTimeout(initDataTable, 500);
        }
    }

    // Initialize when document is ready
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Initialize date filter on page load
        console.log('Document ready - Initializing date filter...');
        const dateFilter = document.getElementById('date_filter');
        if (dateFilter) {
            console.log('Date filter found, current value:', dateFilter.value);
            handleDateFilterChange(dateFilter.value);
        } else {
            console.log('Date filter not found!');
        }

        // Initialize DataTable
        initDataTable();
    });

    // Search functionality
    $('#search').on('keyup', function() {
        if (window.summaryTable) {
            window.summaryTable.search(this.value).draw();
        } else {
            $('#summaryTable').DataTable().search(this.value).draw();
        }
    });
</script>
