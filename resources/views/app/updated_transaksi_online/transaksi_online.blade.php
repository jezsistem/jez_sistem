@extends('layouts.app_v2')

@section('content')
<div class="space-y-6 pb-12">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 bg-white rounded-lg border border-gray-200 p-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola transaksi online</p>
        </div>
        <div class="flex gap-2">
            <select id="st_id_filter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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

    <!-- Export Laporan Section -->
    <div class="bg-pink-50 rounded-lg border border-pink-200 shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Export Laporan</h3>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cabang</label>
                <select id="branch_trx" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Cabang --</option>
                    @foreach ($data['st_id'] as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Cetak</label>
                <select id="status_trx" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Pilih Status Cetak ---</option>
                    <option value="2">Semua Status</option>
                    <option value="1">Sudah Cetak</option>
                    <option value="0">Belum Cetak</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Platform</label>
                <select id="changeplatform" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Platform ---</option>
                    <option value="">Semua Platform</option>
                    <option value="Shopee">Shopee</option>
                    <option value="TikTok">Tiktok</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="hidden" id="sales_date" value=""/>
                <button type="button" id="kt_dashboard_daterangepicker" class="w-full px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors text-left">
                    <span class="text-blue-200 font-weight-bold mr-2" id="kt_dashboard_daterangepicker_title">Today</span>
                    <span class="text-white font-weight-bolder" id="kt_dashboard_daterangepicker_date"></span>
                </button>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">&nbsp;</label>
                <button type="button" id="sales_online_export" class="w-full px-4 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-file-excel mr-2"></i>Export Laporan
                </button>
            </div>
        </div>
    </div>

    <!-- Transaksi Table Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Transaksi</h3>
                <div class="flex gap-2">
                    <button type="button" id="import_modal_btn" class="px-4 py-2.5 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 transition-colors">
                        <i class="fas fa-upload mr-2"></i>Import
                    </button>
                </div>
            </div>
        </div>
        <div class="p-5">
            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="search" id="online_transaction_search" class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari No Order / No resi">
                </div>
                <div>
                    <select id="filter_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Pilih Status Cetak --</option>
                        <option value="0">Belum di Cetak</option>
                        <option value="1">Sudah di Cetak</option>
                    </select>
                </div>
            </div>

            <!-- Data Table -->
            <div class="datatable-container overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200" id="OnlineTransactionb">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nomer Order</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nomer Resi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Platform Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Order</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Item</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ongkos Kirim</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Pembayaran</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status Pengiriman</th>
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

@include('app.updated_transaksi_online.transaksi_online_modal')
@endsection

@push('styles')
@include('app.updated_transaksi_online.transaksi_online_css')
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@include('app.updated_transaksi_online.transaksi_online_js')
@endpush
