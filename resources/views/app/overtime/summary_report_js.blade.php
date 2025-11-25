<script>
    // Global reference for DataTable
    window.summaryTable = null;

    function exportSummaryToExcel() {
        // Implement the export functionality here
        let start_date = $('#start_date').val();
        let end_date = $('#end_date').val();
        let division = $('#division').val();
        let staff = $('#staff').val();

        $.ajax({
            url: "{{ url('/overtime/summary-report/export/excel') }}",
            type: "GET",
            data: {
                start_date: start_date,
                end_date: end_date,
                division: division,
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
                link.download = filename || 'overtime_summary.xlsx';
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

    // Robust DataTable initialization with retry mechanism
    function initDataTable() {
        if (typeof $.fn.DataTable === 'undefined') {
            console.log('DataTable not available, retrying in 100ms...');
            setTimeout(initDataTable, 100);
            return;
        }

        try {
            console.log('Initializing Overtime Summary DataTable...');

            // Build columns configuration
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
                }
            ];

            // Add dynamic columns for overtime types
            @foreach ($data['overtime_types'] as $item)
            columns.push({
                data: 'overtime_type_{{ $item->id }}',
                name: 'overtime_type_{{ $item->id }}',
                width: '10%',
                className: 'text-center',
                render: function(data, type, row) {
                    if (type === 'display') {
                        return data ? parseFloat(data).toFixed(2) : '0.00';
                    }
                    return data || 0;
                }
            });
            @endforeach

            // Add Fee Lembur column
            columns.push({
                data: 'overtime_fee',
                name: 'overtime_fee',
                width: '12%',
                className: 'text-end',
                render: function(data, type, row) {
                    if (type === 'display') {
                        return data ? 'Rp ' + parseFloat(data).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : 'Rp 0';
                    }
                    return data || 0;
                }
            });

            window.summaryTable = $('#overtimeSummaryTable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                responsive: false,
                ajax: {
                    url: "{{ url('overtime/summary-report/datatables') }}",
                    type: 'GET',
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.date_filter = $('#date_filter').val();
                        d.division = $('#division').val();
                        d.staff = $('#staff').val();
                        
                        console.log('Overtime Summary DataTable ajax request data:', d);
                    },
                    dataSrc: function(json) {
                        console.log('Overtime Summary DataTable response:', json);
                        console.log('Data rows:', json.data);

                        // Log each row to see the structure
                        if (json.data && json.data.length > 0) {
                            console.log('First row structure:', json.data[0]);
                        }

                        return json.data || [];
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTable ajax error:', error, thrown);
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
                order: [[2, 'asc']], // Sort by staff name by default
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
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

            console.log('Overtime Summary DataTable initialized successfully');

            // Handle filter changes
            $('#date_filter').on('change', function() {
                handleDateFilterChange($(this).val());
            });

            $('#apply_filter').on('click', function() {
                console.log('Apply filter button clicked, reloading DataTable...');
                window.summaryTable.ajax.reload();
            });

            $('#division, #staff').on('change', function() {
                console.log('Filter changed, reloading DataTable...');
                window.summaryTable.ajax.reload();
            });

            // Handle custom date filter
            $('#start_date, #end_date').on('change', function() {
                if ($('#date_filter').val() === 'custom') {
                    const startDate = $('#start_date').val();
                    const endDate = $('#end_date').val();
                    
                    if (startDate && endDate) {
                        updateBreadcrumbDates(startDate, endDate, 'custom');
                        console.log('Custom date filter applied, reloading DataTable...');
                        window.summaryTable.ajax.reload();
                    }
                }
            });

        } catch (error) {
            console.error('Error initializing Overtime Summary DataTable:', error);
            // Retry after a delay
            setTimeout(initDataTable, 500);
        }
    }

    // Initialize DataTable when document is ready
    $(document).ready(function() {
        initDataTable();
    });

</script>
