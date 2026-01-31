@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('break-times-backup.report_v2') }}" class="px-4 py-2 text-sm font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200">
                <i class="fas fa-chart-bar mr-2"></i>View Report
            </a>
            <a href="{{ route('break-times-backup.summary-report_v2') }}" class="px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-100 rounded-lg hover:bg-indigo-200">
                <i class="fas fa-chart-line mr-2"></i>Summary Report
            </a>
        </div>
    </div>
</div>

<!-- Break Control Panel -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <!-- Break Button Card -->
    <div class="bg-red-500 rounded-xl shadow-lg p-8">
        <div class="text-center">
            <div class="mb-4">
                <i class="fas fa-coffee text-white text-5xl"></i>
            </div>
            <button type="button" id="mainBreakButton" class="w-full bg-white text-gray-800 font-semibold py-4 px-6 rounded-lg hover:bg-gray-100 transition-colors text-lg min-h-[60px]">
                <span id="breakButtonText">Start Break</span>
            </button>
            <div id="breakTimer" class="text-white mt-4 hidden">
                <div class="text-3xl font-bold mb-2">
                    <i class="fas fa-clock mr-2"></i><span id="timerDisplay">00:00</span>
                </div>
                <div class="text-white text-sm opacity-90">
                    <span id="breakTypeDisplay">Break Time</span> - <span id="durationDisplay">Remaining</span>
                </div>
            </div>
        </div>
        
        <!-- Break Allowance Info -->
        <div class="mt-4 p-3 bg-white bg-opacity-20 rounded-lg flex items-center text-white">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Break Allowance:</strong> <span id="allowanceText" class="ml-2">Loading...</span>
        </div>
    </div>
    
    <!-- Currently on Break List -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200">
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Currently on Break</h3>
            <select id="divisionFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-1.5">
                <option value="">All Divisions</option>
                @foreach(\App\Models\UserDivision::where('ud_status', 'active')->get() as $division)
                    <option value="{{ $division->id }}">{{ $division->ud_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="p-4 max-h-[300px] overflow-y-auto" id="currentBreakList">
            <div class="text-center text-gray-500 py-4">
                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                <p>Loading...</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('app._partials.js')
    @include('app.updated_break_time_backup.break_time_js')
@endpush
@endsection
