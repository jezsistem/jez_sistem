@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form id="f_filter" class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <!-- Store Filter -->
            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-2">STORE</label>
                <select id="st_id" name="st_id" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500" required>
                    <option value="">-</option>
                    @foreach ($data['st_id'] as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Data Type Filter -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">TIPE DATA</label>
                <select id="data_filter" name="data_filter" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500" required>
                    <option value="">-</option>
                    <option value="brand">Brand</option>
                    <option value="article">Artikel</option>
                </select>
            </div>

            <!-- Article Filter -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">DATA ARTIKEL</label>
                <select id="article_filter" name="article_filter" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">-</option>
                    <option value="color">Warna</option>
                    <option value="size">Size</option>
                </select>
            </div>

            <!-- Date Range Picker -->
            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-2">RANGE TANGGAL</label>
                <input type="hidden" id="dashboard_date" value=""/>
                <button type="button" id="kt_dashboard_daterangepicker" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm hover:bg-gray-50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="font-medium" id="kt_dashboard_daterangepicker_title">Today</span>
                        <span class="text-gray-500" id="kt_dashboard_daterangepicker_date"></span>
                    </div>
                    <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                </button>
            </div>

            <!-- Action Buttons -->
            <div class="md:col-span-2 flex items-end gap-2">
                <!-- <button type="button" id="export_btn" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                    Export
                </button> -->
                <button type="submit" class="flex-1 px-4 py-2.5 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors">
                    Tampilkan
                </button>
            </div>
        </form>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div id="table"></div>
    </div>
</div>

@include('app.updated_asset_detail.asset_detail_js')
@endsection

@push('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush
