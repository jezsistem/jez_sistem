@extends('layouts.app_v2')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            @if($dateFilter === 'now')
                <p class="text-sm text-gray-500 mt-1">
                    <i class="fas fa-clock mr-1"></i> Staff have a Work Schedule - {{ date('d M Y, H:i') }}
                </p>
            @else
                <p class="text-sm text-gray-500 mt-1">{{ date('d M Y', strtotime($startDate)) }} - {{ date('d M Y', strtotime($endDate)) }}</p>
            @endif
        </div>
        <div class="flex items-center gap-2">
            <button type="button" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700" onclick="exportToExcel()">
                <i class="fas fa-file-excel mr-2"></i> Export Excel
            </button>
            <button type="button" class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-700" onclick="exportToPDF()">
                <i class="fas fa-file-pdf mr-2"></i> Export PDF
            </button>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filters</h3>
    <form method="GET" action="{{ route('daily-schedules.weekly-report_v2') }}" id="filterForm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Filter:</label>
                <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" name="date_filter" id="date_filter">
                    <option value="this_week" {{ $dateFilter == 'this_week' ? 'selected' : '' }}>This Week</option>
                    <option value="past_week" {{ $dateFilter == 'past_week' ? 'selected' : '' }}>Past Week</option>
                    <option value="next_week" {{ $dateFilter == 'next_week' ? 'selected' : '' }}>Next Week</option>
                    <option value="now" {{ $dateFilter == 'now' ? 'selected' : '' }}>NOW (Staff Sedang Bekerja)</option>
                    <option value="custom" {{ $dateFilter == 'custom' ? 'selected' : '' }}>Custom Range</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Week Range:</label>
                <input type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" name="start_date" id="start_date" value="{{ $startDate }}">
                <small class="text-gray-500 text-xs mt-1 block">Select Monday to show full week</small>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Division:</label>
                <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" name="division_id" id="division_filter">
                    <option value="">All Divisions</option>
                    @foreach($divisions as $division)
                        <option value="{{ $division->id }}" {{ $divisionId == $division->id ? 'selected' : '' }}>
                            {{ $division->ud_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">User Position:</label>
                <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" name="position_id" id="position_filter">
                    <option value="">All Positions</option>
                    @foreach($userPositions as $position)
                        <option value="{{ $position->id }}" {{ $positionId == $position->id ? 'selected' : '' }}>
                            {{ $position->up_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Shift:</label>
                <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" name="shift_id" id="shift_filter">
                    <option value="">All Shifts</option>
                    @foreach($shiftCodes as $shift)
                        <option value="{{ $shift->id }}" {{ $shiftId == $shift->id ? 'selected' : '' }}>
                            {{ $shift->sc_code }} - {{ $shift->sc_shift_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Staff Name:</label>
                <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" name="user_name" value="{{ $userName }}" placeholder="Enter staff name">
            </div>
        </div>
        <div class="flex justify-end gap-2 mt-4">
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-red-600">
                <i class="fas fa-filter mr-2"></i> Filter
            </button>
            <a href="{{ route('daily-schedules.weekly-report_v2') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                <i class="fas fa-times mr-2"></i> Clear
            </a>
        </div>
    </form>
</div>

<!-- Report Content Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
    <!-- Color Legend -->
    <div class="bg-gray-50 rounded-lg p-4 mb-6 border border-gray-200">
        <h6 class="text-sm font-semibold text-gray-700 mb-3">Shift Color Legend:</h6>
        <div class="flex flex-wrap gap-3">
            <div class="flex items-center bg-white px-3 py-2 rounded-lg border border-gray-200 shadow-sm">
                <span class="w-5 h-5 rounded border border-gray-300 mr-2" style="background-color: #e5f7ff;"></span>
                <span class="text-sm text-gray-700">Shift 1 & Shift 0</span>
            </div>
            <div class="flex items-center bg-white px-3 py-2 rounded-lg border border-gray-200 shadow-sm">
                <span class="w-5 h-5 rounded border border-gray-300 mr-2" style="background-color: #e6ffe5;"></span>
                <span class="text-sm text-gray-700">Shift 2</span>
            </div>
            <div class="flex items-center bg-white px-3 py-2 rounded-lg border border-gray-200 shadow-sm">
                <span class="w-5 h-5 rounded border border-gray-300 mr-2" style="background-color: #FFF9ED;"></span>
                <span class="text-sm text-gray-700">Full & Full Shift 0</span>
            </div>
            <div class="flex items-center bg-white px-3 py-2 rounded-lg border border-gray-200 shadow-sm">
                <span class="w-5 h-5 rounded border border-gray-300 mr-2" style="background-color: #f8e8e6;"></span>
                <span class="text-sm text-gray-700">Sakit, Libur & Izin</span>
            </div>
        </div>
    </div>

    @if(count($groupedSchedules) > 0)
        @foreach($groupedSchedules as $divisionName => $users)
            <!-- Division Header -->
            <div class="mb-6">
                <h4 class="text-lg font-bold text-blue-600 border-b-2 border-blue-200 pb-2 mb-4">
                    <i class="fas fa-building mr-2"></i> {{ $divisionName }}
                    <span class="ml-2 px-2 py-1 text-sm font-medium text-gray-700 bg-gray-100 rounded">{{ count($users) }} staff</span>
                </h4>
            </div>

            <!-- Schedule Table for this Division -->
            <div class="overflow-x-auto mb-8">
                <table class="min-w-full border-collapse border border-gray-300 schedule-report-table" id="schedule-table-{{ $loop->index }}">
                    <thead class="bg-blue-50">
                        <tr>
                            <th rowspan="2" class="border border-gray-300 text-center align-middle px-4 py-3 font-semibold text-gray-700" style="min-width: 240px; max-width: 240px;">NAMA</th>
                            @foreach($weekDates as $date)
                                @php
                                    $isToday = date('Y-m-d') === $date;
                                    $dateClass = $isToday ? 'today-column' : 'other-date-column';
                                @endphp
                                <th colspan="2" class="{{ $dateClass }} border border-gray-300 text-center px-3 py-2" style="min-width: 120px;">
                                    <div class="font-semibold">{{ date('l', strtotime($date)) }}</div>
                                    <div class="text-xs text-gray-600">{{ date('d M', strtotime($date)) }}</div>
                                </th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach($weekDates as $date)
                                @php
                                    $isToday = date('Y-m-d') === $date;
                                    $dateClass = $isToday ? 'today-column' : 'other-date-column';
                                @endphp
                                <th class="text-center bg-blue-100 {{ $dateClass }} border border-gray-300 px-2 py-2" style="width: 60px;">
                                    <small class="text-xs font-medium">Shift Name</small>
                                </th>
                                <th class="text-center bg-yellow-100 {{ $dateClass }} border border-gray-300 px-2 py-2" style="width: 60px;">
                                    <small class="text-xs font-medium">Start-End Shift</small>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td class="border border-gray-300 align-middle font-semibold px-4 py-3 text-sm" style="width: 200px;">
                                    {{ $user['u_name'] }}
                                    <br>
                                    <small class="text-gray-500">{{ $user['u_nip'] }}</small>
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
                                        
                                        // Color coding based on shift name
                                        $cellClass = '';
                                        if ($schedule && $shiftName) {
                                            if (strpos(strtolower($shiftName), 'shift 1') !== false || strpos(strtolower($shiftName), 'shift 0') !== false) {
                                                $cellClass = 'bg-blue-50'; // light blue for shift 1
                                            } elseif (strpos(strtolower($shiftName), 'shift 2') !== false) {
                                                $cellClass = 'bg-green-50'; // light green for shift 2
                                            } elseif (strpos(strtolower($shiftName), 'full') !== false) {
                                                $cellClass = 'bg-yellow-50'; // light yellow for full
                                            } elseif (strpos(strtolower($shiftName), 'sakit') !== false || strpos(strtolower($shiftName), 'libur') !== false || strpos(strtolower($shiftName), 'izin') !== false) {
                                                $cellClass = 'bg-red-50'; // light red for sakit/libur/izin
                                            } else {
                                                $cellClass = 'bg-blue-50'; // default blue for other shifts
                                            }
                                        } elseif ($shiftCode) {
                                            // Fallback to old logic for shift codes
                                            if (in_array($shiftCode, ['L', 'LL', 'LPH'])) {
                                                $cellClass = 'bg-green-50'; // Green for leave
                                            } elseif ($shiftCode == 'N/A') {
                                                $cellClass = 'bg-gray-50'; // Gray for not set
                                            } else {
                                                $cellClass = 'bg-blue-50'; // Blue for work shifts
                                            }
                                        }
                                    @endphp
                                    
                                    <!-- Shift Name Column -->
                                    <td class="text-center align-middle {{ $cellClass }} {{ $dateClass }} border border-gray-300 px-2 py-2 text-sm">
                                        @if($schedule)
                                            <div class="font-semibold">{{ $shiftName ?: $shiftCode }}</div>
                                            @if($shiftName)
                                                <small class="text-gray-600">{{ $shiftCode }}</small>
                                            @endif
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    
                                    <!-- Start Shift Column (Time Range) -->
                                    <td class="text-center align-middle {{ $cellClass }} {{ $dateClass }} border border-gray-300 px-2 py-2 text-sm">
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
                                                <span class="text-gray-400">-</span>
                                            @elseif($startTime && $endTime)
                                                <span class="font-semibold">{{ date('H:i', strtotime($startTime)) }} - {{ date('H:i', strtotime($endTime)) }}</span>
                                            @elseif($startTime)
                                                <span class="font-semibold">{{ date('H:i', strtotime($startTime)) }}</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        @elseif($schedule && $shiftCode)
                                            @if(in_array($shiftCode, ['L', 'LL', 'LPH']))
                                                <span class="text-gray-400">-</span>
                                            @elseif($startTime && $endTime)
                                                <span class="font-semibold">{{ date('H:i', strtotime($startTime)) }} - {{ date('H:i', strtotime($endTime)) }}</span>
                                            @elseif($startTime)
                                                <span class="font-semibold">{{ date('H:i', strtotime($startTime)) }}</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">-</span>
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
        <div class="text-center py-12">
            <div class="text-gray-400">
                @if($dateFilter === 'now')
                    <i class="fas fa-clock text-6xl mb-4"></i>
                    <h4 class="text-xl font-semibold text-gray-600 mt-3">No Staff Currently Working</h4>
                    <p class="text-gray-500 mt-2">There are no staff working at this hour ({{ date('H:i') }}).</p>
                    <small class="text-gray-400 block mt-2">Filter displays staff with schedules: Start Work ≤ NOW ≤ End Work</small>
                @else
                    <i class="fas fa-info-circle text-6xl mb-4"></i>
                    <h4 class="text-xl font-semibold text-gray-600 mt-3">No Schedule Data Found</h4>
                    <p class="text-gray-500 mt-2">No schedule data available for the selected week and filters.</p>
                @endif
            </div>
        </div>
    @endif
</div>

<style>
@media print {
    .schedule-report-table {
        font-size: 10px !important;
    }
    
    .schedule-report-table td,
    .schedule-report-table th {
        padding: 4px !important;
    }
}

.schedule-report-table {
    font-size: 12px;
    border-collapse: collapse;
    transition: width 0.3s ease;
}

.schedule-report-table th,
.schedule-report-table td {
    border: 1px solid #dee2e6 !important;
    vertical-align: middle;
}

.schedule-report-table th {
    background-color: #f8f9fa !important;
    font-weight: 600;
}

/* Hide other date columns when filter is NOW */
.other-date-column {
    transition: all 0.3s ease;
}

.filter-now .other-date-column {
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

@include('app.updated_daily_schedule.weekly_report_js')
@endsection
