@extends('app.structure')
@section('content')

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateFilter = document.querySelector('select[name="date_filter"]');
    const scheduleTables = document.querySelectorAll('.schedule-report-table');
    
    function updateColumnVisibility() {
        if (dateFilter.value === 'now') {
            // Hide other date columns in all tables and set table width to 60%
            scheduleTables.forEach(table => {
                const otherDateColumns = table.querySelectorAll('.other-date-column');
                otherDateColumns.forEach(col => {
                    col.style.display = 'none';
                });
                // Set table width to 60% for NOW filter only
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
    dateFilter.addEventListener('change', updateColumnVisibility);
});
</script>

<style>
    /* Hide other date columns when filter is NOW */
    .other-date-column {
        transition: all 0.3s ease;
    }
    
    .hide-other-dates .other-date-column {
        display: none !important;
    }
    
    /* Highlight today column when filter is NOW */
    .today-column {
        transition: all 0.3s ease;
    }
    
    .filter-now .today-column {
        background-color: #fff3cd !important;
        border: 2px solid #ffc107 !important;
    }
</style>

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
                            @if($dateFilter === 'now')
                                <span class="text-primary font-weight-bold">
                                    <i class="ki-outline ki-watch"></i> Staff have a Work Schedule - {{ date('d M Y, H:i') }}
                                </span>
                            @else
                                <span class="text-muted">{{ date('d M Y', strtotime($startDate)) }} - {{ date('d M Y', strtotime($endDate)) }}</span>
                            @endif
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
                <form method="GET" action="{{ route('daily-schedules.weekly-report') }}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <label>Date Filter:</label>
                                <select class="form-control" name="date_filter">
                                    <option value="this_week" {{ $dateFilter == 'this_week' ? 'selected' : '' }}>This Week</option>
                                    <option value="past_week" {{ $dateFilter == 'past_week' ? 'selected' : '' }}>Past Week</option>
                                    <option value="next_week" {{ $dateFilter == 'next_week' ? 'selected' : '' }}>Next Week</option>
                                    <option value="now" {{ $dateFilter == 'now' ? 'selected' : '' }}>NOW (Staff Sedang Bekerja)</option>
                                    <option value="custom" {{ $dateFilter == 'custom' ? 'selected' : '' }}>Custom Range</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Week Range:</label>
                                <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                                <small class="form-text text-muted">Select Monday to show full week</small>
                            </div>
                            <div class="col-md-2">
                                <label>Division:</label>
                                <select class="form-control" name="division_id" onchange="this.form.submit()">
                                    <option value="">All Divisions</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}" {{ $divisionId == $division->id ? 'selected' : '' }}>
                                            {{ $division->ud_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>User Position:</label>
                                <select class="form-control" name="position_id" onchange="this.form.submit()">
                                    <option value="">All Positions</option>
                                    @foreach($userPositions as $position)
                                        <option value="{{ $position->id }}" {{ $positionId == $position->id ? 'selected' : '' }}>
                                            {{ $position->up_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Shift:</label>
                                <select class="form-control" name="shift_id" onchange="this.form.submit()">
                                    <option value="">All Shifts</option>
                                    @foreach($shiftCodes as $shift)
                                        <option value="{{ $shift->id }}" {{ $shiftId == $shift->id ? 'selected' : '' }}>
                                            {{ $shift->sc_code }} - {{ $shift->sc_shift_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Staff Name:</label>
                                <input type="text" class="form-control" name="user_name" value="{{ $userName }}" placeholder="Enter staff name">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end py-6">
                        <button type="submit" class="btn btn-primary btn-sm mr-3">
                            <i class="ki-outline ki-filter-search"></i> Filter
                        </button>
                        <a href="{{ route('daily-schedules.weekly-report') }}" class="btn btn-secondary btn-sm">
                            <i class="ki-outline ki-cross"></i> Clear
                        </a>                
                    </div>
                </form>
            </div>

            <!-- Report Content -->
            <div class="card card-custom">
                <div class="card-header flex-wrap py-3">
                    <!-- <div class="card-title">
                        <h3 class="card-label">
                            Weekly Schedule Report
                            <span class="text-muted pt-2 font-size-sm d-block">
                                {{ date('l, d F Y', strtotime($startDate)) }} - {{ date('l, d F Y', strtotime($endDate)) }}
                            </span>
                        </h3>
                    </div> -->
                    <div class="card-toolbar d-flex justify-content-end w-100">
                        <!-- <button type="button" class="btn btn-light-success btn-sm" onclick="window.print()">
                            <i class="ki-outline ki-printer"></i> Print
                        </button> -->
                        <button type="button" class="btn btn-light-green btn-sm ml-2" onclick="exportToExcel()">
                            <i class="ki-outline ki-file-down"></i> Export Excel
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm ml-2" onclick="exportToPDF()">
                            <i class="ki-outline ki-file-down"></i> Export PDF
                        </button>
                    </div>
                </div>
                <div class="card-body">
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
                        @foreach($groupedSchedules as $divisionName => $users)
                            <!-- Division Header -->
                            <div class="division-header mb-4">
                                <h4 class="text-primary font-weight-bold border-bottom pb-2">
                                    <i class="ki ki-home-2"></i> {{ $divisionName }}
                                    <span class="badge badge-light ml-2">{{ count($users) }} staff</span>
                                </h4>
                            </div>

                            <!-- Schedule Table for this Division -->
                            <div class="table-responsive mb-5">
                                <table class="table table-bordered table-hover schedule-report-table">
                                    <thead class="bg-light-primary">
                                        <tr>
                                            <th rowspan="2" class="text-center align-middle" style="min-width: 240px !important;max-width: 240px !important">NAMA</th>
                                            @foreach($weekDates as $date)
                                                @php
                                                    $isToday = date('Y-m-d') === $date;
                                                    $dateClass = $isToday ? 'today-column' : 'other-date-column';
                                                @endphp
                                                <th colspan="2" class="{{ $dateClass }} text-center" style="min-width: 120px;">
                                                    <div class="font-weight-bold">{{ date('l', strtotime($date)) }}</div>
                                                    <div class="font-size-sm">{{ date('d M', strtotime($date)) }}</div>
                                                </th>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            @foreach($weekDates as $date)
                                                @php
                                                    $isToday = date('Y-m-d') === $date;
                                                    $dateClass = $isToday ? 'today-column' : 'other-date-column';
                                                @endphp
                                                <th class="text-center bg-light-info {{ $dateClass }}" style="width: 60px;">
                                                    <small>Shift Name</small>
                                                </th>
                                                <th class="text-center bg-light-warning {{ $dateClass }}" style="width: 60px;">
                                                    <small>Start-End Shift</small>
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                            <tr>
                                                <td class="align-middle font-weight-bold" style="font-size: 14px; width: 200px;">
                                                    {{ $user['u_name'] }}
                                                    <br>
                                                    <small class="text-muted">{{ $user['u_nip'] }}</small>
                                                </td>
                                                @foreach($weekDates as $date)
                                                    @php
                                                        $schedule = $user['schedules'][$date] ?? null;
                                                        $shiftCode = $schedule['sc_code'] ?? '';
                                                        $shiftName = $schedule['sc_shift_name'] ?? '';
                                                        $startTime = $schedule['sc_start_time'] ?? '';
                                                        $endTime = $schedule['sc_end_time'] ?? '';
                                                        $isToday = date('Y-m-d') === $date;
                                                        $dateClass = $isToday ? 'today-column' : 'other-date-column';
                                                        
                                                        // Color coding based on shift name (same as monthly report)
                                                        $cellClass = '';
                                                        if ($schedule && $shiftName) {
                                                            if (strpos(strtolower($shiftName), 'shift 1') !== false || strpos(strtolower($shiftName), 'shift 0') !== false) {
                                                                $cellClass = 'bg-light-info'; // light blue for shift 1 & 0
                                                            } elseif (strpos(strtolower($shiftName), 'shift 2') !== false) {
                                                                $cellClass = 'bg-light-success'; // light green for shift 2
                                                            } elseif (strpos(strtolower($shiftName), 'full') !== false) {
                                                                $cellClass = 'bg-light-warning'; // light yellow for full
                                                            } elseif (strpos(strtolower($shiftName), 'sakit') !== false || strpos(strtolower($shiftName), 'libur') !== false || strpos(strtolower($shiftName), 'izin') !== false) {
                                                                $cellClass = 'bg-light-danger'; // light red for sakit/libur/izin
                                                            } else {
                                                                $cellClass = 'bg-light-primary'; // default blue for other shifts
                                                            }
                                                        } elseif ($shiftCode) {
                                                            // Fallback to old logic for shift codes
                                                            if (in_array($shiftCode, ['L', 'LL', 'LPH'])) {
                                                                $cellClass = 'bg-light-success'; // Green for leave
                                                            } elseif ($shiftCode == 'N/A') {
                                                                $cellClass = 'bg-light-secondary'; // Gray for not set
                                                            } else {
                                                                $cellClass = 'bg-light-primary'; // Blue for work shifts
                                                            }
                                                        }
                                                    @endphp
                                                    
                                                    <!-- Shift Name Column -->
                                                    <td class="text-center align-middle {{ $cellClass }} {{ $dateClass }}" style="font-size: 13px;">
                                                        @if($schedule)
                                                            <div class="font-weight-bold">{{ $shiftName ?: $shiftCode }}</div>
                                                            @if($shiftName)
                                                                <small class="text-muted">{{ $shiftCode }}</small>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    
                                                    <!-- Start Shift Column (Time Range) -->
                                                    <td class="text-center align-middle {{ $cellClass }} {{ $dateClass }}" style="font-size: 13px;">
                                                        @if($schedule && $shiftName)
                                                            @php
                                                                // Check if it's a holiday/leave shift
                                                                $isHoliday = false;
                                                                if (strpos(strtolower($shiftName), 'libur') !== false || 
                                                                     strpos(strtolower($shiftName), 'sakit') !== false || 
                                                                     strpos(strtolower($shiftName), 'izin') !== false) {
                                                                    $isHoliday = true;
                                                                }
                                                            @endphp
                                                            
                                                            @if($isHoliday)
                                                                <span class="text-muted">-</span>
                                                            @elseif($startTime && $endTime)
                                                                <span class="font-weight-bold">{{ date('H:i', strtotime($startTime)) }} - {{ date('H:i', strtotime($endTime)) }}</span>
                                                            @elseif($startTime)
                                                                <span class="font-weight-bold">{{ date('H:i', strtotime($startTime)) }}</span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        @elseif($schedule && $shiftCode)
                                                            @if(in_array($shiftCode, ['L', 'LL', 'LPH']))
                                                                <span class="text-muted">-</span>
                                                            @elseif($startTime && $endTime)
                                                                <span class="font-weight-bold">{{ date('H:i', strtotime($startTime)) }} - {{ date('H:i', strtotime($endTime)) }}</span>
                                                            @elseif($startTime)
                                                                <span class="font-weight-bold">{{ date('H:i', strtotime($startTime)) }}</span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <div class="text-muted">
                                @if($dateFilter === 'now')
                                    <i class="ki-outline ki-watch icon-2x text-primary"></i>
                                    <h4 class="mt-3 text-info">No Staff Currently Working</h4>
                                    <p>There are no staff working at this hour ({{ date('H:i') }}).</p>
                                    <small class="text-muted">Filter displays staff with schedules: Start Work ≤ NOW ≤ End Work</small>
                                @else
                                    <i class="ki ki-information icon-2x"></i>
                                    <h4 class="mt-3">No Schedule Data Found</h4>
                                    <p>No schedule data available for the selected week and filters.</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>

<!-- Color Legend -->
<!-- <div class="card card-custom mt-3">
    <div class="card-body py-3">
        <div class="row align-items-center">
            <div class="col-md-2">
                <strong>Shift Color Legend:</strong>
            </div>
            <div class="col-md-10">
                <span class="badge bg-light-info text-info mr-2">
                    <span class="bullet bullet-bar bg-info mr-2"></span>Shift 1 & Shift 0
                </span>
                <span class="badge bg-light-success text-success mr-2">
                    <span class="bullet bullet-bar bg-success mr-2"></span>Shift 2
                </span>
                <span class="badge bg-light-warning text-warning mr-2">
                    <span class="bullet bullet-bar bg-warning mr-2"></span>Full & Full Shift 0
                </span>
                <span class="badge bg-light-danger text-danger mr-2">
                    <span class="bullet bullet-bar bg-danger mr-2"></span>Sakit, Libur & Izin
                </span>
                <span class="badge bg-light-primary text-primary mr-2">
                    <span class="bullet bullet-bar bg-primary mr-2"></span>Other Shifts
                </span>
            </div>
        </div>
    </div>
</div> -->

<style>
@media print {
    .card-header .card-toolbar,
    .subheader,
    .card:first-child {
        display: none !important;
    }
    
    .schedule-report-table {
        font-size: 10px !important;
    }
    
    .schedule-report-table td,
    .schedule-report-table th {
        padding: 4px !important;
    }
    
    .division-header {
        page-break-before: always;
    }
    
    .division-header:first-child {
        page-break-before: auto;
    }
}

.schedule-report-table {
    font-size: 12px;
    border-collapse: collapse;
    transition: width 0.3s ease;
}

/* Table width for NOW filter */
.schedule-report-table.filter-now {
    width: 50% !important;
    margin: 0 auto !important;
}

.schedule-report-table th,
.schedule-report-table td {
    border: 1px solid #dee2e6 !important;
    vertical-align: middle;
    padding: 8px;
}

.schedule-report-table th {
    background-color: #f8f9fa !important;
    font-weight: 600;
}

.division-header {
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.division-header:first-child {
    margin-top: 0;
}

.bg-light-success {
    background-color: rgba(26, 188, 156, 0.1) !important;
}

.bg-light-danger {
    background-color: rgba(231, 76, 60, 0.1) !important;
}

.bg-light-primary {
    background-color: rgba(52, 152, 219, 0.1) !important;
}

.bg-light-secondary {
    background-color: rgba(149, 165, 166, 0.1) !important;
}

.bg-light-info {
    background-color: rgba(23, 162, 184, 0.1) !important;
}

.bg-light-warning {
    background-color: rgba(255, 193, 7, 0.1) !important;
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
.table thead th {
    min-width: 120px !important;
}

</style>

<script>
// Two-way synchronization between date filter and week range
document.addEventListener('DOMContentLoaded', function() {
    const dateFilterSelect = document.querySelector('select[name="date_filter"]');
    const startDateInput = document.querySelector('input[name="start_date"]');
    
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
                    document.querySelector('form[method="GET"]').submit();
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
                        document.querySelector('form[method="GET"]').submit();
                    }, 100);
                }
            }
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

    const form = document.querySelector('form[method="GET"]');
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
        document.body.removeChild(loadingIndicator);
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

@include('app._partials.js')
@endsection
