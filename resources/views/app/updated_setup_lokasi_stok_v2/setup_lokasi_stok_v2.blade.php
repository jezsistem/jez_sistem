@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white rounded-lg border border-gray-200 p-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Mutasi stock antar bin lokasi</p>
        </div>
        <div class="flex items-center gap-3">
            <select id="st_id_filter" name="st_id_filter" 
                    class="px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                <option value="">- Pilih Store -</option>
                @foreach ($data['st_id'] as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Multibin Button -->
    <div class="flex">
        <button type="button" id="multibin_btn"
                class="px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
            <i class="fas fa-exchange-alt"></i>
            Multibin to Multibin Mutations
        </button>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Panel - Start Bin -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="p-5">
                <!-- Bin Selector -->
                <div class="mb-4" id="start">
                    <!-- Bin selector will be loaded here -->
                </div>
                
                <!-- Search and Total -->
                <div class="flex items-center gap-3 mb-4">
                    <input type="search" id="article_search"
                           class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Cari artikel / brand / warna">
                    <input type="text" id="total_start_article" readonly
                           class="w-28 px-3 py-2.5 border border-gray-300 rounded-lg text-sm bg-gray-50 text-center">
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-center gap-3 mb-4">
                    <button type="button" id="ImportModalBtn"
                            class="px-4 py-2 bg-blue-50 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-100 transition-colors flex items-center gap-2 border border-blue-200">
                        <i class="fas fa-file-import"></i>
                        Import
                    </button>
                    <button type="button" id="CancelBtn"
                            class="px-4 py-2 bg-red-50 text-red-700 text-sm font-medium rounded-lg hover:bg-red-100 transition-colors flex items-center gap-2 border border-red-200">
                        <i class="fas fa-times-circle"></i>
                        Cancel
                    </button>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200" id="StartBintb">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Artikel</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Stok</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Mutasi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        </tbody>
                    </table>
                </div>
                
                <!-- Mutation Button -->
                <div class="flex justify-end mt-4">
                    <button type="button" id="mutation_btn"
                            class="px-5 py-2.5 bg-green-600 text-white text-sm font-bold rounded-lg hover:bg-green-700 transition-colors">
                        Mutasi
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Panel - End Bin -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="p-5">
                <!-- Bin Selector -->
                <div class="mb-4" id="end">
                    <!-- Bin selector will be loaded here -->
                </div>
                
                <!-- Search and Total -->
                <div class="flex items-center gap-3 mb-4">
                    <input type="search" id="article_end_search"
                           class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Cari artikel / brand / warna">
                    <input type="text" id="total_end_article" readonly
                           class="w-28 px-3 py-2.5 border border-gray-300 rounded-lg text-sm bg-gray-50 text-center">
                </div>

                <!-- Table -->
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200" id="EndBintb">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Artikel</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Stok</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- History Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <!-- Header -->
        <div class="p-5 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <h3 class="text-lg font-semibold text-gray-900">History Setup</h3>
                <div class="flex items-center gap-3">
                    <button type="button" id="kt_dashboard_daterangepicker"
                            class="px-4 py-2 bg-blue-50 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-100 transition-colors flex items-center gap-2 border border-blue-200 cursor-pointer">
                        <i class="fas fa-calendar-alt"></i>
                        <span id="kt_dashboard_daterangepicker_title">Today</span>
                        <span id="kt_dashboard_daterangepicker_date" class="font-bold"></span>
                    </button>
                    <button type="button" id="export_btn"
                            class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 transition-colors flex items-center gap-2">
                        <i class="fas fa-file-export"></i>
                        Export Data
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="p-5 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <input type="search" id="history_search"
                       class="w-full sm:w-80 px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="Cari Artikel / Sku / User">
                <div class="w-full sm:w-64" id="history_start">
                    <!-- History start bin selector -->
                </div>
                <div class="w-full sm:w-64" id="history_end">
                    <!-- History end bin selector -->
                </div>
            </div>
        </div>
        
        <!-- Table -->
        <div class="p-5">
            <div class="overflow-x-auto border border-gray-200 rounded-lg" style="min-height: 50vh;">
                <table class="min-w-full divide-y divide-gray-200" id="BinHistorytb">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Artikel</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">SKU</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Store</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">BIN Awal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">QBIN Awal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Qty Mts</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">NQBIN Awal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">BIN Tujuan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Catatan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('app.updated_setup_lokasi_stok_v2.setup_lokasi_stok_v2_modal')
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* DataTables styling for V2 layout */
    .dataTables_wrapper .dataTables_length select {
        padding: 0.5rem 2rem 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.25rem 0.75rem;
        margin: 0 0.125rem;
        font-size: 0.875rem;
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f3f4f6;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #2563eb;
        color: white !important;
        border-color: #2563eb;
    }
    .dataTables_wrapper .dataTables_info {
        font-size: 0.875rem;
        color: #4b5563;
        padding: 0.5rem 0;
    }
    
    /* Select2 styling */
    .select2-container {
        width: 100% !important;
    }
    .select2-container--default .select2-selection--single {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        height: 42px;
        padding: 0.25rem 0;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
        line-height: 1.75rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
    .select2-dropdown {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
    }
    .select2-search--dropdown .select2-search__field {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.5rem;
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: #2563eb;
    }
    
    /* Table cell styling */
    #StartBintb td, #EndBintb td, #BinHistorytb td {
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        vertical-align: middle;
    }
    
    /* Input styling for mutation qty */
    input[type="number"].mutation-input,
    input#mutation_qty {
        width: 70px;
        padding: 0.375rem 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        text-align: center;
    }
    input#mutation_qty:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
    }
    
    /* Button styling from server response */
    #StartBintb .btn,
    #EndBintb .btn,
    #BinHistorytb .btn {
        display: inline-block;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
        line-height: 1.5;
        text-align: center;
        text-decoration: none;
        vertical-align: middle;
        cursor: pointer;
        border: 1px solid transparent;
        border-radius: 0.375rem;
        transition: all 0.15s ease-in-out;
    }
    #StartBintb .btn-sm,
    #EndBintb .btn-sm,
    #BinHistorytb .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.8125rem;
    }
    #StartBintb .btn-primary,
    #EndBintb .btn-primary,
    #BinHistorytb .btn-primary {
        background-color: #3b82f6;
        border-color: #3b82f6;
        color: #fff;
    }
    #StartBintb .btn-primary:hover,
    #EndBintb .btn-primary:hover,
    #BinHistorytb .btn-primary:hover {
        background-color: #2563eb;
        border-color: #2563eb;
        color: #fff;
    }
    #StartBintb .btn-success,
    #EndBintb .btn-success,
    #BinHistorytb .btn-success {
        background-color: #10b981;
        border-color: #10b981;
        color: #fff;
    }
    #StartBintb .btn-success:hover,
    #EndBintb .btn-success:hover,
    #BinHistorytb .btn-success:hover {
        background-color: #059669;
        border-color: #059669;
    }
    #StartBintb .btn-warning,
    #EndBintb .btn-warning,
    #BinHistorytb .btn-warning {
        background-color: #f59e0b;
        border-color: #f59e0b;
        color: #fff;
    }
    #StartBintb .btn-danger,
    #EndBintb .btn-danger,
    #BinHistorytb .btn-danger {
        background-color: #ef4444;
        border-color: #ef4444;
        color: #fff;
    }
    #StartBintb .btn-info,
    #EndBintb .btn-info,
    #BinHistorytb .btn-info {
        background-color: #06b6d4;
        border-color: #06b6d4;
        color: #fff;
    }
    #StartBintb .btn-light,
    #EndBintb .btn-light,
    #BinHistorytb .btn-light {
        background-color: #f3f4f6;
        border-color: #d1d5db;
        color: #374151;
    }
    
    /* Bootstrap grid classes for buttons */
    .col-2 { width: 16.666667%; }
    .col-3 { width: 25%; }
    .col-4 { width: 33.333333%; }
    .col-7 { width: 58.333333%; }
    
    /* Form control styling */
    #StartBintb .form-control,
    #EndBintb .form-control {
        display: block;
        width: 100%;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 400;
        line-height: 1.5;
        color: #1f2937;
        background-color: #fff;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    #StartBintb .form-control:focus,
    #EndBintb .form-control:focus {
        border-color: #3b82f6;
        outline: 0;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.25);
    }
    
    /* Flex utilities */
    .d-flex { display: flex !important; }
    .align-items-center { align-items: center !important; }
    .mb-2 { margin-bottom: 0.5rem !important; }
    .pb-2 { padding-bottom: 0.5rem !important; }
    .d-none { display: none !important; }
</style>
@endpush

@push('scripts')
<!-- Select2 JS (jQuery already loaded in layout) -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Toastr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!-- XLSX -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<!-- Moment & DateRangePicker -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

@include('app.updated_setup_lokasi_stok_v2.setup_lokasi_stok_v2_js')
@endpush
