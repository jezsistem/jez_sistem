@extends('layouts.app_v2')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ date('d M Y', strtotime($startDate)) }} - {{ date('d M Y', strtotime($endDate)) }}</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 text-sm font-medium text-white bg-gray-800 rounded-full">
                {{ $currentUser->up_code ?? 'Unknown Position' }}
            </span>
            @if($currentUser->current_division_name)
            <span class="px-3 py-1 text-sm font-medium text-gray-700 bg-gray-200 rounded-full">
                {{ $currentUser->current_division_name }}
            </span>
            @endif
        </div>
    </div>
</div>

<!-- Alert Area for Import Results -->
<div id="importAlertArea" class="mb-4 hidden">
    <!-- Alerts will be dynamically inserted here -->
</div>

<!-- Filters Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filters</h3>
    <form method="GET" id="filterForm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            @if(in_array($currentUser->up_code ?? '', ['DIRECTOR', 'MANAGER', 'SUPERVISOR']))
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Division</label>
                <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 {{ in_array($currentUser->up_code ?? '', ['SUPERVISOR']) ? 'bg-gray-100' : '' }}" 
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
                    <small class="text-gray-500 text-xs mt-1 block">You can only view your own division</small>
                @endif
            </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Tanggal</label>
                <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="date_filter" name="date_filter">
                    <option value="this_week" {{ $dateFilter == 'this_week' ? 'selected' : '' }}>This Week</option>
                    <option value="past_week" {{ $dateFilter == 'past_week' ? 'selected' : '' }}>Last Week</option>
                    <option value="custom" {{ $dateFilter == 'custom' ? 'selected' : '' }}>Custom Range</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Rentang Minggu</label>
                <input type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" name="start_date" value="{{ $startDate }}">
                <small class="text-gray-500 text-xs mt-1 block">Select Monday to display full week</small>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Staff</label>
                <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="search_filter" name="search" placeholder="Cari berdasarkan nama atau NIP..." value="{{ $search ?? '' }}">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                @if(in_array($currentUser->up_code ?? '', ['DIRECTOR', 'MANAGER']))
                <button type="button" class="px-4 py-2.5 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900" id="load_schedule" onclick="loadScheduleDirectly()">
                    <i class="fas fa-sync mr-2"></i>Load
                </button>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Schedule Table Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Weekly Schedule</h3>
        <div class="flex items-center gap-2">
            <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200" onclick="loadExistingSchedules()">
                <i class="fas fa-sync mr-2"></i>Load Schedule
            </button>
            <button type="button" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700" onclick="showImportModal()">
                <i class="fas fa-upload mr-2"></i>Import Schedule
            </button>
            <div class="relative">
                <button type="button" id="export_weekly_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                    <i class="fas fa-download mr-2"></i>Export
                </button>
                <div id="export_weekly_menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                    <div class="py-1">
                        <button type="button" id="export_weekly_excel_btn" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-excel mr-2"></i>Excel
                        </button>
                        <button type="button" id="export_weekly_pdf_btn" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-pdf mr-2"></i>PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500" id="weeklyScheduleTable">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3" style="min-width: 80px;">NIP</th>
                    <th scope="col" class="px-3 py-3" style="min-width: 200px;">Staff</th>
                    <th scope="col" class="px-3 py-3" style="min-width: 150px;">Division</th>
                    <th scope="col" class="px-3 py-3" style="min-width: 100px;">User Type</th>
                    <th scope="col" class="px-3 py-3 text-center date-header" data-date="{{ $startDate }}" id="date-header-0" style="min-width: 100px;">{{ date('D d-M', strtotime($startDate)) }}</th>
                    <th scope="col" class="px-3 py-3 text-center date-header" data-date="{{ date('Y-m-d', strtotime($startDate . ' +1 day')) }}" id="date-header-1" style="min-width: 100px;">{{ date('D d-M', strtotime($startDate . ' +1 day')) }}</th>
                    <th scope="col" class="px-3 py-3 text-center date-header" data-date="{{ date('Y-m-d', strtotime($startDate . ' +2 day')) }}" id="date-header-2" style="min-width: 100px;">{{ date('D d-M', strtotime($startDate . ' +2 day')) }}</th>
                    <th scope="col" class="px-3 py-3 text-center date-header" data-date="{{ date('Y-m-d', strtotime($startDate . ' +3 day')) }}" id="date-header-3" style="min-width: 100px;">{{ date('D d-M', strtotime($startDate . ' +3 day')) }}</th>
                    <th scope="col" class="px-3 py-3 text-center date-header" data-date="{{ date('Y-m-d', strtotime($startDate . ' +4 day')) }}" id="date-header-4" style="min-width: 100px;">{{ date('D d-M', strtotime($startDate . ' +4 day')) }}</th>
                    <th scope="col" class="px-3 py-3 text-center date-header" data-date="{{ date('Y-m-d', strtotime($startDate . ' +5 day')) }}" id="date-header-5" style="min-width: 100px;">{{ date('D d-M', strtotime($startDate . ' +5 day')) }}</th>
                    <th scope="col" class="px-3 py-3 text-center date-header" data-date="{{ date('Y-m-d', strtotime($startDate . ' +6 day')) }}" id="date-header-6" style="min-width: 100px;">{{ date('D d-M', strtotime($startDate . ' +6 day')) }}</th>
                </tr>
            </thead>
            <tbody id="schedule_tbody">
                @foreach($users as $user)
                @php
                    $userType = $user->ut_name ?: 'FULL TIME';
                    $baseShiftCodes = $shiftCodesByType[$userType] ?? $shiftCodesByType['FULL TIME'];
                    $userExistingShifts = $existingSchedules->where('user_id', $user->id);
                    $existingShiftIds = $userExistingShifts->pluck('sc_id')->unique();
                    $availableShiftCodes = collect($baseShiftCodes);
                    foreach($existingShiftIds as $existingShiftId) {
                        if($existingShiftId && !$availableShiftCodes->contains('id', $existingShiftId)) {
                            $existingShift = $shiftCodes->firstWhere('id', $existingShiftId);
                            if($existingShift) {
                                $availableShiftCodes->push($existingShift);
                            }
                        }
                    }
                    $availableShiftCodes = $availableShiftCodes->sortBy('sc_code');
                @endphp
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-3 py-2">{{ $user->u_nip ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $user->u_name }}</td>
                    <td class="px-3 py-2">{{ $user->ud_name ?? '-' }}</td>
                    <td class="px-3 py-2"><span class="px-2 py-1 text-xs font-medium text-blue-800 bg-blue-100 rounded-full">{{ $userType }}</span></td>
                    @for($i = 0; $i < 7; $i++)
                    @php
                        $date = date('Y-m-d', strtotime($startDate . ' +' . $i . ' day'));
                        $existingSchedule = $existingSchedules->where('user_id', $user->id)->where('ds_date', $date)->first();
                    @endphp
                    <td class="px-3 py-2 schedule-cell bg-gray-50" data-user-id="{{ $user->id }}" data-date="{{ $date }}">
                        <select class="shift-select w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white" data-user-id="{{ $user->id }}" data-date="{{ $date }}" onchange="saveScheduleDirectly(this)">
                            <option value="">-</option>
                            @foreach($availableShiftCodes as $shiftCode)
                                <option value="{{ $shiftCode->id }}" {{ $existingSchedule && $existingSchedule->sc_id == $shiftCode->id ? 'selected' : '' }}>{{ $shiftCode->sc_code }}</option>
                            @endforeach
                        </select>
                    </td>
                    @endfor
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@include('app.updated_daily_schedule.weekly_schedule_modal')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('app._partials.js')
    @include('app.updated_daily_schedule.weekly_schedule_js')
@endpush
@endsection
