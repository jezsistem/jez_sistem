@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Overview of your business performance</p>
                </div>
        <div class="flex items-center gap-3">
            <a href="{{ url('asset_detail') }}" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors">
                <i class="cft-standard-stroke cft-file mr-2"></i>
                        Detail Asset/Penjualan
                    </a>
            <button id="detail_activity_btn" class="px-4 py-2 bg-red-400 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition-colors">
                <i class="cft-standard-stroke cft-user mr-2"></i>
                        Aktifitas User
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Graph Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Grafik</label>
                <select id="info_filter" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <option value="brand">Grafik Brand</option>
                                                <option value="store">Grafik Store</option>
                                            </select>
                                        </div>

            <!-- Division Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Divisi</label>
                <select id="division_filter" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <option value='all'>- Semua Divisi -</option>
                                                <option value="online">Online</option>
                                                <option value="offline">Offline</option>
                                            </select>
                                        </div>

            <!-- Date Range Picker -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
                <input type="hidden" id="dashboard_date" value=""/>
                <button id="kt_dashboard_daterangepicker" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm hover:bg-gray-50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="cft-standard-stroke cft-calendar text-gray-500"></i>
                        <span id="kt_dashboard_daterangepicker_title" class="text-gray-700 font-medium">Today</span>
                        <span id="kt_dashboard_daterangepicker_date" class="text-gray-500"></span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                </button>
                                    </div>
                                    </div>
                                </div>

    <!-- Store Selection -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <label class="block text-sm font-medium text-gray-700 mb-4">Pilih Store</label>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                                    @foreach ($data['st_id'] as $key => $value)
                <button data-id="{{ $key }}" class="st_selection px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-900 font-medium text-sm rounded-lg border-2 border-transparent hover:border-gray-800 hover:text-gray-900 transition-all">
                    {{ $value }}
                </button>
                                    @endforeach
                                </div>
                            </div>

    <!-- Stats Cards -->
    <div id="summary_reload" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Nett Sales Card -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="cft-standard-solid cft-sales-money text-2xl"></i>
                </div>
                <span class="text-sm bg-white bg-opacity-20 px-3 py-1 rounded-full">Today</span>
                                </div>
            <h3 class="text-sm font-medium mb-1 opacity-90">Nett Sales</h3>
            <p class="text-xl font-semibold" id="nett_sales_value">
                <span class="loading-shimmer">Loading...</span>
            </p>
                    </div>

        <!-- Profits Card -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="cft-standard-solid cft-wallet text-2xl"></i>
                                </div>
                <span class="text-sm bg-white bg-opacity-20 px-3 py-1 rounded-full">Today</span>
                                    </div>
            <h3 class="text-sm font-medium mb-1 opacity-90">Profits</h3>
            <p class="text-xl font-semibold" id="profits_value">
                <span class="loading-shimmer">Loading...</span>
            </p>
                    </div>

        <!-- Purchases Card -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="cft-standard-solid cft-purchase-order text-2xl"></i>
                                </div>
                <span class="text-sm bg-white bg-opacity-20 px-3 py-1 rounded-full">Today</span>
                                    </div>
            <h3 class="text-sm font-medium mb-1 opacity-90">Purchases</h3>
            <p class="text-xl font-semibold" id="purchases_value">
                <span class="loading-shimmer">Loading...</span>
            </p>
                    </div>

        <!-- Assets Card -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="cft-standard-solid cft-store text-2xl"></i>
                                </div>
                <span class="text-sm bg-white bg-opacity-20 px-3 py-1 rounded-full">Total</span>
                                    </div>
            <h3 class="text-sm font-medium mb-1 opacity-90">Total Assets</h3>
            <p class="text-xl font-semibold" id="assets_value">
                <span class="loading-shimmer">Loading...</span>
            </p>
                        </div>
                    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sales Chart -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Nett Sales</h3>
                    <p class="text-sm text-gray-500">Sales performance over time</p>
                                </div>
                <button class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="cft-standard-stroke cft-download mr-2"></i>
                    Export
                </button>
                                    </div>
            <div class="h-80">
                <canvas id="nettSalesChart"></canvas>
                        </div>
                    </div>

        <!-- Profits Chart -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Profits</h3>
                    <p class="text-sm text-gray-500">Profit trends analysis</p>
                                </div>
                <button class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="cft-standard-stroke cft-download mr-2"></i>
                    Export
                </button>
                                    </div>
            <div class="h-80">
                <canvas id="profitsChart"></canvas>
                            </div>
                        </div>
                    </div>

    <!-- Detailed Tables Section -->
    <div id="table_reload" class="bg-white rounded-xl border border-gray-200">
        <!-- Tabs -->
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button class="tab-btn border-b-2 border-blue-600 py-4 px-1 text-sm font-medium text-blue-600" data-tab="sales">
                    Sales Details
                </button>
                <button class="tab-btn border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="profits">
                    Profits Details
                </button>
                <button class="tab-btn border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="purchases">
                    Purchases Details
                </button>
            </nav>
                    </div>
        
        <!-- Tab Content -->
        <div class="p-6">
            <div id="table_content">
                <p class="text-center text-gray-500 py-8">Select a store to view details</p>
                                </div>
                            </div>
                        </div>
                    </div>

<!-- User Activity Modal -->
<div id="modal-activity" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-4xl max-h-full">
        <div class="relative bg-white rounded-lg shadow">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-5 border-b rounded-t">
                <h3 class="text-xl font-semibold text-gray-900">
                    <i class="cft-standard-stroke cft-user mr-2"></i>
                    User Activity Log
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="modal-activity">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
                                </div>
            <!-- Modal body -->
            <div class="p-6 space-y-4 max-h-96 overflow-y-auto">
                @if (!empty($data['activity']))
                    @foreach ($data['activity'] as $act)
                        <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="cft-standard-stroke cft-user text-blue-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $act->u_name }}</p>
                                <p class="text-sm text-gray-600 mt-1">{{ $act->ua_description }}</p>
                                <p class="text-xs text-gray-500 mt-2">{{ $act->ua_created_at }}</p>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-center text-gray-500 py-8">No activity recorded</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <style>
    .loading-shimmer {
        display: inline-block;
        background: linear-gradient(90deg, rgba(255,255,255,0.1) 25%, rgba(255,255,255,0.3) 50%, rgba(255,255,255,0.1) 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
    }
    
    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    
    .st_selection {
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .st_selection:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<script>
$(document).ready(function() {
    // Initialize Charts
    const nettSalesCtx = document.getElementById('nettSalesChart');
    const profitsCtx = document.getElementById('profitsChart');
    
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            }
        }
    };

    const nettSalesChart = new Chart(nettSalesCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Nett Sales',
                data: [],
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: chartOptions
    });

    const profitsChart = new Chart(profitsCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Profits',
                data: [],
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: chartOptions
    });

    // Date Range Picker
    $('#kt_dashboard_daterangepicker').daterangepicker({
        startDate: moment(),
        endDate: moment(),
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, function(start, end, label) {
        // Keep "Today" text if it's today, otherwise show date range
        if (label === 'Today') {
            $('#kt_dashboard_daterangepicker_title').html('Today');
            $('#kt_dashboard_daterangepicker_date').html('');
        } else {
            $('#kt_dashboard_daterangepicker_title').html('');
            $('#kt_dashboard_daterangepicker_date').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
        }
        $('#dashboard_date').val(start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD'));
        
        // Get selected store and reload data
        const selectedStore = $('.st_selection.bg-gray-900').data('id');
        loadDashboardData(selectedStore);
    });

    // Store Selection - Auto run immediately on click
    $('.st_selection').on('click', function() {
        $('.st_selection').removeClass('bg-gray-900 text-white border-gray-900').addClass('bg-gray-100 text-gray-900');
        $(this).removeClass('bg-gray-100 text-gray-900').addClass('bg-gray-900 text-white border-gray-900');
        
        const storeId = $(this).data('id');
        
        // Immediately load data (no need to save to session first)
        loadDashboardData(storeId);
        
        // Also save to session for backend (as array)
        $.ajax({
            url: '{{ url("load_store") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                store: [storeId]  // Convert to array for backend
            }
        });
    });

    // Activity Modal - Simple show/hide without Flowbite
    $('#detail_activity_btn').on('click', function(e) {
        e.preventDefault();
        $('#modal-activity').removeClass('hidden').addClass('flex');
        $('#modal-activity').css('display', 'flex');
        
        // Create backdrop
        const backdrop = $('<div>').attr('id', 'modal-activity-backdrop').css({
            'position': 'fixed',
            'inset': '0',
            'background': 'rgba(0, 0, 0, 0.5)',
            'z-index': '40'
        });
        $('body').append(backdrop);
    });
    
    // Close activity modal
    $('[data-modal-hide="modal-activity"]').on('click', function() {
        $('#modal-activity').removeClass('flex').addClass('hidden');
        $('#modal-activity').css('display', 'none');
        $('#modal-activity-backdrop').remove();
    });

    // Tab Switching
    $('.tab-btn').on('click', function() {
        $('.tab-btn').removeClass('border-blue-600 text-blue-600').addClass('border-transparent text-gray-500');
        $(this).removeClass('border-transparent text-gray-500').addClass('border-blue-600 text-blue-600');
        
        const tab = $(this).data('tab');
        loadTableData(tab);
    });

    // Filter change handlers - Auto run immediately on change
    $('#info_filter, #division_filter').on('change', function() {
        const selectedStore = $('.st_selection.bg-blue-600').data('id');
        loadDashboardData(selectedStore);
    });

    // Load Dashboard Data Function
    function loadDashboardData(storeId = null) {
        const dateRange = $('#dashboard_date').val() || moment().format('YYYY-MM-DD');
        const division = $('#division_filter').val();
        const graphType = $('#info_filter').val();
        
        // Prepare store parameter - convert to array if needed
        let storeParam = [];
        if (storeId) {
            storeParam = Array.isArray(storeId) ? storeId : [storeId];
        }

        $.ajax({
            url: '{{ url("get_summaries") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                date: dateRange,
                store: storeParam,
                division: division,
                label: graphType
            },
            success: function(response) {
                try {
                    const data = JSON.parse(response);
                    
                    // Update stats cards with formatted numbers
                    $('#nett_sales_value').text('Rp ' + data.nett_sales);
                    $('#profits_value').text('Rp ' + data.profit);
                    $('#purchases_value').text('Rp ' + data.purchase);
                    $('#assets_value').text('Rp ' + data.cc_assets);

                    // Load chart data
                    loadChartData(storeParam, dateRange, division);
                } catch(e) {
                    console.error('Error parsing dashboard data:', e);
                    console.log('Response:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading dashboard data:', error);
                console.log('Status:', xhr.status);
                console.log('Response:', xhr.responseText);
                
                // Show error message to user
                $('#nett_sales_value').text('Error');
                $('#profits_value').text('Error');
                $('#purchases_value').text('Error');
                $('#assets_value').text('Error');
            }
        });
    }

    // Load Chart Data - Sales Chart
    function loadChartData(storeId, dateRange, division) {
        const graphType = $('#info_filter').val();
        console.log('Loading charts with graph type:', graphType);
        
        // Ensure storeId is in array format
        const storeParam = storeId && storeId.length > 0 ? storeId : null;
        
        // Determine label based on graph type
        let salesLabel = graphType === 'store' ? 'store' : 'sales';  // 'sales' = by brand
        let profitsLabel = graphType === 'store' ? 'store_profits' : 'profits';  // 'profits' = by brand
        
        console.log('Sales label:', salesLabel);
        console.log('Profits label:', profitsLabel);
        
        // Update chart titles based on graph type
        const chartTypeText = graphType === 'store' ? 'by Store' : 'by Brand';
        console.log('Chart will show data:', chartTypeText);
        
        // Load Sales Graph (by brand or by store)
        $.ajax({
            url: '{{ url("load_graph") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                label: salesLabel,
                date: dateRange,
                store: storeParam,
                division: division
            },
            success: function(response) {
                console.log('Sales chart response received:', response);
                console.log('Response length:', response.length);
                
                // If no data or empty response
                if (!response || response.trim().length === 0) {
                    console.log('Empty response from sales chart');
                    nettSalesChart.data.labels = ['No Data'];
                    nettSalesChart.data.datasets[0].data = [0];
                    nettSalesChart.update();
                    return;
                }
                
                // Parse brands and values from response
                const tempDiv = $('<div>').html(response);
                const scriptContent = tempDiv.find('script').html();
                
                if (scriptContent) {
                    console.log('Script content found for sales');
                    console.log('Script length:', scriptContent.length);
                    try {
                        // Extract data points with x and y format
                        // Format: { x: 'BRAND', y: [VALUE] }
                        const dataMatches = scriptContent.matchAll(/{\s*x:\s*['"]([^'"]+)['"],\s*y:\s*\[([^\]]+)\]/g);
                        const categories = [];
                        const values = [];
                        
                        for (const match of dataMatches) {
                            const brand = match[1];
                            const value = parseFloat(match[2]);
                            categories.push(brand);
                            values.push(value);
                        }
                        
                        console.log('Sales Categories:', categories);
                        console.log('Sales Values:', values);
                        
                        if (categories.length > 0 && values.length > 0) {
                            nettSalesChart.data.labels = categories;
                            nettSalesChart.data.datasets[0].data = values;
                            nettSalesChart.update();
                            console.log('Sales chart updated successfully');
                        } else {
                            console.log('Empty categories or values');
                            nettSalesChart.data.labels = ['No Data'];
                            nettSalesChart.data.datasets[0].data = [0];
                            nettSalesChart.update();
                        }
                    } catch(e) {
                        console.error('Error parsing sales chart:', e);
                        nettSalesChart.data.labels = ['Error'];
                        nettSalesChart.data.datasets[0].data = [0];
                        nettSalesChart.update();
                    }
                } else {
                    console.log('No script content found');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading sales chart:', error);
                nettSalesChart.data.labels = ['Error'];
                nettSalesChart.data.datasets[0].data = [0];
                nettSalesChart.update();
            }
        });

        // Load Profits Graph (by brand or by store)
        $.ajax({
            url: '{{ url("load_graph") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                label: profitsLabel,
                date: dateRange,
                store: storeParam,
                division: division
            },
            success: function(response) {
                console.log('Profits chart response received:', response);
                console.log('Response length:', response.length);
                
                // If no data or empty response
                if (!response || response.trim().length === 0) {
                    console.log('Empty response from profits chart');
                    profitsChart.data.labels = ['No Data'];
                    profitsChart.data.datasets[0].data = [0];
                    profitsChart.update();
                    return;
                }
                
                // Parse brands and values from response
                const tempDiv = $('<div>').html(response);
                const scriptContent = tempDiv.find('script').html();
                
                if (scriptContent) {
                    console.log('Script content found for profits');
                    console.log('Script length:', scriptContent.length);
                    try {
                        // Extract data points with x and y format
                        // Format: { x: 'BRAND', y: [VALUE] }
                        const dataMatches = scriptContent.matchAll(/{\s*x:\s*['"]([^'"]+)['"],\s*y:\s*\[([^\]]+)\]/g);
                        const categories = [];
                        const values = [];
                        
                        for (const match of dataMatches) {
                            const brand = match[1];
                            const value = parseFloat(match[2]);
                            categories.push(brand);
                            values.push(value);
                        }
                        
                        console.log('Profits Categories:', categories);
                        console.log('Profits Values:', values);
                        
                        if (categories.length > 0 && values.length > 0) {
                            profitsChart.data.labels = categories;
                            profitsChart.data.datasets[0].data = values;
                            profitsChart.update();
                            console.log('Profits chart updated successfully');
                        } else {
                            console.log('Empty categories or values');
                            profitsChart.data.labels = ['No Data'];
                            profitsChart.data.datasets[0].data = [0];
                            profitsChart.update();
                        }
                    } catch(e) {
                        console.error('Error parsing profits chart:', e);
                        profitsChart.data.labels = ['Error'];
                        profitsChart.data.datasets[0].data = [0];
                        profitsChart.update();
                    }
                } else {
                    console.log('No script content found');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading profits chart:', error);
                profitsChart.data.labels = ['Error'];
                profitsChart.data.datasets[0].data = [0];
                profitsChart.update();
            }
        });
    }

    function loadTableData(type) {
        const dateRange = $('#dashboard_date').val() || moment().format('YYYY-MM-DD');
        const division = $('#division_filter').val();
        const storeId = $('.st_selection.bg-blue-600').data('id');

        if (!storeId) {
            $('#table_content').html('<p class="text-center text-gray-500 py-8">Please select a store first</p>');
            return;
        }
        
        // Convert to array format
        const storeParam = Array.isArray(storeId) ? storeId : [storeId];

        $.ajax({
            url: '{{ url("load_table") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                label: type,
                date: dateRange,
                store: storeParam,
                division: division
            },
            success: function(response) {
                $('#table_content').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Error loading table data:', error);
                console.log('Response:', xhr.responseText);
                $('#table_content').html('<p class="text-center text-red-500 py-8">Error loading data</p>');
            }
        });
    }

    // Initial setup
    $('#kt_dashboard_daterangepicker_title').html('Today');
    $('#kt_dashboard_daterangepicker_date').html('');
    $('#dashboard_date').val(moment().format('YYYY-MM-DD'));
    
    // Load initial data on page load
    setTimeout(function() {
        loadDashboardData();
    }, 300);
});
</script>
@endpush
