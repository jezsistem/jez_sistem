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
                
                // Update statistics when date filter changes
                updateStatistics();
            }
        }
    }

    // Function to update breadcrumb dates
    function updateBreadcrumbDates(startDate, endDate, dateFilter) {
        const breadcrumbElement = document.querySelector('.breadcrumb-item:last-child .text-muted');
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

    // Function to update statistics
    function updateStatistics() {
        const dateFilter = document.getElementById('date_filter').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const divisionId = document.getElementById('division_id').value;
        
        // Show loading state
        const statsContainer = document.getElementById('statsContainer');
        if (statsContainer) {
            statsContainer.style.opacity = '0.6';
        }
        
        // Fetch updated statistics
        fetch('/break-times-backup/summary-report/stats?' + new URLSearchParams({
            date_filter: dateFilter,
            start_date: startDate,
            end_date: endDate,
            division_id: divisionId
        }))
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error fetching statistics:', data.error);
                return;
            }
            
            // Update statistics display
            updateStatsDisplay(data);
            
            // Restore opacity
            if (statsContainer) {
                statsContainer.style.opacity = '1';
            }
        })
        .catch(error => {
            console.error('Error updating statistics:', error);
            // Restore opacity on error
            if (statsContainer) {
                statsContainer.style.opacity = '1';
            }
        });
    }

    // Function to update statistics display
    function updateStatsDisplay(stats) {
        // Update each statistic
        const totalStaff = document.getElementById('stat-total-staff');
        const totalShifts = document.getElementById('stat-total-shifts');
        const totalBreaks = document.getElementById('stat-total-breaks');
        const noBreakShifts = document.getElementById('stat-no-break-shifts');
        const avgBreaksPerStaff = document.getElementById('stat-avg-breaks-per-staff');
        
        if (totalStaff) totalStaff.textContent = stats.total_staff || 0;
        if (totalShifts) totalShifts.textContent = stats.total_shifts || 0;
        if (totalBreaks) totalBreaks.textContent = stats.total_breaks || 0;
        if (noBreakShifts) noBreakShifts.textContent = stats.no_break_shifts || 0;
        if (avgBreaksPerStaff) avgBreaksPerStaff.textContent = stats.avg_breaks_per_staff || 0;
    }

    // Export functions - using same method as index attendance
    function exportToExcel() {
        const url = new URL('{{ route("break-times-backup.summary-report-export-excel") }}');
        
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
        link.download = 'break_time_summary_export.xlsx';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function exportToPDF() {
        const url = new URL('{{ route("break-times-backup.summary-report-export-pdf") }}');
        
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
        link.download = 'break_time_summary_export.pdf';
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
            console.log('Initializing break time summary report DataTable...');
            
            window.summaryTable = $('#summaryTable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                responsive: false, // Set to false to prevent conflicts
                dom: 'rt<"pagination-class"ip>',
                ajax: {
                    url: "{{ route('break-times-backup.summary-report-datatables') }}",
                    data: function (d) {
                        d.date_filter = $('#date_filter').val();
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.division_id = $('#division_id').val();
                        d.search = $('#search').val();
                        
                        console.log('Break Time Summary Report AJAX Data sent:', d);
                    },
                    dataSrc: function(json) {
                        console.log('Break Time Summary Report DataTables response:', json);
                        return json.data || [];
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%' },
                    { data: 'u_nip', name: 'u_nip', width: '10%' },
                    { 
                        data: 'u_name', 
                        name: 'u_name', 
                        width: '15%',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return '<a href="/break-times-backup/staff/' + row.user_id + '" class="text-primary font-weight-bold" style="cursor: pointer;line-height: 1.2;">' + data + '</a><br><span class="text-muted" style="line-height: 1.8;>' + (row.u_nip || '-') + '</span>';
                            }
                            return data;
                        }
                    },
                    { data: 'position_name', name: 'position_name', width: '12%' },
                    { data: 'division_name', name: 'division_name', width: '12%' },
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
                            return data;
                        }
                    },
                    { data: 'total_breaks', name: 'total_breaks', width: '10%', className: 'text-center' },
                    { data: 'no_break_shifts', name: 'no_break_shifts', width: '20%', className: 'text-center' },

                ],
                columnDefs: [
                    {
                        "targets": 0,
                        "className": "text-center",
                        "width": "5%"
                    },
                    {
                        "targets": [6, 7],
                        "className": "text-center"
                    }
                ],
                order: [[2, 'asc']], // Sort by name column
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
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

            console.log('Break Time Summary Report DataTable initialized successfully');
            
        } catch (error) {
            console.error('Error initializing Break Time Summary Report DataTable:', error);
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
                
                // Update statistics
                updateStatistics();
                
                // Reload DataTable
                if (window.summaryTable) {
                    window.summaryTable.ajax.reload();
                }
            });
        }
        
        // Initialize breadcrumb with current values
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        
        if (dateFilter && dateFilter.value !== 'custom') {
            updateBreadcrumbDates(startDate, endDate, dateFilter.value);
        }
        
        // Initialize statistics on page load
        updateStatistics();
        
        // Add event listeners for other filters
        const divisionSelect = document.getElementById('division_id');
        const searchInput = document.getElementById('search');
        
        if (divisionSelect) {
            divisionSelect.addEventListener('change', function() {
                updateStatistics();
            });
        }
        
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                // Debounce search to avoid too many API calls
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    updateStatistics();
                }, 500);
            });
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
