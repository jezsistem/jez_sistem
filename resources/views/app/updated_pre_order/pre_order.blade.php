@extends('layouts.app_v2')

@section('content')
<div class="space-y-6 pb-12">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 bg-white rounded-lg border border-gray-200 p-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola pre order</p>
        </div>
        <div class="flex gap-2">
            <button type="button" id="add_pre_order_btn" class="px-4 py-2.5 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 transition-colors">
                <i class="fas fa-plus mr-2"></i>Data Baru
            </button>
            <button type="button" id="import_modal_btn" class="px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-upload mr-2"></i>Import
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="search" id="pre_order_search" class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari Pre Order">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Storage</label>
                <select id="st_id_filter" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">- Storage -</option>
                    @foreach ($data['st_id'] as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Pre Order</label>
                <select id="po_type_filter" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Pilih Status Pre Order --</option>
                    <option value="0">REPEAT</option>
                    <option value="1">LAUNCHING</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Data Table Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
        <div class="datatable-container overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="PreOrdertb">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pre Order Code</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Store</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Supplier</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Created At</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Data will be loaded by SimpleDatatables -->
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('styles')
@include('app.updated_pre_order.pre_order_css')
@endpush

@push('scripts')
@include('app.updated_pre_order.pre_order_js')
@endpush

@include('app.updated_pre_order.pre_order_modal')
@endsection
