@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between bg-white rounded-lg border border-gray-200 p-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Transfer stock antar store</p>
        </div>
    </div>

    <!-- Manual Transfer Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">By Manual</h3>
        </div>
        <div class="p-5">
            <!-- Store Selection -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Store Awal</label>
                    <select id="st_id_start" name="st_id_start" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">- Store Awal -</option>
                        @foreach ($data['st_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <div id="st_id_start_parent"></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Store Tujuan</label>
                    <select id="st_id_end" name="st_id_end" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">- Store Tujuan -</option>
                        @foreach ($data['st_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <div id="st_id_end_parent"></div>
                </div>
                <div class="flex items-end">
                    <button type="button" id="transfer_btn" class="w-full px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Transfer
                    </button>
                </div>
            </div>

            <!-- BIN Selection -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">BIN Untuk Ditransfer</label>
                <select id="pl_id" name="pl_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">- BIN Untuk Ditransfer -</option>
                </select>
                <div id="pl_id_parent"></div>
            </div>

            <!-- Search and Actions -->
            <div class="flex flex-col md:flex-row items-center gap-3 mb-4">
                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="search" id="article_search" class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari artikel / brand / warna">
                </div>
                <button type="button" id="ImportModalBtn" class="px-4 py-2.5 bg-blue-50 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-100 transition-colors flex items-center gap-2 border border-blue-200">
                    <i class="fas fa-file-import"></i>
                    Import
                </button>
                <button type="button" id="CancelBtn" class="px-4 py-2.5 bg-red-50 text-red-700 text-sm font-medium rounded-lg hover:bg-red-100 transition-colors flex items-center gap-2 border border-red-200">
                    <i class="fas fa-times-circle"></i>
                    Cancel
                </button>
            </div>

            <!-- Transfer Table -->
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200" id="TransferBintb">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Brand</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Artikel</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">[Size] [Qty]</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Transfer</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- In Transfer Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="p-5">
            <!-- Transfer Code and Actions -->
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-4">
                <div>
                    <span id="stf_code" class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg inline-block"></span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" id="transfer_cancel_btn" class="px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                        DELETE
                    </button>
                    <button type="button" id="transfer_draft_btn" class="px-4 py-2 bg-yellow-500 text-white text-sm font-medium rounded-lg hover:bg-yellow-600 transition-colors">
                        DRAFT
                    </button>
                    <button type="button" id="transfer_done_btn" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                        DONE
                    </button>
                </div>
            </div>

            <!-- Search -->
            <div class="mb-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="search" id="in_transfer_search" class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari artikel / brand / warna">
                </div>
            </div>

            <!-- In Transfer Table -->
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200" id="InTransferBintb">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Artikel</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">BIN</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Qty</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Asal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tujuan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- History Transfer Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">History Transfer</h3>
        </div>
        <div class="p-5">
            <!-- Filters -->
            <div class="flex flex-col md:flex-row items-center gap-4 mb-4">
                <select id="status_filter" name="status_filter" class="w-full md:w-48 px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">- Status -</option>
                    <option value="1">In Progress</option>
                    <option value="2">Done</option>
                    <option value="3">Draft</option>
                </select>
                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="search" id="history_search" class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari user / artikel">
                </div>
            </div>

            <!-- History Table -->
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200" id="TransferHistorytb">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Qty</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Store Awal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Store Tujuan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Penerima</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Tanggal</th>
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

@include('app.updated_transfer_stok.transfer_stok_modal')
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@include('app.updated_transfer_stok.transfer_stok_css')
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
@include('app.updated_transfer_stok.transfer_stok_js')
@endpush
