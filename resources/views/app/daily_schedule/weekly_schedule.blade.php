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
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                            <li class="breadcrumb-item text-muted">
                                <span class="text-muted">{{ date('d M Y', strtotime($startDate)) }} - {{ date('d M Y', strtotime($endDate)) }}</span>
                            </li>
                        </ul>
                        <!--end::Breadcrumb-->
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
                <!-- Alert Area for Import Results -->
                <div id="importAlertArea" class="mb-3" style="display: none;">
                    <!-- Alerts will be dynamically inserted here -->
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card card-custom mb-5">
                            <div class="card-header flex-wrap py-3">
                                <div class="card-title">
                                    <h3 class="card-label">Filters</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <form method="GET" id="filterForm">
                                    <div class="card-toolbar d-flex justify-content-between w-100">
                                        <div class="row w-100">
                                            @if(in_array($currentUser->up_code ?? '', ['DIRECTOR', 'MANAGER', 'SUPERVISOR']))
                                                <div class="col-md-2">
                                                    <label>Division</label>
                                                    <select class="form-control {{ in_array($currentUser->up_code ?? '', ['SUPERVISOR']) ? 'bg-light' : '' }}"
                                                            id="division_filter" name="division_id"
                                                            {{ in_array($currentUser->up_code ?? '', ['SUPERVISOR']) ? 'disabled' : '' }}
                                                            {{ in_array($currentUser->up_code ?? '', ['SUPERVISOR']) ? 'title="You can only view your own division"' : '' }}>
                                                        @if(in_array($currentUser->up_code ?? '', ['DIRECTOR', 'MANAGER']))
                                                            <option value="">Semua Divisi</option>
                                                        @endif
                                                        @foreach($divisions as $division)
                                                            <option value="{{ $division->id }}" {{ $divisionId == $division->id ? 'selected' : '' }}>{{ $division->ud_code }} - {{ $division->ud_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @if(in_array($currentUser->up_code ?? '', ['SUPERVISOR']))
                                                        <small class="form-text text-muted">
                                                            You can only view your own division
                                                        </small>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="col-md-2">
                                                <label>Filter Tanggal:</label>
                                                <select class="form-control" id="date_filter" name="date_filter">
                                                    <option value="this_week" {{ $dateFilter == 'this_week' ? 'selected' : '' }}>This Week</option>
                                                    <option value="past_week" {{ $dateFilter == 'past_week' ? 'selected' : '' }}>Last Week</option>
                                                    <option value="custom" {{ $dateFilter == 'custom' ? 'selected' : '' }}>Custom Range</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Rentang Minggu:</label>
                                                <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                                                <small class="form-text text-muted">Select Monday to display full week</small>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Cari Staff:</label>
                                                <input type="text" class="form-control w-100" id="search_filter" name="search" placeholder="Cari berdasarkan nama atau NIP..." value="{{ $search ?? '' }}">
                                            </div>
                                            <div class="col-md-1">
                                                <label>&nbsp;</label>
                                                <button type="submit" class="btn btn-primary btn-block">
                                                    <i class="ki-outline ki-filter-search"></i> Filter
                                                </button>
                                            </div>
                                            @if(in_array($currentUser->up_code ?? '', ['DIRECTOR', 'MANAGER']))
                                                <div class="col-md-1">
                                                    <label>&nbsp;</label>
                                                    <button type="button" class="btn btn-dark btn-block" id="load_schedule" onclick="loadScheduleDirectly()">
                                                        <i class="ki-outline ki-loading"></i> Loading
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex align-items-center justify-content-end">
                                    <button type="button" class="btn btn-light btn-sm mr-2" onclick="loadExistingSchedules()">
                                        <i class="ki-outline ki-arrows-circle"></i> Load Schedule
                                    </button>
                                    <!-- Import Button -->
                                    <button type="button" class="btn btn-green btn-sm mr-2" onclick="showImportModal()">
                                        <i class="ki-outline ki-file-up"></i> Import Schedule
                                    </button>
                                    <!-- Export Buttons -->
                                    <button type="button" class="btn btn-light-green btn-sm mr-2" onclick="exportToExcel()">
                                        <i class="ki-outline ki-file-down"></i> Export Excel
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm mr-2" onclick="exportToPDF()">
                                        <i class="ki-outline ki-file-down"></i> Export PDF
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-checkable" id="weeklyScheduleTable">
                                        <thead class="bg-light text-dark">
                                        <tr>
                                            <th style="min-width: 80px;">NIP</th>
                                            <th style="min-width: 200px;">Staff</th>
                                            <th style="min-width: 150px;">Division</th>
                                            <th style="min-width: 100px;">User Type</th>
                                            <th style="min-width: 100px;" class="date-header" data-date="{{ $startDate }}" id="date-header-0">{{ date('D d-M', strtotime($startDate)) }}</th>
                                            <th style="min-width: 100px;" class="date-header" data-date="{{ date('Y-m-d', strtotime($startDate . ' +1 day')) }}" id="date-header-1">{{ date('D d-M', strtotime($startDate . ' +1 day')) }}</th>
                                            <th style="min-width: 100px;" class="date-header" data-date="{{ date('Y-m-d', strtotime($startDate . ' +2 day')) }}" id="date-header-2">{{ date('D d-M', strtotime($startDate . ' +2 day')) }}</th>
                                            <th style="min-width: 100px;" class="date-header" data-date="{{ date('Y-m-d', strtotime($startDate . ' +3 day')) }}" id="date-header-3">{{ date('D d-M', strtotime($startDate . ' +3 day')) }}</th>
                                            <th style="min-width: 100px;" class="date-header" data-date="{{ date('Y-m-d', strtotime($startDate . ' +4 day')) }}" id="date-header-4">{{ date('D d-M', strtotime($startDate . ' +4 day')) }}</th>
                                            <th style="min-width: 100px;" class="date-header" data-date="{{ date('Y-m-d', strtotime($startDate . ' +5 day')) }}" id="date-header-5">{{ date('D d-M', strtotime($startDate . ' +5 day')) }}</th>
                                            <th style="min-width: 100px;" class="date-header" data-date="{{ date('Y-m-d', strtotime($startDate . ' +6 day')) }}" id="date-header-6">{{ date('D d-M', strtotime($startDate . ' +6 day')) }}</th>
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
                                                <td class="schedule-cell" data-user-id="{{ $user->id }}" data-date="{{ $startDate }}">
                                                    <select class="form-control shift-select" style="font-size: 1rem;" data-user-id="{{ $user->id }}" data-date="{{ $startDate }}" onchange="saveScheduleDirectly(this)">
                                                        <option value="">-</option>
                                                        @foreach($availableShiftCodes as $shiftCode)
                                                            <option value="{{ $shiftCode->id }}">{{ $shiftCode->sc_code }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="schedule-cell" data-user-id="{{ $user->id }}" data-date="{{ date('Y-m-d', strtotime($startDate . ' +1 day')) }}">
                                                    <select class="form-control shift-select" style="font-size: 1rem;" data-user-id="{{ $user->id }}" data-date="{{ date('Y-m-d', strtotime($startDate . ' +1 day')) }}" onchange="saveScheduleDirectly(this)">
                                                        <option value="">-</option>
                                                        @foreach($availableShiftCodes as $shiftCode)
                                                            <option value="{{ $shiftCode->id }}">{{ $shiftCode->sc_code }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="schedule-cell" data-user-id="{{ $user->id }}" data-date="{{ date('Y-m-d', strtotime($startDate . ' +2 day')) }}">
                                                    <select class="form-control shift-select" style="font-size: 1rem;" data-user-id="{{ $user->id }}" data-date="{{ date('Y-m-d', strtotime($startDate . ' +2 day')) }}" onchange="saveScheduleDirectly(this)">
                                                        <option value="">-</option>
                                                        @foreach($availableShiftCodes as $shiftCode)
                                                            <option value="{{ $shiftCode->id }}">{{ $shiftCode->sc_code }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="schedule-cell" data-user-id="{{ $user->id }}" data-date="{{ date('Y-m-d', strtotime($startDate . ' +3 day')) }}">
                                                    <select class="form-control shift-select" style="font-size: 1rem;" data-user-id="{{ $user->id }}" data-date="{{ date('Y-m-d', strtotime($startDate . ' +3 day')) }}" onchange="saveScheduleDirectly(this)">
                                                        <option value="">-</option>
                                                        @foreach($availableShiftCodes as $shiftCode)
                                                            <option value="{{ $shiftCode->id }}">{{ $shiftCode->sc_code }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="schedule-cell" data-user-id="{{ $user->id }}" data-date="{{ date('Y-m-d', strtotime($startDate . ' +4 day')) }}">
                                                    <select class="form-control shift-select" style="font-size: 1rem;" data-user-id="{{ $user->id }}" data-date="{{ date('Y-m-d', strtotime($startDate . ' +4 day')) }}" onchange="saveScheduleDirectly(this)">
                                                        <option value="">-</option>
                                                        @foreach($availableShiftCodes as $shiftCode)
                                                            <option value="{{ $shiftCode->id }}">{{ $shiftCode->sc_code }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="schedule-cell" data-user-id="{{ $user->id }}" data-date="{{ date('Y-m-d', strtotime($startDate . ' +5 day')) }}">
                                                    <select class="form-control shift-select" style="font-size: 1rem;" data-user-id="{{ $user->id }}" data-date="{{ date('Y-m-d', strtotime($startDate . ' +5 day')) }}" onchange="saveScheduleDirectly(this)">
                                                        <option value="">-</option>
                                                        @foreach($availableShiftCodes as $shiftCode)
                                                            <option value="{{ $shiftCode->id }}">{{ $shiftCode->sc_code }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="schedule-cell" data-user-id="{{ $user->id }}" data-date="{{ date('Y-m-d', strtotime($startDate . ' +6 day')) }}">
                                                    <select class="form-control shift-select" style="font-size: 1rem;" data-user-id="{{ $user->id }}" data-date="{{ date('Y-m-d', strtotime($startDate . ' +6 day')) }}" onchange="saveScheduleDirectly(this)">
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

    </div>
    <!--end::Entry-->
    </div>
    <!--end::Content-->

    <!-- Import Excel Modal -->
    <div class="modal" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Weekly Schedule</h5>
                    <button type="button" class="close" onclick="hideImportModal()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="import_start_date">Week Start Date (Monday)</label>
                                <input type="date" class="form-control" id="import_start_date" name="import_start_date" value="{{ $startDate }}">
                                <small class="form-text text-muted">Select Monday to show full week schedule</small>
                            </div>
                        </div>
                        <!-- <div class="col-md-6">
                            <div class="form-group">
                                <label>Division Information</label>
                                <div class="form-control-plaintext">
                                    <span class="badge badge-info">Import will process all divisions</span>
                                </div>
                                <small class="form-text text-muted">No division filter applied</small>
                            </div>
                        </div> -->
                    </div>

                    <div class="form-group">
                        <label for="excel_file">Excel File</label>
                        <input type="file" class="form-control-file" id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv">
                        <small class="form-text text-muted">
                            Format: NIP, Nama, User Type, Senin, Selasa, Rabu, Kamis, Jumat, Sabtu, Minggu<br>
                            File size: max 2MB
                        </small>
                        <div class="mt-3">
                            <a href="/Template_Import_Schedule.xlsx" class="btn btn-sm btn-light-green" download>
                                <i class="ki-outline ki-file-down"></i> Download Template Excel
                            </a>
                        </div>
                    </div>

                    <div class="alert alert-light rounded-lg p-8">
                        <h6><i class="ki-outline ki-information-5" class="text-white"></i> Excel Format Requirements:</h6>
                        <ul class="mb-0">
                            <li><strong>Column A:</strong> NIP (required)</li>
                            <li><strong>Column B:</strong> Nama Staff</li>
                            <li><strong>Column C:</strong> User Type (can be empty, will auto-detect from database)</li>
                            <li><strong>Column D:</strong> Senin (shift code: PS1, PS2, L, SM1, SM2, etc.)</li>
                            <li><strong>Column E:</strong> Selasa</li>
                            <li><strong>Column F:</strong> Rabu</li>
                            <li><strong>Column G:</strong> Kamis</li>
                            <li><strong>Column H:</strong> Jumat</li>
                            <li><strong>Column I:</strong> Sabtu</li>
                            <li><strong>Column J:</strong> Minggu</li>
                        </ul>
                    </div>

                    <div class="alert alert-primary">
                        <h6><i class="ki-outline ki-warning"></i> Validation Rules:</h6>
                        <ul class="mb-0">
                            <li>Shift codes must exist in the system</li>
                            <li>Shift codes must be compatible with user type</li>
                            <li>User Type column can be empty - system will auto-detect from database</li>
                            <li>Import will process all divisions without filter</li>
                            <li>Existing schedules will be updated</li>
                            <li>Empty cells will be ignored</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="hideImportModal()">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="importExcel()">
                        <i class="ki-outline ki-file-up"></i> Import
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Shift Codes for dropdown -->
    <div id="shift_codes_data" style="display: none;">
        @foreach($shiftCodes as $shiftCode)
            <option value="{{ $shiftCode->id }}" data-code="{{ $shiftCode->sc_code }}">{{ $shiftCode->sc_code }} - {{ $shiftCode->sc_description }}</option>
        @endforeach
    </div>

    <!-- DIRECT SCRIPT - LOADED IMMEDIATELY -->
    <script>
        // Backend date variables from PHP
        window.backendStartDate = '{{ $startDate ?? date('Y-m-d', strtotime('monday this week')) }}';
        window.backendEndDate = '{{ $endDate ?? date('Y-m-d', strtotime('sunday this week')) }}';
        window.backendDateFilter = '{{ $dateFilter ?? 'this_week' }}';

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
                        // Use form submission for consistency with other filters
                        document.getElementById('filterForm').submit();
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

            // Get date range from backend or filter
            let startDate, endDate;

            // Check if we have dates from backend (PHP variables)
            if (typeof window.backendStartDate !== 'undefined' && typeof window.backendEndDate !== 'undefined') {
                startDate = new Date(window.backendStartDate);
                endDate = new Date(window.backendEndDate);
            } else {
                // Fallback to filter-based calculation
                const dateRange = getDateRange(dateFilter || 'this_week');
                startDate = dateRange.startDate;
                endDate = dateRange.endDate;
            }

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

            // Add event listener for division filter change (auto-submit like other filters)
            if (divisionFilter) {
                divisionFilter.addEventListener('change', function() {
                    // Only submit if not disabled (for supervisor restriction)
                    if (!this.disabled) {
                        setTimeout(() => {
                            document.getElementById('filterForm').submit();
                        }, 100);
                    }
                });
            }

            // Add event listener for search input
            if (searchFilter) {
                searchFilter.addEventListener('input', handleSearch);
            }

            // Add event listener for date filter change (auto-submit like other filters)
            if (dateFilter) {
                dateFilter.addEventListener('change', function() {
                    const selectedFilter = this.value;
                    if (selectedFilter !== 'custom') {
                        const dates = calculateDatesFromFilter(selectedFilter);
                        if (dates) {
                            const startDateInput = document.querySelector('input[name="start_date"]');
                            if (startDateInput) {
                                startDateInput.value = dates.startDate;
                            }
                        }
                    }
                    // Submit form to apply date filter (consistent with division filter)
                    setTimeout(() => {
                        document.getElementById('filterForm').submit();
                    }, 100);
                });
            }

            // Add event listener for start date change (two-way synchronization)
            const startDateInput = document.querySelector('input[name="start_date"]');
            if (startDateInput) {
                startDateInput.addEventListener('change', function() {
                    const selectedDate = this.value;
                    if (selectedDate) {
                        const endDate = calculateEndDate(selectedDate);
                        const detectedFilter = detectFilterFromDateRange(selectedDate, endDate);
                        if (detectedFilter !== 'custom') {
                            dateFilter.value = detectedFilter;
                        } else {
                            dateFilter.value = 'custom';
                        }
                        // Submit form to apply changes
                        setTimeout(() => {
                            document.getElementById('filterForm').submit();
                        }, 100);
                    }
                });
            }

            // Two-way synchronization functions (same as weekly report)
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

                // Get Monday of current week
                const mondayThisWeek = new Date(today);
                const dayOfWeek = today.getDay();
                const daysToSubtract = dayOfWeek === 0 ? 6 : dayOfWeek - 1;
                mondayThisWeek.setDate(today.getDate() - daysToSubtract);

                // Get Monday of previous week
                const mondayLastWeek = new Date(mondayThisWeek);
                mondayLastWeek.setDate(mondayThisWeek.getDate() - 7);

                // Compare date ranges
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

<style>
    /* Custom CSS for Import Modal */
    .modal {
        display: none;
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
    }

    .modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-dialog {
        margin: 1.75rem auto;
        max-width: 800px;
        width: 90%;
    }

    .modal-content {
        background-color: #fefefe;
        border: 1px solid #888;
        border-radius: 5px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
        padding: 15px;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-body {
        padding: 15px;
    }

    .modal-footer {
        padding: 15px;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .close {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        background: none;
        border: none;
        padding: 0;
    }

    .close:hover {
        color: #000;
    }

    .modal-open {
        overflow: hidden;
    }
</style>

<script>
    // Simple DataTable initialization only
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Weekly Schedule JS loaded');

        // No DataTable needed for weekly schedule input
        console.log('Weekly Schedule - No DataTable initialization needed');

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

    // Filter interaction - form auto-submit on change
    document.addEventListener('DOMContentLoaded', function() {
        // All filters now use onchange="this.form.submit()" for immediate effect
        console.log('Weekly schedule filters loaded with auto-submit');
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
        const startDate = document.getElementById('start_date')?.value || '';
        const endDate = document.getElementById('end_date')?.value || '';
        const searchValue = document.getElementById('search_filter')?.value?.trim() || '';

        console.log('Export filters:', { divisionId, dateFilter, startDate, endDate, searchValue });

        // Build export URL with all filters
        let exportUrl = '{{ route("daily-schedules.export-weekly-public") }}';
        const params = new URLSearchParams();

        if (divisionId) {
            params.append('division_id', divisionId);
        }
        if (dateFilter) {
            params.append('date_filter', dateFilter);
        }
        if (startDate) {
            params.append('start_date', startDate);
        }
        if (endDate) {
            params.append('end_date', endDate);
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
        const startDate = document.getElementById('start_date')?.value || '';
        const endDate = document.getElementById('end_date')?.value || '';
        const searchValue = document.getElementById('search_filter')?.value?.trim() || '';

        console.log('Export PDF filters:', { divisionId, dateFilter, startDate, endDate, searchValue });

        // Build export URL with all filters
        let exportUrl = '{{ route("daily-schedules.export-weekly-pdf-public") }}';
        const params = new URLSearchParams();

        if (divisionId) {
            params.append('division_id', divisionId);
        }
        if (dateFilter) {
            params.append('date_filter', dateFilter);
        }
        if (startDate) {
            params.append('start_date', startDate);
        }
        if (endDate) {
            params.append('end_date', endDate);
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

    // Import Excel functions
    function showImportModal() {
        // Modal will automatically use current division filter
        document.getElementById('importModal').style.display = 'block';
        document.getElementById('importModal').classList.add('show');
        document.body.classList.add('modal-open');
    }

    function hideImportModal() {
        console.log('hideImportModal called');
        const modal = document.getElementById('importModal');
        console.log('Modal element:', modal);

        if (modal) {
            // Force close modal with multiple approaches
            modal.style.display = 'none';
            modal.style.visibility = 'hidden';
            modal.style.opacity = '0';
            modal.classList.remove('show');
            modal.classList.remove('modal-open');
            document.body.classList.remove('modal-open');

            // Force remove any inline styles that might interfere
            modal.removeAttribute('style');
            modal.style.display = 'none';

            console.log('Modal display set to none, show class removed, styles reset');
        } else {
            console.error('Modal element not found!');
        }

        // Also reset any form inputs
        const fileInput = document.getElementById('excel_file');
        if (fileInput) {
            fileInput.value = '';
        }

        // Don't clear alerts here - let them stay visible
        // clearAllImportAlerts(); // Commented out to preserve success/error messages
    }

    // Function to show alerts in the alert area above the table
    function showImportAlert(message, type = 'info', errors = null) {
        console.log('showImportAlert called:', { message, type, errors });

        const alertArea = document.getElementById('importAlertArea');
        console.log('Alert area element:', alertArea);

        if (!alertArea) {
            console.error('Alert area not found!');
            return;
        }

        const alertId = 'alert-' + Date.now();

        let alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" id="${alertId}" role="alert">
            <div class="d-flex align-items-center">
                <i class="ki-outline ki-${type === 'success' ? 'check-circle' : type === 'danger' ? 'cross-circle' : type === 'warning' ? 'warning' : 'information-5'} mr-2 text-white"></i>
                <div class="flex-grow-1">
                    <strong>${type === 'success' ? 'Success' : type === 'danger' ? 'Error' : type === 'warning' ? 'Warning' : 'Info'}:</strong> ${message}
                </div>
            </div>
    `;

        // Add errors list if provided
        if (errors && errors.length > 0) {
            alertHtml += `
            <div class="mt-2">
                <strong>Details:</strong>
                <ul class="mb-0 mt-1 pl-4">
                    ${errors.slice(0, 10).map(error => `<li>${error}</li>`).join('')}
                    ${errors.length > 10 ? `<li>... and ${errors.length - 10} more errors</li>` : ''}
                </ul>
            </div>
        `;
        }

        alertHtml += `
            <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="closeImportAlert('${alertId}')">
                <span aria-hidden="true" class="text-white" style="margin-top: -5px;">&times;</span>
            </button>
        </div>
    `;

        // Show alert area and add alert
        alertArea.style.display = 'block';
        alertArea.innerHTML = alertHtml;

        console.log('Alert displayed:', { alertId, type, message });

        // Auto-hide success alerts after 10 seconds, but keep error alerts visible until manually closed
        if (type === 'success') {
            setTimeout(() => {
                closeImportAlert(alertId);
            }, 10000);
        }
        // Error alerts will stay visible until user manually closes them
    }

    // Function to close specific alert
    function closeImportAlert(alertId) {
        const alert = document.getElementById(alertId);
        if (alert) {
            alert.remove();

            // Hide alert area if no more alerts
            const alertArea = document.getElementById('importAlertArea');
            if (alertArea.children.length === 0) {
                alertArea.style.display = 'none';
            }
        }
    }

    // Function to clear all alerts
    function clearAllImportAlerts() {
        const alertArea = document.getElementById('importAlertArea');
        alertArea.innerHTML = '';
        alertArea.style.display = 'none';
    }

    function importExcel() {
        console.log('importExcel function called');

        const fileInput = document.getElementById('excel_file');
        const startDate = document.getElementById('import_start_date').value;

        // Check modal status before starting
        const modal = document.getElementById('importModal');
        console.log('Modal status before import:', {
            display: modal.style.display,
            hasShowClass: modal.classList.contains('show'),
            isVisible: modal.offsetParent !== null
        });

        if (!fileInput.files[0]) {
            showImportAlert('Please select an Excel file', 'warning');
            return;
        }

        if (!startDate) {
            showImportAlert('Please select a start date', 'warning');
            return;
        }

        // Clear any existing alerts
        clearAllImportAlerts();

        // Create FormData
        const formData = new FormData();
        formData.append('excel_file', fileInput.files[0]);
        formData.append('start_date', startDate);

        // Show loading indicator
        const loadingIndicator = document.createElement('div');
        loadingIndicator.innerHTML = 'Importing Excel file...';
        loadingIndicator.style.cssText = 'position:fixed;top:20px;right:20px;background:#17a2b8;color:white;padding:10px;border-radius:5px;z-index:9999;';
        document.body.appendChild(loadingIndicator);

        // Make AJAX request
        fetch('{{ route("daily-schedules.import-weekly-excel") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                document.body.removeChild(loadingIndicator);

                if (data.success) {
                    // Show success message first
                    showImportAlert(data.message, 'success');

                    // Close modal on success
                    console.log('Import successful, closing modal...');
                    hideImportModal();
                    console.log('Modal should be closed now');

                    // Reset file input
                    fileInput.value = '';

                    // Reload schedules to show updated data
                    setTimeout(() => {
                        loadExistingSchedules();
                    }, 1000);

                } else {
                    // Show error message with details
                    let errorMessage = data.message;
                    console.log('Import failed, showing error alert and closing modal');
                    showImportAlert(errorMessage, 'danger', data.errors || []);

                    // Close modal on error too - let user see the errors clearly
                    hideImportModal();
                    console.log('Modal closed after error, user can see alert clearly');
                }
            })
            .catch(error => {
                document.body.removeChild(loadingIndicator);
                console.error('Import error:', error);
                showImportAlert('Import failed: ' + error.message, 'danger');

                // Close modal on network/technical error too
                hideImportModal();
                console.log('Modal closed after network error, user can see alert clearly');
            });
    }

    // Make import functions globally available
    window.showImportModal = showImportModal;
    window.hideImportModal = hideImportModal;
    window.importExcel = importExcel;
    window.showImportAlert = showImportAlert;
    window.closeImportAlert = closeImportAlert;
    window.clearAllImportAlerts = clearAllImportAlerts;

    // Add event listeners for modal
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('importModal');

        // Close modal when clicking outside
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                console.log('Modal clicked outside, closing...');
                hideImportModal();
            }
        });

        // Close modal with ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && modal.classList.contains('show')) {
                console.log('ESC key pressed, closing modal...');
                hideImportModal();
            }
        });

        console.log('Modal event listeners attached');
    });

    // Debug logging to confirm functions are loaded
    console.log('Export functions loaded:', {
        'exportToExcel': typeof exportToExcel,
        'exportToPDF': typeof exportToPDF,
        'window.exportToExcel': typeof window.exportToExcel,
        'window.exportToPDF': typeof window.exportToPDF
    });

    console.log('Import functions loaded:', {
        'showImportModal': typeof showImportModal,
        'importExcel': typeof importExcel,
        'window.showImportModal': typeof window.showImportModal,
        'window.importExcel': typeof window.importExcel
    });
</script>

@include('app._partials.js')

