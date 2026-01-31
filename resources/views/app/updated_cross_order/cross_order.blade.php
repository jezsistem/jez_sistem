@extends('layouts.app_v2')

@section('content')
<div class="space-y-6 pb-12">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 bg-white rounded-lg border border-gray-200 p-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola cross order</p>
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

    <!-- Cross Order Table Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Cross Order</h3>
                <div class="flex gap-2">
                    <select id="std_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">- Divisi -</option>
                        @foreach ($data['std_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <select id="status_filter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">- Semua -</option>
                        <option value="DONE">DONE</option>
                        <option value="IN PROGRESS">IN PROGRESS</option>
                        <option value="WAITING FOR CONFIRMATION">WAITING FOR CONFIRMATION</option>
                        <option value="SHIPPING NUMBER">SHIPPING NUMBER</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="p-5">
            <!-- Search -->
            <div class="mb-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="search" id="invoice_tracking_search" class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari invoice / nomor resi / customer / Sku">
                </div>
            </div>

            <!-- Data Table -->
            <div class="datatable-container overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200" id="CrossOrdertb">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Invoice</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kasir</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Store Tujuan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kasir Tujuan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Item</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Reject</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
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

@include('app.updated_cross_order.cross_order_modal')
@endsection

@push('styles')
@include('app.updated_cross_order.cross_order_css')
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@include('app.updated_cross_order.cross_order_js')
@endpush
