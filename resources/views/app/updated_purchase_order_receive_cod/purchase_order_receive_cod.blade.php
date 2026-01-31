@extends('layouts.app_v2')

@section('content')
<div class="space-y-6 pb-12">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 bg-white rounded-lg border border-gray-200 p-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Approval penerimaan COD (Cash On Delivery)</p>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
            <div class="relative">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="search" id="po_approval_search" class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari PO / Invoice / Store / Supplier">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Invoice</label>
                <button type="button"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white hover:bg-gray-50 transition-colors"
                    id="kt_dashboard_daterangepicker">
                    <span id="kt_dashboard_daterangepicker_title" class="mr-2 text-gray-500">Today</span>
                    <span id="kt_dashboard_daterangepicker_date" class="font-semibold text-gray-700"></span>
                    <input type="hidden" id="po_date" />
                </button>
            </div>
        </div>
    </div>

    <!-- Data Table Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="APtb">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Store</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">PO</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Supplier</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Invoice</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tgl Invoice</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tgl Terima</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Penerima</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Approval</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- DataTable lama akan mengisi tabel ini -->
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('styles')
    {{-- gunakan styling umum dari halaman lama (bootstrap/datatable) jika dibutuhkan --}}
@endpush

@push('scripts')
    @include('app._partials.js')
    @include('app.updated_purchase_order_receive_cod.purchase_order_receive_cod_js_v2')
@endpush

@include('app.updated_purchase_order_receive_cod.purchase_order_receive_cod_modal_v2')
@endsection

