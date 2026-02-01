@extends('layouts.app_v2')

@section('content')
<div class="space-y-6 pb-12">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 bg-white rounded-lg border border-gray-200 p-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Ringkasan penjualan POS</p>
        </div>
        <div class="flex gap-2">
            <select id="st_id_filter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">- Pilih Store -</option>
                @foreach ($data['st_id'] as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Date Range Picker -->
    <div class="bg-blue-600 rounded-lg p-4">
        <input type="hidden" id="pos_summary_date" value=""/>
        <button type="button" class="btn-date-info font-weight-bold text-white hover:underline cursor-pointer w-full text-left" id="kt_dashboard_daterangepicker">
            <span class="text-blue-200 text-sm font-weight-bold mr-2" id="kt_dashboard_daterangepicker_title">Today</span>
            <span class="text-white text-sm font-weight-bolder" id="kt_dashboard_daterangepicker_date"></span>
        </button>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Offline Chart -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">OFFLINE</h3>
            </div>
            <div class="p-5">
                <div id="offline_chart"></div>
            </div>
        </div>

        <!-- Online Chart -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">ONLINE</h3>
            </div>
            <div class="p-5">
                <div id="online_chart"></div>
            </div>
        </div>
    </div>

    <!-- Cross Chart (hidden by default) -->
    <div class="hidden cross_chart bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">Cross Order</h3>
        </div>
        <div class="p-5">
            <div id="cross_chart"></div>
        </div>
    </div>

    <!-- Tables Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Online Table -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">ONLINE</h3>
            </div>
            <div class="p-5">
                <div class="datatable-container overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200" id="Onlinetb">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Sales</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Penjualan</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Penjualan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Data will be loaded by SimpleDatatables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Offline Table -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">OFFLINE</h3>
            </div>
            <div class="p-5">
                <div class="datatable-container overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200" id="Offlinetb">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Sales</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Penjualan</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Penjualan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Data will be loaded by SimpleDatatables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Rating Table -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">
                    <span class="btn-sm btn-primary font-weight-bold fs-4" id="article_power_label">Product Rating</span>
                </h3>
                <button type="button" id="export_excel" class="px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition-colors">
                    <i class="fas fa-file-excel mr-2"></i>Excel
                </button>
            </div>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <select id="st_filter" class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value=''>- Storage -</option>
                        @foreach ($data['st_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select id="pc_filter" class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value=''>- Kategori -</option>
                        @foreach ($data['pc_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select id="specific_filter" class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value=''>- Parameter (Max) -</option>
                        <option value='sales'>Penjualan</option>
                        <option value='profit'>Profit</option>
                        <option value='aging'>Aging</option>
                    </select>
                </div>
            </div>
            <div class="datatable-container overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200" id="ProductRatingtb">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Power (rating)</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Brand</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Artikel</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aging</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">HJ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Terjual</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Data will be loaded by SimpleDatatables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('app.updated_pos_summary.pos_summary_modal')
@endsection

@push('styles')
@include('app.updated_pos_summary.pos_summary_css')
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{ asset('cdn') }}/jquery.table2excel.js?v2"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@include('app.updated_pos_summary.pos_summary_js')
@endpush
