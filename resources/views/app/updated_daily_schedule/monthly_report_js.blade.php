<script>
function exportToExcel() {
    console.log('Export Excel clicked');
    
    // Show loading indicator
    const loadingIndicator = document.createElement('div');
    loadingIndicator.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="sr-only">Loading...</span></div>';
    loadingIndicator.className = 'position-fixed top-50 start-50 translate-middle';
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
    
    // Build export URL with current filters
    let exportUrl = '{{ route("daily-schedules.export-monthly-excel") }}';
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
    link.download = `monthly-schedule-report-{{ $startDate }}-{{ $endDate }}.xlsx`;
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
    
    // Build export URL with current filters
    let exportUrl = '{{ route("daily-schedules.export-monthly-pdf") }}';
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
    link.download = `monthly-schedule-report-{{ $startDate }}-{{ $endDate }}.pdf`;
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

console.log('Monthly Report Scripts Loaded');

// Two-way synchronization between month_filter and custom_month
document.addEventListener('DOMContentLoaded', function() {
    const monthFilter = document.getElementById('month_filter');
    const customMonth = document.getElementById('custom_month');
    
    if (monthFilter && customMonth) {
        // Initialize custom month input state
        updateCustomMonthState();
        
        // Month filter change handler
        monthFilter.addEventListener('change', function() {
            const selectedFilter = this.value;
            console.log('Month filter changed to:', selectedFilter);
            
            if (selectedFilter !== 'custom') {
                // Calculate month based on filter
                const calculatedMonth = calculateMonthFromFilter(selectedFilter);
                customMonth.value = calculatedMonth;
                console.log('Updated custom month to:', calculatedMonth);
                
                // Auto-submit form to update the page with new month
                console.log('Auto-submitting form to update page...');
                const form = document.getElementById('filterForm');
                if (form) {
                    form.submit();
                }
            }
            
            // Update custom month input state
            updateCustomMonthState();
            
            // Log current state
            console.log('Current state after filter change:', {
                monthFilter: monthFilter.value,
                customMonth: customMonth.value,
                customMonthDisabled: customMonth.disabled
            });
        });
        
        // Custom month change handler
        customMonth.addEventListener('change', function() {
            const selectedMonth = this.value;
            console.log('Custom month changed to:', selectedMonth);
            
            // Detect if this matches any predefined filter
            const detectedFilter = detectFilterFromMonth(selectedMonth);
            if (detectedFilter !== 'custom') {
                monthFilter.value = detectedFilter;
                console.log('Updated month filter to:', detectedFilter);
            } else {
                monthFilter.value = 'custom';
                console.log('Set month filter to custom');
            }
            
            // Update custom month input state
            updateCustomMonthState();
            
            // Auto-submit form to update the page with new month
            console.log('Auto-submitting form to update page...');
            const form = document.getElementById('filterForm');
            if (form) {
                form.submit();
            }
        });
        
        // Add event listeners for division, position, and shift filters
        const divisionFilter = document.getElementById('division_filter');
        const positionFilter = document.getElementById('position_filter');
        const shiftFilter = document.getElementById('shift_filter');
        
        if (divisionFilter) {
            divisionFilter.addEventListener('change', function() {
                console.log('Division filter changed to:', this.value);
                // Auto-submit form to update the page
                const form = document.getElementById('filterForm');
                if (form) {
                    form.submit();
                }
            });
        }
        
        if (positionFilter) {
            positionFilter.addEventListener('change', function() {
                console.log('Position filter changed to:', this.value);
                // Auto-submit form to update the page
                const form = document.getElementById('filterForm');
                if (form) {
                    form.submit();
                }
            });
        }
        
        if (shiftFilter) {
            shiftFilter.addEventListener('change', function() {
                console.log('Shift filter changed to:', this.value);
                // Auto-submit form to update the page
                const form = document.getElementById('filterForm');
                if (form) {
                    form.submit();
                }
            });
        }
    }
});

function updateCustomMonthState() {
    const monthFilter = document.getElementById('month_filter');
    const customMonth = document.getElementById('custom_month');
    
    if (monthFilter && customMonth) {
        const isCustom = monthFilter.value === 'custom';
        
        // Custom month should always be enabled, just change visual state
        customMonth.disabled = false;
        
        if (isCustom) {
            customMonth.style.backgroundColor = '#ffffff';
            customMonth.style.cursor = 'text';
            customMonth.style.borderColor = '#ced4da';
        } else {
            customMonth.style.backgroundColor = '#f8f9fa';
            customMonth.style.cursor = 'text'; // Still allow input
            customMonth.style.borderColor = '#e9ecef';
        }
    }
}

function calculateMonthFromFilter(filter) {
    const today = new Date();
    let targetMonth;
    
    switch (filter) {
        case 'this_month':
            targetMonth = new Date(today.getFullYear(), today.getMonth(), 1);
            break;
        case 'last_month':
            targetMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            break;
        case 'next_month':
            targetMonth = new Date(today.getFullYear(), today.getMonth() + 1, 1);
            break;
        default:
            targetMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    }
    
    console.log('Target month:', targetMonth.toLocaleDateString());
    console.log('Target month name:', targetMonth.toLocaleString('en-US', { month: 'long' }));
    console.log('Result:', targetMonth.toISOString().slice(0, 7));
    console.log('========================');
    
    // Fix timezone issue: use local date formatting instead of toISOString()
    const year = targetMonth.getFullYear();
    const month = String(targetMonth.getMonth() + 1).padStart(2, '0');
    const result = `${year}-${month}`;
    
    console.log('Fixed result (timezone-safe):', result);
    
    return result; // Format: YYYY-MM
}

function detectFilterFromMonth(monthStr) {
    if (!monthStr) return 'this_month';
    
    const today = new Date();
    const selectedDate = new Date(monthStr + '-01');
    
    const thisMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
    const nextMonth = new Date(today.getFullYear(), today.getMonth() + 1, 1);
    
    // Compare months (ignore day)
    if (selectedDate.getFullYear() === thisMonth.getFullYear() && 
        selectedDate.getMonth() === thisMonth.getMonth()) {
        return 'this_month';
    } else if (selectedDate.getFullYear() === lastMonth.getFullYear() && 
               selectedDate.getMonth() === lastMonth.getMonth()) {
        return 'last_month';
    } else if (selectedDate.getFullYear() === nextMonth.getFullYear() && 
               selectedDate.getMonth() === nextMonth.getMonth()) {
        return 'next_month';
    } else {
        return 'custom';
    }
}
</script>
