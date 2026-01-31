@extends('layouts.app_v2')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ date('F Y', strtotime($startDate)) }}</p>
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
    <form method="GET" action="{{ route('daily-schedules.monthly-report_v2') }}" id="filterForm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Month Filter:</label>
                <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" name="month_filter" id="month_filter">
                    <option value="this_month" {{ request('month_filter', 'this_month') == 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ request('month_filter', 'this_month') == 'last_month' ? 'selected' : '' }}>Past Month</option>
                    <option value="next_month" {{ request('month_filter', 'this_month') == 'next_month' ? 'selected' : '' }}>Next Month</option>
                    <option value="custom" {{ request('month_filter', 'this_month') == 'custom' ? 'selected' : '' }}>Custom Month</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Custom Month:</label>
                <input type="month" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" name="month" id="custom_month" value="{{ $month }}">
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
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                <i class="fas fa-filter mr-2"></i> Filter
            </button>
            <a href="{{ route('daily-schedules.monthly-report_v2') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
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
        @foreach($groupedSchedules as $divisionName => $divisionUsers)
            <!-- Division Header -->
            <div class="mb-6">
                <h4 class="text-lg font-bold text-blue-600 border-b-2 border-blue-200 pb-2 mb-4">
                    <i class="fas fa-building mr-2"></i> {{ $divisionName }}
                    <span class="ml-2 px-2 py-1 text-sm font-medium text-gray-700 bg-gray-100 rounded">{{ count($divisionUsers) }} staff</span>
                </h4>
            </div>

            <!-- Calendar View for each user -->
            @foreach($divisionUsers as $userId => $userData)
                <div class="bg-white border border-gray-200 rounded-lg p-5 mb-5">
                    <div class="border-b border-gray-200 pb-3 mb-4">
                        <h5 class="text-lg font-bold text-gray-900">
                            <i class="fas fa-user mr-2"></i> {{ $userData['u_name'] }}
                            <span class="text-gray-500 ml-2">({{ $userData['u_nip'] }})</span>
                            <span class="text-gray-500 ml-2">- {{ $userData['position_name'] }}</span>
                        </h5>
                    </div>
                    
                    <!-- Calendar Grid -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <!-- Calendar Header -->
                        <div class="bg-gray-50">
                            <div class="flex">
                                <div class="flex-1 text-center font-semibold py-3 border-r border-gray-200">Mon</div>
                                <div class="flex-1 text-center font-semibold py-3 border-r border-gray-200">Tue</div>
                                <div class="flex-1 text-center font-semibold py-3 border-r border-gray-200">Wed</div>
                                <div class="flex-1 text-center font-semibold py-3 border-r border-gray-200">Thu</div>
                                <div class="flex-1 text-center font-semibold py-3 border-r border-gray-200">Fri</div>
                                <div class="flex-1 text-center font-semibold py-3 border-r border-gray-200 bg-gray-100">Sat</div>
                                <div class="flex-1 text-center font-semibold py-3 bg-gray-100">Sun</div>
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
                            <div class="flex border-b border-gray-200 last:border-b-0">
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
                                            if (strpos(strtolower($schedule['sc_shift_name']), 'shift 1') !== false ) {
                                                $cellColor = '#e5f7ff'; // light blue
                                            } elseif (strpos(strtolower($schedule['sc_shift_name']), 'shift 2') !== false) {
                                                $cellColor = '#e6ffe5'; // light green
                                            } elseif (strpos(strtolower($schedule['sc_shift_name']), 'full') !== false) {
                                                $cellColor = '#FFF9ED'; // light yellow
                                            } elseif (strpos(strtolower($schedule['sc_shift_name']), 'shift 0') !== false) {
                                                $cellColor = '#e5f7ff'; // light blue
                                            } elseif (strpos(strtolower($schedule['sc_shift_name']), 'sakit') !== false || strpos(strtolower($schedule['sc_shift_name']), 'libur') !== false || strpos(strtolower($schedule['sc_shift_name']), 'izin') !== false) {
                                                $cellColor = '#f8e8e6'; // light red
                                            }
                                        }
                                    @endphp
                                    
                                    <div class="flex-1 min-h-[120px] border-r border-gray-200 last:border-r-0 p-2 {{ $isWeekend ? 'bg-gray-50' : '' }} {{ $isToday ? 'bg-yellow-50 border-2 border-yellow-400' : '' }} {{ !$isCurrentMonth ? 'opacity-60' : '' }}" style="background-color: {{ $schedule ? $cellColor : 'transparent' }};">
                                        <div class="text-center mb-2">
                                            <span class="text-lg font-bold block">{{ $dateInfo['day'] }}</span>
                                            <span class="text-xs text-gray-600">{{ $dateInfo['day_name'] }}</span>
                                        </div>
                                        
                                        @if($schedule)
                                            <div class="text-center text-sm">
                                                <div class="font-semibold">{{ $schedule['sc_code'] }}</div>
                                                <div class="text-xs">{{ $schedule['sc_shift_name'] }}</div>
                                                <div class="text-xs text-gray-600 mt-1">
                                                    {{ date('H:i', strtotime($schedule['sc_start_time'])) }} - {{ date('H:i', strtotime($schedule['sc_end_time'])) }}
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-center text-gray-400 text-sm mt-4">
                                                No Schedule
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                                
                                <!-- Fill remaining cells if week is incomplete -->
                                @for($i = count($week); $i < 7; $i++)
                                    <div class="flex-1 min-h-[120px] border-r border-gray-200 last:border-r-0 bg-gray-50"></div>
                                @endfor
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endforeach
    @else
        <div class="text-center py-12">
            <i class="fas fa-calendar-alt text-6xl text-gray-400 mb-4"></i>
            <h4 class="text-xl font-semibold text-gray-600 mt-3">No schedules found</h4>
            <p class="text-gray-500 mt-2">Try adjusting your filters to see more results.</p>
        </div>
    @endif
</div>

@include('app.updated_daily_schedule.monthly_report_js')
@endsection
