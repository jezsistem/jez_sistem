<script>
    // Global reference for DataTable
    window.staffTable = null;

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
            }
        }
    }

    // Export functions
    function exportToExcel() {
        const url = new URL('{{ route("break-times-backup.staff-export-excel", $data["staff"]->id) }}');
        
        // Add current filters to URL
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const status = document.getElementById('status').value;
        
        if (startDate) {
            url.searchParams.append('start_date', startDate);
        }
        if (endDate) {
            url.searchParams.append('end_date', endDate);
        }
        if (status) {
            url.searchParams.append('status', status);
        }
        
        console.log('Export Excel URL:', url.toString());
        
        // Create download link
        const link = document.createElement('a');
        link.href = url.toString();
                    link.download = 'backup_time_staff_{{ $data["staff"]->u_nip }}_export.xlsx';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function exportToPDF() {
        const url = new URL('{{ route("break-times-backup.staff-export-pdf", $data["staff"]->id) }}');
        
        // Add current filters to URL
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const status = document.getElementById('status').value;
        
        if (startDate) {
            url.searchParams.append('start_date', startDate);
        }
        if (endDate) {
            url.searchParams.append('end_date', endDate);
        }
        if (status) {
            url.searchParams.append('status', status);
        }
        
        console.log('Export PDF URL:', url.toString());
        
        // Create download link
        const link = document.createElement('a');
        link.href = url.toString();
                    link.download = 'backup_time_staff_{{ $data["staff"]->u_nip }}_export.pdf';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Load staff statistics
    function loadStaffStats() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const status = document.getElementById('status').value;
        
        let url = '{{ route("break-times-backup.staff-stats", $data["staff"]->id) }}?start_date=' + startDate + '&end_date=' + endDate;
        if (status) {
            url += '&status=' + status;
        }
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                console.log('Staff stats loaded:', data);
                
                // Update statistics cards
                document.getElementById('totalBreaks').textContent = data.break_stats.total_breaks || 0;
                document.getElementById('totalShifts').textContent = data.shift_stats.total_shifts || 0;
                document.getElementById('exceededBreaks').textContent = data.break_stats.exceeded_breaks || 0;
                
                // Update break allowance info
                if (data.break_stats.shift_type && data.break_stats.max_duration) {
                    const shiftType = data.break_stats.shift_type;
                    const maxDuration = data.break_stats.max_duration;
                    document.getElementById('breakAllowanceInfo').textContent = 
                        `${shiftType} - Max: ${maxDuration}m`;
                } else {
                    document.getElementById('breakAllowanceInfo').textContent = 'Part Time - Max: 60m';
                }
                
                // Format average duration
                const avgDuration = data.break_stats.avg_duration || 0;
                if (avgDuration > 0) {
                    const hours = Math.floor(avgDuration / 60);
                    const minutes = avgDuration % 60;
                    document.getElementById('avgDuration').textContent = 
                        (hours > 0 ? hours + 'h ' : '') + minutes + 'm';
                } else {
                    document.getElementById('avgDuration').textContent = '0m';
                }
            })
            .catch(error => {
                console.error('Error loading staff stats:', error);
            });
    }

    // Robust DataTable initialization with retry mechanism
    function initDataTable() {
        if (typeof $.fn.DataTable === 'undefined') {
            console.log('DataTable not available, retrying in 100ms...');
            setTimeout(initDataTable, 100);
            return;
        }

        try {
            console.log('Initializing staff break time DataTable...');
            
            window.staffTable = $('#staffTable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                responsive: false,
                dom: 'rt<"pagination-class"ip>',
                ajax: {
                    url: "{{ route('break-times-backup.staff-datatables', $data['staff']->id) }}",
                    data: function (d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.status = $('#status').val();
                        
                        console.log('Staff Datatables AJAX Data sent:', d);
                    },
                    dataSrc: function(json) {
                        console.log('Staff Datatables response:', json);
                        
                        // Log sample data for debugging
                        if (json.data && json.data.length > 0) {
                            console.log('Sample data row:', json.data[0]);
                            console.log('Duration field sample:', json.data[0].bt_duration_minutes);
                        }
                        
                        return json.data || [];
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%' },
                    { 
                        data: 'bt_date', 
                        name: 'bt_date', 
                        width: '10%',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return moment(data).format('DD/MM/YYYY');
                            }
                            return data;
                        }
                    },
                    { 
                        data: 'bt_type', 
                        name: 'bt_type', 
                        width: '8%',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                // Convert break_1, break_2, break_3, etc. to backup 1, backup 2, backup 3, etc.
                                const breakType = data.replace('break_', 'backup ');
                                return '<span class="badge badge-light-green">' + breakType + '</span>';
                            }
                            return data;
                        }
                    },
                    { 
                        data: 'bt_start_time', 
                        name: 'bt_start_time', 
                        width: '8%',
                        render: function(data, type, row) {
                            if (type === 'display' && data) {
                                return moment(data, 'HH:mm:ss').format('HH:mm');
                            }
                            return data || '-';
                        }
                    },
                    { 
                        data: 'bt_end_time', 
                        name: 'bt_end_time', 
                        width: '8%',
                        render: function(data, type, row) {
                            if (type === 'display' && data) {
                                return moment(data, 'HH:mm:ss').format('HH:mm');
                            }
                            return data || '-';
                        }
                    },
                    { 
                        data: 'bt_duration_minutes', 
                        name: 'bt_duration_minutes', 
                        width: '8%',
                        render: function(data, type, row) {
                            console.log('Duration column render:', { data, type, row });
                            
                            if (type === 'display') {
                                if (data && data > 0) {
                                    const hours = Math.floor(data / 60);
                                    const minutes = data % 60;
                                    const result = (hours > 0 ? hours + 'h ' : '') + minutes + 'm';
                                    console.log('Formatted duration:', { data, hours, minutes, result });
                                    return result;
                                } else if (data === 0) {
                                    // Try to calculate from start and end time if duration is 0
                                    const startTime = row.bt_start_time;
                                    const endTime = row.bt_end_time;
                                    
                                    if (startTime && endTime) {
                                        try {
                                            const start = moment(startTime, 'HH:mm:ss');
                                            const end = moment(endTime, 'HH:mm:ss');
                                            if (start.isValid() && end.isValid()) {
                                                const durationMinutes = end.diff(start, 'minutes');
                                                if (durationMinutes > 0) {
                                                    const hours = Math.floor(durationMinutes / 60);
                                                    const minutes = durationMinutes % 60;
                                                    const calculatedResult = (hours > 0 ? hours + 'h ' : '') + minutes + 'm';
                                                    console.log('Calculated duration from times (was 0):', { startTime, endTime, durationMinutes, calculatedResult });
                                                    return calculatedResult + ' (calc)';
                                                }
                                            }
                                        } catch (e) {
                                            console.error('Error calculating duration from times:', e);
                                        }
                                    }
                                    
                                    return '0m';
                                } else {
                                    // Try to calculate from start and end time if duration is missing
                                    const startTime = row.bt_start_time;
                                    const endTime = row.bt_end_time;
                                    
                                    if (startTime && endTime) {
                                        try {
                                            const start = moment(startTime, 'HH:mm:ss');
                                            const end = moment(endTime, 'HH:mm:ss');
                                            if (start.isValid() && end.isValid()) {
                                                const durationMinutes = end.diff(start, 'minutes');
                                                if (durationMinutes > 0) {
                                                    const hours = Math.floor(durationMinutes / 60);
                                                    const minutes = durationMinutes % 60;
                                                    const calculatedResult = (hours > 0 ? hours + 'h ' : '') + minutes + 'm';
                                                    console.log('Calculated duration from times (was null):', { startTime, endTime, durationMinutes, calculatedResult });
                                                    return calculatedResult + ' (calc)';
                                                }
                                            }
                                        } catch (e) {
                                            console.error('Error calculating duration from times:', e);
                                        }
                                    }
                                    
                                    return '-';
                                }
                            }
                            return data;
                        }
                    },
                    { 
                        data: 'bt_status', 
                        name: 'bt_status', 
                        width: '8%',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                let badgeClass = 'secondary';
                                if (data === 'completed') badgeClass = 'light-yellow';
                                else if (data === 'active') badgeClass = 'light-blue';
                                else if (data === 'cancelled') badgeClass = 'light-red';
                                return '<span class="badge badge-' + badgeClass + '">' + (data ? data.charAt(0).toUpperCase() + data.slice(1) : '-') + '</span>';
                            }
                            return data;
                        }
                    },
                    { 
                        data: 'shift_start', 
                        name: 'shift_start', 
                        width: '8%',
                        render: function(data, type, row) {
                            if (type === 'display' && data) {
                                return moment(data, 'HH:mm:ss').format('HH:mm');
                            }
                            return data || '-';
                        }
                    },
                    { 
                        data: 'shift_end', 
                        name: 'shift_end', 
                        width: '8%',
                        render: function(data, type, row) {
                            if (type === 'display' && data) {
                                return moment(data, 'HH:mm:ss').format('HH:mm');
                            }
                            return data || '-';
                        }
                    },
                    { 
                        data: 'bt_notes', 
                        name: 'bt_notes', 
                        width: '20%',
                        render: function(data, type, row) {
                            if (type === 'display' && data) {
                                // Check if notes contain exceeded warning
                                if (data.includes('⚠️ EXCEEDED:')) {
                                    return '<div class="text-danger"><i class="fas fa-exclamation-triangle"></i> ' + data + '</div>';
                                }
                                return data;
                            }
                            return data || '-';
                        }
                    }
                ],
                columnDefs: [
                    {
                        "targets": 0,
                        "className": "text-center",
                        "width": "5%"
                    },
                    {
                        "targets": [2, 3, 4, 5, 6, 7, 8],
                        "className": "text-center"
                    }
                ],
                order: [[1, 'desc'], [3, 'desc']], // Sort by date desc, then start time desc
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

            console.log('Staff DataTable initialized successfully');
            
        } catch (error) {
            console.error('Error initializing Staff DataTable:', error);
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
        
        // Load initial statistics
        loadStaffStats();
        
        // Handle filter form submission
        $('#filterForm').on('submit', function(e) {
            e.preventDefault(); // Prevent form submission
            
            // Reload DataTable and stats
            if (window.staffTable) {
                window.staffTable.ajax.reload();
            }
            loadStaffStats();
        });
    });

    // Search functionality
    $('#search').on('keyup', function() {
        if (window.staffTable) {
            window.staffTable.search(this.value).draw();
        } else {
            $('#staffTable').DataTable().search(this.value).draw();
        }
    });
</script>
