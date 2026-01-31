@extends('layouts.app_v2')

@section('content')
<div class="space-y-6 pb-12">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 bg-white rounded-lg border border-gray-200 p-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola approval penerimaan purchase order</p>
        </div>
        <div class="flex gap-2">
            <button type="button" id="export_btn" class="px-4 py-2.5 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 transition-colors">
                <i class="fas fa-download mr-2"></i>Export Data
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="search" id="approval_penerimaan_search" class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari PO/Invoice/Supplier">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cabang</label>
                <select id="filter_cabang" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">- Pilih Cabang -</option>
                    <option value="SURABAYA">Surabaya</option>
                    <option value="MALANG">Malang</option>
                    <option value="KEDIRI">Kediri</option>
                    <option value="JEMBER">Jember</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Transaksi</label>
                <select id="filter_status" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">- Pilih Status transaksi -</option>
                    <option value="approve">Approve</option>
                    <option value="wait_cod">Menunggu Pembayaran</option>
                    <option value="wait">Menunggu Approval</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dispute</label>
                <select id="filter_dispute" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">- It Is Dispute? -</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Dispute</label>
                <select id="filter_status_dispute" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">- Pilih Status Dispute -</option>
                    <option value="1">Progress</option>
                    <option value="0">Closed</option>
                </select>
            </div>
        </div>
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Invoice</label>
            <div class="flex items-center gap-2">
                <input type="text" id="kt_dashboard_daterangepicker" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Pilih tanggal">
                <input type="hidden" id="po_date" />
            </div>
        </div>
    </div>

    <!-- Data Table Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
        <div class="datatable-container overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="ApprovalPenerimaantb">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Store</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">PO</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Supplier</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Invoice</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tgl Invoice</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tgl Barang Datang</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tgl Terima</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Qty</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status Approval</th>
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
@include('app.updated_po_approval.po_approval_css')
@endpush

@push('scripts')
@include('app.updated_po_approval.po_approval_js')
@endpush

@include('app.updated_po_approval.po_approval_modal')
@endsection
