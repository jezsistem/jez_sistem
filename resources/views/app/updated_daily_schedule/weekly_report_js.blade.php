<script>
// Two-way synchronization between date filter and week range
document.addEventListener('DOMContentLoaded', function() {
    const dateFilterSelect = document.getElementById('date_filter');
    const startDateInput = document.getElementById('start_date');
    const scheduleTables = document.querySelectorAll('.schedule-report-table');
    
    // Update column visibility based on date filter
    function updateColumnVisibility() {
        if (dateFilterSelect.value === 'now') {
            // Hide other date columns in all tables
            scheduleTables.forEach(table => {
                const otherDateColumns = table.querySelectorAll('.other-date-column');
                otherDateColumns.forEach(col => {
                    col.style.display = 'none';
                });
                // Set table width to 50% for NOW filter only
                table.style.width = '50%';
            });
        } else {
            // Show all columns in all tables and reset table width
            scheduleTables.forEach(table => {
                const otherDateColumns = table.querySelectorAll('.other-date-column');
                otherDateColumns.forEach(col => {
                    col.style.display = '';
                });
                // Reset table width for other filters
                table.style.width = '';
            });
        }
    }
    
    // Initial call
    updateColumnVisibility();
    
    // Listen for changes
    if (dateFilterSelect) {
        dateFilterSelect.addEventListener('change', updateColumnVisibility);
    }
    
    if (dateFilterSelect && startDateInput) {
        // Handle start date change
        startDateInput.addEventListener('change', function() {
            const selectedDate = this.value;
            console.log('Start date changed to:', selectedDate);
            
            // Don't auto-detect if 'now' filter is currently selected
            if (dateFilterSelect.value === 'now') {
                console.log('Skipping auto-detection because NOW filter is selected');
                return;
            }
            
            if (selectedDate) {
                // Calculate end date (Sunday)
                const endDate = calculateEndDate(selectedDate);
                console.log('Calculated end date:', endDate);
                
                // Detect if this matches any predefined filter
                const detectedFilter = detectFilterFromDateRange(selectedDate, endDate);
                console.log('Detected filter:', detectedFilter);
                
                if (detectedFilter !== 'custom') {
                    dateFilterSelect.value = detectedFilter;
                    console.log('Updated date filter to:', detectedFilter);
                } else {
                    dateFilterSelect.value = 'custom';
                    console.log('Set date filter to custom');
                }
                
                // Auto-submit form after synchronization
                setTimeout(() => {
                    document.getElementById('filterForm').submit();
                }, 100);
            }
        });
        
        // Handle date filter change
        dateFilterSelect.addEventListener('change', function() {
            const selectedFilter = this.value;
            console.log('Date filter changed to:', selectedFilter);
            
            if (selectedFilter !== 'custom') {
                // Calculate dates based on selected filter
                const dates = calculateDatesFromFilter(selectedFilter);
                if (dates) {
                    startDateInput.value = dates.startDate;
                    console.log('Updated start date to:', dates.startDate);
                    
                    // Auto-submit form after synchronization
                    setTimeout(() => {
                        document.getElementById('filterForm').submit();
                    }, 100);
                }
            }
            
            // Update column visibility
            updateColumnVisibility();
        });
    }
    
    // Auto-submit on division, position, and shift filter changes
    const divisionFilter = document.getElementById('division_filter');
    const positionFilter = document.getElementById('position_filter');
    const shiftFilter = document.getElementById('shift_filter');
    
    if (divisionFilter) {
        divisionFilter.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    }
    
    if (positionFilter) {
        positionFilter.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    }
    
    if (shiftFilter) {
        shiftFilter.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    }
});

// Helper function to calculate dates from filter
function calculateDatesFromFilter(filter) {
    const today = new Date();
    let startDate, endDate;
    
    switch (filter) {
        case 'this_week':
            startDate = getMondayOfWeek(today);
            break;
        case 'next_week':
            startDate = getMondayOfWeek(new Date(today.getTime() + 7 * 24 * 60 * 60 * 1000));
            break;
        case 'past_week':
            startDate = getMondayOfWeek(new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000));
            break;
        case 'now':
            // For NOW filter, we still use current week but with special logic in backend
            startDate = getMondayOfWeek(today);
            break;
        default:
            return null;
    }
    
    if (startDate) {
        endDate = calculateEndDate(startDate.toISOString().split('T')[0]);
        return {
            startDate: startDate.toISOString().split('T')[0],
            endDate: endDate
        };
    }
    
    return null;
}

// Helper function to get Monday of a week
function getMondayOfWeek(date) {
    const day = date.getDay();
    const diff = date.getDate() - day + (day === 0 ? -6 : 1); // Adjust when day is Sunday
    return new Date(date.setDate(diff));
}

// Helper function to calculate end date (Sunday)
function calculateEndDate(startDate) {
    const start = new Date(startDate);
    const end = new Date(start);
    end.setDate(start.getDate() + 6); // Add 6 days to get to Sunday
    return end.toISOString().split('T')[0];
}

// Helper function to detect filter from date range
function detectFilterFromDateRange(startDate, endDate) {
    const start = new Date(startDate);
    const end = new Date(endDate);
    const today = new Date();
    
    // Get Monday of current week
    const mondayThisWeek = getMondayOfWeek(today);
    const sundayThisWeek = new Date(mondayThisWeek);
    sundayThisWeek.setDate(mondayThisWeek.getDate() + 6);
    
    // Get Monday of previous week
    const mondayLastWeek = new Date(mondayThisWeek);
    mondayLastWeek.setDate(mondayThisWeek.getDate() - 7);
    const sundayLastWeek = new Date(mondayLastWeek);
    sundayLastWeek.setDate(mondayLastWeek.getDate() + 6);
    
    // Get Monday of next week
    const mondayNextWeek = new Date(mondayThisWeek);
    mondayNextWeek.setDate(mondayThisWeek.getDate() + 7);
    const sundayNextWeek = new Date(mondayNextWeek);
    sundayNextWeek.setDate(mondayNextWeek.getDate() + 6);
    
    // Compare date ranges
    if (start.toDateString() === mondayThisWeek.toDateString() && 
        end.toDateString() === sundayThisWeek.toDateString()) {
        return 'this_week';
    } else if (start.toDateString() === mondayNextWeek.toDateString() && 
               end.toDateString() === sundayNextWeek.toDateString()) {
        return 'next_week';
    } else if (start.toDateString() === mondayLastWeek.toDateString() && 
               end.toDateString() === sundayLastWeek.toDateString()) {
        return 'past_week';
    } else {
        return 'custom';
    }
}

function exportToExcel() {
    console.log('Export Excel clicked');
    const loadingIndicator = document.createElement('div');
    loadingIndicator.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="sr-only">Loading...</span></div>';
    loadingIndicator.className = 'position-fixed top-50 start-50 translate-middle';
    document.body.appendChild(loadingIndicator);

    const form = document.getElementById('filterForm');
    console.log('Form found:', form);
    if (!form) { console.error('Form not found'); return; }
    const formData = new FormData(form);
    console.log('Form data:', Object.fromEntries(formData));
    let exportUrl = '{{ route("daily-schedules.export-weekly-report-public") }}';
    console.log('Base export URL:', exportUrl);
    const params = new URLSearchParams();
    for (let [key, value] of formData.entries()) {
        if (value) {
            params.append(key, value);
            console.log('Adding param:', key, '=', value);
        }
    }
    if (params.toString()) { exportUrl += '?' + params.toString(); }
    console.log('Final export URL:', exportUrl);
    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = `weekly-schedule-report-{{ $startDate }}-{{ $endDate }}.xlsx`;
    console.log('Download filename:', link.download);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    console.log('Download triggered');

    setTimeout(() => {
        if (document.body.contains(loadingIndicator)) {
            document.body.removeChild(loadingIndicator);
        }
    }, 1000);
}

function exportToPDF() {
    console.log('Export PDF clicked');
    
    // Show loading indicator
    const loadingIndicator = document.createElement('div');
    loadingIndicator.innerHTML = 'Preparing PDF export...';
    loadingIndicator.style.cssText = 'position:fixed;top:20px;right:20px;background:#17a2b8;color:white;padding:10px;border-radius:5px;z-index:9999;';
    document.body.appendChild(loadingIndicator);
    
    // Get current filters from form
    const form = document.getElementById('filterForm');
    console.log('Form found:', form);
    
    if (!form) {
        console.error('Form not found');
        return;
    }
    
    const formData = new FormData(form);
    console.log('Form data:', Object.fromEntries(formData));
    
    // Build export URL with current filters - Use public route for better compatibility
    let exportUrl = '{{ route("daily-schedules.export-weekly-report-pdf-public") }}';
    console.log('Base export URL:', exportUrl);
    
    const params = new URLSearchParams();
    
    // Add all current filter parameters
    for (let [key, value] of formData.entries()) {
        if (value) {
            params.append(key, value);
            console.log('Adding param:', key, '=', value);
        }
    }
    
    if (params.toString()) {
        exportUrl += '?' + params.toString();
    }
    
    console.log('Final export URL:', exportUrl);
    
    // Create temporary link and trigger download
    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = `weekly-schedule-report-{{ $startDate }}-{{ $endDate }}.pdf`;
    console.log('Download filename:', link.download);
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    console.log('Download triggered');
    
    // Remove loading indicator
    setTimeout(() => {
        if (document.body.contains(loadingIndicator)) {
            document.body.removeChild(loadingIndicator);
        }
    }, 1000);
}
</script>
