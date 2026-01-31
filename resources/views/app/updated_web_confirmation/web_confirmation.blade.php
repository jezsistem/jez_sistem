@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola konfirmasi pembayaran</p>
        </div>
    </div>
</div>

<!-- Card -->
<div class="bg-white rounded-lg shadow-md p-6">
    <!-- Filters -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
            <input type="search" id="wbc_search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Cari konfirmasi...">
        </div>
        <div>
            <select id="status_filter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <option value="">- Filter Status -</option>
                <option value="0">Menunggu Konfirmasi</option>
                <option value="1">Diterima</option>
                <option value="2">Ditolak</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table id="Wbctb" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">Tanggal</th>
                    <th scope="col" class="px-3 py-3">Invoice</th>
                    <th scope="col" class="px-3 py-3">Nama</th>
                    <th scope="col" class="px-3 py-3">Gambar</th>
                    <th scope="col" class="px-3 py-3 whitespace-nowrap">Rek Tujuan</th>
                    <th scope="col" class="px-3 py-3">IP</th>
                    <th scope="col" class="px-3 py-3">Dibaca</th>
                    <th scope="col" class="px-3 py-3">Terakhir</th>
                    <th scope="col" class="px-3 py-3">Status</th>
                </tr>
            </thead>
            <tbody id="wbc_tbody">
                <tr>
                    <td colspan="10" class="px-3 py-4 text-center text-gray-500">
                        <div class="flex justify-center items-center">
                            <svg class="animate-spin h-5 w-5 mr-3 text-blue-500" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memuat data...
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-between items-center mt-4">
        <div id="wbc_pagination_info" class="text-sm text-gray-600"></div>
        <div id="wbc_pagination_controls" class="flex items-center gap-2"></div>
    </div>
</div>

<input type="hidden" id="ecommerce_url" value="{{ $data['ecommerce_url'] }}">

@push('scripts')
    @include('app._partials.js')
    @include('app.updated_web_confirmation.web_confirmation_js_v2')
@endpush

@include('app.updated_web_confirmation.web_confirmation_modal_v2')
@endsection
