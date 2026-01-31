@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 bg-white rounded-lg border border-gray-200 p-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Tracking pergerakan stock</p>
        </div>
        <div class="flex items-center gap-3">
            <select id="st_id_filter" name="st_id_filter" class="px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg border-0 focus:ring-2 focus:ring-blue-500">
                <option value="">- Storage -</option>
                @foreach ($data['st_id'] as $key => $value)
                    @if ($key == $data['user']->st_id)
                        <option value="{{ $key }}" selected>{{ $value }}</option>
                    @else
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endif
                @endforeach
            </select>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <!-- Brand -->
                <div>
                    <select id="br_id" name="br_id" class="w-full px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg border-0 focus:ring-2 focus:ring-blue-500">
                        <option value="">- Semua Brand -</option>
                        @foreach ($data['br_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Sub Category -->
                <div>
                    <select id="psc_id" name="psc_id" class="w-full px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg border-0 focus:ring-2 focus:ring-blue-500">
                        <option value="">- Semua Sub Kategori -</option>
                        @foreach ($data['psc_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Divisi Penjualan -->
                <div>
                    <select id="std_id" name="std_id" class="w-full px-4 py-2.5 bg-green-500 text-white text-sm font-medium rounded-lg border-0 focus:ring-2 focus:ring-green-500">
                        <option value="">- Divisi Penjualan -</option>
                        @foreach ($data['std_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Date Range -->
                <div class="col-span-2">
                    <button type="button" id="kt_dashboard_daterangepicker" class="w-full px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-200 transition-colors cursor-pointer">
                        <span class="font-size-base" id="kt_dashboard_daterangepicker_title">Today</span>
                        <span class="font-size-base font-weight-bolder ml-2" id="kt_dashboard_daterangepicker_date"></span>
                    </button>
                </div>
                <!-- Status Filter -->
                <div>
                    <select id="status_filter" class="w-full px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg border-0 focus:ring-2 focus:ring-blue-500">
                        <option value="">- Semua Status -</option>
                        <option value="WAITING OFFLINE">WAITING OFFLINE</option>
                        <option value="WAITING ONLINE">WAITING ONLINE</option>
                        <option value="WAITING FOR PACKING">WAITING FOR PACKING</option>
                        <option value="WAITING FOR CHECKOUT">WAITING FOR CHECKOUT</option>
                        <option value="WAITING TO TAKE">WAITING TO TAKE</option>
                        <option value="WAITING FOR NAMESET">WAITING FOR NAMESET</option>
                        <option value="DONE">DONE</option>
                        <option value="REJECT">REJECT</option>
                        <option value="INSTOCK">INSTOCK</option>
                        <option value="INSTOCK APPROVAL">INSTOCK APPROVAL</option>
                        <option value="REFUND">REFUND</option>
                        <option value="EXCHANGE">EXCHANGE</option>
                        <option value="COMPLAINT">COMPLAINT</option>
                    </select>
                </div>
            </div>
            
            <!-- Notice Buttons -->
            <div class="flex flex-wrap items-center gap-2 mt-4">
                <button type="button" id="problem_btn" class="px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-500 transition-colors" data-notice="">0</button>
                <button type="button" id="waiting_online_btn" class="px-4 py-2 bg-yellow-500 text-white text-sm font-medium rounded-lg hover:bg-yellow-600 transition-colors" data-notice="">0</button>
                <button type="button" id="waiting_offline_btn" class="px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-lg hover:bg-yellow-700 transition-colors" data-notice="">0</button>
                <button type="button" id="graph_btn" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                    <i class="fas fa-bar-chart"></i>
                    Graph
                </button>
            </div>
        </div>

        <div class="p-5">
            <!-- Search -->
            <div class="mb-4 flex items-center gap-3">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="search" id="stock_tracking_search" class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari artikel / customer / invoice (ketik minimal 5 karakter)">
                </div>
                <button type="button" id="export_stock_tracking" class="px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Excel
                </button>
            </div>

            <!-- Data Table -->
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200" id="StockTrackingtb">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal <i><small class="text-gray-400">* pergerakan stock</small></i></th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">SKU</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Artikel <i><small class="text-gray-400">* klik untuk detail</small></i></th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Invoice</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cust</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Qty</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Lokasi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('app.updated_stock_tracking.stock_tracking_modal')
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
@include('app.updated_stock_tracking.stock_tracking_css')
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@include('app.updated_stock_tracking.stock_tracking_js')
@endpush
