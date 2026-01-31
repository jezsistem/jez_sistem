<script>
// Backend date variables from PHP
window.backendStartDate = '{{ $startDate ?? date('Y-m-d', strtotime('monday this week')) }}';
window.backendEndDate = '{{ $endDate ?? date('Y-m-d', strtotime('sunday this week')) }}';
window.backendDateFilter = '{{ $dateFilter ?? 'this_week' }}';

// Helper functions
function getDateRange(filter) {
    const today = new Date();
    let startDate, endDate;
    
    switch(filter) {
        case 'this_week':
            const monday = new Date(today);
            const dayOfWeek = today.getDay();
            const diff = today.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1);
            monday.setDate(diff);
            startDate = new Date(monday);
            endDate = new Date(monday);
            endDate.setDate(monday.getDate() + 6);
            break;
        case 'past_week':
            const lastMonday = new Date(today);
            const lastDayOfWeek = today.getDay();
            const lastDiff = today.getDate() - lastDayOfWeek + (lastDayOfWeek === 0 ? -13 : -6);
            lastMonday.setDate(lastDiff);
            startDate = new Date(lastMonday);
            endDate = new Date(lastMonday);
            endDate.setDate(lastMonday.getDate() + 6);
            break;
        default:
            const defaultMonday = new Date(today);
            const defaultDayOfWeek = today.getDay();
            const defaultDiff = today.getDate() - defaultDayOfWeek + (defaultDayOfWeek === 0 ? -6 : 1);
            defaultMonday.setDate(defaultDiff);
            startDate = new Date(defaultMonday);
            endDate = new Date(defaultMonday);
            endDate.setDate(defaultMonday.getDate() + 6);
    }
    
    return { startDate, endDate };
}

function formatDate(date) {
    return date.toISOString().split('T')[0];
}

// Global functions
function loadScheduleDirectly() {
    const divisionSelect = document.getElementById('division_filter');
    const searchInput = document.getElementById('search_filter');
    const dateFilter = document.getElementById('date_filter');
    
    let url = '{{ route("daily-schedules.weekly_v2") }}';
    const params = new URLSearchParams();
    
    if (divisionSelect && divisionSelect.value) {
        params.append('division_id', divisionSelect.value);
    }
    if (searchInput && searchInput.value.trim()) {
        params.append('search', searchInput.value.trim());
    }
    if (dateFilter && dateFilter.value) {
        params.append('date_filter', dateFilter.value);
    }
    
    if (params.toString()) {
        url += '?' + params.toString();
    }
    
    window.location.href = url;
}

function saveScheduleDirectly(selectElement) {
    const userId = selectElement.getAttribute('data-user-id');
    const date = selectElement.getAttribute('data-date');
    const shiftCodeId = selectElement.value;
    
    if (!userId || !date) {
        console.error('Error: Missing user ID or date!');
        return;
    }
    
    const indicator = document.createElement('div');
    indicator.innerHTML = 'Saving...';
    indicator.className = 'fixed top-5 right-5 bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
    document.body.appendChild(indicator);
    
    fetch('{{ route("daily-schedule.save-weekly-schedule") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            start_date: '{{ $startDate }}',
            schedules: [{
                user_id: parseInt(userId),
                dates: [{
                    date: date,
                    shift_code_id: shiftCodeId ? parseInt(shiftCodeId) : null
                }]
            }]
        })
    })
    .then(response => response.json())
    .then(data => {
        document.body.removeChild(indicator);
        
        if (data.success) {
            const successIndicator = document.createElement('div');
            successIndicator.innerHTML = '✓ Tersimpan';
            successIndicator.className = 'fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            document.body.appendChild(successIndicator);
            setTimeout(() => {
                if (document.body.contains(successIndicator)) {
                    document.body.removeChild(successIndicator);
                }
            }, 2000);
        } else {
            console.error('Failed to save:', data.message || 'Unknown error');
        }
    })
    .catch(error => {
        console.error('Save error:', error);
        if (document.body.contains(indicator)) {
            document.body.removeChild(indicator);
        }
    });
}

function loadExistingSchedules() {
    const divisionId = document.getElementById('division_filter')?.value;
    const searchValue = document.getElementById('search_filter')?.value?.trim();
    const dateFilter = document.getElementById('date_filter')?.value;
    
    if (!divisionId && !searchValue && !dateFilter) {
        return;
    }
    loadExistingSchedulesForAll(divisionId, searchValue, dateFilter);
}

function loadExistingSchedulesForAll(divisionId = null, searchValue = null, dateFilter = null) {
    const loadingIndicator = document.createElement('div');
    loadingIndicator.innerHTML = 'Loading schedules...';
    loadingIndicator.className = 'fixed top-5 right-5 bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
    document.body.appendChild(loadingIndicator);
    
    let startDate, endDate;
    
    if (typeof window.backendStartDate !== 'undefined' && typeof window.backendEndDate !== 'undefined') {
        startDate = new Date(window.backendStartDate);
        endDate = new Date(window.backendEndDate);
    } else {
        const dateRange = getDateRange(dateFilter || 'this_week');
        startDate = dateRange.startDate;
        endDate = dateRange.endDate;
    }
    
    let url = '{{ route("daily-schedule.get-weekly-schedules") }}?start_date=' + encodeURIComponent(startDate.toISOString().split('T')[0]);
    if (divisionId) {
        url += '&division_id=' + encodeURIComponent(divisionId);
    }
    if (searchValue) {
        url += '&search=' + encodeURIComponent(searchValue);
    }
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (document.body.contains(loadingIndicator)) {
                document.body.removeChild(loadingIndicator);
            }
            
            if (data.success && data.schedules) {
                data.schedules.forEach(function(userSchedule) {
                    const userId = userSchedule.user_id;
                    
                    Object.keys(userSchedule.dates).forEach(function(date) {
                        const scheduleData = userSchedule.dates[date];
                        const selector = `select[data-user-id="${userId}"][data-date="${date}"]`;
                        const selectElement = document.querySelector(selector);
                        
                        if (selectElement && scheduleData.sc_id) {
                            const optionExists = Array.from(selectElement.options).some(option => option.value == scheduleData.sc_id);
                            
                            if (optionExists) {
                                selectElement.value = scheduleData.sc_id;
                            }
                        }
                    });
                });
                
                if (data.schedules.length > 0) {
                    const successIndicator = document.createElement('div');
                    successIndicator.innerHTML = `✓ Loaded ${data.schedules.length} users with schedules`;
                    successIndicator.className = 'fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                    document.body.appendChild(successIndicator);
                    setTimeout(() => {
                        if (document.body.contains(successIndicator)) {
                            document.body.removeChild(successIndicator);
                        }
                    }, 3000);
                }
            }
        })
        .catch(error => {
            console.error('Error loading schedules:', error);
            if (document.body.contains(loadingIndicator)) {
                document.body.removeChild(loadingIndicator);
            }
        });
}

// Export functions
function exportToExcel() {
    const divisionId = document.getElementById('division_filter')?.value || '';
    const dateFilter = document.getElementById('date_filter')?.value || 'this_week';
    const startDateInput = document.querySelector('input[name="start_date"]');
    const startDate = startDateInput ? startDateInput.value : '';
    const searchValue = document.getElementById('search_filter')?.value?.trim() || '';
    
    let exportUrl = '{{ route("daily-schedules.export-weekly-public") }}';
    const params = new URLSearchParams();
    
    if (divisionId) params.append('division_id', divisionId);
    if (dateFilter) params.append('date_filter', dateFilter);
    if (startDate) params.append('start_date', startDate);
    if (searchValue) params.append('search', searchValue);
    
    if (params.toString()) {
        exportUrl += '?' + params.toString();
    }
    
    fetch(exportUrl)
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.blob();
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            let filename = 'weekly-schedule';
            if (divisionId) {
                const divisionSelect = document.getElementById('division_filter');
                const divisionText = divisionSelect.options[divisionSelect.selectedIndex].text;
                filename += '-' + divisionText.replace(/[^a-zA-Z0-9]/g, '_');
            }
            filename += '-' + dateFilter + '-' + new Date().toISOString().split('T')[0] + '.xlsx';
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
            Swal.fire('Berhasil', 'Export Excel berhasil!', 'success');
        })
        .catch(error => {
            console.error('Export Excel error:', error);
            Swal.fire('Error', 'Export Excel gagal: ' + error.message, 'error');
        });
}

function exportToPDF() {
    const divisionId = document.getElementById('division_filter')?.value || '';
    const dateFilter = document.getElementById('date_filter')?.value || 'this_week';
    const startDateInput = document.querySelector('input[name="start_date"]');
    const startDate = startDateInput ? startDateInput.value : '';
    const searchValue = document.getElementById('search_filter')?.value?.trim() || '';
    
    let exportUrl = '{{ route("daily-schedules.export-weekly-pdf-public") }}';
    const params = new URLSearchParams();
    
    if (divisionId) params.append('division_id', divisionId);
    if (dateFilter) params.append('date_filter', dateFilter);
    if (startDate) params.append('start_date', startDate);
    if (searchValue) params.append('search', searchValue);
    
    if (params.toString()) {
        exportUrl += '?' + params.toString();
    }
    
    fetch(exportUrl)
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.blob();
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            let filename = 'weekly-schedule';
            if (divisionId) {
                const divisionSelect = document.getElementById('division_filter');
                const divisionText = divisionSelect.options[divisionSelect.selectedIndex].text;
                filename += '-' + divisionText.replace(/[^a-zA-Z0-9]/g, '_');
            }
            filename += '-' + dateFilter + '-' + new Date().toISOString().split('T')[0] + '.pdf';
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
            Swal.fire('Berhasil', 'Export PDF berhasil!', 'success');
        })
        .catch(error => {
            console.error('Export PDF error:', error);
            Swal.fire('Error', 'Export PDF gagal: ' + error.message, 'error');
        });
}

// Import functions
function showImportModal() {
    const modal = document.getElementById('importModal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

function hideImportModal() {
    const modal = document.getElementById('importModal');
    if (modal) {
        modal.classList.add('hidden');
        const fileInput = document.getElementById('excel_file');
        if (fileInput) {
            fileInput.value = '';
        }
    }
}

function showImportAlert(message, type = 'info', errors = null) {
    const alertArea = document.getElementById('importAlertArea');
    if (!alertArea) return;
    
    const alertId = 'alert-' + Date.now();
    const bgColor = type === 'success' ? 'bg-green-50 border-green-200' : type === 'danger' ? 'bg-red-50 border-red-200' : 'bg-blue-50 border-blue-200';
    const textColor = type === 'success' ? 'text-green-800' : type === 'danger' ? 'text-red-800' : 'text-blue-800';
    
    let alertHtml = `
        <div class="${bgColor} border ${textColor} rounded-lg p-4 mb-4" id="${alertId}">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'times-circle' : 'info-circle'} mr-2"></i>
                    <strong>${type === 'success' ? 'Success' : type === 'danger' ? 'Error' : 'Info'}:</strong> ${message}
                </div>
                <button type="button" onclick="closeImportAlert('${alertId}')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
    `;
    
    if (errors && errors.length > 0) {
        alertHtml += `
            <div class="mt-2">
                <strong>Details:</strong>
                <ul class="list-disc list-inside mt-1">
                    ${errors.slice(0, 10).map(error => `<li>${error}</li>`).join('')}
                    ${errors.length > 10 ? `<li>... and ${errors.length - 10} more errors</li>` : ''}
                </ul>
            </div>
        `;
    }
    
    alertHtml += '</div>';
    
    alertArea.innerHTML = alertHtml;
    alertArea.classList.remove('hidden');
    
    if (type === 'success') {
        setTimeout(() => closeImportAlert(alertId), 10000);
    }
}

function closeImportAlert(alertId) {
    const alert = document.getElementById(alertId);
    if (alert) {
        alert.remove();
        const alertArea = document.getElementById('importAlertArea');
        if (alertArea && alertArea.children.length === 0) {
            alertArea.classList.add('hidden');
        }
    }
}

function importExcel() {
    const fileInput = document.getElementById('excel_file');
    const startDate = document.getElementById('import_start_date').value;
    
    if (!fileInput.files[0]) {
        showImportAlert('Please select an Excel file', 'warning');
        return;
    }
    
    if (!startDate) {
        showImportAlert('Please select a start date', 'warning');
        return;
    }
    
    const formData = new FormData();
    formData.append('excel_file', fileInput.files[0]);
    formData.append('start_date', startDate);
    
    const loadingIndicator = document.createElement('div');
    loadingIndicator.innerHTML = 'Importing Excel file...';
    loadingIndicator.className = 'fixed top-5 right-5 bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
    document.body.appendChild(loadingIndicator);
    
    fetch('{{ route("daily-schedules.import-weekly-excel") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (document.body.contains(loadingIndicator)) {
            document.body.removeChild(loadingIndicator);
        }
        
        if (data.success) {
            showImportAlert(data.message, 'success');
            hideImportModal();
            fileInput.value = '';
            setTimeout(() => {
                loadExistingSchedules();
            }, 1000);
        } else {
            showImportAlert(data.message, 'danger', data.errors || []);
            hideImportModal();
        }
    })
    .catch(error => {
        if (document.body.contains(loadingIndicator)) {
            document.body.removeChild(loadingIndicator);
        }
        console.error('Import error:', error);
        showImportAlert('Import failed: ' + error.message, 'danger');
        hideImportModal();
    });
}

// Export handlers
$(document).ready(function() {
    $('#export_weekly_btn').on('click', function() {
        $('#export_weekly_menu').toggleClass('hidden');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#export_weekly_btn, #export_weekly_menu').length) {
            $('#export_weekly_menu').addClass('hidden');
        }
    });

    $('#export_weekly_excel_btn').on('click', function() {
        exportToExcel();
        $('#export_weekly_menu').addClass('hidden');
    });

    $('#export_weekly_pdf_btn').on('click', function() {
        exportToPDF();
        $('#export_weekly_menu').addClass('hidden');
    });

    // Auto-load schedules when page loads
    setTimeout(loadExistingSchedulesForAll, 500);
    
    // Filter form handlers
    const divisionFilter = document.getElementById('division_filter');
    const dateFilter = document.getElementById('date_filter');
    const startDateInput = document.querySelector('input[name="start_date"]');
    const searchFilter = document.getElementById('search_filter');
    
    if (divisionFilter) {
        divisionFilter.addEventListener('change', function() {
            if (!this.disabled) {
                setTimeout(() => {
                    document.getElementById('filterForm').submit();
                }, 100);
            }
        });
    }
    
    if (dateFilter) {
        dateFilter.addEventListener('change', function() {
            const selectedFilter = this.value;
            if (selectedFilter !== 'custom') {
                const dates = calculateDatesFromFilter(selectedFilter);
                if (dates && startDateInput) {
                    startDateInput.value = dates.startDate;
                }
            }
            setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 100);
        });
    }
    
    if (startDateInput) {
        startDateInput.addEventListener('change', function() {
            const selectedDate = this.value;
            if (selectedDate) {
                const endDate = calculateEndDate(selectedDate);
                const detectedFilter = detectFilterFromDateRange(selectedDate, endDate);
                if (dateFilter) {
                    if (detectedFilter !== 'custom') {
                        dateFilter.value = detectedFilter;
                    } else {
                        dateFilter.value = 'custom';
                    }
                }
                setTimeout(() => {
                    document.getElementById('filterForm').submit();
                }, 100);
            }
        });
    }
    
    if (searchFilter) {
        var searchTimeout;
        searchFilter.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (document.getElementById('load_schedule')) {
                    loadScheduleDirectly();
                } else {
                    document.getElementById('filterForm').submit();
                }
            }, 500);
        });
    }
    
    // Modal handlers
    const modal = document.getElementById('importModal');
    if (modal) {
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                hideImportModal();
            }
        });
    }
    
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            hideImportModal();
        }
    });
});

// Helper functions for date synchronization
function calculateDatesFromFilter(filter) {
    const today = new Date();
    let startDate, endDate;
    
    switch (filter) {
        case 'this_week':
            const mondayThisWeek = new Date(today);
            const dayOfWeek = today.getDay();
            const daysToSubtract = dayOfWeek === 0 ? 6 : dayOfWeek - 1;
            mondayThisWeek.setDate(today.getDate() - daysToSubtract);
            startDate = mondayThisWeek.toISOString().split('T')[0];
            
            const sundayThisWeek = new Date(mondayThisWeek);
            sundayThisWeek.setDate(mondayThisWeek.getDate() + 6);
            endDate = sundayThisWeek.toISOString().split('T')[0];
            break;
        case 'past_week':
            const mondayLastWeek = new Date(today);
            const dayOfWeekLast = today.getDay();
            const daysToSubtractLast = dayOfWeekLast === 0 ? 6 : dayOfWeekLast - 1;
            mondayLastWeek.setDate(today.getDate() - daysToSubtractLast - 7);
            startDate = mondayLastWeek.toISOString().split('T')[0];
            
            const sundayLastWeek = new Date(mondayLastWeek);
            sundayLastWeek.setDate(mondayLastWeek.getDate() + 6);
            endDate = sundayLastWeek.toISOString().split('T')[0];
            break;
        default:
            return null;
    }
    
    return { startDate, endDate };
}

function getMondayOfWeek(date) {
    const d = new Date(date);
    const day = d.getDay();
    const diff = d.getDate() - day + (day === 0 ? -6 : 1);
    return new Date(d.setDate(diff));
}

function calculateEndDate(startDate) {
    const monday = getMondayOfWeek(startDate);
    const sunday = new Date(monday);
    sunday.setDate(monday.getDate() + 6);
    return sunday.toISOString().split('T')[0];
}

function detectFilterFromDateRange(startDate, endDate) {
    const start = new Date(startDate);
    const end = new Date(endDate);
    const today = new Date();
    
    const mondayThisWeek = new Date(today);
    const dayOfWeek = today.getDay();
    const daysToSubtract = dayOfWeek === 0 ? 6 : dayOfWeek - 1;
    mondayThisWeek.setDate(today.getDate() - daysToSubtract);
    
    const mondayLastWeek = new Date(mondayThisWeek);
    mondayLastWeek.setDate(mondayThisWeek.getDate() - 7);
    
    if (start.toDateString() === mondayThisWeek.toDateString() && 
        end.toDateString() === new Date(mondayThisWeek.getTime() + 6 * 24 * 60 * 60 * 1000).toDateString()) {
        return 'this_week';
    } else if (start.toDateString() === mondayLastWeek.toDateString() && 
               end.toDateString() === new Date(mondayLastWeek.getTime() + 6 * 24 * 60 * 60 * 1000).toDateString()) {
        return 'past_week';
    } else {
        return 'custom';
    }
}

// Make functions globally available
window.loadScheduleDirectly = loadScheduleDirectly;
window.saveScheduleDirectly = saveScheduleDirectly;
window.loadExistingSchedules = loadExistingSchedules;
window.loadExistingSchedulesForAll = loadExistingSchedulesForAll;
window.exportToExcel = exportToExcel;
window.exportToPDF = exportToPDF;
window.showImportModal = showImportModal;
window.hideImportModal = hideImportModal;
window.importExcel = importExcel;
window.showImportAlert = showImportAlert;
window.closeImportAlert = closeImportAlert;
</script>
