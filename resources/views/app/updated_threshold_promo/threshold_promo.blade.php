@extends('layouts.app_v2')

@section('content')
<div class="space-y-6 pb-12">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 bg-white rounded-lg border border-gray-200 p-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola threshold promo</p>
        </div>
        <div class="flex gap-2">
            <button type="button" id="import_modal_btn" class="px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-upload mr-2"></i>Import
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="search" id="threshold_promo_search" class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari Threshold Promo / Article ID">
            </div>
            <div>
                <select id="channel_threshold_promo" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih Channel</option>
                    <option value="ONLINE">ONLINE</option>
                    <option value="OFFLINE">OFFLINE</option>
                </select>
            </div>
            <div>
                <input type="hidden" id="threshold_promo_date_start" value=""/>
                <input type="hidden" id="threshold_promo_date_end" value=""/>
                <button type="button" id="kt_dashboard_daterangepicker" class="w-full px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors text-left">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    <span id="kt_dashboard_daterangepicker_title">All Days</span>
                    <span id="kt_dashboard_daterangepicker_date"></span>
                </button>
            </div>
            <div>
                <button type="button" id="export_btn" class="w-full px-4 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-file-excel mr-2"></i>Export Excel
                </button>
            </div>
        </div>
    </div>

    <!-- Threshold Promo Table Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Threshold Promo</h3>
        </div>
        <div class="p-5">
            <!-- Data Table -->
            <div class="datatable-container overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200" id="PromoRecommendationtb">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Code</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Channel</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Created At</th>
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

@include('app.updated_threshold_promo.threshold_promo_modal')
@endsection

@push('styles')
@include('app.updated_threshold_promo.threshold_promo_css')
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@include('app.updated_threshold_promo.threshold_promo_js')
@endpush
