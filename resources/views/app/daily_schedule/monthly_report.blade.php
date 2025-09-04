@extends('app.structure')
@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-1">
                <!--begin::Page Heading-->
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <!--begin::Page Title-->
                    <h5 class="text-dark font-weight-bold my-1 mr-5">{{ $data['subtitle'] }}</h5>
                    <!--end::Page Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item text-muted">
                            <span class="text-muted">{{ date('F Y', strtotime($startDate)) }}</span>
                        </li>
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page Heading-->
            </div>
            <!--end::Info-->
        </div>
    </div>
    <!--end::Subheader-->

    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <!-- Filters Section -->
            <div class="card card-custom mb-5">
                <div class="card-header flex-wrap py-3">
                    <div class="card-title">
                        <h3 class="card-label">Filters</h3>
                    </div>
                </div>
                <form method="GET" action="{{ route('daily-schedules.monthly-report') }}" class="mb-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2 pr-2">
                                <label>Month Filter:</label>
                                <select class="form-control" name="month_filter" id="month_filter">
                                    <option value="this_month" {{ request('month_filter', 'this_month') == 'this_month' ? 'selected' : '' }}>This Month</option>
                                    <option value="last_month" {{ request('month_filter', 'this_month') == 'last_month' ? 'selected' : '' }}>Past Month</option>
                                    <option value="next_month" {{ request('month_filter', 'this_month') == 'next_month' ? 'selected' : '' }}>Next Month</option>
                                    <option value="custom" {{ request('month_filter', 'this_month') == 'custom' ? 'selected' : '' }}>Custom Month</option>
                                </select>
                            </div>
                            <div class="col-md-2 pr-2">
                                <label>Custom Month:</label>
                                <input type="month" class="form-control" name="month" id="custom_month" value="{{ $month }}">
                            </div>
                            <div class="col-md-2 pr-2">
                                <label>Division:</label>
                                <select class="form-control" name="division_id" id="division_filter">
                                    <option value="">All Divisions</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}" {{ $divisionId == $division->id ? 'selected' : '' }}>
                                            {{ $division->ud_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 pr-2">
                                <label>User Position:</label>
                                <select class="form-control" name="position_id" id="position_filter">
                                    <option value="">All Positions</option>
                                    @foreach($userPositions as $position)
                                        <option value="{{ $position->id }}" {{ $positionId == $position->id ? 'selected' : '' }}>
                                            {{ $position->up_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 pr-2">
                                <label>Shift:</label>
                                <select class="form-control" name="shift_id" id="shift_filter">
                                    <option value="">All Shifts</option>
                                    @foreach($shiftCodes as $shift)
                                        <option value="{{ $shift->id }}" {{ $shiftId == $shift->id ? 'selected' : '' }}>
                                            {{ $shift->sc_code }} - {{ $shift->sc_shift_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 pr-2">
                                <label>Staff Name:</label>
                                <input type="text" class="form-control" name="user_name" value="{{ $userName }}" placeholder="Enter staff name">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end py-6">
                        <button type="submit" class="btn btn-primary btn-sm mr-3">
                            <i class="ki-outline ki-filter-search"></i> Filter
                        </button>
                        <a href="{{ route('daily-schedules.monthly-report') }}" class="btn btn-secondary btn-sm">
                            <i class="ki-outline ki-cross"></i> Clear
                        </a>
                    </div>
                </form>

            </div>

            <!-- Report Content -->
            <div class="card card-custom">
                <div class="card-header flex-wrap py-3">
                    <div class="card-title">
                        <!-- <h3 class="card-label">
                            Monthly Schedule Report
                            <span class="text-muted pt-2 font-size-sm d-block">
                                {{ date('F Y', strtotime($startDate)) }}
                            </span>
                        </h3> -->
                    </div>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-light-green btn-sm ml-2" onclick="exportToExcel()">
                            <i class="ki-outline ki-file-down"></i> Export Excel
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm ml-2" onclick="exportToPDF()">
                            <i class="ki-outline ki-file-down"></i> Export PDF
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Color Legend -->
                    <div class="color-legend mb-4">
                        <h6 class="text-muted mb-2">Shift Color Legend:</h6>
                        <div class="d-flex flex-wrap">
                            <div class="legend-item mr-3 mb-2">
                                <span class="legend-color" style="background-color: #eaf5fb;"></span>
                                <span class="legend-text">Shift 1 & Shift 0</span>
                            </div>
                            <div class="legend-item mr-3 mb-2">
                                <span class="legend-color" style="background-color: #e5f6f3;"></span>
                                <span class="legend-text">Shift 2</span>
                            </div>
                            <div class="legend-item mr-3 mb-2">
                                <span class="legend-color" style="background-color: #FFF9ED;"></span>
                                <span class="legend-text">Full & Full Shift 0</span>
                            </div>
                            <div class="legend-item mr-3 mb-2">
                                <span class="legend-color" style="background-color: #f8e8e6;"></span>
                                <span class="legend-text">Sakit, Libur & Izin</span>
                            </div>
                        </div>
                    </div>
                    
                    @if(count($groupedSchedules) > 0)
                        @foreach($groupedSchedules as $divisionName => $divisionUsers)
                            <!-- Division Header -->
                            <div class="division-header mb-4">
                                <h4 class="text-primary font-weight-bold border-bottom pb-2">
                                    <i class="ki ki-home-2"></i> {{ $divisionName }}
                                    <span class="badge badge-light ml-2">{{ count($divisionUsers) }} staff</span>
                                </h4>
                            </div>

                            <!-- Calendar View for each user -->
                            @foreach($divisionUsers as $userId => $userData)
                                <div class="user-calendar mb-5">
                                    <div class="user-info mb-3">
                                        <h5 class="text-dark font-weight-bold">
                                            <i class="ki ki-user"></i> {{ $userData['u_name'] }}
                                            <span class="text-muted ml-2">({{ $userData['u_nip'] }})</span>
                                            <span class="text-muted ml-2">- {{ $userData['position_name'] }}</span>
                                        </h5>
                                    </div>
                                    
                                    <!-- Calendar Grid -->
                                    <div class="calendar-grid">
                                        <!-- Calendar Header -->
                                        <div class="calendar-header">
                                            <div class="calendar-row">
                                                <div class="calendar-cell header-cell">Mon</div>
                                                <div class="calendar-cell header-cell">Tue</div>
                                                <div class="calendar-cell header-cell">Wed</div>
                                                <div class="calendar-cell header-cell">Thu</div>
                                                <div class="calendar-cell header-cell">Fri</div>
                                                <div class="calendar-cell header-cell weekend">Sat</div>
                                                <div class="calendar-cell header-cell weekend">Sun</div>
                                            </div>
                                        </div>
                                        
                                        <!-- Calendar Weeks -->
                                        @php
                                            $weeks = [];
                                            $currentWeek = [];
                                            
                                            // Start with Monday (day_of_week = 1)
                                            foreach($calendarDates as $dateInfo) {
                                                $currentWeek[] = $dateInfo;
                                                
                                                if($dateInfo['day_of_week'] == 7) { // Sunday
                                                    $weeks[] = $currentWeek;
                                                    $currentWeek = [];
                                                }
                                            }
                                            
                                            // Add remaining days if any
                                            if (!empty($currentWeek)) {
                                                $weeks[] = $currentWeek;
                                            }
                                        @endphp
                                        
                                        @foreach($weeks as $weekIndex => $week)
                                            <div class="calendar-row">
                                                @foreach($week as $dateInfo)
                                                    @php
                                                        $date = $dateInfo['date'];
                                                        $schedule = $userData['schedules'][$date] ?? null;
                                                        $isWeekend = $dateInfo['is_weekend'];
                                                        $isToday = $date == date('Y-m-d');
                                                        $isCurrentMonth = $dateInfo['is_current_month'] ?? true;
                                                        
                                                        // Color coding based on shift name
                                                        $cellColor = '#ffffff'; // default white
                                                        if ($schedule) {
                                                            if (strpos(strtolower($schedule['sc_shift_name']), 'shift 1') !== false || strpos(strtolower($schedule['sc_shift_name']), 'shift 0') !== false) {
                                                                $cellColor = '#eaf5fb'; // light blue
                                                            } elseif (strpos(strtolower($schedule['sc_shift_name']), 'shift 2') !== false) {
                                                                $cellColor = '#e5f6f3'; // light green
                                                            } elseif (strpos(strtolower($schedule['sc_shift_name']), 'full') !== false) {
                                                                $cellColor = '#FFF9ED'; // light yellow
                                                            } elseif (strpos(strtolower($schedule['sc_shift_name']), 'sakit') !== false || strpos(strtolower($schedule['sc_shift_name']), 'libur') !== false || strpos(strtolower($schedule['sc_shift_name']), 'izin') !== false) {
                                                                $cellColor = '#f8e8e6'; // light red
                                                            }
                                                        }
                                                    @endphp
                                                    
                                                    <div class="calendar-cell {{ $isWeekend ? 'weekend' : '' }} {{ $isToday ? 'today' : '' }} {{ !$isCurrentMonth ? 'other-month' : '' }}" style="background-color: {{ $schedule ? $cellColor : 'transparent' }};">
                                                        <div class="date-header">
                                                            <span class="date-number">{{ $dateInfo['day'] }}</span>
                                                            <span class="date-name">{{ $dateInfo['day_name'] }}</span>
                                                        </div>
                                                        
                                                        @if($schedule)
                                                            <div class="schedule-info" style="color: #333;">
                                                                <div class="shift-code">{{ $schedule['sc_code'] }}</div>
                                                                <div class="shift-name">{{ $schedule['sc_shift_name'] }}</div>
                                                                <div class="shift-time">
                                                                    {{ date('H:i', strtotime($schedule['sc_start_time'])) }} - {{ date('H:i', strtotime($schedule['sc_end_time'])) }}
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="no-schedule">
                                                                <span class="text-muted">No Schedule</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                                
                                                <!-- Fill remaining cells if week is incomplete -->
                                                @for($i = count($week); $i < 7; $i++)
                                                    <div class="calendar-cell empty"></div>
                                                @endfor
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="ki ki-calendar-8 text-muted" style="font-size: 3rem;"></i>
                            <h4 class="text-muted mt-3">No schedules found</h4>
                            <p class="text-muted">Try adjusting your filters to see more results.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>

<!-- Custom CSS for Calendar -->
<style>
.calendar-grid {
    border: 1px solid #e1e5e9;
    border-radius: 8px;
    overflow: hidden;
}

.calendar-header {
    background-color: #f8f9fa;
}

.calendar-row {
    display: flex;
    border-bottom: 1px solid #e1e5e9;
}

.calendar-row:last-child {
    border-bottom: none;
}

.calendar-cell {
    flex: 1;
    min-height: 120px;
    border-right: 1px solid #e1e5e9;
    padding: 8px;
    position: relative;
}

.calendar-cell:last-child {
    border-right: none;
}

.header-cell {
    background-color: #f8f9fa;
    font-weight: bold;
    text-align: center;
    padding: 12px 8px;
    min-height: auto;
}

.weekend {
    background-color: #f8f9fa;
}

.today {
    background-color: #fff3cd;
    border: 2px solid #ffc107;
}

.date-header {
    text-align: center;
    margin-bottom: 8px;
}

.date-number {
    font-size: 1.2rem;
    font-weight: bold;
    display: block;
}

.date-name {
    font-size: 0.9rem;
    color: #6c757d;
}

.schedule-info {
    padding: 6px;
    border-radius: 4px;
    text-align: center;
    font-size: 0.95rem;
}

.shift-code {
    font-weight: bold;
    margin-bottom: 2px;
}

.shift-name {
    margin-bottom: 2px;
}

.shift-time {
    font-size: 0.8rem;
    opacity: 0.9;
}

.no-schedule {
    text-align: center;
    padding: 20px 0;
    color: #6c757d;
}

.other-month {
    background-color: #f8f9fa;
    opacity: 0.6;
}

.other-month .date-number,
.other-month .date-name {
    color: #adb5bd;
}

.other-month .no-schedule {
    color: #adb5bd;
}

.empty {
    background-color: #f8f9fa;
}

.user-calendar {
    border: 1px solid #e1e5e9;
    border-radius: 8px;
    padding: 20px;
    background-color: white;
}

.user-info {
    border-bottom: 1px solid #e1e5e9;
    padding-bottom: 10px;
}

/* Color Legend Styles */
.color-legend {
    background-color: #f8f9fa;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #e1e5e9;
}

.legend-item {
    display: flex;
    align-items: center;
    background-color: white;
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid #e1e5e9;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.legend-color {
    width: 20px;
    height: 20px;
    border-radius: 4px;
    margin-right: 8px;
    border: 1px solid #dee2e6;
}



@media (max-width: 768px) {
    .calendar-cell {
        min-height: 80px;
        padding: 4px;
    }
    
    .date-number {
        font-size: 1rem;
    }
    
    .shift-code, .shift-name, .shift-time {
        font-size: 0.9rem;
    }
    
    .calendar-grid {
        font-size: 0.9rem;
    }
    
    .legend-item {
        margin-bottom: 8px;
    }
}
</style>

@endsection

<script>
function exportToExcel() {
    console.log('Export Excel clicked');
    
    // Show loading indicator
    const loadingIndicator = document.createElement('div');
    loadingIndicator.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="sr-only">Loading...</span></div>';
    loadingIndicator.className = 'position-fixed top-50 start-50 translate-middle';
    document.body.appendChild(loadingIndicator);
    
    // Get current filters from form
    const form = document.querySelector('form[method="GET"]');
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
    const form = document.querySelector('form[method="GET"]');
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
                
                // ✅ Auto-submit form to update the page with new month
                console.log('Auto-submitting form to update page...');
                const form = document.querySelector('form[method="GET"]');
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
            
            // ✅ Auto-submit form to update the page with new month
            console.log('Auto-submitting form to update page...');
            const form = document.querySelector('form[method="GET"]');
            if (form) {
                form.submit();
            }
        });
        
        // ✅ Add event listeners for division and shift filters
        const divisionFilter = document.getElementById('division_filter');
        const positionFilter = document.getElementById('position_filter');
        const shiftFilter = document.getElementById('shift_filter');
        
        if (divisionFilter) {
            divisionFilter.addEventListener('change', function() {
                console.log('Division filter changed to:', this.value);
                // Auto-submit form to update the page
                const form = document.querySelector('form[method="GET"]');
                if (form) {
                    form.submit();
                }
            });
        }
        
        if (positionFilter) {
            positionFilter.addEventListener('change', function() {
                console.log('Position filter changed to:', this.value);
                // Auto-submit form to update the page
                const form = document.querySelector('form[method="GET"]');
                if (form) {
                    form.submit();
                }
            });
        }
        
        if (shiftFilter) {
            shiftFilter.addEventListener('change', function() {
                console.log('Shift filter changed to:', this.value);
                // Auto-submit form to update the page
                const form = document.querySelector('form[method="GET"]');
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

@include('app._partials.js')
