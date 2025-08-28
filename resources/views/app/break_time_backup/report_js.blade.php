<script>
    // Global reference for DataTable
    window.breakTimeTable = null;

    // Robust DataTable initialization with retry mechanism
    function initDataTable() {
        if (typeof $.fn.DataTable === 'undefined') {
            console.log('DataTable not available, retrying in 100ms...');
            setTimeout(initDataTable, 100);
            return;
        }

        try {
            console.log('Initializing break time report DataTable...');
            
            window.breakTimeTable = $('#breakTimeTable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                responsive: false, // Set to false to prevent conflicts
                scrollY: false, // Disable vertical scroll
                scrollCollapse: false, // Disable scroll collapse
                dom: 'rt<"pagination-class"ip>',
                ajax: {
                    url : "{{ url('break-times-backup/datatables') }}",
                    data : function (d) {
                        d.search = $('#break_time_search').val();
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.date_filter = $('#date_filter').val();
                        d.user_id = $('#user_id').val();
                        d.division_id = $('#division_id').val();
                        d.status = $('#status').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%' },
                    { data: 'bt_date', name: 'bt_date', width: '10%' },
                    { data: 'u_name', name: 'u_name', width: '18%' },
                    { data: 'u_nip', name: 'u_nip', width: '12%' },
                    { data: 'ud_name', name: 'ud_name', width: '15%' },
                    { data: 'bt_type', name: 'bt_type', width: '10%' },
                    { data: 'bt_start_time', name: 'bt_start_time', width: '10%' },
                    { data: 'bt_end_time', name: 'bt_end_time', width: '10%' },
                    { data: 'bt_duration_minutes', name: 'bt_duration_minutes', width: '10%' },
                    { data: 'bt_status', name: 'bt_status', width: '12%' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, width: '8%' },
                ],
                columnDefs: [
                    {
                        "targets": 0,
                        "className": "text-center",
                        "width": "5%"
                    },
                    {
                        "targets": [1, 6, 7, 8],
                        "className": "text-center"
                    },
                    {
                        "targets": [5, 9],
                        "className": "text-center"
                    },
                    {
                        "targets": 10,
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
            
            console.log('Break time report DataTable initialized successfully');
            
            // Refresh table when filter form is submitted
            $('form').on('submit', function() {
                window.breakTimeTable.draw();
            });
            
                    // Auto refresh table when filter values change
        $('#start_date, #end_date, #user_id, #division_id, #status').on('change', function() {
            window.breakTimeTable.draw();
            // Update statistics when filters change
            updateStatistics();
        });
            
            // Search functionality with debounce
            var searchTimeout;
            $('#break_time_search').on('keyup input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    window.breakTimeTable.draw(false);
                }, 300);
            });
            
        } catch (error) {
            console.error('Error initializing break time report DataTable:', error);
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
        
        // Initialize dropdown menu
        initializeSimpleDropdown();
    });
    
    // Simple working dropdown solution
    function initializeSimpleDropdown() {
        console.log('Initializing simple dropdown system for Break Times Report');
        
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
        
        console.log('Simple dropdown system initialized for Break Times Report');
    }
    
    // Date filter functionality
    function handleDateFilterChange(value) {
        const startDateContainer = document.getElementById('start_date_container');
        const endDateContainer = document.getElementById('end_date_container');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        
        if (value === 'custom') {
            startDateContainer.style.display = 'block';
            endDateContainer.style.display = 'block';
        } else {
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
            
            // Format dates for hidden inputs
            if (startDate && endDate) {
                const formatDate = (date) => {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                };
                
                const formattedStartDate = formatDate(startDate);
                const formattedEndDate = formatDate(endDate);
                
                startDateInput.value = formattedStartDate;
                endDateInput.value = formattedEndDate;
                
                // Update breadcrumb display
                updateBreadcrumbDates(formattedStartDate, formattedEndDate, value);
            }
        }
        
        // Apply filters after date change
        if (window.breakTimeTable) {
            window.breakTimeTable.draw();
        }
        
        // Update statistics after date filter change
        updateStatistics();
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

    // Function to update statistics
    function updateStatistics() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const dateFilter = document.getElementById('date_filter').value;
        const userId = document.getElementById('user_id').value;
        const divisionId = document.getElementById('division_id').value;
        const status = document.getElementById('status').value;
        
        // Show loading state
        const statsContainer = document.getElementById('statsContainer');
        if (statsContainer) {
            statsContainer.style.opacity = '0.6';
        }
        
        // Fetch updated statistics
        fetch('{{ route("break-times-backup.stats") }}?' + new URLSearchParams({
            start_date: startDate,
            end_date: endDate,
            date_filter: dateFilter,
            user_id: userId,
            division_id: divisionId,
            status: status
        }))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStatsDisplay(data.stats);
            } else {
                console.error('Error updating stats:', data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching stats:', error);
        })
        .finally(() => {
            // Restore opacity
            if (statsContainer) {
                statsContainer.style.opacity = '1';
            }
        });
    }

    // Function to update stats display
    function updateStatsDisplay(stats) {
        // Update status-based stats
        if (stats.status_stats) {
            stats.status_stats.forEach(stat => {
                const element = document.getElementById(`stat-${stat.bt_status}-count`);
                if (element) {
                    element.textContent = stat.total;
                }
            });
        }
        
        // Update type-based stats
        if (stats.type_stats) {
            stats.type_stats.forEach(stat => {
                const element = document.getElementById(`stat-${stat.bt_type}-count`);
                if (element) {
                    element.textContent = stat.total;
                }
            });
        }
        
        // Update total count
        const totalElement = document.getElementById('stat-total-count');
        if (totalElement && stats.total_break_times !== undefined) {
            totalElement.textContent = stats.total_break_times;
        }
        
        // Update average duration if needed
        if (stats.average_duration !== undefined) {
            console.log('Average duration:', stats.average_duration, 'minutes');
        }
    }
    
    // Export functions
    function exportToExcel() {
        console.log('Exporting to Excel...');
        
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const userId = document.getElementById('user_id').value;
        const divisionId = document.getElementById('division_id').value;
        const status = document.getElementById('status').value;
        const search = document.getElementById('break_time_search').value;
        
        console.log('Export filters:', {
            start_date: startDate,
            end_date: endDate,
            user_id: userId,
            division_id: divisionId,
            status: status,
            search: search
        });
        
        const params = new URLSearchParams();
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        if (userId) params.append('user_id', userId);
        if (divisionId) params.append('division_id', divisionId);
        if (status) params.append('status', status);
        if (search) params.append('search', search);
        
        const url = "{{ route('break-times-backup.export-excel') }}?" + params.toString();
        console.log('Export URL:', url);
        
        // Show loading indicator
        const exportBtn = event.target;
        const originalText = exportBtn.innerHTML;
        exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exporting...';
        exportBtn.disabled = true;
        
        // Use fetch to handle the download with better error handling
        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin' // Include cookies for auth
        })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                if (!response.ok) {
                    if (response.status === 401) {
                        throw new Error('Unauthorized - Please login again');
                    } else if (response.status === 403) {
                        throw new Error('Forbidden - Access denied');
                    } else if (response.status === 404) {
                        throw new Error('Export endpoint not found');
                    } else if (response.status === 500) {
                        throw new Error('Server error - Please try again later');
                    } else {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                }
                
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('spreadsheet')) {
                    console.warn('Unexpected content type:', contentType);
                }
                
                return response.blob();
            })
            .then(blob => {
                console.log('Blob received:', blob.size, 'bytes');
                
                if (blob.size === 0) {
                    throw new Error('Empty file received');
                }
                
                // Create download link
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = 'backup_times_report.xlsx';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                
                // Reset button
                exportBtn.innerHTML = originalText;
                exportBtn.disabled = false;
                
                console.log('Excel export completed successfully');
            })
            .catch(error => {
                console.error('Error exporting to Excel:', error);
                
                let errorMessage = 'Error exporting to Excel: ' + error.message;
                if (error.message.includes('Failed to fetch')) {
                    errorMessage = 'Network error - Please check your connection and try again';
                }
                
                alert(errorMessage);
                
                // Reset button
                exportBtn.innerHTML = originalText;
                exportBtn.disabled = false;
            });
    }
    
    function exportToPDF() {
        console.log('Exporting to PDF...');
        
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const userId = document.getElementById('user_id').value;
        const divisionId = document.getElementById('division_id').value;
        const status = document.getElementById('status').value;
        const search = document.getElementById('break_time_search').value;
        
        console.log('Export filters:', {
            start_date: startDate,
            end_date: endDate,
            user_id: userId,
            division_id: divisionId,
            status: status,
            search: search
        });
        
        const params = new URLSearchParams();
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        if (userId) params.append('user_id', userId);
        if (divisionId) params.append('division_id', divisionId);
        if (status) params.append('status', status);
        if (search) params.append('search', search);
        
        const url = "{{ route('break-times-backup.export-pdf') }}?" + params.toString();
        console.log('Export URL:', url);
        
        // Create temporary link for download
        const link = document.createElement('a');
        link.href = url;
                    link.download = 'backup_times_report.pdf';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    
    // Apply filters function
    function applyFilters() {
        if (window.breakTimeTable) {
            window.breakTimeTable.ajax.reload();
        } else {
            $('#breakTimeTable').DataTable().ajax.reload();
        }
    }
    
    // Delete break time function
    function deleteBreakTime(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data backup time ini?')) {
            $.ajax({
                url: "{{ route('break-times-backup.destroy', ':id') }}".replace(':id', id),
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (window.breakTimeTable) {
                            window.breakTimeTable.draw();
                        } else {
                            $('#breakTimeTable').DataTable().draw();
                        }
                        // Show success message
                        $('<div class="alert alert-success alert-dismissible">' +
                          '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                          'Data backup time berhasil dihapus!</div>').insertBefore('#breakTimeTable').delay(3000).fadeOut();
                    } else {
                                                  alert('Gagal menghapus data backup time');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat menghapus data');
                }
            });
        }
    }

    // Initialize when document is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Add form submit event listener for date filter
        const filterForm = document.querySelector('form[action*="break-times-backup.report"]');
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
                if (window.breakTimeTable) {
                    window.breakTimeTable.ajax.reload();
                }
                
                // Update statistics
                updateStatistics();
            });
        }
        
        // Initialize breadcrumb with current values
        const dateFilter = document.getElementById('date_filter').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        
        if (dateFilter && dateFilter !== 'custom') {
            updateBreadcrumbDates(startDate, endDate, dateFilter);
        }
        
        // Initialize statistics
        updateStatistics();
    });
</script>
