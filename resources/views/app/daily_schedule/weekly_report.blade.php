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
                            <span class="text-muted">{{ date('d M Y', strtotime($startDate)) }} - {{ date('d M Y', strtotime($endDate)) }}</span>
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
                <div class="card-body">
                    <form method="GET" action="{{ route('daily-schedules.weekly-report') }}">
                        <div class="row">
                            <div class="col-md-2">
                                <label>Date Filter:</label>
                                <select class="form-control" name="date_filter" onchange="this.form.submit()">
                                    <option value="this_week" {{ request('date_filter', 'this_week') == 'this_week' ? 'selected' : '' }}>This Week</option>
                                    <option value="past_week" {{ request('date_filter', 'this_week') == 'past_week' ? 'selected' : '' }}>Past Week</option>
                                    <option value="this_month" {{ request('date_filter', 'this_week') == 'this_month' ? 'selected' : '' }}>This Month</option>
                                    <option value="last_month" {{ request('date_filter', 'this_week') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Week Range:</label>
                                <input type="date" class="form-control" name="start_date" value="{{ $startDate }}" onchange="this.form.submit()">
                                <small class="form-text text-muted">Select Monday to show full week</small>
                            </div>
                            <div class="col-md-2">
                                <label>Team (Division):</label>
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
                            <div class="col-md-2">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="ki-outline ki-filter-search"></i> Filter
                                    </button>
                                    <a href="{{ route('daily-schedules.weekly-report') }}" class="btn btn-secondary btn-sm">
                                        <i class="ki-outline ki-cross"></i> Clear
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Report Content -->
            <div class="card card-custom">
                <div class="card-header flex-wrap py-3">
                    <div class="card-title">
                        <h3 class="card-label">
                            Weekly Schedule Report
                            <span class="text-muted pt-2 font-size-sm d-block">
                                {{ date('l, d F Y', strtotime($startDate)) }} - {{ date('l, d F Y', strtotime($endDate)) }}
                            </span>
                        </h3>
                    </div>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-light-success btn-sm" onclick="window.print()">
                            <i class="ki-outline ki-printer"></i> Print
                        </button>
                        <button type="button" class="btn btn-light-primary btn-sm ml-2" onclick="exportToExcel()">
                            <i class="ki-outline ki-file-down"></i> Export Excel
                        </button>
                        <button type="button" class="btn btn-light-danger btn-sm ml-2" onclick="exportToPDF()">
                            <i class="ki-outline ki-file-down"></i> Export PDF
                        </button>
                    </div>
                </div>
                <div class="card-body">
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
                                            <th rowspan="2" class="text-center align-middle" style="width: 250px;">NAMA</th>
                                            @foreach($weekDates as $date)
                                                <th colspan="2" class="text-center" style="min-width: 240px;">
                                                    <div class="font-weight-bold">{{ date('l', strtotime($date)) }}</div>
                                                    <div class="font-size-sm">{{ date('d M', strtotime($date)) }}</div>
                                                </th>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            @foreach($weekDates as $date)
                                                <th class="text-center bg-light-info" style="width: 120px;">
                                                    <small>Shift Name</small>
                                                </th>
                                                <th class="text-center bg-light-warning" style="width: 120px;">
                                                    <small>Start Shift</small>
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                            <tr>
                                                <td class="text-center align-middle font-weight-bold" style="font-size: 14px; width: 250px;">
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
                                                        
                                                        // Color coding based on shift type
                                                        $cellClass = '';
                                                        if ($shiftCode) {
                                                            if (in_array($shiftCode, ['L', 'LL', 'LPH'])) {
                                                                $cellClass = 'bg-light-success'; // Green for leave
                                                            } elseif (in_array($shiftCode, ['S', 'I'])) {
                                                                $cellClass = 'bg-light-danger'; // Red for sick/permission
                                                            } elseif ($shiftCode == 'N/A') {
                                                                $cellClass = 'bg-light-secondary'; // Gray for not set
                                                            } else {
                                                                $cellClass = 'bg-light-primary'; // Blue for work shifts
                                                            }
                                                        }
                                                    @endphp
                                                    
                                                    <!-- Shift Name Column -->
                                                    <td class="text-center align-middle {{ $cellClass }}" style="font-size: 12px;">
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
                                                    <td class="text-center align-middle {{ $cellClass }}" style="font-size: 12px;">
                                                        @if($schedule && $startTime && $endTime)
                                                            <span class="font-weight-bold">{{ date('H:i', strtotime($startTime)) }} - {{ date('H:i', strtotime($endTime)) }}</span>
                                                        @elseif($schedule && $startTime)
                                                            <span class="font-weight-bold">{{ date('H:i', strtotime($startTime)) }}</span>
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
                                <i class="ki ki-information icon-2x"></i>
                                <h4 class="mt-3">No Schedule Data Found</h4>
                                <p>No schedule data available for the selected week and filters.</p>
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

<!-- Legend -->
<div class="card card-custom mt-3">
    <div class="card-body py-3">
        <div class="row align-items-center">
            <div class="col-md-2">
                <strong>Legend:</strong>
            </div>
            <div class="col-md-10">
                <span class="badge bg-light-success text-success mr-2">
                    <span class="bullet bullet-bar bg-success mr-2"></span>Libur/Leave
                </span>
                <span class="badge bg-light-danger text-danger mr-2">
                    <span class="bullet bullet-bar bg-danger mr-2"></span>Sakit/Izin
                </span>
                <span class="badge bg-light-primary text-primary mr-2">
                    <span class="bullet bullet-bar bg-primary mr-2"></span>Work Shift
                </span>
                <span class="badge bg-light-secondary text-secondary mr-2">
                    <span class="bullet bullet-bar bg-secondary mr-2"></span>Not Set
                </span>
            </div>
        </div>
    </div>
</div>

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
</style>

<script>
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
