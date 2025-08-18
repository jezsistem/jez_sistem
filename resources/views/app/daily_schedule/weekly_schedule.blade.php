@extends('app.structure')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!--begin::Content-->
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
                </div>
                <!--end::Page Heading-->
            </div>
            <!--end::Info-->
            
            <!--begin::User Info-->
            <div class="d-flex align-items-center">
                <span class="badge badge-dark mr-2">
                    {{ $currentUser->up_code ?? 'Unknown Position' }}
                </span>
                @if($currentUser->current_division_name)
                <span class="badge badge-secondary">
                   {{ $currentUser->current_division_name }}
                </span>
                @endif
            </div>
            <!--end::User Info-->
        </div>
    </div>
    <!--end::Subheader-->

    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <form method="GET" id="filterForm">
                                <div class="card-toolbar d-flex justify-content-between w-100">
                                    <div class="row w-50">
                                        @if(in_array($currentUser->up_code ?? '', ['DIRECTOR', 'MANAGER']))
                                        <div class="col-md-3">
                                        <!-- <label for="division_filter">Division</label> -->
                                        <select class="form-control" id="division_filter" name="division_id">
                                            <option value="">All Divisions</option>
                                            @foreach($divisions as $division)
                                                    <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>{{ $division->ud_code }} - {{ $division->ud_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif
                                    <div class="col-md-3">
                                        <select class="form-control" id="date_filter" name="date_filter">
                                            <option value="this_week" {{ request('date_filter', 'this_week') == 'this_week' ? 'selected' : '' }}>This Week</option>
                                            <option value="past_week" {{ request('date_filter', 'this_week') == 'past_week' ? 'selected' : '' }}>Past Week</option>
                                            <option value="this_month" {{ request('date_filter', 'this_week') == 'this_month' ? 'selected' : '' }}>This Month</option>
                                            <option value="last_month" {{ request('date_filter', 'this_week') == 'last_month' ? 'selected' : '' }}>Past Month</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control w-100" id="search_filter" name="search" placeholder="Search by name or NIP..." value="{{ $search ?? '' }}">
                                    </div>
                                    @if(in_array($currentUser->up_code ?? '', ['DIRECTOR', 'MANAGER']))
                                    <div class="col-md-3">
                                        <label>&nbsp;</label>
                                            <button type="button" class="btn btn-dark btn-block" id="load_schedule" onclick="loadScheduleDirectly()">
                                                <i class="ki-outline ki-filter-search"></i> Load Schedule
                                            </button>
                                        </div>
                                    @endif
                                    </div>
                                                                    <div class="d-flex align-items-center">
                                        <button type="button" class="btn btn-light btn-sm mr-2" onclick="loadExistingSchedules()">
                                            <i class="ki-outline ki-arrows-circle"></i> Load Schedules
                                        </button>
                                        <!-- Export Buttons -->
                                        <button type="button" class="btn btn-light-green btn-sm mr-2" onclick="exportToExcel()">
                                            <i class="ki-outline ki-file-down"></i> Export Excel
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm mr-2" onclick="exportToPDF()">
                                            <i class="ki-outline ki-file-down"></i> Export PDF
                                        </button>
                                        <!-- <a href="{{ route('daily-schedules.index') }}" class="btn btn-secondary">
                                            <i class="ki-outline ki-arrow-left"></i> Back to Daily Schedules
                                        </a> -->
                        </div>
                    </div>
                </form>
            </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-checkable" id="weeklyScheduleTable">
                                    <thead class="bg-light text-dark">
                                        <tr>
                                            <th style="min-width: 80px;">NIP</th>
                                            <th style="min-width: 200px;">Nama Staff</th>
                                            <th style="min-width: 150px;">Divisi</th>
                                            <th style="min-width: 100px;">User Type</th>
                                            <th style="min-width: 100px;" class="date-header" data-date="{{ date('Y-m-d', strtotime('monday this week')) }}" id="date-header-0">{{ date('D d-M', strtotime('monday this week')) }}</th>
                                            <th style="min-width: 100px;" class="date-header" data-date="{{ date('Y-m-d', strtotime('monday this week +1 day')) }}" id="date-header-1">{{ date('D d-M', strtotime('monday this week +1 day')) }}</th>
                                            <th style="min-width: 100px;" class="date-header" data-date="{{ date('Y-m-d', strtotime('monday this week +2 day')) }}" id="date-header-2">{{ date('D d-M', strtotime('monday this week +2 day')) }}</th>
                                            <th style="min-width: 100px;" class="date-header" data-date="{{ date('Y-m-d', strtotime('monday this week +3 day')) }}" id="date-header-3">{{ date('D d-M', strtotime('monday this week +3 day')) }}</th>
                                            <th style="min-width: 100px;" class="date-header" data-date="{{ date('Y-m-d', strtotime('monday this week +4 day')) }}" id="date-header-4">{{ date('D d-M', strtotime('monday this week +4 day')) }}</th>
                                            <th style="min-width: 100px;" class="date-header" data-date="{{ date('Y-m-d', strtotime('monday this week +5 day')) }}" id="date-header-5">{{ date('D d-M', strtotime('monday this week +5 day')) }}</th>
                                            <th style="min-width: 100px;" class="date-header" data-date="{{ date('Y-m-d', strtotime('monday this week +6 day')) }}" id="date-header-6">{{ date('D d-M', strtotime('monday this week +6 day')) }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="schedule_tbody">
                                        @foreach($users as $user)
                                        @php
                                            // Get shift codes for this user's type
                                            $userType = $user->ut_name ?: 'FULL TIME'; // Default to FULL TIME if no user type
                                            $baseShiftCodes = $shiftCodesByType[$userType] ?? $shiftCodesByType['FULL TIME'];
                                            
                                            // Get existing shift codes for this user to ensure they appear in dropdown
                                            $userExistingShifts = $existingSchedules->where('user_id', $user->id);
                                            $existingShiftIds = $userExistingShifts->pluck('sc_id')->unique();
                                            
                                            // Merge base shift codes with existing ones to ensure compatibility
                                            $availableShiftCodes = collect($baseShiftCodes);
                                            
                                            // Add any existing shift codes that aren't in the base list
                                            foreach($existingShiftIds as $existingShiftId) {
                                                if($existingShiftId && !$availableShiftCodes->contains('id', $existingShiftId)) {
                                                    $existingShift = $shiftCodes->firstWhere('id', $existingShiftId);
                                                    if($existingShift) {
                                                        $availableShiftCodes->push($existingShift);
                                                    }
                                                }
                                            }
                                            
                                            // Sort by shift code
                                            $availableShiftCodes = $availableShiftCodes->sortBy('sc_code');
                                        @endphp
                                        <tr>
                                            <td>{{ $user->u_nip ?? '-' }}</td>
                                            <td>{{ $user->u_name }}</td>
                                            <td>{{ $user->ud_name ?? '-' }}</td>
                                            <td><small class="badge badge-primary">{{ $userType }}</small></td>
                                            <td class="schedule-cell" data-user-id="{{ $user->id }}" data-date="{{ date('Y-m-d', strtotime('monday this week')) }}">
                                                <select class="form-control shift-select" style="font-size: 1rem;" data-user-id="{{ $user->id }}" data-date="{{ date('Y-m-d', strtotime('monday this week')) }}" onchange="saveScheduleDirectly(this)">
                                                    <option value="">-</option>
                                                    @foreach($availableShiftCodes as $shiftCode)
                                                        <option value="{{ $shiftCode->id }}">{{ $shiftCode->sc_code }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="schedule-cell" data-user-id="{{ $user->id }}" data-date="{{ date('Y-m-d', strtotime('monday this week +1 day')) }}">
                                                <select class="form-control shift-select" style="font-size: 1rem;" data-user-id="{{ $user->id }}" data-date="{{ date('Y-m-d', strtotime('monday this week +1 day')) }}" onchange="saveScheduleDirectly(this)">
                                                    <option value="">-</option>
                                                    @foreach($availableShiftCodes as $shiftCode)
                                                        <option value="{{ $shiftCode->id }}">{{ $shiftCode->sc_code }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="schedule-cell" data-user-id="{{ $user->id }}" data-date="{{ date('Y-m-d', strtotime('monday this week +2 day')) }}">
                                                <select class="form-control shift-select" style="font-size: 1rem;" data-user-id="{{ $user->id }}" data-date="{{ date('Y-m-d', strtotime('monday this week +2 day')) }}" onchange="saveScheduleDirectly(this)">
                                                    <option value="">-</option>
                                                    @foreach($availableShiftCodes as $shiftCode)
                                                        <option value="{{ $shiftCode->id }}">{{ $shiftCode->sc_code }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="schedule-cell" data-user-id="{{ $user->id }}" data-date="{{ date('Y-m-d', strtotime('monday this week +3 day')) }}">
                                                <select class="form-control shift-select" style="font-size: 1rem;" data-user-id="{{ $user->id }}" data-date="{{ date('Y-m-d', strtotime('monday this week +3 day')) }}" onchange="saveScheduleDirectly(this)">
                                                    <option value="">-</option>
                                                    @foreach($availableShiftCodes as $shiftCode)
                                                        <option value="{{ $shiftCode->id }}">{{ $shiftCode->sc_code }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="schedule-cell" data-user-id="{{ $user->id }}" data-date="{{ date('Y-m-d', strtotime('monday this week +4 day')) }}">
                                                <select class="form-control shift-select" style="font-size: 1rem;" data-user-id="{{ $user->id }}" data-date="{{ date('Y-m-d', strtotime('monday this week +4 day')) }}" onchange="saveScheduleDirectly(this)">
                                                    <option value="">-</option>
                                                    @foreach($availableShiftCodes as $shiftCode)
                                                        <option value="{{ $shiftCode->id }}">{{ $shiftCode->sc_code }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="schedule-cell" data-user-id="{{ $user->id }}" data-date="{{ date('Y-m-d', strtotime('monday this week +5 day')) }}">
                                                <select class="form-control shift-select" style="font-size: 1rem;" data-user-id="{{ $user->id }}" data-date="{{ date('Y-m-d', strtotime('monday this week +5 day')) }}" onchange="saveScheduleDirectly(this)">
                                                    <option value="">-</option>
                                                    @foreach($availableShiftCodes as $shiftCode)
                                                        <option value="{{ $shiftCode->id }}">{{ $shiftCode->sc_code }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="schedule-cell" data-user-id="{{ $user->id }}" data-date="{{ date('Y-m-d', strtotime('monday this week +6 day')) }}">
                                                <select class="form-control shift-select" style="font-size: 1rem;" data-user-id="{{ $user->id }}" data-date="{{ date('Y-m-d', strtotime('monday this week +6 day')) }}" onchange="saveScheduleDirectly(this)">
                                                    <option value="">-</option>
                                                    @foreach($availableShiftCodes as $shiftCode)
                                                        <option value="{{ $shiftCode->id }}">{{ $shiftCode->sc_code }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->

<!-- Shift Codes for dropdown -->
<div id="shift_codes_data" style="display: none;">
    @foreach($shiftCodes as $shiftCode)
        <option value="{{ $shiftCode->id }}" data-code="{{ $shiftCode->sc_code }}">{{ $shiftCode->sc_code }} - {{ $shiftCode->sc_description }}</option>
    @endforeach
</div>

<!-- DIRECT SCRIPT - LOADED IMMEDIATELY -->
<script>
// Helper functions - must be defined first
function getDateRange(filter) {
    const today = new Date();
    let startDate, endDate;
    
    switch(filter) {
        case 'this_week':
            // Monday of current week to Sunday
            const monday = new Date(today);
            const dayOfWeek = today.getDay();
            const diff = today.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1); // Adjust when day is Sunday
            monday.setDate(diff);
            startDate = new Date(monday);
            endDate = new Date(monday);
            endDate.setDate(monday.getDate() + 6);
            break;
            
        case 'past_week':
            // Monday to Sunday of previous week
            const lastMonday = new Date(today);
            const lastDayOfWeek = today.getDay();
            const lastDiff = today.getDate() - lastDayOfWeek + (lastDayOfWeek === 0 ? -13 : -6);
            lastMonday.setDate(lastDiff);
            startDate = new Date(lastMonday);
            endDate = new Date(lastMonday);
            endDate.setDate(lastMonday.getDate() + 6);
            break;
            
        case 'this_month':
            // First day to last day of current month
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            break;
            
        case 'last_month':
            // First day to last day of previous month
            startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            endDate = new Date(today.getFullYear(), today.getMonth(), 0);
            break;
            
        default:
            // Default to this week
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

function getWeekDates(filter) {
    const { startDate } = getDateRange(filter);
    const dates = [];
    
    if (filter === 'this_week' || filter === 'past_week') {
        // For weekly views, show 7 days
        for (let i = 0; i < 7; i++) {
            const date = new Date(startDate);
            date.setDate(startDate.getDate() + i);
            dates.push(date);
        }
    } else {
        // For monthly views, show first 7 days as example
        for (let i = 0; i < 7; i++) {
            const date = new Date(startDate);
            date.setDate(startDate.getDate() + i);
            dates.push(date);
        }
    }
    
    return dates;
}

function formatDate(date) {
    return date.toISOString().split('T')[0];
}

// Global functions for HTML onclick/onchange
function loadScheduleDirectly() {
    const divisionSelect = document.getElementById('division_filter');
    const searchInput = document.getElementById('search_filter');
    const dateFilter = document.getElementById('date_filter');
    
    let url = '{{ route("daily-schedules.weekly") }}';
    const params = new URLSearchParams();
    
    // Add division filter if available and user is director/manager
    if (divisionSelect && divisionSelect.value) {
        params.append('division_id', divisionSelect.value);
    }
    
    // Add search filter if provided
    if (searchInput && searchInput.value.trim()) {
        params.append('search', searchInput.value.trim());
    }
    
    // Add date filter
    if (dateFilter && dateFilter.value) {
        params.append('date_filter', dateFilter.value);
    }
    
    // Append parameters to URL if any
    if (params.toString()) {
        url += '?' + params.toString();
    }
    
    window.location.href = url;
}

// Function to update table headers and cells based on date filter
function updateTableDates(filter) {
    const dates = getWeekDates(filter);
    
    // Update table headers
    dates.forEach((date, index) => {
        const headerElement = document.getElementById(`date-header-${index}`);
        if (headerElement) {
            const formattedDate = date.toLocaleDateString('en-US', { 
                weekday: 'short', 
                day: 'numeric', 
                month: 'short' 
            });
            headerElement.textContent = formattedDate;
            headerElement.setAttribute('data-date', formatDate(date));
        }
    });
    
    // Update table cells data attributes
    const cells = document.querySelectorAll('.schedule-cell');
    cells.forEach((cell, cellIndex) => {
        const columnIndex = (cellIndex % 7) + 4; // +4 because first 4 columns are not date columns
        if (columnIndex < dates.length + 4) {
            const date = dates[columnIndex - 4];
            cell.setAttribute('data-date', formatDate(date));
            
            // Update select elements data attributes
            const select = cell.querySelector('.shift-select');
            if (select) {
                select.setAttribute('data-date', formatDate(date));
            }
        }
    });
}

// Function to handle search input
function handleSearch() {
    const searchInput = document.getElementById('search_filter');
    if (searchInput) {
        // Add debounce to avoid too many requests
        clearTimeout(window.searchTimeout);
        window.searchTimeout = setTimeout(() => {
            // For supervisor and staff, just reload page with search parameter
            // For director and manager, use loadScheduleDirectly
            if (document.getElementById('load_schedule')) {
                loadScheduleDirectly();
            } else {
                // Direct page reload for supervisor/staff
                const searchValue = searchInput.value.trim();
                const dateFilter = document.getElementById('date_filter')?.value || 'this_week';
                let url = '{{ route("daily-schedules.weekly") }}';
                const params = new URLSearchParams();
                
                if (searchValue) {
                    params.append('search', searchValue);
                }
                if (dateFilter) {
                    params.append('date_filter', dateFilter);
                }
                
                if (params.toString()) {
                    url += '?' + params.toString();
                }
                window.location.href = url;
            }
        }, 500);
    }
}

function saveScheduleDirectly(selectElement) {
    const userId = selectElement.getAttribute('data-user-id');
    const date = selectElement.getAttribute('data-date');
    const shiftCodeId = selectElement.value;
    
    if (!userId || !date) {
        console.error('Error: Missing user ID or date!');
        return;
    }
    
    // Show saving indicator
    const indicator = document.createElement('div');
    indicator.innerHTML = 'Saving...';
    indicator.style.cssText = 'position:fixed;top:20px;right:20px;background:#17a2b8;color:white;padding:10px;border-radius:5px;z-index:9999;';
    document.body.appendChild(indicator);
    
    // Make AJAX request
    fetch('{{ route("daily-schedule.save-weekly-schedule") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            start_date: '{{ date('Y-m-d', strtotime('monday this week')) }}',
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
            successIndicator.style.cssText = 'position:fixed;top:20px;right:20px;background:#28a745;color:white;padding:10px;border-radius:5px;z-index:9999;';
            document.body.appendChild(successIndicator);
            setTimeout(() => document.body.removeChild(successIndicator), 2000);
        } else {
            console.error('Failed to save:', data.message || 'Unknown error');
        }
    })
    .catch(error => {
        console.error('Save error:', error);
        document.body.removeChild(indicator);
    });
}



// Function to load existing schedules (with division filter)
function loadExistingSchedules() {
    const divisionId = document.getElementById('division_filter')?.value;
    const searchValue = document.getElementById('search_filter')?.value?.trim();
    const dateFilter = document.getElementById('date_filter')?.value;
    
    if (!divisionId && !searchValue && !dateFilter) {
        return;
    }
    loadExistingSchedulesForAll(divisionId, searchValue, dateFilter);
}

// Function to load existing schedules for all users (or specific division)
function loadExistingSchedulesForAll(divisionId = null, searchValue = null, dateFilter = null) {
    // Show loading indicator
    const loadingIndicator = document.createElement('div');
    loadingIndicator.innerHTML = 'Loading schedules...';
    loadingIndicator.style.cssText = 'position:fixed;top:20px;right:20px;background:#17a2b8;color:white;padding:10px;border-radius:5px;z-index:9999;';
    document.body.appendChild(loadingIndicator);
    
    // Get date range based on filter
    const { startDate, endDate } = getDateRange(dateFilter || 'this_week');
    
    // Build URL with optional division filter and search
    let url = '{{ route("daily-schedule.get-weekly-schedules") }}?start_date=' + encodeURIComponent(startDate.toISOString().split('T')[0]);
    if (divisionId) {
        url += '&division_id=' + encodeURIComponent(divisionId);
    }
    if (searchValue) {
        url += '&search=' + encodeURIComponent(searchValue);
    }
    
    // Fetch existing schedules
    fetch(url)
        .then(response => response.json())
        .then(data => {
            document.body.removeChild(loadingIndicator);
            
            console.log('API Response:', data);
            
            if (data.success && data.schedules) {
                console.log('Found', data.schedules.length, 'users with schedules');
                
                // Populate dropdowns with existing data
                data.schedules.forEach(function(userSchedule) {
                    const userId = userSchedule.user_id;
                    
                    // Loop through dates for this user
                    Object.keys(userSchedule.dates).forEach(function(date) {
                        const scheduleData = userSchedule.dates[date];
                        const selector = `select[data-user-id="${userId}"][data-date="${date}"]`;
                        const selectElement = document.querySelector(selector);
                        
                        if (selectElement && scheduleData.sc_id) {
                            // Check if the shift code option exists in this dropdown
                            const optionExists = Array.from(selectElement.options).some(option => option.value == scheduleData.sc_id);
                            
                            if (optionExists) {
                                selectElement.value = scheduleData.sc_id;
                            } else {
                                console.warn('Shift code', scheduleData.sc_id, 'not available in dropdown for user', userId, 'on date', date);
                            }
                        }
                    });
                });
                
                // Show success indicator (only if schedules were found)
                if (data.schedules.length > 0) {
                    const successIndicator = document.createElement('div');
                    successIndicator.innerHTML = `✓ Loaded ${data.schedules.length} users with schedules`;
                    successIndicator.style.cssText = 'position:fixed;top:20px;right:20px;background:#28a745;color:white;padding:10px;border-radius:5px;z-index:9999;';
                    document.body.appendChild(successIndicator);
                    setTimeout(() => document.body.removeChild(successIndicator), 3000);
                } else {
                    console.log('No schedules found for this week');
                }
            } else {
                console.error('API call failed:', data);
                const errorIndicator = document.createElement('div');
                errorIndicator.innerHTML = '❌ Failed to load schedules';
                errorIndicator.style.cssText = 'position:fixed;top:20px;right:20px;background:#dc3545;color:white;padding:10px;border-radius:5px;z-index:9999;';
                document.body.appendChild(errorIndicator);
                setTimeout(() => document.body.removeChild(errorIndicator), 3000);
            }
        })
        .catch(error => {
            console.error('Error loading schedules:', error);
            document.body.removeChild(loadingIndicator);
            const errorIndicator = document.createElement('div');
            errorIndicator.innerHTML = '❌ Network error loading schedules';
            errorIndicator.style.cssText = 'position:fixed;top:20px;right:20px;background:#dc3545;color:white;padding:10px;border-radius:5px;z-index:9999;';
            document.body.appendChild(errorIndicator);
            setTimeout(() => document.body.removeChild(errorIndicator), 3000);
        });
}

// Auto-load schedules when page loads
document.addEventListener('DOMContentLoaded', function() {
    const divisionFilter = document.getElementById('division_filter');
    const searchFilter = document.getElementById('search_filter');
    const dateFilter = document.getElementById('date_filter');
    
    // Always load schedules, regardless of division filter
    setTimeout(loadExistingSchedulesForAll, 500); // Small delay to ensure everything is ready
    
    // Add event listener for division filter change
    if (divisionFilter) {
        divisionFilter.addEventListener('change', function() {
            if (!this.value) {
                // Clear filter when "All Divisions" is selected
                window.location.href = '{{ route("daily-schedules.weekly") }}';
            }
        });
    }
    
    // Add event listener for search input
    if (searchFilter) {
        searchFilter.addEventListener('input', handleSearch);
    }

    // Add event listener for date filter change
    if (dateFilter) {
        dateFilter.addEventListener('change', function() {
            // Update table dates first
            updateTableDates(this.value);
            // Then reload schedules
            loadExistingSchedules();
        });
    }
});

// Make sure functions are globally available
window.loadScheduleDirectly = loadScheduleDirectly;
window.saveScheduleDirectly = saveScheduleDirectly;
window.loadExistingSchedules = loadExistingSchedules;
window.loadExistingSchedulesForAll = loadExistingSchedulesForAll;
window.handleSearch = handleSearch;

</script>

@endsection

<style>
    .table-responsive {
        overflow-x: auto;
    }
    
    #weeklyScheduleTable {
        font-size: 1rem;
    }
    
    #weeklyScheduleTable th {
        text-align: center;
        vertical-align: middle;
        font-weight: bold;
        font-size: 1rem;
    }
    
    #weeklyScheduleTable td {
        text-align: center;
        vertical-align: middle;
        padding: 8px 4px;
    }
    
    .shift-select {
        font-size: 1rem !important;
        padding: 4px 6px !important;
        height: 30px !important;
        border-radius: 4px !important;
        /* Remove default browser arrow and show custom arrow */
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right 8px center !important;
        background-size: 16px !important;
        padding-right: 30px !important;
    }
    
    /* Ensure all select elements show only custom dropdown arrow */
    select.form-control {
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right 8px center !important;
        background-size: 16px !important;
        padding-right: 30px !important;
    }
    
    /* Hide default browser arrow completely */
    select.form-control::-ms-expand {
        display: none !important;
    }
    
    /* Ensure select elements are properly styled */
    .form-control:focus {
        border-color: #007bff !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
    }
    
    /* Specific styling for filter dropdowns */
    #division_filter,
    #date_filter {
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right 8px center !important;
        background-size: 16px !important;
        padding-right: 30px !important;
        cursor: pointer !important;
    }
    
    /* Hide default browser arrow for filter dropdowns */
    #division_filter::-ms-expand,
    #date_filter::-ms-expand {
        display: none !important;
    }
    
    /* Hover effect for filter dropdowns */
    #division_filter:hover,
    #date_filter:hover {
        border-color: #007bff !important;
    }
    
    /* Focus effect for filter dropdowns */
    #division_filter:focus,
    #date_filter:focus {
        border-color: #007bff !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
    }
    
    .schedule-cell {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
    }
    
    .schedule-cell:hover {
        background-color: #e9ecef;
    }
    
    .date-header {
        font-size: 1rem !important;
        padding: 8px 4px !important;
    }
    
    .btn {
        font-size: 1rem;
        padding: 8px 16px;
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    
    .form-control {
        font-size: 1rem;
    }
    
    label {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 4px;
    }
    
    .save-indicator {
        position: fixed !important;
        top: 20px !important;
        right: 20px !important;
        padding: 10px 15px !important;
        border-radius: 5px !important;
        color: white !important;
        font-size: 1rem !important;
        z-index: 9999 !important;
    }
</style>

<script>
// Simple DataTable initialization only
document.addEventListener('DOMContentLoaded', function() {
    console.log('Weekly Schedule JS loaded');
    
    // Check if jQuery is available
    if (typeof $ === 'undefined') {
        console.warn('jQuery not available, using vanilla JavaScript');
        
        // Initialize DataTable with vanilla JS if available
        if (typeof $.fn !== 'undefined' && typeof $.fn.DataTable !== 'undefined') {
            $('#weeklyScheduleTable').DataTable({
                destroy: true,
                processing: false,
                serverSide: false,
                responsive: true,
                dom: 'rt<"pagination-class"ip>',
                pageLength: 25,
                language: {
                    "sProcessing":   "Loading...",
                    "sLengthMenu":   "Tampilkan _MENU_ entri",
                    "sZeroRecords":  "Tidak ditemukan data yang sesuai",
                    "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
                    "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                    "sSearch":       "Cari:",
                    "sUrl":          ""
                },
                columnDefs: [
                    {
                        "targets": [0, 1, 2, 3],
                        "orderable": true
                    },
                    {
                        "targets": [4, 5, 6, 7, 8, 9, 10],
                        "orderable": false
                    }
                ],
                order: [[1, 'asc']]
            });
            console.log('Weekly Schedule DataTable initialized with jQuery');
        } else {
            console.log('DataTable not available, skipping initialization');
        }
    } else {
        // jQuery is available, use it
        $(document).ready(function() {
            $('#weeklyScheduleTable').DataTable({
                destroy: true,
                processing: false,
                serverSide: false,
                responsive: true,
                dom: 'rt<"pagination-class"ip>',
                pageLength: 25,
                language: {
                    "sProcessing":   "Loading...",
                    "sLengthMenu":   "Tampilkan _MENU_ entri",
                    "sZeroRecords":  "Tidak ditemukan data yang sesuai",
                    "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
                    "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                    "sSearch":       "Cari:",
                    "sUrl":          ""
                },
                columnDefs: [
                    {
                        "targets": [0, 1, 2, 3],
                        "orderable": true
                    },
                    {
                        "targets": [4, 5, 6, 7, 8, 9, 10],
                        "orderable": false
                    }
                ],
                order: [[1, 'asc']]
            });
            console.log('Weekly Schedule DataTable initialized with jQuery');
        });
    }
    
    // Debug: Log current filter values
    function logCurrentFilters() {
        const divisionId = document.getElementById('division_filter')?.value || '';
        const dateFilter = document.getElementById('date_filter')?.value || 'this_week';
        const searchValue = document.getElementById('search_filter')?.value?.trim() || '';
        
        console.log('Current filter values:', {
            division_id: divisionId,
            date_filter: dateFilter,
            search: searchValue
        });
        
        // Log division text if selected
        if (divisionId) {
            const divisionSelect = document.getElementById('division_filter');
            const divisionText = divisionSelect.options[divisionSelect.selectedIndex].text;
            console.log('Selected division:', divisionText);
        }
    }
    
    // Log filters every 10 seconds for debugging
    setInterval(logCurrentFilters, 10000);
    
    // Log filters on page load
    logCurrentFilters();
    
    // Log filters when any filter changes - use vanilla JavaScript
    const filterElements = ['division_filter', 'date_filter', 'search_filter'];
    filterElements.forEach(function(elementId) {
        const element = document.getElementById(elementId);
        if (element) {
            element.addEventListener('change', function() {
                setTimeout(logCurrentFilters, 100);
            });
            element.addEventListener('keyup', function() {
                setTimeout(logCurrentFilters, 100);
            });
        }
    });
});

// Export functions
function exportToExcel() {
    console.log('Export to Excel called');
    
    // Show loading indicator
    const loadingIndicator = document.createElement('div');
    loadingIndicator.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="sr-only">Loading...</span></div>';
    loadingIndicator.className = 'position-fixed top-50 start-50 translate-middle';
    document.body.appendChild(loadingIndicator);

    // Get all current filter values
    const divisionId = document.getElementById('division_filter')?.value || '';
    const dateFilter = document.getElementById('date_filter')?.value || 'this_week';
    const searchValue = document.getElementById('search_filter')?.value?.trim() || '';
    
    console.log('Export filters:', { divisionId, dateFilter, searchValue });

    // Build export URL with all filters
    let exportUrl = '{{ route("daily-schedules.export-weekly-public") }}';
    const params = new URLSearchParams();
    
    if (divisionId) {
        params.append('division_id', divisionId);
    }
    if (dateFilter) {
        params.append('date_filter', dateFilter);
    }
    if (searchValue) {
        params.append('search', searchValue);
    }
    
    if (params.toString()) {
        exportUrl += '?' + params.toString();
    }

    console.log('Export Excel URL:', exportUrl);

    // Use fetch instead of direct link to handle errors
    fetch(exportUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.blob();
        })
        .then(blob => {
            // Create download link
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            
            // Generate filename based on filters
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
            
            // Show success message
            showAlert('Export Excel berhasil!', 'success');
        })
        .catch(error => {
            console.error('Export Excel error:', error);
            showAlert('Export Excel gagal: ' + error.message, 'danger');
        })
        .finally(() => {
            // Remove loading indicator
            if (document.body.contains(loadingIndicator)) {
                document.body.removeChild(loadingIndicator);
            }
        });
}

function exportToPDF() {
    console.log('Export to PDF called');
    
    // Show loading indicator
    const loadingIndicator = document.createElement('div');
    loadingIndicator.innerHTML = 'Preparing PDF export...';
    loadingIndicator.style.cssText = 'position:fixed;top:20px;right:20px;background:#17a2b8;color:white;padding:10px;border-radius:5px;z-index:9999;';
    document.body.appendChild(loadingIndicator);
    
    // Get all current filter values
    const divisionId = document.getElementById('division_filter')?.value || '';
    const dateFilter = document.getElementById('date_filter')?.value || 'this_week';
    const searchValue = document.getElementById('search_filter')?.value?.trim() || '';
    
    console.log('Export PDF filters:', { divisionId, dateFilter, searchValue });
    
    // Build export URL with all filters
    let exportUrl = '{{ route("daily-schedules.export-weekly-pdf-public") }}';
    const params = new URLSearchParams();
    
    if (divisionId) {
        params.append('division_id', divisionId);
    }
    if (dateFilter) {
        params.append('date_filter', dateFilter);
    }
    if (searchValue) {
        params.append('search', searchValue);
    }
    
    if (params.toString()) {
        exportUrl += '?' + params.toString();
    }
    
    console.log('Export PDF URL:', exportUrl);
    console.log('Export PDF params:', params.toString());
    
    // Use fetch instead of direct link to handle errors
    fetch(exportUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.blob();
        })
        .then(blob => {
            // Create download link
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            
            // Generate filename based on filters
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
            
            // Show success message
            showAlert('Export PDF berhasil!', 'success');
        })
        .catch(error => {
            console.error('Export PDF error:', error);
            showAlert('Export PDF gagal: ' + error.message, 'danger');
        })
        .finally(() => {
            // Remove loading indicator
            if (document.body.contains(loadingIndicator)) {
                document.body.removeChild(loadingIndicator);
            }
        });
}

// Helper function to show alerts
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top:20px;right:20px;z-index:9999;min-width:300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (document.body.contains(alertDiv)) {
            document.body.removeChild(alertDiv);
        }
    }, 5000);
}

// Make sure functions are globally available
window.exportToExcel = exportToExcel;
window.exportToPDF = exportToPDF;

// Debug logging to confirm functions are loaded
console.log('Export functions loaded:', {
    'exportToExcel': typeof exportToExcel,
    'exportToPDF': typeof exportToPDF,
    'window.exportToExcel': typeof window.exportToExcel,
    'window.exportToPDF': typeof window.exportToPDF
});
</script>

@include('app._partials.js')

