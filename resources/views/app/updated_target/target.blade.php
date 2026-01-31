@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 bg-white rounded-lg border border-gray-200 p-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola target penjualan</p>
        </div>
        <div class="flex gap-2">
            <button type="button" id="target_excel_btn" class="px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors cursor-pointer">
                <i class="fas fa-file-excel mr-2"></i>Export Excel
            </button>
            <button type="button" id="add_target_btn" class="px-4 py-2.5 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 transition-colors">
                <i class="fas fa-plus mr-2"></i>Data Baru
            </button>
            <button type="button" id="import_modal_btn" class="px-4 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-upload mr-2"></i>Import
            </button>
        </div>
    </div>

    <!-- Target Table Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Data Target</h3>
            </div>
        </div>
        <div class="p-5">
            <!-- Search -->
            <div class="mb-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="search" id="target_search" class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari target">
                </div>
            </div>

            <!-- Data Table -->
            <div class="datatable-container overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200" id="Targettb">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bulan Tahun</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Target</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tercapai</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">%</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">[Penjualan] [Item Terjual]</th>
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

@include('app.updated_target.target_modal')
@endsection

@push('styles')
@include('app.updated_target.target_css')
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@include('app.updated_target.target_js')
@endpush
